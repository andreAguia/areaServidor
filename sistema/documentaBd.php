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

    # Verifica a fase do programa
    $banco = get('banco', 'grh');

    # Define os bancos
    $bancos = ["grh", "areaservidor", "contratos"];

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

    # Botão voltar
    $linkBotao1 = new Link("Voltar", 'administracao.php');
    $linkBotao1->set_class('button');
    $linkBotao1->set_title('Volta para a página anterior');
    $linkBotao1->set_accessKey('V');
    $menu->add_link($linkBotao1, "left");

    # Diagramas
    $linkBotao4 = new Link("Diagramas", "documentaDiagrama.php?banco=$banco");
    $linkBotao4->set_class('button');
    $linkBotao4->set_title('Diagramas do sistema');
    $menu->add_link($linkBotao4, "right");

    # Relatórios
    $imagem = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
    $botaoRel = new Button();
    $botaoRel->set_title("Relatório");
    $botaoRel->set_onClick("window.open('../relatorios/documentaBd.php?banco=$banco','_blank','menubar=no,scrollbars=yes,location=no,directories=no,status=no,width=750,height=600');");
    $botaoRel->set_imagem($imagem);
    $menu->add_link($botaoRel, "right");

    $menu->show();

    # Cria um menu
    $menu = new MenuBar();

    foreach ($bancos as $item) {
        # Diagramas
        $linkItem = new Link($item, "?banco=$item");

        if ($banco == $item) {
            $linkItem->set_class('button');
        } else {
            $linkItem->set_class('hollow button');
        }
        $linkItem->set_title('Banco de Dados');
        $menu->add_link($linkItem, "right");
    }

    $menu->show();

    # Conecta com o banco de dados
    $servico = new Doc();

    $select = "SELECT TABLE_NAME,
                      TABLE_COMMENT,
                      ENGINE,
                      TABLE_ROWS,
                      AVG_ROW_LENGTH,
                      DATA_LENGTH,
                      AUTO_INCREMENT,
                      TABLE_NAME
                 FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'uenf_{$banco}'";

    $conteudo = $servico->select($select);

    # Monta a tabela
    $tabela = new Tabela();
    $tabela->set_titulo("Banco: " . $banco);
    $tabela->set_conteudo($conteudo);
    $tabela->set_label(["Nome", "Descrição", "Motor", "Num. Registros", "Tamanho Médio", "Tamanho Total", "Auto Incremento","Estrutura"]);
    $tabela->set_align(["left", "left"]);
    $tabela->set_width([20, 25, 10, 10, 10, 10, 10]);
    $tabela->set_numeroOrdem(true);
    
    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new BotaoGrafico();
    $botao->set_label('');
    $botao->set_title('Editar Usuário');
    $botao->set_url("documentaTabela.php?banco={$banco}&id=");
    $botao->set_target("_blank");
    $botao->set_imagem(PASTA_FIGURAS . 'olho.png', 20, 20);

    # Coloca o objeto link na tabela			
    $tabela->set_link([null, null, null, null, null, null, null, $botao]);

    if (count($conteudo) == 0) {
        br();
        $callout = new Callout();
        $callout->abre();
        p('Nenhum item encontrado !!', 'center');
        $callout->fecha();
    } else {
        # exibe a tabela
        $tabela->show();
    }

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}