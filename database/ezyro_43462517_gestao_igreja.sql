-- ============================================================================
-- assembleiaDeus - SCRIPT DE INSTALACAO DO BANCO DE DADOS
-- ============================================================================
-- Banco   : ezyro_43462517_gestao_igreja
-- Usuario : ezyro_43462517
-- Servidor: sql303.ezyro.com (producao) / localhost (XAMPP)
-- Senha   : configurada EXCLUSIVAMENTE no arquivo .env (fora deste script)
--
-- ATENCAO: este script contem DROP TABLE IF EXISTS. Ele APAGA os dados das
--          tabelas abaixo antes de recria-las. Use apenas na instalacao inicial
--          ou em um banco de testes. Faca backup antes de reexecutar.
--
-- Como importar:
--   1) Crie o banco ezyro_43462517_gestao_igreja no phpMyAdmin (XAMPP) ou no
--      painel da FreeHost/Ezyro.
--   2) Selecione o banco e importe este arquivo (.sql).
--   3) Configure .env com DB_HOST, DB_NAME, DB_USER, DB_PASS (ETAPA 3).
--   4) O usuario administrador inicial sera criado pelo processo seguro
--      /admin/instalar (ETAPA 5), com troca de senha obrigatoria no 1o acesso.
--      Nao existe senha padrao gravada neste arquivo.
--
-- Compatibility: MySQL 5.7+ / MariaDB 10.2+ / MySQL 8.x
-- Charset      : utf8mb4 / utf8mb4_unicode_ci
-- Engine       : InnoDB para TODAS as tabelas
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- CREATE DATABASE IF NOT EXISTS `ezyro_43462517_gestao_igreja`
--   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `ezyro_43462517_gestao_igreja`;

-- ============================================================================
-- 1. IDENTIDADE E SEGURANCA
-- ============================================================================

DROP TABLE IF EXISTS `consentimentos_lgpd`;
DROP TABLE IF EXISTS `auditoria`;
DROP TABLE IF EXISTS `logs_acesso`;
DROP TABLE IF EXISTS `tokens_recuperacao`;
DROP TABLE IF EXISTS `contatos`;
DROP TABLE IF EXISTS `noticias`;
DROP TABLE IF EXISTS `paginas`;
DROP TABLE IF EXISTS `galeria`;
DROP TABLE IF EXISTS `eventos`;
DROP TABLE IF EXISTS `tipos_eventos`;
DROP TABLE IF EXISTS `carteirinhas`;
DROP TABLE IF EXISTS `documentos_emitidos`;
DROP TABLE IF EXISTS `modelos_documentos`;
DROP TABLE IF EXISTS `consagracoes`;
DROP TABLE IF EXISTS `batismos`;
DROP TABLE IF EXISTS `auditoria_financeira`;
DROP TABLE IF EXISTS `lancamentos_financeiros`;
DROP TABLE IF EXISTS `formas_pagamento`;
DROP TABLE IF EXISTS `categorias_financeiras`;
DROP TABLE IF EXISTS `arquivo_morto_obreiros`;
DROP TABLE IF EXISTS `historico_ministerial`;
DROP TABLE IF EXISTS `obreiros`;
DROP TABLE IF EXISTS `cargos_ministeriais`;
DROP TABLE IF EXISTS `membros`;
DROP TABLE IF EXISTS `horarios_cultos`;
DROP TABLE IF EXISTS `pontos_pregacao`;
DROP TABLE IF EXISTS `congregacoes`;
DROP TABLE IF EXISTS `igrejas_matriz`;
DROP TABLE IF EXISTS `redes_sociais`;
DROP TABLE IF EXISTS `configuracoes`;
DROP TABLE IF EXISTS `perfil_permissao`;
DROP TABLE IF EXISTS `permissoes`;
DROP TABLE IF EXISTS `usuarios`;
DROP TABLE IF EXISTS `perfis`;

