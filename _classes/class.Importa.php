<?php

class Importa
{
    private $nomeTabela;
    private $novoNomeTabela = NULL;     // Usado para quando se quer alterar o nome da tabela
    private $campoId;
    private $descricao;
    
    private $nomeVelho;
    private $nomeNovo;
    private $ignoraCampos;   // Campos a ser ignorados
    private $criarCampo;     // Campo a ser criado
    
#################################################################
    
    public function __construct($nomeTabela = NULL, $campoId = NULL,$descricao = NULL){
        $this->nomeTabela = $nomeTabela;
        $this->campoId = $campoId;
        $this->descricao = $descricao;
    }
    
#################################################################    
    
    public function novoNomeTabela($novoNomeTabela = NULL){
        $this->novoNomeTabela = $novoNomeTabela;
    }
    
#################################################################    
    
    public function mudarNome($descricao = NULL){
        $this->descricao = $descricao;
    }
    
#################################################################    
    
    public function criarCampo($criarCampo = NULL){
        $this->criarCampo = $criarCampo;
    }
    
#################################################################      
    
    public function ignoraCampos($ignoraCampos = NULL){
        if(is_null($ignoraCampos)){
            $this->ignoraCampos = array("");
        }else{
            $this->ignoraCampos = $ignoraCampos;
        }
    }
    
#################################################################        
    
    public function go(){
        # Faz a conexão com o servidor
        $link = mysql_connect('localhost', 'root', '');
        if (!$link) {
            die('Não foi possível conectar: ' . mysql_error());
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
        $tabela = NULL; // array de tabelas
        $campos = NULL; // array de campos

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
        
        $numRegistros = 0;      // Contador de registros importados

        # Inicia o sql
        if(is_null($this->novoNomeTabela)){
            $sql = "CREATE TABLE $this->nomeTabela (";
        }else{
            $sql = "CREATE TABLE $this->novoNomeTabela (";
        }
        

        $sql .= "PRIMARY KEY($this->campoId),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$this->nomeTabela] as $camp){
            switch ($camp['Field'])
            {
                case in_array($camp['Field'],$this->ignoraCampos):
                    break;
                case $this->campoId :
                    $sql .= $this->campoId." INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
                    break;
                case "matricula" :
                    $sql .= "idServidor INT(11) UNSIGNED NOT NULL,";
                    break;
                case "obs":
                    $altera = "ALTER TABLE pessoal.$this->nomeTabela CHANGE COLUMN obs obs LONGTEXT NULL DEFAULT NULL;";
                    # altera o campo no banco pessoal
                    mysql_select_db("pessoal") or die(mysql_error());
                    mysql_query($altera) Or die(mysql_error());
                    
                    $sql .= $camp['Field']." LONGTEXT";
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
        
        # Se tem algum campo para criar
        if(!is_null($this->criarCampo)){
            $sql .= $this->criarCampo.",";            
        }
        
        # Retira a última vírgula e coloca os parentesis
        $sql = substr($sql,0,-1);
        $sql .= ")";
        $sql .= "COMMENT = '";
        $sql .= utf8_decode($this->descricao);
        $sql .= "';";
        
        #echo $sql;
        
        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $this->nomeTabela";
        $servidor = new Pessoal2();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            if(is_null($this->novoNomeTabela)){
                $sql = 'INSERT INTO uenf_grh.'.$this->nomeTabela.' (';
            }else{
                $sql = 'INSERT INTO uenf_grh.'.$this->novoNomeTabela.' (';
            }
            
            # Nome dos campos
            foreach ($campos[$this->nomeTabela] as $camp){
                switch ($camp['Field']){
                    case in_array($camp['Field'],$this->ignoraCampos):
                        break;
                    case "matricula" :
                        $sql .= "idServidor,";
                        break;
                    default :                        
                        $sql .= $camp['Field'].",";
                        break;
                }
            }
            # Retira a última vírgula e coloca os parentesis
            $sql = substr($sql,0,-1);
            $sql .= ') VALUES (';

            # Valores
            foreach ($campos[$this->nomeTabela] as $camp){
                switch ($camp['Field']){
                    case in_array($camp['Field'],$this->ignoraCampos):
                        break;
                    case "matricula" :
                        $pessoal = new Pessoal();
                        $valor = $pessoal->get_idServidor($row[$camp['Field']]);
                        $sql .= $valor.',';
                        break;
                    default :
                        if((is_null($row[$camp['Field']])) OR ($row[$camp['Field']] == "")){
                            $sql .= 'NULL,';
                        }else{
                            $sql .= '"'.utf8_decode($row[$camp['Field']]).'",';
                        }
                        break;
                }
            }
            $numRegistros ++;
            # Retira a última vírgula e coloca os parentesis
            $sql = substr($sql,0,-1);
            $sql .= ")";

            if(!mysql_query($sql)) {
              echo mysql_error();
            }
        }

        echo $this->nomeTabela;
        if(is_null($this->novoNomeTabela)){
            echo " ".$numRegistros." importados";
        }else{
            echo " (".$this->novoNomeTabela.") ".$numRegistros." importados";
        }
        br();
        
    }

}