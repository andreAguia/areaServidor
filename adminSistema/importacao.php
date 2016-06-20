<?php
/**
 * Rotina de Importação
 *  
 * By Alat
 */

# Configuração
include ("_config.php");

# Conecta ao Banco de Dados da Fenorte
$servidor = new Pessoal();
$uenf = new Doc();

# Começa uma nova página
$page = new Page();			
$page->iniciaPagina();

# Cabeçalho da Página
AreaServidor::cabecalho();

# Limita o tamanho da tela
$grid = new Grid();
$grid->abreColuna(12);

br();
titulo("Importação do Banco de dados");

$painel = new Callout();
$painel->abre();

# Faz a conexão com o servidor
$link = mysql_connect('localhost', 'root', '');
if (!$link) {
    die('Não foi possível conectar: ' . mysql_error());
}

# Exclui o banco grh velho
$sql = 'DROP DATABASE grh';
if (mysql_query($sql, $link)) {
    p("O banco de dados grh foi excluído com sucesso");
} else {
    p('Erro ao excluir o banco de dados: ' . mysql_error());
}

# Cria um novo banco grh
$sql = 'CREATE DATABASE grh';
if (mysql_query($sql, $link)) {
    p("O banco de dados grh foi criado");
} else {
    p('Erro criando o banco de dados: ' . mysql_error());
}

# Pega as tabelas do banco pessoal
$sql = "SHOW TABLES FROM pessoal";
$result = mysql_query($sql);

if (!$result) {
   p("DB Error, could not list tables");
   p('MySQL Error: ' . mysql_error());
    exit;
}

# pega os nomes das tabelas e joga em um array
$tabela = null; // array de tabelas
$campos = null; // array de campos

while ($row = mysql_fetch_row($result)) {
    $tabela[] = $row[0];
}

foreach ($tabela as $tab){
    $result = mysql_query("SHOW COLUMNS FROM pessoal.$tab");
    if (!$result) {
        echo 'Could not run query: ' . mysql_error();
        exit;
    }
    if (mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $campos[$tab][] = $row;
        }
    }
} 

print_r($campos);

$painel->fecha();
$grid->fechaColuna();
$grid->fechaGrid();        
$page->terminaPagina();