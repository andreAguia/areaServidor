<?php

# Configuração
include ("_config.php");

# Começa uma nova página
$page = new Page();
$page->iniciaPagina();

# Cabeçalho
AreaServidor::cabecalho();

# Verifica a fase do programa
$fase = get('fase','Framework');

# Limita o tamanho da tela
$grid = new Grid();
$grid->abreColuna(12);

switch ($fase)
{
  case "Framework" :
      $pastaClasses = PASTA_CLASSES_GERAIS;
      $arquivoFuncao = PASTA_FUNCOES_GERAIS.'/funcoes.gerais.php';
      $classBotao2 = 'disabled button';
      $classBotao3 = 'button';
      break;

  case "Pessoal" :
      $pastaClasses = PASTA_CLASSES;
      $arquivoFuncao = PASTA_FUNCOES.'/funcoes.especificas.php';
      $classBotao2 = 'button';
      $classBotao3 = 'disabled button';
      break;
}

# Cria um menu
$menu = new MenuBar();

# Botão voltar
$linkBotao1 = new Link("Voltar",'administracao.php');
$linkBotao1->set_class('button');
$linkBotao1->set_title('Volta para a página anterior');
$linkBotao1->set_accessKey('V');
$menu->add_link($linkBotao1,"left");

# Framework
$linkBotao2 = new Link("Framework","documentacao.php?fase=Framework");
$linkBotao2->set_class($classBotao2);
$linkBotao2->set_title('Documentação das Classes e Funções do Framework');
$linkBotao2->set_accessKey('F');
$menu->add_link($linkBotao2,"right");

# Sistema de Pessoal
$linkBotao3 = new Link("Pessoal","documentacao.php?fase=Pessoal");
$linkBotao3->set_class($classBotao3);
$linkBotao3->set_title('Documentação das Classes e Funções do Sistema de Pessoal');
$linkBotao3->set_accessKey('P');
$menu->add_link($linkBotao3,"right");

# Botão bd
$linkBotao4 = new Link("Banco de Dados","documentaBd.php");
$linkBotao4->set_class('button');
$linkBotao4->set_title('Documentação de Banco de Dados');
$linkBotao4->set_accessKey('B');
$menu->add_link($linkBotao4,"right");

$menu->show();

##### Framework #####

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
        echo '<dd><a href="documentaClasse.php?classe='.$partesArquivo[0].'.'.$partesArquivo[1].'">'.$partesArquivo[1].'</a></dd>';
       
    }
    else{
        echo '<dd><a href="documentaClasse.php?classe='.$partesArquivo[0].'.'.$partesArquivo[1].'">'.$partesArquivo[1].'</a></dd>';
       
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

    echo '<a href="documentaFuncao.php?funcao='.substr($line, $posicao+9,$tamanhoNome).'">';
    echo substr($line, $posicao+9,$tamanhoNome);
    echo '</a>';
    br();
  }
}
$callout1->fecha();

$grid2->fechaColuna();
$grid2->fechaGrid();

##### Intranet #####

##### Pessoal #####

$grid->fechaColuna();
$grid->fechaGrid();

$page->terminaPagina();