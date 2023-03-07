q<?php

/**
 * Manual de Procedimentos
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {
    # Conecta ao Banco de Dados
    $procedimento = new Procedimento();
    $intra = new Intra();

    # Verifica a fase do programa
    $fase = get('fase');

    # Pega od Ids
    $idProcedimento = get('idProcedimento', get_session('idProcedimento'));

    # Joga os parâmetros par as sessions
    set_session('idProcedimento', $idProcedimento);

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Novo procedimento somente para administradores
    if (Verifica::acesso($idUsuario, 1)) {
        $menu1 = new MenuBar();        
        $gerenciarProcedimento = new Link("Gerenciar", 'procedimentoNota.php');
        $gerenciarProcedimento->set_class('button small');
        $gerenciarProcedimento->set_title('Gerenciar os Procedimento');
        $menu1->add_link($gerenciarProcedimento, "right");
        
        $menu1->show();
    }else{
        br();
    }

    # Define o grid
    $col1P = 0;
    $col1M = 4;
    $col1L = 3;

    $col2P = 12 - $col1P;
    $col2M = 12 - $col1M;
    $col2L = 12 - $col1L;

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna($col1P, $col1M, $col1L);
    
    titulotable("Menu");

    # Menu de Projetos
    $procedimento->menuPrincipal($idProcedimento, $idUsuario);

    $grid->fechaColuna();

    # Define a coluna de Conteúdo
    $grid->abreColuna($col2P, $col2M, $col2L);
    titulotable("Procedimento");

    switch ($fase) {

        #############################################################################################################################
        #   Inicial
        ############################################################################################################################# 

        case "" :

            break;

        ############################################################################

        case "exibeProcedimento" :

            if (!empty($idProcedimento)) {
                $procedimento->exibeProcedimento($idProcedimento, $idUsuario);
            }
            break;

        ############################################################################    
    }

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}