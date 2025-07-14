<?php

/**
 * Cadastro de Serviços
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

    # Define o link da volta
    $voltaServico = get_session("voltaServico");

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))) {
        $parametro = retiraAspas(get_session('sessionParametro'));
    } else {
        $parametro = post('parametro');
        set_session('sessionParametro', $parametro);
    }

    # Começa uma nova página
    $page = new Page();
    $page->set_jscript("<script>"
            . "CKEDITOR.replace('oque');"
            . "CKEDITOR.replace('quem');"
            . "CKEDITOR.replace('como');"
            . "CKEDITOR.replace('obs');"
            . "CKEDITOR.replace('documentos');"
            . "CKEDITOR.replace('legislacao');"
            . "</script>");
    $page->iniciaPagina();

    # Cabeçalho da Página
    #AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Serviço');

    # botão de voltar da lista
    $objeto->set_voltarLista('servicos.php');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $objeto->set_selectLista("SELECT idServico,
                                     categoria,
                                     nome,
                                     idServico,
                                     idServico
                                FROM tbservico
                            ORDER BY categoria, nome");

    # select do edita
    $objeto->set_selectEdita("SELECT categoria,
                                     nome,
                                     oque,
                                     quem,
                                     como,
                                     obs
                                FROM tbservico
                                WHERE idServico = {$id}");

    # Caminhos
    if (empty($voltaServico)) {
        $objeto->set_linkListar('?fase=listar');
        $objeto->set_voltarForm('?fase=listar');
    } else {
        $objeto->set_voltarForm($voltaServico);
        $objeto->set_linkListar($voltaServico);
    }

    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(["id", "Categoria", "Nome", "Anexos"]);
    $objeto->set_width([5, 20, 60, 5]);
    $objeto->set_align(["center", "center", "left"]);

    # Botão Anexos
    $botao1 = new BotaoGrafico();
    $botao1->set_label('');
    $botao1->set_title('Cadastra os Anexos');
    $botao1->set_url("cadastroServicoAnexos.php?idServico=");
    $botao1->set_imagem(PASTA_FIGURAS . 'documentacao.png', 20, 20);

    # Coloca o objeto link na tabela			
    $objeto->set_link(["", "", "", $botao1]);

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbservico');

    # Nome do campo id
    $objeto->set_idCampo('idServico');

    # Pega os dados da combo Usuario
    $comboCategoria = $intra->select('SELECT distinct categoria
                                        FROM tbservico
                                    ORDER BY categoria');
    array_unshift($comboCategoria, null);

    # Campos para o formulario
    $objeto->set_campos(array(
        array('nome' => 'categoria',
            'label' => 'Categoria:',
            'tipo' => 'texto',
            'size' => 100,
            'title' => 'Categoria.',
            'datalist' => $comboCategoria,
            'required' => true,
            'autofocus' => true,
            'col' => 6,
            'linha' => 1),
        array('nome' => 'nome',
            'label' => 'Nome:',
            'tipo' => 'texto',
            'size' => 100,
            'title' => 'Nome.',
            'required' => true,
            'col' => 6,
            'linha' => 1),
        array('nome' => 'oque',
            'label' => 'O Que é:',
            'tipo' => 'editor',
            'tagHtml' => true,
            'size' => array(90, 8),
            'title' => 'O que é.',
            'required' => true,
            'linha' => 2),
        array('nome' => 'quem',
            'label' => 'Quem Tem Direito:',
            'tipo' => 'editor',
            'tagHtml' => true,
            'size' => array(90, 8),
            'required' => true,
            'title' => 'O que é.',
            'col' => 12,
            'linha' => 3),
        array('nome' => 'como',
            'label' => 'Como Solicitar:',
            'tipo' => 'editor',
            'required' => true,
            'tagHtml' => true,
            'size' => array(90, 8),
            'title' => 'O que é.',
            'col' => 12,
            'linha' => 4),
        array('nome' => 'obs',
            'label' => 'Obs:',
            'tipo' => 'editor',
            'tagHtml' => true,
            'size' => array(90, 8),
            'title' => 'O que é.',
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