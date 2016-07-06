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
    $linkBotao3->set_class('button');
    $linkBotao3->set_title('Exibe informações do banco de dados');
    $linkBotao3->set_accessKey('B');

    # Cria um menu
    $menu = new MenuBar();
    $menu->add_link($linkBotao1,"left");
    $menu->add_link($linkBotao2,"right");
    $menu->add_link($linkBotao3,"right");    
    $menu->show();
    
    $grid->fechaColuna();
    $grid->fechaGrid();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    switch ($fase)
    {
      case "Framework" :
          $pasta = '../_diagramas/framework';
          break;

      case "Grh" :
          $pasta = '../_diagramas/grh';
          break;

      case "Administracao" :
          $pasta = '../_diagramas/admin';
          break;
    }
    
    # Topbar        
    $top = new TopBar('Diagramas');
    $top->show();
    br();

    $callout = new Callout();
    $callout->abre();
        echo '<dl>';
        # Abre a pasta das Classes
        $ponteiro  = opendir($pasta);
        while ($arquivo = readdir($ponteiro)) {

            # Desconsidera os diretorios 
            if($arquivo == ".." || $arquivo == "."){
                continue;
            }

            # Divide o nome do arquivos
            $partesArquivo = explode('.',$arquivo);

            echo '<dd><a href="'.$pasta.'/'.$partesArquivo[0].'.'.$partesArquivo[1].'" target="_blank">'.$partesArquivo[0].'</a></dd>';
        }

        echo '</dl>';
        echo '</div>';
    $callout->fecha();

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}