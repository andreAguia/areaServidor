<?php

class Procedimento {

    /**
     * Abriga as várias rotina do Sistema de Manual de Procedimentos
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     */
    # Array com os tipos de procedimentos
    private $tiposProcedimentos = null;

    ###########################################################

    /**
     * Método Construtor
     */
    public function __construct() {
        
        # Define o array com os tipos de procedimentos
        $this->tiposProcedimentos = [
            [null, null],
            [1, "Digitado"],
            [2, "Arquivo JPG"],
            [3, "Arquivo PDF"],
            [4, "Link"],
            [5, "Rotina"]
        ];
    }

    ###########################################################

    function get_tiposProcedimento() {

        /**
         * Define o array de procedimentos
         */
        return $this->tiposProcedimentos;
    }

    ##########################################################

    public function menuPrincipal($subCategoria = null, $idProcedimento = null, $editar = false) {
        /**
         * Exibe o menu de categoria.
         *
         * @syntax Procedimento::menuCategorias;
         *         
         * @param $idProcedimento integer null o id do procedimento a ser ressaltado no menu informando que está sendo editada.
         *
         */
        # Acessa o banco de dados
        $intra = new Intra();
        br();

        # Prepara o array JáAbertos
        $abertos[] = null;                      // Cria o Array com o valor nullo
        $jaAbertos = get_session("abertos");    // Carrega ele com os valores anteriores (se tiver)

        if (!empty($jaAbertos)) {

            foreach ($jaAbertos as $item) {
                # Se já tivero iten, retira do array
                $key = array_search($item, $abertos);
                if ($key !== false) {
                    unset($abertos[$key]);
                } else {
                    array_push($abertos, $item);
                }
            }
        }

        # Verifica se tem subCategoria e adiciona no array
        if (!empty($subCategoria)) {
            $key = array_search($subCategoria, $abertos);
            if ($key !== false) {
                unset($abertos[$key]);
            } else {
                array_push($abertos, $subCategoria);
            }
        }

        # Categorias
        $arrayCategorias = $this->get_menuCategorias();
        if (empty($arrayCategorias)) {
            return;
        }

        # Inicia o menu           
        $menu1 = new Menu("menuProcedimentos");

        # Exibe os itens
        foreach ($arrayCategorias as $valor) {
            $menu1->add_item('titulo', '<b>' . $valor["categoria"] . '</b>');
            $categoriaAnterior = $valor["categoria"];
            $subCategoriaAnterior = null;
            $tituloAnterior = null;

            # Pega as subCategorias
            $arraySubCategorias = $this->get_menuSubCategorias($valor["categoria"]);

            foreach ($arraySubCategorias as $valor2) {
                # Exibe as subcategorias se estiver aberto
                $menu1->add_item('link', $valor2["subCategoria"], '?fase=exibeProcedimento&subCategoria=' . $valor2["subCategoria"]);

                # Verifica se abre o itens
                if (in_array($valor2["subCategoria"], $abertos)) {
                    # Pega os itens
                    $arrayTitulos = $this->get_menuTitulos($valor2["subCategoria"]);

                    foreach ($arrayTitulos as $valor3) {
                        if ($idProcedimento == $valor3['idProcedimento']) {
                            $menu1->add_item('sublink', "<b>-|{$valor3['titulo']}|</b>", '?fase=exibeProcedimento&idProcedimento=' . $valor3["idProcedimento"]);
                        } else {
                            $menu1->add_item('sublink', "- {$valor3['titulo']}", '?fase=exibeProcedimento&idProcedimento=' . $valor3["idProcedimento"]);
                        }
                    }
                }
            }
        }

        if ($editar) {
            $menu1->add_item('titulo', '<b>Gerenciar</b>');
            $menu1->add_item('link', "Editar Procedimentos", 'procedimentoNota.php');
        }

        # exibe o menu
        $menu1->show();

        # trata o array para gtravar na session
        if (!empty($jaAbertos)) {
            $abertos = array_filter($abertos);  // apaga os valores em branco (se tiver)
            $abertos = array_unique($abertos);  // retira as duplicatas
        }

        set_session('abertos', $abertos);
        br(10);
    }

    ##########################################################

