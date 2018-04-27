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
    
    public function get_dadosTarefas($idTarefa){
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
                          idSecao,
                          dataInicial,
                          dataFinal,
                          feito,
                          idEtiqueta
                     FROM tbprojetotarefa
                     WHERE idTarefa = '.$idTarefa.' 
                ORDER BY noOrdem';
        
        $intra = new Intra();
        
        $row = $intra->select($select,false);
        return $row;
    }
    
    ###########################################################
    
    public function exibeTarefas($idProjeto = NULL, $feito = FALSE){
    /**
     * Retorna uma lista das terefas do projeto informado
     * 
     * @param $idProjeto integer NULL o idProjeto 
     * 
     * @syntax $projeto->exibeTarefas($idProjeto);  
     */
    
        # Pega as tarefas
        $select = 'SELECT idTarefa,
                          tarefa,
                          descricao,
                          idSecao,
                          dataInicial,
                          dataFinal,
                          feito,
                          idEtiqueta
                     FROM tbprojetotarefa
                     WHERE idProjeto = '.$idProjeto;
        
        if($feito){
            $select.= ' AND feito';
        }else{
            $select.= ' AND NOT feito';
        }
        
        $select .=' ORDER BY feito, noOrdem';
        
        $intra = new Intra();
        
        $tarefas = $intra->select($select);
        $numeroTarefas = $intra->count($select);
            
        echo '<ul id="projetosTarefas">';
            
        # Se existir alguma tarefa percorre
        # as tarefas e monta a lista
        if($numeroTarefas>0){
            
            if($feito){
                p("Tarefas Completadas","f14");
            }
            
            # Percorre o array e preenche o $return
            foreach ($tarefas as $valor) {
                $div = new Div("divTarefas");
                $div->abre();
                $grid = new Grid();
                
                # Ticked
                $grid->abreColuna(1);

                    $botao = new BotaoGrafico();
                    $botao->set_url('?fase=mudaTarefa&idTarefa='.$valor[0].'&idProjeto='.$idProjeto);

                    if($valor[6] == 1){
                        $botao->set_image(PASTA_FIGURAS.'tickCheio.png',15,15);
                    }else{
                        $botao->set_image(PASTA_FIGURAS.'tickVazio.png',15,15);
                    }
                    $botao->show();

                $grid->fechaColuna();
                
                # Tarefa
                $grid->abreColuna(6);
                    echo "<li>$valor[1]</li>";
                $grid->fechaColuna();
                
                # Datas Inicial
                $grid->abreColuna(2);
                    $dataInicial = date_to_php($valor[4]);
                    $dataFinal = date_to_php($valor[5]);
                    
                    echo "<li id='projetoDataInicial'>".formataDataTarefa($dataInicial,$dataFinal)."</li>";
                    #echo "<li id='projetoDataInicial'>".$dataInicial.'-'.$dataFinal."</li>";
                    
                $grid->fechaColuna();
                
                # Etiqueta
                $grid->abreColuna(2);
                    if(!vazio($valor[7])){
                        echo "<li>".label($valor[7])."</li>";
                    }
                $grid->fechaColuna();
                
                # Editar
                $grid->abreColuna(1);
                    $botao = new BotaoGrafico();
                    $botao->set_url('?fase=tarefaNova&idTarefa='.$valor[0].'&idProjeto='.$idProjeto);
                    $botao->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',15,15);
                    $botao->show();
                $grid->fechaColuna();
                $grid->fechaGrid(); 
                
                $div->fecha();
                
                hr("projetosTarefas");   
            }
        }
        echo '</ul>';
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
    
        # Pega os projetos cadastrados
        $select = 'SELECT projeto
                     FROM tbprojeto
                     WHERE idProjeto = '.$idProjeto;
        
        $intra = new Intra();
        $row = $intra->select($select,false);
        return $row[0];
    }
    
    ###########################################################
    
    public function listaEtiquetas($idProjeto = NULL){
    /**
     * Retorna um array com todas as etiquetas cadastrados
     * 
     * @param $idProjeto integer NULL o idProjeto quando se quer informações de apenas um projeto
     * 
     * @note Quando o idProjeto não é informado será retornado todos os projetos.
     * 
     * @syntax $projeto->listaEtiquetas([$idProjeto]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idEtiqueta,
                          etiqueta
                     FROM tbprojetoetiqueta
                     ORDER BY etiqueta';
        
        $intra = new Intra();
        $row = $intra->select($select);
        return $row;
    }
    
    ###########################################################
    
    public function numeroEtiquetas($idProjeto = NULL){
    /**
     * Retorna o número de etiquetas cadastradas
     * 
     * @param $idProjeto integer NULL o idProjeto quando se quer informações de apenas um projeto
     * 
     * @note Quando o idProjeto não é informado será retornado todos os projetos.
     * 
     * @syntax $projeto->numeroEtiquetas([$idProjeto]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idEtiqueta,
                          etiqueta
                     FROM tbprojetoetiqueta
                     ORDER BY etiqueta';
        
        $intra = new Intra();
        $row = $intra->count($select);
        return $row;
    }
    
    ###########################################################
}