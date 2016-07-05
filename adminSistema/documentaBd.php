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
    $fase = get('fase');

    # Cria um menu
    $menu = new MenuBar();

    switch ($fase)
    {
      case "Framework" :
          loadPage("documentacao.php");
          break;

      case "Grh" :
          $banco = "grh";
          break;

      case "Administracao" :
          $banco = "admin";
          break;
    }

    # Botão voltar
    botaoVoltar("documentacao.php");

    # Topbar        
    $top = new TopBar($fase);
    $top->show();

    # Conecta com o banco de dados
    $servico = new Doc();

    $select = "SELECT TABLE_NAME,
                      TABLE_COMMENT,
                      TABLE_TYPE,
                      ENGINE,
                      TABLE_ROWS,
                      AVG_ROW_LENGTH,
                      DATA_LENGTH,
                      AUTO_INCREMENT
                 FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$banco."'"; 
    $conteudo = $servico->select($select);

    $label = array("Nome","Descrição","Tipo","Motor","Num. Registros","Tamanho Médio","Tamanho Total","AI");
    $width = array(10,30,10,10,10,10,10,5);
    #$function = array("datetime_to_php",null,null,null,"get_nome");
    $align = array("left","left");

    # Monta a tabela
    $tabela = new Tabela();
    $tabela->set_conteudo($conteudo);
    $tabela->set_cabecalho($label,$width,$align);
    #$tabela->set_funcao($function); 
    $tabela->set_numeroOrdem(true);
    $tabela->set_editar("documentaTabela.php?fase=$fase&bd=".$banco);
    $tabela->set_idCampo('TABLE_NAME');
    $tabela->set_nomeColunaEditar('Ver');
    $tabela->set_editarBotao('ver.png');

    # exibe a tabela
    $tabela->show();

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}