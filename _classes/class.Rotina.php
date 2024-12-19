<?php

class Rotina {
    /**
     * Abriga as várias rotina do Sistema de Rotinas
     * 
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     */
    ###########################################################

    /**
     * Método Construtor
     */
    public function __construct() {
        
    }

    ###########################################################

    public function get_nomeRotina($id = null) {
        /**
         * Retorna o nome da rotina
         * 
         * @param $id integer null o id
         * 
         * @syntax $rotina->get_nomeRotina([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT nome
                     FROM tbrotina
                     WHERE idRotina = {$id}";

        $intra = new Intra();
        $row = $intra->select($select, false);

        if (empty($row)) {
            return null;
        } else {
            return $row[0];
        }
    }

    ###########################################################

    public function get_descricaoRotina($id = null) {
        /**
         * Retorna o nome da rotina
         * 
         * @param $id integer null o id
         * 
         * @syntax $rotina->get_nomeRotina([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT descricao
                     FROM tbrotina
                     WHERE idRotina = {$id}";

        $intra = new Intra();
        $row = $intra->select($select, false);

        if (empty($row)) {
            return null;
        } else {
            return $row[0];
        }
    }

    ###########################################################

    public function get_categoriaRotina($id = null) {
        /**
         * Retorna o nome da rotina
         * 
         * @param $id integer null o id
         * 
         * @syntax $rotina->get_nomeRotina([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT categoria
                     FROM tbrotina
                     WHERE idRotina = {$id}";

        $intra = new Intra();
        $row = $intra->select($select, false);

        if (empty($row)) {
            return null;
        } else {
            return $row[0];
        }
    }

    ###########################################################

    public function get_numItens($id = null) {
        /**
         * Retorna o número de itens de uma rotina
         * 
         * @param $id integer null o id
         * 
         * @syntax $rotina->get_numItens([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT idRotinaItens
                     FROM tbrotinaitens
                    WHERE idRotina = {$id}";

        $intra = new Intra();
        return $intra->count($select);
    }

    ###########################################################

    public function exibeRotina($id = null) {
        /**
         * exibe tabela com rotina
         * 
         * @param $id integer null o id
         * 
         * @syntax $rotina->exibeRotina([$id]);  
         */
        # Pega os dados
        $select = "SELECT marcador,
                          quem,
                          procedimento,
                          obs
                     FROM tbrotinaitens
                    WHERE idRotina = {$id}
                 ORDER BY numOrdem";

        $intra = new Intra();
        $row = $intra->select($select);

        if (empty($row)) {
            $grid = new Grid("center");
            $grid->abreColuna(8);
            br(3);

            calloutAlert("Não Existe Informação Cadastrada");

            $grid->fechaColuna();
            $grid->fechaGrid();
        } else {

            p("{$this->get_categoriaRotina($id)} - {$this->get_nomeRotina($id)}", "rotinaTitulo");
            p($this->get_descricaoRotina($id), "rotinaDescricao");
            br();

            # Monta a tabela
            $tabela = new Tabela(null, "tabelaRotina");
            #$tabela->set_titulo();
            $tabela->set_conteudo($row);
            $tabela->set_label(["Marcador", "Quem", "Procedimento", "Obs"]);
            $tabela->set_width([10, 15, 45, 20]);
            $tabela->set_align(["center", "center", "left", "left"]);
            $tabela->set_funcao(["bold"]);
            $tabela->set_totalRegistro(false);
            $tabela->set_numeroOrdem(true);
            $tabela->show();
        }
    }

    ###########################################################

    public function get_ultimoNumOrdem($id = null) {
        /**
         * Retorna o nome da rotina
         * 
         * @param $id integer null o id
         * 
         * @syntax $rotina->get_nomeRotina([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT numOrdem
                     FROM tbrotinaitens
                     WHERE idRotina = {$id}
                    ORDER BY numOrdem DESC LIMIT 1";

        $intra = new Intra();
        $row = $intra->select($select, false);

        if (empty($row)) {
            return 0;
        } else {
            return $row[0];
        }
    }

    ###########################################################

    public function get_ultimoQuem($id = null) {
        /**
         * Retorna o nome da rotina
         * 
         * @param $id integer null o id
         * 
         * @syntax $rotina->get_nomeRotina([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT quem
                     FROM tbrotinaitens
                     WHERE idRotina = {$id}
                    ORDER BY numOrdem DESC LIMIT 1";

        $intra = new Intra();
        $row = $intra->select($select, false);

        if (empty($row)) {
            return null;
        } else {
            return $row[0];
        }
    }

    ###########################################################

    public function exibeDadosRotinaPrincipal($id = null) {

        p($this->get_categoriaRotina($id), "rotinaTitulo");
        p($this->get_nomeRotina($id), "rotinaTitulo");
        p($this->get_descricaoRotina($id), "rotinaDescricao");
    }

    ###########################################################

    public function exibeRotinaCategoria($categoria = null) {
        /**
         * exibe tabela com rotina
         * 
         * @param $id integer null o id
         * 
         * @syntax $rotina->exibeRotina([$id]);  
         */
        # Acessa o banco de dados
        $intra = new Intra();

        # Pega as rotinas desta categoria
        $select = "SELECT idRotina,
                          nome,
                          descricao
                     FROM tbrotina
                    WHERE categoria = '{$categoria}'";

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
                $this->exibeRotina($row1[0][0]);
            } else {

                foreach ($row1 as $item) {
                    $label[] = $item['nome'];
                }

                $tab = new Tab($label);

                foreach ($row1 as $item) {
                    $tab->abreConteudo();

                    $this->exibeRotina($item[0]);

                    $tab->fechaConteudo();
                }
                $tab->show();
                br();
            }
        }
    }

    ###########################################################
}
