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
    $idProcedimento = get('idProcedimento');
    $subCategoria = get('subCategoria');
    $categoria = get('categoria');

    # Joga os parâmetros par as sessions
    #set_session('idProcedimento', $idProcedimento);
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    $top = new TitleBar("Procedimentos da GRH");
    $top->show();

    # Define o grid
    $col1P = 6;
    $col1M = 4;
    $col1L = 4;

    $col2P = 12 - $col1P;
    $col2M = 12 - $col1M;
    $col2L = 12 - $col1L;

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna($col1P, $col1M, $col1L);

    # Menu
    if (Verifica::acesso($idUsuario, 1)) {
        $procedimento->menuPrincipal($subCategoria, $idProcedimento, true);
    } else {
        $procedimento->menuPrincipal($subCategoria, $idProcedimento);
    }

    $grid->fechaColuna();

    # Define a coluna de Conteúdo
    $grid->abreColuna($col2P, $col2M, $col2L);

    switch ($fase) {

        #############################################################################################################################
        #   Inicial
        ############################################################################################################################# 

        case "" :
        case "exibeProcedimento" :

            # Verifica se é um procedimento propriamente dito
            if (!empty($idProcedimento)) {
                # Verifica se pode ou não ser editado (user admin)
                if (Verifica::acesso($idUsuario, 1)) {
                    $procedimento->exibeProcedimento($idProcedimento, true);
                } else {
                    $procedimento->exibeProcedimento($idProcedimento);
                }
            } else {
                # Verifica se é a tela inicial
                if (empty($subCategoria)) {
                    # Verifica se é categoria
                    if (empty($categoria)) {
                        br(10);
                        p("Procedimentos GRH", "f26", "center");
                        p("Versão: " . VERSAO . "<br/>Atualizado em: " . ATUALIZACAO, "f12", "center");
                    } else {
                        # Verifica se é subcategoria
                        $procedimento->exibeProcedimentoCategoria($categoria);
                    }
                } else {
                    # Verifica se é subcategoria
                    $procedimento->exibeProcedimentoSubCategoria2($subCategoria);
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