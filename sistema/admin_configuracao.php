<?php

/**
 * Gerencia as Variáveis do Sistema
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
    $objeto->set_nome('Configurações do Sistema');

    # botão de voltar da lista
    $objeto->set_voltarLista('areaServidor.php?fase=menuAdmin');

    # controle de pesquisa
    $result2 = $intra->select('SELECT distinct categoria, categoria FROM tbvariaveis ORDER BY 1');
    array_unshift($result2, "Todos");
    $objeto->set_parametroLabel('Pesquisar');
    $objeto->set_parametroValue($parametro);
    $objeto->set_tipoCampoPesquisa("combo");
    $objeto->set_arrayPesquisa($result2);

    # select da lista
    $select = "SELECT categoria,
                      nome,                      
                      comentario,
                      valor,
                      idVariaveis
                 FROM tbvariaveis";

    if ($parametro <> "Todos") {
        $select .= " WHERE categoria = '{$parametro}'";
    }

    $select .= " ORDER BY categoria, nome";

    $objeto->set_selectLista($select);

    # select do edita
    $objeto->set_selectEdita('SELECT categoria,
                                     nome,                                     
                                     comentario,	
                                     valor							    
                                FROM tbvariaveis
                               WHERE idVariaveis = ' . $id);

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(["Categoria", "Nome", "Comentário", "Valor"]);
    $objeto->set_width([10, 10, 50, 20]);
    $objeto->set_align(["center", "left", "left", "left"]);

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbvariaveis');

    # Nome do campo id
    $objeto->set_idCampo('idVariaveis');

    # Pega as categorias cadastradas
    $select = 'SELECT distinct categoria
                 FROM tbvariaveis
                WHERE categoria is not null
                 ORDER BY categoria';

    $dadosCategoria = $intra->select($select);

    # Campos para o formulario
    $objeto->set_campos(array(
        array('nome' => 'categoria',
            'label' => 'Categoria:',
            'tipo' => 'texto',
            'required' => true,
            'datalist' => $dadosCategoria,
            'size' => 50,
            'title' => 'Categoria da Variável.',
            'autofocus' => true,
            'col' => 4,
            'linha' => 1),
        array('nome' => 'nome',
            'label' => 'Nome:',
            'tipo' => 'texto',
            'size' => 90,
            'title' => 'Nome da Variável.',
            'required' => true,
            'col' => 8,
            'linha' => 1),
        array('nome' => 'comentario',
            'label' => 'Comentário:',
            'tipo' => 'textarea',
            'size' => array(90, 5),
            'required' => true,
            'title' => 'Descrição resumida da utilidade da variável.',
            'col' => 12,
            'linha' => 2),
        array('nome' => 'valor',
            'label' => 'Valor:',
            'tipo' => 'texto',
            'size' => 90,
            'title' => 'Valor da Variável.',
            'col' => 12,
            'linha' => 3),
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