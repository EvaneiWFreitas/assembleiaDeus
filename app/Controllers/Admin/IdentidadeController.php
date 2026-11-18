<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Audit;
use App\Core\Flash;
use App\Models\Configuracao;

final class IdentidadeController extends AdminController
{
    private const TAMANHO_MAXIMO = 2097152;

    public function form(): void
    {
        $this->guard('site.editar');

        $logotipo = trim((string) Configuracao::get('logotipo', ''));

        $this->renderAdmin('admin.configuracoes.identidade', [
            'title'     => 'Identidade da igreja',
            'subtitulo' => 'Gerencie o nome, a sigla e o logotipo usados no site e no painel.',
            'valores'   => [
                'ministerio_nome'  => Configuracao::get('ministerio_nome', 'Assembleia de Deus'),
                'ministerio_sigla' => Configuracao::get('ministerio_sigla', 'AD'),
                'logotipo'         => $logotipo,
            ],
            'logoUrl'   => $logotipo !== '' ? url('uploads/' . $logotipo) : asset('images/logo.svg'),
        ]);
    }

    public function salvar(): void
    {
        $this->guard('site.editar');

        $nome = trim((string) $this->request->post('ministerio_nome', ''));
        $sigla = trim((string) $this->request->post('ministerio_sigla', ''));

        if ($nome === '' || $sigla === '') {
            $this->falhar('Preencha o nome e a sigla da igreja.');
        }

        $anterior = [
            'ministerio_nome'  => Configuracao::get('ministerio_nome', ''),
            'ministerio_sigla' => Configuracao::get('ministerio_sigla', ''),
            'logotipo'         => trim((string) Configuracao::get('logotipo', '')),
        ];

        $novoLogo = null;
        $arquivo = $this->request->file('logotipo');
        if ($arquivo !== null && (int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $novoLogo = $this->uploadLogo($arquivo);
            if ($novoLogo === null) {
                $this->falhar('O logotipo deve ser JPG, PNG, WebP ou SVG, com até 2 MB.');
            }
        }

        Configuracao::set('ministerio_nome', mb_substr($nome, 0, 120));
        Configuracao::set('ministerio_sigla', mb_substr($sigla, 0, 20));
        Configuracao::set('logotipo', $novoLogo ?? $anterior['logotipo']);

        if (
            $anterior['ministerio_nome'] !== '' &&
            $nome !== $anterior['ministerio_nome'] &&
            Configuracao::get('hero_titulo', '') === $anterior['ministerio_nome']
        ) {
            Configuracao::set('hero_titulo', mb_substr($nome, 0, 180));
        }

        if ($novoLogo !== null) {
            $this->removerArquivo($anterior['logotipo']);
        }

        Audit::record('editar', 'configuracoes', [
            'ministerio_nome'  => mb_substr($nome, 0, 120),
            'ministerio_sigla' => mb_substr($sigla, 0, 20),
            'logotipo'         => $novoLogo ?? $anterior['logotipo'],
        ], $anterior, 'configuracoes', 'identidade');

        Flash::success('Identidade da igreja atualizada.');
        $this->redirectAdmin('configuracoes/identidade');
    }

    public function removerLogo(): void
    {
        $this->guard('site.editar');

        $anterior = trim((string) Configuracao::get('logotipo', ''));
        $this->removerArquivo($anterior);
        Configuracao::set('logotipo', '');

        Audit::record('editar', 'configuracoes', ['logotipo' => ''], ['logotipo' => $anterior], 'configuracoes', 'logotipo');

        Flash::success('Logotipo removido. O logo padrão foi restaurado.');
        $this->redirectAdmin('configuracoes/identidade');
    }

    private function uploadLogo(array $arquivo): ?string
    {
        if ((int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        if ((int) ($arquivo['size'] ?? 0) > self::TAMANHO_MAXIMO) {
            return null;
        }

        $temporario = (string) ($arquivo['tmp_name'] ?? '');
        if ($temporario === '' || !is_uploaded_file($temporario)) {
            return null;
        }

        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $info === false ? '' : (string) finfo_file($info, $temporario);
        if ($info !== false) {
            finfo_close($info);
        }

        $extensoes = [
            'image/jpeg'    => 'jpg',
            'image/png'     => 'png',
            'image/webp'    => 'webp',
            'image/svg+xml' => 'svg',
        ];

        if (!isset($extensoes[$mime])) {
            return null;
        }

        if ($mime === 'image/svg+xml') {
            $svg = (string) file_get_contents($temporario);
            if (preg_match('/<script\b|on\w+\s*=|javascript:/i', $svg) === 1) {
                return null;
            }
        } else {
            $dimensoes = @getimagesize($temporario);
            if ($dimensoes === false || (int) $dimensoes[0] < 1 || (int) $dimensoes[1] < 1) {
                return null;
            }
        }

        $nome = 'logo-' . date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $extensoes[$mime];
        if (!move_uploaded_file($temporario, ROOT_PATH . '/public/uploads/' . $nome)) {
            return null;
        }

        return $nome;
    }

    private function removerArquivo(string $arquivo): void
    {
        if ($arquivo === '' || basename($arquivo) !== $arquivo) {
            return;
        }

        $caminho = ROOT_PATH . '/public/uploads/' . $arquivo;
        if (is_file($caminho)) {
            @unlink($caminho);
        }
    }

    private function falhar(string $mensagem): never
    {
        Flash::error($mensagem);
        $this->redirectAdmin('configuracoes/identidade');
    }
}
