<?php

declare(strict_types=1);

/**
 * Helpers utilitários do sistema (formatação BR, ícones de status etc).
 */
if (! function_exists('formatar_moeda')) {
    function formatar_moeda($valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
}

if (! function_exists('formatar_data')) {
    function formatar_data(?string $data): string
    {
        if ($data === null || $data === '') {
            return '-';
        }

        return date('d/m/Y', strtotime($data));
    }
}

if (! function_exists('formatar_data_hora')) {
    function formatar_data_hora(?string $data): string
    {
        if ($data === null || $data === '') {
            return '-';
        }

        return date('d/m/Y H:i', strtotime($data));
    }
}

if (! function_exists('badge_status')) {
    function badge_status($ativo): string
    {
        return $ativo
            ? '<span class="badge bg-success-subtle text-success">Ativo</span>'
            : '<span class="badge bg-danger-subtle text-danger">Inativo</span>';
    }
}

if (! function_exists('tem_permissao')) {
    function tem_permissao(string $modulo, string $acao): bool
    {
        if (session()->get('usuario')['super_admin'] ?? false) {
            return true;
        }
        $permissoes = session()->get('permissoes') ?? [];

        return in_array($modulo . ':' . $acao, $permissoes, true) || in_array($modulo . ':*', $permissoes, true);
    }
}
