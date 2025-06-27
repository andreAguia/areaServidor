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
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servico = new Servico();

    # Verifica a fase do programa
    $fase = get('fase', 'listar');

    # Pega o id (se tiver)
    $id = soNumeros(get('id'));
    $idServico = soNumeros(get('idServico'));
    
    # Pega os dados desse serviço
    $dados = $servico->get_dados($idServico);

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))) {
        $parametro = retiraAspas(get_session('sessionParametro'));
    } else {
        $parametro = post('parametro');
        set_session('sessionParametro', $parametro);
    }

    # Define os tipos de documentos
    $arrayTipos = [
        [null, null],
        [1, "Documento"],
        [2, "Arquivo JPG"],
        [3, "Arquivo PDF"],
        [4, "Link"],
        [5, "Rotina"],
    ];

    # Começa uma nova página
    $page = new Page();
    $page->set_jscript("<script>CKEDITOR.replace('texto');</script>");
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome("Anexos");
    $objeto->set_subtitulo($dados["nome"]);
    
    # botão de voltar da lista
    $objeto->set_voltarLista("cadastroServico.php?id={$idServico}");

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $selectListar = "SELECT idServicoAnexos,
                            categoria,
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
                      FROM tbservicoanexos 
                     WHERE (categoria LIKE '%{$parametro}%' OR titulo LIKE '%{$parametro}%')
                       AND idServico = {$idServico}
                  ORDER BY categoria, numOrdem, titulo";

    $objeto->set_selectLista("$selectListar&idServico={$idServico}");

    # select do edita
    $objeto->set_selectEdita("SELECT categoria,
                                     numOrdem,
                                     visibilidade,
                                     tipo,
                                     titulo,
                                     descricao,
                                     link,
                                     idRotina,
                                     texto,
                                     idServico
                                FROM tbservicoanexos
                               WHERE idServicoAnexos = {$id}");

    # Caminhos
    $objeto->set_linkEditar("?fase=editar&idServico={$idServico}");
    $objeto->set_linkExcluir("?fase=excluir&idServico={$idServico}");
    $objeto->set_linkGravar("?fase=gravar&idServico={$idServico}");
    $objeto->set_linkListar("?fase=listar&idServico={$idServico}");

    # Parametros da tabela
    $objeto->set_label(["Id", "Categoria", "Ordem", "Título", "Descrição", "Tipo", "Visibilidade"]);
    $objeto->set_align(["center", "center", "center", "left", "left"]);

    $objeto->set_rowspan(1);
    $objeto->set_grupoCorColuna(1);

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbservicoanexos');

    # Nome do campo id
    $objeto->set_idCampo('idServicoAnexos');

    # Pega os dados da combo de rotina
    $rotina = $intra->select('SELECT idRotina,
                                     CONCAT(categoria," - ",nome)
                                FROM tbrotina
                            ORDER BY categoria, nome');
    array_unshift($rotina, array(null, null));

    # Pega os dados da combo de categoria
    $categoriaLista = $intra->select('SELECT distinct categoria
                                FROM tbservicoanexos
                            ORDER BY categoria');
    array_unshift($categoriaLista, array(null, null));

    # Campos para o formulario
    $objeto->set_campos([
        array('linha' => 1,            
            'autofocus' => true,
            'nome' => 'categoria',
            'label' => 'Categoria:',
            'tipo' => 'texto',
            'datalist' => $categoriaLista,
            'required' => true,
            'col' => 4,
            'size' => 100),        
        array('linha' => 1,
            'nome' => 'numOrdem',
            'label' => 'numOrdem:',
            'tipo' => 'texto',
            'required' => true,
            'col' => 2,
            'size' => 4),
        array('linha' => 1,
            'nome' => 'visibilidade',
            'label' => 'Visivel:',
            'tipo' => 'combo',
            'required' => true,
            'array' => array(array(1, "Sim"), array(2, "Não")),
            'col' => 2,
            'size' => 15),
        array('linha' => 1,
            'col' => 3,
            'nome' => 'tipo',
            'label' => 'Tipo:',
            'tipo' => 'combo',
            'required' => true,
            'autofocus' => true,
            'array' => $arrayTipos,
            'size' => 15),        
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
            'required' => true,
            'col' => 6,
            'size' => 250),        
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
            'nome' => 'texto',
            'label' => 'Texto:',
            'tipo' => 'editor',
            'tagHtml' => true,
            'size' => array(90, 5),
            'title' => 'Texto'),
        array('linha' => 5,
            'nome' => 'idServico',
            'label' => 'idServico',
            'title' => 'idServico',
            'tipo' => 'hidden',
            'padrao' => $idServico,
            'col' => 2,
            'size' => 11),
    ]);

    # idUsuário para o Log
    $objeto->set_idUsuario($idUsuario);

    # Alterações para acessar diretamente da visualização dos procedimentos
//    $objeto->set_voltarForm("procedimentos.php?idProcedimento={$id}");
//    $objeto->set_linkListar("procedimentos.php?idProcedimento={$id}");

    $procedimento = new Procedimento();

    # Dados da rotina de Upload
    $pasta = PASTA_SERVICOANEXOS;
    $nome = "Arquivo";
    $tabela = "tbservicoanexos";

    # Botão de Upload
    if (!empty($id)) {

        # Botão de Upload
        $botao = new Button("Upload {$nome}");
        $botao->set_url("cadastroServicoAnexosUpload.php?fase=upload&id={$id}");
        $botao->set_title("Faz o Upload do {$nome}");
        $botao->set_target("_blank");

        $objeto->set_botaoEditarExtra([$botao]);
    }

    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :
        case "gravar" :
        case "excluir" :
            $objeto->$fase($id);
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}