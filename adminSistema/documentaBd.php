<?php

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

# Verifica a fase do programa
$fase = get('fase','pessoal');

# Cria um menu
$menu = new MenuBar();

# Botão voltar
$linkBotao1 = new Link("Voltar",'documentacao.php');
$linkBotao1->set_class('button');
$linkBotao1->set_title('Volta para a página anterior');
$linkBotao1->set_accessKey('V');
$menu->add_link($linkBotao1,"left");

# Administração (intra)
$linkBotao2 = new Link("Intra","?fase=intra");
if($fase <> "intra"){
   $linkBotao2->set_class('button');
}else{
   $linkBotao2->set_class('disabled button');
}
$linkBotao2->set_title('Banco de Dados da Documentação');
$linkBotao2->set_accessKey('I');
$menu->add_link($linkBotao2,"right");

# Banco Pessoal
$linkBotao3 = new Link("Pessoal","?fase=pessoal");
if($fase <> "pessoal"){
   $linkBotao3->set_class('button');
}else{
   $linkBotao3->set_class('disabled button');
}
$linkBotao3->set_title('Banco de Dados do Sistema de Pessoal');
$linkBotao3->set_accessKey('P');
$menu->add_link($linkBotao3,"right");

# Banco Grh
$linkBotao5 = new Link("Grh","?fase=grh");
if($fase <> "grh"){
   $linkBotao5->set_class('button');
}else{
   $linkBotao5->set_class('disabled button');
}
$linkBotao5->set_title('Banco de Dados do Novo Sistema de Pessoal');
$linkBotao5->set_accessKey('G');
$menu->add_link($linkBotao5,"right");

# Importação
$linkBotao4 = new Link("Importação","?fase=grh001");
if($fase <> "grh001"){
   $linkBotao4->set_class('button');
}else{
   $linkBotao4->set_class('disabled button');
}
$linkBotao4->set_title('Banco de Dados para importação');
#$linkBotao4->set_accessKey('I');
$menu->add_link($linkBotao4,"right");

$menu->show();

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
             FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$fase."'"; 
$conteudo = $servico->select($select);

$label = array("Nome","Descrição","Tipo","Motor","Num. Registros","Tamanho Médio","Tamanho Total","AI");
$width = array(15,25,10,10,10,10,10,5);
#$function = array("datetime_to_php",null,null,null,"get_nome");
$align = array("left","left");

# Monta a tabela
$tabela = new Tabela();
$tabela->set_conteudo($conteudo);
$tabela->set_cabecalho($label,$width,$align);
#$tabela->set_funcao($function); 
$tabela->set_numeroOrdem(true);
$tabela->set_editar("documentaTabela.php?bd=".$fase);
$tabela->set_idCampo('TABLE_NAME');
$tabela->set_nomeColunaEditar('Ver');
$tabela->set_editarBotao('ver.png');

# exibe a tabela
$tabela->show();

$grid->fechaColuna();
$grid->fechaGrid();

$page->terminaPagina();