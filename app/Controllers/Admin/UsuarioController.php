<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Audit;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\Congregacao;
use App\Models\Usuario;
use PDOException;

/**
 * Gerenciamento de contas com acesso ao painel administrativo.
 */
final class UsuarioController extends BaseEstruturaController
{
    public function index(): void
    {
        $this->guard('seguranca.usuarios_ver');
        $this->limparOld();

        $this->renderAdmin('admin.usuarios.index', [
            'title'            => 'Usuários',
            'usuarios'         => Usuario::all(),
            'usuario_logado_id'=> Auth::id(),
            'permissoes'       => $this->permissoes(),
        ]);
    }

    public function criarForm(): void
    {
        $this->guard('seguranca.usuarios_criar');

        $this->formulario('Novo usuário', 'Crie uma conta com acesso ao painel administrativo.', false, []);
    }

    public function criar(): void
    {
        $this->guard('seguranca.usuarios_criar');

        $dados = $this->normalizar();
        $erros = $this->validar($dados, false, null);
        if ($erros !== []) {
            $this->falhar($erros, 'usuarios/novo');
        }

        $dados['senha_hash'] = hashSenha($dados['senha']);
        $id = Usuario::create($dados);

        Audit::record('criar', 'seguranca', $this->dadosSemSenha($dados), [], 'usuarios', $id);
        $this->limparOld();
        Flash::success('Usuário criado com sucesso.');
        $this->redirectAdmin('usuarios');
    }

    public function editarForm(string $id): void
    {
        $this->guard('seguranca.usuarios_editar');

        $usuario = Usuario::find((int) $id);
        if ($usuario === null || !$this->podeGerenciar($usuario)) {
            Flash::error('Usuário não encontrado ou sem permissão para edição.');
            $this->redirectAdmin('usuarios');
        }

        $this->formulario('Editar usuário', 'Atualize os dados e o acesso da conta.', true, $usuario);
    }

    public function editar(string $id): void
    {
        $this->guard('seguranca.usuarios_editar');

        $usuario = Usuario::find((int) $id);
        if ($usuario === null || !$this->podeGerenciar($usuario)) {
            Flash::error('Usuário não encontrado ou sem permissão para edição.');
            $this->redirectAdmin('usuarios');
        }

        $dados = $this->normalizar();
        $erros = $this->validar($dados, true, $usuario);
        if ($erros !== []) {
            $this->falhar($erros, 'usuarios/' . (int) $id . '/editar');
        }

        if ($dados['senha'] !== '') {
            $dados['senha_hash'] = hashSenha($dados['senha']);
        }

        Usuario::update((int) $id, $dados);
        Auth::clearPermissionsCache();

        $anteriores = $this->dadosSemSenha($usuario);
        $novos = $this->dadosSemSenha($dados);
        if ($dados['senha_hash'] ?? null) {
            $novos['senha'] = '***';
        }

        Audit::record('editar', 'seguranca', $novos, $anteriores, 'usuarios', (string) (int) $id);
        $this->limparOld();
        Flash::success('Usuário atualizado com sucesso.');
        $this->redirectAdmin('usuarios');
    }

    public function excluir(string $id): void
    {
        $this->guard('seguranca.usuarios_excluir');

        $usuario = Usuario::find((int) $id);
        if ($usuario === null || !$this->podeGerenciar($usuario)) {
            Flash::error('Usuário não encontrado ou sem permissão para exclusão.');
            $this->redirectAdmin('usuarios');
        }

        if ((int) $usuario['id'] === Auth::id()) {
            Flash::error('Você não pode excluir a sua própria conta.');
            $this->redirectAdmin('usuarios');
        }

        if ((int) $usuario['perfil_nivel'] >= 100 && Usuario::contarSuperadmins() <= 1) {
            Flash::error('Não é possível excluir o último superadministrador.');
            $this->redirectAdmin('usuarios');
        }

        try {
            Usuario::delete((int) $id);
        } catch (PDOException) {
            Flash::error('Não foi possível excluir este usuário.');
            $this->redirectAdmin('usuarios');
        }

        Audit::record('excluir', 'seguranca', [], $this->dadosSemSenha($usuario), 'usuarios', (string) (int) $id);
        Flash::success('Usuário excluído com sucesso.');
        $this->redirectAdmin('usuarios');
    }

    private function formulario(string $titulo, string $subtitulo, bool $edicao, array $usuario): void
    {
        $perfis = [];
        foreach (Usuario::perfisOptions() as $perfil) {
            $perfis[(int) $perfil['id']] = [
                'nome'  => (string) $perfil['nome'],
                'nivel' => (int) $perfil['nivel'],
            ];
        }

        $congregacoes = [];
        foreach (Congregacao::options() as $congregacao) {
            $congregacoes[(int) $congregacao['id']] = trim(
                (string) $congregacao['nome'] . ' - ' . (string) $congregacao['igreja_nome'],
                ' -'
            );
        }

        $this->renderAdmin('admin.usuarios.form', [
            'title'        => $titulo,
            'titulo'       => $titulo,
            'subtitulo'    => $subtitulo,
            'acao'         => $edicao ? url('admin/usuarios/' . (int) $usuario['id'] . '/editar') : url('admin/usuarios'),
            'voltar'       => 'admin/usuarios',
            'botao'        => $edicao ? 'Salvar alterações' : 'Salvar usuário',
            'usuario'      => $usuario,
            'perfis'       => $perfis,
            'congregacoes' => $congregacoes,
            'edicao'       => $edicao,
        ]);
    }

