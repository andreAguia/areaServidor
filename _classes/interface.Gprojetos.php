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
        
        # Inicia o menu
        $menu1 = new Menu();
        $menu1->add_item('titulo1','Projetos','?','Cartões de Projetos');
        $menu1->add_item('link','+ Novo Projeto','?fase=projetoNovo');
        
        # Verifica se tem projetos
        if($numProjetos>0){
            # Percorre o array 
            foreach ($dadosProjetos as $valor){
                $numTarefa = $projeto->get_numeroTarefasPendentes($valor[0]);
                $texto = $valor[1]." <span id='numProjeto'>$numTarefa</span>";
                
                # Marca o item que está sendo editado
                if($idProjeto == $valor[0]){
                    $texto = "> ".$texto;
                }
                $menu1->add_item('link',$texto,'?fase=projeto&idProjeto='.$valor[0],$valor[2]);
            }
        }
        $menu1->show();
    }

    ##########################################################
    
    public static function menuCadernos($idCaderno = NULL){
    /**
    * Exibe o menu de cadernos.
    * 
    * @syntax Gprojetos::Gprojetos;
    * 
    * @param $idPCaderno integer NULL o id do caderno a sser ressaltado no menu informando que stá sendo editado.
    */    
   
        # Pega os projetos cadastrados
        $select = 'SELECT idCaderno,
                          caderno,
                          descricao
                     FROM tbprojetocaderno
                  ORDER BY caderno';
        
        # Acessa o banco de dados
        $projeto = new Projeto();
        $intra = new Intra();
        $dadosProjetos = $intra->select($select);
        $numCadernos = $intra->count($select);
        
        # Inicia o menu
        $menu1 = new Menu();
        $menu1->add_item('titulo1','Cadernos');
        $menu1->add_item('link','+ Novo Caderno','?fase=cadernoNovo');
        
        # Verifica se tem cadernos
        if($numCadernos>0){
            
            # Percorre o array 
            foreach ($dadosProjetos as $valor){
                $numNotas = $projeto->get_numeroNotas($valor[0]);
                $texto = $valor[1]." <span id='numProjeto'>$numNotas</span>";
                
                # Marca o item que está sendo editado
                if($idCaderno == $valor[0]){
                    $texto = "> ".$texto;
                }
                $menu1->add_item('link',$texto,'?fase=caderno&idCaderno='.$valor[0],$valor[2]);
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
                    $texto = "> ".$texto;
                }
                $menu1->add_item('link',$texto,'?fase=projetoEtiqueta&etiqueta='.$valor[0]);
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
        
        # Verifica se tem etiquetas
        if($numSolicitantes>0){
            
            # Percorre o array 
            foreach ($dadosSolicitantes as $valor) {
                $numTarefa = $projeto->get_numeroTarefasSolitante($solicitante);
                $texto = $valor[0]." <span id='numProjeto'>$numTarefa</span>";

                # Marca o item que está sendo editado
                if($solicitante == $valor[0]){
                    $texto = "> ".$texto;
                }
                $menu1->add_item('link',$texto,'?fase=projetoSolicitante&solicitante='.$valor[0]);
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
            echo '<nav aria-label="Grupos" role="navigation">';
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
                
                $grid->abreColuna(12,6,4);
                $card = new Callout($valor[4],"card");
                $card->abre();
                
                        $link = new Link($valor[1],'?fase=projeto&idProjeto='.$valor[0],$valor[2]);
                        $link->set_id("aCardNomeProjeto");
                        $link->set_title("Exibe os Projetos ativos do grupo ".$grupoValor[0]);
                        $link->show();
                        
                        #p($valor[1],"pCardNomeProjeto");             // Nome do projeto
                        p(strtoupper($valor[3]),"pCardNomeGrupo");    // Grupo
                    
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
    
    public static function menuFazendo($fase){
    /**
    * Exibe o menu de projetos ativos.
    * 
    * @syntax Gprojetos::Gprojetos;
    * 
    * @param $idProjeto integer NULL o id do projeto a sser ressaltado no menu informando que stá sendo editado.
    */    
           
        # Inicia o menu
        $menu1 = new Menu();
        #$menu1->add_item('titulo','Cronológico');
        
        if($fase == "hoje"){
            $menu1->add_item('link','> Fazendo','?fase=fazendo','Exibe as tarefas que estao sendo feitas atualmente');
        }else{
            $menu1->add_item('link','Fazendo','?fase=fazendo','Exibe as tarefas que estao sendo feitas atualmente');
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
        $tamanho = 20;
        
        # Exibe a prioridade
        switch ($row[1]){
            case 1 :
                $figura = new Imagem(PASTA_FIGURAS.'prioridadeMedia.png','Prioridade Média',$tamanho,$tamanho);
                $figura->show();
                echo " ";
                break;
            
            case 2 :
                $figura = new Imagem(PASTA_FIGURAS.'prioridadeAlta.png','Prioridade Média',$tamanho,$tamanho);
                $figura->show();
                echo " ";
                break;
            
            case 3 :
                $figura = new Imagem(PASTA_FIGURAS.'prioridadeUrgente.png','Prioridade Média',$tamanho,$tamanho);
                $figura->show();
                echo " ";
                break;
        }
        
        $projeto = new Projeto();
        $nomeProjeto = $projeto->get_nomeProjeto($row[3]);        
        
        # Verifica se está pendente
        if($row[2]){
            
            $link = new Link($row[0],'?fase=tarefaNova&idTarefa='.$idTarefa);
            $link->show();
            br();
            
            # Projeto
            span($nomeProjeto,"projeto");
            
            
            # Etiqueta
            if(!is_null($row[4])){
                echo "&nbsp&nbsp&nbsp";
                span($row[4],"etiqueta");
            } 
            
            # Solicitante
            if(!is_null($row[5])){
                echo "&nbsp&nbsp&nbsp";
                span($row[5],"solicitante");
            } 
            
        }else{
            
            $link = new Link(del($row[0]),'?fase=tarefaNova&idTarefa='.$idTarefa);
            $link->show();            
            br();
            
             # Projeto
            span($nomeProjeto,"projeto");
            
            
            # Etiqueta
            if(!is_null($row[4])){
                echo "&nbsp&nbsp&nbsp";
                span($row[4],"etiqueta");
            } 
            
            # Solicitante
            if(!is_null($row[5])){
                echo "&nbsp&nbsp&nbsp";
                span($row[5],"solicitante");
            } 
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