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

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Categorias');

    # botão de voltar da lista
    $objeto->set_voltarLista('procedimentos.php');

    # select da lista
    $objeto->set_selectLista ('SELECT idCategoria,
                                      categoria,
                                      descricao
                                 FROM tbprocedimentoCategoria
                             ORDER BY categoria');
    # select do edita
    $objeto->set_selectEdita('SELECT categoria,
                                     descricao,
                                     idCategoria
                                FROM tbprocedimentoCategoria
                               WHERE idCategoria = '.$id);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('procedimentos.php');

    # Parametros da tabela
    $objeto->set_label(array("Id","Categoria","Descrição"));
    #$objeto->set_width(array(10,40,40));
    $objeto->set_align(array("center","left","left"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbprocedimentoCategoria');

    # Nome do campo id
    $objeto->set_idCampo('idCategoria');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array ('linha' => 1,
               'nome' => 'categoria',
               'label' => 'Categoria:',
               'tipo' => 'texto',
               'required' => TRUE,
               'autofocus' => TRUE,
               'col' => 12,
               'size' => 100),
        array ('linha' => 2,
               'nome' => 'descricao',
               'title' => 'Descrição detalhada da Categoria',
               'label' => 'Descrição:',
               'tipo' => 'textarea',
               'size' => array(80,5))));

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