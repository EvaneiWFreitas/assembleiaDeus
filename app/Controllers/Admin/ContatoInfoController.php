<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Audit;
use App\Core\Flash;
use App\Models\Configuracao;

final class ContatoInfoController extends AdminController
{
    private const TAMANHO_IMAGEM = 2097152;

    private const CAMPOS = [
        'ministerio_cnpj'           => ['post' => 'cnpj',            'max' => 40,  'default' => ''],
        'ministerio_endereco'       => ['post' => 'endereco',        'max' => 255, 'default' => ''],
        'ministerio_telefone'       => ['post' => 'telefone',        'max' => 40,  'default' => ''],
        'ministerio_whatsapp'       => ['post' => 'whatsapp',        'max' => 40,  'default' => ''],
        'ministerio_email'          => ['post' => 'email',           'max' => 150, 'default' => ''],
        'pastor_presidente'         => ['post' => 'pastor_nome',     'max' => 120, 'default' => ''],
        'contato_rotulo_pastor'     => ['post' => 'rotulo_pastor',   'max' => 80,  'default' => 'Pastor Presidente'],
        'contato_eyebrow'           => ['post' => 'eyebrow',         'max' => 80,  'default' => 'Estamos à disposição'],
        'contato_titulo'            => ['post' => 'titulo_pagina',   'max' => 120, 'default' => 'Fale conosco'],
        'contato_bloco_titulo'      => ['post' => 'titulo_bloco',    'max' => 120, 'default' => 'Informações'],
        'contato_home_eyebrow'      => ['post' => 'home_eyebrow',    'max' => 80,  'default' => 'Fale conosco'],
        'contato_home_titulo'       => ['post' => 'home_titulo',     'max' => 120, 'default' => 'Entre em contato'],
        'contato_home_bloco_titulo' => ['post' => 'home_bloco',      'max' => 120, 'default' => 'Informações de contato'],
        'contato_mensagem_vazia'    => ['post' => 'mensagem_vazia',  'max' => 255, 'default' => 'Os dados de contato serão publicados aqui pela administração.'],
    ];

    public function form(): void
    {
        $this->guard('site.editar');

        $valores = [];
        foreach (self::CAMPOS as $chave => $regra) {
            $valores[$chave] = Configuracao::get($chave, $regra['default']);
        }
        $valores['pastor_foto'] = Configuracao::get('pastor_foto', '');

        $this->renderAdmin('admin.configuracoes.contato', [
            'title'    => 'Informações de contato',
            'subtitulo' => 'Gerencie os dados de contato e as informações do Pastor Presidente publicadas no site.',
            'valores'  => $valores,
        ]);
    }

    public function salvar(): void
    {
        $this->guard('site.editar');

        $anterior = $this->valoresAtuais();
        $novo = [];

        foreach (self::CAMPOS as $chave => $regra) {
            $valor = trim((string) $this->request->post($regra['post'], ''));
            if ($chave === 'ministerio_email' && $valor !== '' && filter_var($valor, FILTER_VALIDATE_EMAIL) === false) {
                $this->falhar('Informe um e-mail válido.');
            }
            $novo[$chave] = mb_substr($valor, 0, $regra['max']);
        }

        $fotoAnterior = Configuracao::get('pastor_foto', '');
        $novaFoto = $this->processarFoto();
        $novo['pastor_foto'] = $novaFoto ?? $fotoAnterior;

        foreach ($novo as $chave => $valor) {
            Configuracao::set($chave, $valor);
        }

        if ($novaFoto !== null && $fotoAnterior !== '') {
            $this->removerArquivo($fotoAnterior);
        }

        Audit::record(
            'editar',
            'site',
            $novo,
            array_merge($anterior, ['pastor_foto' => $fotoAnterior]),
            'configuracoes',
            'contato'
        );

        Flash::success('Informações de contato atualizadas.');
        $this->redirectAdmin('configuracoes/contato');
    }

    public function removerFoto(): void
    {
        $this->guard('site.editar');

        $anterior = Configuracao::get('pastor_foto', '');
        $this->removerArquivo($anterior);
        Configuracao::set('pastor_foto', '');

        Audit::record('editar', 'site', ['pastor_foto' => ''], ['pastor_foto' => $anterior], 'configuracoes', 'pastor_foto');

        Flash::success('Foto do Pastor Presidente removida.');
        $this->redirectAdmin('configuracoes/contato');
    }

    private function valoresAtuais(): array
    {
        $valores = [];
        foreach (self::CAMPOS as $chave => $regra) {
            $valores[$chave] = Configuracao::get($chave, $regra['default']);
        }

        return $valores;
    }

    private function processarFoto(): ?string
    {
        $arquivo = $this->request->file('foto_pastor');
        if ($arquivo === null || (int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ((int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || (int) ($arquivo['size'] ?? 0) > self::TAMANHO_IMAGEM) {
            $this->falhar('A foto do pastor deve ser JPG, PNG ou WebP, com até 2 MB.');
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
            $this->falhar('A foto do pastor deve ser JPG, PNG ou WebP, com até 2 MB.');
        }

        $nome = 'pastor-' . date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $extensoes[$mime];
        if (!move_uploaded_file($temporario, ROOT_PATH . '/public/uploads/' . $nome)) {
            $this->falhar('Não foi possível salvar a foto do pastor.');
        }

        return 'uploads/' . $nome;
    }

    private function removerArquivo(string $caminho): void
    {
        if ($caminho === '' || !str_starts_with($caminho, 'uploads/')) {
            return;
        }

        $caminhoCompleto = ROOT_PATH . '/public/' . $caminho;
        if (is_file($caminhoCompleto)) {
            @unlink($caminhoCompleto);
        }
    }

    private function falhar(string $mensagem): never
    {
        Flash::error($mensagem);
        $this->redirectAdmin('configuracoes/contato');
    }
}
