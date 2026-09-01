<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class IgrejaModel extends Model
{
    protected $table      = 'igrejas';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'razao_social', 'nome', 'cnpj', 'telefone', 'whatsapp', 'email', 'site',
        'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
        'pais', 'pastor_responsavel', 'data_fundacao', 'logo', 'ativo',
    ];

    protected $validationRules = [
        'nome'         => 'required|min_length[3]|max_length[191]',
        'razao_social' => 'permit_empty|max_length[191]',
        'cnpj'         => 'permit_empty|min_length[14]|max_length[18]',
        'email'        => 'permit_empty|valid_email',
    ];

    /**
     * Retorna a igreja cadastrada (registro único), criando-a vazia se necessário.
     */
    public function getIgreja(): array
    {
        $igreja = $this->first();

        if ($igreja === null) {
            $this->insert(['nome' => 'Minha Igreja', 'razao_social' => '']);
            $igreja = $this->first();
        }

        return $igreja;
    }
}
