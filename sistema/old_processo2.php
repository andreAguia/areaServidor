<?php
/**
 * Cadastro de Processos
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,5);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));
    
    # Define como padrão a máscara do processo novo
    #$tipoProcesso = get("tipoProcesso","processoNovo");

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
    $objeto->set_nome('Sistema de Controle de Processos');

    # botão de voltar da lista
    #$objeto->set_voltarLista('areaServidor.php');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);

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
                                      ORDER BY 1 desc');	

    # select do edita
    $objeto->set_selectEdita('SELECT data,
                                     numero,
                                     assunto						    
                                FROM tbprocesso
                               WHERE idProcesso = '.$id);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    
    # Esconde o botão editar
    $objeto->set_botaoEditar(FALSE);
    
    # Esconde o botão voltar da lista
    $objeto->set_botaoVoltarLista(FALSE);
    
    # Altera o link de volta do formulário
    if(!is_null($id)){
        $objeto->set_voltarForm('processoMovimentacao.php?idProcesso='.$id);
        $objeto->set_linkListar('processoMovimentacao.php?idProcesso='.$id);
    }

    # Parametros da tabela
    $objeto->set_label(array("Data","Número","Assunto","Movimentação"));
    $objeto->set_width(array(10,20,65));		
    $objeto->set_align(array("center","center","left"));
    $objeto->set_funcao(array("date_to_php",NULL,"retiraAcento"));	
    
    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new Link(NULL,'processoMovimentacao.php?idProcesso=','Movimentação do Processo');
    $botao->set_image(PASTA_FIGURAS.'movimentacao.png',20,20);
    $objeto->set_link(array(NULL,NULL,NULL,$botao));

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
                                'tipo' => 'texto',
                                'title' => 'O numero do Processo',
                                'autofocus' => TRUE,
                                'required' => TRUE,
                                'unique' => TRUE,
                                'col' => 5,
                                'size' => 40),
                        array ( 'nome' => 'data',
                                'label' => 'Data:',
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
    
    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :
            # Inicia o sesion do processo
            set_session('idProcesso');
            
            $objeto->listar();
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