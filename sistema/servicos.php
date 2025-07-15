<?php

/**
 * Serviços
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
    $intra = new Intra();

    # Verifica a fase do programa
    $fase = get('fase', 'inicial');

    # Pega od Ids
    $id = get('id');
    $idServicoAnexos = get('idServicoAnexos');

    # Variaveis
    $categoria = null;

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    $esconde = get_session("escondeCabecalho"); // Esconde quando for da area do Servidor e exibe quando for no sistema de rh
    if (empty($esconde) OR !$esconde) {
        AreaServidor::cabecalho();
        br();
    }

    # Limita o tamanho da tela
    $grid = new Grid();

    switch ($fase) {

        ########################################################

        case "" :
        case "inicial" :
            /*
             * Exibe o Painel Inicial de Serviços por categoria
             */

            $grid->abreColuna(12);

            $servico = new Servico();
            $servico->exibeMenu();

            if (Verifica::acesso($idUsuario, 1)) {

                # Cria um menu
                $menu = new MenuBar();

                # Editar
                $linkEditar = new Link("Editar Serviços", "?fase=editaServico");
                #$linkEditar->set_class('button');
                $menu->add_link($linkEditar, "left");

                $menu->show();
            }

            $grid->fechaColuna();
            break;

        ########################################################    

        case "exibeServico" :
            /*
             * Exibe a tela de Serviço
             */

            $grid->abreColuna(12);

            # Cria um menu
            $menu = new MenuBar();

            # Voltar
            $linkVoltar = new Link("Voltar", "?");
            $linkVoltar->set_class('button');
            $linkVoltar->set_accessKey('V');
            $menu->add_link($linkVoltar, "left");

            if (Verifica::acesso($idUsuario, 1)) {
                # Editar
                $linkEditar = new Link("Editar Serviço", "?fase=editaServico&id={$id}");
                $linkEditar->set_class('button');
                $menu->add_link($linkEditar, "right");

                # Editar Anexos
                $linkEditar = new Link("Editar Anexos", "?fase=editaAnexo&id={$id}");
                $linkEditar->set_class('button');
                $menu->add_link($linkEditar, "right");
            }

            $menu->show();

            # Exibe o serviço
            $servico = new Servico();
            $servico->exibeServicos($id);

            $grid->fechaColuna();
            break;

        ########################################################    

        case "exibeAnexo" :
            /*
             * Quando o anexo for um documento digitado
             */

            $grid->abreColuna(12);

            $servico = new Servico();
            $servico->exibeAnexo($idServicoAnexos);

            $grid->fechaColuna();
            break;

        ########################################################    

        case "editaServico" :
            /*
             * Edita Serviço
             */

            $grid->abreColuna(12);

            # Exibe o aguarde
            br(8);
            aguarde("Carregando");

            # Informa a origem
            if (empty($id)) {
                set_session('voltaServico', "servicos.php");
                loadPage("cadastroServico.php");
            } else {
                set_session('voltaServico', "servicos.php?fase=exibeServico&id={$id}");
                loadPage("cadastroServico.php?fase=editar&id={$id}");
            }

            $grid->fechaColuna();
            break;

        ########################################################    

        case "editaAnexo" :
            /*
             * Edita Serviço
             */

            $grid->abreColuna(12);

            # Exibe o aguarde
            br(8);
            aguarde("Carregando");

            # Define a volta
            set_session('voltaServico', "servicos.php?fase=exibeServico&id={$id}");

            # Define o anexo de qual serviço está sendo manipulado
            set_session('idServico', $id);

            # Vai para a página
            loadPage("cadastroServicoAnexos.php");

            $grid->fechaColuna();
            break;

        ########################################################
    }


    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}