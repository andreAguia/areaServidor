<?php
/**
 * Chat
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario);

if($acesso){
    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
    $chat = new Chat();

    # Verifica a fase do programa
    $fase = get('fase','chat'); 

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    
    
    switch ($fase){
        
########################################################################################
        
        case "chat" :
            # Limita o tamanho da tela
            $grid1 = new Grid();
            $grid1->abreColuna(3);
            
            p("GRH","f18");
            
            
            $grid1->fechaColuna();
            $grid1->abreColuna(9);
            
            # Cria um menu
            $menu = new MenuBar();

            # Voltar
            $linkVoltar = new Link("Voltar","../../grh/grhSistema/grh.php");
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Voltar');
            $menu->add_link($linkVoltar,"left");
            
            # Área do Servidor
            $linkArea = new Link("Área do Servidor","areaServidor.php");
            $linkArea->set_class('button');
            $linkArea->set_title('Área do Servidor');
            #$menu->add_link($linkArea,"right");
            
            $menu->show();
            
            $div = new Div("divAreaMensagens");
            $div->abre();
            
            $chat->exibe_mensagens();
            
            $div->fecha();
            
            $form = new Form('?fase=valida','chat');        

                # mensagem
                $controle = new Input('mensagem','texto');
                $controle->set_size(200);
                $controle->set_linha(1);
                $controle->set_col(12);
                $controle->set_autofocus(TRUE); 
                $controle->set_title('Insira aqui a mensagem');
                $controle->set_onChange('formPadrao.submit();');
                $form->add_item($controle);

            $form->show();
            
            $grid1->fechaColuna();
            $grid1->fechaGrid();
            break;
        
    ################################################################################

        case "valida":
            # Valida o Login

            # Pega os dados digitados
            $mensagem = post('mensagem');
            
            # Campos
            $campos = array("idUsuario","idSala","data","tipo","mensagem");
            $valores = array($idUsuario,NULL,date("Y-m-d H:i:s"),"1",$mensagem);
            
            # Grava no banco
            $intra->gravar($campos,$valores,NULL,'tbchat','idChat',FALSE);
            
            loadPage('?');
            break;
    
    ################################################################################

    }
    
        
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}