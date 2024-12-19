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
    if ($fase <> "ver") {
        AreaServidor::cabecalho();
    }

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Rotina');

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $objeto->set_selectLista("SELECT idRotina,
                                     categoria,
                                     nome,
                                     descricao,
                                     obs,
                                     idRotina
                                FROM tbrotina
                               WHERE categoria LIKE '%{$parametro}%'
                                  OR nome LIKE '%{$parametro}%'	
                                  OR descricao LIKE '%{$parametro}%' 
                            ORDER BY categoria, nome");

    # select do edita
    $objeto->set_selectEdita("SELECT categoria,
                                     nome,
                                     descricao,
                                     obs						    
                                FROM tbrotina
                               WHERE idRotina = {$id}");

    # ordem da lista
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editaItem');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(["Id", "Categoria", "Rotina", "Descrição", "Obs", "Itens", "Ver"]);
    $objeto->set_width([5, 15, 20, 20, 20, 5, 5]);
    $objeto->set_align(["center", "left", "left", "left", "left"]);
    $objeto->set_classe([null, null, null, null, null, "Rotina"]);
    $objeto->set_metodo([null, null, null, null, null, "get_numItens"]);

    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new BotaoGrafico();
    $botao->set_label('');
    $botao->set_title('Editar Itens');
    $botao->set_url("?fase=ver&id=");
    $botao->set_target("_blank");
    //$botao->set_url('?fase=editaItem&id=' . $id);
    $botao->set_imagem(PASTA_FIGURAS . 'olho.png', 20, 20);

    # Coloca o objeto link na tabela			
    $objeto->set_link([null, null, null, null, null, null, $botao]);

    $objeto->set_rowspan(1);
    $objeto->set_grupoCorColuna(1);

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbrotina');

    # Nome do campo id
    $objeto->set_idCampo('idRotina');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
        array('nome' => 'categoria',
            'label' => 'Categoria:',
            'tipo' => 'texto',
            'size' => 100,
            'title' => 'Categoria da rotina.',
            'required' => true,
            'autofocus' => true,
            'col' => 4,
            'linha' => 1),
        array('nome' => 'nome',
            'label' => 'Rotina:',
            'tipo' => 'texto',
            'size' => 100,
            'title' => 'Nome da rotina.',
            'col' => 8,
            'linha' => 1),
        array('nome' => 'descricao',
            'label' => 'Descrição:',
            'tipo' => 'texto',
            'size' => 100,
            'title' => 'Descrição da rotina',
            'col' => 12,
            'linha' => 2),
        array('nome' => 'obs',
            'label' => 'Obs:',
            'tipo' => 'textarea',
            'size' => array(90, 5),
            'title' => 'Observações.',
            'col' => 12,
            'linha' => 3)
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

        case "editaItem" :
            if (empty($id)) {
                loadPage("?fase=editar");
            } else {
                set_session('idRotina', $id);
                loadPage("rotinaItens.php");
            }
            break;

        case "ver" :
            $grid = new Grid();
            $grid->abreColuna(12);
            br();

            $rotina = new Rotina();
            $rotina->exibeRotina($id);

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;
    }

    $page->terminaPagina();
} else {
    loadPage("login.php");
}