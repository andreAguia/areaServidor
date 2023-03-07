<?php

/**
 * Cadastro de Computador
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
    $rotina = new Rotina();

    # Verifica a fase do programa
    $fase = get('fase', 'listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));
    
    # Pega a rotina
    $idRotina = get_session('idRotina');

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))) {
        $parametro = retiraAspas(get_session('sessionParametro'));
    } else {
        $parametro = post('parametro');
        set_session('sessionParametro', $parametro);
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
    $objeto->set_nome($rotina->get_nomeRotina($idRotina));

    # botão de voltar da lista
    $objeto->set_voltarLista('rotina.php');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $objeto->set_selectLista("SELECT numOrdem,
                                     quem,
                                     procedimento,
                                     obs,
                                     idRotinaItens
                                FROM tbrotinaitens
                               WHERE (quem LIKE '%{$parametro}%' OR procedimento LIKE '%{$parametro}%')
                                 AND idRotina = {$idRotina}
                            ORDER BY numOrdem");

    # select do edita
    $objeto->set_selectEdita("SELECT numOrdem,
                                     quem,
                                     procedimento,
                                     obs					    
                                FROM tbrotinaitens
                               WHERE idRotinaItens = {$id}");

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(["#", "Quem", "Procedimento", "Obs"]);
    $objeto->set_width([5, 15, 35, 35]);
    $objeto->set_align(["center", "center", "left", "left"]);
    
    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbrotinaitens');

    # Nome do campo id
    $objeto->set_idCampo('idRotinaItens');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array('nome' => 'numOrdem',
            'label' => 'Numero de Ordem:',
            'tipo' => 'texto',
            'size' => 3,
            'title' => 'Número de ordem.',
            'required' => true,
            'autofocus' => true,
            'col' => 2,
            'linha' => 1),
        array('nome' => 'quem',
            'label' => 'Quem:',
            'tipo' => 'texto',
            'size' => 100,
            'title' => 'Nome da rotina.',
            'col' => 8,
            'linha' => 1),
        array('nome' => 'procedimento',
            'label' => 'Procedimento:',
            'tipo' => 'textarea',
            'size' => array(90, 5),
            'title' => 'Observações.',
            'col' => 12,
            'linha' => 2),
        array('nome' => 'obs',
            'label' => 'Obs:',
            'tipo' => 'textarea',
            'size' => array(90, 5),
            'title' => 'Observações.',
            'col' => 12,
            'linha' => 3),
        array('nome' => 'idRotina',
            'label' => '',
            'tipo' => 'hidden',
            'size' => 6,
            'padrao' => $idRotina,
            'linha' => 4),
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