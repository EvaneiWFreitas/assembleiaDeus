<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $igreja  = (new \App\Models\IgrejaModel())->where('ativo', 1)->first() ?? [];
        $banners = (new \App\Models\BannerModel())->listarAtivos();

        $eventos = [];
        $db = db_connect();
        if ($db->tableExists('agenda')) {
            $eventos = (new \App\Models\AgendaModel())->proximosEventos(6);
        }

        return view('site/inicio', [
            'igreja'     => $igreja,
            'membros'    => (new \App\Models\MembroModel())->where('status', 'Ativo')->countAllResults(),
            'banners'    => $banners,
            'eventos'    => $eventos,
            'diretorias' => (new \App\Models\DiretoriaModel())->getAtivos(),
            'cidade'     => $igreja['cidade'] ?? '',
            'estado'     => $igreja['estado'] ?? '',
            'endereco'   => trim(($igreja['logradouro'] ?? '') . ', ' . ($igreja['numero'] ?? ''), ', '),
        ]);
    }
}
