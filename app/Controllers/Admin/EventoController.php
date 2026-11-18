<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Acl;
use App\Core\AdminController;
use App\Core\Audit;
use App\Core\Flash;
use App\Models\Configuracao;
use App\Models\Evento;

final class EventoController extends AdminController
{
    private const TAMANHO_MAXIMO = 2097152;

    private const TEXTOS = [
        'eventos_eyebrow'            => 'Agenda do ministério',
        'eventos_titulo'             => 'Eventos e programação',
        'eventos_vazio'              => 'Nenhum evento publicado no momento.',
        'eventos_ver_detalhes'       => 'Ver detalhes',
        'eventos_detalhe_data'       => 'Data',
        'eventos_detalhe_horario'    => 'Horário',
        'eventos_detalhe_local'      => 'Local',
        'eventos_detalhe_congregacao' => 'Congregação',
        'eventos_home_eyebrow'       => 'Agenda',
        'eventos_home_titulo'        => 'Próximos eventos',
        'eventos_home_botao'         => 'Ver todos',
        'eventos_home_vazio'         => 'Nenhum evento programado no momento.',
        'eventos_home_local'         => 'Local a confirmar',
        'eventos_home_ver_detalhes'  => 'Ver detalhes',
    ];

    public function index(): void
    {
        $this->guard('eventos.ver');

        $this->renderAdmin('admin.eventos.index', [
            'title'        => 'Eventos e programação',
            'subtitulo'    => 'Gerencie os eventos exibidos na agenda do ministério e na página inicial.',
            'eventos'      => Evento::todos(),
            'podeCriar'    => Acl::can('eventos.criar'),
            'podeEditar'   => Acl::can('eventos.editar'),
            'podeExcluir'  => Acl::can('eventos.excluir'),
            'podePublicar' => Acl::can('eventos.publicar'),
        ]);
    }

    public function textosForm(): void
    {
        $this->guard('eventos.editar');

        $this->renderAdmin('admin.eventos.textos', [
            'title'     => 'Textos da agenda',
            'subtitulo' => 'Gerencie os textos exibidos na página de eventos e na seção da página inicial.',
            'textos'    => $this->textos(),
        ]);
    }

    public function salvarTextos(): void
    {
        $this->guard('eventos.editar');

        $novo = [];
        foreach (self::TEXTOS as $chave => $padrao) {
            $novo[$chave] = mb_substr(trim((string) $this->request->post($chave, '')), 0, 180);
        }

        if ($novo['eventos_eyebrow'] === '' || $novo['eventos_titulo'] === '' || $novo['eventos_home_titulo'] === '') {
            $this->falhar('Informe ao menos os subtítulos e títulos das seções.');
        }

        $anterior = [];
        foreach ($novo as $chave => $valor) {
            $anterior[$chave] = Configuracao::get($chave, '');
            Configuracao::set($chave, $valor);
        }

        Audit::record('editar', 'eventos', $novo, $anterior, 'configuracoes', 'eventos_textos');

        Flash::success('Textos da agenda atualizados.');
        $this->redirectAdmin('eventos/textos');
    }

    public function novoForm(): void
    {
        $this->guard('eventos.criar');

        $this->renderAdmin('admin.eventos.form', [
            'title'        => 'Novo evento',
            'subtitulo'    => 'Cadastre um evento ou atividade da agenda do ministério.',
            'evento'       => $this->eventoPadrao(),
            'tipos'        => Evento::tipos(),
            'congregacoes' => Evento::congregacoes(),
            'responsaveis' => Evento::responsaveis(),
            'situacoes'    => Evento::situacoes(),
            'podePublicar' => Acl::can('eventos.publicar'),
            'acao'         => url('admin/eventos'),
        ]);
    }

    public function editarForm(string $id): void
    {
        $this->guard('eventos.editar');

        $id = (int) $id;
        $evento = Evento::buscar($id);
        if ($evento === null) {
            Flash::error('Evento não encontrado.');
            $this->redirectAdmin('eventos');
        }

        $this->renderAdmin('admin.eventos.form', [
            'title'        => 'Editar evento',
            'subtitulo'    => 'Atualize as informações do evento e sua publicação.',
            'evento'       => $this->paraFormulario($evento),
            'tipos'        => Evento::tipos(),
            'congregacoes' => Evento::congregacoes(),
            'responsaveis' => Evento::responsaveis(),
            'situacoes'    => Evento::situacoes(),
            'podePublicar' => Acl::can('eventos.publicar'),
            'acao'         => url('admin/eventos/' . $id . '/editar'),
        ]);
    }