    private function normalizar(): array
    {
        return [
            'nome'                  => $this->texto($this->request->post('nome'), 150),
            'email'                 => mb_strtolower((string) $this->texto($this->request->post('email'), 190)),
            'telefone'              => $this->texto($this->request->post('telefone'), 20),
            'perfil_id'             => $this->inteiro($this->request->post('perfil_id')),
            'congregacao_id'        => $this->inteiro($this->request->post('congregacao_id')),
            'senha'                 => (string) $this->request->post('senha', ''),
            'senha_confirmacao'     => (string) $this->request->post('senha_confirmacao', ''),
            'ativo'                 => $this->request->post('ativo') !== null ? 1 : 0,
            'must_change_password'  => $this->request->post('must_change_password') !== null ? 1 : 0,
        ];
    }

    private function validar(array $dados, bool $edicao, ?array $usuarioAtual): array
    {
        $erros = [];

        if (empty($dados['nome'])) {
            $erros[] = 'Informe o nome do usuário.';
        }
        if (filter_var($dados['email'], FILTER_VALIDATE_EMAIL) === false) {
            $erros[] = 'Informe um e-mail válido.';
        } elseif (Usuario::emailExiste($dados['email'], $edicao ? (int) $usuarioAtual['id'] : null)) {
            $erros[] = 'Este e-mail já está em uso.';
        }

        $perfis = Usuario::perfisOptions();
        $perfilIds = array_map('intval', array_column($perfis, 'id'));
        if ($dados['perfil_id'] === null || !in_array($dados['perfil_id'], $perfilIds, true)) {
            $erros[] = 'Selecione um perfil válido.';
        } else {
            $perfil = Auth::perfil();
            if ($perfil !== null && (int) $perfil['nivel'] < 100) {
                $niveis = [];
                foreach ($perfis as $opcao) {
                    $niveis[(int) $opcao['id']] = (int) $opcao['nivel'];
                }
                if ($niveis[$dados['perfil_id']] >= (int) $perfil['nivel']) {
                    $erros[] = 'Você não pode atribuir um perfil com nível maior ou igual ao seu.';
                }
            }
        }

        if ($dados['congregacao_id'] !== null) {
            $congregacoes = array_map('intval', array_column(Congregacao::options(), 'id'));
            if (!in_array($dados['congregacao_id'], $congregacoes, true)) {
                $erros[] = 'Selecione uma congregação válida.';
            }
        }

        if (!$edicao || $dados['senha'] !== '') {
            if (mb_strlen($dados['senha']) < 8) {
                $erros[] = 'A senha deve ter ao menos 8 caracteres.';
            }
            if ($dados['senha'] !== $dados['senha_confirmacao']) {
                $erros[] = 'As senhas informadas não conferem.';
            }
        }

        if ($edicao && (int) $usuarioAtual['id'] === Auth::id()) {
            if ($dados['perfil_id'] !== (int) $usuarioAtual['perfil_id']) {
                $erros[] = 'Você não pode alterar o seu próprio perfil.';
            }
            if ($dados['ativo'] !== 1) {
                $erros[] = 'Você não pode desativar a sua própria conta.';
            }
        }

        if ($erros !== []) {
            $this->guardarOld($this->dadosSemSenha($dados));
        }

        return $erros;
    }

    private function podeGerenciar(array $usuario): bool
    {
        $perfil = Auth::perfil();
        if ($perfil === null) {
            return false;
        }

        if ((int) $perfil['nivel'] >= 100) {
            return true;
        }

        return (int) ($usuario['perfil_nivel'] ?? 100) < (int) $perfil['nivel'];
    }

    private function dadosSemSenha(array $dados): array
    {
        return [
            'nome'                  => $dados['nome'] ?? null,
            'email'                 => $dados['email'] ?? null,
            'telefone'              => $dados['telefone'] ?? null,
            'perfil_id'             => $dados['perfil_id'] ?? null,
            'congregacao_id'        => $dados['congregacao_id'] ?? null,
            'ativo'                 => $dados['ativo'] ?? null,
            'must_change_password'  => $dados['must_change_password'] ?? null,
        ];
    }

    private function permissoes(): array
    {
        return [
            'criar'   => 'seguranca.usuarios_criar',
            'editar'  => 'seguranca.usuarios_editar',
            'excluir' => 'seguranca.usuarios_excluir',
        ];
    }
}
