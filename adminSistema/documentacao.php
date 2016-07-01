<?php

# Servidor logado
$idUsuario = null;

# Configuração
include ("_config.php");

# Começa uma nova página
$page = new Page();
$page->iniciaPagina();

# Cabeçalho
AreaServidor::cabecalho();

# Limita o tamanho da tela
$grid = new Grid();
$grid->abreColuna(12);

botaoVoltar("administracao.php");

# Exibe o título
titulo("Documentação dos Sistemas");
        
$tamanhoImage = 60;

# Cria 3 colunas
$grid = new Grid();

$grid->abreColuna(4);
$fieldset = new Fieldset('Framework');
$fieldset->abre();

    $menu = new MenuGrafico(3);

    # Código
    $botao = new BotaoGrafico();
    $botao->set_label('Código');
    $botao->set_url('documentaCodigo.php?fase=Framework');
    $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
    $botao->set_title('Classes e Funções');
    $menu->add_item($botao);

    # Variáveis de Configuração
    $botao = new BotaoGrafico();
    $botao->set_label('Banco de Dados');
    $botao->set_url('documentaBd.php?fase=Framework');
    $botao->set_image(PASTA_FIGURAS.'bd.png',$tamanhoImage,$tamanhoImage);
    $botao->set_title('Exibe informações do banco de dados');
    $menu->add_item($botao);

    # Histórico Geral
    $botao = new BotaoGrafico();
    $botao->set_label('Diagrama');
    $botao->set_url('documentaDiagrama.php?fase=Framework');
    $botao->set_title('Diagramas do sistema');
    $botao->set_image(PASTA_FIGURAS.'diagrama.jpg',$tamanhoImage,$tamanhoImage);    
    $menu->add_item($botao);

    $menu->show();
    
$fieldset->fecha();
$grid->fechaColuna();


$grid->abreColuna(4);
$fieldset = new Fieldset('Administração');
$fieldset->abre();

    $menu = new MenuGrafico(3);

    # Código
    $botao = new BotaoGrafico();
    $botao->set_label('Código');
    $botao->set_url('documentaCodigo.php?fase=Administracao');
    $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
    $botao->set_title('Classes e Funções');
    $menu->add_item($botao);

    # Variáveis de Configuração
    $botao = new BotaoGrafico();
    $botao->set_label('Banco de Dados');
    $botao->set_url('documentaBd.php?fase=Administracao');
    $botao->set_image(PASTA_FIGURAS.'bd.png',$tamanhoImage,$tamanhoImage);
    $botao->set_title('Exibe informações do banco de dados');
    $menu->add_item($botao);

    # Histórico Geral
    $botao = new BotaoGrafico();
    $botao->set_label('Diagrama');
    $botao->set_url('documentaDiagrama.php?fase=Administracao');
    $botao->set_title('Diagramas do sistema');
    $botao->set_image(PASTA_FIGURAS.'diagrama.jpg',$tamanhoImage,$tamanhoImage);    
    $menu->add_item($botao);

    $menu->show();
    
$fieldset->fecha();
$grid->fechaColuna();

$grid->abreColuna(4);
$fieldset = new Fieldset('GRH');
$fieldset->abre();

    $menu = new MenuGrafico(3);

    # Código
    $botao = new BotaoGrafico();
    $botao->set_label('Código');
    $botao->set_url('documentaCodigo.php?fase=Grh');
    $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
    $botao->set_title('Classes e Funções');
    $menu->add_item($botao);

    # Variáveis de Configuração
    $botao = new BotaoGrafico();
    $botao->set_label('Banco de Dados');
    $botao->set_url('documentaBd.php?fase=Grh');
    $botao->set_image(PASTA_FIGURAS.'bd.png',$tamanhoImage,$tamanhoImage);
    $botao->set_title('Exibe informações do banco de dados');
    $menu->add_item($botao);

    # Histórico Geral
    $botao = new BotaoGrafico();
    $botao->set_label('Diagrama');
    $botao->set_url('documentaDiagrama.php?fase=Grh');
    $botao->set_title('Diagramas do sistema');
    $botao->set_image(PASTA_FIGURAS.'diagrama.jpg',$tamanhoImage,$tamanhoImage);    
    $menu->add_item($botao);

    $menu->show();
    
$fieldset->fecha();
$grid->fechaColuna();
$grid->fechaGrid();

$grid->fechaColuna();
$grid->fechaGrid();

$page->terminaPagina();