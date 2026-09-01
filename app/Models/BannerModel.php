<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class BannerModel extends Model
{
    protected $table         = 'banners';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = ['titulo', 'subtitulo', 'imagem', 'link', 'ordem', 'ativo'];

    protected $validationRules = [
        'titulo'    => 'required|min_length[3]|max_length[150]',
        'subtitulo' => 'permit_empty|max_length[255]',
        'link'      => 'permit_empty|valid_url|max_length[191]',
        'ordem'     => 'permit_empty|integer',
        'ativo'     => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Banners ativos da página principal, na ordem definida.
     */
    public function listarAtivos(): array
    {
        return $this->where('ativo', 1)
            ->where('deleted_at', null)
            ->orderBy('ordem', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Lista com busca para o painel administrativo.
     */
    public function listarPainel(?string $busca = null): array
    {
        $builder = $this->builder()->where('deleted_at', null);

        if ($busca) {
            $builder->groupStart()
                ->like('titulo', $busca)
                ->orLike('subtitulo', $busca)
                ->groupEnd();
        }

        return $builder->orderBy('ordem', 'ASC')->orderBy('id', 'ASC')->get()->getResultArray();
    }
}