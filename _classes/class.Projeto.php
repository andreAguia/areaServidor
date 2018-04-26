<?php
class Projeto{
 /**
  * Abriga as várias rotina do Sistema de Gestão de Projetos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  * 
  * @var private $projeto        integer NULL O id do projeto a ser acessado
  * 
  */
    
    private $projeto = NULL;
    
    ###########################################################
    
    
    /**
    * Método Construtor
    */
    public function __construct(){
        
    }

    ###########################################################
    
    public function listaProjetosAtivos($idProjeto = NULL){
    /**
     * Retorna um array com todas as informações dos projetos ativos cadastrados
     * 
     * @param $idProjeto integer NULL o idProjeto quando se quer informações de apenas um projeto
     * 
     * @note Quando o idProjeto não é informado será retornado todos os projetos.
     * 
     * @syntax $projeto->listaProjetosAtivos([$idProjeto]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idProjeto,
                          projeto,
                          descricao,
                          grupo
                     FROM tbprojeto
                     WHERE ativo';
                     
        if(!is_null($idProjeto)){
            $select .= ' AND idProjeto = '.$idProjeto;
        };
                
        $select .= ' ORDER BY grupo';
        
        $intra = new Intra();
        
        if(!is_null($idProjeto)){
            $row = $intra->select($select,false);
        }else{
            $row = $intra->select($select);
        }
        return $row;
    }
    
    ###########################################################
    
    public function numeroProjetosAtivos(){
    /**
     * Retorna um inteirocom o número de projetos ativos cadastrados
     * 
     * @param $idLotacao integer NULL o idLotacão da lotação a ser exibida as férias
     * 
     * @note Quando o $idLotacao não é informado será exibido de todas as lotações.
     * 
     * @syntax $ListaFerias->set_lotacao([$idLotacao]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idProjeto,
                          projeto,
                          descricao,
                          grupo
                     FROM tbprojeto
                     WHERE ativo
                     ORDER BY grupo';
        
        $intra = new Intra();
        $numProjetos = $intra->count($select);
        return $numProjetos;
    }
    
    ###########################################################
    
    public function listaTarefas($idProjeto = NULL){
    /**
     * Retorna um array com todas as tarefas do projeto informado
     * 
     * @param $idProjeto integer NULL o idProjeto 
     * 
     * @syntax $projeto->listaTarefas($idProjeto);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idTarefa,
                          tarefa,
                          descricao,
                          idSecao,
                          dataInicial,
                          dataFinal
                     FROM tbprojetotarefa
                     WHERE  idProjeto = '.$idProjeto.' 
                ORDER BY noOrdem';
        
        $intra = new Intra();
        
        $row = $intra->select($select);
        return $row;
    }
    
    ###########################################################
    
    public function numeroTarefas($idProjeto = NULL){
    /**
     * Retorna um inteirocom o número de tarefas de um projeto
     * 
     * @param $idProjeto integer NULL o idProjeto 
     * 
     * @note Quando o $idLotacao não é informado será exibido de todas as lotações.
     * 
     * @syntax $projeto->numeroTarefas($idProjeto);  
     */
    
       # Pega os projetos cadastrados
        $select = 'SELECT idTarefa,
                          tarefa,
                          descricao,
                          idSecao,
                          dataInicial,
                          dataFinal
                     FROM tbprojetotarefa
                     WHERE  idProjeto = '.$idProjeto.' 
                ORDER BY noOrdem';
        
        $intra = new Intra();
        $numTarefas = $intra->count($select);
        return $numTarefas;
    }
    
    ###########################################################
}