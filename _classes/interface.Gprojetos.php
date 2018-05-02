<?php

class Gprojetos{
 /**
  * Encapsula as rotinas da interface com o usuário do sistema de Gestão de Projetos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  */
     
##########################################################
    
    public static function menuProjetosAtivos($idProjeto = NULL){
    /**
    * Exibe o menu de projetos ativos.
    * 
    * @syntax Gprojetos::Gprojetos;
    * 
    * @param $idProjeto integer NULL o id do projeto a sser ressaltado no menu informando que stá sendo editado.
    */    
   
        # Pega os projetos cadastrados
        $select = 'SELECT idProjeto,
                          projeto,
                          descricao,
                          grupo
                     FROM tbprojeto
                     WHERE ativo
                  ORDER BY projeto';
        
        # Acessa o banco de dados
        $projeto = new Projeto();
        $intra = new Intra();
        $dadosProjetos = $intra->select($select);
        $numProjetos = $intra->count($select);
        
        # Verifica se tem projetos
        if($numProjetos>0){
            # Inicia o menu
            $menu1 = new Menu();
            $menu1->add_item('titulo','Projetos Ativos','?','Cartões de Projetos');
        
            # Percorre o array 
            foreach ($dadosProjetos as $valor){
                $numTarefa = $projeto->get_numeroTarefasPendentes($valor[0]);
                $texto = $valor[1]." ($numTarefa)";
                
                # Marca o item que está sendo editado
                if($idProjeto == $valor[0]){
                    $texto = ">".$texto;
                }
                $menu1->add_item('link',$texto,'?fase=projeto&idProjeto='.$valor[0],$valor[2]);
             }
             
             $menu1->show();
        }

        $menu2 = new Menu();
        $menu2->add_item('link','+ Novo Projeto','?fase=projetoNovo');
        $menu2->show();
    }

    ##########################################################
    
    public static function menuEtiquetas($idEtiqueta = NULL){
    /**
    * Exibe o menu de projetos ativos.
    * 
    * @syntax Gprojetos::Gprojetos;
    * 
    * @param $idProjeto integer NULL o id do projeto a sser ressaltado no menu informando que stá sendo editado.
    */    
   
        # Pega os projetos cadastrados
        $select = 'SELECT idEtiqueta,
                          etiqueta,
                          cor
                     FROM tbprojetoetiqueta
                     ORDER BY etiqueta';
        
        # Acessa o banco de dados
        $projeto = new Projeto();
        $intra = new Intra();
        $dadosEtiquetas = $intra->select($select);
        $numEtiquetas = $intra->count($select);

        # Verifica se tem etiquetas
        if($numEtiquetas>0){
            # Inicia o menu
            $menu1 = new Menu();
            $menu1->add_item('titulo','Etiquetas');
            
            # Percorre o array 
            foreach ($dadosEtiquetas as $valor) {
                $numTarefa = $projeto->get_numeroTarefasEtiqueta($valor[0]);
                $texto = $valor[1]." ($numTarefa)";

                # Marca o item que está sendo editado
                if($idEtiqueta == $valor[0]){
                    $texto = ">".$texto;
                }

                $menu1->add_item('link',$texto,'?fase=projetoEtiqueta&idEtiqueta='.$valor[0],'Exibe as tarefas com a etiqueta '.$valor[1]);
            }

            $menu1->show();
        }

        $menu2 = new Menu();
        $menu2->add_item('link','+ Nova Etiqueta','?fase=etiquetaNova');
        $menu2->show();
    }

    ##########################################################
    
