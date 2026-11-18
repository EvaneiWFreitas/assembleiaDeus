<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Flash;

/**
 * Utilitários compartilhados pelos cadastros de estrutura da ETAPA 6.
 */
abstract class BaseEstruturaController extends AdminController
{
    protected function texto(mixed $valor, ?int $max = null): ?string
    {
        if (!is_scalar($valor)) {
            return null;
        }

        $texto = trim((string) $valor);
        if ($texto === '') {
            return null;
        }

        return $max === null ? $texto : mb_substr($texto, 0, $max);
    }

    protected function inteiro(mixed $valor): ?int
    {
        if (!is_scalar($valor)) {
            return null;
        }

        $texto = trim((string) $valor);

        return $texto === '' ? null : (int) $texto;
    }

    protected function estado(mixed $valor): ?string
    {
        $estado = $this->texto($valor, 2);

        return $estado === null ? null : mb_strtoupper($estado);
    }

    protected function cpf(mixed $valor): ?string
    {
        $digitos = soDigitos($valor);

        return $digitos === '' ? null : mb_substr($digitos, 0, 11);
    }

    protected function data(mixed $valor): ?string
    {
        $texto = $this->texto($valor, 10);
        if ($texto === null) {
            return null;
        }

        $data = \DateTimeImmutable::createFromFormat('Y-m-d', $texto);

        return $data !== false && $data->format('Y-m-d') === $texto ? $texto : null;
    }

    protected function validarObrigatorios(array $dados, array $obrigatorios): array
    {
        $erros = [];
        foreach ($obrigatorios as $campo => $rotulo) {
            if (empty($dados[$campo])) {
                $erros[] = "Informe {$rotulo}.";
            }
        }

        if (!empty($dados['estado']) && !preg_match('/^[A-Z]{2}$/', (string) $dados['estado'])) {
            $erros[] = 'Informe um estado válido com 2 letras.';
        }

        foreach (['data_fundacao', 'data_abertura'] as $campoData) {
            if (!empty($dados[$campoData]) && $this->data($dados[$campoData]) === null) {
                $erros[] = 'Informe uma data válida.';
            }
        }

        return $erros;
    }

    protected function falhar(array $erros, string $rota): never
    {
        Flash::error(implode(' ', $erros));
        $this->redirectAdmin($rota);
    }

    protected function guardarOld(array $dados): void
    {
        $_SESSION['old'] = $dados;
    }

    protected function limparOld(): void
    {
        unset($_SESSION['old']);
    }
}