    public function criar(): void
    {
        $this->guard('eventos.criar');

        $dados = $this->dadosValidados();
        $dados['foto'] = $this->processarFoto();
        $id = Evento::criar($dados);

        Audit::record('criar', 'eventos', $dados, [], 'eventos', (string) $id);

        Flash::success('Evento cadastrado.');
        $this->redirectAdmin('eventos');
    }

    public function editar(string $id): void
    {
        $this->guard('eventos.editar');

        $id = (int) $id;
        $anterior = Evento::buscar($id);
        if ($anterior === null) {
            Flash::error('Evento não encontrado.');
            $this->redirectAdmin('eventos');
        }

        $dados = $this->dadosValidados($anterior);
        $novaFoto = $this->processarFoto();
        $dados['foto'] = $novaFoto ?? $anterior['foto'];
        Evento::atualizar($id, $dados);

        if ($novaFoto !== null) {
            $this->removerArquivo((string) ($anterior['foto'] ?? ''));
        }

        Audit::record('editar', 'eventos', $dados, $anterior, 'eventos', (string) $id);

        Flash::success('Evento atualizado.');
        $this->redirectAdmin('eventos');
    }

    public function removerFoto(string $id): void
    {
        $this->guard('eventos.editar');

        $id = (int) $id;
        $anterior = Evento::buscar($id);
        if ($anterior === null) {
            Flash::error('Evento não encontrado.');
            $this->redirectAdmin('eventos');
        }

        $this->removerArquivo((string) ($anterior['foto'] ?? ''));
        Evento::atualizarFoto($id, null);

        Audit::record('editar', 'eventos', ['foto' => null], ['foto' => $anterior['foto']], 'eventos', (string) $id);

        Flash::success('Foto do evento removida.');
        $this->redirectAdmin('eventos/' . $id . '/editar');
    }

    public function excluir(string $id): void
    {
        $this->guard('eventos.excluir');

        $id = (int) $id;
        $anterior = Evento::buscar($id);
        if ($anterior === null || Evento::excluir($id) === 0) {
            Flash::error('Evento não encontrado.');
            $this->redirectAdmin('eventos');
        }

        $this->removerArquivo((string) ($anterior['foto'] ?? ''));
        Audit::record('excluir', 'eventos', [], $anterior, 'eventos', (string) $id);

        Flash::success('Evento excluído.');
        $this->redirectAdmin('eventos');
    }

    private function textos(): array
    {
        $textos = [];
        foreach (self::TEXTOS as $chave => $padrao) {
            $textos[$chave] = Configuracao::get($chave, $padrao);
        }

        return $textos;
    }

    private function dadosValidados(?array $anterior = null): array
    {
        $titulo = trim((string) $this->request->post('titulo', ''));
        if ($titulo === '') {
            $this->falhar('Informe o título do evento.');
        }

        $inicio = $this->dataHora('data_inicio', 'Informe a data de início.');
        $fim = $this->dataHora('data_fim', 'Informe uma data de término válida.');
        if ($inicio !== null && $fim !== null && $fim < $inicio) {
            $this->falhar('A data de término não pode ser anterior ao início.');
        }

        $horario = trim((string) $this->request->post('horario', ''));
        if ($horario !== '' && preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $horario) !== 1) {
            $this->falhar('Informe um horário válido.');
        }

        $tipoId = $this->inteiroOpcional('tipo_id');
        if ($tipoId !== null && !Evento::tipoExiste($tipoId)) {
            $this->falhar('Selecione um tipo de evento válido.');
        }

        $congregacaoId = $this->inteiroOpcional('congregacao_id');
        if ($congregacaoId !== null && !Evento::congregacaoExiste($congregacaoId)) {
            $this->falhar('Selecione uma congregação válida.');
        }
        if (!Evento::congregacaoPermitida($congregacaoId)) {
            $this->falhar('Selecione a congregação vinculada ao seu perfil.');
        }

        $responsavelId = $this->inteiroOpcional('responsavel_id');
        if ($responsavelId !== null && !Evento::responsavelExiste($responsavelId)) {
            $this->falhar('Selecione um responsável válido.');
        }

        $situacao = (string) $this->request->post('situacao', 'PLANEJADO');
        if (!in_array($situacao, Evento::situacoes(), true)) {
            $this->falhar('Selecione uma situação válida.');
        }

        $publicar = (int) ($anterior['publicar_site'] ?? 0);
        if (Acl::can('eventos.publicar')) {
            $publicar = $this->request->post('publicar_site', '0') === '1' ? 1 : 0;
        }

