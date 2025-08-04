<?php

class Servico {

    /**
     * Abriga as várias rotina do Controle de Serviços da GRH
     * 
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $projeto        integer null O id do projeto a ser acessado
     * 
     */
    private $projeto = null;

    ###########################################################

    /**
     * Método Construtor
     */
    public function __construct() {
        
    }

    ###########################################################

    public function exibeMenu($idUsuario = null) {

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

        # Titulo
        tituloTable("Serviços da GRH");
        br();

        # Variaveis
        $categoria = null;

        $grid->fechaColuna();
        $grid->abreColuna(12, 6, 4);

        # Pega as Categorias
        $select = "SELECT categoria,
                              nome,
                              idServico
                         FROM tbservico
                     ORDER BY categoria, nome";

        $intra = new Intra();
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

            $link = new Link($item['nome'], "servicos.php?fase=exibeServico&id=" . $item['idServico']);
            $link->set_id('servicoLink');
            $link->show();

            echo "</li>";
        }

        # Fecha o último painel
        echo "</ul>";
        $painel1->fecha();

//        if (Verifica::acesso($idUsuario, 1)) {
//
//            # Inicia o painel
//            $painel1 = new Callout('success');
//            $painel1->set_title($item['categoria']);
//            $painel1->abre();
//
//            p(bold("CONFIGURAÇÕES"), 'servicoCategoria');
//            hr('documentacao');
//
//            echo "<ul>";
//            echo "<li>";
//
//            $link = new Link("Editar Serviços", "?fase=editaServico");
//            $link->set_id('servicoLink');
//            $link->show();
//
//            echo "</li>";
//            echo "</ul>";
//
//            $painel1->fecha();
//        }

        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    public function get_dados($id = null) {
        /**
         * Retorna um array com todas as informações
         * 
         * @param $id integer null o id
         * 
         * @syntax $servico->get_Dados([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT *
                     FROM tbservico
                     WHERE idServico = {$id}";

        $intra = new Intra();
        $row = $intra->select($select, false);
        return $row;
    }

    ###########################################################

    public function get_anexos($id = null) {
        /**
         * Retorna um array com os anexos de um servico
         * 
         * @param $id integer null o id
         * 
         * @syntax $servico->get_Dados([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT *
                     FROM tbservicoanexos
                    WHERE idServico = {$id}
                 ORDER BY categoria";

        $intra = new Intra();
        $row = $intra->select($select);
        return $row;
    }

    ###########################################################

    public function exibeServicos($id = null) {

        # Pega os dados desse servico
        $dados = $this->get_dados($id);

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

        # Título
        titulotable($dados["nome"]);
        br();

        $grid->fechaColuna();
        $grid->abreColuna(8);

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

                # Exibe o texto
                echo $dados[$item[1]];
            }
        }

        $div->fecha();
        $grid->fechaColuna();

        /*
         * Anexos
         */

        $grid->abreColuna(4);

        # Carrega os anexos
        $dados2 = $this->get_anexos($id);

        # Defina a categoria para o agrupamento
        $categoriaAtual = null;

        # Verifica se tem algum anexo
        if (count($dados2) > 0) {

            $painel1 = new Callout('success');
            $painel1->set_title('Anexos');
            $painel1->abre();

            # Menu
            $menu = new Menu("menuProcedimentos");

            # Percorre o array 
            foreach ($dados2 as $valor) {

                # Verifica se mudou a categoria
                if ($categoriaAtual <> $valor["categoria"]) {
                    $categoriaAtual = $valor["categoria"];
                    $menu->add_item('titulo', $valor["categoria"], '#', "Categoria " . $valor["categoria"]);
                    #$menu->add_item('br');
                }

                if (empty($valor["title"])) {
                    $title = $valor["texto"];
                } else {
                    $title = $valor["title"];
                }

                # Documento Digitado e Link
                if ($valor["tipo"] == 1 OR $valor["tipo"] == 4 OR $valor["tipo"] == 5) {
                    $menu->add_item('linkWindow', " - " . $valor["titulo"], "?fase=exibeAnexo&idServicoAnexos={$valor['idServicoAnexos']}", $valor["descricao"]);
                }

                # Tipo jpg
                if ($valor["tipo"] == 2) {
                    $menu->add_item('linkWindow', " - " . $valor["titulo"], PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . '.jpg', $valor["descricao"]);
                }

                # Tipo pdf
                if ($valor["tipo"] == 3) {
                    $menu->add_item('linkWindow', " - " . $valor["titulo"], PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . '.pdf', $valor["descricao"]);
                }
            }

            $menu->show();
            $painel1->fecha();
        }

        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    public function get_anexo($id = null) {
        /**
         * Retorna um array com os anexos de um servico
         * 
         * @param $id integer null o id
         * 
         * @syntax $servico->get_Dados([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT *
                     FROM tbservicoanexos
                    WHERE idServicoAnexos = {$id}";

        $intra = new Intra();
        $row = $intra->select($select, false);
        return $row;
    }

    ###########################################################

    function exibeAnexo($id, $editar = false) {

        /**
         * Fornece todos os dados da categoria
         */
        # Pega os dados
        $dados = $this->get_anexo($id);

        if (!empty($dados)) {

            # Se for documento
            if ($dados['tipo'] == 1) {

                # Limita o tamanho da tela
                $grid = new Grid("center");
                $grid->abreColuna(10);

                # Exibe o titulo
                #p("{$dados['categoria']} / {$dados['titulo']}", "procedimentoPai");
                br();

                p($dados['titulo'], "procedimentoTitulo");
                p($dados['descricao'], "procedimentoDescricao");
                hr("procedimento");

                if (empty($dados['texto'])) {
                    br(4);
                    p("Não há conteúdo", "center");
                    br(10);
                } else {
                    # Div onde vai exibir o procedimento
                    $div = new Div("divNota");
                    $div->abre();
                    
                    echo $dados['texto'];
                    
                    $div->fecha();
                }

                $grid->fechaColuna();
                $grid->fechaGrid();
            }

            # Se for link
            if ($dados['tipo'] == 4) {

                br(3);
                aguarde("Carregando ...");
                loadPage($dados["link"]);
            }

            # Se for rotina
            if ($dados['tipo'] == 5) {

                $rotina = new Rotina();
                $rotina->exibeRotina($dados['idRotina']);
            }
        }
    }

    ###########################################################
}
