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

        #############################################################################################################################
        #   Inicial
        ############################################################################################################################# 

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
                    $painel1 = new Callout('success');
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

        case "exibeServico" :

            $servico = new Servico();
            $dados = $servico->get_dados($id);

            $grid->abreColuna(12);

            
            # Cria um menu
            $menu = new MenuBar();

            # Voltar
            $linkVoltar = new Link("Voltar", "?");
            $linkVoltar->set_class('button');
            $linkVoltar->set_accessKey('V');
            $menu->add_link($linkVoltar, "left");
            
            $menu->show();

            # Título
            titulotable($dados["nome"]);
            br();

            $grid->fechaColuna();
            $grid->abreColuna(8);
            
            # Div onde vai exibir o servico
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
                    echo "<h3><b>{$item[0]}</b></h3>";
                    echo $dados[$item[1]];
                }
            }

            $div->fecha();
            
            $grid->fechaColuna();
            $grid->abreColuna(4);
            
            # Div onde vai exibir o servico
            $div = new Div("divNota");
            $div->abre();
            
            # Define os Campos
            $campos1 = [
                ["Documentação", "documentos"],
                ["Legislação", "legislacao"],
            ];

            # Percorre os campos
            foreach ($campos1 as $item) {
                if (!empty($dados[$item[1]])) {
                    echo "<h3><b>{$item[0]}</b></h3>";
                    echo $dados[$item[1]];
                }
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