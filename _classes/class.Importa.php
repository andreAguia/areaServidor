<?php

class Importa
{
    private $nomeTabela;
    private $campoId;
    private $descricao;
    
    private $nomeVelho;
    private $nomeNovo;
    
#################################################################
    
    public function __construct($nomeTabela = NULL, $campoId = NULL,$descricao = NULL){
        $this->nomeTabela = $nomeTabela;
        $this->campoId = $campoId;
        $this->descricao = $descricao;
    }
    
#################################################################    
    
    public function mudarNome($arraudescricao = NULL){
        $this->descricao = $descricao;
    }
    
#################################################################    
    
    public function go(){
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
        $sql = "CREATE TABLE $this->nomeTabela (";

        $sql .= "PRIMARY KEY($this->campoId),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$this->nomeTabela] as $camp){
            switch ($camp['Field'])
            {
                case $this->campoId :
                    $sql .= $this->campoId." INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
                    break;
                case "matricula" :
                    $sql .= "idServidor INT(11) UNSIGNED NOT NULL,";
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

        # Retira a última vírgula e coloca os parentesis
        $sql = substr($sql,0,-1);
        $sql .= ")";
        $sql .= "COMMENT = '";
        $sql .= utf8_decode($this->descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $this->nomeTabela";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$this->nomeTabela.' (';

            # Nome dos campos
            foreach ($campos[$this->nomeTabela] as $camp){
                switch ($camp['Field']){
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
                    case "matricula" :
                        $pessoal2 = new Pessoal2();
                        $valor = $pessoal2->get_idServidor($row[$camp['Field']]);
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

        echo $numRegistros." importados";
        br();
        
    }

}