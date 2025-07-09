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
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    if ($fase == "inicial") {
        br();
        $top = new TitleBar("Serviços da GRH");
        $top->show();
        br();
    }

    # Limita o tamanho da tela
    $grid->fechaColuna();

    switch ($fase) {

        ########################################################

        case "" :
        case "inicial" :
            /*
             * Exibe o Painel Inicial de Serviços por categoria
             */

            $grid->abreColuna(12, 6, 4);

            # Pega as Categorias
            $select = "SELECT categoria,
                              nome,
                              idServico
                         FROM tbservico
                     ORDER BY categoria, nome";

            $row = $intra->select($select);

            # Monta os quadros sendo um para cada categoria
            foreach ($row as $item) {
                if ($item['categoria'] <> $categoria) {

                    if (!is_null($categoria)) {
                        # Finalisa o painel anterior
                        echo "</ul>";
                        $painel1->fecha();
                    }

                    # Atualiza a variável de categoria
                    $categoria = $item['categoria'];

                    # Inicia o painel
                    $painel1 = new Callout('primary');
                    $painel1->set_title($item['categoria']);
                    $painel1->abre();
                    p(bold(maiuscula($item['categoria'])), 'servicoCategoria');
                    hr('documentacao');

                    echo "<ul>";
                }

                echo "<li>";

                $link = new Link($item['nome'], "?fase=exibeServico&id=" . $item['idServico']);
                $link->set_id('servicoLink');
                $link->show();

                echo "</li>";
            }

            # Fecha o último painel
            echo "</ul>";
            $painel1->fecha();

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
                $linkEditar = new Link("Editar", "?fase=editaServico&id={$id}");
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

        case "exibeAnexoDocumento" :
            /*
             * Quando o anexo for um documento digitado
             */

            $grid->abreColuna(12);

            $servico = new Servico();
            $servico->exibeAnexoDocumento($idServicoAnexos);

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
            set_session('voltaServico', "servicos.php?fase=exibeServico&id={$id}");

            loadPage("cadastroServico.php?fase=editar&id={$id}");

            $grid->fechaColuna();
            break;

        ########################################################
    }


    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}