    function get_menuCategorias() {

        /**
         * Fornece as categorias para o menu
         */
        $intra = new Intra();

        $select = "SELECT categoria,
                          idProcedimento
                     FROM tbprocedimento
                    WHERE categoria IS NOT NULL 
                 GROUP BY categoria
                 ORDER BY categoria";

        return $intra->select($select);
    }

    ##########################################################

    function get_menuSubCategorias($categoria) {

        /**
         * Fornece as subCategorias para o menu
         */
        $intra = new Intra();

        $select = "SELECT subCategoria,
                          idProcedimento
                     FROM tbprocedimento
                    WHERE subCategoria IS NOT NULL 
                      AND categoria = '{$categoria}'
                 GROUP BY subCategoria       
                 ORDER BY subCategoria";

        return $intra->select($select);
    }

    ##########################################################

    function get_menuTitulos($subCategoria) {

        /**
         * Fornece titulos para o menu
         */
        $intra = new Intra();

        $select = "SELECT titulo,
                           idProcedimento
                      FROM tbprocedimento
                     WHERE titulo IS NOT NULL 
                       AND subCategoria = '{$subCategoria}'
                       AND visibilidade = 1    
                  ORDER BY numOrdem, titulo";

        return $intra->select($select);
    }

    ###########################################################

    function get_dadosProcedimento($idProcedimento) {

        /**
         * Fornece todos os dados da categoria
         */
        $intra = new Intra();

        # Pega os dados
        $select = "SELECT *
                     FROM tbprocedimento
                    WHERE idProcedimento = $idProcedimento";
        return $intra->select($select, false);
    }

    ###########################################################

    function get_tipo($idProcedimento) {

        /**
         * Fornece o id de um titulo
         */
        $intra = new Intra();

        # Pega os dados
        $select = "SELECT tipo
                     FROM tbprocedimento
                    WHERE idProcedimento = '{$idProcedimento}'";

        $dados = $intra->select($select, false);

        if (empty($dados[0])) {
            return null;
        } else {
            return $dados[0];
        }
    }

    ###########################################################

