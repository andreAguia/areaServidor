<?php

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
$acesso = Verifica::acesso($idUsuario, [1, 2, 12]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $procedimento = new Procedimento();
    $intra = new Intra();

    # Verifica a fase do programa
    $fase = get('fase');

    # Pega od Ids
    $idProcedimento = get('idProcedimento', get_session('idProcedimento'));
    $subCategoria = get('subCategoria');

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

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(6, 4, 3);

    titulotable("Menu");

    $div = new Div("divProcedimentos");
    $div->abre();

    # Menu
    $procedimento->menuPrincipal($subCategoria, $idProcedimento);
    $div->fecha();

    $grid->fechaColuna();
    # Define a coluna de Conteúdo
    $grid->abreColuna(6, 8, 9);
    titulotable("Procedimento");

    switch ($fase) {

        #############################################################################################################################
        #   Inicial
        ############################################################################################################################# 

        case "" :
        case "exibeProcedimento" :
            if (!empty($idProcedimento)) {
                if (Verifica::acesso($idUsuario, 1)) {
                    $procedimento->exibeProcedimento($idProcedimento, true);
                } else {
                    $procedimento->exibeProcedimento($idProcedimento);
                }
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