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

    botaoVoltar("documentacao.php");

    titulo('Diagramas');

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