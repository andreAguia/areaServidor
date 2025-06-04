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
}
