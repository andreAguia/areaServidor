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
    
    public function get_Movimento($idMovimento = NULL){
    /**
     * Retorna um array com todas as informações de um movimento específico
     * 
     * @param $idMovimento integer NULL o idMovimento
     * 
     * @syntax $processo->get_Movimento([$idMovimento]);  
     */
    
        # Pega os processos cadastrados
        $select = 'SELECT idProcessoMovimento,
                          idProcesso,
                          status,
                          data,
                          setorCombo,
                          setorTexto,
                          motivo
                     FROM tbprocessomovimento
                     WHERE idProcessoMovimento = '.$idMovimento;
        
        $intra = new Intra();
        $row = $intra->select($select,false);
        return $row;
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
}