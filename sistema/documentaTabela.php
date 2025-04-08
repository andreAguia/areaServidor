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

    # Pega o banco e a tabela
    $banco = get('banco');
    $tabela = get('id');

    # Botão voltar
    $linkBotaoVoltar = new Link("Voltar", 'documentaBd.php?banco=' . $banco);
    $linkBotaoVoltar->set_class('button float-left');
    $linkBotaoVoltar->set_title('Volta para a página anterior');
    $linkBotaoVoltar->set_accessKey('V');

    # Conteudo da tabela
    $linkBotaoEditar = new Link("Conteúdo", 'documentaTabelaConteudo.php?banco=' . $banco . '&tabela=' . $tabela);
    $linkBotaoEditar->set_class('button');
    $linkBotaoEditar->set_title('Exibe o conteúdo da tabela');
    $linkBotaoEditar->set_accessKey('C');

    # Relatórios
    $imagem = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
    $botaoRel = new Button();
    $botaoRel->set_title("Relatório");
    $botaoRel->set_onClick("window.open('../relatorios/documentaTabela.php?banco=$banco&tabela=$tabela','_blank','menubar=no,scrollbars=yes,location=no,directories=no,status=no,width=750,height=600');");
    $botaoRel->set_imagem($imagem);

    # Cria um menu
    $menu = new MenuBar();
    #$menu->add_link($linkBotaoVoltar, "left");
    $menu->add_link($linkBotaoEditar,"right");
    $menu->add_link($botaoRel, "right");
    $menu->show();

    # Conecta com o banco de dados
    $servico = new Doc();

    $select = "SELECT ORDINAL_POSITION,
                      COLUMN_NAME,                      
                      COLUMN_TYPE,
                      COLUMN_KEY,
                      EXTRA,
                      COLUMN_COMMENT,
                      COLUMN_DEFAULT,
                      IS_nullABLE
                 FROM COLUMNS 
                WHERE TABLE_SCHEMA = 'uenf_{$banco}' 
                  AND TABLE_NAME = '{$tabela}'
                  ORDER BY ORDINAL_POSITION";

    $conteudo = $servico->select($select);

    $label = array("#", "Nome", "Tipo", "Chave", "Extra", "Descrição", "Padrão", "Nulo");
    #$function = array("datetime_to_php",null,null,null,"get_nome");
    $align = array("center", "left", "center", "center", "center", "left");

    # Monta a tabela
    $tabela2 = new Tabela();
    $tabela2->set_titulo($banco);
    $tabela2->set_subtitulo($tabela);
    $tabela2->set_conteudo($conteudo);
    $tabela2->set_label($label);
    $tabela2->set_align($align);
    $tabela2->set_idCampo('COLUMN_NAME');

    # exibe a tabela
    $tabela2->show();

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}