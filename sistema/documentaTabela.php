<?php

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){    

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();

    # Cabeçalho
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Verifica a fase do programa
    $table = get('id');

    # Pega o bd
    $banco = get('banco');

    # Botão voltar
    $linkBotaoVoltar = new Link("Voltar",'documentaBd.php?banco='.$banco);
    $linkBotaoVoltar->set_class('button float-left');
    $linkBotaoVoltar->set_title('Volta para a página anterior');
    $linkBotaoVoltar->set_accessKey('V');

    # Botão editar descrição da tabela
    $linkBotaoEditar = new Link("Conteúdo",'documentaTabelaConteudo.php?banco='.$banco.'&tabela='.$table);
    $linkBotaoEditar->set_class('button');
    $linkBotaoEditar->set_title('Exibe o conteúdo da tabela');
    $linkBotaoEditar->set_accessKey('C');

    # Cria um menu
    $menu = new MenuBar();
    $menu->add_link($linkBotaoVoltar,"left");
    $menu->add_link($linkBotaoEditar,"right");
    $menu->show();

    # Conecta com o banco de dados
    $servico = new Doc();

    $select = "SELECT ORDINAL_POSITION,
                      COLUMN_NAME,
                      COLUMN_KEY,
                      EXTRA,
                      COLUMN_COMMENT,
                      COLUMN_TYPE,
                      CHARACTER_MAXIMUM_LENGTH,
                      COLUMN_DEFAULT,
                      IS_NULLABLE
                 FROM COLUMNS 
                WHERE TABLE_SCHEMA = '".$banco."' 
                  AND TABLE_NAME = '".$table."'";

    $conteudo = $servico->select($select);

    $label = array("#","Nome","Chave","Extra","Descrição","Tipo","Tamanho","Padrão","Nulo");
    #$function = array("datetime_to_php",NULL,NULL,NULL,"get_nome");
    $align = array("center","left","center","center","left");

    # Monta a tabela
    $tabela = new Tabela();
    $tabela->set_titulo($banco." / ".$table);
    $tabela->set_conteudo($conteudo);
    $tabela->set_label($label);
    $tabela->set_align($align);
    $tabela->set_idCampo('COLUMN_NAME');

    # exibe a tabela
    $tabela->show();

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}