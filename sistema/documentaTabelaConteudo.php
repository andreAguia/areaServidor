<?php

# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Verifica a fase do programa
    $table = get('tabela');

    # Pega o bd
    $banco = get('banco');

    # Botão voltar
    $linkBotaoVoltar = new Link("Voltar", 'documentaTabela.php?banco=' . $banco . '&id=' . $table);
    $linkBotaoVoltar->set_class('button float-left');
    $linkBotaoVoltar->set_title('Volta para a página anterior');
    $linkBotaoVoltar->set_accessKey('V');

    # Cria um menu
    $menu = new MenuBar();
    $menu->add_link($linkBotaoVoltar, "left");
    $menu->show();

    # Pega os nomes da tabela
    $servico = new Doc();

    $select1 = "SELECT COLUMN_NAME
                 FROM COLUMNS 
                WHERE TABLE_SCHEMA = '" . $banco . "' 
                  AND TABLE_NAME = '" . $table . "'";
    $conteudo1 = $servico->select($select1);

    # Pega os nomes das colunas
    $colunas = array();
    foreach ($conteudo1 as $item) {
        $colunas[] = $item[0];
        $align[] = "left";
    }

    # Pega o conteúdo da tabela
    $select2 = "SELECT * FROM $banco.$table";
    $conteudo2 = $servico->select($select2);


    # Monta a tabela
    $tabela = new Tabela();
    $tabela->set_titulo($banco . " / " . $table);
    $tabela->set_conteudo($conteudo2);
    $tabela->set_label($colunas);
    $tabela->set_align($align);

    # exibe a tabela
    $tabela->show();

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}