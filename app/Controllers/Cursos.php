<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\CursoModel;
use App\Models\MembroModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Cursos (Escola Bíblica, Formação de Obreiros...) com aulas e alunos.
 */
class Cursos extends BaseController
{
    private const DIR_FOTO_AULA = 'public/uploads/aulas/';
    private const DIR_FOTO_CURSO = 'public/uploads/cursos/';

    private CursoModel $cursos;
    private MembroModel $membros;

    public function __construct()
    {
        $this->cursos  = new CursoModel();
        $this->membros = new MembroModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('cursos', 'visualizar');

        $this->dados['titulo'] = 'Cursos';
        $this->dados['cursos'] = $this->cursos->listar();

        return view('cursos/index', $this->dados);
    }

    /**
     * Lista todos os inscritos públicos (aluno_inscricoes) com filtros.
     */
    public function inscricoes(): string
    {
        $this->exigirPermissao('cursos', 'visualizar');

        $cursoId = (int) $this->request->getGet('curso_id');
        $status  = (string) $this->request->getGet('status');

        $db = db_connect();

        $builder = $db->table('aluno_inscricoes ai')
            ->select('ai.*, c.nome AS curso_nome, a.nome AS aluno_nome, a.email AS aluno_email, a.telefone AS aluno_telefone')
            ->join('cursos c', 'c.id = ai.curso_id')
            ->join('alunos a', 'a.id = ai.aluno_id')
            ->orderBy('ai.data_inscricao', 'DESC');

        if ($cursoId > 0) {
            $builder->where('ai.curso_id', $cursoId);
        }
        if ($status !== '') {
            $builder->where('ai.status', $status);
        }

        $this->dados['titulo']     = 'Inscritos em Cursos Online';
        $this->dados['inscricoes'] = $builder->get()->getResultArray();
        $this->dados['cursos']     = $db->table('cursos')->where('deleted_at', null)->orderBy('nome')->get()->getResultArray();
        $this->dados['filtroCurso'] = $cursoId;
        $this->dados['filtroStatus'] = $status;

        return view('cursos/inscricoes', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('cursos', 'cadastrar');

        $this->dados['titulo']      = 'Novo Curso';
        $this->dados['registro']    = null;
        $this->dados['membros']     = $this->membros->getDropdown();
        $this->dados['statusCurso'] = CursoModel::STATUS;

        return view('cursos/form', $this->dados);
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('cursos', 'editar');
        $this->carregarDadosCurso($id);

        $this->dados['titulo'] = 'Gerenciar Curso';

        return view('cursos/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('cursos', 'cadastrar');

        if (! $this->validate($this->cursos->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $foto = $this->receberFotoCurso();
        if ($foto['ok'] === false) {
            return redirect()->back()->withInput()->with('erros', ['foto' => $foto['msg']]);
        }

        $dados = $this->dadosPost();
        $dados['foto'] = $foto['arquivo'];
        $this->cursos->insert($dados);
        $id = (int) $this->cursos->getInsertID();

        (new Auditoria())->log('criar', 'cursos', $id, null, ['nome' => $dados['nome']]);

        return redirect()->to('/cursos')->with('sucesso', 'Curso cadastrado com sucesso!');
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('cursos', 'editar');

        $antes = $this->cursos->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->cursos->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $foto = $this->receberFotoCurso();
        if ($foto['ok'] === false) {
            return redirect()->back()->withInput()->with('erros', ['foto' => $foto['msg']]);
        }

        $dados = $this->dadosPost();
        $apagarArquivo = null;

        if ($foto['arquivo'] !== null) {
            $dados['foto']       = $foto['arquivo'];
            $apagarArquivo       = $antes['foto'] ?? null;
        } elseif ($this->request->getPost('remover_foto') === '1' && ! empty($antes['foto'])) {
            $dados['foto'] = null;
            $apagarArquivo = $antes['foto'];
        }

        $this->cursos->update($id, $dados);

        if ($apagarArquivo !== null && is_file(ROOTPATH . self::DIR_FOTO_CURSO . $apagarArquivo)) {
            @unlink(ROOTPATH . self::DIR_FOTO_CURSO . $apagarArquivo);
        }

        (new Auditoria())->log('editar', 'cursos', $id, ['nome' => $antes['nome']], $dados);

        return redirect()->back()->with('sucesso', 'Curso atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('cursos', 'excluir');

        $antes = $this->cursos->find($id);
        if ($antes !== null) {
            if (! empty($antes['foto']) && is_file(ROOTPATH . self::DIR_FOTO_CURSO . $antes['foto'])) {
                @unlink(ROOTPATH . self::DIR_FOTO_CURSO . $antes['foto']);
            }
            $this->cursos->delete($id);
            (new Auditoria())->log('excluir', 'cursos', $id, ['nome' => $antes['nome']]);
        }

        return redirect()->to('/cursos')->with('sucesso', 'Curso excluído com sucesso!');
    }

    public function adicionarAula(int $id): RedirectResponse
    {
        $this->exigirPermissao('cursos', 'editar');

        if (! $this->validate(['tema' => 'required|max_length[150]'])) {
            return redirect()->back()->with('erro', 'Informe o tema da aula.');
        }

        $foto = $this->receberFotoAula();
        if ($foto['ok'] === false) {
            return redirect()->back()->with('erro', $foto['msg']);
        }

        $this->cursos->adicionarAula([
            'curso_id'  => $id,
            'data'      => $this->request->getPost('data') ?: null,
            'tema'      => trim((string) $this->request->getPost('tema')),
            'conteudo'  => trim((string) $this->request->getPost('conteudo')) ?: null,
            'video_url' => trim((string) $this->request->getPost('video_url')) ?: null,
            'foto'      => $foto['arquivo'],
        ]);

        (new Auditoria())->log('criar', 'curso_aulas', $id);

        return redirect()->back()->with('sucesso', 'Aula adicionada com sucesso!');
    }

    public function editarAula(int $id, int $aulaId): RedirectResponse
    {
        $this->exigirPermissao('cursos', 'editar');

        if (! $this->validate(['tema' => 'required|max_length[150]'])) {
            return redirect()->back()->with('erro', 'Informe o tema da aula.');
        }

        $aula = $this->cursos->db->table('curso_aulas')->where('id', $aulaId)->get()->getRowArray();
        if ($aula === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $foto = $this->receberFotoAula();
        if ($foto['ok'] === false) {
            return redirect()->back()->with('erro', $foto['msg']);
        }

        $dados = [
            'data'       => $this->request->getPost('data') ?: null,
            'tema'       => trim((string) $this->request->getPost('tema')),
            'conteudo'   => trim((string) $this->request->getPost('conteudo')) ?: null,
            'video_url'  => trim((string) $this->request->getPost('video_url')) ?: null,
        ];

        $apagarArquivo = null;

        if ($foto['arquivo'] !== null) {
            $dados['foto']       = $foto['arquivo'];
            $apagarArquivo       = $aula['foto'] ?? null;
        } elseif ($this->request->getPost('remover_foto') === '1' && ! empty($aula['foto'])) {
            $dados['foto'] = null;
            $apagarArquivo = $aula['foto'];
        }

        $this->cursos->db->table('curso_aulas')->where('id', $aulaId)->update($dados);

        if ($apagarArquivo !== null && is_file(ROOTPATH . self::DIR_FOTO_AULA . $apagarArquivo)) {
            @unlink(ROOTPATH . self::DIR_FOTO_AULA . $apagarArquivo);
        }
        (new Auditoria())->log('editar', 'curso_aulas', $aulaId, ['tema' => $aula['tema']], $dados);

        return redirect()->back()->with('sucesso', 'Aula atualizada com sucesso!');
    }

    public function excluirAula(int $id, int $aulaId): RedirectResponse
    {
        $this->exigirPermissao('cursos', 'excluir');

        $aula = $this->cursos->db->table('curso_aulas')->where('id', $aulaId)->get()->getRowArray();
        if ($aula !== null) {
            if (! empty($aula['foto']) && is_file(ROOTPATH . self::DIR_FOTO_AULA . $aula['foto'])) {
                @unlink(ROOTPATH . self::DIR_FOTO_AULA . $aula['foto']);
            }
            $this->cursos->db->table('curso_aulas')->where('id', $aulaId)->delete();
            (new Auditoria())->log('excluir', 'curso_aulas', $aulaId, ['tema' => $aula['tema']]);
        }

        return redirect()->back()->with('sucesso', 'Aula excluída com sucesso!');
    }

    public function matricular(int $id): RedirectResponse
    {
        $this->exigirPermissao('cursos', 'editar');

        $membroId = (int) $this->request->getPost('membro_id');
        if ($membroId <= 0) {
            return redirect()->back()->with('erro', 'Selecione um membro para matricular.');
        }

        if (! $this->cursos->matricular($id, $membroId)) {
            return redirect()->back()->with('erro', 'Este membro já está matriculado no curso.');
        }

        (new Auditoria())->log('criar', 'curso_alunos', $id, null, ['membro_id' => $membroId]);

        return redirect()->back()->with('sucesso', 'Aluno matriculado com sucesso!');
    }

    public function atualizarAluno(int $id, int $alunoId): RedirectResponse
    {
        $this->exigirPermissao('cursos', 'editar');

        $this->cursos->atualizarAluno($alunoId, [
            'frequencia'  => $this->request->getPost('frequencia') !== '' ? (float) $this->request->getPost('frequencia') : null,
            'nota_final'  => $this->request->getPost('nota_final') !== '' ? (float) $this->request->getPost('nota_final') : null,
            'status'      => $this->request->getPost('status') ?: 'Matriculado',
            'certificado' => $this->request->getPost('certificado') ? 1 : 0,
        ]);

        (new Auditoria())->log('editar', 'curso_alunos', $alunoId);

        return redirect()->back()->with('sucesso', 'Dados do aluno atualizados!');
    }

    public function removerAluno(int $id, int $alunoId): RedirectResponse
    {
        $this->exigirPermissao('cursos', 'editar');

        $this->cursos->removerAluno($alunoId);
        (new Auditoria())->log('excluir', 'curso_alunos', $alunoId);

        return redirect()->back()->with('sucesso', 'Aluno removido do curso!');
    }

    private function carregarDadosCurso(int $id): void
    {
        $this->dados['registro']  = $this->cursos->find($id);
        $this->dados['membros']   = $this->membros->getDropdown();
        $this->dados['aulas']     = $this->cursos->getAulas($id);
        $this->dados['alunos']    = $this->cursos->getAlunos($id);
        $this->dados['statusCurso'] = CursoModel::STATUS;

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    private function dadosPost(): array
    {
        return [
            'nome'         => trim((string) $this->request->getPost('nome')),
            'descricao'    => trim((string) $this->request->getPost('descricao')) ?: null,
            'professor_id' => $this->request->getPost('professor_id') ?: null,
            'data_inicio'  => $this->request->getPost('data_inicio') ?: null,
            'data_fim'     => $this->request->getPost('data_fim') ?: null,
            'vagas'        => $this->request->getPost('vagas') ?: null,
            'status'       => $this->request->getPost('status') ?: 'Planejado',
            'ativo'        => $this->request->getPost('ativo') ? 1 : 0,
        ];
    }

    /**
     * Recebe e valida a foto da aula (PNG/JPG/WebP, até 5MB).
     */
    private function receberFotoAula(): array
    {
        $arquivo = $this->request->getFile('foto_aula');

        if ($arquivo === null || ! $arquivo->isValid()) {
            return ['ok' => true, 'arquivo' => null];
        }

        if (! in_array($arquivo->getMimeType(), ['image/png', 'image/jpeg', 'image/webp'], true)) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'Formato inválido para a foto: use PNG, JPG ou WebP.'];
        }

        if ($arquivo->getSizeByUnit('mb') > 5) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'A foto da aula deve ter no máximo 5MB.'];
        }

        $diretorio = ROOTPATH . self::DIR_FOTO_AULA;
        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0775, true);
        }

        $nome = $arquivo->getRandomName();
        $arquivo->move($diretorio, $nome);

        return ['ok' => true, 'arquivo' => $nome];
    }

    /**
     * Recebe e valida a foto de capa do curso (PNG/JPG/WebP, até 5MB).
     */
    private function receberFotoCurso(): array
    {
        $arquivo = $this->request->getFile('foto_curso');

        if ($arquivo === null || ! $arquivo->isValid()) {
            return ['ok' => true, 'arquivo' => null];
        }

        if (! in_array($arquivo->getMimeType(), ['image/png', 'image/jpeg', 'image/webp'], true)) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'Formato inválido para a foto: use PNG, JPG ou WebP.'];
        }

        if ($arquivo->getSizeByUnit('mb') > 5) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'A foto do curso deve ter no máximo 5MB.'];
        }

        $diretorio = ROOTPATH . self::DIR_FOTO_CURSO;
        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0775, true);
        }

        $nome = $arquivo->getRandomName();
        $arquivo->move($diretorio, $nome);

        return ['ok' => true, 'arquivo' => $nome];
    }
}
