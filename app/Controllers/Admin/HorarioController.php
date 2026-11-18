<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Audit;
use App\Core\Database;
use App\Core\Flash;
use App\Models\Configuracao;
use App\Models\ConteudoPublico;
use App\Models\HorarioCulto;

final class HorarioController extends AdminController
{
    private const DIAS = [
        'DOMINGO' => 'Domingo',
        'SEGUNDA' => 'Segunda-feira',
        'TERCA'   => 'Terça-feira',
        'QUARTA'  => 'Quarta-feira',
        'QUINTA'  => 'Quinta-feira',
        'SEXTA'   => 'Sexta-feira',
        'SABADO'  => 'Sábado',
    ];

    public function index(): void
    {
        $this->guard('site.editar');

        $locais = $this->locais();
        $rotulosLocais = [];
        foreach ($locais as $itens) {
            foreach ($itens as $item) {
                $rotulosLocais[$item['valor']] = $item['rotulo'];
            }
        }

        $this->renderAdmin('admin.horarios.index', [
            'title'         => 'Cultos e programação',
            'subtitulo'     => 'Gerencie os textos e os horários exibidos na página inicial.',
            'textos'        => [
                'cultos_eyebrow' => Configuracao::get('cultos_eyebrow', 'Cultos e programação'),
                'cultos_titulo'  => Configuracao::get('cultos_titulo', 'Horários dos cultos e programação semanal'),
                'cultos_vazio'   => Configuracao::get('cultos_vazio', 'Os horários dos cultos serão publicados aqui pela administração.'),
                'cultos_botao'   => Configuracao::get('cultos_botao', 'Veja toda a programação'),
            ],
            'horarios'      => HorarioCulto::todos(),
            'rotulosLocais' => $rotulosLocais,
            'dias'          => self::DIAS,
        ]);
    }

    public function salvarTextos(): void
    {
        $this->guard('site.editar');

        $novo = [
            'cultos_eyebrow' => mb_substr(trim((string) $this->request->post('cultos_eyebrow', '')), 0, 120),
            'cultos_titulo'  => mb_substr(trim((string) $this->request->post('cultos_titulo', '')), 0, 180),
            'cultos_vazio'   => mb_substr(trim((string) $this->request->post('cultos_vazio', '')), 0, 255),
            'cultos_botao'   => mb_substr(trim((string) $this->request->post('cultos_botao', '')), 0, 120),
        ];

        if ($novo['cultos_eyebrow'] === '' || $novo['cultos_titulo'] === '') {
            $this->falhar('Informe ao menos o subtítulo e o título da seção.');
        }

        $anterior = [];
        foreach ($novo as $chave => $valor) {
            $anterior[$chave] = Configuracao::get($chave, '');
            Configuracao::set($chave, $valor);
        }

        Audit::record('editar', 'site', $novo, $anterior, 'configuracoes', 'cultos_textos');

        Flash::success('Textos da seção atualizados.');
        $this->redirectAdmin('horarios');
    }

    public function novoForm(): void
    {
        $this->guard('site.editar');

        $opcoes = $this->locais();
        $this->renderAdmin('admin.horarios.form', [
            'title'     => 'Novo horário',
            'subtitulo' => 'Cadastre um culto ou atividade da programação semanal.',
            'horario'   => [
                'id'              => null,
                'local'           => '',
                'dia_semana'      => 'DOMINGO',
                'horario'         => '09:00',
                'descricao'       => '',
                'duracao_minutos' => null,
                'ativo'           => 1,
            ],
            'locais'    => $opcoes,
            'dias'      => self::DIAS,
            'acao'      => url('admin/horarios'),
        ]);
    }

    public function editarForm(string $id): void
    {
        $this->guard('site.editar');

        $id = (int) $id;
        $horario = HorarioCulto::buscar($id);
        if ($horario === null) {
            Flash::error('Horário não encontrado.');
            $this->redirectAdmin('horarios');
        }

        $this->renderAdmin('admin.horarios.form', [
            'title'     => 'Editar horário',
            'subtitulo' => 'Atualize os dados do culto ou atividade.',
            'horario'   => array_merge($horario, [
                'local' => $horario['local_tipo'] . ':' . $horario['local_id'],
            ]),
            'locais'    => $this->locais(),
            'dias'      => self::DIAS,
            'acao'      => url('admin/horarios/' . $id . '/editar'),
        ]);
    }

    public function criar(): void
    {
        $this->guard('site.editar');

        $dados = $this->dadosValidados();
        HorarioCulto::criar($dados);

        Audit::record('criar', 'site', $dados, [], 'horarios_cultos', 'novo');

        Flash::success('Horário cadastrado.');
        $this->redirectAdmin('horarios');
    }

