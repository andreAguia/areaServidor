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
    
    public function get_MovimentoSetor($idMovimento = NULL){
    /**
     * Retorna o setor desse movimento
     * 
     * @param $idMovimento integer NULL o idMovimento
     * 
     * @syntax $processo->get_MovimentoSetor([$idMovimento]);  
     */
    
        # Pega os processos cadastrados
        $select = 'SELECT setorCombo,
                          setorTexto
                     FROM tbprocessomovimento
                     WHERE idProcessoMovimento = '.$idMovimento;
        
        $intra = new Intra();
        $row = $intra->select($select,FALSE);
        
        # Verifica qual campo foi preenchido
        # Se for setor interno, pega o nome e retorna 
        if((!vazio($row[0]) AND ($row[0] <> 0))){
            $pessoal = new Pessoal();
            $retorno = $pessoal->get_nomeLotacao($row[0]);
        }else{
            $retorno = $row[1];
        }
        
        return $retorno;
    }
    
    ###########################################################
    
    public function get_numMovimentos($idProcesso){
    /**
     * Retorna o número de movimentos de um processo
     * 
     * @param $idProcesso integer NULL o idProcesso
     * 
     * @syntax $processo->get_numMovimentos($idProcesso);  
     */
    
        # Pega os processos cadastrados
        $select = 'SELECT idProcessoMovimento
                     FROM tbprocessomovimento
                     WHERE idProcesso = '.$idProcesso;
        
        $intra = new Intra();
        $row = $intra->count($select);
        
        return $row;
    }
}