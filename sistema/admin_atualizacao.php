<?php

/**
 * Cadastro e Atualizações do Sistema
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
    $parametro = post('parametro', retiraAspas(get_session('sessionParametro')));
    set_session('sessionParametro', $parametro);

    # Pega as versões desse ano
    $selectVersao = "SELECT versao FROM tbatualizacao WHERE YEAR(data) = YEAR(NOW()) ORDER BY data desc, versao desc";
    $row = $intra->select($selectVersao, false);

    if (empty($row[0])) {
        $padraoVersao = date("y") . ".01";
    } else {
        $extensao = strrchr($row[0], '.');
        $num = $extensao ? strtolower(substr($extensao, 1)) : '';
        $num++;

        $padraoVersao = date("y") . "." . sprintf("%02d", $num);
    }

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    #AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Atualizações');

    # botão de voltar da lista
    $objeto->set_voltarLista('admin_menu.php?fase=menuSistema');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar nos campos Versão e/ou Data:');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $objeto->set_selectLista("SELECT data,
                                     versao,
                                     alteracoes,
                                     idatualizacao
                                FROM tbatualizacao
                               WHERE versao LIKE '%{$parametro}%'
                                  OR data LIKE '%{$parametro}%'
                                  OR alteracoes LIKE '%{$parametro}%'    
                            ORDER BY 1 desc");

    # select do edita
    $objeto->set_selectEdita("SELECT versao,
                                     data,
                                     alteracoes						    
                                FROM tbatualizacao
                               WHERE idatualizacao = {$id}");

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(["Data", "Versão", "Alterações"]);
    $objeto->set_width([10, 10, 70]);
    $objeto->set_align(["center", "center", "left"]);
    $objeto->set_funcao(["date_to_php", null, 'formataAtribuicao']);

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbatualizacao');

    # Nome do campo id
    $objeto->set_idCampo('idatualizacao');

    # Campos para o formulario
    $objeto->set_campos(array(
        array('nome' => 'versao',
            'label' => 'Versão:',
            'tipo' => 'texto',
            'size' => 20,
            'title' => 'Versão do Sistema.',
            'required' => true,
            'padrao' => $padraoVersao,
            'col' => 3,
            'linha' => 1),
        array('linha' => 1,
            'nome' => 'data',
            'required' => true,
            'label' => 'Data:',
            'tipo' => 'date',
            'padrao' => date("Y-m-d"),
            'title' => 'Data da atualização',
            'col' => 3,
            'size' => 15),
        array('nome' => 'alteracoes',
            'label' => 'Alterações:',
            'tipo' => 'textarea',
            'autofocus' => true,
            'size' => array(90, 12),
            'title' => 'Alterações detalhadas desta versão.',
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