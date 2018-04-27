<?php
/**
 * Gestão de Projetos
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("../sistema/_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
    $projeto = new Projeto();

    # Verifica a fase do programa
    $fase = get('fase','ínicial');
    
    # Pega os ids quando se é necessário
    $idProjeto = get('idProjeto');
    $idTarefa = get('idTarefa');
    $idEtiqueta = get('idEtiqueta');
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    # Cabeçalho
    AreaServidor::cabecalho();

    botaoVoltar('../sistema/administracao.php');
    titulo("Sistema de Gestão de Projetos");
    br();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(3);

    # Menu de Projetos
    $row = $projeto->listaProjetosAtivos();
    $numProjetos = $projeto->numeroProjetosAtivos();

    # Inicia o menu
    $menu1 = new Menu();
    $menu1->add_item('titulo','Projetos Ativos');

    # Se existir algum projeto percorre
    # o array e monta o menu
    if($numProjetos>0){
        # Percorre o array 
        foreach ($row as $valor) {                    
            $menu1->add_item('link',$valor[1],'?fase=projeto&idProjeto='.$valor[0],$valor[2]);
        }
    }

    $menu1->show();
    
    $menu2 = new Menu();
    $menu2->add_item('link','+ Novo Projeto','?fase=projetoNovo');
    $menu2->show();
    
    # Menu de Etiquetas
    $dadosEtiquetas = $projeto->listaEtiquetas();
    $numEtiquetas = $projeto->numeroEtiquetas();

    # Inicia o menu
    $menu1 = new Menu();
    $menu1->add_item('titulo','Etiquetas');

    # Se existir alguma etiqueta percorre
    # o array e monta o menu
    if($numEtiquetas>0){
        # Percorre o array 
        foreach ($dadosEtiquetas as $valor) {                    
            $menu1->add_item('link',$valor[1],'?fase=projetoEtiqueta&idEtiqueta='.$valor[0]);
        }
    }

    $menu1->show();
    
    $menu2 = new Menu();
    $menu2->add_item('link','+ Nova Etiqueta','?fase=etiquetaNova');
    $menu2->show();

    $grid->fechaColuna();
    
    switch ($fase){
        case "ínicial" :
            
            $grid->abreColuna(9);
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
            
        ###########################################################
            
        case "projeto" :
            
            $grid->abreColuna(9);
            
            # Pega os dados do projeto pesquisado
            $projetoPesquisado = $projeto->listaProjetosAtivos($idProjeto);
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(9);
            p($projetoPesquisado[1],'descricaoProjetoTitulo');
            p($projetoPesquisado[2],'descricaoProjeto');
            $grid->fechaColuna();
            $grid->abreColuna(3);
                
                $menu2 = new Menu();
                $menu2->add_item('link','+ Nova Tarefa','?fase=tarefaNova&idProjeto='.$idProjeto);
                $menu2->show();
                
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            
            # Exibe as tarefas com data
            $projeto->exibeTarefas($idProjeto,FALSE,TRUE);
            
            # Exibe as tarefas sem data
            $projeto->exibeTarefas($idProjeto,FALSE,FALSE);
            
            # Exibe as tarefas completadas
            $projeto->exibeTarefas($idProjeto,TRUE);
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                 
        ###########################################################
            
        case "projetoEtiqueta" :
            
            $grid->abreColuna(9);
            
            # Pega os dados da etiqueta pesquisado
            $etiquetaPesquisada = $projeto->get_dadosEtiqueta($idEtiqueta);
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(9);
            p($etiquetaPesquisada[1],'descricaoProjetoTitulo');
            p("Tarefas com a etiqueta: ".$etiquetaPesquisada[1],'descricaoProjeto');
             $grid->fechaColuna();
            $grid->abreColuna(3);
                
                $menu2 = new Menu();
                $menu2->add_item('link','Editar','?fase=etiquetaNova&idEtiqueta='.$idEtiqueta);
                $menu2->show();
                
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            
            # Exibe as tarefas com data
            $projeto->exibeTarefasEtiqueta($idEtiqueta,FALSE,TRUE);
            
            # Exibe as tarefas sem data
            $projeto->exibeTarefasEtiqueta($idEtiqueta,FALSE,FALSE);
            
            # Exibe as tarefas completadas
            $projeto->exibeTarefasEtiqueta($idEtiqueta,TRUE);
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                 
        ###########################################################   
            
         case "projetoNovo" :
             
            $grid->abreColuna(9);
             
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
            
            p("Novo Projeto","f18");
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            br();
            
            # Formulário
            $form = new Form('?fase=validaProjeto');        
                    
            # projeto
            $controle = new Input('projeto','texto','Nome do Projeto:',1);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Nome do rojeto');
            $controle->set_title('O nome do Projeto a ser criado');
            $form->add_item($controle);
            
            # descrição
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,5));
            $controle->set_linha(2);
            $controle->set_title('A descrição detalhda do projeto');
            $controle->set_placeholder('Descrição');
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
            
        case "validaProjeto" :
            
            # Recuperando os valores
            $projeto = post('projeto');
            $descricao = post('descricao');
            
            $id = NULL;
            
            # Cria arrays para gravação
            $arrayNome = array("projeto","descricao","ativo","grupo");
            $arrayValores = array($projeto,$descricao,1,"Geral");
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$id,"tbprojeto","idProjeto");
            loadPage("?");
            break;
        
        ###########################################################  
            
        case "tarefaNova" :
                        
            $grid->abreColuna(9);
            
            # Verifica se é incluir ou editar
            if(!is_null($idTarefa)){
                # Pega os dados dessa etiqueta
                $dados = $projeto->get_dadosTarefas($idTarefa);
            }else{
                $dados = array(NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
            }
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
            
            p($projeto->get_nomeProjeto($idProjeto)." - Nova Tarefa","f18");
            
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            br();
            
            # Pega os dados da combo etiqueta
            $selectetiqueta = 'SELECT idEtiqueta, 
                                     etiqueta
                                FROM tbprojetoetiqueta
                               ORDER BY etiqueta';
            
            $result = $intra->select($selectetiqueta);
            array_unshift($result, array(NULL,NULL)); # Adiciona o valor de nulo
            
            # Formuário
            $form = new Form('?fase=validaTarefa&idTarefa='.$idTarefa);        
                    
            # tarefa
            $controle = new Input('tarefa','texto','Tarefa:',1);
            $controle->set_size(200);
            $controle->set_linha(1);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Tarefa');
            $controle->set_title('A tarefa a ser executada');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # descrição
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,10));
            $controle->set_linha(2);
            $controle->set_title('A descrição detalhda do tarefa');
            $controle->set_placeholder('Descrição da tarefa');
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # dataInicial
            $controle = new Input('dataInicial','data','Data:',1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(3);
            $controle->set_title('A data inicial da tarefa');
            $controle->set_placeholder('A Data Inicial');
            $controle->set_valor($dados[4]);
            $form->add_item($controle);
            
            # dataFinal
            $controle = new Input('dataFinal','data','Data da Conclusão:',1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(3);
            $controle->set_title('A data da conclusão da tarefa');
            $controle->set_placeholder('A Data da conclusão');
            $controle->set_valor($dados[5]);
            $form->add_item($controle);
            
            # etiqueta
            $controle = new Input('idEtiqueta','combo','Etiqueta:',1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(3);
            $controle->set_placeholder('Etiqueta');
            $controle->set_title('Uma etiqueta para ajudar na busca');
            $controle->set_array($result);
            $controle->set_valor($dados[7]);
            $form->add_item($controle);
            
            # idProjeto
            $controle = new Input('idProjeto','hidden','',1);
            $controle->set_size(20);
            $controle->set_linha(4);
            $controle->set_valor($idProjeto);
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
            
        case "validaTarefa" :
            
            # Recuperando os valores
            $tarefa = post('tarefa');
            $descricao = post('descricao');
            $dataInicial = post('dataInicial');
            $dataFinal = post('dataFinal');
            $idProjeto = post('idProjeto');
            $idEtiqueta = post('idEtiqueta');
                      
            # Cria arrays para gravação
            $arrayNome = array("tarefa","descricao","dataInicial","dataFinal","idProjeto","feito","idEtiqueta");
            $arrayValores = array($tarefa,$descricao,$dataInicial,$dataFinal,$idProjeto,0,$idEtiqueta);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idTarefa,"tbprojetoTarefa","idTarefa");
            loadPage("?fase=projeto&idProjeto=".$idProjeto);
            break;
        
        ###########################################################  
        
        case "mudaTarefa" :
            
            # Pega os dados da tarefa
            $valor = $projeto->get_dadosTarefas($idTarefa);
            
            # Verifica o valor de feito
            if($valor[6] == 1){
                $feito = 0;
            }else{
                $feito = 1;
            }
            
            # Cria arrays para gravação
            $arrayNome = array("feito");
            $arrayValores = array($feito);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idTarefa,"tbprojetoTarefa","idTarefa");
            
            if(is_null($idProjeto)){
                loadPage("?fase=projetoEtiqueta&idEtiqueta=".$idEtiqueta);
            }else if(is_null($idEtiqueta)){
                loadPage("?fase=projeto&idProjeto=".$idProjeto);
            }
            break;
            
        ###########################################################  
            
         case "etiquetaNova" :
             
            $grid->abreColuna(9);
             
            # Verifica se é incluir ou editar
            if(!is_null($idEtiqueta)){
                # Pega os dados dessa etiqueta
                $dados = $projeto->get_dadosEtiqueta($idEtiqueta);
            }else{
                $dados = array(NULL,NULL,NULL);
            } 
             
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
            
            p("Nova Etiqueta","f18");
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            br();
            
            # Formulário
            $form = new Form('?fase=validaEtiqueta&idEtiqueta='.$idEtiqueta);        
                    
            # projeto
            $controle = new Input('etiqueta','texto','Etiqueta:',1);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_col(4);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Etiqueta');
            $controle->set_title('A Etiqueta');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # cor
            $controle = new Input('cor','combo','Cor:',1);
            $controle->set_size(10);
            $controle->set_col(3);
            $controle->set_linha(1);
            $controle->set_title('A cor da etiqueta');
            $controle->set_placeholder('Cor');
            $controle->set_array(array("secondary","primary","success","warning","alert"));
            $controle->set_valor($dados[2]);
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
            
        case "validaEtiqueta" :
            
            # Recuperando os valores
            $etiqueta = post('etiqueta');
            $cor = post('cor');
                      
            # Cria arrays para gravação
            $arrayNome = array("etiqueta","cor");
            $arrayValores = array($etiqueta,$cor);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idEtiqueta,"tbprojetoEtiqueta","idEtiqueta");
            
            if(is_null($idEtiqueta)){
                loadPage("?");
            }else{
                loadPage("?fase=projetoEtiqueta&idEtiqueta=".$idEtiqueta);
            }
            break;
        
        ########################################################### 
            
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();  
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}