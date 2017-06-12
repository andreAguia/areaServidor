<?php
/**
 * Área do Servidor
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

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
        $menu1->add_link($linkVoltar,"left");
        
        # Administração do Sistema
        if(Verifica::acesso($idUsuario,1)){   // Somente Administradores
            $linkAdm = new Link("Administração","administracao.php");
            $linkAdm->set_class('button success');
            $linkAdm->set_title('Administração dos Sistemas');
            $menu1->add_link($linkAdm,"right");
        }
        
        # Alterar Senha
        $linkSenha = new Link("Alterar Senha","trocarSenha.php");
        $linkSenha->set_class('button');
        $linkSenha->set_title('Altera a senha do usuário logado');
        $menu1->add_link($linkSenha,"right");
        
        $menu1->show();

        titulo('Área do Servidor');
        $tamanhoImage = 70;
        
        # Exibe os dados do Servidor
        Grh::listaDadosServidor($intra->get_idServidor($idUsuario));

        $fieldset = new Fieldset('Sistemas');
        $fieldset->abre();

        $menu = new MenuGrafico();
        
        if(Verifica::acesso($idUsuario,2)){   // Verifica acesso ao sistema
            $botao = new BotaoGrafico();
            $botao->set_label('Sistema de Pessoal');
            $botao->set_url('../../grh/grhSistema/grh.php');
            $botao->set_image(PASTA_FIGURAS.'sistemaPessoal.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Acessa o Sistema de Pessoal');
            $botao->set_accesskey('P');
            $menu->add_item($botao);
        }
        
        if(Verifica::acesso($idUsuario,3)){   // Acesso ao sistema de férias
            $botao = new BotaoGrafico();
            $botao->set_label('Sistema de Férias');
            $botao->set_url('sistemaFerias.php');
            $botao->set_image(PASTA_FIGURAS.'sunsetbeach.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Sistema de Controle da Solicitação de Férias');
            $botao->set_accesskey('F');
            $menu->add_item($botao);
        }
        
        $menu->show();

        $fieldset->fecha();
        
        # Exibe o rodapé da página
        br();
        AreaServidor::rodape($idUsuario);

##############################
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}

