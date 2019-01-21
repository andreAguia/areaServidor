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
    
    public function get_dadosProjeto($idProjeto = NULL){
    /**
     * Retorna um array com todas as informações do projeto informado
     * 
     * @param $idProjeto integer NULL o idProjeto
     * 
     * @syntax $projeto->getDadosProjetos([$idProjeto]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idProjeto,
                          projeto,
                          descricao,
                          grupo,
                          cor,
                          numOrdem
                     FROM tbprojeto
                     WHERE idProjeto = '.$idProjeto;
        
        $intra = new Intra();
        $row = $intra->select($select,false);
        return $row;
    }
    
    ###########################################################
    
    public function get_dadosCaderno($idCaderno = NULL){
    /**
     * Retorna um array com todas as informações do caderno informado
     * 
     * @param $idCaderno integer NULL o idCaderno
     * 
     * @syntax $projeto->get_dadosCaderno([$idCaderno]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idCaderno,
                          caderno,
                          descricao
                     FROM tbprojetocaderno
                     WHERE idCaderno = '.$idCaderno;
        
        $intra = new Intra();
        $row = $intra->select($select,false);
        return $row;
    }
    
    ###########################################################
    
    public function get_dadosNota($idNota = NULL){
    /**
     * Retorna um array com as informações da nota
     * 
     * @param $idNota integer NULL o idNota
     * 
     * @syntax $projeto->get_dadosNota([$idNota]);  
     */
    
        # Pega as notas
        $select = 'SELECT idNota,
                          idCaderno,
                          titulo,
                          nota,
                          numOrdem
                     FROM tbprojetonota
                     WHERE idNota = '.$idNota;
        
        $intra = new Intra();
        $row = $intra->select($select,false);
        return $row;
    }
    
    ###########################################################
    
    public function get_numeroTarefasPendentes($idProjeto){
    /**
     * Retorna um inteiro com o número de tarefas pendentes de um projeto
     * 
     * @param $idProjeto integer NULL o idProjeto 
     * 
     * @note usado no menu de projetos ativos informando o número de tarefas no menu
     * 
     * @syntax $projeto->get_numeroTarefasPendentes([$idProjeto]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idTarefa
                     FROM tbprojetotarefa
                    WHERE pendente AND idProjeto = '.$idProjeto;
        
        $intra = new Intra();
        $numTarefas = $intra->count($select);
        return $numTarefas;
    }
    
    ###########################################################
    
    public function get_numeroTarefasConcluidas($idProjeto){
    /**
     * Retorna um inteiro com o número de tarefas concluídas de um projeto
     * 
     * @param $idProjeto integer NULL o idProjeto 
     * 
     * @note usado no menu de projetos ativos informando o número de tarefas no menu
     * 
     * @syntax $projeto->get_numeroTarefasConcluidas([$idProjeto]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idTarefa
                     FROM tbprojetotarefa
                    WHERE NOT pendente AND idProjeto = '.$idProjeto;
        
        $intra = new Intra();
        $numTarefas = $intra->count($select);
        return $numTarefas;
    }
    
    ###########################################################
    
    public function get_numeroTarefasEtiqueta($etiqueta){
    /**
     * Retorna um inteiro com o número de tarefas pendentes de uma Etiqueta
     * 
     * @param $etiqueta integer NULL o etiqueta 
     * 
     * @note usado no menu de etiquetas informando o número de tarefas no menu
     * 
     * @syntax $projeto->get_numeroTarefasEtiqueta([$etiqueta]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idTarefa
                     FROM tbprojetotarefa
                    WHERE pendente AND etiqueta = "'.$etiqueta.'"';
        
        $intra = new Intra();
        $numTarefas = $intra->count($select);
        return $numTarefas;
    }
    
     ###########################################################
    
    public function get_numeroTarefasSolitante($solicitante){
    /**
     * Retorna um inteiro com o número de tarefas pendentes de uma Etiqueta
     * 
     * @param $etiqueta integer NULL o etiqueta 
     * 
     * @note usado no menu de etiquetas informando o número de tarefas no menu
     * 
     * @syntax $projeto->get_numeroTarefasEtiqueta([$etiqueta]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idTarefa
                     FROM tbprojetotarefa
                    WHERE pendente AND solicitante = "'.$solicitante.'"';
        
        $intra = new Intra();
        $numTarefas = $intra->count($select);
        return $numTarefas;
    }
    
    ###########################################################
    
    public function get_dadosTarefa($idTarefa){
    /**
     * Retorna um array com todos os dados de uma tarefa específica
     * 
     * @param $idTarefa integer NULL o idTarefa 
     * 
     * @syntax $projeto->get_dadosTarefas($idTarefa);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idTarefa,
                          tarefa,
                          descricao,
                          noOrdem,
                          dataInicial,
                          dataFinal,
                          pendente,
                          etiqueta,
                          idProjeto,
                          conclusao,
                          status,
                          solicitante
                     FROM tbprojetotarefa
                     WHERE idTarefa = '.$idTarefa.' 
                ORDER BY noOrdem';
        
        $intra = new Intra();
        
        $row = $intra->select($select,false);
        return $row;
    }
    
    ###########################################################
    
    public function get_nomeProjeto($idProjeto){
    /**
     * Retorna o nome do projeto informado
     * 
     * @param $idProjeto integer NULL o idProjeto
     * 
     * @syntax $projeto->get_nomeProjeto([$idProjeto]);  
     */
        
        if(is_null($idProjeto)){
           return NULL; 
        }else{
            # Pega os projetos cadastrados
            $select = 'SELECT projeto
                         FROM tbprojeto
                        WHERE idProjeto = '.$idProjeto;

            $intra = new Intra();
            $row = $intra->select($select,FALSE);
            return $row[0];        
        }
    }
           
    ###########################################################
    
    public function get_nomeEtiqueta($etiqueta){
    /**
     * Retorna o nome da etiqueta informado
     * 
     * @param $etiqueta integer NULL a $etiqueta
     * 
     * @syntax $projeto->get_nomeEtiqueta([$etiqueta]);  
     */
    
        if(is_null($etiqueta)){
           return NULL; 
        }else{
            # Pega os projetos cadastrados
            $select = 'SELECT etiqueta
                         FROM tbprojetoetiqueta
                        WHERE etiqueta = '.$etiqueta;

            $intra = new Intra();
            $row = $intra->select($select,FALSE);
            return $row[0];
        }
    }
           
    ###########################################################
    
    public function get_corProjeto($idProjeto){
    /**
     * Retorna a cor do projeto informado
     * 
     * @param $idProjeto integer NULL o idProjeto
     * 
     * @syntax $projeto->get_corProjeto([$idProjeto]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT cor
                     FROM tbprojeto
                     WHERE idProjeto = '.$idProjeto;
        
        $intra = new Intra();
        $row = $intra->select($select,false);
        return $row[0];
    }
           
    ###########################################################
    
    public function get_numeroNotas($idCaderno){
    /**
     * Retorna um inteiro com o número de notas de um caderno
     * 
     * @param $idCaderno integer NULL o idCaderno 
     * 
     * @syntax $projeto->get_numeroNotas([$idCaderno]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idNota
                     FROM tbprojetonota
                    WHERE idCaderno = '.$idCaderno;
        
        $intra = new Intra();
        $numNotas = $intra->count($select);
        return $numNotas;
    }
    
    ###########################################################
}