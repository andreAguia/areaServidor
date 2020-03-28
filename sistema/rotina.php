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
    $objeto->set_nome('Procedimentos');

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);

    # ordenação
    if(is_null($orderCampo))
            $orderCampo = 1;

    if(is_null($orderTipo))
            $orderTipo = 'asc';

    # select da lista
    $objeto->set_selectLista('SELECT categoria,
                                     titulo,
                                     descricao,
                                     idManualProcedimento
                                FROM tbmanualprocedimento
                               WHERE categoria LIKE "%'.$parametro.'%"
                                  OR titulo LIKE "%'.$parametro.'%"	
                                  OR descricao LIKE "%'.$parametro.'%"	
                            ORDER BY '.$orderCampo.' '.$orderTipo);	

    # select do edita
    $objeto->set_selectEdita('SELECT categoria,
                                     titulo,
                                     descricao						    
                                FROM tbmanualprocedimento
                               WHERE idManualProcedimento = '.$id);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(array("Categoria","Título","Descrição"));
    $objeto->set_width(array(15,35,40));		
    $objeto->set_align(array("center","center","left"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbmanualprocedimento');

    # Nome do campo id
    $objeto->set_idCampo('idManualProcedimento');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array( 
                        array ( 'nome' => 'categoria',
                                'label' => 'Categoria:',
                                'tipo' => 'texto',
                                'size' => 20,
                                'title' => 'Categoriar do procedimento.',
                                'required' => TRUE,
                                'autofocus' => TRUE,
                                'col' => 4,
                                'linha' => 1),
                        array ( 'nome' => 'titulo',
                                'label' => 'Título:',
                                'tipo' => 'texto',
                                'size' => 100,
                                'title' => 'Categoriar do procedimento.',
                                'required' => TRUE,
                                'col' => 8,
                                'linha' => 1),
                        array ( 'nome' => 'descricao',
                                'label' => 'Descreicao:',
                                'tipo' => 'textarea',
                                'size' => array(90,5),
                                'title' => 'Descrição do procedimento.',
                                'col' => 12,
                                'linha' => 2)	 	 	 	 	 	 
                    ));

    # Log
    $objeto->set_idUsuario($idUsuario);
    
    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :	
            loadPage("rotinaTarefas.php?idManualProcedimento=".$id);
            break;
        
        case "excluir" :	
        case "gravar" :		
            $objeto->$fase($id);		
            break;		
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("login.php");
}