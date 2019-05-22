<?php
/**
 * Manual de Procedimentos
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){
    # Conecta ao Banco de Dados
    $procedimento = new Procedimento();
    
    # Verifica a fase do programa
    $fase = get('fase');
    
    # Pega od Ids
    $idCategoria = get("idCategoria");
    $idProcedimento = get("idProcedimento");
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho da Página
    AreaServidor::cabecalho();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);  
    
    # Cria um menu
    $menu1 = new MenuBar("button-group");

    # Sair da Área do Servidor
    $linkVoltar = new Link("Voltar","../../grh/grhSistema/grh.php");
    $linkVoltar->set_class('button');
    $linkVoltar->set_title('Voltar a página anterior');    
    $menu1->add_link($linkVoltar,"left");
    
    # Novo Projeto
    $linkSenha = new Link("Novo Projeto","?fase=projetoNovo");
    $linkSenha->set_class('button');
    $linkSenha->set_title('Cria novo projeto');
    #$menu1->add_link($linkSenha,"right");
    
    # Fazendo
    $linkSenha = new Link("Fazendo","?fase=fazendo");
    $linkSenha->set_class('button success');
    $linkSenha->set_title('Exibe as Tarefas que estão sendo feitas');
    #$menu1->add_link($linkSenha,"right");

    $menu1->show();
    
    # Título
    titulo("Manual de Procedimentos");
    
    # Define o grid
    $col1P = 0;
    $col1M = 4;
    $col1L = 3;

    $col2P = 12 - $col1P;
    $col2M = 12 - $col1M;
    $col2L = 12 - $col1L;
    
    # Limita o tamanho da tela
    $grid = new Grid();    
    $grid->abreColuna($col1P,$col1M,$col1L);
    br();
    
    # Menu de Projetos
    $procedimento->menuCategorias($idCategoria,$idProcedimento);
    
    $grid->fechaColuna();
    
    switch ($fase){        
        
    #############################################################################################################################
    #   Inicial
    ############################################################################################################################# 
        
        case "" :
            # Define a coluna de Conteúdo
            $grid->abreColuna($col2P,$col2M,$col2L);
                        
            $div = new Div("teste");
            $div->abre();
            
            $div->fecha();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
    
    #############################################################################################################################
        
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();  
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}