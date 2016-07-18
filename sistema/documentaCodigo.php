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

    # Banco de Dados
    $linkBotao2 = new Link("Banco de Dados","documentaBd.php?fase=$fase");
    $linkBotao2->set_class('button');
    $linkBotao2->set_title('Exibe informações do banco de dados');
    $linkBotao2->set_accessKey('B');
    
    # Diagramas
    $linkBotao3 = new Link("Diagramas","documentaDiagrama.php?fase=$fase");
    $linkBotao3->set_class('button');
    $linkBotao3->set_title('Diagramas do sistema');
    $linkBotao3->set_accessKey('D');

    # Cria um menu
    $menu = new MenuBar();
    $menu->add_link($linkBotao1,"left");
    $menu->add_link($linkBotao2,"right");
    $menu->add_link($linkBotao3,"right");    
    $menu->show();

    switch ($fase)
    {
      case "Framework" :
          $pastaClasses = PASTA_CLASSES_GERAIS;
          $arquivoFuncao = PASTA_FUNCOES_GERAIS.'/funcoes.gerais.php';
          break;

      case "Grh" :
          $pastaClasses = PASTA_CLASSES_GRH;
          $arquivoFuncao = PASTA_FUNCOES_GRH.'/funcoes.especificas.php';
          break;

      case "areaServidor" :
          $pastaClasses = PASTA_CLASSES;
          $arquivoFuncao = PASTA_FUNCOES.'/funcoes.especificas.php';
          break;
    }

    # Topbar        
    $top = new TopBar($fase);
    $top->show();
    br();

    # Divide Coluna para classes e funções
    $grid2 = new Grid();

    # Coluna das classes
    $grid2->abreColuna(6);
    $callout = new Callout();
    $callout->abre();
    titulo('Classes');

    $grupoarquivo = null;
    br();
    echo '<dl>';
    # Abre a pasta das Classes
    $ponteiro  = opendir($pastaClasses);
    while ($arquivo = readdir($ponteiro)) {

        # Desconsidera os diretorios 
        if($arquivo == ".." || $arquivo == "." || $arquivo == "exemplos"){
            continue;
        }

        # Divide o nome do arquivos
        $partesArquivo = explode('.',$arquivo);

        if($grupoarquivo <> $partesArquivo[0]){
            echo '<dt>'.ucfirst($partesArquivo[0]).'</dt>';

            $grupoarquivo = $partesArquivo[0];
            echo '<dd><a href="documentaClasse.php?sistema='.$fase.'&classe='.$partesArquivo[0].'.'.$partesArquivo[1].'">'.$partesArquivo[1].'</a></dd>';

        }
        else{
            echo '<dd><a href="documentaClasse.php?sistema='.$fase.'&classe='.$partesArquivo[0].'.'.$partesArquivo[1].'">'.$partesArquivo[1].'</a></dd>';

        }
    }

    echo '</dl>';
    echo '</div>';
    $grid2->fechaColuna(); // Coluna das classes

    # Coluna das funções
    $grid2->abreColuna(6);

    $callout1 = new Callout();
    $callout1->abre();
    titulo('Funções');
    br();

    # Lê e guarda no array $lines o conteúdo do arquivo
    $lines = file ($arquivoFuncao);

    # Percorre o array
    foreach ($lines as $line_num => $line){
      $line = htmlspecialchars($line);

      # Função
      if (stristr($line, "function")){
        $posicao = stripos($line,'function');
        $posicaoParentesis = stripos($line,'(');
        $tamanhoNome = $posicaoParentesis - ($posicao+9);

        echo '<a href="documentaFuncao.php?sistema='.$fase.'&funcao='.substr($line, $posicao+9,$tamanhoNome).'">';
        echo substr($line, $posicao+9,$tamanhoNome);
        echo '</a>';
        br();
      }
    }
    $callout1->fecha();

    $grid2->fechaColuna();
    $grid2->fechaGrid();

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}