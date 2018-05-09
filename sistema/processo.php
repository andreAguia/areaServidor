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
    $processo = new Processo();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');
    
    # Define como padrão a máscara do processo novo
    $tipoProcesso = get("tipoProcesso","processoNovo");

    # pega o id se tiver)
    $idProcesso = soNumeros(get('idProcesso'));
    $idProcessoMovimento = soNumeros(get('idProcessoMovimento'));

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
            $orderTipo = 'desc';
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
    $objeto->set_selectEdita('SELECT numero,
                                     data,
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
    $objeto->set_width(array(15,20,55));		
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
                        array ( 'linha' => 1,
                                'nome' => 'numero',
                                'label' => 'Processo:',
                                'tipo' => $tipoProcesso,
                                'title' => 'O numero do Processo',
                                'autofocus' => TRUE,
                                'required' => TRUE,
                                'unique' => TRUE,
                                'col' => 6,
                                'size' => 50),
                        array ( 'nome' => 'data',
                                'label' => 'data:',
                                'tipo' => 'data',
                                'size' => 20,
                                'title' => 'data do processo',
                                'required' => TRUE,
                                'col' => 3,
                                'linha' => 1),
                        array ( 'nome' => 'assunto',
                                'label' => 'Assunto:',
                                'tipo' => 'textarea',
                                'size' => array(90,5),
                                'title' => 'Assunto.',
                                'required' => TRUE,
                                'col' => 12,
                                'linha' => 2)	 	 	 	 	 	 
                    ));

    # Log
    $objeto->set_idUsuario($idUsuario);
    
    # Botões extras
    $botaoProcessoNovo = new Button("Processo Novo","#");
    $botaoProcessoAntigo = new Button("Processo Antigo","#");
    
    
    
    $objeto->set_botaoEditarExtra(array($botaoProcessoNovo,$botaoProcessoAntigo)); 
    
    ################################################################
    switch ($fase){
        
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :	
            $objeto->editar($idProcesso);
            
            $grid1 = new Grid();
            $grid1->abreColuna(12);
            br();
            
            # Informa os formatos
            $painel = new Callout("primary");
            $painel->abre();
            
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(6);
                p("Modelo de processo antigo","center");
                p("E-xx/xxxxxx/xxxx","center");
            $grid->fechaColuna();    
            $grid->abreColuna(6);    
                p("Modelo de processo novo","center");
                p("E-xx/xxx/xxxxxx/xxxx","center");
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            $painel->fecha();
            
            $grid1->fechaColuna();
            $grid1->fechaGrid(); 
            break;
            
        case "excluir" :
            $objeto->$fase($idProcesso);		
            break;
        
        case "gravar" :	
            $objeto->gravar($idProcesso,"");	
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
            
            # Verifica se é incluir ou editar
            if(!is_null($idProcessoMovimento)){
                # Pega os dados dessa etiqueta
                $dados = $processo->get_Movimento($idProcessoMovimento);
                $titulo = "Editar Movimento";
                $idProcesso = $dados[1];
            }else{
                $dados = array(NULL,NULL,NULL,NULL,NULL,NULL,NULL);
                $titulo = "Novo Movimento";
            }
            
            # Cria um menu
            $menu1 = new MenuBar();

            # Sair 
            $linkVoltar = new Link("Voltar","?fase=movimentacao&idProcesso=".$idProcesso);
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Voltar');
            $menu1->add_link($linkVoltar,"left");

            $menu1->show();
            $grid = new Grid();
            $grid->abreColuna(3);            
            
            Gprocessos::exibeProcesso($idProcesso);
            
            $grid->fechaColuna();
            $grid->abreColuna(9);
            
            tituloTable($titulo);
            br();
            
            # Pega os dados da combo setorCombo
            $selectLotacao = 'SELECT idlotacao, 
                                     concat(IFNULL(tblotacao.UADM,"")," - ",IFNULL(tblotacao.DIR,"")," - ",IFNULL(tblotacao.GER,"")," - ",IFNULL(tblotacao.nome,"")) as lotacao
                                FROM tblotacao
                               WHERE ativo
                               ORDER BY lotacao';
            
            $comboSetor = $servidor->select($selectLotacao);
            array_unshift($comboSetor, array(NULL,NULL)); # Adiciona o valor de nulo
            
            # Formuário
            $form = new Form('?fase=validaMovimento&idProcesso='.$idProcesso.'&idProcessoMovimento='.$idProcessoMovimento);        
                    
            # data
            $controle = new Input('data','data','Data:',1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_col(6);
            $controle->set_autofocus(TRUE);
            $controle->set_required(TRUE);
            $controle->set_title('A data do movimento');
            $controle->set_placeholder('A Data do Movimento');
            $controle->set_valor($dados[3]);
            $form->add_item($controle);
            
            # status
            $controle = new Input('status','combo','Status:',1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_col(6); 
            $controle->set_required(TRUE);
            $controle->set_title('Status do movimento');
            $controle->set_array(array(NULL,"Entrada","Saída"));
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # setorCombo
            $controle = new Input('setorCombo','combo','Origem ou destino dentro da UENF:',1);
            $controle->set_size(20);
            $controle->set_linha(2);
            $controle->set_col(6);
            $controle->set_title('Setor dentro da UENF');
            $controle->set_array($comboSetor);
            $controle->set_valor($dados[4]);
            $form->add_item($controle);
            
            # setorTexto
            $controle = new Input('setorTexto','texto','Origem ou destino fora da UENF:',1);
            $controle->set_size(200);
            $controle->set_linha(2);
            $controle->set_col(6);
            $controle->set_title('Setor de fora da UENF');
            $controle->set_valor($dados[5]);
            $form->add_item($controle);
            
            # motivo
            $controle = new Input('motivo','textarea','Motivo:',1);
            $controle->set_size(array(80,5));
            $controle->set_linha(4);
            $controle->set_title('O motivo do movimento');
            $controle->set_valor($dados[6]);
            $form->add_item($controle);
            
            # idProcesso
            $controle = new Input('idProcesso','hidden','',1);
            $controle->set_size(20);
            $controle->set_linha(5);
            $controle->set_valor($idProcesso);
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
            
        case "validaMovimento" :
            
            # Recuperando os valores
            $data = post('data');
            $status = post('status');
            $setorCombo = post('setorCombo');
            $setorTexto = post('setorTexto');
            $motivo = post('motivo');
            $idProcesso = post('idProcesso');
            
            # Variáveis da mensagem de erro
            $erro = 0;		    // flag de erro: 1 - tem erro; 0 - não tem	
            $msgErro = NULL;        // repositório de mensagens de erro
            
            # Passa para nullo
            if($setorCombo == 0){
                $setorCombo = NULL;
            }
            
            # Critica o setor
            if((vazio($setorCombo)) AND (vazio($setorTexto))){
                $msgErro.='Tem que ser definido um setor de origem/destino!\n';
                $erro = 1;
            }
                      
            if ($erro == 0){
                # Cria arrays para gravação
                $arrayNome = array("status","data","setorCombo","SetorTexto","motivo","idProcesso");
                $arrayValores = array($status,$data,$setorCombo,$setorTexto,$motivo,$idProcesso);

                # Grava	
                $intra->gravar($arrayNome,$arrayValores,$idProcessoMovimento,"tbprocessomovimento","idProcessoMovimento");
                
                aguarde();

                loadPage("?fase=movimentacao&idProcesso=".$idProcesso);
            }else{
                alert($msgErro);
                back(1);
            }
            break;
        
        ###########################################################  
        
        case "movimentacaoExcluir" :
            
            $tabela = "tbprocessomovimento";
            $idNome = "idProcessoMovimento";
            
            # Pega os dados
            $dados = $processo->get_Movimento($idProcessoMovimento);
            
            # Pega o processo
            $idProcesso = $dados[1];
            
            $intra->excluir($idProcessoMovimento,$tabela,$idNome);
            loadPage("?fase=movimentacao&idProcesso=".$idProcesso);
            break;
        
        ###########################################################
        
        
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("login.php");
}