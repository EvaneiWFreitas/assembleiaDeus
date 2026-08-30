<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class AuditoriaModel extends Model
{
    protected $table         = 'logs_auditoria';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'usuario_id', 'usuario_nome', 'ip', 'acao', 'modulo',
        'registro_id', 'dados_anteriores', 'dados_novos',
    ];

    /**
     * Registra uma ação de auditoria.
     */
    public function registrar(string $acao, string $modulo, ?int $registroId = null, ?array $anteriores = null, ?array $novos = null): void
    {
        $session = session();
        $usuario = $session->get('usuario');

        $this->insert([
            'usuario_id'       => $usuario['id'] ?? null,
            'usuario_nome'     => $usuario['nome'] ?? 'Sistema',
            'ip'               => service('request')->getIPAddress(),
            'acao'             => $acao,
            'modulo'           => $modulo,
            'registro_id'      => $registroId,
            'dados_anteriores' => $anteriores !== null ? json_encode($anteriores, JSON_UNESCAPED_UNICODE) : null,
            'dados_novos'      => $novos !== null ? json_encode($novos, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }
}
