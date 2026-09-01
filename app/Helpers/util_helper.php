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

if (! function_exists('favicon_link')) {
    /**
     * Retorna o <link rel="icon"> com a logo da igreja (caso cadastrada).
     * Fallback: ícone SVG de igreja.
     */
    function favicon_link(): string
    {
        try {
            $igreja = (new \App\Models\IgrejaModel())->first() ?? [];
            $logo   = (string) ($igreja['logo'] ?? '');
            $nome   = basename($logo);

            if ($nome !== '' && is_file(ROOTPATH . 'public/uploads/' . $nome)) {
                return '<link rel="icon" type="image/png" href="' . base_url('uploads/' . rawurlencode($nome)) . '">';
            }
        } catch (\Throwable $e) {
            // segue para o fallback
        }

        return '<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 100 100%27%3E%3Ccircle cx=%2750%27 cy=%2750%27 r=%2748%27 fill=%27%230d6efd%27/%3E%3Ctext x=%2750%27 y=%2770%27 text-anchor=%27middle%27 font-size=%2752%27%3E%E2%9B%AA%3C/text%3E%3C/svg%3E">';
    }
}
