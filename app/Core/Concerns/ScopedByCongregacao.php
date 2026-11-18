<?php

declare(strict_types=1);

namespace App\Core\Concerns;

use App\Core\Auth;

/**
 * Restringe consultas e escritas dos perfis vinculados a uma congregação.
 */
trait ScopedByCongregacao
{
    protected static function escopoCongregacao(): ?int
    {
        $perfil = Auth::perfil();
        if ($perfil === null) {
            return null;
        }

        $perfisEscopados = array_map(
            static fn (mixed $id): int => (int) $id,
            (array) config('permissions.scoped_profiles', [])
        );

        if (!in_array((int) $perfil['id'], $perfisEscopados, true)) {
            return null;
        }

        return Auth::congregacaoId() ?? 0;
    }

    protected static function emEscopo(?int $congregacaoId): bool
    {
        $escopo = self::escopoCongregacao();

        if ($escopo === null) {
            return true;
        }

        if ($escopo === 0 || $congregacaoId === null) {
            return false;
        }

        return $congregacaoId === $escopo;
    }
}
