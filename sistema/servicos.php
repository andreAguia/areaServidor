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

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    $top = new TitleBar("Serviços da GRH");
    $top->show();
    br();

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

            botaoVoltar('?');
            br();

//            # Div onde vai exibir o servico
//            $div = new Div("divNota");
//            $div->abre();

            # Título
            p($dados["nome"], "servicoTitulo");
            br();

            # O que é
            p("O que é", "servicoTitulo2");
            hr('documentacao');
            echo $dados["oque"];
            br(2);
            
            # quem
            p("Quem Pode Requerer", "servicoTitulo2");
            hr('documentacao');
            echo $dados["quem"];
            br(2);
            
            # Como
            p("Como Requerer", "servicoTitulo2");
            hr('documentacao');
            echo $dados["como"];
            
//            $div->fecha();

            var_dump($dados);

            $grid->fechaColuna();
            break;

        ############################################################################    
    }


    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}