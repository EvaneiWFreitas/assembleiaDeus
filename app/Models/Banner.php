<?php

declare(strict_types=1);

namespace App\Models;

final class Banner
{
    private const CHAVE  = 'banner_principal';
    private const LIMITE = 6;

    public static function caminhos(): array
    {
        $valor = trim(Configuracao::get(self::CHAVE, ''));
        if ($valor === '') {
            return [];
        }

        $decodificado = json_decode($valor, true);
        if (!is_array($decodificado)) {
            return [$valor];
        }

        $caminhos = [];
        foreach ($decodificado as $item) {
            if (!is_string($item)) {
                continue;
            }

            $item = trim($item);
            if ($item !== '') {
                $caminhos[] = $item;
            }
        }

        return array_values(array_unique(array_slice($caminhos, 0, self::LIMITE)));
    }

    public static function exibir(): array
    {
        $urls = array_map(static fn (string $caminho): string => url($caminho), self::caminhos());

        return $urls === [] ? [asset('images/banner.svg')] : $urls;
    }

    public static function adicionar(array $novos): int
    {
        $atuais    = self::caminhos();
        $disponiveis = self::LIMITE - count($atuais);

        if ($disponiveis <= 0) {
            return 0;
        }

        $novos = array_values(array_unique(array_slice($novos, 0, $disponiveis)));
        if ($novos === []) {
            return 0;
        }

        self::salvar(array_merge($atuais, $novos));

        return count($novos);
    }

    public static function removerIndice(int $indice): ?string
    {
        $atuais = self::caminhos();

        if (!isset($atuais[$indice])) {
            return null;
        }

        $removido = $atuais[$indice];
        unset($atuais[$indice]);
        self::salvar(array_values($atuais));

        return $removido;
    }

    public static function limpar(): void
    {
        self::salvar([]);
    }

    public static function limite(): int
    {
        return self::LIMITE;
    }

    private static function salvar(array $caminhos): void
    {
        $caminhos = array_values(array_unique(array_filter(
            array_map(static fn ($caminho): string => trim((string) $caminho), $caminhos),
            static fn (string $caminho): bool => $caminho !== ''
        )));

        Configuracao::set(
            self::CHAVE,
            json_encode(array_slice($caminhos, 0, self::LIMITE), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}