-- ----------------------------------------------------------------------------
-- perfis: 7 niveis de acesso fixos
-- ----------------------------------------------------------------------------
CREATE TABLE `perfis` (
  `id`          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`        VARCHAR(60)  NOT NULL,
  `slug`        VARCHAR(60)  NOT NULL,
  `descricao`   VARCHAR(255) DEFAULT NULL,
  `nivel`       TINYINT UNSIGNED NOT NULL DEFAULT 10,
  `ativo`       TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_perfis_slug` (`slug`),
  UNIQUE KEY `uq_perfis_nome` (`nome`),
  KEY `idx_perfis_nivel` (`nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- usuarios: criado antes da estrutura para evitar FKs circulares
-- ----------------------------------------------------------------------------
CREATE TABLE `usuarios` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`                  VARCHAR(150) NOT NULL,
  `email`                 VARCHAR(190) NOT NULL,
  `senha_hash`            VARCHAR(255) NOT NULL,
  `perfil_id`             TINYINT UNSIGNED NOT NULL,
  `congregacao_id`        INT UNSIGNED DEFAULT NULL,
  `foto`                  VARCHAR(255) DEFAULT NULL,
  `telefone`              VARCHAR(20) DEFAULT NULL,
  `must_change_password`  TINYINT(1) NOT NULL DEFAULT 1,
  `ativo`                 TINYINT(1) NOT NULL DEFAULT 1,
  `ultimo_acesso`         DATETIME DEFAULT NULL,
  `tentativas_falha`      TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `bloqueado_ate`         DATETIME DEFAULT NULL,
  `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_email` (`email`),
  KEY `idx_usuarios_perfil` (`perfil_id`),
  KEY `idx_usuarios_congregacao` (`congregacao_id`),
  KEY `idx_usuarios_ativo` (`ativo`),
  CONSTRAINT `fk_usuarios_perfil` FOREIGN KEY (`perfil_id`)
    REFERENCES `perfis` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- permissoes / perfil_permissao (RBAC)
-- ----------------------------------------------------------------------------
CREATE TABLE `permissoes` (
  `id`          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `modulo`      VARCHAR(50) NOT NULL,
  `acao`        VARCHAR(50) NOT NULL,
  `slug`        VARCHAR(120) NOT NULL,
  `nome`        VARCHAR(120) NOT NULL,
  `descricao`   VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permissoes_slug` (`slug`),
  KEY `idx_permissoes_modulo` (`modulo`),
  KEY `idx_permissoes_acao` (`acao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `perfil_permissao` (
  `perfil_id`      TINYINT UNSIGNED NOT NULL,
  `permissao_id`   SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`perfil_id`, `permissao_id`),
  KEY `idx_pp_permissao` (`permissao_id`),
  CONSTRAINT `fk_pp_perfil` FOREIGN KEY (`perfil_id`)
    REFERENCES `perfis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pp_permissao` FOREIGN KEY (`permissao_id`)
    REFERENCES `permissoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- configuracoes (chave/valor) e redes_sociais
-- ----------------------------------------------------------------------------
CREATE TABLE `configuracoes` (
  `id`          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `chave`       VARCHAR(80) NOT NULL,
  `valor`       LONGTEXT DEFAULT NULL,
  `grupo`       VARCHAR(50) NOT NULL DEFAULT 'geral',
  `tipo`        VARCHAR(20) NOT NULL DEFAULT 'texto',
  `descricao`   VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_configuracoes_chave` (`chave`),
  KEY `idx_configuracoes_grupo` (`grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `redes_sociais` (
  `id`          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`        VARCHAR(50) NOT NULL,
  `url`         VARCHAR(255) NOT NULL,
  `icone`       VARCHAR(60) NOT NULL DEFAULT 'fas fa-link',
  `ordem`       TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `ativa`       TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_redes_ordem` (`ordem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. ESTRUTURA ECLESIASTICA
-- ============================================================================

CREATE TABLE `igrejas_matriz` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`                  VARCHAR(150) NOT NULL,
  `cnpj`                  VARCHAR(18) DEFAULT NULL,
  `endereco`              VARCHAR(255) NOT NULL,
  `numero`                VARCHAR(10) DEFAULT NULL,
  `complemento`           VARCHAR(100) DEFAULT NULL,
  `bairro`                VARCHAR(100) DEFAULT NULL,
  `cidade`                VARCHAR(100) NOT NULL,
  `estado`                CHAR(2) NOT NULL,
  `cep`                   VARCHAR(9) DEFAULT NULL,
  `telefone`              VARCHAR(20) DEFAULT NULL,
  `email`                 VARCHAR(190) DEFAULT NULL,
  `pastor_responsavel_id` INT UNSIGNED DEFAULT NULL,
  `data_fundacao`         DATE DEFAULT NULL,
  `foto`                  VARCHAR(255) DEFAULT NULL,
  `situacao`              ENUM('ATIVA','INATIVA','EM_PLANTIO') NOT NULL DEFAULT 'ATIVA',
  `observacoes`           TEXT DEFAULT NULL,
  `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_igrejas_cnpj` (`cnpj`),
  KEY `idx_igrejas_nome` (`nome`),
  KEY `idx_igrejas_cidade` (`cidade`),
  KEY `idx_igrejas_situacao` (`situacao`),
  KEY `idx_igrejas_pastor` (`pastor_responsavel_id`),
  CONSTRAINT `fk_igrejas_pastor` FOREIGN KEY (`pastor_responsavel_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `congregacoes` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo`                VARCHAR(20) NOT NULL,
  `nome`                  VARCHAR(150) NOT NULL,
  `igreja_matriz_id`      INT UNSIGNED NOT NULL,
  `endereco`              VARCHAR(255) NOT NULL,
  `numero`                VARCHAR(10) DEFAULT NULL,
  `complemento`           VARCHAR(100) DEFAULT NULL,
  `bairro`                VARCHAR(100) DEFAULT NULL,
  `cidade`                VARCHAR(100) NOT NULL,
  `estado`                CHAR(2) NOT NULL,
  `cep`                   VARCHAR(9) DEFAULT NULL,
  `telefone`              VARCHAR(20) DEFAULT NULL,
  `email`                 VARCHAR(190) DEFAULT NULL,
  `pastor_responsavel_id` INT UNSIGNED DEFAULT NULL,
  `data_abertura`         DATE DEFAULT NULL,
  `foto`                  VARCHAR(255) DEFAULT NULL,
  `situacao`              ENUM('ATIVA','INATIVA','EM_PLANTIO') NOT NULL DEFAULT 'ATIVA',
  `observacoes`           TEXT DEFAULT NULL,
  `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_congregacoes_codigo` (`codigo`),
  KEY `idx_congregacoes_igreja` (`igreja_matriz_id`),
  KEY `idx_congregacoes_nome` (`nome`),
  KEY `idx_congregacoes_cidade` (`cidade`),
  KEY `idx_congregacoes_situacao` (`situacao`),
  KEY `idx_congregacoes_pastor` (`pastor_responsavel_id`),
  CONSTRAINT `fk_congregacoes_igreja` FOREIGN KEY (`igreja_matriz_id`)
    REFERENCES `igrejas_matriz` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_congregacoes_pastor` FOREIGN KEY (`pastor_responsavel_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pontos_pregacao` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`              VARCHAR(150) NOT NULL,
  `congregacao_id`    INT UNSIGNED NOT NULL,
  `endereco`          VARCHAR(255) NOT NULL,
  `numero`            VARCHAR(10) DEFAULT NULL,
  `bairro`            VARCHAR(100) DEFAULT NULL,
  `cidade`            VARCHAR(100) NOT NULL,
  `estado`            CHAR(2) NOT NULL,
  `cep`               VARCHAR(9) DEFAULT NULL,
  `responsavel_id`    INT UNSIGNED DEFAULT NULL,
  `responsavel_nome`  VARCHAR(150) DEFAULT NULL,
  `dias_horarios`     VARCHAR(255) DEFAULT NULL,
  `data_abertura`     DATE DEFAULT NULL,
  `foto`              VARCHAR(255) DEFAULT NULL,
  `situacao`          ENUM('ATIVO','INATIVO') NOT NULL DEFAULT 'ATIVO',
  `observacoes`       TEXT DEFAULT NULL,
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pontos_congregacao` (`congregacao_id`),
  KEY `idx_pontos_nome` (`nome`),
  KEY `idx_pontos_cidade` (`cidade`),
  KEY `idx_pontos_situacao` (`situacao`),
  KEY `idx_pontos_responsavel` (`responsavel_id`),
  CONSTRAINT `fk_pontos_congregacao` FOREIGN KEY (`congregacao_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pontos_responsavel` FOREIGN KEY (`responsavel_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `horarios_cultos` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `local_tipo`      ENUM('IGREJA','CONGREGACAO','PONTO') NOT NULL,
  `local_id`        INT UNSIGNED NOT NULL,
  `congregacao_id`  INT UNSIGNED DEFAULT NULL,
  `dia_semana`      ENUM('DOMINGO','SEGUNDA','TERCA','QUARTA','QUINTA','SEXTA','SABADO') NOT NULL,
  `horario`         TIME NOT NULL,
  `descricao`       VARCHAR(120) DEFAULT NULL,
  `duracao_minutos` SMALLINT UNSIGNED DEFAULT NULL,
  `ativo`           TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_horarios_local` (`local_tipo`, `local_id`),
  KEY `idx_horarios_congregacao` (`congregacao_id`),
  KEY `idx_horarios_dia` (`dia_semana`),
  CONSTRAINT `fk_horarios_congregacao` FOREIGN KEY (`congregacao_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. PESSOAS (MEMBROS, OBREIROS, ARQUIVO MORTO)
-- ============================================================================

CREATE TABLE `membros` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo`            VARCHAR(20) NOT NULL,
  `nome_completo`     VARCHAR(180) NOT NULL,
  `foto`              VARCHAR(255) DEFAULT NULL,
  `cpf`               VARCHAR(14) DEFAULT NULL,
  `rg`                VARCHAR(20) DEFAULT NULL,
  `data_nascimento`   DATE DEFAULT NULL,
  `sexo`              ENUM('MASCULINO','FEMININO','NAO_INFORMADO') NOT NULL DEFAULT 'NAO_INFORMADO',
  `estado_civil`      ENUM('SOLTEIRO','CASADO','DIVORCIADO','VIUVO','UNIAO_ESTAVEL','NAO_INFORMADO') NOT NULL DEFAULT 'NAO_INFORMADO',
  `telefone`          VARCHAR(20) DEFAULT NULL,
  `whatsapp`          VARCHAR(20) DEFAULT NULL,
  `email`             VARCHAR(190) DEFAULT NULL,
  `endereco`          VARCHAR(255) DEFAULT NULL,
  `numero`            VARCHAR(10) DEFAULT NULL,
  `complemento`       VARCHAR(100) DEFAULT NULL,
  `bairro`            VARCHAR(100) DEFAULT NULL,
  `cidade`            VARCHAR(100) DEFAULT NULL,
  `estado`            CHAR(2) DEFAULT NULL,
  `cep`               VARCHAR(9) DEFAULT NULL,
  `data_ingresso`     DATE DEFAULT NULL,
  `igreja_origem_id`  INT UNSIGNED DEFAULT NULL,
  `congregacao_id`    INT UNSIGNED DEFAULT NULL,
  `data_batismo`      DATE DEFAULT NULL,
  `situacao`          ENUM('ATIVO','INATIVO','TRANSFERIDO','FALECIDO') NOT NULL DEFAULT 'ATIVO',
  `observacoes`       TEXT DEFAULT NULL,
  `created_by`        INT UNSIGNED DEFAULT NULL,
  `updated_by`        INT UNSIGNED DEFAULT NULL,
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_membros_codigo` (`codigo`),
  UNIQUE KEY `uq_membros_cpf` (`cpf`),
  KEY `idx_membros_nome` (`nome_completo`),
  KEY `idx_membros_congregacao` (`congregacao_id`),
  KEY `idx_membros_igreja_origem` (`igreja_origem_id`),
  KEY `idx_membros_situacao` (`situacao`),
  KEY `idx_membros_nascimento` (`data_nascimento`),
  KEY `idx_membros_ingresso` (`data_ingresso`),
  KEY `idx_membros_email` (`email`),
  KEY `idx_membros_created_by` (`created_by`),
  KEY `idx_membros_updated_by` (`updated_by`),
  CONSTRAINT `fk_membros_congregacao` FOREIGN KEY (`congregacao_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_membros_igreja_origem` FOREIGN KEY (`igreja_origem_id`)
    REFERENCES `igrejas_matriz` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_membros_created_by` FOREIGN KEY (`created_by`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_membros_updated_by` FOREIGN KEY (`updated_by`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cargos_ministeriais` (
  `id`          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`        VARCHAR(80) NOT NULL,
  `slug`        VARCHAR(80) NOT NULL,
  `descricao`   VARCHAR(255) DEFAULT NULL,
  `ativo`       TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cargos_nome` (`nome`),
  UNIQUE KEY `uq_cargos_slug` (`slug`),
  KEY `idx_cargos_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `obreiros` (
  `id`                          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo`                      VARCHAR(20) NOT NULL,
  `nome_completo`               VARCHAR(180) NOT NULL,
  `foto`                        VARCHAR(255) DEFAULT NULL,
  `cpf`                         VARCHAR(14) DEFAULT NULL,
  `rg`                          VARCHAR(20) DEFAULT NULL,
  `data_nascimento`             DATE DEFAULT NULL,
  `telefone`                    VARCHAR(20) DEFAULT NULL,
  `whatsapp`                    VARCHAR(20) DEFAULT NULL,
  `email`                       VARCHAR(190) DEFAULT NULL,
  `endereco`                    VARCHAR(255) DEFAULT NULL,
  `numero`                      VARCHAR(10) DEFAULT NULL,
  `complemento`                 VARCHAR(100) DEFAULT NULL,
  `bairro`                      VARCHAR(100) DEFAULT NULL,
  `cidade`                      VARCHAR(100) DEFAULT NULL,
  `estado`                      CHAR(2) DEFAULT NULL,
  `cep`                         VARCHAR(9) DEFAULT NULL,
  `igreja_origem_id`            INT UNSIGNED DEFAULT NULL,
  `congregacao_id`              INT UNSIGNED DEFAULT NULL,
  `cargo_id`                    SMALLINT UNSIGNED NOT NULL,
  `data_consagracao`            DATE DEFAULT NULL,
  `numero_registro_ministerial` VARCHAR(40) DEFAULT NULL,
  `situacao`                    ENUM('ATIVO','AFASTADO','ARQUIVADO','FALECIDO') NOT NULL DEFAULT 'ATIVO',
  `observacoes`                 TEXT DEFAULT NULL,
  `usuario_id`                  INT UNSIGNED DEFAULT NULL,
  `created_by`                  INT UNSIGNED DEFAULT NULL,
  `updated_by`                  INT UNSIGNED DEFAULT NULL,
  `created_at`                  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_obreiros_codigo` (`codigo`),
  UNIQUE KEY `uq_obreiros_cpf` (`cpf`),
  UNIQUE KEY `uq_obreiros_registro` (`numero_registro_ministerial`),
  UNIQUE KEY `uq_obreiros_usuario` (`usuario_id`),
  KEY `idx_obreiros_nome` (`nome_completo`),
  KEY `idx_obreiros_cargo` (`cargo_id`),
  KEY `idx_obreiros_congregacao` (`congregacao_id`),
  KEY `idx_obreiros_situacao` (`situacao`),
  KEY `idx_obreiros_created_by` (`created_by`),
  KEY `idx_obreiros_updated_by` (`updated_by`),
  CONSTRAINT `fk_obreiros_cargo` FOREIGN KEY (`cargo_id`)
    REFERENCES `cargos_ministeriais` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_obreiros_congregacao` FOREIGN KEY (`congregacao_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_obreiros_igreja_origem` FOREIGN KEY (`igreja_origem_id`)
    REFERENCES `igrejas_matriz` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_obreiros_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_obreiros_created_by` FOREIGN KEY (`created_by`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_obreiros_updated_by` FOREIGN KEY (`updated_by`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `historico_ministerial` (
  `id`                       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `obreiro_id`               INT UNSIGNED NOT NULL,
  `tipo`                     ENUM('NOMEACAO','MUDANCA_CARGO','TRANSFERENCIA','AFASTAMENTO','RETORNO','CONSAGRACAO','DESLIGAMENTO','OUTRO') NOT NULL,
  `cargo_anterior_id`        SMALLINT UNSIGNED DEFAULT NULL,
  `cargo_novo_id`            SMALLINT UNSIGNED DEFAULT NULL,
  `congregacao_anterior_id`  INT UNSIGNED DEFAULT NULL,
  `congregacao_nova_id`      INT UNSIGNED DEFAULT NULL,
  `data_evento`              DATE NOT NULL,
  `descricao`                TEXT DEFAULT NULL,
  `documento_referencia`     VARCHAR(100) DEFAULT NULL,
  `responsavel_id`           INT UNSIGNED DEFAULT NULL,
  `created_at`               TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`               TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_hist_obreiro` (`obreiro_id`),
  KEY `idx_hist_tipo` (`tipo`),
  KEY `idx_hist_data` (`data_evento`),
  KEY `idx_hist_cargo_anterior` (`cargo_anterior_id`),
  KEY `idx_hist_cargo_novo` (`cargo_novo_id`),
  KEY `idx_hist_cong_anterior` (`congregacao_anterior_id`),
  KEY `idx_hist_cong_nova` (`congregacao_nova_id`),
  KEY `idx_hist_responsavel` (`responsavel_id`),
  CONSTRAINT `fk_hist_obreiro` FOREIGN KEY (`obreiro_id`)
    REFERENCES `obreiros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_cargo_anterior` FOREIGN KEY (`cargo_anterior_id`)
    REFERENCES `cargos_ministeriais` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_cargo_novo` FOREIGN KEY (`cargo_novo_id`)
    REFERENCES `cargos_ministeriais` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_cong_anterior` FOREIGN KEY (`congregacao_anterior_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_cong_nova` FOREIGN KEY (`congregacao_nova_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_responsavel` FOREIGN KEY (`responsavel_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `arquivo_morto_obreiros` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `obreiro_id`            INT UNSIGNED NOT NULL,
  `motivo`                TEXT NOT NULL,
  `data_desligamento`     DATE NOT NULL,
  `responsavel_id`        INT UNSIGNED DEFAULT NULL,
  `dados_snapshot`        LONGTEXT DEFAULT NULL,
  `historico_snapshot`    LONGTEXT DEFAULT NULL,
  `observacoes`           TEXT DEFAULT NULL,
  `restaurado_em`         DATETIME DEFAULT NULL,
  `restaurado_por`        INT UNSIGNED DEFAULT NULL,
  `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_arquivo_obreiro` (`obreiro_id`),
  KEY `idx_arquivo_data` (`data_desligamento`),
  KEY `idx_arquivo_responsavel` (`responsavel_id`),
  KEY `idx_arquivo_restaurado` (`restaurado_em`),
  CONSTRAINT `fk_arquivo_obreiro` FOREIGN KEY (`obreiro_id`)
    REFERENCES `obreiros` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_arquivo_responsavel` FOREIGN KEY (`responsavel_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_arquivo_restaurado_por` FOREIGN KEY (`restaurado_por`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. FINANCEIRO
-- ============================================================================

CREATE TABLE `categorias_financeiras` (
  `id`          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`        VARCHAR(100) NOT NULL,
  `tipo`        ENUM('ENTRADA','SAIDA') NOT NULL,
  `descricao`   VARCHAR(255) DEFAULT NULL,
  `ativo`       TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categorias_nome_tipo` (`nome`, `tipo`),
  KEY `idx_categorias_tipo` (`tipo`),
  KEY `idx_categorias_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `formas_pagamento` (
  `id`                 TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`               VARCHAR(60) NOT NULL,
  `slug`               VARCHAR(60) NOT NULL,
  `requer_comprovante` TINYINT(1) NOT NULL DEFAULT 0,
  `ativo`              TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_formas_nome` (`nome`),
  UNIQUE KEY `uq_formas_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lancamentos_financeiros` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo`                ENUM('DIZIMO','OFERTA','ENTRADA','SAIDA') NOT NULL,
  `membro_id`           INT UNSIGNED DEFAULT NULL,
  `ofertante_nome`      VARCHAR(180) DEFAULT NULL,
  `congregacao_id`      INT UNSIGNED DEFAULT NULL,
  `categoria_id`        SMALLINT UNSIGNED NOT NULL,
  `forma_pagamento_id`  TINYINT UNSIGNED DEFAULT NULL,
  `valor`               DECIMAL(12,2) NOT NULL,
  `data_lancamento`     DATE NOT NULL,
  `referencia_mensal`   CHAR(7) DEFAULT NULL,
  `responsavel_id`      INT UNSIGNED DEFAULT NULL,
  `comprovante`         VARCHAR(255) DEFAULT NULL,
  `observacoes`         TEXT DEFAULT NULL,
  `estado`              ENUM('CONFIRMADO','PENDENTE','CANCELADO') NOT NULL DEFAULT 'CONFIRMADO',
  `created_by`          INT UNSIGNED DEFAULT NULL,
  `updated_by`          INT UNSIGNED DEFAULT NULL,
  `created_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lanc_tipo` (`tipo`),
  KEY `idx_lanc_data` (`data_lancamento`),
  KEY `idx_lanc_congregacao` (`congregacao_id`),
  KEY `idx_lanc_categoria` (`categoria_id`),
  KEY `idx_lanc_forma` (`forma_pagamento_id`),
  KEY `idx_lanc_membro` (`membro_id`),
  KEY `idx_lanc_referencia` (`referencia_mensal`),
  KEY `idx_lanc_estado` (`estado`),
  KEY `idx_lanc_responsavel` (`responsavel_id`),
  KEY `idx_lanc_created_by` (`created_by`),
  KEY `idx_lanc_updated_by` (`updated_by`),
  CONSTRAINT `fk_lanc_membro` FOREIGN KEY (`membro_id`)
    REFERENCES `membros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_lanc_congregacao` FOREIGN KEY (`congregacao_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_lanc_categoria` FOREIGN KEY (`categoria_id`)
    REFERENCES `categorias_financeiras` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_lanc_forma` FOREIGN KEY (`forma_pagamento_id`)
    REFERENCES `formas_pagamento` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_lanc_responsavel` FOREIGN KEY (`responsavel_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_lanc_created_by` FOREIGN KEY (`created_by`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_lanc_updated_by` FOREIGN KEY (`updated_by`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `auditoria_financeira` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lancamento_id`     BIGINT UNSIGNED NOT NULL,
  `acao`              ENUM('CRIAR','EDITAR','CANCELAR','ESTORNAR') NOT NULL,
  `dados_anteriores`  LONGTEXT DEFAULT NULL,
  `dados_novos`       LONGTEXT DEFAULT NULL,
  `motivo`            VARCHAR(255) DEFAULT NULL,
  `usuario_id`        INT UNSIGNED DEFAULT NULL,
  `ip`                VARCHAR(45) DEFAULT NULL,
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audfin_lancamento` (`lancamento_id`),
  KEY `idx_audfin_acao` (`acao`),
  KEY `idx_audfin_usuario` (`usuario_id`),
  KEY `idx_audfin_data` (`created_at`),
  CONSTRAINT `fk_audfin_lancamento` FOREIGN KEY (`lancamento_id`)
    REFERENCES `lancamentos_financeiros` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_audfin_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. SACRAMENTOS E DOCUMENTOS
-- ============================================================================

CREATE TABLE `batismos` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `membro_id`             INT UNSIGNED NOT NULL,
  `data_batismo`          DATE NOT NULL,
  `local_batismo`         VARCHAR(255) DEFAULT NULL,
  `igreja_id`             INT UNSIGNED DEFAULT NULL,
  `congregacao_id`        INT UNSIGNED DEFAULT NULL,
  `pastor_responsavel_id` INT UNSIGNED DEFAULT NULL,
  `certificado_numero`    VARCHAR(40) DEFAULT NULL,
  `observacoes`           TEXT DEFAULT NULL,
  `created_by`            INT UNSIGNED DEFAULT NULL,
  `updated_by`            INT UNSIGNED DEFAULT NULL,
  `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_batismos_certificado` (`certificado_numero`),
  KEY `idx_batismos_membro` (`membro_id`),
  KEY `idx_batismos_data` (`data_batismo`),
  KEY `idx_batismos_igreja` (`igreja_id`),
  KEY `idx_batismos_congregacao` (`congregacao_id`),
  KEY `idx_batismos_pastor` (`pastor_responsavel_id`),
  CONSTRAINT `fk_batismos_membro` FOREIGN KEY (`membro_id`)
    REFERENCES `membros` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_batismos_igreja` FOREIGN KEY (`igreja_id`)
    REFERENCES `igrejas_matriz` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_batismos_congregacao` FOREIGN KEY (`congregacao_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_batismos_pastor` FOREIGN KEY (`pastor_responsavel_id`)
    REFERENCES `obreiros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_batismos_created_by` FOREIGN KEY (`created_by`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_batismos_updated_by` FOREIGN KEY (`updated_by`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `consagracoes` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `obreiro_id`            INT UNSIGNED NOT NULL,
  `cargo_id`              SMALLINT UNSIGNED NOT NULL,
  `data_consagracao`      DATE NOT NULL,
  `local_consagracao`     VARCHAR(255) DEFAULT NULL,
  `congregacao_id`        INT UNSIGNED DEFAULT NULL,
  `pastor_presidente_id`  INT UNSIGNED DEFAULT NULL,
  `diploma_numero`        VARCHAR(40) DEFAULT NULL,
  `observacoes`           TEXT DEFAULT NULL,
  `created_by`            INT UNSIGNED DEFAULT NULL,
  `updated_by`            INT UNSIGNED DEFAULT NULL,
  `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_consagracoes_diploma` (`diploma_numero`),
  KEY `idx_consagracoes_obreiro` (`obreiro_id`),
  KEY `idx_consagracoes_cargo` (`cargo_id`),
  KEY `idx_consagracoes_data` (`data_consagracao`),
  KEY `idx_consagracoes_congregacao` (`congregacao_id`),
  KEY `idx_consagracoes_presidente` (`pastor_presidente_id`),
  CONSTRAINT `fk_consagracoes_obreiro` FOREIGN KEY (`obreiro_id`)
    REFERENCES `obreiros` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_consagracoes_cargo` FOREIGN KEY (`cargo_id`)
    REFERENCES `cargos_ministeriais` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_consagracoes_congregacao` FOREIGN KEY (`congregacao_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_consagracoes_presidente` FOREIGN KEY (`pastor_presidente_id`)
    REFERENCES `obreiros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_consagracoes_created_by` FOREIGN KEY (`created_by`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_consagracoes_updated_by` FOREIGN KEY (`updated_by`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `modelos_documentos` (
  `id`            SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo`          ENUM('CARTA_CONVITE','DIPLOMA_BATISMO','DIPLOMA_CONSAGRACAO','CARTEIRINHA') NOT NULL,
  `nome`          VARCHAR(100) NOT NULL,
  `conteudo_html` LONGTEXT NOT NULL,
  `css_extra`     TEXT DEFAULT NULL,
  `assinatura`    VARCHAR(255) DEFAULT NULL,
  `logotipo`      VARCHAR(255) DEFAULT NULL,
  `ativo`         TINYINT(1) NOT NULL DEFAULT 1,
  `padrao`        TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_modelos_tipo_nome` (`tipo`, `nome`),
  KEY `idx_modelos_tipo` (`tipo`),
  KEY `idx_modelos_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `documentos_emitidos` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo`              ENUM('CARTA_CONVITE','DIPLOMA_BATISMO','DIPLOMA_CONSAGRACAO','CARTEIRINHA') NOT NULL,
  `modelo_id`         SMALLINT UNSIGNED DEFAULT NULL,
  `referencia_id`     INT UNSIGNED DEFAULT NULL,
  `numero`            VARCHAR(60) NOT NULL,
  `destinatario`      VARCHAR(180) DEFAULT NULL,
  `dados`             LONGTEXT DEFAULT NULL,
  `caminho_pdf`       VARCHAR(255) DEFAULT NULL,
  `codigo_validacao`  VARCHAR(60) NOT NULL,
  `qr_code`           VARCHAR(255) DEFAULT NULL,
  `emitido_por`       INT UNSIGNED DEFAULT NULL,
  `emitido_em`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valido_ate`        DATE DEFAULT NULL,
  `segunda_via`       TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_documentos_numero` (`numero`),
  UNIQUE KEY `uq_documentos_validacao` (`codigo_validacao`),
  KEY `idx_documentos_tipo` (`tipo`),
  KEY `idx_documentos_modelo` (`modelo_id`),
  KEY `idx_documentos_referencia` (`tipo`, `referencia_id`),
  KEY `idx_documentos_emitido_em` (`emitido_em`),
  KEY `idx_documentos_emitido_por` (`emitido_por`),
  CONSTRAINT `fk_documentos_modelo` FOREIGN KEY (`modelo_id`)
    REFERENCES `modelos_documentos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_documentos_emitido_por` FOREIGN KEY (`emitido_por`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `carteirinhas` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo`              ENUM('MEMBRO','OBREIRO') NOT NULL,
  `referencia_id`     INT UNSIGNED NOT NULL,
  `modelo`            VARCHAR(40) NOT NULL,
  `codigo_validacao`  VARCHAR(60) NOT NULL,
  `data_emissao`      DATE NOT NULL,
  `data_validade`     DATE DEFAULT NULL,
  `caminho_pdf`       VARCHAR(255) DEFAULT NULL,
  `emitida_por`       INT UNSIGNED DEFAULT NULL,
  `segunda_via`       TINYINT(1) NOT NULL DEFAULT 0,
  `ativa`             TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_carteirinhas_codigo` (`codigo_validacao`),
  KEY `idx_carteirinhas_referencia` (`tipo`, `referencia_id`),
  KEY `idx_carteirinhas_modelo` (`modelo`),
  KEY `idx_carteirinhas_emissao` (`data_emissao`),
  KEY `idx_carteirinhas_emitida_por` (`emitida_por`),
  CONSTRAINT `fk_carteirinhas_emitida_por` FOREIGN KEY (`emitida_por`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. EVENTOS E CONTEUDO PUBLICO
-- ============================================================================

CREATE TABLE `paginas` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`           VARCHAR(80) NOT NULL,
  `titulo`         VARCHAR(180) NOT NULL,
  `conteudo`       LONGTEXT DEFAULT NULL,
  `meta_descricao` VARCHAR(255) DEFAULT NULL,
  `imagem`         VARCHAR(255) DEFAULT NULL,
  `ativa`          TINYINT(1) NOT NULL DEFAULT 1,
  `ordem`          TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_paginas_slug` (`slug`),
  KEY `idx_paginas_ativa` (`ativa`),
  KEY `idx_paginas_ordem` (`ordem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tipos_eventos` (
  `id`          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`        VARCHAR(60) NOT NULL,
  `slug`        VARCHAR(60) NOT NULL,
  `icone`       VARCHAR(60) DEFAULT NULL,
  `cor`         VARCHAR(7) DEFAULT NULL,
  `ativo`       TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tipos_eventos_nome` (`nome`),
  UNIQUE KEY `uq_tipos_eventos_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `eventos` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo`          VARCHAR(180) NOT NULL,
  `descricao`       TEXT DEFAULT NULL,
  `tipo_id`         TINYINT UNSIGNED DEFAULT NULL,
  `data_inicio`     DATETIME NOT NULL,
  `data_fim`        DATETIME DEFAULT NULL,
  `horario`         TIME DEFAULT NULL,
  `local_evento`    VARCHAR(255) DEFAULT NULL,
  `endereco`        VARCHAR(255) DEFAULT NULL,
  `foto`            VARCHAR(255) DEFAULT NULL,
  `responsavel_id`  INT UNSIGNED DEFAULT NULL,
  `congregacao_id`  INT UNSIGNED DEFAULT NULL,
  `publicar_site`   TINYINT(1) NOT NULL DEFAULT 1,
  `destaque`        TINYINT(1) NOT NULL DEFAULT 0,
  `situacao`        ENUM('PLANEJADO','EM_ANDAMENTO','CONCLUIDO','CANCELADO') NOT NULL DEFAULT 'PLANEJADO',
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_eventos_tipo` (`tipo_id`),
  KEY `idx_eventos_inicio` (`data_inicio`),
  KEY `idx_eventos_publicar` (`publicar_site`),
  KEY `idx_eventos_destaque` (`destaque`),
  KEY `idx_eventos_congregacao` (`congregacao_id`),
  KEY `idx_eventos_responsavel` (`responsavel_id`),
  KEY `idx_eventos_situacao` (`situacao`),
  CONSTRAINT `fk_eventos_tipo` FOREIGN KEY (`tipo_id`)
    REFERENCES `tipos_eventos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_eventos_congregacao` FOREIGN KEY (`congregacao_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_eventos_responsavel` FOREIGN KEY (`responsavel_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `galeria` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo`      VARCHAR(150) DEFAULT NULL,
  `descricao`   VARCHAR(255) DEFAULT NULL,
  `imagem`      VARCHAR(255) NOT NULL,
  `evento_id`   INT UNSIGNED DEFAULT NULL,
  `ordem`       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `ativa`       TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_galeria_evento` (`evento_id`),
  KEY `idx_galeria_ordem` (`ordem`),
  KEY `idx_galeria_ativa` (`ativa`),
  CONSTRAINT `fk_galeria_evento` FOREIGN KEY (`evento_id`)
    REFERENCES `eventos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `noticias` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo`        VARCHAR(180) NOT NULL,
  `slug`          VARCHAR(200) NOT NULL,
  `resumo`        VARCHAR(500) DEFAULT NULL,
  `conteudo`      LONGTEXT DEFAULT NULL,
  `imagem`        VARCHAR(255) DEFAULT NULL,
  `video`         VARCHAR(500) DEFAULT NULL,
  `autor_id`      INT UNSIGNED DEFAULT NULL,
  `publicada_em`  DATETIME DEFAULT NULL,
  `publicada`     TINYINT(1) NOT NULL DEFAULT 0,
  `destaque`      TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_noticias_slug` (`slug`),
  KEY `idx_noticias_publicada` (`publicada`),
  KEY `idx_noticias_data` (`publicada_em`),
  KEY `idx_noticias_destaque` (`destaque`),
  KEY `idx_noticias_autor` (`autor_id`),
  CONSTRAINT `fk_noticias_autor` FOREIGN KEY (`autor_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contatos` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`            VARCHAR(150) NOT NULL,
  `email`           VARCHAR(190) NOT NULL,
  `telefone`        VARCHAR(20) DEFAULT NULL,
  `assunto`         VARCHAR(180) DEFAULT NULL,
  `mensagem`        TEXT NOT NULL,
  `ip`              VARCHAR(45) DEFAULT NULL,
  `lido`            TINYINT(1) NOT NULL DEFAULT 0,
  `respondido`      TINYINT(1) NOT NULL DEFAULT 0,
  `respondido_por`  INT UNSIGNED DEFAULT NULL,
  `respondido_em`   DATETIME DEFAULT NULL,
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contatos_lido` (`lido`),
  KEY `idx_contatos_respondido` (`respondido`),
  KEY `idx_contatos_data` (`created_at`),
  KEY `idx_contatos_respondido_por` (`respondido_por`),
  CONSTRAINT `fk_contatos_respondido_por` FOREIGN KEY (`respondido_por`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. AUDITORIA, ACESSO E LGPD
-- ============================================================================

CREATE TABLE `tokens_recuperacao` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`  INT UNSIGNED NOT NULL,
  `token_hash`  VARCHAR(255) NOT NULL,
  `expira_em`   DATETIME NOT NULL,
  `usado_em`    DATETIME DEFAULT NULL,
  `ip`          VARCHAR(45) DEFAULT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tokens_hash` (`token_hash`),
  KEY `idx_tokens_usuario` (`usuario_id`),
  KEY `idx_tokens_expira` (`expira_em`),
  CONSTRAINT `fk_tokens_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `logs_acesso` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`  INT UNSIGNED DEFAULT NULL,
  `acao`        ENUM('LOGIN','LOGIN_FALHA','LOGOUT','SESSAO_EXPIRADA','TROCA_SENHA','BLOQUEIO') NOT NULL,
  `ip`          VARCHAR(45) DEFAULT NULL,
  `user_agent`  VARCHAR(255) DEFAULT NULL,
  `detalhes`    VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_logs_usuario` (`usuario_id`),
  KEY `idx_logs_acao` (`acao`),
  KEY `idx_logs_ip` (`ip`),
  KEY `idx_logs_data` (`created_at`),
  CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `auditoria` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`        INT UNSIGNED DEFAULT NULL,
  `acao`              VARCHAR(80) NOT NULL,
  `modulo`            VARCHAR(60) NOT NULL,
  `tabela`            VARCHAR(80) DEFAULT NULL,
  `registro_id`       VARCHAR(60) DEFAULT NULL,
  `dados_anteriores`  LONGTEXT DEFAULT NULL,
  `dados_novos`       LONGTEXT DEFAULT NULL,
  `ip`                VARCHAR(45) DEFAULT NULL,
  `user_agent`        VARCHAR(255) DEFAULT NULL,
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_auditoria_usuario` (`usuario_id`),
  KEY `idx_auditoria_modulo` (`modulo`),
  KEY `idx_auditoria_acao` (`acao`),
  KEY `idx_auditoria_registro` (`tabela`, `registro_id`),
  KEY `idx_auditoria_data` (`created_at`),
  CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela polimorfica: titular_tipo define qual entidade titular_id representa.
-- Sem FK propositalmente (membros / obreiros / usuarios / contatos).
CREATE TABLE `consentimentos_lgpd` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titular_tipo`        ENUM('MEMBRO','OBREIRO','USUARIO','CONTATO') NOT NULL,
  `titular_id`          INT UNSIGNED NOT NULL,
  `finalidade`          VARCHAR(150) NOT NULL,
  `consentido`          TINYINT(1) NOT NULL,
  `termo_versao`        VARCHAR(20) DEFAULT NULL,
  `data_consentimento`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip`                  VARCHAR(45) DEFAULT NULL,
  `user_agent`          VARCHAR(255) DEFAULT NULL,
  `revogado_em`         DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lgpd_titular` (`titular_tipo`, `titular_id`),
  KEY `idx_lgpd_finalidade` (`finalidade`),
  KEY `idx_lgpd_data` (`data_consentimento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- FK circular pendente: usuarios.congregacao_id (congregacoes depende de usuarios)
-- ----------------------------------------------------------------------------
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_congregacao` FOREIGN KEY (`congregacao_id`)
    REFERENCES `congregacoes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- 8. DADOS INICIAIS (CONFIGURACAO - NAO SAO DADOS FICTICIOS DE MEMBROS)
-- ============================================================================

-- 8.1 Perfis de acesso
INSERT INTO `perfis` (`id`, `nome`, `slug`, `descricao`, `nivel`) VALUES
(1, 'Superadministrador',    'superadministrador', 'Acesso total, inclusive seguranca, backup e restauracoes', 100),
(2, 'Administrador Geral',   'administrador-geral', 'Gestao completa, exceto chaves de seguranca e backup', 90),
(3, 'Pastor Presidente',     'pastor-presidente',  'Lideranca espiritual, sacramentos, documentos e relatorios', 80),
(4, 'Pastor de Congregacao', 'pastor-congregacao', 'Acesso restrito a congregacao vinculada', 70),
(5, 'Secretario',            'secretario',         'Cadastros, documentos, eventos e contato com o publico', 60),
(6, 'Tesoureiro',            'tesoureiro',         'Financeiro e relatorios financeiros', 50),
(7, 'Obreiro autorizado',    'obreiro-autorizado', 'Consulta limitada a eventos e propria ficha', 10);

-- 8.2 Permissoes granulares
INSERT INTO `permissoes` (`modulo`, `acao`, `slug`, `nome`) VALUES
('dashboard','ver','dashboard.ver','Visualizar dashboard'),
('configuracoes','ver','configuracoes.ver','Visualizar configuracoes'),
('configuracoes','editar','configuracoes.editar','Editar configuracoes'),
('igrejas','ver','igrejas.ver','Visualizar igrejas'),
('igrejas','criar','igrejas.criar','Criar igreja'),
('igrejas','editar','igrejas.editar','Editar igreja'),
('igrejas','excluir','igrejas.excluir','Excluir igreja'),
('congregacoes','ver','congregacoes.ver','Visualizar congregacoes'),
('congregacoes','criar','congregacoes.criar','Criar congregacao'),
('congregacoes','editar','congregacoes.editar','Editar congregacao'),
('congregacoes','excluir','congregacoes.excluir','Excluir congregacao'),
('pontos_pregacao','ver','pontos_pregacao.ver','Visualizar pontos de pregacao'),
('pontos_pregacao','criar','pontos_pregacao.criar','Criar ponto de pregacao'),
('pontos_pregacao','editar','pontos_pregacao.editar','Editar ponto de pregacao'),
('pontos_pregacao','excluir','pontos_pregacao.excluir','Excluir ponto de pregacao'),
('membros','ver','membros.ver','Visualizar membros'),
('membros','criar','membros.criar','Cadastrar membro'),
('membros','editar','membros.editar','Editar membro'),
('membros','excluir','membros.excluir','Excluir membro'),
('membros','exportar','membros.exportar','Exportar membros'),
('membros','imprimir','membros.imprimir','Imprimir ficha de membro'),
('membros','carteirinha','membros.carteirinha','Emitir carteirinha de membro'),
('obreiros','ver','obreiros.ver','Visualizar obreiros'),
('obreiros','criar','obreiros.criar','Cadastrar obreiro'),
('obreiros','editar','obreiros.editar','Editar obreiro'),
('obreiros','excluir','obreiros.excluir','Excluir obreiro'),
('obreiros','exportar','obreiros.exportar','Exportar obreiros'),
('obreiros','imprimir','obreiros.imprimir','Imprimir ficha de obreiro'),
('obreiros','diploma','obreiros.diploma','Emitir diploma de obreiro'),
('obreiros','transferir','obreiros.transferir','Transferir obreiro'),
('arquivo_morto','ver','arquivo_morto.ver','Consultar arquivo morto'),
('arquivo_morto','arquivar','arquivo_morto.arquivar','Arquivar obreiro'),
('arquivo_morto','restaurar','arquivo_morto.restaurar','Restaurar obreiro arquivado'),
('arquivo_morto','imprimir','arquivo_morto.imprimir','Imprimir ficha historica'),
('arquivo_morto','exportar','arquivo_morto.exportar','Exportar arquivo morto'),
('financeiro','ver','financeiro.ver','Visualizar financeiro'),
('financeiro','criar','financeiro.criar','Lancar movimento'),
('financeiro','editar','financeiro.editar','Editar movimento'),
('financeiro','excluir','financeiro.excluir','Excluir movimento'),
('financeiro','cancelar','financeiro.cancelar','Cancelar movimento'),
('financeiro','exportar','financeiro.exportar','Exportar financeiro'),
('financeiro','imprimir','financeiro.imprimir','Imprimir relatorio financeiro'),
('financeiro','auditar','financeiro.auditar','Auditar alteracoes financeiras'),
('batismos','ver','batismos.ver','Visualizar batismos'),
('batismos','criar','batismos.criar','Registrar batismo'),
('batismos','editar','batismos.editar','Editar batismo'),
('batismos','excluir','batismos.excluir','Excluir batismo'),
('batismos','imprimir','batismos.imprimir','Imprimir registro de batismo'),
('batismos','diploma','batismos.diploma','Emitir diploma de batismo'),
('consagracoes','ver','consagracoes.ver','Visualizar consagracoes'),
('consagracoes','criar','consagracoes.criar','Registrar consagracao'),
('consagracoes','editar','consagracoes.editar','Editar consagracao'),
('consagracoes','excluir','consagracoes.excluir','Excluir consagracao'),
('consagracoes','imprimir','consagracoes.imprimir','Imprimir registro de consagracao'),
('consagracoes','diploma','consagracoes.diploma','Emitir diploma de consagracao'),
('diplomas','ver','diplomas.ver','Consultar diplomas emitidos'),
('diplomas','emitir','diplomas.emitir','Emitir diploma'),
('diplomas','imprimir','diplomas.imprimir','Imprimir diploma'),
('cartas_convite','ver','cartas_convite.ver','Consultar cartas'),
('cartas_convite','emitir','cartas_convite.emitir','Emitir carta convite'),
('cartas_convite','imprimir','cartas_convite.imprimir','Imprimir carta convite'),
('carteirinhas','ver','carteirinhas.ver','Consultar carteirinhas'),
('carteirinhas','emitir','carteirinhas.emitir','Emitir carteirinha'),
('carteirinhas','segunda_via','carteirinhas.segunda_via','Emitir segunda via'),
('carteirinhas','validar','carteirinhas.validar','Validar carteirinha'),
('carteirinhas','imprimir','carteirinhas.imprimir','Imprimir carteirinha'),
('eventos','ver','eventos.ver','Visualizar eventos'),
('eventos','criar','eventos.criar','Criar evento'),
('eventos','editar','eventos.editar','Editar evento'),
('eventos','excluir','eventos.excluir','Excluir evento'),
('eventos','publicar','eventos.publicar','Publicar evento no site'),
('relatorios','ver','relatorios.ver','Visualizar relatorios'),
('relatorios','exportar','relatorios.exportar','Exportar relatorios'),
('relatorios','imprimir','relatorios.imprimir','Imprimir relatorios'),
('auditoria','ver','auditoria.ver','Visualizar auditoria'),
('auditoria','exportar','auditoria.exportar','Exportar auditoria'),
('site','ver','site.ver','Visualizar conteudo do site'),
('site','editar','site.editar','Editar paginas institucionais'),
('site_noticias','ver','site_noticias.ver','Visualizar noticias'),
('site_noticias','criar','site_noticias.criar','Criar noticia'),
('site_noticias','editar','site_noticias.editar','Editar noticia'),
('site_noticias','excluir','site_noticias.excluir','Excluir noticia'),
('site_galeria','ver','site_galeria.ver','Visualizar galeria'),
('site_galeria','criar','site_galeria.criar','Adicionar foto'),
('site_galeria','editar','site_galeria.editar','Editar foto'),
('site_galeria','excluir','site_galeria.excluir','Excluir foto'),
('site_contatos','ver','site_contatos.ver','Visualizar mensagens de contato'),
('site_contatos','responder','site_contatos.responder','Responder contato'),
('site_contatos','excluir','site_contatos.excluir','Excluir contato'),
('seguranca','usuarios_ver','seguranca.usuarios_ver','Visualizar usuarios'),
('seguranca','usuarios_criar','seguranca.usuarios_criar','Criar usuario'),
('seguranca','usuarios_editar','seguranca.usuarios_editar','Editar usuario'),
('seguranca','usuarios_excluir','seguranca.usuarios_excluir','Excluir usuario'),
('seguranca','perfis_ver','seguranca.perfis_ver','Visualizar perfis'),
('seguranca','perfis_editar','seguranca.perfis_editar','Editar perfis'),
('seguranca','permissoes_ver','seguranca.permissoes_ver','Visualizar permissoes'),
('seguranca','permissoes_editar','seguranca.permissoes_editar','Editar permissoes'),
('seguranca','backup','seguranca.backup','Gerar backup'),
('seguranca','restore','seguranca.restore','Restaurar backup'),
('seguranca','lgpd_ver','seguranca.lgpd_ver','Visualizar consentimentos LGPD'),
('seguranca','lgpd_editar','seguranca.lgpd_editar','Gerenciar consentimentos LGPD');

-- 8.3 Matriz perfil x permissao (INSERT IGNORE evita duplicatas)
INSERT IGNORE INTO `perfil_permissao` (`perfil_id`, `permissao_id`)
SELECT 1, `id` FROM `permissoes`;

INSERT IGNORE INTO `perfil_permissao` (`perfil_id`, `permissao_id`)
SELECT 2, `id` FROM `permissoes`
WHERE `slug` NOT IN ('seguranca.backup','seguranca.restore','seguranca.permissoes_editar');

INSERT IGNORE INTO `perfil_permissao` (`perfil_id`, `permissao_id`)
SELECT 3, `id` FROM `permissoes`
WHERE `modulo` IN ('dashboard','igrejas','congregacoes','pontos_pregacao','membros','obreiros',
                   'batismos','consagracoes','diplomas','cartas_convite','carteirinhas',
                   'eventos','relatorios','site','site_noticias','site_galeria','auditoria')
  AND `acao` <> 'excluir'
UNION
SELECT 3, `id` FROM `permissoes`
WHERE `slug` IN ('arquivo_morto.ver','arquivo_morto.arquivar','arquivo_morto.imprimir',
                 'financeiro.ver','configuracoes.ver','membros.exportar','obreiros.exportar');

INSERT IGNORE INTO `perfil_permissao` (`perfil_id`, `permissao_id`)
SELECT 4, `id` FROM `permissoes`
WHERE `slug` IN (
 'dashboard.ver','membros.ver','membros.criar','membros.editar','membros.imprimir',
 'obreiros.ver','batismos.ver','batismos.criar','consagracoes.ver','carteirinhas.ver',
 'carteirinhas.emitir','eventos.ver','eventos.criar','eventos.editar','relatorios.ver',
 'site.ver','financeiro.ver','igrejas.ver','congregacoes.ver','pontos_pregacao.ver'
);

INSERT IGNORE INTO `perfil_permissao` (`perfil_id`, `permissao_id`)
SELECT 5, `id` FROM `permissoes`
WHERE `modulo` IN ('dashboard','membros','obreiros','batismos','consagracoes',
                   'cartas_convite','carteirinhas','eventos','relatorios','site',
                   'site_noticias','site_galeria','site_contatos','igrejas',
                   'congregacoes','pontos_pregacao')
  AND `acao` <> 'excluir'
UNION
SELECT 5, `id` FROM `permissoes`
WHERE `slug` IN ('arquivo_morto.ver','relatorios.imprimir');

INSERT IGNORE INTO `perfil_permissao` (`perfil_id`, `permissao_id`)
SELECT 6, `id` FROM `permissoes`
WHERE `slug` IN (
 'dashboard.ver','financeiro.ver','financeiro.criar','financeiro.editar',
 'financeiro.cancelar','financeiro.exportar','financeiro.imprimir','financeiro.auditar',
 'relatorios.ver','relatorios.exportar','relatorios.imprimir',
 'membros.ver','congregacoes.ver'
);

INSERT IGNORE INTO `perfil_permissao` (`perfil_id`, `permissao_id`)
SELECT 7, `id` FROM `permissoes`
WHERE `slug` IN ('dashboard.ver','eventos.ver','site.ver','carteirinhas.validar');

-- 8.4 Cargos ministeriais
INSERT INTO `cargos_ministeriais` (`nome`, `slug`, `descricao`) VALUES
('Pastor',       'pastor',       'Pastor titular ou auxiliar'),
('Evangelista',  'evangelista',  'Evangelista'),
('Presbitero',   'presbitero',   'Presbitero'),
('Diacono',      'diacono',      'Diakonos'),
('Diaconisa',    'diaconisa',    'Diaconisa'),
('Missionario',  'missionario',  'Missionario'),
('Missionaria',  'missionaria',  'Missionaria'),
('Cooperador',   'cooperador',   'Cooperador'),
('Cooperadora',  'cooperadora',  'Cooperadora'),
('Auxiliar',     'auxiliar',     'Auxiliar ministerial');

-- 8.5 Formas de pagamento
INSERT INTO `formas_pagamento` (`nome`, `slug`, `requer_comprovante`) VALUES
('Dinheiro',              'dinheiro',              0),
('Pix',                   'pix',                   0),
('TED',                   'ted',                   1),
('Transferencia bancaria','transferencia-bancaria',1),
('Boleto',                'boleto',                1),
('Cartao de debito',      'cartao-debito',         1),
('Cartao de credito',     'cartao-credito',        1),
('Deposito em conta',     'deposito-conta',        1);

-- 8.6 Categorias financeiras
INSERT INTO `categorias_financeiras` (`nome`, `tipo`, `descricao`) VALUES
('Dizimo',                  'ENTRADA','Dizimos recebidos'),
('Oferta',                  'ENTRADA','Ofertas gerais'),
('Oferta missionaria',      'ENTRADA','Ofertas destinadas a missoes'),
('Doacao',                  'ENTRADA','Doacoes e contribuicoes voluntarias'),
('Entrada diversa',         'ENTRADA','Outras entradas'),
('Despesa administrativa',  'SAIDA',  'Despesas de administracao'),
('Manutencao do templo',    'SAIDA',  'Manutencao predial'),
('Eventos e campanhas',     'SAIDA',  'Realizacao de eventos'),
('Missoes',                 'SAIDA',  'Apoio a missoes'),
('Material ministerial',    'SAIDA',  'Material de estudo e liturgia'),
('Impostos e taxas',        'SAIDA',  'Tributos e taxas'),
('Salarios e honorarios',   'SAIDA',  'Honorarios e salarios'),
('Outras despesas',         'SAIDA',  'Despesas nao categorizadas');

-- 8.7 Tipos de evento
INSERT INTO `tipos_eventos` (`nome`, `slug`, `icone`, `cor`) VALUES
('Culto',           'culto',           'fas fa-church',     '#0b1f3a'),
('Congresso',       'congresso',       'fas fa-users',      '#c9a227'),
('Festividade',     'festividade',     'fas fa-star',       '#c9a227'),
('Campanha',        'campanha',        'fas fa-bullhorn',   '#0b1f3a'),
('Conferencia',     'conferencia',     'fas fa-microphone', '#0b1f3a'),
('Reuniao',         'reuniao',         'fas fa-comments',   '#4a5568'),
('Escola Biblica',  'escola-biblica',  'fas fa-book-bible', '#4a5568'),
('Evento especial', 'evento-especial', 'fas fa-calendar-star','#c9a227');

-- 8.8 Paginas institucionais (conteudo a ser preenchido no painel)
INSERT INTO `paginas` (`slug`, `titulo`, `conteudo`, `meta_descricao`, `ativa`) VALUES
('historia','Nossa Historia','<p>[Conteudo a ser preenchido pelo administrador no painel.]</p>','Historia do ministerio',1),
('missao-visao-valores','Missao, Visao e Valores','<p>[Conteudo a ser preenchido pelo administrador no painel.]</p>','Missao, visao e valores',1),
('palavra-pastoral','Palavra Pastoral','<p>[Conteudo a ser preenchido pelo administrador no painel.]</p>','Palavra pastoral',1);

-- 8.9 Configuracoes iniciais (valores neutros, sem dados ficticios)
INSERT INTO `configuracoes` (`chave`, `valor`, `grupo`, `tipo`, `descricao`) VALUES
('ministerio_nome','Assembleia de Deus','identidade','texto','Nome do ministerio'),
('ministerio_sigla','AD','identidade','texto','Sigla do ministerio'),
('ministerio_cnpj','','identidade','texto','CNPJ do ministerio'),
('ministerio_endereco','','identidade','texto','Endereco da igreja matriz'),
('ministerio_telefone','','identidade','texto','Telefone de contato'),
('ministerio_whatsapp','','identidade','texto','WhatsApp de contato'),
('ministerio_email','','identidade','texto','E-mail de contato'),
('pastor_presidente','','identidade','texto','Nome do pastor presidente'),
('pastor_foto','','identidade','imagem','Foto do pastor presidente'),
('contato_rotulo_pastor','Pastor Presidente','identidade','texto','Rotulo exibido antes do nome do pastor presidente'),
('contato_eyebrow','Estamos à disposição','identidade','texto','Destaque da pagina de contato'),
('contato_titulo','Fale conosco','identidade','texto','Titulo da pagina de contato'),
('contato_bloco_titulo','Informações','identidade','texto','Titulo do bloco de informacoes da pagina de contato'),
('contato_home_eyebrow','Fale conosco','identidade','texto','Destaque da secao de contato na home'),
('contato_home_titulo','Entre em contato','identidade','texto','Titulo da secao de contato na home'),
('contato_home_bloco_titulo','Informações de contato','identidade','texto','Titulo do bloco de informacoes na home'),
('contato_mensagem_vazia','Os dados de contato serão publicados aqui pela administração.','identidade','texto','Mensagem exibida quando nao ha dados de contato'),
('assinatura_institucional','','identidade','texto','Assinatura usada em documentos'),
('logotipo','','identidade','imagem','Caminho do logotipo'),
('banner_principal','','identidade','imagem','Caminho do banner do site'),
('hero_eyebrow','Ministério Evangélico','identidade','texto','Subtítulo exibido sobre o carrossel'),
('hero_titulo','Assembleia de Deus','identidade','texto','Título exibido sobre o carrossel'),
('hero_texto','Igreja Matriz, congregações e pontos de pregação unidos na fé, na comunhão e no anúncio do evangelho.','identidade','texto','Texto exibido sobre o carrossel'),
('hero_cor_eyebrow','#a97f22','identidade','cor','Cor do subtítulo do carrossel'),
('hero_cor_titulo','#2b608c','identidade','cor','Cor do título do carrossel'),
('hero_cor_texto','#516b85','identidade','cor','Cor do texto do carrossel'),
('noticias_eyebrow','Acontecendo no ministério','identidade','texto','Subtítulo da página de notícias'),
('noticias_titulo','Notícias','identidade','texto','Título da página de notícias'),
('noticias_vazio','Nenhuma notícia publicada no momento.','identidade','texto','Mensagem para página sem notícias'),
('noticias_ler','Ler notícia','identidade','texto','Texto do botão dos cards de notícias'),
('noticias_home_eyebrow','Acontecendo','identidade','texto','Subtítulo da seção de notícias na home'),
('noticias_home_titulo','Notícias','identidade','texto','Título da seção de notícias na home'),
('noticias_home_botao','Todas as notícias','identidade','texto','Texto do botão da seção de notícias na home'),
('noticias_home_vazio','Nenhuma notícia publicada no momento.','identidade','texto','Mensagem para seção de notícias vazia na home'),
('noticias_home_ler','Ler notícia','identidade','texto','Texto do link dos notícias na home'),
('cultos_eyebrow','Cultos e programação','identidade','texto','Subtítulo da seção de cultos'),
('cultos_titulo','Horários dos cultos e programação semanal','identidade','texto','Título da seção de cultos'),
('cultos_vazio','Os horários dos cultos serão publicados aqui pela administração.','identidade','texto','Mensagem para seção de cultos sem horários'),
('cultos_botao','Veja toda a programação','identidade','texto','Texto do botão da seção de cultos'),
('eventos_eyebrow','Agenda do ministério','identidade','texto','Subtítulo da página de eventos'),
('eventos_titulo','Eventos e programação','identidade','texto','Título da página de eventos'),
('eventos_vazio','Nenhum evento publicado no momento.','identidade','texto','Mensagem para agenda sem eventos'),
('eventos_ver_detalhes','Ver detalhes','identidade','texto','Texto do botão dos eventos'),
('eventos_detalhe_data','Data','identidade','texto','Rótulo de data na página de evento'),
('eventos_detalhe_horario','Horário','identidade','texto','Rótulo de horário na página de evento'),
('eventos_detalhe_local','Local','identidade','texto','Rótulo de local na página de evento'),
('eventos_detalhe_congregacao','Congregação','identidade','texto','Rótulo de congregação na página de evento'),
('eventos_home_eyebrow','Agenda','identidade','texto','Subtítulo da seção de eventos na home'),
('eventos_home_titulo','Próximos eventos','identidade','texto','Título da seção de eventos na home'),
('eventos_home_botao','Ver todos','identidade','texto','Texto do botão da seção de eventos na home'),
('eventos_home_vazio','Nenhum evento programado no momento.','identidade','texto','Mensagem para seção de eventos vazia na home'),
('eventos_home_local','Local a confirmar','identidade','texto','Local padrão dos eventos na home'),
('eventos_home_ver_detalhes','Ver detalhes','identidade','texto','Texto do link dos eventos na home'),
('historia_eyebrow','Nossa história','identidade','texto','Subtítulo da seção história na home'),
('historia_titulo','O que Deus tem feito entre nós','identidade','texto','Título da seção história na home'),
('historia_texto','','identidade','texto','Resumo da história exibido na home'),
('historia_imagem','','identidade','imagem','Imagem da seção história na home'),
('cor_primaria','#17324d','identidade','cor','Azul institucional claro'),
('cor_secundaria','#c79a37','identidade','cor','Dourado institucional suave'),
('cor_terciaria','#ffffff','identidade','cor','Branco institucional'),
('fuso_horario','America/Sao_Paulo','sistema','texto','Fuso horario'),
('itens_por_pagina','20','sistema','texto','Quantidade de registros por pagina'),
('manutencao','0','sistema','boolean','Ativa o modo manutencao'),
('lgpd_termo_versao','1.0','lgpd','texto','Versao do termo de consentimento'),
('validacao_base_url','','sistema','texto','URL base para QR Code de carteirinhas');

-- 8.10 Modelos de documentos (templates com marcadores {{...}})
INSERT INTO `modelos_documentos` (`tipo`, `nome`, `conteudo_html`, `padrao`) VALUES
('CARTA_CONVITE','Carta convite institucional',
'<div class="ad-doc ad-carta"><div class="ad-doc-head"><img src="{{logo}}" alt="Logo"><h1>{{ministerio_nome}}</h1><p>{{ministerio_endereco}} | {{ministerio_telefone}}</p></div><p class="ad-data">{{local}}, {{data_extenso}}</p><p><strong>A</strong><br>{{convidado}}<br>{{igreja_convidada}}<br>{{endereco_convidado}}</p><p><strong>Assunto:</strong> Convite para {{nome_evento}}</p><p>Prezados irmaos e irmas em Cristo,</p><p>Temos o prazer de convidar-vos para <strong>{{nome_evento}}</strong>, realizado no dia {{data_evento}}, as {{horario_evento}}, em {{local_evento}}.</p><p>Contamos com a presenca de todos. Pedimos confirmacao pelos contatos acima.</p><p>Em Cristo,<br><br><span class="ad-assinatura">{{pastor_responsavel}}</span><br>{{cargo_pastor}}</p></div>',
1),

('DIPLOMA_BATISMO','Diploma de batismo classico',
'<div class="ad-doc ad-diploma"><div class="ad-borda"></div><div class="ad-selo"><img src="{{logo}}" alt="Logo"></div><p class="ad-topo">{{ministerio_nome}}</p><h1>DIPLOMA DE BATISMO</h1><p>Certificamos que</p><h2>{{nome_batizado}}</h2><p>foi batizado(a) em obediencia ao mandamento do Senhor, no dia <strong>{{data_batismo}}</strong>, em <strong>{{local_batismo}}</strong>, celebrado pelo pastor <strong>{{pastor_celebrante}}</strong>.</p><p class="ad-maior">Ide, pois, e fazei discipulos de todas as nacoes.</p><div class="ad-assinaturas"><div><span>{{pastor_celebrante}}</span><small>Pastor Celebrante</small></div><div><span>{{pastor_presidente}}</span><small>Pastor Presidente</small></div></div><p class="ad-numero">Certificado n. {{numero_certificado}}</p></div>',
1),

('DIPLOMA_CONSAGRACAO','Diploma de consagracao classico',
'<div class="ad-doc ad-diploma"><div class="ad-borda"></div><div class="ad-selo"><img src="{{logo}}" alt="Logo"></div><p class="ad-topo">{{ministerio_nome}}</p><h1>DIPLOMA DE CONSAGRACAO</h1><p>Certificamos que</p><h2>{{nome_obreiro}}</h2><p>foi consagrado(a) ao cargo ministerial de <strong>{{cargo_ministerial}}</strong>, na congregacao <strong>{{congregacao}}</strong>, no dia <strong>{{data_consagracao}}</strong>, em {{local_consagracao}}, sob a presidencia do pastor <strong>{{pastor_presidente}}</strong>.</p><p class="ad-maior">Servi com alegria e fidelidade ao Senhor.</p><div class="ad-assinaturas"><div><span>{{pastor_presidente}}</span><small>Pastor Presidente</small></div><div><span>{{assinatura_institucional}}</span><small>Secretaria Ministerial</small></div></div><p class="ad-numero">Diploma n. {{numero_diploma}}</p></div>',
1),

('CARTEIRINHA','Modelo classico',
'<div class="ad-card ad-card-classico"><div class="ad-card-frente"><div class="ad-card-topo"><img class="ad-logo" src="{{logo}}" alt="Logo"><div><h3>{{ministerio_nome}}</h3><p>Carteirinha de {{tipo}}</p></div></div><img class="ad-foto" src="{{foto}}" alt="Foto"><div class="ad-dados"><h4>{{nome_completo}}</h4><p><b>Codigo:</b> {{codigo_identificacao}}</p><p><b>Cargo:</b> {{cargo_ministerial}}</p><p><b>Congregacao:</b> {{congregacao}}</p><p><b>Emissao:</b> {{data_emissao}} <b>Validade:</b> {{data_validade}}</p></div><img class="ad-qr" src="{{qrcode}}" alt="QR Code"><small class="ad-codigo">{{codigo_validacao}}</small></div></div>',
1),

('CARTEIRINHA','Modelo moderno',
'<div class="ad-card ad-card-moderno"><div class="ad-fundo"></div><div class="ad-card-conteudo"><img class="ad-logo" src="{{logo}}" alt="Logo"><h3>{{ministerio_nome}}</h3><span class="ad-tipo">{{tipo}}</span><img class="ad-foto" src="{{foto}}" alt="Foto"><h4>{{nome_completo}}</h4><p>{{codigo_identificacao}} | {{congregacao}}</p><img class="ad-qr" src="{{qrcode}}" alt="QR Code"><small>{{codigo_validacao}}</small></div></div>',
0),

('CARTEIRINHA','Modelo horizontal',
'<div class="ad-card ad-card-horizontal"><div class="ad-esquerda"><img class="ad-logo" src="{{logo}}" alt="Logo"><img class="ad-foto" src="{{foto}}" alt="Foto"></div><div class="ad-direita"><h3>{{ministerio_nome}}</h3><h4>{{nome_completo}}</h4><p><b>{{tipo}}</b> | Cod. {{codigo_identificacao}}</p><p>{{cargo_ministerial}} - {{congregacao}}</p><img class="ad-qr" src="{{qrcode}}" alt="QR Code"></div></div>',
0),

('CARTEIRINHA','Modelo vertical',
'<div class="ad-card ad-card-vertical"><img class="ad-logo" src="{{logo}}" alt="Logo"><h3>{{ministerio_nome}}</h3><img class="ad-foto" src="{{foto}}" alt="Foto"><h4>{{nome_completo}}</h4><p>{{tipo}} | {{codigo_identificacao}}</p><p>{{cargo_ministerial}}</p><p>{{congregacao}}</p><img class="ad-qr" src="{{qrcode}}" alt="QR Code"><small>{{codigo_validacao}}</small></div>',
0),

('CARTEIRINHA','Modelo institucional com fotografia',
'<div class="ad-card ad-card-institucional"><header><img class="ad-logo" src="{{logo}}" alt="Logo"><span>{{ministerio_sigla}}</span></header><h2>{{ministerio_nome}}</h2><p class="ad-sub">Identificacao de {{tipo}}</p><img class="ad-foto" src="{{foto}}" alt="Foto"><h3>{{nome_completo}}</h3><table><tr><th>Codigo</th><td>{{codigo_identificacao}}</td></tr><tr><th>Cargo</th><td>{{cargo_ministerial}}</td></tr><tr><th>Congregacao</th><td>{{congregacao}}</td></tr><tr><th>Validade</th><td>{{data_validade}}</td></tr></table><img class="ad-qr" src="{{qrcode}}" alt="QR Code"><footer>{{codigo_validacao}}</footer></div>',
0),

('CARTEIRINHA','Modelo com QR Code',
'<div class="ad-card ad-card-qrcode"><div class="ad-qr-grande"><img src="{{qrcode}}" alt="QR Code"><small>{{codigo_validacao}}</small></div><div class="ad-info"><img class="ad-logo" src="{{logo}}" alt="Logo"><h3>{{ministerio_nome}}</h3><h4>{{nome_completo}}</h4><p>{{tipo}} - {{codigo_identificacao}}</p><p>{{cargo_ministerial}} | {{congregacao}}</p><img class="ad-foto" src="{{foto}}" alt="Foto"><p class="ad-emissao">Emitida em {{data_emissao}}</p></div></div>',
0);

-- ============================================================================
-- 9. VERIFICACAO POS-INSTALACAO (consultas uteis)
-- ============================================================================
-- SELECT COUNT(*) AS total_tabelas FROM information_schema.tables
--   WHERE table_schema = DATABASE() AND table_engine = 'InnoDB';
-- SELECT p.nome, COUNT(pp.permissao_id) AS permissoes
--   FROM perfis p LEFT JOIN perfil_permissao pp ON pp.perfil_id = p.id
--   GROUP BY p.id ORDER BY p.nivel DESC;
-- SELECT TABLE_NAME, CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
--   WHERE REFERENCED_TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME;

-- FIM DO SCRIPT
