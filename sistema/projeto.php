<?php
/**
 * Gestão de Projetos
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
    $projeto = new Projeto();

    # Verifica a fase do programa
    $fase = get('fase','ínicial');
    
    # Pega os ids quando se é necessário
    $idProjeto = get('idProjeto');
    $idCaderno = get('idCaderno');
    $idTarefa = get('idTarefa');
    $etiqueta = get('etiqueta');
    $solicitante = get('solicitante');
    $idNota = get('idNota');
    $grupo = get('grupo');
    $hojeGet = get('hoje');
    
    $origem = get('origem');
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    # Cabeçalho
    AreaServidor::cabecalho();

    # Define o botão voltar de acordo com a rotina
    if($fase == 'ínicial'){
        botaoVoltar('administracao.php');
    }else{
        switch ($origem){
            case NULL :
                botaoVoltar('?');
                break;
            
            case "projeto" :
            case "nota" :   
                botaoVoltar('?fase=projeto&idProjeto='.$idProjeto);
                break;
        }
    }
    titulo("Sistema de Gestão de Projetos");
    br();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(6,4,3);
    
    # Menu Cronológico
    Gprojetos::menuCronologico($fase);

    # Menu de Projetos
    Gprojetos::menuProjetosAtivos($idProjeto);
    
    # Menu de Cadernos
    Gprojetos::menuCadernos($idCaderno);
    
    # Menu de Etiquetas
    Gprojetos::menuEtiquetas($etiqueta);
    
    # Menu de Solicitantes
    Gprojetos::menuSolicitante($solicitante);

    $grid->fechaColuna();
    
    switch ($fase){
        case "ínicial" :
            
            $grid->abreColuna(6,8,9);
            
            # Menu de Projetos
            Gprojetos::cartoesProjetosAtivos($grupo);  
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
            
        ###########################################################
            
        case "projeto" :            
            $grid->abreColuna(6,8,9);
            
            # Pega os dados do projeto pesquisado
            $projetoPesquisado = $projeto->get_dadosProjeto($idProjeto);
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(8);
            
                # Exibe o nome e a descrição
                p($projetoPesquisado[1],'descricaoProjetoTitulo');
                p($projetoPesquisado[2],'descricaoProjeto');
                
            $grid->fechaColuna();
            $grid->abreColuna(4);
                
                # Menu
                $menu1 = new MenuBar("small button-group");
                
                # Nova Tarefa
                $link4 = new Link("+",'?fase=tarefaNova&idProjeto='.$idProjeto);
                $link4->set_class('button');
                $link4->set_title('Nova tarefa');
                $menu1->add_link($link4,"right");
                
                # Editar
                $link1 = new Link("Editar",'?fase=projetoNovo&idProjeto='.$idProjeto);
                $link1->set_class('button');
                $link1->set_title('Editar Projeto');
                $menu1->add_link($link1,"right");
                
                # Concluídas
                $link3 = new Link("Concluídas",'?fase=projetoConcluidas&origem=nota&idProjeto='.$idProjeto);
                $link3->set_class('button');
                $link3->set_title('Concluídas');
                $menu1->add_link($link3,"right");
                
                $menu1->show();
                
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            
            hr("projetosTarefas");
            
            # Exibe as tarefas pendentes com data
            $lista = new ListaTarefas("Tarefas Pendentes com Data");
            $lista->set_projeto($idProjeto);
            $lista->set_datado(TRUE);
            $lista->show();
            
            # Exibe as tarefas pendentes sem data
            $lista = new ListaTarefas("Tarefas Pendentes sem Data");
            $lista->set_projeto($idProjeto);
            $lista->set_datado(FALSE);
            $lista->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                 
        ###########################################################
            
        case "projetoConcluidas" :            
            $grid->abreColuna(6,8,9);
            
            # Pega os dados do projeto pesquisado
            $projetoPesquisado = $projeto->get_dadosProjeto($idProjeto);
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
            
                # Exibe o nome e a descrição
                p($projetoPesquisado[1],'descricaoProjetoTitulo');
                p($projetoPesquisado[2],'descricaoProjeto');
                
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            
            hr("projetosTarefas");
            
            # Exibe as tarefas completatadas
            $lista = new ListaTarefas("Tarefas Concluídas");
            $lista->set_projeto($idProjeto);
            $lista->set_datado(NULL);
            $lista->set_pendente(FALSE);
            $lista->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                 
        ###########################################################
            
        case "projetoEtiqueta" :
            
            $grid->abreColuna(6,8,9);
            
            # Exibe o nome e a descrição
            p($etiqueta,'descricaoProjetoTitulo');            
            hr("projetosTarefas");
            
            # Exibe as tarefas pendentes com data
            $lista = new ListaTarefas("Tarefas Pendentes com Data");
            $lista->set_etiqueta($etiqueta);
            $lista->set_datado(TRUE);
            $lista->show();
            
            # Exibe as tarefas pendentes sem data
            $lista = new ListaTarefas("Tarefas Pendentes sem Data");
            $lista->set_etiqueta($etiqueta);
            $lista->set_datado(FALSE);
            $lista->show();
            
            # Exibe as tarefas completatadas
            $lista = new ListaTarefas("Tarefas Concluídas");
            $lista->set_etiqueta($etiqueta);
            $lista->set_datado(NULL);
            $lista->set_pendente(FALSE);
            $lista->show();  
            break;
                 
        ###########################################################   
            
         case "projetoNovo" :
             
            $grid->abreColuna(6,8,9);
             
            # Verifica se é incluir ou editar
            if(!is_null($idProjeto)){
                # Pega os dados desse projeto
                $dados = $projeto->get_dadosProjeto($idProjeto);
                $titulo = "Editar Projeto";
            }else{
                $dados = array(NULL,NULL,NULL,NULL);
                $titulo = "Novo Projeto";
            }
             
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
                p($titulo,"f18");
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            
            # Formulário
            $form = new Form('?fase=validaProjeto&idProjeto='.$idProjeto);        
                    
            # projeto
            $controle = new Input('projeto','texto','Nome do Projeto:',1);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Nome do Projeto');
            $controle->set_title('O nome do Projeto a ser criado');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # descrição
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,5));
            $controle->set_linha(2);
            $controle->set_title('A descrição detalhda do projeto');
            $controle->set_placeholder('Descrição');
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # grupo
            $controle = new Input('grupo','texto','Nome do agrupamento:',1);
            $controle->set_size(50);
            $controle->set_linha(3);
            $controle->set_col(6);
            $controle->set_placeholder('Grupo');
            $controle->set_title('O nome agrupamento do Projeto');
            $controle->set_valor($dados[3]);
            $form->add_item($controle);
            
            # cor
            $controle = new Input('cor','combo','Cor:',1);
            $controle->set_size(10);
            $controle->set_col(6);
            $controle->set_linha(3);
            $controle->set_title('A cor da etiqueta');
            $controle->set_placeholder('Cor');
            $controle->set_array(array("secondary","primary","success","warning","alert"));
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(4);
            $form->add_item($controle);
            
            $form->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                        
        ###########################################################
            
        case "validaProjeto" :
            
            # Recuperando os valores
            $projeto = post('projeto');
            $descricao = post('descricao');
            $grupo = post('grupo');
            $cor = post('cor');
            
            # Cria arrays para gravação
            $arrayNome = array("projeto","descricao","ativo","grupo","cor");
            $arrayValores = array($projeto,$descricao,1,$grupo,$cor);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idProjeto,"tbprojeto","idProjeto");
            loadPage("?");
            break;
        
        ###########################################################   
            
         case "cadernoNovo" :
             
            $grid->abreColuna(6,8,9);
             
            # Verifica se é incluir ou editar
            if(!is_null($idCaderno)){
                # Pega os dados 
                $dados = $projeto->get_dadosCaderno($idCaderno);
                $titulo = "Editar";
            }else{
                $dados = array(NULL,NULL,NULL,NULL);
                $titulo = "Novo Caderno";
            }
             
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
                p($titulo,"f18");
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            
            # Formulário
            $form = new Form('?fase=validaCaderno&idCaderno='.$idCaderno);        
                    
            # caderno
            $controle = new Input('caderno','texto','Nome do Caderno:',1);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Nome do Caderno');
            $controle->set_title('O nome do Caderno a ser criado');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # descrição
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,5));
            $controle->set_linha(2);
            $controle->set_title('A descrição detalhda do caderno');
            $controle->set_placeholder('Descrição');
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(4);
            $form->add_item($controle);
            
            $form->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                        
        ###########################################################
            
        case "validaCaderno" :
            
            # Recuperando os valores
            $caderno = post('caderno');
            $descricao = post('descricao');
            
            # Cria arrays para gravação
            $arrayNome = array("caderno","descricao");
            $arrayValores = array($caderno,$descricao);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idCaderno,"tbprojetocaderno","idCaderno");
            loadPage("?");
            break;
        
        ###########################################################    
            
        case "tarefaNova" :
                        
            $grid->abreColuna(6,8,9);
            
            # Verifica se é incluir ou editar
            if(!is_null($idTarefa)){
                # Pega os dados dessa tarefa
                $dados = $projeto->get_dadosTarefa($idTarefa);
                $titulo = "Editar Tarefa";
                $etiqueta = $dados[7];
                $idProjeto = $dados[8];
            }else{
                $dados = array(NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
                $titulo = "Nova Tarefa";
            }
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
                p($projeto->get_nomeProjeto($idProjeto)." - ".$titulo,"f18");
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            
            # Pega os dados da combo projeto
            $select = 'SELECT idProjeto,
                              projeto
                         FROM tbprojeto
                     ORDER BY projeto';
            
            $comboProjeto = $intra->select($select);
            array_unshift($comboProjeto, array(NULL,NULL)); # Adiciona o valor de nulo
            
            # Formuário
            $form = new Form('?fase=validaTarefa&idTarefa='.$idTarefa.'&hoje='.$hojeGet);        
                    
            # tarefa
            $controle = new Input('tarefa','texto','Tarefa:',1);
            $controle->set_size(200);
            $controle->set_linha(1);
            $controle->set_col(8);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Tarefa');
            $controle->set_title('A tarefa a ser executada');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # idProjeto
            $controle = new Input('idProjeto','combo','Projeto:',1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_col(4);
            $controle->set_array($comboProjeto);
            if(is_null($idTarefa)){
                $controle->set_valor($idProjeto);
            }else{
                $controle->set_valor($dados[8]);
            }
            $form->add_item($controle);   
            
            # descrição
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,5));
            $controle->set_linha(2);
            $controle->set_title('A descrição detalhda do tarefa');
            $controle->set_placeholder('Descrição da tarefa');
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # etiqueta
            $controle = new Input('etiqueta','texto','Etiqueta:',1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(4);
            $controle->set_placeholder('Etiqueta');
            $controle->set_title('Uma etiqueta para ajudar na busca');
            $controle->set_valor($dados[7]);
            $form->add_item($controle);
            
            # solicitante
            $controle = new Input('solicitante','texto','Solicitante:',1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(4);
            $controle->set_placeholder('Solicitante');
            $controle->set_title('O Solicitante');
            $controle->set_valor($dados[11]);
            $form->add_item($controle);
            
            # prioridade
            $controle = new Input('noOrdem','combo','Prioridade:',1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(4);
            $controle->set_placeholder('Prioridade');
            $controle->set_title('A prioridade da tarefa');
            $controle->set_array(array(array(0,"Nenhuma"),array(1,"Média"),array(2,"Alta"),array(3,"Urgente")));
            $controle->set_valor($dados[3]);
            $form->add_item($controle);
            
            # status
            $controle = new Input('status','combo','Status:',1);
            $controle->set_size(20);
            $controle->set_linha(4);
            $controle->set_col(4);
            $controle->set_placeholder('Status');
            $controle->set_title('O status da tarefa');
            $controle->set_array(array("a fazer","fazendo"));
            $controle->set_valor($dados[10]);
            $form->add_item($controle);
            
            # dataInicial
            $controle = new Input('dataInicial','data','Data:',1);
            $controle->set_size(20);
            $controle->set_linha(4);
            $controle->set_col(4);
            $controle->set_title('A data inicial da tarefa');
            $controle->set_placeholder('A Data Inicial');
            $controle->set_valor($dados[4]);
            $form->add_item($controle);
            
            # dataFinal
            $controle = new Input('dataFinal','data','Data da Conclusão:',1);
            $controle->set_size(20);
            $controle->set_linha(4);
            $controle->set_col(4);
            $controle->set_title('A data da conclusão da tarefa');
            $controle->set_placeholder('A Data da conclusão');
            $controle->set_valor($dados[5]);
            $form->add_item($controle);
            
            # conclusao
            $controle = new Input('conclusao','textarea','Conclusão:',1);
            $controle->set_size(array(80,5));
            $controle->set_linha(5);
            $controle->set_title('O que foi feito para colcluir');
            $controle->set_valor($dados[9]);
            $form->add_item($controle);
            
            # pendente
            $controle = new Input('pendente','hidden','',1);
            $controle->set_size(20);
            $controle->set_linha(6);
            $controle->set_valor($dados[6]);
            $form->add_item($controle);     
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(7);
            $form->add_item($controle);
            
            $form->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                        
        ###########################################################
            
        case "validaTarefa" :
            
            # Recuperando os valores
            $tarefa = post('tarefa');
            $descricao = post('descricao');
            $dataInicial = vazioPraNulo(post('dataInicial'));
            $dataFinal = vazioPraNulo(post('dataFinal'));
            $idProjeto = post('idProjeto');
            $etiqueta = vazioPraNulo(post('etiqueta'));
            $pendente = post('pendente');
            $conclusao = post('conclusao');
            $noOrdem = post('noOrdem');
            $solicitante = post('solicitante');
            $status = post('status');
            
            # Força a tarefa pendente quando é inclusão
            if(is_null($idTarefa)){
                $pendente = 1;
            }
                      
            # Cria arrays para gravação
            $arrayNome = array("tarefa","descricao","dataInicial","dataFinal","idProjeto","pendente","etiqueta","conclusao","noOrdem","solicitante","status");
            $arrayValores = array($tarefa,$descricao,$dataInicial,$dataFinal,$idProjeto,$pendente,$etiqueta,$conclusao,$noOrdem,$solicitante,$status);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idTarefa,"tbprojetotarefa","idTarefa");
            if($hojeGet){
                loadPage("?fase=hoje");
            }else{
                loadPage("?fase=projeto&idProjeto=".$idProjeto);
            }
            break;
        
        ###########################################################  
        
        case "mudaTarefa" :
            
            # Pega os dados da tarefa
            $valor = $projeto->get_dadosTarefa($idTarefa);
            
            # Verifica o valor de pendente
            if($valor[6] == 1){
                $pendente = 0;
            }else{
                $pendente = 1;
            }
            
            # Cria arrays para gravação
            $arrayNome = array("pendente");
            $arrayValores = array($pendente);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idTarefa,"tbprojetotarefa","idTarefa");
            
            if($hojeGet){
                loadPage("?fase=hoje");
            }else{            
            
                if(is_null($idProjeto)){
                    loadPage("?fase=projetoEtiqueta&etiqueta=".$etiqueta);
                }

                if(is_null($etiqueta)){
                    loadPage("?fase=projeto&idProjeto=".$idProjeto);
                }
            }
            break;
            
        ###########################################################
            
        case "hoje" :
            
            $grid->abreColuna(6,8,9);
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
            
            $hoje = date("d/m/Y");
            
                # Exibe o nome e a descrição
                p('Tarefas Pendentes Hoje.','descricaoProjetoTitulo');
                p('Tarefas de todos os projetos pendentes para hoje: '.$hoje,'descricaoProjeto');
                
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            
            hr("projetosTarefas");
            
            # Exibe as tarefas pendentes de hoje
            $lista = new ListaTarefas("Tarefas Pendentes Hoje");
            #$lista->set_projeto($idProjeto);
            $lista->set_hoje(TRUE);
            $lista->set_datado(TRUE);
            $lista->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                 
        ###########################################################
            
        case "timeline" :            
            $grid->abreColuna(6,8,9);
            $projetoPesquisado = $projeto->get_dadosProjeto($idProjeto);
            
            # Nome
            $grid = new Grid();
            $grid->abreColuna(12);
            
                p($projetoPesquisado[1],'descricaoProjetoTitulo');
                p($projetoPesquisado[2],'descricaoProjeto');
                                
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            
            hr("projetosTarefas");
            br();
            
            # Exibe as tarefas pendentes com data
            $lista = new ListaTarefas("Tarefas Pendentes com Data");
            $lista->set_projeto($idProjeto);            
            $lista->set_datado(TRUE);
            $lista->showTimeline();
            $lista->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                 
        ###########################################################    
            
        case "notaNova" :
             
            $grid->abreColuna(6,8,9);
             
            # Verifica se é incluir ou editar
            if(!is_null($idNota)){
                # Pega os dados dessa nota
                $dados = $projeto->get_dadosNota($idNota);
                $titulo = "Editar Nota";
            }else{
                $dados = array(NULL,NULL,NULL,NULL,NULL);
                $titulo = "Nova Nota";
            } 
             
            # Titulo
            $grid = new Grid();
            $grid->abreColuna(12);
                p($titulo,"f18");
            $grid->fechaColuna(); 
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            
            # Pega os dados da combo caderno
            $select = 'SELECT idCaderno,
                              caderno
                         FROM tbprojetocaderno
                     ORDER BY caderno';
            
            $comboCaderno = $intra->select($select);
            array_unshift($comboCaderno, array(NULL,NULL)); # Adiciona o valor de nulo
            
            # Formuário
            $form = new Form('?fase=validaNota&idNota='.$idNota);        
                    
            # Título
            $controle = new Input('titulo','texto','Título:',1);
            $controle->set_size(100);
            $controle->set_linha(1);
            $controle->set_col(8);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_title('Título da nota');
            $controle->set_valor($dados[3]);
            $form->add_item($controle);
            
            # idProjeto
            $controle = new Input('idCaderno','combo','Caderno:',1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_col(4);
            $controle->set_array($comboCaderno);
            if(is_null($idNota)){
                $controle->set_valor($idCaderno);
            }else{
                $controle->set_valor($dados[1]);
            }
            $form->add_item($controle);  
                                    
            # nota            
            $controle = new Input('nota','textarea','Descrição:',1);
            $controle->set_size(array(80,15));
            $controle->set_linha(2);
            $controle->set_col(12);
            $controle->set_title('Corpo da nota');
            $controle->set_valor($dados[4]);
            $form->add_item($controle);
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(3);
            $form->add_item($controle);
            
            $form->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                        
        ###########################################################
            
        case "validaNota" :
            
            # Recuperando os valores
            $titulo = post('titulo');
            $caderno = post('idCaderno');
            $nota = post('nota');
                      
            # Cria arrays para gravação
            $arrayNome = array("titulo","idCaderno","nota");
            $arrayValores = array($titulo,$caderno,$nota);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idNota,"tbprojetonota","idNota");
            
            if(is_null($idNota)){
                loadPage("?fase=caderno&idCaderno=$caderno");
            }else{
                loadPage("?fase=caderno&idCaderno=$caderno&idNota=$idNota");
            }
            break;
        
        ###########################################################    
            
        case "caderno" :
             
            # Area das notas
           $grid->abreColuna(6,8,9);
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(8);
            
                # Exibe o nome e a descrição
                $dados = $projeto->get_dadosCaderno($idCaderno);
                p($dados[1],'descricaoProjetoTitulo');
                p($dados[2],'descricaoProjeto');
                
            $grid->fechaColuna();
            $grid->abreColuna(4);
                
                # Menu
                $menu1 = new MenuBar("small button-group");

                # Nova Nota
                $link4 = new Link("+",'?fase=notaNova&idCaderno='.$idCaderno);
                $link4->set_class('button');
                $link4->set_title('Nova Nota');
                $menu1->add_link($link4,"right");
                
                # Editar
                $link1 = new Link("Editar",'?fase=cadernoNovo&idCaderno='.$idCaderno);
                $link1->set_class('button');
                $link1->set_title('Editar Projeto');
                $menu1->add_link($link1,"right");
                
                $menu1->show();
                
            $grid->fechaColuna();
            $grid->fechaGrid();             
            
            hr("projetosTarefas");
                        
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(3);
            
                # Exibe as notas
                #$painel = new Callout();
                #$painel->abre();
            
                # Pega as notas
                $select = 'SELECT idNota
                             FROM tbprojetonota
                            WHERE idcaderno = '.$idCaderno.' ORDER BY titulo';

                # Acessa o banco
                $intra = new Intra();
                $notas = $intra->select($select);
                $numNotas = $intra->count($select);

                # Inicia a tabela
                $tabela = new Tabela("tableNotas");
                #$tabela->set_titulo("Notas");

                $tabela->set_conteudo($notas);
                $tabela->set_label(array(""));
                $tabela->set_classe(array("Gprojetos"));
                $tabela->set_metodo(array("showNota"));
                $tabela->set_align(array("left"));
                
                if($numNotas > 0){
                    $tabela->show();
                }else{
                    #tituloTable("Notas");
                    br();
                    p("Clique em + para acrescentar uma nota","f14","center");
                    br(3);
                }
                
                #$painel->fecha();
            
            $grid->fechaColuna();
            
            # Área da nota editada
            $grid->abreColuna(9);
            
            # Pega os dados dessa nota
            if(!is_null($idNota)){
                $dados = $projeto->get_dadosNota($idNota);
            
                # Exibe a nota
                $painel = new Callout();
                $painel->abre();
                    $grid = new Grid();
                    $grid->abreColuna(10);
                        p($dados[3],'descricaoProjetoTitulo');
                    $grid->fechaColuna();
                    $grid->abreColuna(2);

                        # Menu
                        $menu1 = new MenuBar("small button-group");

                        # Nova Nota
                        $link = new Link("Editar",'?fase=notaNova&origem=nota&idNota='.$idNota);
                        $link->set_class('button');
                        $link->set_title('Editar Nota');
                        $menu1->add_link($link,"right");

                        $menu1->show();

                    $grid->fechaColuna();
                    $grid->fechaGrid();
                
                        hr("projetosTarefas");
                        echo "<pre id='preNota'>".$dados[4]."</pre>";
                        
                $painel->fecha();
            }else{
                $painel = new Callout();
                $painel->abre();
                    br(3);
                    p("Nenhuma nota selecionada","f14","center");
                    br(3);
                $painel->fecha();
                
            }

            $grid->fechaColuna();
            $grid->fechaGrid();   
            
            $grid->fechaColuna();
            $grid->fechaGrid();   
            break;
                        
        ###########################################################
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();  
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}