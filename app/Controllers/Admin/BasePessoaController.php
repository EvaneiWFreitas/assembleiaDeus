<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Congregacao;
use App\Models\Igreja;
use App\Models\Usuario;

/**
 * Utilitários compartilhados pelos cadastros de pessoas da ETAPA 7.
 */
abstract class BasePessoaController extends BaseEstruturaController
{
    protected function congregacoesOptions(): array
    {
        $opcoes = [];
        foreach (Congregacao::options() as $congregacao) {
            $opcoes[(int) $congregacao['id']] = $congregacao['nome'] . ' / ' . $congregacao['igreja_nome'];
        }

        return $opcoes;
    }

    protected function igrejasOptions(): array
    {
        $opcoes = [];
        foreach (Igreja::options() as $igreja) {
            $opcoes[(int) $igreja['id']] = $igreja['nome'];
        }

        return $opcoes;
    }

    protected function usuariosOptions(): array
    {
        $opcoes = [];
        foreach (Usuario::all() as $usuario) {
            $opcoes[(int) $usuario['id']] = $usuario['nome'];
        }

        return $opcoes;
    }

    protected function filtrosDaRequisicao(): array
    {
        return [
            'busca'          => $this->texto($this->request->query('busca'), 100) ?? '',
            'situacao'       => $this->texto($this->request->query('situacao'), 30),
            'congregacao_id' => $this->inteiro($this->request->query('congregacao_id')),
            'cargo_id'       => $this->inteiro($this->request->query('cargo_id')),
        ];
    }

    protected function filtrosFormulario(string $acao, array $filtros, array $situacoes, array $extras = []): array
    {
        $campos = [
            [
                'name' => 'busca', 'label' => 'Buscar', 'type' => 'search', 'col' => 'col-md-4',
                'value' => $filtros['busca'] ?? '', 'placeholder' => 'Nome, código, CPF ou e-mail',
            ],
            [
                'name' => 'situacao', 'label' => 'Situação', 'type' => 'select', 'col' => 'col-md-2',
                'value' => $filtros['situacao'] ?? '', 'options' => $situacoes,
            ],
            [
                'name' => 'congregacao_id', 'label' => 'Congregação', 'type' => 'select', 'col' => 'col-md-3',
                'value' => $filtros['congregacao_id'] ?? '', 'options' => $this->congregacoesOptions(),
            ],
        ];

        foreach ($extras as $extra) {
            $campos[] = $extra;
        }

        return ['acao' => url($acao), 'campos' => $campos];
    }

    protected function validarPessoaComum(array $dados, array $obrigatorios, callable $congregacaoPermitida): array
    {
        $erros = $this->validarObrigatorios($dados, $obrigatorios);

        if ($dados['cpf'] !== null && strlen($dados['cpf']) !== 11) {
            $erros[] = 'Informe um CPF com 11 dígitos.';
        }
        if ($dados['email'] !== null && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        }
        if ($dados['congregacao_id'] !== null && !$congregacaoPermitida($dados['congregacao_id'])) {
            $erros[] = 'Você não pode vincular esta pessoa a outra congregação.';
        }

        return $erros;
    }
}
