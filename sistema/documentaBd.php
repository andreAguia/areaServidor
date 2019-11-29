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
    
    # Verifica a fase do programa
    $banco = get('banco','grh');
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    # Botão voltar
    $linkBotao1 = new Link("Voltar",'administracao.php');
    $linkBotao1->set_class('button');
    $linkBotao1->set_title('Volta para a página anterior');
    $linkBotao1->set_accessKey('V');
    
    # Diagramas
    $linkBotao4 = new Link("Diagramas","documentaDiagrama.php?banco=$banco");
    $linkBotao4->set_class('button');
    $linkBotao4->set_title('Diagramas do sistema');
    $linkBotao4->set_accessKey('D');
    
    # Relatórios
    $imagem = new Imagem(PASTA_FIGURAS.'print.png',NULL,15,15);
    $botaoRel = new Button();
    $botaoRel->set_title("Relatório");
    $botaoRel->set_onClick("window.open('../relatorios/documentaBd.php?banco=$banco','_blank','menubar=no,scrollbars=yes,location=no,directories=no,status=no,width=750,height=600');");
    $botaoRel->set_imagem($imagem);
    
    # Cria um menu
    $menu = new MenuBar();
    $menu->add_link($linkBotao1,"left"); 
    $menu->add_link($linkBotao4,"right");
    $menu->add_link($botaoRel,"right");
    $menu->show();
    
    $grid->fechaColuna();
    $grid->fechaGrid();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

    # Conecta com o banco de dados
    $servico = new Doc();

    $select = "SELECT TABLE_NAME,
                      TABLE_COMMENT,
                      ENGINE,
                      TABLE_ROWS,
                      AVG_ROW_LENGTH,
                      DATA_LENGTH,
                      AUTO_INCREMENT
                 FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$banco'"; 

    $conteudo = $servico->select($select);
    
    $label = array("Nome","Descrição","Motor","Num. Registros","Tamanho Médio","Tamanho Total","Auto Incremento");
    #$function = array("datetime_to_php",NULL,NULL,NULL,"get_nome");
    $align = array("left","left");

    # Monta a tabela
    $tabela = new Tabela();
    $tabela->set_titulo("Banco: ".$banco);
    $tabela->set_conteudo($conteudo);
    $tabela->set_label($label);
    $tabela->set_align($align);
    #$tabela->set_funcao($function); 
    $tabela->set_numeroOrdem(TRUE);
    $tabela->set_editar("documentaTabela.php?banco=$banco");
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