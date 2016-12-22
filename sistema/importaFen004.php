<?php
/**
 * Rotina de Importação
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();
    
    # Verifica a fase do programa
    $fase = get('fase');
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    br();

    switch ($fase)
    {
        case "" :
            br(4);
            aguarde();
            br();    
           
            loadPage('?fase=importa');
            break;

        case"importa" :            
        # Cria um menu
        $menu = new MenuBar();

        # Botão voltar
        $linkBotao1 = new Link("Voltar",'administracao.php?fase=importacao');
        $linkBotao1->set_class('button');
        $linkBotao1->set_title('Volta para a página anterior');
        $linkBotao1->set_accessKey('V');
        $menu->add_link($linkBotao1,"left");

        # Refazer
        $linkBotao2 = new Link("Refazer","?");
        $linkBotao2->set_class('button');
        $linkBotao2->set_title('Refazer a Importação');
        $linkBotao2->set_accessKey('R');
        $menu->add_link($linkBotao2,"right");
        $menu->show();

        # Conecta ao banco
        $uenf = new Uenf();
        $pessoal = new Pessoal();

        # Pega a quantidade de registros com os bolsistas
        $select = "select matr from fen004 WHERE tp_afa = 20";
        $result = $uenf->select($select);
        $totalRegistros = count($result); 

        titulo('Importação da tabela de Afastamentos - '.$totalRegistros." Registros");

        # Cria um painel
        $painel = new Callout();
        $painel->abre();

        # Variáveis
        $numItens = 0;              // Número de itens importados
        $numItensDescartados = 0;   // Número de itens descartados   
        $numItensImportados = 0;    // Número de itens importados
        $numItensTblicenca = 0;     // Número de itens importados para tblicenca
        $numItensTbTRE = 0;         // Número de itens importados para tbtrabalhoTre
        $numItensTbAtestado = 0;    // Número de itens importados para tbatestado
        $numItensFerias = 0;        // Número de itens importados
        $numItensNAnalisados = 0;   // Número de itens não analisados
        $numIdInvalido = 0;         // Número de ids inválidos

        # Inicia a Importação
        $select = "SELECT * FROM fen004 WHERE tp_afa = 20 ORDER BY DT_INI";

        $conteudo = $uenf->select($select,true);

        echo "<table class='tabelaPadrao'>";
        echo "<tr>";
        echo "<th>Matrícula</th>";
        echo "<th>IdServidor</th>";
        echo "<th>Data Inicial</th>";
        echo "<th>Data Final</th>";
        echo "<th>Tipo</th>"; 
        echo "<th>Importar para</th>"; 
        echo "</tr>";

        # Percorre a tabela
        foreach ($conteudo as $campo){
            # Define variável para guardar o tipo
            $tipo = NULL;

            # Define os valores
            $matricula = $campo[0];
            $dt_ini = substr(datetime_to_php($campo[1]),0,10); 
            $tp_afa = $campo[2];
            $dt_fim = substr(datetime_to_php($campo[3]),0,10); 

            # Dados para gravação quando for para tblicença
            $idServidor = $pessoal->get_idServidor($matricula);
            $tabela = 'tblicenca';
            $idCampo = 'idLicenca';
            $numDias = dataDif($dt_ini,$dt_fim)+1;
            $sexo = $pessoal->get_sexo($idServidor);

            # Inicia a tabela que exibe o processo de impotação
            echo "<tr>";
            echo "<td>".$matricula."</td>";
            echo "<td>".$idServidor."</td>";
            echo "<td>".$dt_ini."</td>";
            echo "<td>".$dt_fim."</td>";
            echo "<td>".$tp_afa."</td>";
            echo "<td>";

            # Volta a data ao formato de gravação
            $dt_ini = date_to_bd($dt_ini);

            # Verifica validade do idServidor
            if(is_null($idServidor)){
                $numIdInvalido++;
                label("idInválido. Não é possível importar","alert");
                echo "</td>";
                continue;
            }

            switch ($tp_afa){
                case 1:
                    label("Ativo.Não importar.","alert");
                    $numItensDescartados++;
                    break;
                
                ##############################################################################
                
                case 2:
                    $tipo = 16;
                    echo "Licença Sem Vencimento. Importar para tipo 16.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;
                
                ##############################################################################
                
                case 3:
                    $tipo = 9;
                    echo "Acidente de trabalho. Importar para tipo 9.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;
                
                ##############################################################################
                
                case 4:
                    $tipo = 4;
                    echo "Serviço milirar. Importar para tipo 4.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 5:
                    $tipo = 18;
                    echo "Licença Gestante. Importar para tipo 18.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 6:
                    $tipo = 21;
                    echo "Afastado por doença. Importar para tipo 21.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 7:
                    label("Cedido a outro órgão - Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 8:
                    $tipo = 29;
                    echo "Outros. Importar para tipo 29.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 9:
                    label("Demitido - Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 0:
                    label("Cedido a outro órgão - Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 10:
                    $tipo = 25;
                    echo "Faltas. Importar para tipo 25.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 11:
                    $tipo = 12;
                    echo "Afastamento por Luto. Importar para tipo 12.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 12:
                    $tipo = 11;
                    echo "Afastamento por casamento. Importar para tipo 11.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 13:
                    label("Afastamento obrigatório. Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 14:
                    $tipo = 22;
                    echo "Afastamento para juri. Importar para tipo 22.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 15:
                    $tipo = "TRE";
                    echo "Afastamento para TRE. Importar para tabela do TRE.";
                    $numItensImportados++;
                    $numItensTbTRE++;
                    
                    # Grava na tabela
                    $campos = array("idServidor","data","folgas","dias");
                    $valor = array($idServidor,$dt_ini,($numDias*2),$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,'tbtrabalhotre','idtrabalhotre',FALSE);
                    break;

                ##############################################################################
                
                case 16:
                    $tipo = 17;
                    echo "Afastamento para Campanha eleitoral. Importar para tipo 17.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 17:
                    $tipo = "ATESTADO";
                    echo "Falta abonada.Atestado. Importar para tabela de atestados.";
                    $numItensImportados++;
                    $numItensTbAtestado++;
                    
                    # Grava na tabela
                    $campos = array("idServidor","dtInicio","numDias");
                    $valor = array($idServidor,$dt_ini,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,'tbatestado','idAtestado',FALSE);
                    break;

                ##############################################################################
                
                case 18:
                case 19:
                    label("Férias. Importar para tabela de férias.Sem registros!! Não Importar.","alert");
                    $numItensFerias++;
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 20:    #############################
                    $tipo = 6;
                    echo "Lic Especial (premio). Importar para tipo 6.";
                    $numItensImportados++;
                    $numItensTblicenca++;
                    
                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 21:
                    $tipo = 13;
                    echo "Lic Paternidade. Importar para tipo 13.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 22:
                    $tipo = 21;
                    echo "Lic Saúde INSS. Importar para tipo 21.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 23:
                    $tipo = 21;
                    echo "Lic Saúde - Inicial com Alta. Importar para tipo 21.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias","tipo","alta");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias,1,1);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 24:
                    $tipo = 21;
                    echo "Lic Saúde - Inicial sem Alta. Importar para tipo 21.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias","tipo","alta");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias,1,NULL);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 25:
                    $tipo = 21;
                    echo "Lic Saúde - Prorrogação - com Alta. Importar para tipo 21.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias","tipo","alta");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias,2,1);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 26: 
                    $tipo = 21;
                    echo "Lic Saúde - Prorrogação - sem Alta. Importar para tipo 21.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias","tipo","alta");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias,2,NULL);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 27:
                    $tipo = 2;
                    echo "Lic Saúde INSS família. Importar para tipo 2.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 28:
                    if($sexo == "Feminino"){
                        $tipo = 14;
                    }else{
                        $tipo = 15;
                    }

                    echo "Lic Adoção. Importar para tipo 14 ou 15. Dependendo do gênero.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 29:
                    $tipo = 10;
                    echo "Lic Amamentação. Importar para tipo 10.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 30:
                    label("Prisao Adm. Ignora.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 31:    
                    label("Aguardando Aposetadoria Compulsória. Ignora.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 32:
                    $tipo = 26;
                    echo "Suspensao. Importar para tipo 26.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 33:
                    $tipo = 25;
                    echo "Faltas. Importar para tipo 25.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 34:
                    label("Disp outro orgao.Não Importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 35:
                    label("Inq. Administrativo.Não Importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 36:
                    echo "Afastamento para TRE. Importar para tabela do TRE.";
                    $numItensImportados++;
                    $numItensTbTRE++;
                    
                    # Grava na tabela
                    $campos = array("idServidor","data","folgas","dias");
                    $valor = array($idServidor,$dt_ini,($numDias*2),$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,'tbtrabalhotre','idtrabalhotre',FALSE);
                    break;

                ##############################################################################
                
                case 37:
                    $tipo = 7;
                    echo "Afastamento para estudo. Importar para tipo 7.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 38:
                case 39:
                case 40:
                case 41:
                case 42:
                case 43:
                    label("Aposentadoria. Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 44:
                    label("Estagio Experimental. Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 45:
                    $tipo = 27;
                    echo "Falta para prova. Importar para tipo 27.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 46:
                    label("Falta por greve. Ainda não sei. Decidido não importar","alert");  #######
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 47:
                    label("Abandono de serviço. Não importar","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 48:
                    $tipo = 8;
                    echo "Afastamento para exercer mandato. Importar para tipo 8.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 49:
                    $tipo = 4;
                    echo "Lic serv Militar. Importar para tipo 4.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 50:
                    $tipo = 5;
                    echo "Lic acompanhar conjuge. Importar para tipo 5.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 51:
                    $tipo = 16;
                    echo "Lic sem vencimentos. Importar para tipo 16.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 52:
                    $tipo = 28;
                    echo "Afastamento por prisão.. Importar para tipo 28.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 53:
                    label("Pedido exoneração. Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 54:
                    label("Falecimento. Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 55:
                    label("Rescisão do contrato. Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 56:
                    $tipo = 23;
                    echo "Afastamento do pais. Importar para tipo 23.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 57:
                    label("Suspensão do contrato. Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 58:
                    $tipo = 9;
                    echo "Acidente de trabalho. Importar para tipo 9.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 59:
                    label("Impontualidade. Não Importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 60:
                    $tipo = 20;
                    echo "Doação de sangue. Importar para tipo 20.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 61:
                    label("Resolução da Sare 3434/4. Não Importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 62:
                    $tipo = 7;
                    echo "Licença para estudo. Importar para tipo 7.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 63:
                    $tipo = 9;
                    echo "Acidente de trabalho. Importar para tipo 9.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 64:
                    label("Suspensão preventiva. Não importar.","alert");
                    $numItensDescartados++;
                    break;

                ##############################################################################
                
                case 65:
                    $tipo = 23;
                    echo "Afastamento do pais. Importar para tipo 23.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 66:
                    $tipo = 7;
                    echo "Pos doc. Importar para tipo 7.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 67:
                    $tipo = 12;
                    echo "Licença Luto. Importar para tipo 12.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                case 68:
                    $tipo = 7;
                    echo "Estagio senior. Importar para tipo 7.";
                    $numItensImportados++;
                    $numItensTblicenca++;

                    # Grava na tabela
                    $campos = array("idServidor","dtinicial","idTpLicenca","numDias");
                    $valor = array($idServidor,$dt_ini,$tipo,$numDias);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    break;

                ##############################################################################
                
                default:
                    label("Ainda não analisado.");
                    $numItensNAnalisados++;
                    break;

                ##############################################################################
                
            }	
            echo "</td>";
            echo "</tr>";

            $numItens++;
            }

        echo "</table>";

        # Exibe o número de itens importado
        echo $numItens." itens";br();
        echo $numItensDescartados." itens descartados";br();
        echo $numItensImportados." itens importados";br();
        echo $numItensTbTRE." itens importados para o TRE";br();
        echo $numItensTbAtestado." itens importados para tabela de atestado";br();        
        echo $numItensFerias." itens Férias";br();
        echo $numItensNAnalisados." itens Não analisados";br();
        echo $numIdInvalido." ids inválidos";br();
                    
        $painel->fecha();
        break;
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}