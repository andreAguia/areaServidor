<?php

/**
 * Administração de Usuários
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

if (Verifica::acesso($idUsuario, 1)) {

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Verifica a fase do programa
    $fase = get('fase');

    # Limpa a session de Pesquisa
    set_session('sessionParametro');
    set_session('idServidor');

    switch ($fase) {

        case "menuUsuario":
            # Título
            tituloTable('Gestão de Usuários');
            $tamanhoImage = 64;
            br();

            # Inicia o menu
            $menu = new MenuGrafico(3);

            # Administração de Usuários
            $botao = new BotaoGrafico();
            $botao->set_label('Cadastro de Usuários');
            $botao->set_url('admin_usuarios.php');
            $botao->set_imagem(PASTA_FIGURAS . 'usuarios.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Gerencia os Usuários');
            $menu->add_item($botao);

            # Regras
            $botao = new BotaoGrafico();
            $botao->set_label('Regras de Acesso');
            $botao->set_url('admin_regras.php');
            $botao->set_imagem(PASTA_FIGURAS . 'regras.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Cadastro de Regras');
            $menu->add_item($botao);

            # Histórico Geral
            $botao = new BotaoGrafico();
            $botao->set_label('Histórico de Acesso');
            $botao->set_title('Histórico Geral do Sistema');
            $botao->set_imagem(PASTA_FIGURAS . 'historico.png', $tamanhoImage, $tamanhoImage);
            $botao->set_url('admin_historico.php');
            $menu->add_item($botao);

            # Computadores (IPs)
            $botao = new BotaoGrafico();
            $botao->set_label('Acesso ao Sistema');
            $botao->set_title('Cadastro de computadores com acesso ao sistema');
            $botao->set_imagem(PASTA_FIGURAS . 'computador.png', $tamanhoImage, $tamanhoImage);
            $botao->set_url('computador.php');
            #$menu->add_item($botao);

            $menu->show();
            break;

        ##################################################################

        case "menuSistema":
            # Título
            tituloTable('Gestão do Sistema');
            $tamanhoImage = 64;
            br();

            # Inicia o menu
            $menu = new MenuGrafico(4);

            # Cadastro de Atualizações
            $botao = new BotaoGrafico();
            $botao->set_label('Atualizações');
            $botao->set_url('admin_atualizacao.php');
            $botao->set_imagem(PASTA_FIGURAS . 'atualizacao.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Gerencia o cadastro de atualizações');
            $menu->add_item($botao);

            # Cadastro de Mensagens
            $botao = new BotaoGrafico();
            $botao->set_label('Mensagens');
            $botao->set_title('Cadastro de Mensagens');
            $botao->set_imagem(PASTA_FIGURAS . 'mensagem.jpg', $tamanhoImage, $tamanhoImage);
            $botao->set_url('admin_mensagem.php');
            $menu->add_item($botao);

            # Variáveis de Configuração
            $botao = new BotaoGrafico();
            $botao->set_label('Configurações');
            $botao->set_url('admin_configuracao.php');
            $botao->set_imagem(PASTA_FIGURAS . 'configuracao.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Edita as Variáveis de&#10;configuração da Intranet');
            $menu->add_item($botao);

            # Documentação
            $botao = new BotaoGrafico();
            $botao->set_label('Documentação<br/>Problemas');
            #$botao->set_target('blank');
            $botao->set_title('Documentação do Sistema');
            $botao->set_imagem(PASTA_FIGURAS . 'documentacao.png', $tamanhoImage, $tamanhoImage);
            $botao->set_url('admin_documentacao.php');
            $menu->add_item($botao);

            $menu->show();
            break;

        ##################################################################

        case "menuProcedimento":
            # Título
            tituloTable('Procedimentos');
            $tamanhoImage = 64;
            br();

            # Inicia o menu
            $menu = new MenuGrafico(4);

            # Cadastro de Serviços
            $botao = new BotaoGrafico();
            $botao->set_label('Serviços');
            #$botao->set_target('blank');
            $botao->set_title('Cadastro de Serviços');
            $botao->set_imagem(PASTA_FIGURAS . 'lista.png', $tamanhoImage, $tamanhoImage);
            $botao->set_url("cadastroServico.php");
            $menu->add_item($botao);

            # Controle de procedimentos
            $botao = new BotaoGrafico();
            $botao->set_label('Procedimentos');
            $botao->set_url('procedimentos.php');
            #$botao->set_url('pastaDigitalizada.php');
            $botao->set_imagem(PASTA_FIGURAS . 'procedimentos.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Sistema de procedimentos');
            $menu->add_item($botao);

            # Controle de Rotinas 2
            $botao = new BotaoGrafico();
            $botao->set_label('Rotinas');
            $botao->set_url('admin_rotina.php');
            #$botao->set_url('pastaDigitalizada.php');
            $botao->set_imagem(PASTA_FIGURAS . 'rotina.jpg', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Sistema de controle de manuais de procedimentos');
            $menu->add_item($botao);

            # Menu de Documentos
            $botao = new BotaoGrafico();
            $botao->set_label('Menu de Documentos');
            #$botao->set_target('blank');
            $botao->set_title('Menu de Documentos do sistema GRH');
            $botao->set_imagem(PASTA_FIGURAS . 'menu.png', $tamanhoImage, $tamanhoImage);
            $botao->set_url("../../grh/grhSistema/cadastroMenuDocumentos.php");
            $menu->add_item($botao);

            # Tarefas
            $botao = new BotaoGrafico();
            $botao->set_label('Tarefas');
            $botao->set_url('projeto.php');
            $botao->set_imagem(PASTA_FIGURAS . 'atribuicoes.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Sistema de gestão de tarefas');
            $botao->set_target("_blank");
            $menu->add_item($botao);

            # Notas
            $botao = new BotaoGrafico();
            $botao->set_label('Notas');
            $botao->set_url('projetoNota.php');
            $botao->set_imagem(PASTA_FIGURAS . 'contratos.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Sistema de notas dos sistemas');
            $botao->set_target("_blank");
            $menu->add_item($botao);

            $menu->show();
            break;

        ##################################################################
    }

    $page->terminaPagina();
}