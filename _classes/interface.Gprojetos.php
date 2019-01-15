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
    * @param $idProjeto integer NULL o id do projeto a ser ressaltado no menu informando que stá sendo editado.
    */    
        
        # Acessa o banco de dados
        $projeto = new Projeto();
        $intra = new Intra();
        
        # Exibe menu de grupos
        $select1 = "SELECT DISTINCT grupo
                     FROM tbprojeto
                     WHERE ativo
                     ORDER BY grupo";
        
        $dadosGrupos = $intra->select($select1);
        $numGrupos = $intra->count($select1);
        
        # Pega os projetos cadastrados
        $select = 'SELECT idProjeto,
                          projeto,
                          descricao,
                          grupo
                     FROM tbprojeto
                     WHERE ativo
                  ORDER BY projeto';
        
        $numProjetos = $intra->count($select);
        
        # Inicia o menu
        $menu1 = new Menu();
        $menu1->add_item('titulo1','Projetos');
        #$menu1->add_item('link','+ Novo Projeto','?fase=projetoNovo');
                
        # Verifica se tem projetos
        if($numProjetos > 0){
            foreach ($dadosGrupos as $valor1){
                
                $menu1->add_item('titulo2',"<i class='fi-book'></i> ".$valor1[0],"?fase=cartaoProjeto&grupo=".$valor1[0],$valor1[0]);
                #$menu1->add_item('titulo1','Projetos','?fase=cartaoProjeto','Cartões de Projetos');
                #$menu1->add_item('link','+ Novo Projeto','?fase=projetoNovo');
                
                
                # Pega os projetos cadastrados
                $select3 = 'SELECT idProjeto,
                                  projeto,
                                  descricao,
                                  grupo
                             FROM tbprojeto
                             WHERE ativo AND grupo = "'.$valor1[0].'"
                          ORDER BY numOrdem, projeto';

                $dadosProjetos = $intra->select($select3);
                
                # Percorre o array 
                foreach ($dadosProjetos as $valor){
                    
                    $numTarefa = $projeto->get_numeroTarefasPendentes($valor[0]);
                    $texto = $valor[1]." <span id='numProjeto'>$numTarefa</span>";

                    # Marca o item que está sendo editadoFramework
                    if($idProjeto == $valor[0]){
                        $menu1->add_item('link',"<i class='fi-list-bullet'></i> <b>".$texto."</b>",'?fase=projeto&idProjeto='.$valor[0],$valor[2]);
                    }else{
                        $menu1->add_item('link',"<i class='fi-list-bullet'></i> ".$texto,'?fase=projeto&idProjeto='.$valor[0],$valor[2]);
                    }
                }                
            }            
        }
        $menu1->show();
        
    }

    ##########################################################
    
    public static function menuCadernos($idCaderno = NULL,$idNota = NULL){
    /**
    * Exibe o menu de cadernos.
    * 
    * @syntax Gprojetos::Gprojetos;
    * 
    * @param $idPCaderno integer NULL o id do caderno a sser ressaltado no menu informando que stá sendo editado.
    */    
   
        # Acessa o banco de dados
        $projeto = new Projeto();
        $intra = new Intra();
        
        # Exibe menu de grupos
        $select1 = "SELECT DISTINCT grupo
                     FROM tbprojetocaderno
                     ORDER BY grupo";
        
        $dadosGrupos = $intra->select($select1);
        $numGrupos = $intra->count($select1);
        
        # Pega os projetos cadastrados
        $select = 'SELECT idCaderno,
                          caderno,
                          descricao
                     FROM tbprojetocaderno
                  ORDER BY caderno';
        
        $dadosProjetos = $intra->select($select);
        $numCadernos = $intra->count($select);
        
        # Inicia o menu
        $menu1 = new Menu();
        $menu1->add_item('titulo1','Cadernos');
        #$menu1->add_item('link','+ Novo Caderno','?fase=cadernoNovo');
                
        # Verifica se tem cadernos
        if($numCadernos > 0){
            foreach ($dadosGrupos as $valor1){
                # Grupos
                $menu1->add_item('titulo2',"<i class='fi-results'></i> ".$valor1[0],"?fase=cartaoCaderno&grupo=".$valor1[0],"Estante: ".$valor1[0]);
                #$menu1->add_item('titulo1','Projetos','?fase=cartaoProjeto','Cartões de Projetos');
                #$menu1->add_item('link','+ Novo Projeto','?fase=projetoNovo');
                
                
                # Pega os projetos cadastrados
                $select3 = 'SELECT idCaderno,
                                   caderno,
                                   descricao
                              FROM tbprojetocaderno
                             WHERE grupo = "'.$valor1[0].'"
                          ORDER BY caderno';

                $dadosProjetos = $intra->select($select3);
                
                # Percorre o array 
                foreach ($dadosProjetos as $valor){
                    $numNotas = $projeto->get_numeroNotas($valor[0]);
                    $texto = $valor[1]." <span id='numProjeto'>$numNotas</span>";
                    $linkNovo = "<a href='?fase=notaNova' title='Nova nota'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='badge secondary'><i class='fi-plus'></i></span></a>";

                    # Marca o item que está sendo editado
                    if($idCaderno == $valor[0]){
                        $menu1->add_item('link',"<i class='fi-book'></i><b> ".$texto."</b>".$linkNovo,'?fase=caderno&idCaderno='.$valor[0],"Caderno: ".$valor[1]);

                        # Pega as notas
                        $select = 'SELECT idNota,
                                          titulo
                                     FROM tbprojetonota
                                    WHERE idcaderno = '.$valor[0].' ORDER BY numOrdem,titulo';

                        # Acessa o banco
                        $notas = $intra->select($select);
                        $numNotas = $intra->count($select);

                        # Incluir nota
                        #$menu1->add_item('sublink','+ Nova Nota','?fase=notaNova');

                        # Percorre as notas 
                        foreach($notas as $tituloNotas){
                            if($idNota == $tituloNotas[0]){
                                $menu1->add_item('sublink',"<i class='fi-page'></i><b> ".$tituloNotas[1].'</b>','?fase=caderno&idNota='.$tituloNotas[0]);
                            }else{
                                $menu1->add_item('sublink',"<i class='fi-page'></i> ".$tituloNotas[1],'?fase=caderno&idNota='.$tituloNotas[0]);
                            }
                        }
                    }else{
                        $menu1->add_item('link',"<i class='fi-book'></i> ".$texto,'?fase=caderno&idCaderno='.$valor[0],"Caderno: ".$valor[1]);
                    }
                    
                }           
            }            
        }
        $menu1->show();
    }

    ##########################################################
    
    public static function menuEtiquetas($etiqueta = NULL){
    /**
    * Exibe o menu de projetos ativos.
    * 
    * @syntax Gprojetos::Gprojetos;
    * 
    * @param $idProjeto integer NULL o id do projeto a sser ressaltado no menu informando que stá sendo editado.
    */    
   
        # Pega os projetos cadastrados
        $select = 'SELECT distinct etiqueta
                     FROM tbprojetotarefa
                    WHERE etiqueta is not null
                     ORDER BY etiqueta';
        
        # Acessa o banco de dados
        $projeto = new Projeto();
        $intra = new Intra();
        $dadosEtiquetas = $intra->select($select);
        $numEtiquetas = $intra->count($select);

        # Inicia o menu
        $menu1 = new Menu();
        $menu1->add_item('titulo1','Etiquetas');
        
        # Verifica se tem etiquetas
        if($numEtiquetas>0){
            
            # Percorre o array 
            foreach ($dadosEtiquetas as $valor) {
                $numTarefa = $projeto->get_numeroTarefasEtiqueta($valor[0]);
                $texto = $valor[0]." <span id='numProjeto'>$numTarefa</span>";

                # Marca o item que está sendo editado
                if($etiqueta == $valor[0]){
                    $menu1->add_item('link',"<i class='fi-price-tag'></i> <b>".$texto.'</b>','?fase=etiqueta&etiqueta='.$valor[0]);
                }else{
                    $menu1->add_item('link',"<i class='fi-price-tag'></i> ".$texto,'?fase=etiqueta&etiqueta='.$valor[0]);
                }
            }
        }
        $menu1->show();
    }

    ##########################################################
    
    public static function menuSolicitante($solicitante = NULL){
    /**
    * Exibe o menu de solicitantes.
    * 
    * @syntax Gprojetos::Gprojetos;
    * 
    * @param $idProjeto integer NULL o id do projeto a sser ressaltado no menu informando que stá sendo editado.
    */    
   
        # Pega os projetos cadastrados
        $select = 'SELECT distinct solicitante
                     FROM tbprojetotarefa
                    WHERE solicitante is not null
                 ORDER BY solicitante';
        
        # Acessa o banco de dados
        $projeto = new Projeto();
        $intra = new Intra();
        $dadosSolicitantes = $intra->select($select);
        $numSolicitantes = $intra->count($select);

        # Inicia o menu
        $menu1 = new Menu();
        $menu1->add_item('titulo1','Solicitantes');
        
        # Verifica se tem solicitante
        if($numSolicitantes>0){
            
            # Percorre o array 
            foreach ($dadosSolicitantes as $valor) {
                $numTarefa = $projeto->get_numeroTarefasSolitante($valor[0]);
                $texto = $valor[0]." <span id='numProjeto'>$numTarefa</span>";

                # Marca o item que está sendo editado
                if($solicitante == $valor[0]){
                    $menu1->add_item('link',"<i class='fi-torso'></i> <b>".$texto."</b>",'?fase=solicitante&solicitante='.$valor[0]);
                }else{
                    $menu1->add_item('link',"<i class='fi-torso'></i> ".$texto,'?fase=solicitante&solicitante='.$valor[0]);
                }
            }
        }
        $menu1->show();
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
        
        # Verifica se tem projetos
        if($numProjetos>0){
            # Inicia o grid
            $grid = new Grid();
            
            # Percorre o array 
            foreach ($dadosProjetos as $valor){
                
                $grid->abreColuna(12,6,4);
                $card = new Callout($valor[4],"card");
                $card->abre();
                
                    $div = new Div("divEditaNota");
                    $div->abre();
                        $link = new Link("Editar",'?fase=projetoEditar&idProjeto='.$valor[0],"Edita Projeto: ".$valor[1]);
                        $link->set_id("editaNota");
                        $link->show();
                    $div->fecha();        
                
                    $link = new Link($valor[1],'?fase=projeto&idProjeto='.$valor[0],"Projeto: ".$valor[1]);
                    $link->set_id("aCardNomeProjeto");
                    $link->show();

                    #p($valor[1],"pCardNomeProjeto");             // Nome do projeto
                    p(strtoupper($valor[3]),"pCardNomeGrupo");    // Grupo
                    
                    hr("hrCard");
                    p($valor[2],"descricaoProjeto");   // descrição
                    br();
                    
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
    
    public static function cartoesCadernos($grupo = NULL){
    /**
    * Exibe o os cadernos ativo em forma de cartões
    * 
    * @syntax Gprojetos::Gprojetos;
    */    
   
        # Pega os projetos cadastrados
        $select = 'SELECT idCaderno,
                          caderno,
                          descricao,
                          grupo,
                          cor
                     FROM tbprojetocaderno';
        
        if(!is_null($grupo)){
            $select .= ' WHERE grupo = "'.$grupo.'"';
        }
        
        $select .= ' ORDER BY caderno';
                
        # Acessa o banco de dados
        $projeto = new Projeto();
        $intra = new Intra();
        $dadosCaderno = $intra->select($select);
        $numCadernos = $intra->count($select);
        
        # Verifica se tem cadernos
        if($numCadernos > 0){
            # Inicia o grid
            $grid = new Grid();
            
            # Percorre o array 
            foreach ($dadosCaderno as $valor){
                
                $grid->abreColuna(12,6,4);
                $card = new Callout($valor[4],"card");
                $card->abre();
                
                    $div = new Div("divEditaNota");
                    $div->abre();
                        $link = new Link("Editar",'?fase=cadernoNovo&idCaderno='.$valor[0],"Edita caderno: ".$valor[1]);
                        $link->set_id("editaNota");
                        $link->show();
                    $div->fecha();  
                
                    $link = new Link($valor[1],'?fase=caderno&idCaderno='.$valor[0],"Caderno: ".$valor[1]);
                    $link->set_id("aCardNomeProjeto");
                    $link->show(); 
                    
                    p(strtoupper($valor[3]),"pCardNomeGrupo");    // Grupo
                
                    hr("hrCard");
                    p($valor[2],"descricaoProjeto");   // descrição
                    br();
                    
                    
                    $numNotas = $projeto->get_numeroNotas($valor[0]);
                    
                    if($numNotas > 0){
                    
                        $select2 = 'SELECT titulo
                                     FROM tbprojetonota
                                    WHERE idCaderno = '.$valor[0].'  
                                   ORDER BY nota';

                        $nomeNota = $intra->select($select2);

                        # Percorre o array 
                        foreach ($nomeNota as $valor2){
                            p($valor2[0],"pCardNumTarefas");
                        }
                    }
                    
                    p("Notas: ".$numNotas,"pCardNumTarefas");
                    
                $card->fecha();
                $grid->fechaColuna();
            }
             
            $grid->fechaGrid();
        }
    }

    ##########################################################
    
    public static function menuFazendo($fase){
    /**
    * Exibe o menu de projetos ativos.
    * 
    * @syntax Gprojetos::Gprojetos;
    */    
           
        # Inicia o menu
        $menu1 = new Menu();
        $menu1->add_item('titulo1','Status');
        
        if($fase == "fazendo"){
            $menu1->add_item('titulo2',"<b>Fazendo</b>",'?fase=fazendo','Exibe as tarefas que estao sendo feitas atualmente');            
        }else{
            $menu1->add_item('titulo2',"Fazendo",'?fase=fazendo','Exibe as tarefas que estao sendo feitas atualmente');
        }
        $menu1->show();
    }

    ###########################################################
    
    public function showProjeto($idProjeto){
    /**
     * Retorna o nome do projeto informado
     * 
     * @param $idProjeto integer NULL o idProjeto
     * 
     * @syntax $projeto->get_nomeProjeto([$idProjeto]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT projeto,
                          cor,
                          descricao
                     FROM tbprojeto
                     WHERE idProjeto = '.$idProjeto;
        
        $intra = new Intra();
        $row = $intra->select($select,false);
        if(!is_null($row[0])){
            label($row[0],$row[1],NULL,$row[2]);
        }else{
            echo "--";
        }
    }
           
    ###########################################################
    
    public function showNota($idNota){
    /**
     * Retorna um link para editar a nota
     * 
     * @param $idNota integer NULL o idNota
     * 
     * @syntax $projeto->showNota([$idNota]);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT titulo,
                          idCaderno
                     FROM tbprojetonota
                    WHERE idNota = '.$idNota;
        
        $intra = new Intra();
        $row = $intra->select($select,false);
        
        $link = new Link($row[0],"?fase=caderno&idCaderno=$row[1]&idNota=$idNota");
        $link->show();
    }
           
    ###########################################################
    
    
    public function showEtiqueta($etiqueta = NULL){
    /**
     * Retorna o nome da etiqueta
     * 
     * @param $etiqueta integer NULL o etiqueta
     * 
     * @syntax $projeto->get_nomeProjeto([$etiqueta]);  
     */
    
        # Pega as etiquetas cadastradas
        $select = 'SELECT etiqueta,
                          cor,
                          descricao
                     FROM tbprojetoetiqueta
                     WHERE etiqueta = '.$etiqueta;
        
        $intra = new Intra();
        
        if(is_null($etiqueta)){
            echo "--";
        }else{
            $row = $intra->select($select,false);
            if(!is_null($row[0])){
                label($row[0],$row[1],NULL,$row[2]);
            }else{
                echo "--";
            }
        }
    }
           
    ###########################################################
    
    public function showTarefa($idTarefa){
    /**
     * Exibe a tarefa
     * 
     * @param $idTarefa integer NULL o $idTarefa
     * @param $esconde  integer NULL NULL -> exibe o projeto e a etiqueta | 1 -> esconde Projeto | 2 -> esconde etiqueta
     * 
     * @syntax $projeto->showTarefa($idTarefa);  
     */

        # Pega os projetos cadastrados
        $select = 'SELECT tarefa,
                          noOrdem,
                          pendente,
                          idProjeto,
                          etiqueta,
                          solicitante
                     FROM tbprojetotarefa
                    WHERE idTarefa = '.$idTarefa;
        
        $intra = new Intra();
        $row = $intra->select($select,false);
        
        $projeto = new Projeto();
        $nomeProjeto = $projeto->get_nomeProjeto($row[3]);        
        
        # Verifica se está pendente
        if($row[2]){
            
            $link = new Link($row[0],'?fase=tarefaNova&idTarefa='.$idTarefa);
            $link->show();
            br();
            
            # Projeto
            span($nomeProjeto,"projeto",NULL,"Projeto");
                        
            # Etiqueta
            if(!is_null($row[4])){
                echo "&nbsp&nbsp&nbsp";
                span($row[4],"etiqueta",NULL,"Etiqueta");
            } 
            
            # Solicitante
            if(!is_null($row[5])){
                echo "&nbsp&nbsp&nbsp";
                span($row[5],"solicitante",NULL,"Solicitante");
            } 
            
        }else{
            
            $link = new Link(del($row[0]),'?fase=tarefaNova&idTarefa='.$idTarefa);
            $link->show();            
            br();
            
             # Projeto
            span($nomeProjeto,"projeto",NULL,"Projeto");
                        
            # Etiqueta
            if(!is_null($row[4])){
                echo "&nbsp&nbsp&nbsp";
                span($row[4],"etiqueta",NULL,"Etiqueta");
            } 
            
            # Solicitante
            if(!is_null($row[5])){
                echo "&nbsp&nbsp&nbsp";
                span($row[5],"solicitante",NULL,"Solicitante");
            } 
        }
    }
           
    ###########################################################
    
    public function showPrioridade($idTarefa){
    /**
     * Exibe a prioridade de uma tarefa
     * 
     * @param $idTarefa integer NULL o $idTarefa
     * 
     * @syntax $projeto->showPrioridade($idTarefa);  
     */
    
        # Pega os projetos cadastrados
        $select = 'SELECT noOrdem
                     FROM tbprojetotarefa
                    WHERE idTarefa = '.$idTarefa;
        
        $intra = new Intra();
        $row = $intra->select($select,false); 
        $tamanho = 20;
        
        # Exibe a prioridade
        switch ($row[0]){
            case 1 :
                $figura = new Imagem(PASTA_FIGURAS.'prioridadeMedia.png','Prioridade Média',$tamanho,$tamanho);
                $figura->show();
                break;
            
            case 2 :
                $figura = new Imagem(PASTA_FIGURAS.'prioridadeAlta.png','Prioridade Média',$tamanho,$tamanho);
                $figura->show();
                break;
            
            case 3 :
                $figura = new Imagem(PASTA_FIGURAS.'prioridadeUrgente.png','Prioridade Média',$tamanho,$tamanho);
                $figura->show();
                break;
        }
    }
           
    ###########################################################
    
    public function showData($idTarefa){
    /**
     * Retorna o nome da etiqueta
     * 
     * @param $etiqueta integer NULL o idProjeto
     * 
     * @syntax $projeto->get_nomeProjeto([$etiqueta]);  
     */
    
       
        # Pega os projetos cadastrados
        $select = 'SELECT dataInicial,
                          dataFinal
                     FROM tbprojetotarefa
                     WHERE idTarefa = '.$idTarefa;
        
        $intra = new Intra();
        $row = $intra->select($select,false);
        
        $dataInicial = date_to_php($row[0]);
        $dataFinal = date_to_php($row[1]);
        
        if($dataFinal == "00/00/0000"){
            $dataFinal = NULL;
        }
        
        if($dataInicial == "00/00/0000"){
            $dataInicial = NULL;
        }
        
        $hoje = date('d/m/Y');
        $amanha = addDias($hoje,1,FALSE);
        $ontem = addDias($hoje,-1,FALSE);
         
        # Inicia as Variáveis de retorno
        $inicialRetorno = $dataInicial;
        $finalRetorno = $dataFinal;
        
        # Se alguma data é hoje
        if($dataInicial == $hoje){
            $inicialRetorno = "Hoje";
        }
        
        if($dataFinal == $hoje){
            $finalRetorno = "Hoje";
        }
        
        # Se alguma data é amanhã
        if($dataInicial == $amanha){
            $inicialRetorno = "Amanhã";
        }
        
        if($dataFinal == $amanha){
            $finalRetorno = "Amanhã";
        }
        
        # Se alguma data é ontem
        if($dataInicial == $ontem){
            $inicialRetorno = "Ontem";
        }
        
        if($dataFinal == $ontem){
            $finalRetorno = "Ontem";
        }
        
        if(vazio($finalRetorno)){
            $textoRetorno = $inicialRetorno;
        }else{
            $textoRetorno = $inicialRetorno." até ".$finalRetorno;
        }
        
        # Exibe a data
        if(!is_null($textoRetorno)){
             echo $textoRetorno;
        }else{
            echo "--";
        }
    }
}