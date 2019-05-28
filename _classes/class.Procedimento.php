<?php
class Procedimento{
 /**
  * Abriga as várias rotina do Sistema de Manual de Procedimentos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  * 
  */
    
   ##########################################################
    
    public function menuPrincipal($idProcedimento = NULL, $idUsuario = NULL){
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
        
        # Pega os procedimentos do menu Inicial: idPai = 0
        if(Verifica::acesso($idUsuario,1)){
            $select = 'SELECT idProcedimento,
                              titulo,
                              descricao
                         FROM tbprocedimento
                        WHERE idPai = 0    
                  ORDER BY numOrdem';
        }else{
            $select = 'SELECT idProcedimento,
                              titulo,
                              descricao
                         FROM tbprocedimento
                        WHERE idPai = 0  
                          AND visibilidade = 1 
                  ORDER BY numOrdem';
        }
        
        $dados = $intra->select($select);
        $numCategorias = $intra->count($select);
        
        # Inicia o menu
        $menu1 = new Menu("menuProcedimentos");
        #$menu1->add_item('titulo1','Menu','?fase=menuCaderno');
                
        # Verifica se tem Categorias cadastradas
        if($numCategorias > 0){
            
            # Percorre o array 
            foreach ($dados as $valor){
                $texto = $valor[1];

                $menu1->add_item('titulo','<b>'.$texto.'</b>','?idCategoria='.$valor[0],$valor[2]);
                
                # Verifica se tem filhos
                $filhos = $this->get_filhosProcedimento($valor[0], $idUsuario);
                
                if(!is_null($filhos > 0)){
                    foreach ($filhos as $valorFilhos){
                        
                        if($idProcedimento == $valorFilhos[0]){
                            $menu1->add_item('link','📁 <b>'.$valorFilhos[1].'</b>','?fase=exibeProcedimento&idProcedimento='.$valorFilhos[0],$valorFilhos[2]);
                        }else{
                            $menu1->add_item('link','📁 '.$valorFilhos[1],'?fase=exibeProcedimento&idProcedimento='.$valorFilhos[0],$valorFilhos[2]);
                        }
                        
                        # Verifica se tem netos
                        $netos = $this->get_filhosProcedimento($valorFilhos[0], $idUsuario);
                        
                        if(!is_null($netos > 0)){
                            
                            foreach ($netos as $valorNetos){
                                if($idProcedimento == $valorNetos[0]){
                                    $menu1->add_item('sublink',"📄 <strong>".$valorNetos[1].'</strong>','?fase=exibeProcedimento&idProcedimento='.$valorNetos[0],$valorNetos[2]);
                                }else{
                                    $menu1->add_item('sublink',"📄 ".$valorNetos[1],'?fase=exibeProcedimento&idProcedimento='.$valorNetos[0],$valorNetos[2]);
                                }
                                
                            }
                        }
                    }
                }
            }           
        }
        $menu1->show();
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
    
    function get_filhosProcedimento($idProcedimento, $idUsuario = NULL){
        
    /**
     * Fornece todos os dados da categoria
     */
        
        # Pega os dados
        $select="SELECT *
                   FROM tbprocedimento
                  WHERE idPai = $idProcedimento";
        
        if(!Verifica::acesso($idUsuario,1)){
            $select .= " AND visibilidade = 1";
        }
        
        $intra = new Intra();
        $dados = $intra->select($select);
        
        return $dados;
    }
    
    ###########################################################
}