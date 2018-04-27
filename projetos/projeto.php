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
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    # Cabeçalho
    AreaServidor::cabecalho();

    botaoVoltar('../sistema/administracao.php');
    
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
            $menu1->add_item('link',$valor[1],'#');
        }
    }

    $menu1->show();
    
    $menu2 = new Menu();
    $menu2->add_item('link','+ Novo Etiqueta','?fase=EtiquetaNova');
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
            $grid->abreColuna(10);
            p($projetoPesquisado[1],'descricaoProjetoTitulo');
            p($projetoPesquisado[2],'descricaoProjeto');
            $grid->fechaColuna();
            $grid->abreColuna(2);
            
                $botao = new BotaoGrafico();
                $botao->set_url('?fase=tarefaNova&idProjeto='.$idProjeto);
                $botao->set_image(PASTA_FIGURAS.'adicionar.png',25,25);
                $botao->set_title('Adicionar uma tarefa');
                $botao->show();
                
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            
            # Exibe as tarefas
            $projeto->exibeTarefas($idProjeto);
            
            # Exibe as tarefas completadas
            $projeto->exibeTarefas($idProjeto,TRUE);
            
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
                $dados = array(NULL,NULL,NULL,NULL,NULL,NULL);
            }
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
            
            p($projeto->get_nomeProjeto($idProjeto)." - Nova Tarefa","f18");
            
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            br();
            
            # Formuário
            $form = new Form('?fase=validaTarefa&idTarefa='.$idTarefa);        
                    
            # tarefa
            $controle = new Input('tarefa','texto','Tarefa:',1);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Tarefa');
            $controle->set_title('A tarefa a ser executada');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # descrição
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,5));
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
            $controle = new Input('etiqueta','combo','Etiqueta:',1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(3);
            $controle->set_placeholder('Etiqueta');
            $controle->set_title('Uma etiqueta para ajudar na busca');
            $controle->set_array($dadosEtiquetas);
            $controle->set_valor($dados[1]);
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
            
            $id = NULL;
            
            # Cria arrays para gravação
            $arrayNome = array("tarefa","descricao","dataInicial","dataFinal","idProjeto","feito","idEtiqueta");
            $arrayValores = array($tarefa,$descricao,$dataInicial,$dataFinal,$idProjeto,0,$idEtiqueta);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$id,"tbprojetoTarefa","idTarefa");
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
            loadPage("?fase=projeto&idProjeto=".$idProjeto);
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();  
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}