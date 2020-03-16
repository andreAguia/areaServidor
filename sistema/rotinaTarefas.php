<?php
/**
 * Cadastro de Manual de procedimentos
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
    $idManualProcedimento = get('idManualProcedimento',get_session('idManualProcedimento'));
    
    set_session('idManualProcedimento',$idManualProcedimento);

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))){                                     # Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    }else{ 
        $parametro = post('parametro');								# Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro',$parametro);			 		# transfere para a session para poder recuperá-lo depois
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
    $objeto->set_nome('Tarefas');

    # botão de voltar da lista
    $objeto->set_voltarLista('rotina.php');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);
    
    # select da lista
    $objeto->set_selectLista('SELECT responsavel,
                                     tarefa,
                                     obs,
                                     idManualTarefa
                                FROM tbmanualtarefa JOIN tbmanualprocedimento USING (idManualProcedimento)
                               WHERE idManualProcedimento = '.$idManualProcedimento.'  
                                  AND (responsavel LIKE "%'.$parametro.'%"
                                  OR tarefa LIKE "%'.$parametro.'%"	
                                  OR obs LIKE "%'.$parametro.'%"
                                  OR titulo LIKE "%'.$parametro.'%")');	

    # select do edita
    $objeto->set_selectEdita('SELECT idManualProcedimento,
                                     responsavel,
                                     tarefa,
                                     obs						    
                                FROM tbmanualtarefa
                               WHERE idManualTarefa = '.$id);

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_rowspan(1);
    $objeto->set_grupoCorColuna(1);

    # Parametros da tabela
    $objeto->set_label(array("Responsável","Tarefa","Observação"));
    $objeto->set_width(array(10,40,40));
    $objeto->set_align(array("center","left","left"));
    $objeto->set_funcao(array(NULL,"exibeTarefa",NULL));
    
    # Esconde o botão iniciar para usar um diferente na rotina de listar
    $objeto->set_botaoIncluir(FALSE);
    $objeto->set_botaoVoltarLista(FALSE);
    
    $objeto->set_numeroOrdem(TRUE);
    
    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbmanualtarefa');

    # Nome do campo id
    $objeto->set_idCampo('idManualTarefa');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);
    
    # Pega os dados da combo Usuario
    $comboProcedimento = $intra->select('SELECT idManualProcedimento,
                                           titulo
                                      FROM tbmanualprocedimento
                                  ORDER BY titulo');
    array_unshift($comboProcedimento, array(NULL,NULL)); 


    # Campos para o formulario
    $objeto->set_campos(array( 
                        array ('linha' => 1,
                               'nome' => 'idManualProcedimento',
                               'label' => 'Procedimento:',
                               'tipo' => 'hidden',
                               'valor' => $idManualProcedimento,
                               'size' => 15),
                        array ( 'nome' => 'responsavel',
                                'label' => 'Responsavel:',
                                'tipo' => 'texto',
                                'size' => 50,
                                'title' => 'Responsável.',
                                'required' => TRUE,
                                'autofocus' => TRUE,
                                'col' => 12,
                                'linha' => 2),
                        array ( 'nome' => 'tarefa',
                                'label' => 'Tarefa:',
                                'tipo' => 'editor',
                                'size' => array(90,5),
                                'title' => 'Tarefa.',
                                'tagHtml' => TRUE,
                                'col' => 12,
                                'linha' => 3), 
                        array ( 'nome' => 'obs',
                                'label' => 'Obs:',
                                'tipo' => 'editor',
                                'size' => array(90,5),
                                'title' => 'Obs.',
                                'tagHtml' => TRUE,
                                'col' => 12,
                                'linha' => 4), 
                    ));

    # Log
    $objeto->set_idUsuario($idUsuario);
    
    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :   
            $grid = new Grid();
            $grid->abreColuna(12);
            
            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $botaoVoltar = new Link("Voltar","manualProcedimento.php");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Voltar a página anterior');
            $botaoVoltar->set_accessKey('V');
            $menu1->add_link($botaoVoltar,"left");
            
            # Relatórios
            $imagem = new Imagem(PASTA_FIGURAS.'print.png',NULL,15,15);
            $botaoRel = new Button();
            $botaoRel->set_title("Relatório");
            $botaoRel->set_url("#");
            $botaoRel->set_target("_blank");
            $botaoRel->set_imagem($imagem);
            $menu1->add_link($botaoRel,"right");
            
            # Incluir
            $botaoVoltar = new Link("Incluir Tarefa","?fase=editar");
            $botaoVoltar->set_class('button');
            $botaoVoltar->set_title('Inclui uma tarefa.');
            $menu1->add_link($botaoVoltar,"right");

            $menu1->show();
            
            $grid->fechaColuna();
            
            # Área Lateral
            
            $grid->abreColuna(3);

            # Exibe dados da vaga
            $manual = new ManualProcedimento();
            $manual->exibeDadosProcedimento($idManualProcedimento);
            
            $grid->fechaColuna();
            
            # Área Principal
            
            $grid->abreColuna(9);
            
                $objeto->listar();
            
            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

        case "editar" :	
        case "excluir" :	
        case "gravar" :		
            $objeto->$fase($id);		
            break;		
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("login.php");
}