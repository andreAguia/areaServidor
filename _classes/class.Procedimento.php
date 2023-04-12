<?php

class Procedimento {

    /**
     * Abriga as várias rotina do Sistema de Manual de Procedimentos
     *
     * @author André Águia (Alat) - alataguia@gmail.com
     *
     */
    ##########################################################

    public function menuPrincipal($idProcedimento = null, $idUsuario = null) {
        /**
         * Exibe o menu de categoria.
         *
         * @syntax Procedimento::menuCategorias;
         *
         * @param $idCategoria    integer null o id da categoria a ser ressaltado no menu informando que está sendo editada.
         * @param $idProcedimento integer null o id do procedimento a ser ressaltado no menu informando que está sendo editada.
         *
         */
        # Acessa o banco de dados
        $intra = new Intra();

        # Pega os procedimentos do menu Inicial: idPai = 0
        if (Verifica::acesso($idUsuario, 1)) {
            $select = 'SELECT idProcedimento,
                              titulo,
                              descricao
                         FROM tbprocedimento
                        WHERE idPai = 0
                  ORDER BY idPai, numOrdem';
        } else {
            $select = 'SELECT idProcedimento,
                              titulo,
                              descricao
                         FROM tbprocedimento
                        WHERE idPai = 0
                          AND visibilidade = 1
                  ORDER BY idPai, numOrdem';
        }

        $dados = $intra->select($select);
        $numCategorias = $intra->count($select);

        # Verifica se tem Categorias cadastradas
        if ($numCategorias > 0) {

            # Inicia o menu           
            $menu1 = new Menu("menuProcedimentos");            

            # Percorre o array
            foreach ($dados as $valor) {
                $texto = $valor[1];

                $menu1->add_item('titulo', '<b>' . $texto . '</b>', '?fase=exibeProcedimento&idProcedimento=' . $valor[0], $valor[2]);

                # Verifica se tem filhos
                $filhos = $this->get_filhosProcedimento($valor[0], $idUsuario);

                if (!is_null($filhos > 0)) {
                    foreach ($filhos as $valorFilhos) {

                        if ($idProcedimento == $valorFilhos[0]) {
                            $menu1->add_item('link', '<b>' . $valorFilhos[1] . '</b>', '?fase=exibeProcedimento&idProcedimento=' . $valorFilhos[0], $valorFilhos[2]);
                        } else {
                            $menu1->add_item('link', $valorFilhos[1], '?fase=exibeProcedimento&idProcedimento=' . $valorFilhos[0], $valorFilhos[2]);
                        }

                        # Verifica se tem netos
                        $netos = $this->get_filhosProcedimento($valorFilhos[0], $idUsuario);

                        if (!is_null($netos > 0)) {

                            foreach ($netos as $valorNetos) {
                                if ($idProcedimento == $valorNetos[0]) {
                                    $menu1->add_item('sublink', "<strong>" . $valorNetos[1] . '</strong>', '?fase=exibeProcedimento&idProcedimento=' . $valorNetos[0], $valorNetos[2]);
                                } else {
                                    $menu1->add_item('sublink', $valorNetos[1], '?fase=exibeProcedimento&idProcedimento=' . $valorNetos[0], $valorNetos[2]);
                                }
                            }
                        }
                    }
                }
            }
            $menu1->show();
        }
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

        $dados = $intra->select($select, false);
        return $dados;
    }

    ###########################################################

    function get_idProcedimento($titulo) {

        /**
         * Fornece o id de um titulo
         */
        $intra = new Intra();

        # Pega os dados
        $select = "SELECT idProcedimento
                     FROM tbprocedimento
                    WHERE titulo = '{$titulo}'";

        $dados = $intra->select($select, false);
        return $dados['idProcedimento'];
    }

    ###########################################################

