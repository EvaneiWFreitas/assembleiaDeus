<?php

namespace App\Controllers;

use App\Models\AgendaModel;
use App\Models\AgendaPresencaModel;
use App\Models\CelulaModel;
use App\Models\DiscipuladoModel;
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

    public function evento(int $id): string
    {
        $agenda  = new AgendaModel();
        $evento  = $agenda->where('deleted_at', null)->where('status !=', 'Cancelado')->find($id);

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
