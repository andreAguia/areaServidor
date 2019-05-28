<?php
/**
 * Cadastro de Categoria de Procedimentos
 *  
 * By Alat
 */

# Reservado para o servidor logado
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,2);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));
    
    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))){					# Se o parametro n?o vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    }else{ 
        $parametro = post('parametro');                # Se vier por post, retira as aspas e passa para a variavel parametro
        set_session('sessionParametro',$parametro);    # transfere para a session para poder recuperá-lo depois
    }

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
    $objeto->set_voltarLista('procedimentos.php');
    
    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $objeto->set_selectLista ('SELECT FILHO.numOrdem,
                                      IF(FILHO.visibilidade = 1,"Público","Admin"),
                                      PAI.titulo,
                                      FILHO.titulo,
                                      FILHO.descricao,
                                      FILHO.idProcedimento
                                 FROM tbprocedimento FILHO LEFT JOIN tbprocedimento PAI ON (FILHO.idPai = PAI.idProcedimento)
                                WHERE FILHO.titulo LIKE "%'.$parametro.'%"
                                   OR FILHO.descricao LIKE "%'.$parametro.'%" 
                             ORDER BY PAI.titulo, FILHO.numOrdem');
    # select do edita
    $objeto->set_selectEdita('SELECT numOrdem,
                                     visibilidade,
                                     idPai,
                                     titulo,
                                     descricao,
                                     link,
                                     textoProcedimento
                                FROM tbprocedimento
                               WHERE idProcedimento = '.$id);

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');

    # Parametros da tabela
    $objeto->set_label(array("Ordem","Visibilidade","Pai","Título","Descrição"));
    #$objeto->set_width(array(5,10,10,25,35));
    $objeto->set_align(array("center","center","left","left","left"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbprocedimento');

    # Nome do campo id
    $objeto->set_idCampo('idProcedimento');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);
    
    # Pega os dados da combo de Categoria
    $result3 = $intra->select('SELECT idProcedimento,
                                      titulo
                                 FROM tbprocedimento
                             ORDER BY titulo');
    array_push($result3, array(0,"Principal"));

    # Campos para o formulario
    $objeto->set_campos(array(
        array ('linha' => 1,
               'nome' => 'numOrdem',
               'autofocus' => TRUE,
               'label' => 'numOrdem:',
               'tipo' => 'texto',
               'required' => TRUE,
               'col' => 2,
               'size' => 4),
        array ('linha' => 1,
               'nome' => 'visibilidade',
               'label' => 'Visibilidade:',
               'tipo' => 'combo',
               'required' => TRUE,
               'array' => array(array(1,"Público"),array(2,"Admin")),
               'col' => 2,
               'size' => 15),
        array ('linha' => 1,
               'nome' => 'idPai',
               'label' => 'Pai:',
               'tipo' => 'combo',               
               'required' => TRUE,
               'array' => $result3,
               'col' => 4,
               'size' => 30),
        array ('linha' => 1,
               'nome' => 'titulo',
               'label' => 'Título:',
               'tipo' => 'texto',
               'required' => TRUE,
               'autofocus' => TRUE,
               'col' => 4,
               'size' => 100),
        array ('linha' => 2,
               'nome' => 'descricao',
               'title' => 'Descrição detalhada da Categoria',
               'label' => 'Descrição:',
               'tipo' => 'texto',
               'col' => 6,
               'size' => 250),
        array ('linha' => 2,
               'nome' => 'link',
               'title' => 'link',
               'label' => 'link:',
               'tipo' => 'texto',
               'col' => 6,
               'size' => 250),
        array ('linha' => 3,
               'nome' => 'textoProcedimento',
               'label' => 'Texto:',
               'tipo' => 'editor',
               'tagHtml' => TRUE,
               'size' => array(90,5),
               'title' => 'Texto')));

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase){
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
    loadPage("../../areaServidor/sistema/login.php");
}