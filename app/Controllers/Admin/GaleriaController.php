<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Acl;
use App\Core\AdminController;
use App\Core\Audit;
use App\Core\Flash;
use App\Models\Evento;
use App\Models\Galeria;

final class GaleriaController extends AdminController
{
    private const TAMANHO_IMAGEM = 2097152;

    public function index(): void
    {
        $this->guard('site_galeria.ver');

        $this->renderAdmin('admin.galeria.index', [
            'title'       => 'Galeria de fotografias',
            'subtitulo'   => 'Gerencie as fotografias exibidas na seção Memórias e na galeria do site.',
            'fotos'       => Galeria::todas(),
            'podeCriar'   => Acl::can('site_galeria.criar'),
            'podeEditar'  => Acl::can('site_galeria.editar'),
            'podeExcluir' => Acl::can('site_galeria.excluir'),
        ]);
    }

    public function novoForm(): void
    {
        $this->guard('site_galeria.criar');

        $this->renderAdmin('admin.galeria.form', [
            'title'    => 'Nova fotografia',
            'subtitulo' => 'Cadastre uma nova fotografia para a galeria do site.',
            'foto'     => $this->fotoPadrao(),
            'eventos'  => Evento::opcoes(),
            'acao'     => url('admin/galeria'),
        ]);
    }

    public function criar(): void
    {
        $this->guard('site_galeria.criar');

        $dados = $this->dadosValidados();
        $imagem = $this->processarImagem();
        if ($imagem === null) {
            $this->falhar('Selecione uma imagem para a fotografia.', null);
        }

        $dados['imagem'] = $imagem;
        $id = Galeria::criar($dados);

        Audit::record('criar', 'site_galeria', $dados, [], 'galeria', (string) $id);

        Flash::success('Fotografia cadastrada com sucesso.');
        $this->redirectAdmin('galeria');
    }

    public function editarForm(string $id): void
    {
        $this->guard('site_galeria.editar');

        $foto = Galeria::buscar((int) $id);
        if ($foto === null) {
            Flash::error('Fotografia não encontrada.');
            $this->redirectAdmin('galeria');
        }

        $this->renderAdmin('admin.galeria.form', [
            'title'     => 'Editar fotografia',
            'subtitulo' => 'Atualize os dados, a imagem e a publicação da fotografia.',
            'foto'      => $foto,
            'eventos'   => Evento::opcoes(),
            'acao'      => url('admin/galeria/' . (int) $id . '/editar'),
        ]);
    }

    public function editar(string $id): void
    {
        $this->guard('site_galeria.editar');

        $id = (int) $id;
        $anterior = Galeria::buscar($id);
        if ($anterior === null) {
            Flash::error('Fotografia não encontrada.');
            $this->redirectAdmin('galeria');
        }

        $dados = $this->dadosValidados($anterior);
        $novaImagem = $this->processarImagem($anterior);
        $dados['imagem'] = $novaImagem ?? (string) ($anterior['imagem'] ?? '');

        Galeria::atualizar($id, $dados);

        if ($novaImagem !== null) {
            $this->removerArquivo((string) ($anterior['imagem'] ?? ''));
        }

        Audit::record('editar', 'site_galeria', $dados, $anterior, 'galeria', (string) $id);

        Flash::success('Fotografia atualizada com sucesso.');
        $this->redirectAdmin('galeria');
    }

    public function excluir(string $id): void
    {
        $this->guard('site_galeria.excluir');

        $id = (int) $id;
        $anterior = Galeria::buscar($id);
        if ($anterior === null || Galeria::excluir($id) === 0) {
            Flash::error('Fotografia não encontrada.');
            $this->redirectAdmin('galeria');
        }

        $this->removerArquivo((string) ($anterior['imagem'] ?? ''));
        Audit::record('excluir', 'site_galeria', [], $anterior, 'galeria', (string) $id);

        Flash::success('Fotografia excluída.');
        $this->redirectAdmin('galeria');
    }

    private function fotoPadrao(): array
    {
        return [
            'id'            => null,
            'titulo'        => '',
            'descricao'     => '',
            'imagem'        => '',
            'evento_id'     => null,
            'ordem'         => 0,
            'ativa'         => 1,
            'evento_titulo' => null,
        ];
    }

    private function dadosValidados(?array $anterior = null): array
    {
        $titulo = mb_substr(trim((string) $this->request->post('titulo', '')), 0, 150);
        $descricao = mb_substr(trim((string) $this->request->post('descricao', '')), 0, 255);

        $eventoId = (int) $this->request->post('evento_id', '0');
        if ($eventoId > 0 && Evento::buscar($eventoId) === null) {
            $this->falhar('Evento relacionado inválido.', $anterior);
        }

        $ordem = (string) $this->request->post('ordem', '0');
        $ordem = ctype_digit($ordem) ? (int) $ordem : 0;

        return [
            'titulo'    => $titulo,
            'descricao' => $descricao,
            'evento_id' => $eventoId > 0 ? $eventoId : null,
            'ordem'     => min($ordem, 65535),
            'ativa'     => $this->request->post('ativa', '0') === '1' ? 1 : 0,
        ];
    }

    private function processarImagem(?array $anterior = null): ?string
    {
        $arquivo = $this->request->file('imagem');
        if ($arquivo === null || (int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ((int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || (int) ($arquivo['size'] ?? 0) > self::TAMANHO_IMAGEM) {
            $this->falhar('A imagem deve ser JPG, PNG ou WebP, com até 2 MB.', $anterior);
        }

        $temporario = (string) ($arquivo['tmp_name'] ?? '');
        if ($temporario === '' || !is_uploaded_file($temporario)) {
            $this->falhar('Não foi possível receber a imagem enviada.', $anterior);
        }

        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $info === false ? '' : (string) finfo_file($info, $temporario);
        if ($info !== false) {
            finfo_close($info);
        }

        $extensoes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensoes[$mime]) || @getimagesize($temporario) === false) {
            $this->falhar('A imagem deve ser JPG, PNG ou WebP, com até 2 MB.', $anterior);
        }

        $pasta = ROOT_PATH . '/public/uploads/galeria';
        $this->garantirPasta($pasta);

        $nome = 'foto-' . date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $extensoes[$mime];
        if (!move_uploaded_file($temporario, $pasta . '/' . $nome)) {
            $this->falhar('Não foi possível salvar a imagem da galeria.', $anterior);
        }

        return 'galeria/' . $nome;
    }

    private function garantirPasta(string $pasta): void
    {
        if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) {
            $this->falhar('Não foi possível preparar a pasta da galeria.', null);
        }
    }

    private function removerArquivo(string $arquivo): void
    {
        if ($arquivo === '' || !str_starts_with($arquivo, 'galeria/') || basename($arquivo) !== substr($arquivo, 8)) {
            return;
        }

        $caminho = ROOT_PATH . '/public/uploads/' . $arquivo;
        if (is_file($caminho)) {
            @unlink($caminho);
        }
    }

    private function falhar(string $mensagem, ?array $anterior): never
    {
        Flash::error($mensagem);
        $this->redirectAdmin($anterior === null ? 'galeria/novo' : 'galeria/' . (int) $anterior['id'] . '/editar');
    }
}
