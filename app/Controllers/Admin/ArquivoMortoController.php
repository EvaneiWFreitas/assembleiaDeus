<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Acl;
use App\Core\Audit;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\ArquivoMorto;

/**
 * Arquivo morto de obreiros e restauração controlada.
 */
final class ArquivoMortoController extends BasePessoaController
{
    public function index(): void
    {
        $this->guard('arquivo_morto.ver');
        $this->limparOld();

        $filtros = [
            'busca'          => $this->texto($this->request->query('busca')),
            'estado'         => $this->texto($this->request->query('estado')),
            'congregacao_id' => $this->inteiro($this->request->query('congregacao_id')),
        ];

        $this->renderAdmin('admin.arquivo-morto.index', [
            'title'          => 'Arquivo Morto',
            'titulo'         => 'Arquivo Morto',
            'descricao'      => 'Consulte os desligamentos preservados e restaure obreiros quando necessário.',
            'itens'          => ArquivoMorto::all($filtros),
            'rota'           => 'admin/arquivo-morto',
            'filtros'        => $filtros,
            'congregacoes'   => $this->congregacoesOptions(),
            'podeArquivar'   => Acl::can('arquivo_morto.arquivar'),
            'podeRestaurar'  => $this->podeRestaurar(),
        ]);
    }

    public function arquivarForm(): void
    {
        $this->guard('arquivo_morto.arquivar');

        $obreiros = ArquivoMorto::obreirosDisponiveis();
        if ($obreiros === []) {
            Flash::warning('Não há obreiros elegíveis para arquivamento.');
            $this->redirectAdmin('arquivo-morto');
        }

        $opcoes = [];
        foreach ($obreiros as $obreiro) {
            $rotulo = $obreiro['nome_completo'] . ' — ' . $obreiro['codigo']
                . ' — ' . ($obreiro['cargo_nome'] ?? 'Sem cargo');
            $opcoes[(int) $obreiro['id']] = $rotulo;
        }

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Arquivar obreiro',
            'titulo'    => 'Arquivar obreiro',
            'subtitulo' => 'O registro original será preservado e marcado como arquivado.',
            'acao'      => url('admin/arquivo-morto/arquivar'),
            'voltar'    => 'admin/arquivo-morto',
            'botao'     => 'Arquivar obreiro',
            'registro'  => [
                'obreiro_id'        => $this->inteiro($this->request->query('obreiro_id')) ?? '',
                'data_desligamento' => date('Y-m-d'),
            ],
            'campos'    => [
                [
                    'name' => 'obreiro_id', 'label' => 'Obreiro', 'type' => 'select', 'required' => true,
                    'col' => 'col-md-8', 'options' => $opcoes, 'placeholder' => 'Selecione o obreiro...',
                ],
                [
                    'name' => 'data_desligamento', 'label' => 'Data do desligamento', 'type' => 'date',
                    'required' => true, 'col' => 'col-md-4',
                ],
                [
                    'name' => 'motivo', 'label' => 'Motivo', 'type' => 'textarea', 'required' => true,
                    'col' => 'col-12', 'rows' => 3,
                ],
                [
                    'name' => 'observacoes', 'label' => 'Observações', 'type' => 'textarea',
                    'col' => 'col-12', 'rows' => 3,
                ],
            ],
        ]);
    }

    public function arquivar(): void
    {
        $this->guard('arquivo_morto.arquivar');

        $dados = [
            'obreiro_id'        => $this->inteiro($this->request->post('obreiro_id')),
            'data_desligamento' => $this->data($this->request->post('data_desligamento')),
            'motivo'            => $this->texto($this->request->post('motivo')),
            'observacoes'       => $this->texto($this->request->post('observacoes')),
            'responsavel_id'    => Auth::id(),
        ];

        $erros = [];
        if ($dados['obreiro_id'] === null || ArquivoMorto::obreiroDisponivel($dados['obreiro_id']) === null) {
            $erros[] = 'Selecione um obreiro elegível.';
        }
        if ($dados['data_desligamento'] === null) {
            $erros[] = 'Informe uma data de desligamento válida.';
        }
        if ($dados['motivo'] === null) {
            $erros[] = 'Informe o motivo do desligamento.';
        }

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'arquivo-morto/arquivar');
        }

        $id = ArquivoMorto::arquivar($dados);
        Audit::record(
            'arquivar',
            'arquivo_morto',
            ['obreiro_id' => $dados['obreiro_id'], 'data_desligamento' => $dados['data_desligamento']],
            [],
            'arquivo_morto_obreiros',
            (string) $id
        );

        $this->limparOld();
        Flash::success('Obreiro arquivado com sucesso.');
        $this->redirectAdmin('arquivo-morto');
    }

    public function detalhes(string $id): void
    {
        $this->guard('arquivo_morto.ver');

        $arquivo = ArquivoMorto::find((int) $id);
        if ($arquivo === null) {
            Flash::error('Registro de arquivo morto não encontrado.');
            $this->redirectAdmin('arquivo-morto');
        }

        $this->renderAdmin('admin.arquivo-morto.detalhes', [
            'title'          => 'Ficha histórica',
            'arquivo'        => $arquivo,
            'dados'          => ArquivoMorto::decodificar($arquivo['dados_snapshot'] ?? null),
            'historico'      => ArquivoMorto::decodificar($arquivo['historico_snapshot'] ?? null),
            'podeRestaurar'  => $this->podeRestaurar() && empty($arquivo['restaurado_em']),
        ]);
    }

    public function restaurarForm(string $id): void
    {
        $this->guardRestaurar();

        $arquivo = ArquivoMorto::find((int) $id);
        if ($arquivo === null) {
            Flash::error('Registro de arquivo morto não encontrado.');
            $this->redirectAdmin('arquivo-morto');
        }
        if (!empty($arquivo['restaurado_em'])) {
            Flash::warning('Este obreiro já foi restaurado.');
            $this->redirectAdmin('arquivo-morto/' . (int) $id);
        }

        $this->renderAdmin('admin.arquivo-morto.restaurar', [
            'title'   => 'Restaurar obreiro',
            'arquivo' => $arquivo,
        ]);
    }

    public function restaurar(string $id): void
    {
        $this->guardRestaurar();

        $arquivo = ArquivoMorto::find((int) $id);
        if ($arquivo === null) {
            Flash::error('Registro de arquivo morto não encontrado.');
            $this->redirectAdmin('arquivo-morto');
        }

        try {
            ArquivoMorto::restaurar((int) $id, (int) Auth::id());
        } catch (\RuntimeException $e) {
            Flash::error($e->getMessage());
            $this->redirectAdmin('arquivo-morto/' . (int) $id);
        }

        Audit::record(
            'restaurar',
            'arquivo_morto',
            ['restaurado_em' => date('Y-m-d H:i:s')],
            ['obreiro_id' => (int) $arquivo['obreiro_id']],
            'arquivo_morto_obreiros',
            (string) (int) $id
        );

        Flash::success('Obreiro restaurado com sucesso.');
        $this->redirectAdmin('arquivo-morto/' . (int) $id);
    }

    private function guardRestaurar(): void
    {
        $this->guard('arquivo_morto.restaurar');
        Acl::requireNivel(100);
    }

    private function podeRestaurar(): bool
    {
        $perfil = Auth::perfil();

        return Acl::can('arquivo_morto.restaurar')
            && $perfil !== null
            && (int) $perfil['nivel'] >= 100;
    }
}
