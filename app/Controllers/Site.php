<?php

namespace App\Controllers;

use App\Models\AgendaModel;
use App\Models\AgendaPresencaModel;
use App\Models\AlunoModel;
use App\Models\CelulaModel;
use App\Models\CursoModel;
use App\Models\DiscipuladoModel;
use App\Models\DiretoriaModel;
use App\Models\IgrejaModel;
use App\Models\MinisterioModel;

/**
 * Controlador do site público (páginas visitadas por qualquer pessoa).
 */
class Site extends BaseController
{
    private array $dadosBase;

    public function __construct()
    {
        $igreja        = (new IgrejaModel())->first() ?? [];
        $this->dadosBase = [
            'igreja' => $igreja,
            'nome'   => $igreja['nome'] ?? 'Assembleia de Deus',
            'cidade' => $igreja['cidade'] ?? '',
            'estado' => $igreja['estado'] ?? '',
        ];
    }

    public function index(): string
    {
        return view('site/home', $this->dadosBase);
    }

    public function ministerios(): string
    {
        $dados = $this->dadosBase + [
            'titulo'       => 'Ministérios',
            'pagina'       => 'ministerios',
            'ministerios'  => (new MinisterioModel())
                ->builder('ministerios m')
                ->select('m.*, d.nome AS departamento')
                ->join('departamentos d', 'd.id = m.departamento_id', 'left')
                ->where('m.deleted_at', null)
                ->where('m.ativo', 1)
                ->orderBy('m.nome')
                ->get()->getResultArray(),
        ];

        return view('site/ministerios', $dados);
    }

    public function diretorias(): string
    {
        $dados = $this->dadosBase + [
            'titulo'     => 'Diretoria',
            'pagina'     => 'diretorias',
            'diretorias' => (new DiretoriaModel())->getAtivos(),
        ];

        return view('site/diretorias', $dados);
    }

    public function celulas(): string
    {
        $dados = $this->dadosBase + [
            'titulo'  => 'Células',
            'pagina'  => 'celulas',
            'celulas' => (new CelulaModel())->listarComLideres(),
        ];

        return view('site/celulas', $dados);
    }

    public function discipulados(): string
    {
        $model = new DiscipuladoModel();

        $todos = $model->listar();
        $status = [];
        $emAndamento = [];
        foreach ($todos as $d) {
            $status[$d['status']] = ($status[$d['status']] ?? 0) + 1;
            if (($d['status'] ?? '') === 'Em andamento') {
                $emAndamento[] = $d;
            }
        }
        ksort($status);

        $dados = $this->dadosBase + [
            'titulo'      => 'Discipulados',
            'pagina'      => 'discipulados',
            'status'      => $status,
            'emAndamento' => array_slice($emAndamento, 0, 6),
        ];

        return view('site/discipulados', $dados);
    }

    /**
     * Página pública com os cursos oferecidos (para estudo online).
     */
    public function cursos(): string
    {
        $dados = $this->dadosBase + [
            'titulo'  => 'Cursos',
            'pagina'  => 'cursos',
            'cursos'  => (new CursoModel())->listarPublico(),
        ];

        return view('site/cursos', $dados);
    }

    /**
     * Detalhes públicos de um curso + cadastro online para estudar.
     */
    public function cursoDetalhe(int $id): string
    {
        $cursos = new CursoModel();
        $curso  = $cursos->where('deleted_at', null)->where('ativo', 1)->find($id);

        if ($curso === null || $curso['status'] === 'Cancelado' || $curso['status'] === 'Concluído') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $dados = $this->dadosBase + [
            'titulo' => $curso['nome'],
            'pagina' => 'cursos',
            'curso'  => $curso,
            'aulas'  => $cursos->getAulasPublicas((int) $curso['id']),
        ];

        return view('site/curso', $dados);
    }

