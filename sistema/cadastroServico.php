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
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Serviço');

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

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
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(["id", "Categoria", "Nome", "Anexos", "Ver"]);
    $objeto->set_width([5, 20, 55, 5, 5]);
    $objeto->set_align(["center", "center", "left"]);

    # Botão Anexos
    $botao1 = new BotaoGrafico();
    $botao1->set_label('');
    $botao1->set_title('Cadastra os Anexos');
    $botao1->set_url("cadastroServicoAnexos.php?idServico=");
    $botao1->set_imagem(PASTA_FIGURAS . 'documentacao.png', 20, 20);

    # Botão ver
    $botao2 = new BotaoGrafico();
    $botao2->set_label('');
    $botao2->set_title('Visualiza');
    $botao2->set_target('_blank');
    $botao2->set_url("servicos.php?fase=exibeServico&id=");
    $botao2->set_imagem(PASTA_FIGURAS . 'olho.png', 20, 20);

    # Coloca o objeto link na tabela			
    $objeto->set_link(["", "", "", $botao1, $botao2]);

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbservico');

    # Nome do campo id
    $objeto->set_idCampo('idServico');

    # Pega os dados da combo Usuario
    $comboUsuario = $intra->select('SELECT idusuario,
                                           usuario
                                      FROM tbusuario
                                  ORDER BY usuario');
    array_unshift($comboUsuario, array(null, null));

    # Campos para o formulario
    $objeto->set_campos(array(
        array('nome' => 'categoria',
            'label' => 'Categoria:',
            'tipo' => 'texto',
            'size' => 100,
            'title' => 'Categoria.',
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
            'linha' => 2),
        array('nome' => 'quem',
            'label' => 'Quem Tem Direito:',
            'tipo' => 'editor',
            'tagHtml' => true,
            'size' => array(90, 8),
            'title' => 'O que é.',
            'col' => 12,
            'linha' => 3),
        array('nome' => 'como',
            'label' => 'Como Solicitar:',
            'tipo' => 'editor',
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

    # Ver os serviços
    $linkProc = new Link("Serviços", "../../areaServidor/sistema/servicos.php");
    $linkProc->set_class('button');
    $linkProc->set_title('Acessa a área de procedimentos');
    $linkProc->set_target("_aba");

    # array de botões    
    $objeto->set_botaoListarExtra([$linkProc]);

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