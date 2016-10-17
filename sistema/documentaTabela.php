<?php

# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso)
{    

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
    $bd = get('bd');

    # Pega o bd
    $fase = get('fase');

    # Botão voltar
    $linkBotaoVoltar = new Link("Voltar",'documentabd.php?fase='.$fase);
    $linkBotaoVoltar->set_class('button float-left');
    $linkBotaoVoltar->set_title('Volta para a página anterior');
    $linkBotaoVoltar->set_accessKey('V');

    # Botão editar descrição da tabela
    $linkBotaoEditar = new Link("Conteúdo",'documentaEditaTabela.php?tabela='.$table);
    $linkBotaoEditar->set_class('button');
    $linkBotaoEditar->set_title('Exibe o conteúdo da tabela');
    $linkBotaoEditar->set_accessKey('C');

    # Cria um menu
    $menu = new MenuBar();
    $menu->add_link($linkBotaoVoltar,"left");
    $menu->add_link($linkBotaoEditar,"right");
    $menu->show();

    # Topbar        
    $top = new TopBar($bd." / ".$table);
    $top->show();

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
                WHERE TABLE_SCHEMA = '".$bd."' 
                  AND TABLE_NAME = '".$table."'";

    $conteudo = $servico->select($select);

    $label = array("#","Nome","Chave","Extra","Descrição","Tipo","Tamanho","Padrão","Nulo");
    $width = array(5,15,5,5,25,15,5,5,5,5);
    #$function = array("datetime_to_php",null,null,null,"get_nome");
    $align = array("center","left","center","center","left");

    # Monta a tabela
    $tabela = new Tabela();
    $tabela->set_conteudo($conteudo);
    $tabela->set_cabecalho($label,$width,$align);
    #$tabela->set_editar("documentaTabela.php?fase=editaDescricaoCampo");
    $tabela->set_idCampo('COLUMN_NAME');

    # exibe a tabela
    $tabela->show();

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}