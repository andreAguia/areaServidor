<?php
/**
 * Gerencia de Usuários
 *  
 * By Alat
 */

# Reservado para o servidor logado
$idusuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idusuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));    
    
    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro')))					# Se o parametro n?o vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    else
    { 
        $parametro = post('parametro');                # Se vier por post, retira as aspas e passa para a variavel parametro
        set_session('sessionParametro',$parametro);    # transfere para a session para poder recuperá-lo depois
    }

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Usuários');	

    # botão salvar
    $objeto->set_botaoSalvarGrafico(false);

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

    # controle de pesquisa
    #$objeto->set_parametroLabel('Pesquisar');
    #$objeto->set_parametroValue($parametro);

    # ordenação
    if(is_null($orderCampo))
         $orderCampo = "1";

    if(is_null($orderTipo))
        $orderTipo = 'asc';

    # select da lista
    $objeto->set_selectLista ('SELECT usuario,
                                      idservidor,
                                      ultimoAcesso,
                                      idusuario
                                 FROM tbusuario
                                WHERE usuario LIKE "%'.$parametro.'%"
                             ORDER BY '.$orderCampo.' '.$orderTipo);

    # select do edita
    $objeto->set_selectEdita('SELECT usuario,
                                     obs
                                FROM tbusuario
                               WHERE idusuario = '.$id);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    #$objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');

    # Parametros da tabela
    $objeto->set_label(array("Usuário","Nome","Último Acesso"));
    $objeto->set_width(array(20,50,20));
    $objeto->set_align(array("center"));

    #$objeto->set_classe(array(null,null,null,null,null,null,"pessoal"));
    #$objeto->set_metodo(array(null,null,null,null,null,null,"get_lotacaoNumServidores"));
    #$objeto->set_function(array(null,null,null,null,null,null,null,"get_lotacaoNumServidores"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbusuario');

    # Nome do campo id
    $objeto->set_idCampo('idusuario');

    # Campos para o formulario
    $objeto->set_campos(array(
        array ('linha' => 1,
                'col' => 4, 
               'nome' => 'usuario',
               'label' => 'Usuário:',
               'tipo' => 'texto',
               'autofocus' => true,
               'size' => 15)));

    # Log
    $objeto->set_idusuario($idusuario);

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
}