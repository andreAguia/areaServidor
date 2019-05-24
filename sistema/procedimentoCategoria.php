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
    $objeto->set_selectLista ('SELECT numOrdem,
                                      visibilidade,
                                      categoria,
                                      descricao,
                                      idCategoria
                                 FROM tbprocedimentocategoria
                             ORDER BY numOrdem');
    # select do edita
    $objeto->set_selectEdita('SELECT numOrdem,
                                     visibilidade,
                                     categoria,
                                     descricao,                                     
                                     idCategoria
                                FROM tbprocedimentocategoria
                               WHERE idCategoria = '.$id);

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');

    # Parametros da tabela
    $objeto->set_label(array("numOrdem","Visibilidade","Categoria","Descrição"));
    $objeto->set_width(array(5,10,20,50));
    $objeto->set_align(array("center","center","left","left"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbprocedimentocategoria');

    # Nome do campo id
    $objeto->set_idCampo('idCategoria');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

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
               'nome' => 'categoria',
               'label' => 'Categoria:',
               'tipo' => 'texto',
               'required' => TRUE,
               'autofocus' => TRUE,
               'col' => 8,
               'size' => 100),
        array ('linha' => 2,
               'nome' => 'descricao',
               'title' => 'Descrição detalhada da Categoria',
               'label' => 'Descrição:',
               'tipo' => 'texto',
               'col' => 12,
               'size' => 250)
        ));

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