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

    # Verifica a fase do programa
    $fase = get('fase', 'listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))) {                                     # Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro')); # passa o parametro da session para a variavel parametro retirando as aspas
    } else {
        $parametro = post('parametro');        # Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro', $parametro);      # transfere para a session para poder recuperá-lo depois
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
    $objeto->set_nome('Computador');

    # botão de voltar da lista
    $objeto->set_voltarLista('areaServidor.php?fase=usuarios');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);

    # ordenação
    if (is_null($orderCampo))
        $orderCampo = 1;

    if (is_null($orderTipo))
        $orderTipo = 'asc';

    # select da lista
    $objeto->set_selectLista('SELECT ip,
                                     tbusuario.usuario,
                                     patrimonio,
                                     ginfo,
                                     tbcomputador.obs,
                                     idComputador
                                FROM tbcomputador LEFT JOIN tbusuario USING (idusuario)
                               WHERE ip LIKE "%' . $parametro . '%"
                                  OR tbusuario.usuario LIKE "%' . $parametro . '%"	
                                  OR patrimonio LIKE "%' . $parametro . '%"	
                                  OR ginfo LIKE "%' . $parametro . '%"	    
                            ORDER BY ' . $orderCampo . ' ' . $orderTipo);

    # select do edita
    $objeto->set_selectEdita('SELECT ip,
                                     idUsuario,
                                     patrimonio,
                                     ginfo,
                                     obs							    
                                FROM tbcomputador
                               WHERE idComputador = ' . $id);

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
    $objeto->set_label(array("IP", "Usuário", "Patrimônio", "GInfo", "Obs"));
    $objeto->set_width(array(10, 15, 10, 10, 45));
    $objeto->set_align(array("center", "center", "center", "center", "left"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbcomputador');

    # Nome do campo id
    $objeto->set_idCampo('idComputador');

    # Pega os dados da combo Usuario
    $comboUsuario = $intra->select('SELECT idusuario,
                                           usuario
                                      FROM tbusuario
                                  ORDER BY usuario');
    array_unshift($comboUsuario, array(null, null));

    # Campos para o formulario
    $objeto->set_campos(array(
        array('nome' => 'ip',
            'label' => 'IP:',
            'tipo' => 'texto',
            'size' => 20,
            'title' => 'IP do computador.',
            'required' => true,
            'autofocus' => true,
            'col' => 3,
            'linha' => 1),
        array('linha' => 1,
            'nome' => 'idUsuario',
            'label' => 'Usuário:',
            'tipo' => 'combo',
            'array' => $comboUsuario,
            'title' => 'Usuário que normalmente usa essa máquina',
            'col' => 3,
            'size' => 15),
        array('nome' => 'patrimonio',
            'label' => 'Patrimônio:',
            'tipo' => 'texto',
            'size' => 20,
            'title' => 'Número de patrimônio do computador.',
            'col' => 3,
            'linha' => 1),
        array('nome' => 'ginfo',
            'label' => 'GInfo:',
            'tipo' => 'texto',
            'size' => 20,
            'title' => 'Número da Ginfo do computador.',
            'col' => 3,
            'linha' => 1),
        array('nome' => 'obs',
            'label' => 'Obs:',
            'tipo' => 'textarea',
            'size' => array(90, 5),
            'title' => 'Observações.',
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
        case "excluir" :
        case "gravar" :
            $objeto->$fase($id);
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("login.php");
}