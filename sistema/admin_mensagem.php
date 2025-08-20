<?php

/**
 * Cadastro deComputador
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase', 'listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Pega o parametro de pesquisa (se tiver)
    $parametro = retiraAspas(post('parametro', get_session('sessionParametro')));
    set_session('sessionParametro', $parametro);

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();
    br();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Mensagem');

    # botão de voltar da lista
    $objeto->set_voltarLista('areaServidor.php?fase=menuAdmin');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $objeto->set_selectLista("SELECT idMensagem,
                                     mensagem
                                FROM tbmensagem
                               WHERE mensagem LIKE '%{$parametro}%'	    
                            ORDER BY idMensagem");

    # select do edita
    $objeto->set_selectEdita("SELECT mensagem							    
                                FROM tbmensagem
                               WHERE idMensagem = {$id}");

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(["id", "Mensagem"]);
    $objeto->set_width([5, 85]);
    $objeto->set_align(["center", "left"]);

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbmensagem');

    # Nome do campo id
    $objeto->set_idCampo('idMensagem');

    # Campos para o formulario
    $objeto->set_campos(array(
        array('nome' => 'mensagem',
            'label' => 'Mensagem:',
            'autofocus' => true,
            'tipo' => 'textarea',
            'size' => array(90, 5),
            'title' => 'Mensagem a ser exibida.',
            'col' => 12,
            'linha' => 1)
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
        case "excluir" :
        case "gravar" :
            $objeto->$fase($id);
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("login.php");
}