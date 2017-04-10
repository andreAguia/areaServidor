<?php
/**
 * Área do Servidor
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase','menu'); # Qual a fase

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
    $grid = new Grid();
    $grid->abreColuna(12);
    
    switch ($fase)
    {	
        # Exibe o Menu Inicial
        case "menu" :
    
        # Cria um menu
        $menu1 = new MenuBar();

        # Sair da Área do Servidor
        $linkVoltar = new Link("Sair","login.php");
        $linkVoltar->set_class('button');
        $linkVoltar->set_title('Sair do Sistema');
        $linkVoltar->set_confirma('Tem certeza que deseja sair do sistema?');
        $linkVoltar->set_accessKey('i');
        $menu1->add_link($linkVoltar,"left");
        $menu1->show();

        titulo('Área do Servidor');
        $tamanhoImage = 70;

        $fieldset = new Fieldset('Sistemas');
        $fieldset->abre();

        $menu = new MenuGrafico();

        $botao = new BotaoGrafico();
        $botao->set_label('Alterar Senha');
        $botao->set_url('trocarSenha.php');
        $botao->set_image(PASTA_FIGURAS.'alteraSenha.png',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Alterar Senha');
        $botao->set_accesskey('S');
        $menu->add_item($botao);
        
        if(Verifica::acesso($idUsuario,2)){   // Verifica acesso ao sistema
            $botao = new BotaoGrafico();
            $botao->set_label('Sistema de Pessoal');
            $botao->set_url('../../grh/grhSistema/grh.php');
            $botao->set_image(PASTA_FIGURAS.'sistemaPessoal.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Acessa o Sistema de Pessoal');
            $botao->set_accesskey('P');
            $menu->add_item($botao);
        }
        
        if(Verifica::acesso($idUsuario,1)){   // Somente Administradores
            $botao = new BotaoGrafico();
            $botao->set_label('Administração');
            $botao->set_url('administracao.php');
            $botao->set_image(PASTA_FIGURAS.'framework.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Administração dos Sistemas');
            $botao->set_accesskey('A');
            $menu->add_item($botao);
        }
        
        $menu->show();

        $fieldset->fecha();
        
        # Exibe o rodapé da página
        br();
        AreaServidor::rodape($idUsuario);

        ###############################
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}

