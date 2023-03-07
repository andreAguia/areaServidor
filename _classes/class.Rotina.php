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
        return $row[0];
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
        $select = "SELECT numOrdem,
                          quem,
                          procedimento,
                          obs
                     FROM tbrotinaitens
                    WHERE idRotina = {$id}
                 ORDER BY numOrdem";

        $intra = new Intra();
        $row = $intra->select($select);

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_titulo($this->get_nomeRotina($id));
        $tabela->set_conteudo($row);
        $tabela->set_label(["#", "Quem", "Procedimento", "Obs"]);
        $tabela->set_width([5, 15, 35, 35]);
        $tabela->set_align(["center", "center", "left", "left"]);
        $tabela->set_totalRegistro(false);
        #$tabela->set_numeroOrdem(true);
        $tabela->show();
    }

    ###########################################################
}