    public function editar(string $id): void
    {
        $this->guard('site.editar');

        $id = (int) $id;
        $anterior = HorarioCulto::buscar($id);
        if ($anterior === null) {
            Flash::error('Horário não encontrado.');
            $this->redirectAdmin('horarios');
        }

        $dados = $this->dadosValidados();
        HorarioCulto::atualizar($id, $dados);

        Audit::record('editar', 'site', $dados, $anterior, 'horarios_cultos', (string) $id);

        Flash::success('Horário atualizado.');
        $this->redirectAdmin('horarios');
    }

    public function excluir(string $id): void
    {
        $this->guard('site.editar');

        $id = (int) $id;
        $anterior = HorarioCulto::buscar($id);
        if ($anterior === null) {
            Flash::error('Horário não encontrado.');
            $this->redirectAdmin('horarios');
        }

        HorarioCulto::excluir($id);
        Audit::record('excluir', 'site', [], $anterior, 'horarios_cultos', (string) $id);

        Flash::success('Horário excluído.');
        $this->redirectAdmin('horarios');
    }

    private function dadosValidados(): array
    {
        [$tipo, $localId] = $this->localValidado();
        $dia = (string) $this->request->post('dia_semana', '');
        if (!isset(self::DIAS[$dia])) {
            $this->falhar('Selecione um dia da semana válido.');
        }

        $horario = (string) $this->request->post('horario', '');
        if (preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $horario) !== 1) {
            $this->falhar('Informe um horário válido.');
        }

        $descricao = trim((string) $this->request->post('descricao', ''));
        if ($descricao === '') {
            $descricao = 'Culto';
        }

        $duracao = trim((string) $this->request->post('duracao_minutos', ''));
        $duracaoMinutos = $duracao === '' ? null : (int) $duracao;
        if ($duracaoMinutos !== null && ($duracaoMinutos < 1 || $duracaoMinutos > 1440)) {
            $this->falhar('A duração deve ficar entre 1 e 1440 minutos.');
        }

        return [
            'local_tipo'      => $tipo,
            'local_id'        => $localId,
            'congregacao_id'  => $this->congregacaoVinculada($tipo, $localId),
            'dia_semana'      => $dia,
            'horario'         => $horario . ':00',
            'descricao'       => mb_substr($descricao, 0, 120),
            'duracao_minutos' => $duracaoMinutos,
            'ativo'           => $this->request->post('ativo', '0') === '1' ? 1 : 0,
        ];
    }

    private function localValidado(): array
    {
        $valor = (string) $this->request->post('local', '');
        $partes = explode(':', $valor, 2);
        if (count($partes) !== 2 || !in_array($partes[0], ['IGREJA', 'CONGREGACAO', 'PONTO'], true) || !ctype_digit($partes[1])) {
            $this->falhar('Selecione um local válido.');
        }

        return [$partes[0], (int) $partes[1]];
    }

    private function congregacaoVinculada(string $tipo, int $localId): ?int
    {
        if ($tipo === 'CONGREGACAO') {
            return $localId;
        }

        if ($tipo === 'PONTO') {
            $ponto = Database::fetch('SELECT `congregacao_id` FROM `pontos_pregacao` WHERE `id` = ? LIMIT 1', [$localId]);
            if ($ponto !== null && $ponto['congregacao_id'] !== null) {
                return (int) $ponto['congregacao_id'];
            }
        }

        return null;
    }

    private function locais(): array
    {
        $locais = ['IGREJA' => [], 'CONGREGACAO' => [], 'PONTO' => []];

        foreach (ConteudoPublico::igrejas() as $igreja) {
            $locais['IGREJA'][] = [
                'valor'  => 'IGREJA:' . (int) $igreja['id'],
                'rotulo' => 'Igreja: ' . $igreja['nome'],
            ];
        }
        foreach (ConteudoPublico::congregacoes() as $congregacao) {
            $locais['CONGREGACAO'][] = [
                'valor'  => 'CONGREGACAO:' . (int) $congregacao['id'],
                'rotulo' => 'Congregação: ' . $congregacao['nome'],
            ];
        }
        foreach (ConteudoPublico::pontos() as $ponto) {
            $locais['PONTO'][] = [
                'valor'  => 'PONTO:' . (int) $ponto['id'],
                'rotulo' => 'Ponto: ' . $ponto['nome'],
            ];
        }

        return $locais;
    }

    private function falhar(string $mensagem): never
    {
        Flash::error($mensagem);
        $this->redirectAdmin('horarios');
    }
}
