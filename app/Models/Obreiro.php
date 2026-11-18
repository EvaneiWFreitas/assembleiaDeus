<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Concerns\ScopedByCongregacao;
use App\Core\Database;

/**
 * Obreiros cadastrados no sistema.
 */
final class Obreiro
{
    use ScopedByCongregacao;

    public static function all(array $filtros = []): array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return [];
        }

        $sql = 'SELECT o.*, c.nome AS congregacao_nome, i.nome AS igreja_nome,
                       cm.nome AS cargo_nome, u.nome AS usuario_nome
                  FROM obreiros o
                  LEFT JOIN congregacoes c ON c.id = o.congregacao_id
                  LEFT JOIN igrejas_matriz i ON i.id = o.igreja_origem_id
                  INNER JOIN cargos_ministeriais cm ON cm.id = o.cargo_id
                  LEFT JOIN usuarios u ON u.id = o.usuario_id
                 WHERE 1 = 1';
        $params = [];

        if ($escopo !== null) {
            $sql .= ' AND o.congregacao_id = ?';
            $params[] = $escopo;
        }

        $busca = trim((string) ($filtros['busca'] ?? ''));
        if ($busca !== '') {
            $sql .= ' AND (o.nome_completo LIKE ? OR o.codigo LIKE ? OR o.cpf LIKE ? OR o.numero_registro_ministerial LIKE ?)';
            $like = '%' . $busca . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if (!empty($filtros['situacao'])) {
            $sql .= ' AND o.situacao = ?';
            $params[] = $filtros['situacao'];
        }

        if (!empty($filtros['cargo_id'])) {
            $sql .= ' AND o.cargo_id = ?';
            $params[] = (int) $filtros['cargo_id'];
        }

        if (!empty($filtros['congregacao_id'])) {
            $sql .= ' AND o.congregacao_id = ?';
            $params[] = (int) $filtros['congregacao_id'];
        }

        $sql .= ' ORDER BY o.nome_completo';

        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array
    {
        $escopo = self::escopoCongregacao();
        if ($escopo === 0) {
            return null;
        }

        $sql = 'SELECT o.*, c.nome AS congregacao_nome, i.nome AS igreja_nome,
                       cm.nome AS cargo_nome, u.nome AS usuario_nome
                  FROM obreiros o
                  LEFT JOIN congregacoes c ON c.id = o.congregacao_id
                  LEFT JOIN igrejas_matriz i ON i.id = o.igreja_origem_id
                  INNER JOIN cargos_ministeriais cm ON cm.id = o.cargo_id
                  LEFT JOIN usuarios u ON u.id = o.usuario_id
                 WHERE o.id = ?';
        $params = [$id];

        if ($escopo !== null) {
            $sql .= ' AND o.congregacao_id = ?';
            $params[] = $escopo;
        }

        return Database::fetch($sql . ' LIMIT 1', $params);
    }

    public static function congregacaoPermitida(int $congregacaoId): bool
    {
        return self::emEscopo($congregacaoId);
    }

    public static function codigoExiste(string $codigo, ?int $ignorarId = null): bool
    {
        return self::valorExiste('codigo', $codigo, $ignorarId);
    }

    public static function cpfExiste(string $cpf, ?int $ignorarId = null): bool
    {
        return self::valorExiste('cpf', $cpf, $ignorarId);
    }

    public static function registroExiste(string $registro, ?int $ignorarId = null): bool
    {
        return self::valorExiste('numero_registro_ministerial', $registro, $ignorarId);
    }

    public static function usuarioVinculado(int $usuarioId, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM obreiros WHERE usuario_id = ?';
        $params = [$usuarioId];

        if ($ignorarId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $ignorarId;
        }

        return (int) Database::fetchColumn($sql, $params) > 0;
    }

    public static function proximoCodigo(): string
    {
        $ultimo = Database::fetchColumn(
            "SELECT codigo FROM obreiros WHERE codigo LIKE 'OBR-%' ORDER BY codigo DESC LIMIT 1"
        );

        $numero = $ultimo !== false && $ultimo !== null ? ((int) substr((string) $ultimo, 4) + 1) : 1;

        do {
            $codigo = sprintf('OBR-%04d', $numero++);
        } while (self::codigoExiste($codigo));

        return $codigo;
    }

    public static function create(array $dados): string
    {
        return Database::insert(
            'INSERT INTO obreiros
                (codigo, nome_completo, foto, cpf, rg, data_nascimento, telefone, whatsapp,
                 email, endereco, numero, complemento, bairro, cidade, estado, cep,
                 igreja_origem_id, congregacao_id, cargo_id, data_consagracao,
                 numero_registro_ministerial, situacao, observacoes, usuario_id,
                 created_by, updated_by)
             VALUES
                (:codigo, :nome_completo, :foto, :cpf, :rg, :data_nascimento, :telefone, :whatsapp,
                 :email, :endereco, :numero, :complemento, :bairro, :cidade, :estado, :cep,
                 :igreja_origem_id, :congregacao_id, :cargo_id, :data_consagracao,
                 :numero_registro_ministerial, :situacao, :observacoes, :usuario_id,
                 :created_by, :updated_by)',
            self::parametros($dados)
        );
    }

    public static function update(int $id, array $dados): void
    {
        $params = self::parametros($dados);
        unset($params[':created_by']);
        $params[':id'] = $id;

        Database::execute(
            'UPDATE obreiros SET
                codigo = :codigo, nome_completo = :nome_completo, foto = :foto, cpf = :cpf,
                rg = :rg, data_nascimento = :data_nascimento, telefone = :telefone,
                whatsapp = :whatsapp, email = :email, endereco = :endereco, numero = :numero,
                complemento = :complemento, bairro = :bairro, cidade = :cidade, estado = :estado,
                cep = :cep, igreja_origem_id = :igreja_origem_id,
                congregacao_id = :congregacao_id, cargo_id = :cargo_id,
                data_consagracao = :data_consagracao,
                numero_registro_ministerial = :numero_registro_ministerial,
                situacao = :situacao, observacoes = :observacoes, usuario_id = :usuario_id,
                updated_by = :updated_by
             WHERE id = :id',
            $params
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM obreiros WHERE id = ?', [$id]);
    }

    private static function valorExiste(string $coluna, string $valor, ?int $ignorarId): bool
    {
        if ($valor === '') {
            return false;
        }

        $sql = "SELECT COUNT(*) FROM obreiros WHERE {$coluna} = ?";
        $params = [$valor];

        if ($ignorarId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $ignorarId;
        }

        return (int) Database::fetchColumn($sql, $params) > 0;
    }

    private static function parametros(array $dados): array
    {
        return [
            ':codigo'                      => $dados['codigo'],
            ':nome_completo'               => $dados['nome_completo'],
            ':foto'                        => $dados['foto'],
            ':cpf'                         => $dados['cpf'],
            ':rg'                          => $dados['rg'],
            ':data_nascimento'             => $dados['data_nascimento'],
            ':telefone'                    => $dados['telefone'],
            ':whatsapp'                    => $dados['whatsapp'],
            ':email'                       => $dados['email'],
            ':endereco'                    => $dados['endereco'],
            ':numero'                      => $dados['numero'],
            ':complemento'                 => $dados['complemento'],
            ':bairro'                      => $dados['bairro'],
            ':cidade'                      => $dados['cidade'],
            ':estado'                      => $dados['estado'],
            ':cep'                         => $dados['cep'],
            ':igreja_origem_id'            => $dados['igreja_origem_id'],
            ':congregacao_id'              => $dados['congregacao_id'],
            ':cargo_id'                    => $dados['cargo_id'],
            ':data_consagracao'            => $dados['data_consagracao'],
            ':numero_registro_ministerial' => $dados['numero_registro_ministerial'],
            ':situacao'                    => $dados['situacao'],
            ':observacoes'                 => $dados['observacoes'],
            ':usuario_id'                  => $dados['usuario_id'],
            ':created_by'                  => $dados['created_by'] ?? null,
            ':updated_by'                  => $dados['updated_by'] ?? null,
        ];
    }
}
