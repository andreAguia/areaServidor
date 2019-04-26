<?php
class Procedimento{
 /**
  * Abriga as várias rotina do Sistema de Manual de Procedimentos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  * 
  */
    
   ##########################################################
    
    public function menuCategorias($idCategoria = NULL,$idProcedimento = NULL){
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
        
        # Pega as categorias cadastradas
        $select = 'SELECT idCategoria,
                          categoria,
                          descricao
                     FROM tbprocedimentoCategoria
                  ORDER BY numOrdem, categoria';
        
        $dados = $intra->select($select);
        $numCategorias = $intra->count($select);
        
        # Inicia o menu
        $menu1 = new Menu();
        $menu1->add_item('titulo1','Categorias','?fase=menuCaderno');
        $menu1->add_item('sublink','+ Nova Categoria','?fase=cadernoNovo');
                
        # Verifica se tem Categorias cadastradas
        if($numCategorias > 0){
            
            # Percorre o array 
            foreach ($dados as $valor){
                $numProcedimento = $this->get_numeroProcedimentos($valor[0]);
                $texto = $valor[1]." <span id='numProjeto'>$numProcedimento</span>";                

                # Marca o item que está sendo editado
                if($idCategoria == $valor[0]){
                    $menu1->add_item('titulo2',"<b> ".$texto."</b>",'?fase=dadosCaderno&idCaderno='.$valor[0],$valor[2]);

                    # Pega os procedimentos
                    $select = 'SELECT idProcedimento,
                                      titulo,
                                      descricao
                                 FROM tbprocedimento
                                WHERE idcategoria = '.$valor[0].' ORDER BY numOrdem,titulo';

                    # Acessa o banco
                    $procedimentos = $intra->select($select);
                    $numProcedimento = $intra->count($select);

                    # Percorre as notas 
                    foreach($procedimentos as $titulo){
                        if($idProcedimento == $titulo[0]){
                            $menu1->add_item('link',"<b> -".$titulo.'</b>','?fase=categoria&idProcedimento='.$titulo[0],$titulo[2]);
                        }else{
                            $menu1->add_item('link',"- ".$titulo[1],'?fase=categoria&idProcedimento='.$titulo[0],$titulo[2]);
                        }
                    }
                    
                    # Incluir nota
                    $menu1->add_item('sublink','+ Novo Procedimento','?fase=notaNova');
                }else{
                    $menu1->add_item('titulo2',$texto,'?fase=dadosCategoria&idCategoria='.$valor[0],$valor[2]);
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
}