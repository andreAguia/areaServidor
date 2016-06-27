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

# Verifica a fase do programa
$fase = get('fase');

switch ($fase)
{
    case "" :
        br(4);
        mensagemAguarde();
        loadPage('?fase=importa');
        
        
        break;
    case"importa" :

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
        $descricao = "Tabela principal de servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
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
        $sql .= "COMMENT = '";
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

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

        #########################################################################
        #                         tbatestado
        #########################################################################

        $tab = "tbatestado";
        $idTab = "idatestado";
        $descricao = "Tabela de controle de atestado dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case "apagado" :
                case "log" :
                    break;
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
                    break;
                case "matricula" :
                    $sql .= "idServidor INT(11) UNSIGNED NOT NULL,";
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

        # Retira a última vírgula e coloca os parentesis
        $sql = substr($sql,0,-1);
        $sql .= ")";
        $sql .= "COMMENT = '";
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "apagado" :
                    case "log" :
                        break;
                    case "matricula" :
                        $sql .= "idServidor,";
                        break;
                    case "nome_medico" :
                        $camp['Field'] = "nomeMedico";
                        $sql .= $camp['Field'].",";
                        break;
                    case "especi_medico" :
                        $camp['Field'] = "especialidade";
                        $sql .= $camp['Field'].",";
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
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "apagado" :
                    case "log" :
                        break;
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
        
        ########################################################################
        #                         tbaverbacao
        #########################################################################

        $tab = "tbaverbacao";
        $idTab = "idaverbacao";
        $descricao = "Tabela de controle de tempo de serviço averbado";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbbanco
        #########################################################################

        $tab = "tbbanco";
        $idTab = "idbanco";
        $descricao = "Tabela auxiliar de bancos";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela       
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbcargo
        #########################################################################

        $tab = "tbcargo";
        $idTab = "idcargo";
        $descricao = "Tabela de cargos dos servidores estatutários";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        ########################################################################
        #                         tbcedido
        #########################################################################

        $tab = "tbcedido";
        $idTab = "idcedido";
        $descricao = "Tabela de controle de servidores cedidos vindo de outros órgãos";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela        
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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
        
        #########################################################################
        #                         tbclasse
        #########################################################################

        $tab = "tbclasse";
        $idTab = "idclasse";
        $descricao = "Tabela de classes e salários";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbcomissao
        #########################################################################

        $tab = "tbcomissao";
        $idTab = "idcomissao";
        $descricao = "Tabela de cargos em comissão";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbconcurso
        #########################################################################

        $tab = "tbconcurso";
        $idTab = "idconcurso";
        $descricao = "Tabela de cadastro de concursos";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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
        
        #########################################################################
        #                         tbcontatos
        #########################################################################

        $tab = "tbcontatos";
        $idTab = "idcontatos";
        $descricao = "Tabela de telefones e emails dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbdbv
        #########################################################################

        $tab = "tbdbv";
        $idTab = "iddbv";
        $descricao = "Tabela de declaração de bens e valores dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbdependente
        #########################################################################

        $tab = "tbdependente";
        $idTab = "iddependente";
        $descricao = "Tabela dos dependentes dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbdiaria
        #########################################################################

        $tab = "tbdiaria";
        $idTab = "iddiaria";
        $descricao = "Tabela de controle de diárias dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbdocumentacao
        #########################################################################

        $tab = "tbdocumentacao";
        $idTab = "iddocumentacao";
        $descricao = "Tabela dos dados documentais dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());
        
        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbelogio
        #########################################################################

        $tab = "tbelogio";
        $idTab = "idelogio";
        $descricao = "Tabela de controle de regstro de elogios dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbescolaridade
        #########################################################################

        $tab = "tbescolaridade";
        $idTab = "idescolaridade";
        $descricao = "Tabela dos tipos de escolaridade possíveis dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbestciv
        #########################################################################

        $tab = "tbestciv";
        $idTab = "idestciv";
        $descricao = "Tabela dos tipos de estado civil";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbfaltas
        #########################################################################

        $tab = "tbfaltas";
        $idTab = "idfaltas";
        $descricao = "Tabela de controle de faltas dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbferias
        #########################################################################

        $tab = "tbferias";
        $idTab = "idferias";
        $descricao = "Tabela de controle de férias dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbfolga
        #########################################################################

        $tab = "tbfolga";
        $idTab = "idfolga";
        $descricao = "Tabela de controle de dias folgados por ter trabalhado no TRE";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());
        
        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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
        
        #########################################################################
        #                         tbtrabalhotre
        #########################################################################

        $tab = "tbtrabalhotre";
        $idTab = "idtrabalhotre";
        $descricao = "Tabela de controle de dias trabalhados no TRE";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        
        #########################################################################
        #                         tbformacao
        #########################################################################

        $tab = "tbformacao";
        $idTab = "idformacao";
        $descricao = "Tabela de controle da formação escolar do servidor";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbgratificacao
        #########################################################################

        $tab = "tbgratificacao";
        $idTab = "idgratificacao";
        $descricao = "Tabela de controle das gratificações especiais dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbhistcessao
        #########################################################################

        $tab = "tbhistcessao";
        $idTab = "idhistcessao";
        $descricao = "Tabela de histórico de cessões de servidores da uenf para outros órtgãos";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbhistlot
        #########################################################################

        $tab = "tbhistlotacao";
        $idTab = "idhistlotacao";
        $descricao = "Tabela de histórico de lotações dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tblicenca
        #########################################################################

        $tab = "tblicenca";
        $idTab = "idlicenca";
        $descricao = "Tabela de controle de licencas dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case "apagado" :
                case "log" :
                    break;
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "apagado" :
                    case "log" :
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
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "apagado" :
                    case "log" :
                        break;
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

        #########################################################################
        #                         tblotacao
        #########################################################################

        $tab = "tblotacao";
        $idTab = "idlotacao";
        $descricao = "Tabela de controle de lotação dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case "repPatrimonio" :        
                    break;
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "repPatrimonio" :        
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
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "repPatrimonio" :
                        break;
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

        #########################################################################
        #                         tbparentesco
        #########################################################################

        $tab = "tbparentesco";
        $idTab = "idparentesco";
        $descricao = "Tabela de tipos de parentesco";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbperfil
        #########################################################################

        $tab = "tbperfil";
        $idTab = "idperfil";
        $descricao = "Tabela de perfil dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case "matIni" : 
                case "matFim" : 
                    break;
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "matIni" : 
                    case "matFim" :         
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
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "matIni" : 
                    case "matFim" : 
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

        #########################################################################
        #                         tbpessoa
        #########################################################################

        $tab = "tbpessoa";
        $idTab = "idpessoa";
        $descricao = "Tabela dos dados pessoais";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbplano
        #########################################################################

        $tab = "tbplano";
        $idTab = "idplano";
        $descricao = "Tabela dos planos";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbprogressao
        #########################################################################

        $tab = "tbprogressao";
        $idTab = "idprogressao";
        $descricao = "Tabela de controle de progressão e enquadramento dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbpublicacaopremio
        #########################################################################

        $tab = "tbpublicacaopremio";
        $idTab = "idpublicacaopremio";
        $descricao = "Tabela de controle de publicação de licença prêmio dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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
        
        #########################################################################
        #                         tbsituacao
        #########################################################################

        $tab = "tbsituacao";
        $idTab = "idsit";
        $descricao = "Tabela dos tipos de situação de um servidor";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY(idSituacao),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "idSituacao INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "idSit" :
                        $sql .= "idsituacao,";
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
        
        #########################################################################
        #                         tbsuspensao
        #########################################################################

        $tab = "tbsuspensao";
        $idTab = "idsuspensao";
        $descricao = "Tabela de controle de suspenão de servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        #########################################################################
        #                         tbtipocomissao
        #########################################################################

        $tab = "tbtipocomissao";
        $idTab = "idtipocomissao";
        $descricao = "Tabela dos tipos de cargos em comissão";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case "exibeSite":
                    break;
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "exibeSite":
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
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
                    case "exibeSite":
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

        #########################################################################
        #                         tbtipolicenca
        #########################################################################

        $tab = "tbtipolicenca";
        $idTab = "idtipolicenca";
        $descricao = "Tabela dos tipos de licença";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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
        
        #########################################################################
        #                         tbtipoprogressao
        #########################################################################

        $tab = "tbtipoprogressao";
        $idTab = "idtipoprogressao";
        $descricao = "Tabela dos tipos de progressao e enquadramento";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;

        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        echo "<td>tabela criada no banco grh</td>";
        echo "<td>importando os dados</td>";

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
                switch ($camp['Field']){
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

        #########################################################################
        #                         tbtrienio
        #########################################################################

        $tab = "tbtrienio";
        $idTab = "idtrienio";
        $descricao = "Tabela de controle de triênio dos servidores";

        $numRegistros = 0;      // Contador de registros importados

        # Exibe o nome da tabela
        echo $tab;
        
        # Inicia o sql
        $sql = "CREATE TABLE $tab (";

        $sql .= "PRIMARY KEY($idTab),";

        # le os nomes dos campos e preenche o sql
        foreach ($campos[$tab] as $camp){
            switch ($camp['Field'])
            {
                case $idTab :
                    $sql .= "$idTab INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
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
        $sql .= utf8_decode($descricao);
        $sql .= "';";

        # Criando a tabela no banco grh
        mysql_select_db("grh") or die(mysql_error());
        mysql_query($sql) Or die(mysql_error());

        # Conecta ao Banco de Dados da Fenorte
        $select = "SELECT * FROM $tab";
        $servidor = new Pessoal();
        $result = $servidor->select($select);

        # le o array e monta o sql de gravação
        foreach ($result as $row){
            $sql = 'INSERT INTO grh.'.$tab.' (';

            # Nome dos campos
            foreach ($campos[$tab] as $camp){
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
            foreach ($campos[$tab] as $camp){
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

        $painel->fecha();
        break;
}

$grid->fechaColuna();
$grid->fechaGrid();        
$page->terminaPagina();