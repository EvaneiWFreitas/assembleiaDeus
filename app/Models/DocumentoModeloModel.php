<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class DocumentoModeloModel extends Model
{
    protected $table            = 'documentos_modelos';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'titulo', 'tipo', 'conteudo', 'variaveis', 'ativo', 'padrao',
    ];

    public const TIPOS = [
        'certificado_curso'       => 'Certificado de Curso',
        'certificado_consagracao' => 'Certificado de Consagração',
        'papel_timbrado_ata'      => 'Papel Timbrado para Atas',
        'papel_timbrado_convite'  => 'Papel Timbrado para Convites',
        'outro'                   => 'Outro Documento',
    ];

    protected $validationRules = [
        'titulo'  => 'required|min_length[3]|max_length[150]',
        'tipo'    => 'required|in_list[certificado_curso,certificado_consagracao,papel_timbrado_ata,papel_timbrado_convite,outro]',
        'conteudo' => 'required',
    ];

    public function listar(): array
    {
        return $this->builder('documentos_modelos d')
            ->where('d.deleted_at', null)
            ->orderBy('d.tipo')
            ->orderBy('d.titulo')
            ->get()->getResultArray();
    }

    public function getPadrao(string $tipo): ?array
    {
        return $this->where('tipo', $tipo)
            ->where('padrao', 1)
            ->where('ativo', 1)
            ->where('deleted_at', null)
            ->first();
    }
}