    /**
     * Cadastro online: cria o aluno (se não existe) e o inscreve no curso.
     */
    public function registrarCurso(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $cursos = new CursoModel();
        $curso  = $cursos->where('deleted_at', null)->where('ativo', 1)->find($id);

        if ($curso === null || $curso['status'] === 'Cancelado' || $curso['status'] === 'Concluído') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $nome     = trim((string) $this->request->getPost('nome'));
        $email    = trim((string) $this->request->getPost('email'));
        $telefone = trim((string) $this->request->getPost('telefone'));
        $cpf      = trim((string) $this->request->getPost('cpf'));
        $senha    = (string) $this->request->getPost('senha');

        if ($nome === '' || $email === '') {
            return redirect()->back()->withInput()->with('erro_cad', 'Informe seu nome e e-mail para se cadastrar.');
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('erro_cad', 'Informe um e-mail válido.');
        }
        if (strlen($senha) < 6) {
            return redirect()->back()->withInput()->with('erro_cad', 'A senha deve ter ao menos 6 caracteres.');
        }

        $alunos = new AlunoModel();
        $aluno  = $alunos->porEmail($email);

        if ($aluno === null) {
            $alunoId = $alunos->cadastrar([
                'nome'     => $nome,
                'email'    => $email,
                'telefone' => $telefone,
                'cpf'      => $cpf,
            ], $senha);

            if ($alunoId === null) {
                return redirect()->back()->withInput()->with('erro_cad', 'Não foi possível criar seu cadastro. Tente novamente.');
            }
            $aluno = $alunos->find($alunoId);
        } else {
            $alunoId = (int) $aluno['id'];
            // Se o aluno já existe e informou senha, valida para garantir que é ele
            if ($senha !== '' && ! password_verify($senha, $aluno['senha_hash'])) {
                return redirect()->back()->withInput()->with('erro_cad', 'Este e-mail já está cadastrado com outra senha. Faça login na Área do Aluno.');
            }
        }

        if ($cursos->alunoInscrito((int) $curso['id'], $alunoId)) {
            return redirect()->to('/site/curso/entrar')->with('info_aluno', 'Você já está inscrito neste curso. Acesse sua Área do Aluno para assistir.');
        }

        $cursos->inscreverAluno((int) $curso['id'], $alunoId);

        // Já deixa o aluno autenticado para assistir às aulas
        session()->set('aluno_logado', true);
        session()->set('aluno', [
            'id'    => $alunoId,
            'nome'  => $aluno['nome'],
            'email' => $aluno['email'],
        ]);

        return redirect()->to('/site/area-aluno')->with('sucesso_aluno', 'Matrícula concluída! Bem-vindo(a) à sua Área do Aluno.');
    }

    /**
     * Formulário de login da área do aluno.
     */
    public function alunoLogin(): string
    {
        $dados = $this->dadosBase + [
            'titulo' => 'Área do Aluno',
            'pagina' => 'cursos',
        ];

        return view('site/aluno-login', $dados);
    }

    /**
     * Autentica o aluno (e-mail + senha).
     */
    public function alunoAutenticar(): \CodeIgniter\HTTP\RedirectResponse
    {
        $email = trim((string) $this->request->getPost('email'));
        $senha = (string) $this->request->getPost('senha');

        $aluno = (new AlunoModel())->porEmail($email);

        if ($aluno === null || ! (bool) $aluno['ativo'] || ! password_verify($senha, $aluno['senha_hash'])) {
            return redirect()->to('/site/curso/entrar')->withInput()->with('erro_login', 'E-mail ou senha inválidos.');
        }

        session()->set('aluno_logado', true);
        session()->set('aluno', [
            'id'    => (int) $aluno['id'],
            'nome'  => $aluno['nome'],
            'email' => $aluno['email'],
        ]);

        return redirect()->to('/site/area-aluno')->with('sucesso_aluno', 'Login realizado com sucesso!');
    }

    /**
     * Encerra a sessão do aluno.
     */
    public function alunoLogout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->remove(['aluno_logado', 'aluno']);

