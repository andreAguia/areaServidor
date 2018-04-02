<?php
/**
 * Rotina de Importação
 *  
 * By Alat
 */

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

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

    # Botão voltar
    $linkBotao1 = new Link("Voltar",'administracao.php');
    $linkBotao1->set_class('button');
    $linkBotao1->set_title('Volta para a página anterior');
    $linkBotao1->set_accessKey('V');
    $menu->add_link($linkBotao1,"left");

    # Administração (intra)
    $linkBotao2 = new Link("Refazer","?");
    $linkBotao2->set_class('button');
    $linkBotao2->set_title('Refazer a Importação');
    $linkBotao2->set_accessKey('R');
    $menu->add_link($linkBotao2,"right");
    $menu->show();

    titulo("Importação do Banco de dados Antigo da FENORTE para UENF");

    # Verifica a fase do programa
    $fase = get('fase');

    switch ($fase)
    {
        case "" :
            br(4);
            aguarde();
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
            $tabela = NULL; // array de tabelas
            $campos = NULL; // array de campos

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

            $sql .= "idServidor INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,";
            $sql .= "PRIMARY KEY(idServidor),";

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
            $servidor = new Pessoal2();
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

            # tbatestado
            $tab = "tbatestado";
            $idTab = "idAtestado";
            $descricao = "Tabela de controle de atestado dos servidores";
            $ignoraCampos = array("apagado","log");

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbaverbacao
            $tab = "tbaverbacao";
            $idTab = "idAverbacao";
            $descricao = "Tabela de controle de tempo de serviço averbado";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbbanco
            $tab = "tbbanco";
            $idTab = "idBanco";
            $descricao = "Tabela auxiliar de bancos";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbcargo
            $tab = "tbcargo";
            $idTab = "idCargo";
            $descricao = "Tabela de cargos dos servidores estatutários";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbcedido
            $tab = "tbcedido";
            $idTab = "idCedido";
            $descricao = "Tabela de controle de servidores cedidos vindo de outros órgãos";
            $ignoraCampos = NULL;
            $criarCampo = "idCedido INT(11) UNSIGNED NOT NULL AUTO_INCREMENT";

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->criarCampo($criarCampo);          // Necessário pois não existia esse campo
            $importa->go();

            # tbclasse
            $tab = "tbclasse";
            $idTab = "idClasse";
            $descricao = "Tabela de classes e salários";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbcomissao
            $tab = "tbcomissao";
            $idTab = "idComissao";
            $descricao = "Tabela de cargos em comissão";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbconcurso
            $tab = "tbconcurso";
            $idTab = "idConcurso";
            $descricao = "Tabela de cadastro de concursos";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbcontatos
            $tab = "tbcontatos";
            $idTab = "idContatos";
            $descricao = "Tabela de telefones e emails dos servidores";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbdependente
            $tab = "tbdependente";
            $idTab = "idDependente";
            $descricao = "Tabela dos dependentes dos servidores";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go(); 

            # tbdiaria
            $tab = "tbdiaria";
            $idTab = "idDiaria";
            $descricao = "Tabela de controle de diárias dos servidores";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go(); 

            # tbdocumentacao
            $tab = "tbdocumentacao";
            $idTab = "idDocumentacao";
            $descricao = "Tabela dos dados documentais dos servidores";
            $ignoraCampos = array("idDocumento");                              // Renomeia a chave 
            $criarCampo = "idDocumentacao INT(11) UNSIGNED NOT NULL AUTO_INCREMENT";

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->criarCampo($criarCampo);
            $importa->go();

            # tbelogio
            $tab = "tbelogio";
            $idTab = "idElogio";
            $descricao = "Tabela de controle de regstro de elogios dos servidores";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbescolaridade
            $tab = "tbescolaridade";
            $idTab = "idEscolaridade";
            $descricao = "Tabela dos tipos de escolaridade possíveis dos servidores";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbestciv
            $tab = "tbestciv";
            $idTab = "idEstCiv";
            $descricao = "Tabela dos tipos de estado civil";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbferias
            $tab = "tbferias";
            $idTab = "idFerias";
            $descricao = "Tabela de controle de férias dos servidores";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbfolga
            $tab = "tbfolga";
            $idTab = "idFolga";
            $descricao = "Tabela de controle de dias folgados por ter trabalhado no TRE";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbtrabalhotre
            $tab = "tbfolgatre";
            $idTab = "idTrabalhoTre";
            $descricao = "Tabela de controle de dias trabalhados no TRE";
            $ignoraCampos = array("idFolgaTre");
            $criarCampo = "idTrabalhoTre INT(11) UNSIGNED NOT NULL AUTO_INCREMENT";

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->novoNomeTabela("tbtrabalhotre");
            $importa->criarCampo($criarCampo);
            $importa->go();

            # tbformacao
            $tab = "tbformacao";
            $idTab = "idFormacao";
            $descricao = "Tabela de controle da formação escolar do servidor";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbgratificacao
            $tab = "tbgratif";
            $idTab = "idGratificacao";
            $descricao = "Tabela de controle das gratificações especiais dos servidores";
            $ignoraCampos = array("idGratif");
            $criarCampo = "idGratificacao INT(11) UNSIGNED NOT NULL AUTO_INCREMENT";

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->novoNomeTabela("tbgratificacao");
            $importa->criarCampo($criarCampo);
            $importa->go();

            # tbhistcessao
            $tab = "tbhistcessao";
            $idTab = "idHistCessao";
            $descricao = "Tabela de histórico de cessões de servidores da uenf para outros órtgãos";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbhistlot
            $tab = "tbhistlot";
            $idTab = "idHistLot";
            $descricao = "Tabela de histórico de lotações dos servidores";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tblicenca
            $tab = "tblicenca";
            $idTab = "idLicenca";
            $descricao = "Tabela de controle de licencas dos servidores";
            $ignoraCampos = array("apagado","log");

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tblotacao
            $tab = "tblotacao";
            $idTab = "idLotacao";
            $descricao = "Tabela de controle de lotação dos servidores";
            $ignoraCampos = array("repPatrimonio");

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbparentesco
            $tab = "tbparentesco";
            $idTab = "idParentesco";
            $descricao = "Tabela de tipos de parentesco";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbperfil
            $tab = "tbperfil";
            $idTab = "idPerfil";
            $descricao = "Tabela de perfil dos servidores";
            $ignoraCampos = array("matIni","matFim");

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbpessoa
            $tab = "tbpessoa";
            $idTab = "idPessoa";
            $descricao = "Tabela dos dados pessoais";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbplano
            $tab = "tbplano";
            $idTab = "idPlano";
            $descricao = "Tabela dos planos";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbprogressao
            $tab = "tbprogressao";
            $idTab = "idProgressao";
            $descricao = "Tabela de controle de progressão e enquadramento dos servidores";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbpublicacaopremio
            $tab = "tbpublicacaopremio";
            $idTab = "idPublicacaoPremio";
            $descricao = "Tabela de controle de publicação de licença prêmio dos servidores";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbsituacao
            $tab = "tbsituacao";
            $idTab = "idSituacao";
            $descricao = "Tabela dos tipos de situação de um servidor";
            $ignoraCampos = array("IdSit");                              // Renomeia a chave 
            $criarCampo = "idSituacao INT(11) UNSIGNED NOT NULL AUTO_INCREMENT";

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->criarCampo($criarCampo);
            $importa->go();

            # tbtipocomissao
            $tab = "tbtipocomissao";
            $idTab = "idTipoComissao";
            $descricao = "Tabela dos tipos de cargos em comissão";
            $ignoraCampos = array("exibeSite");

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbtipolicenca
            $tab = "tbtipolicenca";
            $idTab = "idTpLicenca";
            $descricao = "Tabela dos tipos de licença";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbtipoprogressao
            $tab = "tbtipoprogressao";
            $idTab = "idTpProgressao";
            $descricao = "Tabela dos tipos de progressao e enquadramento";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            # tbtrienio
            $tab = "tbtrienio";
            $idTab = "idTrienio";
            $descricao = "Tabela de controle de triênio dos servidores";
            $ignoraCampos = NULL;

            $importa = new Importa($tab,$idTab,$descricao);
            $importa->ignoraCampos($ignoraCampos);
            $importa->go();

            $painel->fecha();
            break;
    }

    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}