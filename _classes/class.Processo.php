<?php
class Processo{
 /**
  * Abriga as várias rotina do Sistema de controle de Processos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  * 
  * @var private $processo        integer NULL O id do processo a ser acessado
  * 
  */
    
    private $processo = NULL;
    
    ###########################################################
    
    /**
    * Método Construtor
    */
    public function __construct(){
        
    }

    ###########################################################
    
    public function get_dadosProcesso($idProcesso = NULL){
    /**
     * Retorna um array com todas as informações do processo informado
     * 
     * @param $idProcesso integer NULL o idProcesso
     * 
     * @syntax $processo->get_dadosProcesso([$idProcesso]);  
     */
    
        # Pega os processos cadastrados
        $select = 'SELECT idProcesso,
                          numero,
                          data,
                          assunto
                     FROM tbprocesso
                     WHERE idProcesso = '.$idProcesso;
        
        $intra = new Intra();
        $row = $intra->select($select);
        return $row;
    }
    
    ###########################################################
    
    public function get_MovimentosProcesso($idProcesso = NULL){
    /**
     * Retorna um array com todas as informações do processo informado
     * 
     * @param $idProcesso integer NULL o idProcesso
     * 
     * @syntax $processo->get_dadosProcesso([$idProcesso]);  
     */
    
        # Pega os processos cadastrados
        $select = 'SELECT idProcesso,
                          numero,
                          data,
                          assunto
                     FROM tbprocesso
                     WHERE idProcesso = '.$idProcesso;
        
        $intra = new Intra();
        $row = $intra->select($select);
        return $row;
    }
    
    ###########################################################
}