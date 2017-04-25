<?php

# Servidor logado 
$idUsuario = NULL;

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
    
    # Verifica a fase do programa
    $fase = get('fase');
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    # Botão voltar
    $linkBotao1 = new Link("Voltar",'administracao.php');
    $linkBotao1->set_class('button');
    $linkBotao1->set_title('Volta para a página anterior');
    $linkBotao1->set_accessKey('V');
    
    # Código
    $linkBotao2 = new Link("Código","documentaCodigo.php?fase=$fase");
    $linkBotao2->set_class('button');
    $linkBotao2->set_title('Classes e Funções');
    $linkBotao2->set_accessKey('C');

    # Banco de Dados
    $linkBotao3 = new Link("Banco de Dados","documentaBd.php?fase=$fase");
    $linkBotao3->set_class('disabled button');
    $linkBotao3->set_title('Exibe informações do banco de dados');
    $linkBotao3->set_accessKey('B');
    
    # Diagramas
    $linkBotao4 = new Link("Diagramas","documentaDiagrama.php?fase=$fase");
    $linkBotao4->set_class('button');
    $linkBotao4->set_title('Diagramas do sistema');
    $linkBotao4->set_accessKey('D');

    # Cria um menu
    $menu = new MenuBar();
    $menu->add_link($linkBotao1,"left");
    $menu->add_link($linkBotao2,"right");
    $menu->add_link($linkBotao3,"right");    
    $menu->add_link($linkBotao4,"right");
    $menu->show();
    
    $grid->fechaColuna();
    $grid->fechaGrid();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

    switch ($fase)
    {
      case "Framework" :
          $banco = "framework";
          break;

      case "Grh" :
          $banco = "grh";
          break;

      case "areaServidor" :
          $banco = "areaServidor";
          break;
    }

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
    #$function = array("datetime_to_php",NULL,NULL,NULL,"get_nome");
    $align = array("left","left");

    # Monta a tabela
    $tabela = new Tabela();
    $tabela->set_conteudo($conteudo);
    $tabela->set_cabecalho($label,$width,$align);
    #$tabela->set_funcao($function); 
    $tabela->set_numeroOrdem(TRUE);
    $tabela->set_editar("documentaTabela.php?fase=$fase&bd=".$banco);
    $tabela->set_idCampo('TABLE_NAME');
    $tabela->set_nomeColunaEditar('Ver');
    $tabela->set_editarBotao('ver.png');

    if(count($conteudo) == 0){
        br();
        $callout = new Callout();
        $callout->abre();
            p('Nenhum item encontrado !!','center');
        $callout->fecha();
    }else{
        # exibe a tabela
        $tabela->show();
    }

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}