    public static function cartoesProjetosAtivos($grupo = NULL){
    /**
    * Exibe o os projetos ativo em forma de cartões
    * 
    * @syntax Gprojetos::Gprojetos;
    * 
    * @param $idProjeto integer NULL o id do projeto a sser ressaltado no menu informando que stá sendo editado.
    */    
   
        # Pega os projetos cadastrados
        $select = 'SELECT idProjeto,
                          projeto,
                          descricao,
                          grupo,
                          cor
                     FROM tbprojeto
                     WHERE ativo';
        
        if(!is_null($grupo)){
            $select .= ' AND grupo = "'.$grupo.'"';
        }
        
        $select .= ' ORDER BY grupo,projeto';
        
        # Acessa o banco de dados
        $projeto = new Projeto();
        $intra = new Intra();
        $dadosProjetos = $intra->select($select);
        $numProjetos = $intra->count($select);
        
        # Título
        tituloTable("Projetos Ativos");
        br();
        
        # Exibe menu de grupos
        $select2 = "SELECT DISTINCT grupo
                     FROM tbprojeto
                     WHERE ativo
                     ORDER BY grupo";
        
        $dadosGrupos = $intra->select($select2);
        $numGrupos = $intra->count($select2);
        
        if($numGrupos>0){
            # Inicia o grid
            echo '<nav aria-label="You are here:" role="navigation">';
            echo '<ul class="breadcrumbs">';
            
            echo '<li>';
            $link = new Link("Todos","?");
            $link->set_title("Exibe todos os Projetos ativos");
            $link->show();
            echo '</li>';
            
            # Percorre o array 
            foreach ($dadosGrupos as $grupoValor){
               
                echo '<li>';
                $link = new Link($grupoValor[0],"?grupo=".$grupoValor[0]);
                $link->set_title("Exibe os Projetos ativos do grupo ".$grupoValor[0]);
                $link->show();
                echo '</li>';
                
            }

            echo '</ul>';
            echo '</nav>';
        }
        
        # Verifica se tem projetos
        if($numProjetos>0){
            # Inicia o grid
            $grid = new Grid();
            
            # Percorre o array 
            foreach ($dadosProjetos as $valor){
                
                $grid->abreColuna(4);
                $card = new Callout($valor[4],"card");
                $card->abre();
                    $grid = new Grid();
                    $grid->abreColuna(10);
                        p($valor[1],"pCardNomeProjeto");             // Nome do projeto
                        p(strtoupper($valor[3]),"pCardNomeGrupo");   // Grupo
                    $grid->fechaColuna();
                    $grid->abreColuna(2);
                        $botao = new BotaoGrafico();
                        $botao->set_url('?fase=projetoNovo&idProjeto='.$valor[0]);
                        $botao->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',15,15);
                        $botao->show();
                    $grid->fechaColuna();
                    $grid->fechaGrid();
                    hr("hrCard");
                    p($valor[2],"f12");   // descrição
                    
                    $numTarefaPendentes = $projeto->get_numeroTarefasPendentes($valor[0]);
                    $numTarefaConcluidas = $projeto->get_numeroTarefasConcluidas($valor[0]);
                    $total = $numTarefaConcluidas+$numTarefaPendentes;
                    
                    P("Tarefas pendentes: ".$numTarefaPendentes,"pCardNumTarefas");
                    P("Tarefas Concluídas: ".$numTarefaConcluidas,"pCardNumTarefas");
                    P("Tarefas Totais: ".$total,"pCardNumTarefas");
                    
                $card->fecha();
                $grid->fechaColuna();
            }
             
            $grid->fechaGrid();
        }
    }

    ##########################################################
    
    public static function menuCronologico($fase){
    /**
    * Exibe o menu de projetos ativos.
    * 
    * @syntax Gprojetos::Gprojetos;
    * 
    * @param $idProjeto integer NULL o id do projeto a sser ressaltado no menu informando que stá sendo editado.
    */    
   
        
        # Inicia o menu
        $menu1 = new Menu();
        $menu1->add_item('titulo','Cronológico');
        
        if($fase == "hoje"){
            $menu1->add_item('link','>Hoje','?fase=hoje','Exibe as tarefas para hoje');
        }else{
            $menu1->add_item('link','Hoje','?fase=hoje','Exibe as tarefas para hoje');
        }
        $menu1->show();
    }

    ##########################################################
}