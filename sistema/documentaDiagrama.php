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
    $banco = get('banco');
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    # Botão voltar
    $linkBotao1 = new Link("Voltar",'administracao.php');
    $linkBotao1->set_class('button');
    $linkBotao1->set_title('Volta para a página anterior');
    $linkBotao1->set_accessKey('V');

    # Cria um menu
    $menu = new MenuBar();
    $menu->add_link($linkBotao1,"left");
    $menu->show();
    
    $grid->fechaColuna();
    $grid->fechaGrid();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    switch ($banco){

      case "grh" :
          $pasta = '../_diagramas/grh';
          break;

      case "areaservidor" :
          $pasta = '../_diagramas/areaServidor';
          break;
    }
    
    # Titulo
    tituloTable("Diagramas");
    br();
    
    # Verifica a existencia da pasta
    if (file_exists($pasta)) {
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
    }else{
        $callout = new Callout();
        $callout->abre();
            p('Nenhum item encontrado !!','center');
        $callout->fecha();
    }

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}