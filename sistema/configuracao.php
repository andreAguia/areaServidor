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

    # Varifica a Categoria
    $categoria = get("categoria", get_session('categoria', "Acesso"));
    set_session('categoria', $categoria);

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Ordem da tabela
    $orderCampo = get('orderCampo', 2);
    $orderTipo = get('order_tipo', 'asc');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    if ($fase == "listar") {
        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

        # Botão voltar
        $linkBotao1 = new Link("Voltar", 'administracao.php');
        $linkBotao1->set_class('button');
        $linkBotao1->set_title('Volta para a página anterior');
        $linkBotao1->set_accessKey('V');

        # Código
        $linkBotao2 = new Link("Incluir", '?fase=editar');
        $linkBotao2->set_class('button');
        $linkBotao2->set_title('Incluir uma nova variavel de configuraçao');
        $linkBotao2->set_accessKey('I');

        # Cria um menu
        $menu = new MenuBar();
        $menu->add_link($linkBotao1, "left");
        $menu->add_link($linkBotao2, "right");
        $menu->show();

        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ################################################################
    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    if (is_null($categoria)) {
        $objeto->set_nome('Configurações do Sistema');
    } else {
        $objeto->set_nome('Configurações do Sistema - ' . $categoria);
    }

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

    # ordenação
    if (is_null($orderCampo)) {
        $orderCampo = 1;
    }

    if (is_null($orderTipo)) {
        $orderTipo = 'asc';
    }

    # select da lista
    $select = 'SELECT categoria,
                      nome,                      
                      comentario,
                      valor,
                      idVariaveis
                 FROM tbvariaveis';

    if (!is_null($categoria)) {
        $select .= ' WHERE categoria = "' . $categoria . '" ';
    }

    $select .= ' ORDER BY ' . $orderCampo . ' ' . $orderTipo;

    $objeto->set_selectLista($select);
    # select do edita
    $objeto->set_selectEdita('SELECT categoria,
                                     nome,                                     
                                     comentario,	
                                     valor							    
                                FROM tbvariaveis
                               WHERE idVariaveis = ' . $id);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_botaoVoltarLista(false);
    $objeto->set_botaoIncluir(false);
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(array("Categoria", "Nome", "Comentário", "Valor"));
    #$objeto->set_width(array(10,10,10,60));		
    $objeto->set_align(array("center", "left", "left", "left"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbvariaveis');

    # Nome do campo id
    $objeto->set_idCampo('idVariaveis');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Pega as etiquetas cadastradas
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
            # Divide a página em 2 colunas
            $grid = new Grid();
            $grid->abreColuna(2);

            $menu = new Menu("menuVertical2");
            $result = $intra->select('SELECT distinct categoria, categoria
                                              FROM tbvariaveis                                
                                          ORDER BY 1');

            $menu->add_item('titulo', "Categoria", "#");
            foreach ($result as $value) {
                $menu->add_item('link', $value[0], "?categoria=" . $value[0]);
                #$botao = new Link($value[0],"?categoria=".$value[0]);
                #$botao->set_class('button success');
                #$botao->set_title('Filtra para categoria: '.$value[0]);
                #$menu[] = $botao;
            }
            $menu->show();
            $objeto->set_botaoListarExtra($menu);
            br();

            $grid->fechaColuna();

            ############################################
            # Área do Sistema
            $grid->abreColuna(10);

            $objeto->listar();
            $grid->fechaColuna();
            $grid->fechaGrid();
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