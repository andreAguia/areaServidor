<?php
/**
 * Cadastro e Atualizações do Sistema
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro')))									# Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    else
    { 
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
    $objeto->set_nome('Atualizações');

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar nos campos Versão e/ou Data:');
    $objeto->set_parametroValue($parametro);

    # ordenação
    if(is_null($orderCampo))
            $orderCampo = 1;

    if(is_null($orderTipo))
            $orderTipo = 'desc';

    # select da lista
    $objeto->set_selectLista('SELECT data,
                                     versao,
                                     alteracoes,
                                     idatualizacao
                                FROM tbatualizacao
                               WHERE versao LIKE "%'.$parametro.'%"
                                  OR data LIKE "%'.$parametro.'%" 
                            ORDER BY '.$orderCampo.' '.$orderTipo);	

    # select do edita
    $objeto->set_selectEdita('SELECT versao,
                                     data,
                                     alteracoes						    
                                FROM tbatualizacao
                               WHERE idatualizacao = '.$id);

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
    $objeto->set_label(array("Data","Versão","Alterações"));
    $objeto->set_width(array(10,10,70));		
    $objeto->set_align(array("center","center","left"));
    $objeto->set_funcao(array("date_to_php"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbatualizacao');

    # Nome do campo id
    $objeto->set_idCampo('idatualizacao');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array( 
                        array ( 'nome' => 'versao',
                                'label' => 'Versão:',
                                'tipo' => 'texto',
                                'size' => 20,
                                'title' => 'Versão do Sistema.',
                                'required' => TRUE,
                                'autofocus' => TRUE,
                                'col' => 3,
                                'linha' => 1),
                        array ('linha' => 1,
                               'nome' => 'data',
                               'label' => 'Data:',
                               'tipo' => 'date',
                               'title' => 'Data da atualização', 
                               'col' => 3,
                               'size' => 15),
                        array ( 'nome' => 'alteracoes',
                                'label' => 'Alterações:',
                                'tipo' => 'textarea',
                                'size' => array(90,12),
                                'title' => 'Alterações detalhadas desta versão.',
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
            $objeto->$fase($id);		
            break;		
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("login.php");
}