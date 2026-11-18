<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Controle de acesso baseado em perfis (RBAC).
 */
final class Acl
{
    private static ?array $permissions = null;

    public static function permissions(): array
    {
        if (is_array(self::$permissions)) {
            return self::$permissions;
        }

        if (isset($_SESSION['auth_permissions']) && is_array($_SESSION['auth_permissions'])) {
            return self::$permissions = $_SESSION['auth_permissions'];
        }

        $perfil = Auth::perfil();
        if ($perfil === null) {
            return self::$permissions = [];
        }

        $linhas = Database::fetchAll(
            'SELECT p.slug
               FROM permissoes p
               INNER JOIN perfil_permissao pp ON pp.permissao_id = p.id
              WHERE pp.perfil_id = ?',
            [$perfil['id']]
        );

        $slugs = array_values(array_map(static fn (array $linha): string => (string) $linha['slug'], $linhas));

        $_SESSION['auth_permissions'] = $slugs;

        return self::$permissions = $slugs;
    }

    public static function can(string $permissao): bool
    {
        $perfil = Auth::perfil();
        if ($perfil === null) {
            return false;
        }

        if ((int) $perfil['nivel'] >= 100) {
            return true;
        }

        return in_array($permissao, self::permissions(), true);
    }

    public static function require(string $permissao): void
    {
        if (self::can($permissao)) {
            return;
        }

        self::negar($permissao);
    }

    public static function requireNivel(int $nivel): void
    {
        $perfil = Auth::perfil();
        if ($perfil !== null && (int) $perfil['nivel'] >= $nivel) {
            return;
        }

        self::negar("nível {$nivel}");
    }

    private static function negar(string $requisito): void
    {
        View::render(
            'admin.erro.403',
            ['permissao' => $requisito, 'title' => 'Acesso negado'],
            'admin',
            403
        );
        exit;
    }

    public static function menu(): array
    {
        $modulos = (array) config('permissions.modules', []);
        $grupos = [
            'Visão geral' => ['dashboard'],
            'Estrutura'   => ['igrejas', 'congregacoes', 'pontos_pregacao'],
            'Pessoas'     => ['membros', 'obreiros', 'arquivo_morto'],
            'Ministério'  => ['ministerio_historia', 'ministerio_palavra'],
            'Cultos e programação' => ['cultos_horarios'],
            'Eventos e programação' => ['eventos', 'eventos_textos'],
            'Site'        => ['site_banner', 'site_textos', 'site_historia'],
            'Configurações' => ['configuracoes_identidade'],
            'Sistema'     => ['seguranca', 'auditoria'],
        ];

        $permissoesMenu = [
            'ministerio_historia' => 'site.editar',
            'ministerio_palavra'  => 'site.editar',
            'cultos_horarios'     => 'site.editar',
            'eventos'             => 'eventos.ver',
            'eventos_textos'      => 'eventos.editar',
            'site_banner'   => 'site.editar',
            'site_textos'   => 'site.editar',
            'site_historia' => 'site.editar',
            'configuracoes_identidade' => 'site.editar',
            'seguranca'     => 'seguranca.usuarios_ver',
        ];
        $rotasImplementadas = [
            'dashboard'       => 'dashboard',
            'igrejas'         => 'igrejas',
            'congregacoes'    => 'congregacoes',
            'pontos_pregacao' => 'pontos',
            'membros'         => 'membros',
            'obreiros'        => 'obreiros',
            'arquivo_morto'   => 'arquivo-morto',
            'ministerio_historia' => 'ministerio/historia',
            'ministerio_palavra'  => 'ministerio/palavra-pastoral',
            'cultos_horarios'     => 'horarios',
            'eventos'             => 'eventos',
            'eventos_textos'      => 'eventos/textos',
            'site_banner'     => 'site',
            'site_textos'     => 'site/carrossel-textos',
            'site_historia'   => 'site/historia',
            'configuracoes_identidade' => 'configuracoes/identidade',
            'seguranca'       => 'usuarios',
            'auditoria'       => 'auditoria',
        ];

        $uri = Request::capture()->uri;
        $resultado = [];

        foreach ($grupos as $titulo => $chaves) {
            $itens = [];

            foreach ($chaves as $chave) {
                if (!isset($modulos[$chave]) || !is_array($modulos[$chave])) {
                    continue;
                }

                $permissao = $permissoesMenu[$chave] ?? ($chave . '.ver');
                if (!self::can($permissao)) {
                    continue;
                }

                $modulo = $modulos[$chave];
                $href = isset($rotasImplementadas[$chave])
                    ? url('admin/' . $rotasImplementadas[$chave])
                    : null;

                $caminho = $href === null ? '' : (string) parse_url($href, PHP_URL_PATH);
                $itens[] = [
                    'label'    => (string) ($modulo['label'] ?? $chave),
                    'icon'     => (string) ($modulo['icon'] ?? 'fas fa-circle'),
                    'href'     => $href,
                    'disabled' => $href === null,
                    'active'   => $caminho !== '' && ($uri === $caminho || str_starts_with($uri, $caminho . '/')),
                ];
            }

            if ($itens !== []) {
                $resultado[] = ['titulo' => $titulo, 'itens' => $itens];
            }
        }

        return $resultado;
    }
}
