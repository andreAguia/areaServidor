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
        
        # Monta o painel
        $painel = new Callout();
        $painel->abre();
        
        titulo("Menu Principal");
        
        # Pega os procedimentos do menu Inicial: idPai = 0
        if(Verifica::acesso($idUsuario,1)){
            $select = 'SELECT idProcedimento,
                              titulo,
                              descricao
                         FROM tbprocedimento
                        WHERE idPai = 0    
                  ORDER BY idPai,numOrdem';
        }else{
            $select = 'SELECT idProcedimento,
                              titulo,
                              descricao
                         FROM tbprocedimento
                        WHERE idPai = 0  
                          AND visibilidade = 1 
                  ORDER BY idPai,umOrdem';
        }
        
        $dados = $intra->select($select);
        $numCategorias = $intra->count($select);
            
        # Verifica se tem Categorias cadastradas
        if($numCategorias > 0){
            
            # Inicia o menu
            $menu1 = new Menu("menuProcedimentos");
            #$menu1->add_item('titulo1','Menu','?fase=menuCaderno');
        
            # Percorre o array 
            foreach ($dados as $valor){
                $texto = $valor[1];

                $menu1->add_item('titulo','<b>'.$texto.'</b>','?fase=exibeProcedimento&idProcedimento='.$valor[0],$valor[2]);
                
                # Verifica se tem filhos
                $filhos = $this->get_filhosProcedimento($valor[0], $idUsuario);
                
                if(!is_null($filhos > 0)){
                    foreach ($filhos as $valorFilhos){
                        
                        if($idProcedimento == $valorFilhos[0]){
                            $menu1->add_item('link','<b>'.$valorFilhos[1].'</b>','?fase=exibeProcedimento&idProcedimento='.$valorFilhos[0],$valorFilhos[2]);
                        }else{
                            $menu1->add_item('link',$valorFilhos[1],'?fase=exibeProcedimento&idProcedimento='.$valorFilhos[0],$valorFilhos[2]);
                        }
                        
                        # Verifica se tem netos
                        $netos = $this->get_filhosProcedimento($valorFilhos[0], $idUsuario);
                        
                        if(!is_null($netos > 0)){
                            
                            foreach ($netos as $valorNetos){
                                if($idProcedimento == $valorNetos[0]){
                                    $menu1->add_item('sublink',"- <strong>".$valorNetos[1].'</strong>','?fase=exibeProcedimento&idProcedimento='.$valorNetos[0],$valorNetos[2]);
                                }else{
                                    $menu1->add_item('sublink',"- ".$valorNetos[1],'?fase=exibeProcedimento&idProcedimento='.$valorNetos[0],$valorNetos[2]);
                                }
                                
                            }
                        }
                    }
                }
            }
            $menu1->show();
        }
        
        # Fecha o painel
        $painel->fecha();
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
    
    function exibeProcedimento($idProcedimento, $idUsuario = NULL){
        
    /**
     * Fornece todos os dados da categoria
     */
        
        # Pega os dados
        $dados = $this->get_dadosProcedimento($idProcedimento);
        $link = $dados["link"];
        $texto = $dados['textoProcedimento'];
        $titulo = $dados['titulo'];
        $descricao = $dados['descricao'];

        # Monta o painel
        $painel = new Callout();
        $painel->abre();

        # Botão de Editar
        if(!vazio($idUsuario)){
            if(Verifica::acesso($idUsuario,1)){
                $divBtn = new Div("editarProcedimento");
                $divBtn->abre();

                $btnEditar = new Link("<i class='fi-pencil'></i>","procedimentoNota.php?fase=editar&id=$idProcedimento");
                $btnEditar->set_class('button secondary');
                $btnEditar->set_title('Editar o Procedimento');
                $btnEditar->show();

                $divBtn->fecha();
            }
        }

        tituloTable($titulo);
        p($descricao,"f10","left");

            # Div onde vai exibir o procedimento
            $div = new Div("divNota");
            $div->abre();

            if(vazio($link)){

                if(vazio($texto)){
                    br(4);
                    p("Não há conteúdo","center");
                    br(4);
                }else{
                    echo $texto;
                }
            }else{
                $figura = new Imagem(PASTA_FIGURAS.$link,$descricao,'100%','100%');
                $figura->show();
            }
            $div->fecha();

        # Fecha o painel
        $painel->fecha();
    }
    
    ###########################################################
}