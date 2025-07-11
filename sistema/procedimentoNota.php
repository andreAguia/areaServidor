<?php

/**
 * Cadastro de Categoria de Procedimentos
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {
    # Carrega as classes
    $intra = new Intra();
    $procedimento = new Procedimento();

    # Verifica a fase do programa
    $fase = get('fase', 'listar');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))) { 
        $parametro = retiraAspas(get_session('sessionParametro'));
    } else {
        $parametro = post('parametro');
        set_session('sessionParametro', $parametro);
    }

    # Define os tipos de documentos
    $arrayTipos = $procedimento->get_tiposProcedimento();

    # Começa uma nova página
    $page = new Page();
    $page->set_jscript("<script>CKEDITOR.replace('textoProcedimento');</script>");
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Procedimentos');

    # botão de voltar da lista
    $objeto->set_voltarLista("procedimentos.php?idProcedimento={$id}");    

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $selectListar = "SELECT idProcedimento,
                            categoria,
                            subCategoria,
                            numOrdem,
                            titulo,
                            descricao,
                            CASE tipo";

    foreach ($arrayTipos as $item) {
        if (!empty($item[0])) {
            $selectListar .= " WHEN {$item[0]} THEN '{$item[1]}' ";
        }
    }

    $selectListar .= " ELSE '' ";

    $selectListar .= "     END,
                           visibilidade                                     
                      FROM tbprocedimento 
                     WHERE categoria LIKE '%{$parametro}%'
                        OR subCategoria LIKE '%{$parametro}%'
                        OR titulo LIKE '%{$parametro}%'
                  ORDER BY categoria, subCategoria, numOrdem, titulo";

    $objeto->set_selectLista($selectListar);

    # select do edita
    $objeto->set_selectEdita("SELECT tipo,
                                     categoria,
                                     subCategoria,
                                     titulo,
                                     descricao,
                                     numOrdem,
                                     visibilidade,
                                     link,
                                     idRotina,
                                     textoProcedimento
                                FROM tbprocedimento
                               WHERE idProcedimento = {$id}");

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');

    # Parametros da tabela
    $objeto->set_label(["Id", "Categoria", "Sub-categoria", "Ordem", "Título", "Descrição", "Tipo", "Visibilidade"]);
    $objeto->set_align(["center", "center", "center", "center", "left", "left"]);

    $objeto->set_rowspan([1, 2]);
    $objeto->set_grupoCorColuna(1);

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbprocedimento');

    # Nome do campo id
    $objeto->set_idCampo('idProcedimento');

    # Pega os dados da combo de rotina
    $rotina = $intra->select('SELECT idRotina,
                                     CONCAT(categoria," - ",nome)
                                FROM tbrotina
                            ORDER BY categoria, nome');
    array_unshift($rotina, array(null, null));

    # Pega os dados da combo de categoria
    $categoriaLista = $intra->select('SELECT distinct categoria
                                FROM tbprocedimento
                            ORDER BY categoria');
    array_unshift($categoriaLista, array(null, null));

    # Pega os dados da combo de categoria
    $subCategoriaLista = $intra->select('SELECT distinct subCategoria
                                FROM tbprocedimento
                            ORDER BY subCategoria');
    array_unshift($subCategoriaLista, array(null, null));

    # Campos para o formulario
    $objeto->set_campos([
        array('linha' => 1,
            'col' => 3,
            'nome' => 'tipo',
            'label' => 'Tipo:',
            'tipo' => 'combo',
            'required' => true,
            'autofocus' => true,
            'array' => $arrayTipos,
            'size' => 15),
        array('linha' => 1,
            'nome' => 'categoria',
            'label' => 'Categoria:',
            'tipo' => 'texto',
            'datalist' => $categoriaLista,
            'required' => true,
            'col' => 4,
            'size' => 100),
        array('linha' => 1,
            'nome' => 'subCategoria',
            'label' => 'sub-Categoria:',
            'tipo' => 'texto',
            'datalist' => $subCategoriaLista,
            'required' => true,
            'col' => 5,
            'size' => 100),
        array('linha' => 2,
            'nome' => 'titulo',
            'label' => 'Título:',
            'tipo' => 'texto',
            'required' => true,
            'col' => 6,
            'size' => 100),
        array('linha' => 2,
            'nome' => 'descricao',
            'title' => 'Descrição detalhada da Categoria',
            'label' => 'Descrição:',
            'tipo' => 'texto',
            'col' => 6,
            'size' => 250),
        array('linha' => 3,
            'nome' => 'numOrdem',
            'autofocus' => true,
            'label' => 'numOrdem:',
            'tipo' => 'texto',
            'required' => true,
            'col' => 2,
            'size' => 4),
        array('linha' => 3,
            'nome' => 'visibilidade',
            'label' => 'Visivel:',
            'tipo' => 'combo',
            'required' => true,
            'array' => array(array(1, "Sim"), array(2, "Não")),
            'col' => 2,
            'size' => 15),
        array('linha' => 4,
            'nome' => 'link',
            'title' => 'link',
            'label' => 'Link Externo: (Quando for link)',
            'tipo' => 'texto',
            'col' => 12,
            'size' => 250),
        array('linha' => 5,
            'nome' => 'idRotina',
            'title' => 'link',
            'label' => 'Rotina: (Quando for rotina)',
            'tipo' => 'combo',
            'array' => $rotina,
            'col' => 12,
            'size' => 250),
        array('linha' => 7,
            'nome' => 'textoProcedimento',
            'label' => 'Texto:',
            'tipo' => 'editor',
            'tagHtml' => true,
            'size' => array(90, 5),
            'title' => 'Texto')]);

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    # Alterações para acessar diretamente da visualização dos procedimentos
    $objeto->set_voltarForm("procedimentos.php?idProcedimento={$id}");
    $objeto->set_linkListar("procedimentos.php?idProcedimento={$id}");

    # Dados da rotina de Upload
    $pasta = PASTA_PROCEDIMENTOS;
    $nome = "Arquivo";
    $tabela = "tbprocedimento";

    # Carrega a rotina de acordo com o tipo de documento: jpg ou pdf
    if ($procedimento->get_tipo($id) == 2) {

        # Botão de Upload
        if (!empty($id)) {

            # Botão de Upload
            $botao = new Button("Upload {$nome}");
            $botao->set_url("procedimentoNotaUploadImagem.php?fase=upload&id={$id}");
            $botao->set_title("Faz o Upload do {$nome}");
            $botao->set_target("_blank");

            $objeto->set_botaoEditarExtra([$botao]);
        }
    } elseif ($procedimento->get_tipo($id) == 3) {

        # Botão de Upload
        if (!empty($id)) {

            # Botão de Upload
            $botao = new Button("Upload {$nome}");
            $botao->set_url("procedimentoNotaUploadPdf.php?fase=upload&id={$id}");
            $botao->set_title("Faz o Upload do {$nome}");
            $botao->set_target("_blank");

            $objeto->set_botaoEditarExtra([$botao]);
        }
    }

    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :
        case "gravar" :
            $objeto->$fase($id);
            break;

        case "excluir" :
            $objeto->set_linkListar('?');
            $objeto->$fase($id);
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}