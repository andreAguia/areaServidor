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
        $select = "SELECT quem,
                          procedimento,
                          obs
                     FROM tbrotinaitens
                    WHERE idRotina = {$id}
                 ORDER BY numOrdem";

        $intra = new Intra();
        $row = $intra->select($select);

        p($this->get_nomeRotina($id), "rotinaTitulo");
        p($this->get_descricaoRotina($id), "rotinaDescricao");
        br();

        # Monta a tabela
        $tabela = new Tabela(null, "tabelaRotina");
        #$tabela->set_titulo();
        $tabela->set_conteudo($row);
        $tabela->set_label(["Quem", "Procedimento", "Obs"]);
        $tabela->set_width([15, 55, 20]);
        $tabela->set_align(["center", "left", "left"]);
        $tabela->set_totalRegistro(false);
        $tabela->set_numeroOrdem(true);
        $tabela->show();
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
}