        return [
            'titulo'         => mb_substr($titulo, 0, 180),
            'descricao'      => mb_substr(trim((string) $this->request->post('descricao', '')), 0, 10000),
            'tipo_id'        => $tipoId,
            'data_inicio'    => $inicio,
            'data_fim'       => $fim,
            'horario'        => $horario === '' ? null : $horario . ':00',
            'local_evento'   => mb_substr(trim((string) $this->request->post('local_evento', '')), 0, 255),
            'endereco'       => mb_substr(trim((string) $this->request->post('endereco', '')), 0, 255),
            'foto'           => null,
            'responsavel_id' => $responsavelId,
            'congregacao_id' => $congregacaoId,
            'publicar_site'  => $publicar,
            'destaque'       => $this->request->post('destaque', '0') === '1' ? 1 : 0,
            'situacao'       => $situacao,
        ];
    }

    private function dataHora(string $campo, string $mensagem): ?string
    {
        $valor = trim((string) $this->request->post($campo, ''));
        if ($valor === '') {
            return null;
        }

        $data = \DateTime::createFromFormat('!Y-m-d\TH:i', $valor);
        if ($data === false || $data->format('Y-m-d\TH:i') !== $valor) {
            $this->falhar($mensagem);
        }

        return $data->format('Y-m-d H:i:s');
    }

    private function inteiroOpcional(string $campo): ?int
    {
        $valor = trim((string) $this->request->post($campo, ''));
        if ($valor === '') {
            return null;
        }

        if (!ctype_digit($valor)) {
            $this->falhar('Selecione uma opção válida para ' . $campo . '.');
        }

        return (int) $valor;
    }

    private function processarFoto(): ?string
    {
        $arquivo = $this->request->file('foto');
        if ($arquivo === null || (int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ((int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || (int) ($arquivo['size'] ?? 0) > self::TAMANHO_MAXIMO) {
            $this->falhar('A foto deve ser JPG, PNG ou WebP, com até 2 MB.');
        }

        $temporario = (string) ($arquivo['tmp_name'] ?? '');
        if ($temporario === '' || !is_uploaded_file($temporario)) {
            $this->falhar('Não foi possível receber a foto enviada.');
        }

        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $info === false ? '' : (string) finfo_file($info, $temporario);
        if ($info !== false) {
            finfo_close($info);
        }

        $extensoes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensoes[$mime]) || @getimagesize($temporario) === false) {
            $this->falhar('A foto deve ser JPG, PNG ou WebP, com até 2 MB.');
        }

        $pasta = ROOT_PATH . '/public/uploads/eventos';
        if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
            $this->falhar('Não foi possível preparar a pasta de fotos.');
        }

        $nome = 'evento-' . date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $extensoes[$mime];
        if (!move_uploaded_file($temporario, $pasta . '/' . $nome)) {
            $this->falhar('Não foi possível salvar a foto do evento.');
        }

        return 'eventos/' . $nome;
    }

    private function removerArquivo(string $arquivo): void
    {
        if ($arquivo === '' || str_starts_with($arquivo, 'eventos/') === false || basename($arquivo) !== substr($arquivo, 8)) {
            return;
        }

        $caminho = ROOT_PATH . '/public/uploads/' . $arquivo;
        if (is_file($caminho)) {
            @unlink($caminho);
        }
    }

    private function eventoPadrao(): array
    {
        return [
            'id'              => null,
            'titulo'          => '',
            'descricao'       => '',
            'tipo_id'         => null,
            'data_inicio'     => '',
            'data_fim'        => '',
            'horario'         => '',
            'local_evento'    => '',
            'endereco'        => '',
            'foto'            => null,
            'responsavel_id'  => null,
            'congregacao_id'  => null,
            'publicar_site'   => 1,
            'destaque'        => 0,
            'situacao'        => 'PLANEJADO',
        ];
    }

    private function paraFormulario(array $evento): array
    {
        foreach (['data_inicio', 'data_fim'] as $campo) {
            if (!empty($evento[$campo])) {
                $evento[$campo] = str_replace(' ', 'T', substr((string) $evento[$campo], 0, 16));
            }
        }
        if (!empty($evento['horario'])) {
            $evento['horario'] = substr((string) $evento['horario'], 0, 5);
        }

        return $evento;
    }

    private function falhar(string $mensagem): never
    {
        Flash::error($mensagem);
        $this->redirectAdmin('eventos');
    }
}
