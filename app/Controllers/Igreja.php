<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\IgrejaModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Dados da igreja (registro único).
 */
class Igreja extends BaseController
{
    private IgrejaModel $igrejas;

    public function __construct()
    {
        $this->igrejas = new IgrejaModel();
    }

    public function editar(): string
    {
        $this->exigirPermissao('igreja', 'visualizar');

        $this->dados['titulo']   = 'Dados da Igreja';
        $this->dados['registro'] = $this->igrejas->getIgreja();

        return view('igrejas/form', $this->dados);
    }

    public function atualizar(): RedirectResponse
    {
        $this->exigirPermissao('igreja', 'editar');

        $igreja = $this->igrejas->getIgreja();

        if (! $this->validate($this->igrejas->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = [
            'razao_social'       => trim((string) $this->request->getPost('razao_social')),
            'nome'               => trim((string) $this->request->getPost('nome')),
            'cnpj'               => preg_replace('/\D/', '', (string) $this->request->getPost('cnpj')) ?: null,
            'telefone'           => trim((string) $this->request->getPost('telefone')) ?: null,
            'whatsapp'           => trim((string) $this->request->getPost('whatsapp')) ?: null,
            'email'              => trim((string) $this->request->getPost('email')) ?: null,
            'site'               => trim((string) $this->request->getPost('site')) ?: null,
            'cep'                => trim((string) $this->request->getPost('cep')) ?: null,
            'logradouro'         => trim((string) $this->request->getPost('logradouro')) ?: null,
            'numero'             => trim((string) $this->request->getPost('numero')) ?: null,
            'complemento'        => trim((string) $this->request->getPost('complemento')) ?: null,
            'bairro'             => trim((string) $this->request->getPost('bairro')) ?: null,
            'cidade'             => trim((string) $this->request->getPost('cidade')) ?: null,
            'estado'             => strtoupper(trim((string) $this->request->getPost('estado'))) ?: null,
            'pais'               => trim((string) $this->request->getPost('pais')) ?: 'Brasil',
            'pastor_responsavel' => trim((string) $this->request->getPost('pastor_responsavel')) ?: null,
            'data_fundacao'      => $this->request->getPost('data_fundacao') ?: null,
        ];

        // Upload de logo (seguro: apenas imagens, tamanho limitado)
        $arquivo = $this->request->getFile('logo');
        if ($arquivo !== null && $arquivo->isValid() && ! $arquivo->hasMoved()) {
            if (! in_array($arquivo->getMimeType(), ['image/png', 'image/jpeg', 'image/webp'], true) || $arquivo->getSizeByUnit('mb') > 2) {
                return redirect()->back()->withInput()->with('erro', 'Logo inválida: use PNG/JPG/WebP de até 2MB.');
            }
            $nome = $arquivo->getRandomName();
            $arquivo->move(ROOTPATH . 'public/uploads', $nome);
            $dados['logo'] = $nome;
        }

        $this->igrejas->update($igreja['id'], $dados);
        (new Auditoria())->log('editar', 'igreja', (int) $igreja['id'], $igreja, $dados);

        return redirect()->to('/igreja')->with('sucesso', 'Dados da igreja atualizados com sucesso!');
    }
}
