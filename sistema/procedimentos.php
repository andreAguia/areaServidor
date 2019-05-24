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
$acesso = Verifica::acesso($idUsuario);

if($acesso){
    # Conecta ao Banco de Dados
    $procedimento = new Procedimento();
    $intra = new Intra();
    
    # Verifica a fase do programa
    $fase = get('fase');
    
    # Pega od Ids    
    $idCategoria = get('idCategoria',get_session('idCategoria'));
    $idProcedimento = get('idProcedimento',get_session('idProcedimento'));
    
    # Joga os parâmetros par as sessions    
    set_session('idCategoria',$idCategoria);
    set_session('idProcedimento',$idProcedimento);
    
    # Começa uma nova página
    $page = new Page();
    
    if($fase == "exibeProcedimento"){
        
        # Pega os dados
        $dados = $procedimento->get_dadosProcedimento($idProcedimento);
        $link = $dados["link"];
        
        if(!vazio($link)){        
            # Carrega a página do link
            $page->set_bodyOnLoad("ajaxLoadPage('$link','divProcedimento',null);");
        }
    }
    $page->iniciaPagina();
    
    # Cabeçalho da Página
    AreaServidor::cabecalho();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);  
    
    if(Verifica::acesso($idUsuario,1)){
        # Cria um menu
        $menu1 = new MenuBar("button-group");

        # Sair da Área do Servidor
        $linkVoltar = new Link("Voltar","../../grh/grhSistema/grh.php");
        $linkVoltar->set_class('button');
        $linkVoltar->set_title('Voltar a página anterior');    
        #$menu1->add_link($linkVoltar,"left");

        # Categorias
        $linkCategoria = new Link("Categorias","procedimentoCategoria.php");
        $linkCategoria->set_class('button');
        $linkCategoria->set_title('Gerencia as categorias');
        $menu1->add_link($linkCategoria,"right");

        # Categorias
        $linkProcedimento = new Link("Procedimentos","procedimentoNota.php");
        $linkProcedimento->set_class('button');
        $linkProcedimento->set_title('Gerencia as categorias');
        $menu1->add_link($linkProcedimento,"right");

        $menu1->show();
    }else{
        br();
    }
    
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
    $procedimento->menuCategorias($idCategoria,$idProcedimento,$idUsuario);
    
    $grid->fechaColuna();
    
    # Define a coluna de Conteúdo
    $grid->abreColuna($col2P,$col2M,$col2L);
    
    switch ($fase){        
        
    #############################################################################################################################
    #   Inicial
    ############################################################################################################################# 
        
        case "" :       
            
            break;
   
    ############################################################################
        
        case "exibeProcedimento" :
            
            if(!vazio($link)){   
            
                # Monta o painel
                $painel = new Callout();
                $painel->abre();

                # Div onde vai exibir o procedimento
                $div = new Div("divProcedimento");
                $div->abre();
                $div->fecha();

                # Fecha o painel
                $painel->fecha();

            }
            break;
        
    ############################################################################    
        
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();  
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}