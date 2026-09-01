<?php

namespace App\Controllers;

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
}
