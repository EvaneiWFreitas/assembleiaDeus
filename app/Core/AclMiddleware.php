<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Exige uma permissao especifica para a acao solicitada.
 */
final class AclMiddleware
{
    public static function handle(?string $permissao): void
    {
        if ($permissao === null || $permissao === '') {
            return;
        }

        Acl::require($permissao);
    }
}