        return redirect()->to('/site/cursos')->with('info_aluno', 'Você saiu da sua conta.');
    }

    /**
     * Área do aluno: lista os cursos em que está matriculado.
     */
    public function areaAluno(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $aluno = session()->get('aluno');
        if (! session()->get('aluno_logado') || $aluno === null) {
            return redirect()->to('/site/curso/entrar')->with('erro_login', 'Faça login para acessar seus cursos.');
        }

        $dados = $this->dadosBase + [
            'titulo'     => 'Minha Área de Cursos',
            'pagina'     => 'cursos',
            'inscricoes' => (new AlunoModel())->inscricoes((int) $aluno['id']),
        ];

        return view('site/area-aluno', $dados);
    }

    /**
     * Lista as aulas de um curso para o aluno matriculado.
     */
    public function cursoAulas(int $cursoId): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $aluno = session()->get('aluno');
        if (! session()->get('aluno_logado') || $aluno === null) {
            return redirect()->to('/site/curso/entrar')->with('erro_login', 'Faça login para acessar suas aulas.');
        }

        $db = db_connect();
        $inscrito = $db->table('aluno_inscricoes')
            ->where('curso_id', $cursoId)
            ->where('aluno_id', (int) $aluno['id'])
            ->countAllResults() > 0;

        if (! $inscrito) {
            return redirect()->to('/site/area-aluno')->with('erro_login', 'Você não está matriculado neste curso.');
        }

        $curso = (new CursoModel())->find($cursoId);
        $aulas = $db->table('curso_aulas')->where('curso_id', $cursoId)->orderBy('data')->orderBy('id')->get()->getResultArray();

        $dados = $this->dadosBase + [
            'titulo' => $curso['nome'] ?? 'Curso',
            'pagina' => 'cursos',
            'curso'  => $curso,
            'aulas'  => $aulas,
        ];

        return view('site/curso-aulas', $dados);
    }

    /**
     * Exibe a videoaula de uma aula de um curso em que o aluno está matriculado.
     */
    public function assistirAula(int $aulaId): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $aluno = session()->get('aluno');
        if (! session()->get('aluno_logado') || $aluno === null) {
            return redirect()->to('/site/curso/entrar')->with('erro_login', 'Faça login para assistir às aulas.');
        }

        $db = db_connect();
        $aula = $db->table('curso_aulas')->where('id', $aulaId)->get()->getRowArray();

        if ($aula === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Somente alunos matriculados naquele curso podem assistir
        $inscrito = $db->table('aluno_inscricoes')
            ->where('curso_id', (int) $aula['curso_id'])
            ->where('aluno_id', (int) $aluno['id'])
            ->countAllResults() > 0;

        if (! $inscrito) {
            return redirect()->to('/site/area-aluno')->with('erro_login', 'Você não está matriculado neste curso.');
        }

        $curso = (new CursoModel())->find((int) $aula['curso_id']);
        $aulas = $db->table('curso_aulas')->where('curso_id', (int) $aula['curso_id'])->orderBy('data')->orderBy('id')->get()->getResultArray();

        $dados = $this->dadosBase + [
            'titulo' => $aula['tema'],
            'pagina' => 'cursos',
            'curso'  => $curso,
            'aula'   => $aula,
            'aulas'  => $aulas,
        ];

        return view('site/aula', $dados);
    }

    public function evento(int $id): string
    {
        $agenda = new AgendaModel();
        $evento = $agenda->where('deleted_at', null)->where('status !=', 'Cancelado')->find($id);

        if ($evento === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $congregacoes = (new \App\Models\CongregacaoModel())->getDropdown();
        $evento['congregacao_nome'] = $congregacoes[$evento['congregacao_id']] ?? null;

        // Prioriza a foto enviada diretamente no evento; senão busca pelo nome do membro
        $fotoEvento = $evento['foto_responsavel'] ?? null;
        if ($fotoEvento && is_file(ROOTPATH . 'public/uploads/agenda/' . $fotoEvento)) {
            $evento['responsavel_foto']     = $fotoEvento;
            $evento['responsavel_foto_url'] = base_url('uploads/agenda/' . $fotoEvento);
        } else {
            $responsavel = null;
            if (! empty($evento['responsavel'])) {
                $nomeBusca = preg_replace('/^(pr\.?|pastor[as]?)\s+/i', '', trim($evento['responsavel']));
                if ($nomeBusca !== '') {
                    $responsavel = (new \App\Models\MembroModel())
                        ->where('deleted_at', null)
                        ->groupStart()
                            ->like('nome', $nomeBusca, 'both')
                            ->orLike('nome_social', $nomeBusca, 'both')
                        ->groupEnd()
                        ->orderBy('nome', 'ASC')
                        ->first();
                }
            }
            $fotoMembro = $responsavel['foto'] ?? null;
            if ($fotoMembro && is_file(ROOTPATH . 'public/uploads/membros/' . $fotoMembro)) {
                $evento['responsavel_foto']     = $fotoMembro;
                $evento['responsavel_foto_url'] = base_url('uploads/membros/' . $fotoMembro);
            } else {
                $evento['responsavel_foto']     = null;
                $evento['responsavel_foto_url'] = null;
            }
        }

        $dados = $this->dadosBase + [
            'titulo' => $evento['titulo'],
            'pagina' => 'evento',
            'evento' => $evento,
        ];

        return view('site/evento', $dados);
    }

    /**
     * Confirma presença em um evento (rota pública).
     */
    public function confirmarPresenca(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $agenda  = new AgendaModel();
        $evento  = $agenda->where('deleted_at', null)->where('status !=', 'Cancelado')->find($id);

        if ($evento === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $presencas = new AgendaPresencaModel();

        $nome     = trim((string) $this->request->getPost('nome'));
        $telefone = trim((string) $this->request->getPost('telefone'));

        if ($nome === '') {
            return redirect()->back()->withInput()->with('erro_presenca', 'Informe seu nome para confirmar a presença.');
        }

        $presencas->save([
            'agenda_id'     => $id,
            'membro_id'     => null,
            'nome'          => $nome,
            'telefone'      => preg_replace('/\D/', '', $telefone) ?: null,
            'confirmado_em' => date('Y-m-d H:i:s'),
        ]);

        if ($presencas->errors()) {
            return redirect()->back()->withInput()->with('erro_presenca', $presencas->errors() ? reset($presencas->errors()) : 'Erro ao confirmar.');
        }

        return redirect()->to('/site/evento/' . $id)->with('sucesso_presenca', 'Presença confirmada! Sua presença já está registrada. Até lá!');
    }
}
