<?php
class Procedimento{
 /**
  * Abriga as várias rotina do Sistema de Manual de Procedimentos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  * 
  */
    
   ##########################################################
    
    public function menuCategorias($idCategoria = NULL,$idProcedimento = NULL, $idUsuario = NULL){
    /**
     * Exibe o menu de categoria.
     * 
     * @syntax Procedimento::menuCategorias;
     * 
     * @param $idCategoria    integer NULL o id da categoria a ser ressaltado no menu informando que está sendo editada.
     * @param $idProcedimento integer NULL o id do procedimento a ser ressaltado no menu informando que está sendo editada.
     * 
     */    
   
        # Acessa o banco de dados
        $intra = new Intra();
        
        if(Verifica::acesso($idUsuario,1)){
            $select = 'SELECT idCategoria,
                          categoria,
                          descricao
                     FROM tbprocedimentocategoria
                  ORDER BY numOrdem, categoria';
        }else{
            $select = 'SELECT idCategoria,
                          categoria,
                          descricao
                     FROM tbprocedimentocategoria
                     WHERE visibilidade = 1
                  ORDER BY numOrdem, categoria';
        }
        
        $dados = $intra->select($select);
        $numCategorias = $intra->count($select);
        
        # Inicia o menu
        $menu1 = new Menu();
        $menu1->add_item('titulo1','Menu','?fase=menuCaderno');
                
        # Verifica se tem Categorias cadastradas
        if($numCategorias > 0){
            
            # Percorre o array 
            foreach ($dados as $valor){
                #$numProcedimento = $this->get_numeroProcedimentos($valor[0]);
                #$texto = $valor[1]." <span id='numProjeto'>$numProcedimento</span>";
                $texto = $valor[1];

                $menu1->add_item('titulo2',$texto,'?idCategoria='.$valor[0],$valor[2]);
                
                # Pega os procedimentos
                if(Verifica::acesso($idUsuario,1)){
                    $select = 'SELECT idProcedimento,
                                      titulo,
                                      descricao
                                 FROM tbprocedimento
                                WHERE idcategoria = '.$valor[0].' ORDER BY numOrdem,titulo';
                }else{
                    $select = 'SELECT idProcedimento,
                                      titulo,
                                      descricao
                                 FROM tbprocedimento
                                WHERE visibilidade = 1 AND idcategoria = '.$valor[0].' ORDER BY numOrdem,titulo';
                }

                # Acessa o banco
                $procedimentos = $intra->select($select);
                $numProcedimento = $intra->count($select);

                # Percorre as notas 
                foreach($procedimentos as $titulo){
                    if($idProcedimento == $titulo[0]){
                        $menu1->add_item('link',"<b> - ".$titulo[1].'</b>','?fase=exibeProcedimento&idProcedimento='.$titulo[0],$titulo[2]);
                    }else{
                        $menu1->add_item('link',"- ".$titulo[1],'?fase=exibeProcedimento&idProcedimento='.$titulo[0],$titulo[2]);
                    }
                }
            }           
                        
        }
        $menu1->show();
    }

    ###########################################################
    
    public function get_numeroProcedimentos($idCategoria){
    /**
     * Retorna um inteiro com o número de procedimentos de uma categoria
     * 
     * @param $idCategoria integer NULL o idCategoria 
     * 
     * @syntax $procedimento->get_numeroProcedimentos([$idCategoria]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT idProcedimento
                     FROM tbprocedimento
                    WHERE idCategoria = '.$idCategoria;
        
        $intra = new Intra();
        return $intra->count($select);
    }
    
    ###########################################################
    
    function get_dadosCategoria($idCategoria){
        
    /**
     * Fornece todos os dados da categoria
     */
        
        # Pega os dados
        $select="SELECT *
                   FROM tbprocedimentocategoria
                  WHERE idCategoria = $idCategoria";
        
        $intra = new Intra();
        $dados = $intra->select($select,FALSE);
        
        return $dados;
    }
    
    ###########################################################
    
    function get_dadosProcedimento($idProcedimento){
        
    /**
     * Fornece todos os dados da categoria
     */
        
        # Pega os dados
        $select="SELECT *
                   FROM tbprocedimento
                  WHERE idProcedimento = $idProcedimento";
        
        $intra = new Intra();
        $dados = $intra->select($select,FALSE);
        
        return $dados;
    }
    
    ###########################################################
}