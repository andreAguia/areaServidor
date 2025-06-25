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

            $grid->abreColuna(12, 6, 4);

            # Pega os assuntos
            $select = "SELECT categoria,
                              nome,
                              idServico
                         FROM tbservico
                     ORDER BY categoria, nome";

            $row = $intra->select($select);

            # Monta os quadros
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

            $servico = new Servico();
            $dados = $servico->get_dados($id);

            $grid->abreColuna(12);
//
//            # Cria um menu
//            $menu = new MenuBar();
//
//            # Voltar
//            $linkVoltar = new Link("Voltar", "?");
//            $linkVoltar->set_class('button');
//            $linkVoltar->set_accessKey('V');
//            $menu->add_link($linkVoltar, "left");
//
//            $menu->show();

            # Título
            br();
            titulotable($dados["nome"]);
            br();

            $div = new Div("divNota");
            $div->abre();

            # Define os Campos
            $campos = [
                ["O que é", "oque"],
                ["Quem Pode Requerer", "quem"],
                ["Como Requerer", "como"],
                ["Observações", "obs"],
            ];

            # Percorre os campos
            foreach ($campos as $item) {
                if (!empty($dados[$item[1]])) {

                    $menu = new Menu("menuProcedimentos");
                    $menu->add_item('titulo', $item[0], '#', $item[0]);
                    $menu->show();

//                    echo "<h6><b>{$item[0]}</b></h6>";
//                    hr("geral");
//                    br();

                    echo $dados[$item[1]];
                }
            }

            # Carrega os anexos
            $dados2 = $servico->get_anexos($id);

            # Defina a categoria para o agrupamento
            $categoriaAtual = null;

            # Verifica se tem algum anexo
            if (count($dados2) > 0) {

                # Menu
                $menu = new Menu("menuProcedimentos");

                # Percorre o array 
                foreach ($dados2 as $valor) {
                    # Verifica se mudou a categoria
                    if ($categoriaAtual <> $valor["categoria"]) {
                        $categoriaAtual = $valor["categoria"];
                        $menu->add_item('titulo', $valor["categoria"], '#', "Categoria " . $valor["categoria"]);
                    }

                    if (empty($valor["title"])) {
                        $title = $valor["texto"];
                    } else {
                        $title = $valor["title"];
                    }

                    # Verifica qual o tipo: 1-Documento e 2-Link
                    if ($valor["tipo"] == 1) {
                        # É do tipo Documento
                        $arquivoDocumento = PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . ".pdf";
                        if (file_exists($arquivoDocumento)) {
                            # Caso seja PDF abre uma janela com o pdf
                            $menu->add_item('linkWindow', $valor["titulo"], PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . '.pdf', $valor["descricao"]);
                        } else {
                            # Caso seja um .doc, somente faz o download
                            $menu->add_item('link', $valor["titulo"], PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . '.doc', $valor["descricao"]);
                        }
                    }

                    # Tipo Link
                    if ($valor["tipo"] == 2) {
                        $menu->add_item('linkWindow', $valor["texto"], $valor["link"], $title);
                    }

                    # Tipo pdf
                    if ($valor["tipo"] == 3) {
                        $arquivoDocumento = PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . ".pdf";

                        $menu->add_item('linkWindow', " - " . $valor["titulo"], PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . '.pdf', $valor["descricao"]);
                    }
                }


                $menu->show();
            }

            $div->fecha();

            $grid->fechaColuna();
            break;

        ############################################################################    
    }


    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}