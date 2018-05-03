<?php
/**
 * Cadastro de Computador
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
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $idProcesso = soNumeros(get('idProcesso'));

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))){                                # Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));  # passa o parametro da session para a variavel parametro retirando as aspas
    }else{ 
        $parametro = post('parametro');                             # Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro',$parametro);                 # transfere para a session para poder recuperá-lo depois
    }

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('order_tipo');

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Cadastro de Processos');

    # botão de voltar da lista
    $objeto->set_voltarLista('areaServidor.php');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);

    # ordenação
    if(is_null($orderCampo)){
            $orderCampo = 1;
    }

    if(is_null($orderTipo)){
            $orderTipo = 'asc';
    }

    # select da lista
    $objeto->set_selectLista('SELECT data,
                                     numero,
                                     assunto,
                                     idProcesso,
                                     idProcesso
                                FROM tbprocesso
                               WHERE data LIKE "%'.$parametro.'%"
                                  OR numero LIKE "%'.$parametro.'%"	
                                  OR assunto LIKE "%'.$parametro.'%"		    
                            ORDER BY '.$orderCampo.' '.$orderTipo);	

    # select do edita
    $objeto->set_selectEdita('SELECT data,
                                     numero,
                                     assunto							    
                                FROM tbprocesso
                               WHERE idProcesso = '.$idProcesso);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');
    
    # Retira os botões de editar e excluir padrões
    $objeto->set_botaoExcluir(FALSE);
    $objeto->set_botaoEditar(FALSE);

    # Parametros da tabela
    $objeto->set_label(array("Data","Número","Assunto","Movimentação"));
    $objeto->set_width(array(15,15,60));		
    $objeto->set_align(array("center","center","left"));
    $objeto->set_funcao(array("date_to_php"));
    
    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new BotaoGrafico();
    $botao->set_label('');
    $botao->set_title('Movimentação do processo');
    $botao->set_url('?fase=movimentacao&idProcesso='.$idProcesso);
    $botao->set_image(PASTA_FIGURAS.'movimentacao.png',20,20);

    # Coloca o objeto link na tabela			
    $objeto->set_link(array("","","",$botao));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbprocesso');

    # Nome do campo id
    $objeto->set_idCampo('idProcesso');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);
    
    # Campos para o formulario
    $objeto->set_campos(array( 
                        array ( 'nome' => 'data',
                                'label' => 'data:',
                                'tipo' => 'data',
                                'size' => 20,
                                'title' => 'data do processo',
                                'required' => TRUE,
                                'autofocus' => TRUE,
                                'col' => 3,
                                'linha' => 1),
                        array ( 'linha' => 1,
                                'nome' => 'numero',
                                'label' => 'Processo:',
                                'tipo' => 'texto',
                                'title' => 'O numero do Processo',
                                'required' => TRUE,
                                'col' => 3,
                                'size' => 15),
                        array ( 'nome' => 'assunto',
                                'label' => 'Assunto:',
                                'tipo' => 'textarea',
                                'size' => array(90,5),
                                'title' => 'Assunto.',
                                'col' => 12,
                                'linha' => 2)	 	 	 	 	 	 
                    ));

    # Log
    $objeto->set_idUsuario($idUsuario);
    
    ################################################################
    switch ($fase)
    {
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :	
        case "excluir" :	
        case "gravar" :		
            $objeto->$fase($idProcesso);		
            break;
        
    ################################################################    
        
        case "movimentacao" :
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);
            
            # Cria um menu
            $menu1 = new MenuBar();

            # Sair 
            $linkVoltar = new Link("Voltar","?");
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Voltar');
            $menu1->add_link($linkVoltar,"left");
            
            # Inserir Movimento 
            $linkVoltar = new Link("Incluir Movimento","?fase=movimentacaoIncluir&idProcesso=".$idProcesso);
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Incluir Movimento');
            $menu1->add_link($linkVoltar,"right");

            $menu1->show();
            $grid = new Grid();
            $grid->abreColuna(3);
            
            
            Gprocessos::exibeProcesso($idProcesso);
            
            $grid->fechaColuna();
            $grid->abreColuna(9);
                $lista = new ListaMovimentos($idProcesso);
                $lista->show();
            $grid->fechaColuna();
            $grid->fechaGrid();
            
            $grid->fechaColuna();
            $grid->fechaGrid();
            break;
        
    ################################################################
        
        case "movimentacaoIncluir" :
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);
            
            # Cria um menu
            $menu1 = new MenuBar();

            # Sair 
            $linkVoltar = new Link("Voltar","?");
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Voltar');
            $menu1->add_link($linkVoltar,"left");

            $menu1->show();
            $grid = new Grid();
            $grid->abreColuna(3);
            
            
            Gprocessos::exibeProcesso($idProcesso);
            
            $grid->fechaColuna();
            $grid->abreColuna(9);
            
            # Verifica se é incluir ou editar
            if(!is_null($idTarefa)){
                # Pega os dados dessa etiqueta
                $dados = $projeto->get_dadosTarefa($idTarefa);
                $titulo = "Editar Tarefa";
            }else{
                $dados = array(NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
                $titulo = "Nova Tarefa";
            }
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(8,9,10);
                p($projeto->get_nomeProjeto($idProjeto)." - ".$titulo,"f18");
            $grid->fechaColuna();
            $grid->abreColuna(4,3,2);
                $link = new Button("Cancelar","?");
                $link->show();
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
            $form = new Form('?fase=validaTarefa&idTarefa='.$idTarefa.'&hoje='.$hojeGet);        
                    
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
            $controle->set_col(6);
            $controle->set_title('A data inicial da tarefa');
            $controle->set_placeholder('A Data Inicial');
            $controle->set_valor($dados[4]);
            $form->add_item($controle);
            
            # dataFinal
            $controle = new Input('dataFinal','data','Data da Conclusão:',1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(6);
            $controle->set_title('A data da conclusão da tarefa');
            $controle->set_placeholder('A Data da conclusão');
            $controle->set_valor($dados[5]);
            $form->add_item($controle);
            
            # etiqueta
            $controle = new Input('idEtiqueta','combo','Etiqueta:',1);
            $controle->set_size(20);
            $controle->set_linha(4);
            $controle->set_col(6);
            $controle->set_placeholder('Etiqueta');
            $controle->set_title('Uma etiqueta para ajudar na busca');
            $controle->set_array($result);
            $controle->set_valor($dados[7]);
            $form->add_item($controle);
            
            # idProjeto
            $controle = new Input('idProjeto','hidden','',1);
            $controle->set_size(20);
            $controle->set_linha(5);
            $controle->set_valor($idProjeto);
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
            $dataInicial = post('dataInicial');
            $dataFinal = post('dataFinal');
            $idProjeto = post('idProjeto');
            $idEtiqueta = post('idEtiqueta');
            $pendente = post('pendente');
            
            # Força a tarefa pendente quando é inclusão
            if(is_null($idTarefa)){
                $pendente = 1;
            }
                      
            # Cria arrays para gravação
            $arrayNome = array("tarefa","descricao","dataInicial","dataFinal","idProjeto","pendente","idEtiqueta");
            $arrayValores = array($tarefa,$descricao,$dataInicial,$dataFinal,$idProjeto,$pendente,$idEtiqueta);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idTarefa,"tbprojetoTarefa","idTarefa");
            if($hojeGet){
                loadPage("?fase=hoje");
            }else{
                loadPage("?fase=projeto&idProjeto=".$idProjeto);
            }
            break;
        
        ###########################################################  
        
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("login.php");
}