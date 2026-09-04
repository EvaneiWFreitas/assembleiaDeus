<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Cria modelos de documentos padrão para a igreja.
 */
class DocumentosModelosSeeder extends Seeder
{
    public function run(): void
    {
        $modelos = [
            // Certificado de Curso
            [
                'titulo'    => 'Certificado de Conclusão de Curso',
                'tipo'      => 'certificado_curso',
                'conteudo'  => '<div style="text-align: center; padding: 40px; border: 3px double #1e3a8a; font-family: Georgia, serif;">
    <div style="font-size: 12px; letter-spacing: 3px; color: #666; margin-bottom: 20px;">IGREJA ASSEMBLEIA DE DEUS</div>
    <h1 style="font-size: 36px; color: #1e3a8a; margin: 20px 0; font-weight: bold;">CERTIFICADO DE CONCLUSÃO</h1>
    <div style="width: 100px; height: 2px; background: #1e3a8a; margin: 20px auto;"></div>
    <p style="font-size: 18px; color: #333; margin: 30px 0; line-height: 1.6;">
        Certificamos que <strong style="font-size: 22px; text-transform: uppercase;">{nome_aluno}</strong> 
        concluiu com êxito o curso <strong>{nome_curso}</strong>,
        com carga horária de <strong>{carga_horaria}</strong> horas,
        realizado no período de <strong>{data_inicio}</strong> a <strong>{data_conclusao}</strong>.
    </p>
    <p style="font-size: 16px; color: #555; margin: 30px 0;">
        <em>"Esforça-te, e tem bom ânimo" - Josué 1:9</em>
    </p>
    <div style="margin-top: 60px;">
        <div style="display: inline-block; text-align: left; margin: 0 60px;">
            <div style="border-top: 1px solid #333; width: 250px; padding-top: 5px;">{nome_pastor}</div>
            <div style="font-size: 12px; color: #666;">Pastor Presidente</div>
        </div>
        <div style="display: inline-block; text-align: left; margin: 0 60px;">
            <div style="border-top: 1px solid #333; width: 250px; padding-top: 5px;">{nome_secretario}</div>
            <div style="font-size: 12px; color: #666;">Secretário(a)</div>
        </div>
    </div>
    <div style="margin-top: 40px; font-size: 12px; color: #999;">
        Emitido em {data_emissao} | Certificado nº {numero_certificado} | {nome_igreja}
    </div>
    <div style="margin-top: 8px; font-size: 11px; color: #aaa;">
        CNPJ: {cnpj} | Inscrição Estadual: {inscricao_estadual}
    </div>
</div>',
                'variaveis' => '["nome_aluno", "nome_curso", "carga_horaria", "data_inicio", "data_conclusao", "nome_pastor", "nome_secretario", "nome_igreja", "data_emissao", "numero_certificado", "cnpj", "inscricao_estadual"]',
                'ativo'     => 1,
                'padrao'    => 1,
            ],
            // Certificado de Consagração
            [
                'titulo'    => 'Certificado de Consagração de Obreiro',
                'tipo'      => 'certificado_consagracao',
                'conteudo'  => '<div style="text-align: center; padding: 40px; border: 3px double #8b4513; font-family: Georgia, serif; background: #fdfbf7;">
    <div style="font-size: 14px; letter-spacing: 2px; color: #8b4513; margin-bottom: 20px; font-weight: bold;">ASSEMBLEIA DE DEUS - MINISTÉRIO</div>
    <h1 style="font-size: 34px; color: #8b4513; margin: 20px 0; font-weight: bold;">CERTIFICADO DE CONSAGRAÇÃO</h1>
    <div style="width: 120px; height: 3px; background: #8b4513; margin: 20px auto;"></div>
    <div style="font-size: 20px; color: #333; margin: 30px 0; font-style: italic;">"E a imposição das mãos..."</div>
    <p style="font-size: 18px; color: #333; margin: 30px 0; line-height: 1.8;">
        Por este instrumento, a Igreja Assembleia de Deus em <strong>{nome_igreja}</strong> 
        consagra o irmão <strong style="font-size: 22px; text-transform: uppercase;">{nome_obreiro}</strong> 
        ao santo ministério de <strong>{cargo}</strong>,
        reconhecendo seu chamado, fidelidade e preparo para servir ao Senhor.
    </p>
    <p style="font-size: 16px; color: #555; margin: 30px 0;">
        Consagrado em <strong>{data_consagracao}</strong>, sob a autoridade pastoral do 
        <strong>Pr. {nome_pastor}</strong>.
    </p>
    <p style="font-size: 15px; color: #666; margin: 30px 0; font-style: italic;">
        "{versiculo}"
    </p>
    <div style="margin-top: 60px;">
        <div style="display: inline-block; text-align: left; margin: 0 60px;">
            <div style="border-top: 1px solid #333; width: 280px; padding-top: 5px;">Pr. {nome_pastor}</div>
            <div style="font-size: 12px; color: #666;">Pastor Presidente</div>
        </div>
        <div style="display: inline-block; text-align: left; margin: 0 60px;">
            <div style="border-top: 1px solid #333; width: 280px; padding-top: 5px;">{nome_vice}</div>
            <div style="font-size: 12px; color: #666;">Vice-Presidente</div>
        </div>
    </div>
    <div style="margin-top: 40px; font-size: 12px; color: #999;">
        Emitido em {data_emissao} | Certificado nº {numero_certificado} | {nome_igreja}
    </div>
    <div style="margin-top: 8px; font-size: 11px; color: #aaa;">
        CNPJ: {cnpj} | Inscrição Estadual: {inscricao_estadual}
    </div>
</div>',
                'variaveis' => '["nome_obreiro", "cargo", "data_consagracao", "nome_pastor", "nome_vice", "nome_igreja", "versiculo", "data_emissao", "numero_certificado", "cnpj", "inscricao_estadual"]',
                'ativo'     => 1,
                'padrao'    => 1,
            ],
            // Papel Timbrado para Atas
            [
                'titulo'    => 'Modelo de Ata de Reunião',
                'tipo'      => 'papel_timbrado_ata',
                'conteudo'  => '<div style="font-family: Arial, sans-serif; padding: 30px; max-width: 800px; margin: 0 auto;">
    <!-- Cabeçalho Timbrado -->
    <div style="text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 30px;">
        <div style="font-size: 24px; font-weight: bold; color: #1e3a8a; letter-spacing: 1px;">{nome_igreja}</div>
        <div style="font-size: 14px; color: #555; margin-top: 5px;">{endereco_igreja}</div>
        <div style="font-size: 13px; color: #777;">Tel: {telefone_igreja} | E-mail: {email_igreja}</div>
        <div style="font-size: 12px; color: #888;">CNPJ: {cnpj} | Inscrição Estadual: {inscricao_estadual}</div>
        <div style="width: 60px; height: 1px; background: #1e3a8a; margin: 15px auto;"></div>
    </div>
    
    <!-- Título do Documento -->
    <h2 style="text-align: center; color: #1e3a8a; font-size: 22px; margin-bottom: 5px;">ATA DE REUNIÃO</h2>
    <p style="text-align: center; color: #666; font-size: 14px;">{tipo_reuniao}</p>
    
    <!-- Dados da Reunião -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 14px;">
        <tr>
            <td style="font-weight: bold; width: 150px; padding: 8px; border-bottom: 1px solid #ddd;">Data:</td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{data_reuniao}</td>
            <td style="font-weight: bold; width: 150px; padding: 8px; border-bottom: 1px solid #ddd;">Local:</td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{local}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 8px; border-bottom: 1px solid #ddd;">Horário:</td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{hora_inicio} - {hora_fim}</td>
            <td style="font-weight: bold; padding: 8px; border-bottom: 1px solid #ddd;">Presidente:</td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{nome_presidente}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 8px;">Secretário:</td>
            <td style="padding: 8px;">{nome_secretario}</td>
            <td style="font-weight: bold; padding: 8px;">Status:</td>
            <td style="padding: 8px;">{status}</td>
        </tr>
    </table>
    
    <!-- Pauta -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #1e3a8a; border-left: 4px solid #1e3a8a; padding-left: 10px; font-size: 16px;">PAUTA</h3>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; white-space: pre-wrap; font-size: 14px; line-height: 1.8;">{pauta}</div>
    </div>
    
    <!-- Assinaturas -->
    <div style="margin-top: 60px;">
        <div style="display: flex; justify-content: space-between; text-align: center;">
            <div style="width: 45%;">
                <div style="border-top: 1px solid #333; height: 50px; margin-bottom: 5px;"></div>
                <div style="font-weight: bold;">{nome_presidente}</div>
                <div style="font-size: 12px; color: #666;">Presidente</div>
            </div>
            <div style="width: 45%;">
                <div style="border-top: 1px solid #333; height: 50px; margin-bottom: 5px;"></div>
                <div style="font-weight: bold;">{nome_secretario}</div>
                <div style="font-size: 12px; color: #666;">Secretário(a)</div>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 11px; color: #999; text-align: center;">
        Documento emitido em {data_emissao} pela {nome_igreja}
    </div>
</div>',
                'variaveis' => '["nome_igreja", "endereco_igreja", "telefone_igreja", "email_igreja", "cnpj", "inscricao_estadual", "data_reuniao", "tipo_reuniao", "local", "hora_inicio", "hora_fim", "nome_presidente", "nome_secretario", "pauta", "status", "data_emissao"]',
                'ativo'     => 1,
                'padrao'    => 1,
            ],
            // Papel Timbrado para Convites
            [
                'titulo'    => 'Convite para Evento - Igrejas Convidadas',
                'tipo'      => 'papel_timbrado_convite',
                'conteudo'  => '<div style="font-family: Georgia, serif; padding: 40px; max-width: 700px; margin: 0 auto; border: 2px solid #8b4513; background: #fdfbf7;">
    <!-- Cabeçalho -->
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="font-size: 16px; letter-spacing: 2px; color: #8b4513; font-weight: bold;">{nome_igreja}</div>
        <div style="font-size: 13px; color: #666; margin-top: 5px;">{endereco_igreja}</div>
        <div style="font-size: 12px; color: #888;">Tel: {telefone_igreja} | E-mail: {email_igreja}</div>
        <div style="font-size: 11px; color: #999;">CNPJ: {cnpj} | Inscrição Estadual: {inscricao_estadual}</div>
    </div>
    
    <div style="width: 100px; height: 2px; background: #8b4513; margin: 0 auto 30px;"></div>
    
    <!-- Saudação -->
    <p style="text-align: right; font-size: 15px; color: #333; margin-bottom: 30px;">
        {local}, {data_emissao}
    </p>
    
    <p style="font-size: 16px; color: #333; margin-bottom: 20px;">
        Ao(à) Rev. <strong>{nome_convidado}</strong><br>
        <span style="color: #666;">{nome_igreja_convidada}</span>
    </p>
    
    <!-- Corpo do Convite -->
    <div style="font-size: 15px; line-height: 1.8; color: #333; text-align: justify; margin-bottom: 30px;">
        <p>
            É com grande alegria e no amor de Cristo que nos dirigimos a vossa reverência 
            para convidar a vossa igreja e liderança para participar do nosso 
            <strong>{tipo_evento}</strong>, a realizar-se no dia <strong>{data_evento}</strong>, 
            às <strong>{hora_evento}</strong>, em <strong>{local_evento}</strong>.
        </p>
        <p>
            Contaremos com a honrosa presença de vossa congregação para juntos adorarmos 
            ao Senhor e fortalecermos os laços de fraternidade entre nossas igrejas.
        </p>
        <p>
            <em>"Oh! quão bom e quão suave é que os irmãos vivam em união." (Salmo 133:1)</em>
        </p>
    </div>
    
    <!-- Assinatura -->
    <div style="margin-top: 50px; text-align: center;">
        <div style="border-top: 1px solid #8b4513; width: 300px; margin: 0 auto 10px; padding-top: 8px;">
            <strong>Pr. {nome_pastor}</strong>
        </div>
        <div style="font-size: 13px; color: #666;">Pastor Presidente - {nome_igreja}</div>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 11px; color: #999; text-align: center;">
        Atenciosamente, {nome_igreja} | CNPJ: {cnpj} | Inscrição Estadual: {inscricao_estadual} | Documento emitido em {data_emissao}
    </div>
</div>',
                'variaveis' => '["nome_igreja", "endereco_igreja", "telefone_igreja", "email_igreja", "cnpj", "inscricao_estadual", "nome_convidado", "nome_igreja_convidada", "data_evento", "hora_evento", "local_evento", "tipo_evento", "nome_pastor", "local", "data_emissao"]',
                'ativo'     => 1,
                'padrao'    => 1,
            ],
            // Declaração de Membro
            [
                'titulo'    => 'Declaração de Membro em Comunhão',
                'tipo'      => 'outro',
                'conteudo'  => '<div style="font-family: Arial, sans-serif; padding: 40px; max-width: 700px; margin: 0 auto;">
    <div style="text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 20px; margin-bottom: 30px;">
        <div style="font-size: 22px; font-weight: bold; color: #1e3a8a;">{nome_igreja}</div>
        <div style="font-size: 13px; color: #666;">{endereco_igreja} - Tel: {telefone_igreja}</div>
        <div style="font-size: 12px; color: #888;">CNPJ: {cnpj} | Inscrição Estadual: {inscricao_estadual}</div>
    </div>
    
    <h2 style="text-align: center; color: #1e3a8a; margin-bottom: 30px;">DECLARAÇÃO</h2>
    
    <p style="font-size: 15px; line-height: 1.8; text-align: justify; color: #333; margin-bottom: 20px;">
        Declaramos, para os devidos fins, que o(a) irmão(ã) <strong>{nome_membro}</strong>, 
        portador(a) do CPF nº <strong>{cpf}</strong> e RG nº <strong>{rg}</strong>, 
        reside à <strong>{endereco_membro}</strong>,
        é membro desta igreja desde <strong>{data_batismo}</strong>, 
        encontrando-se em <strong>plena comunhão</strong> e exercendo suas atividades eclesiásticas normalmente.
    </p>
    
    <p style="font-size: 15px; line-height: 1.8; text-align: justify; color: #333; margin-bottom: 20px;">
        A presente declaração é emitida a pedido do(a) interessado(a) para <strong>{finalidade}</strong>, 
        gozando de fé pública eclesiástica.
    </p>
    
    <div style="margin-top: 60px; text-align: center;">
        <div style="border-top: 1px solid #333; width: 300px; margin: 0 auto 10px; padding-top: 8px;">
            <strong>Pr. {nome_pastor}</strong>
        </div>
        <div style="font-size: 13px; color: #666;">Pastor Presidente - {nome_igreja}</div>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 11px; color: #999; text-align: center;">
        {local}, {data_emissao} | Declaração nº {numero_documento} | CNPJ: {cnpj} | Inscrição Estadual: {inscricao_estadual}
    </div>
</div>',
                'variaveis' => '["nome_igreja", "endereco_igreja", "telefone_igreja", "cnpj", "inscricao_estadual", "nome_membro", "cpf", "rg", "endereco_membro", "data_batismo", "finalidade", "nome_pastor", "local", "data_emissao", "numero_documento"]',
                'ativo'     => 1,
                'padrao'    => 0,
            ],
        ];

        foreach ($modelos as $modelo) {
            $existe = $this->db->table('documentos_modelos')
                ->where('titulo', $modelo['titulo'])
                ->get()
                ->getRow();

            if ($existe === null) {
                $this->db->table('documentos_modelos')->insert($modelo);
            } else {
                $this->db->table('documentos_modelos')
                    ->where('id', $existe->id)
                    ->update([
                        'conteudo'  => $modelo['conteudo'],
                        'variaveis' => $modelo['variaveis'],
                        'tipo'      => $modelo['tipo'],
                        'ativo'     => $modelo['ativo'],
                        'padrao'    => $modelo['padrao'],
                    ]);
            }
        }
    }
}