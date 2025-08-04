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

        case "usuarios":
            iframe("admin_usuarios.php");
            break;

        ##################################################################

        case "regras":
            iframe("regras.php");
            break;

        ##################################################################

        case "historico":
            set_session("idServidor");
            iframe("admin_historico.php");
            break;

        ##################################################################

        case "menuSistema":
            # Título
            tituloTable('Sistema');
            $tamanhoImage = 64;
            br();

            # Inicia o menu
            $menu = new MenuGrafico(4);

            # Variáveis de Configuração
            $botao = new BotaoGrafico();
            $botao->set_label('Configurações');
            $botao->set_url('admin_configuracao.php');
            $botao->set_imagem(PASTA_FIGURAS . 'configuracao.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Edita as Variáveis de&#10;configuração da Intranet');
            $menu->add_item($botao);

            # Documentação
            $botao = new BotaoGrafico();
            $botao->set_label('Documentação');
            #$botao->set_target('blank');
            $botao->set_title('Documentação do Sistema');
            $botao->set_imagem(PASTA_FIGURAS . 'documentacao.png', $tamanhoImage, $tamanhoImage);
            $botao->set_url('admin_documentacao.php');
            $menu->add_item($botao);

            # Cadastro de Atualizações
            $botao = new BotaoGrafico();
            $botao->set_label('Atualizações');
            $botao->set_url('atualizacao.php');
            $botao->set_imagem(PASTA_FIGURAS . 'atualizacao.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Gerencia o cadastro de atualizações');
            $menu->add_item($botao);

            # Cadastro de Mensagens
            $botao = new BotaoGrafico();
            $botao->set_label('Mensagens');
            $botao->set_title('Cadastro de Mensagens');
            $botao->set_imagem(PASTA_FIGURAS . 'mensagem.jpg', $tamanhoImage, $tamanhoImage);
            $botao->set_url('mensagem.php');
            $menu->add_item($botao);

            $menu->show();
            break;

        ##################################################################
    }

    $page->terminaPagina();
}