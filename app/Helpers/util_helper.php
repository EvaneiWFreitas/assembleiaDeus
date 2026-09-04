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

if (! function_exists('limpar_decimal')) {
    /**
     * Normaliza um valor digitado em formato BR (1.234,56 ou 1234.56) para
     * o formato aceito pelo banco (1234.56). Retorna número float.
     */
    function limpar_decimal(string|int|float|null $valor): float
    {
        $valor = trim((string) $valor);
        if ($valor === '') {
            return 0.0;
        }

        if (str_contains($valor, ',')) {
            $semMilhar = str_replace('.', '', $valor);
            $valor     = str_replace(',', '.', $semMilhar);
        }

        return (float) $valor;
    }
}

if (! function_exists('formatar_cpf')) {
    function formatar_cpf(?string $cpf): string
    {
        $cpf = preg_replace('/\D/', '', (string) $cpf ?? '');
        if (strlen($cpf) !== 11) {
            return $cpf !== '' ? $cpf : '-';
        }

        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
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

if (! function_exists('video_embed_url')) {
    /**
     * Converte uma URL de vídeo (YouTube/Vimeo) em URL de incorporação no iframe.
     * Retorna null quando não for possível incorporar.
     */
    function video_embed_url(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        // YouTube (youtu.be, youtube.com/watch, shorts)
        if (preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([\w-]{6,})~', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // Vimeo
        if (preg_match('~vimeo\.com/(\d+)~', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return null;
    }
}

if (! function_exists('badge_status')) {    function badge_status($ativo): string
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

if (! function_exists('rich_text')) {
    /**
     * Renderiza conteúdo HTML produzido no editor de texto rico.
     * Mantém apenas tags e atributos de formatação seguros, removendo
     * scripts e handlers de eventos (proteção básica contra XSS).
     */
    function rich_text(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $tagsPermitidas = '<p><br><br/><strong><b><em><i><u><s><strike><sub><sup><h1><h2><h3><h4><h5><h6><ul><ol><li><a><blockquote><pre><code><hr><span><div><img><font><small><mark><del><ins><table><thead><tbody><tfoot><tr><td><th><caption>';

        $limpo = strip_tags($html, $tagsPermitidas);

        // Remove quaisquer handlers de eventos e javascript: de atributos
        $limpo = preg_replace('#\s(?:on\w+)\s*=\s*(["\']).*?\1#is', '', $limpo) ?? $limpo;
        $limpo = preg_replace('#(href|src)\s*=\s*(["\'])javascript:.*?\2#is', '$1=$2#$2', $limpo) ?? $limpo;

        return $limpo;
    }
}