    function exibeProcedimento($idProcedimento, $editar = false) {

        /**
         * Fornece todos os dados da categoria
         */
        # Pega os dados
        $dados = $this->get_dadosProcedimento($idProcedimento);

        $grid = new Grid();
        $grid->abreColuna(12);

        # Div onde vai exibir o procedimento
        $div = new Div("divNota");
        $div->abre();

        if (!empty($dados)) {

            # Exibe de acordo com o tipo do arquivo
            switch ($dados["tipo"]) {
                case 1 : // documento
                    br();
                    # Botão de Editar
                    if ($editar) {
                        $divBtn = new Div("editarProcedimento");
                        $divBtn->abre();

                        $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
                        $btnEditar->set_class('button secondary');
                        $btnEditar->set_title('Editar o Procedimento');
                        $btnEditar->show();

                        $divBtn->fecha();
                    }

                    # Exibe o titulo
                    p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                    br();

                    p($dados['titulo'], "procedimentoTitulo");
                    p($dados['descricao'], "procedimentoDescricao");
                    hr("procedimento");

                    if (empty($dados['textoProcedimento'])) {
                        br(4);
                        p("Não há conteúdo", "center");
                        br(10);
                    } else {
                        echo $dados['textoProcedimento'];
                    }
                    break;
                case 2: // arquivo jpg
                    br();
                    # Botão de Editar
                    if ($editar) {
                        $divBtn = new Div("editarProcedimento");
                        $divBtn->abre();

                        $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
                        $btnEditar->set_class('button secondary');
                        $btnEditar->set_title('Editar o Procedimento');
                        $btnEditar->show();

                        $divBtn->fecha();
                    }

                    # Exibe o titulo
                    p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                    br();

                    p($dados['titulo'], "procedimentoTitulo");
                    p($dados['descricao'], "procedimentoDescricao");
                    br();

                    # Define o arquivo
                    $arquivo = PASTA_PROCEDIMENTOS . $idProcedimento . '.jpg';

                    if (file_exists($arquivo)) {
                        $figura = new Imagem($arquivo, $dados['descricao'], '100%', '100%');
                        $figura->show();
                    } else {
                        br(4);
                        p("Não há conteúdo", "center");
                        br(10);
                    }
                    break;
                case 3: // arquivo pdf
                    br();
                    # Botão de Editar
                    if ($editar) {
                        $divBtn = new Div("editarProcedimento");
                        $divBtn->abre();

                        $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
                        $btnEditar->set_class('button secondary');
                        $btnEditar->set_title('Editar o Procedimento');
                        $btnEditar->show();

                        $divBtn->fecha();
                    }

                    # Exibe o titulo
                    p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                    br();

                    p($dados['titulo'], "procedimentoTitulo");
                    p($dados['descricao'], "procedimentoDescricao");
                    br();

                    # Define o arquivo
                    $arquivo = PASTA_PROCEDIMENTOS . $idProcedimento . '.pdf';

                    if (file_exists($arquivo)) {
                        echo '<iframe src="' . PASTA_PROCEDIMENTOS . $idProcedimento . '.pdf" height="1000px" width="100%" marginwidth ="0" marginheight ="0" style="border:1px solid #d7d7d7;"></iframe>';
                    } else {
                        # Monta o painel
                        $painel = new Callout();
                        $painel->abre();

                        # Exibe o titulo
                        p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                        br();

                        p($dados['titulo'], "procedimentoTitulo");
                        p($dados['descricao'], "procedimentoDescricao");
                        hr("procedimento");

                        br(4);
                        p("Não há conteúdo", "center");
                        br(10);
                        $painel->fecha();
                    }
                    break;
                case 4: // link
                    
                    loadPage($dados['link']);
//                    br();
//                    # Botão de Editar
//                    if ($editar) {
//                        $divBtn = new Div("editarProcedimento");
//                        $divBtn->abre();
//
//                        $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
//                        $btnEditar->set_class('button secondary');
//                        $btnEditar->set_title('Editar o Procedimento');
//                        $btnEditar->show();
//
//                        $divBtn->fecha();
//                    }
//
//                    # Exibe o titulo
//                    p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
//                    br();
//
//                    p($dados['titulo'], "procedimentoTitulo");
//                    p($dados['descricao'], "procedimentoDescricao");
//                    br();
//                    
//                    echo $dados['link'];
//                    
//                    iframe($dados['link']);

                    #echo "<iframe src='{$dados['link']}' height='1000px' width='100%' marginwidth ='0' marginheight ='0' style='border:1px solid #d7d7d7;'></iframe>";
                    break;
                case 5: // rotina
                    br();
                    # Botão de Editar
                    if ($editar) {
                        $divBtn = new Div("editarProcedimento");
                        $divBtn->abre();

                        $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
                        $btnEditar->set_class('button secondary');
                        $btnEditar->set_title('Editar o Procedimento');
                        $btnEditar->show();

                        $divBtn->fecha();
                    }

                    # Exibe o titulo
                    p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                    br();

                    $rotina = new Rotina();
                    $rotina->exibeRotina($dados['idRotina']);
                    break;
            }
            $div->fecha();
        }
        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    public function exibeProcedimentoSubCategoria($subCategoria) {
        /**
         * exibe os procedimentos da subCategoria
         * 
         * @param $id integer null o id
         * 
         * @syntax $rotina->exibeRotina([$id]);  
         */
        # Acessa o banco de dados
        $intra = new Intra();

        # Pega os itens de uma subCategoria
        $row = $this->get_menuTitulos($subCategoria);

        p($subCategoria, "f20", "center");

        if (empty($row)) {
            $grid = new Grid("center");
            $grid->abreColuna(8);
            br(3);

            calloutAlert("Não Existe Informação Cadastrada");

            $grid->fechaColuna();
            $grid->fechaGrid();
        } else {

            # Verifica quantas rotinas existem nesta catagoria
            if (count($row) == 1) {
                $this->exibeProcedimento($row["idProcedimento"]);
            } else {

                foreach ($row as $item) {
                    $label[] = $item['titulo'];
                }

                $tab = new Tab($label);

                foreach ($row as $item) {
                    $tab->abreConteudo();

                    $this->exibeProcedimento($item["idProcedimento"]);

                    $tab->fechaConteudo();
                }
                $tab->show();
                br();
            }
        }
    }

    ###########################################################
}
