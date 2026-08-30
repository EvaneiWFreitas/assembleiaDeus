<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class PermissaoModel extends Model
{
    protected $table      = 'permissoes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['modulo', 'acao', 'descricao'];

    protected $validationRules = [
        'modulo' => 'required|alpha_dash|max_length[80]',
        'acao'   => 'required|alpha_dash|max_length[30]',
    ];

    /**
     * Retorna todas as permissões agrupadas por módulo.
     */
    public function getAgrupadas(): array
    {
        $rows = $this->orderBy('modulo')->orderBy('acao')->findAll();
        $agrupadas = [];
        foreach ($rows as $row) {
            $agrupadas[$row['modulo']][] = $row;
        }

        return $agrupadas;
    }
}
