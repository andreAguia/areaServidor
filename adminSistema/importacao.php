<?php
/**
 * Rotina de Importação
 *  
 * By Alat
 */

# Configuração
include ("_config.php");

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
$sql = 'CREATE DATABASE grh CHARACTER SET utf8 COLLATE utf8_unicode_ci';
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

p("Lendo as estruturas das tabelas da Fenorte...");

while ($row = mysql_fetch_row($result)) {
    $tabela[] = $row[0];
}

# Pega a estrutura das tabelas e guarda nos arrays $tabela e $campos
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

#########################################################################
#                         tbfuncionario
#########################################################################

$tab = "tbfuncionario";

# Exibe o nome da tabela
hr();
echo $tab;

# Inicia o sql
$sql = "CREATE TABLE tbservidor (";

$sql .= "idservidor INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
$sql .= "PRIMARY KEY(idservidor),";

# le os nomes dos campos e preenche o sql
foreach ($campos[$tab] as $camp){
    switch ($camp['Field'])
    {
        case "senha_intra" :
        case "presenca" :    
        case "senha_tipo" :
        case "visivel_intra" :
        case "ult_acesso" :
        case "idGrupo" :
            break; 
        case "Sit" :
            $camp['Field'] = "situacao";            
        default :
            $sql .= $camp['Field']." ".$camp['Type'];
    
            if((isset($camp['NULL'])) AND  ($camp['NULL'] == "YES")){
                $sql .= " NOT NULL";
            }
            $sql .= ",";
            break;
    }
}

# Retira a última vírgula e coloca os parentesis
$sql = substr($sql,0,-1);
$sql .= ")";

# Criando a tabela no banco grh
mysql_select_db("grh") or die(mysql_error());
mysql_query($sql) Or die(mysql_error());
	
echo "... tabela criada no banco grh";
echo "... importando os dados";

# Conecta ao Banco de Dados da Fenorte
$select = "SELECT * FROM tbfuncionario";
$servidor = new Pessoal();
$result = $servidor->select($select);

# le o array e monta o sql de gravação
foreach ($result as $row){
    $sql = 'INSERT INTO grh.tbservidor (';
    
    # Nome dos campos
    foreach ($campos[$tab] as $camp){
        switch ($camp['Field']){
            case "senha_intra" :
            case "presenca" :    
            case "senha_tipo" :
            case "visivel_intra" :
            case "ult_acesso" :
            case "idGrupo" :
                break; 
            case "Sit" :
                $camp['Field'] = "situacao";            
            default :
                $sql .= $camp['Field'].",";
                break;
        }
    }
    # Retira a última vírgula e coloca os parentesis
    $sql = substr($sql,0,-1);
    $sql .= ') VALUES (';
    
    # Valores
    foreach ($campos[$tab] as $camp){
        switch ($camp['Field']){
            case "senha_intra" :
            case "presenca" :    
            case "senha_tipo" :
            case "visivel_intra" :
            case "ult_acesso" :
            case "idGrupo" :
                break; 
            #case "Sit" :
            #    $camp['Field'] = "situacao";            
            default :
                if(is_null($row[$camp['Field']])){
                    $sql .= 'NULL,';
                }else{
                    $sql .= '"'.$row[$camp['Field']].'",';
                }
                break;
        }
    }
    # Retira a última vírgula e coloca os parentesis
    $sql = substr($sql,0,-1);
    $sql .= ")";
    
    if(!mysql_query($sql)) {
      echo mysql_error();
    }
}

#########################################################################
#                         tbatestado
#########################################################################

$tab = "tbatestado";

# Exibe o nome da tabela
hr();
echo $tab;

# Inicia o sql
$sql = "CREATE TABLE $tab (";

$sql .= "idatestado INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
$sql .= "PRIMARY KEY(idatestado),";

# le os nomes dos campos e preenche o sql
foreach ($campos[$tab] as $camp){
    switch ($camp['Field'])
    {
        case "apagado" :
        case "log" :
            break; 
        case "nome_medico" :
            $sql .= "nomeMedico VARCHAR(50)";
    
            if((isset($camp['NULL'])) AND  ($camp['NULL'] == "YES")){
                $sql .= " NOT NULL";
            }
            $sql .= ",";
            break;
        case "especi_medico" :
            $sql .= "especialidade VARCHAR(50)";
    
            if((isset($camp['NULL'])) AND  ($camp['NULL'] == "YES")){
                $sql .= " NOT NULL";
            }
            $sql .= ",";
            break;    
        default :
            $sql .= $camp['Field']." ".$camp['Type'];
    
            if((isset($camp['NULL'])) AND  ($camp['NULL'] == "YES")){
                $sql .= " NOT NULL";
            }
            $sql .= ",";
            break;
    }
}



#print_r($campos);

$painel->fecha();
$grid->fechaColuna();
$grid->fechaGrid();        
$page->terminaPagina();