    function get_filhosProcedimento($idProcedimento, $idUsuario = null) {

        /**
         * Fornece todos os dados da categoria
         */
        # Pega os dados
        $select = "SELECT *
                   FROM tbprocedimento
                  WHERE idPai = $idProcedimento
                  ORDER BY numOrdem";

        if (!Verifica::acesso($idUsuario, 1)) {
            $select .= " AND visibilidade = 1";
        }

        $intra = new Intra();
        $dados = $intra->select($select);

        return $dados;
    }

    ###########################################################

    function exibeProcedimento($idProcedimento, $idUsuario = null) {

        /**
         * Fornece todos os dados da categoria
         */
        # Pega os dados
        $dados = $this->get_dadosProcedimento($idProcedimento);

        $grid = new Grid();
        $grid->abreColuna(12);

        if (!empty($dados)) {

            $link = $dados["link"];
            $texto = $dados['textoProcedimento'];
            $titulo = $dados['titulo'];
            $descricao = $dados['descricao'];
            $idPai = $dados['idPai'];

            # Dados do Pai
            $dadosPai = $this->get_dadosProcedimento($idPai);
            if (!empty($dadosPai['titulo'])) {
                $pai = $dadosPai['titulo'];
            }

            # Monta o painel
            $painel = new Callout();
            $painel->abre();

            # Botão de Editar
            if (!empty($idUsuario)) {
                if (Verifica::acesso($idUsuario, 1)) {
                    $divBtn = new Div("editarProcedimento");
                    $divBtn->abre();

                    $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
                    $btnEditar->set_class('button secondary');
                    $btnEditar->set_title('Editar o Procedimento');
                    $btnEditar->show();

                    $divBtn->fecha();
                }
            }

            # Exibe o titulo do pai (quando houver)
            if (!empty($pai)) {
                p($pai, "procedimentoPai");
            }

            p($titulo, "procedimentoTitulo");
            p($descricao, "procedimentoDescricao");
            hr("procedimento");

            # Div onde vai exibir o procedimento
            $div = new Div("divNota");
            $div->abre();

            if (vazio($link)) {

                if (vazio($texto)) {
                    br(4);
                    p("Não há conteúdo", "center");
                    br(4);
                } else {
                    echo $texto;
                }
            } else {
                $figura = new Imagem(PASTA_FIGURAS . $link, $descricao, '100%', '100%');
                $figura->show();
            }
            $div->fecha();

            # Fecha o painel
            $painel->fecha();
        } else {
            br(5);
            p("Não há dados para serem exibidos", "center");
        }

        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    public function exibeProcedimentoFilhos($pai = null) {
        /**
         * exibe tabela com rotina
         * 
         * @param $id integer null o id
         * 
         * @syntax $rotina->exibeRotina([$id]);  
         */
        # Acessa o banco de dados
        $intra = new Intra();
        
        # Pega o idPai de um pai
        $idPai = $this->get_idProcedimento($pai);

        # Pega as rotinas desta categoria
        $select = "SELECT idProcedimento,
                          titulo,
                          descricao
                     FROM tbprocedimento
                    WHERE idPai = '{$idPai}'
                 ORDER BY numOrdem";

        $row1 = $intra->select($select);

        if (empty($row1)) {
            $grid = new Grid("center");
            $grid->abreColuna(8);
            br(3);

            calloutAlert("Não Existe Informação Cadastrada");

            $grid->fechaColuna();
            $grid->fechaGrid();
        } else {

            # Verifica quantas rotinas existem nesta catagoria
            if ($intra->count($select) == 1) {
                $this->exibeProcedimento($row1[0][0]);
            } else {

                foreach ($row1 as $item) {
                    $label[] = $item['titulo'];
                }

                $tab = new Tab($label);

                foreach ($row1 as $item) {
                    $tab->abreConteudo();

                    $this->exibeProcedimento($item[0]);

                    $tab->fechaConteudo();
                }
                $tab->show();
                br();
            }
        }
    }

    ###########################################################
}
