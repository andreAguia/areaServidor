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
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Verifica a fase do programa
    $fase = get('fase');
    $ano = get('ano');

    # Parâmetros da importação
    $tt = 0;                                    // contador de registros
    $problemas = 0;                             // contador de problemas
    $problemaLinha = array();                   // guarda os problemas
    $contador = 1;                              // contador de linhas
    $anoImportacao = 2020;

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    br();

    #########################################################################

    switch ($fase) {
        case "" :
            # Botão voltar
            $linkVoltar = new Link("Voltar", 'administracao.php');
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Volta para a página anterior');
            $linkVoltar->set_accessKey('V');

            # 2019
            $link2019 = new Link($anoImportacao, "?fase=inicia");
            $link2019->set_class('button');
            $link2019->set_title('Importar');

            # Cria um menu
            $menu = new MenuBar();
            $menu->add_link($linkVoltar, "left");
            $menu->add_link($link2019, "right");

            $menu->show();

            titulo('Importação de Férias de arquivo do Excell para o banco de dados');
            break;

        #########################################################################

        case "inicia" :
            titulo('Analisando ...');
            br(4);
            aguarde("Analisando o arquivo.");

            loadPage('?fase=analisa');
            break;

        #########################################################################

        case "analisa" :

            # Define o arquivo a ser importado
            $arquivo = "../importacao/$anoImportacao.csv";

            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar", 'importacaoFerias.php');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1, "left");

            # Refazer
            $linkBotao2 = new Link("Refazer", '?fase=inicia');
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('R');
            $menu->add_link($linkBotao2, "right");
            $menu->show();

            titulo("Importação da tabela de Férias $anoImportacao");

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Abre o banco de dados
            $pessoal = new Pessoal();

            # Verifica a existência do arquivo
            if (file_exists($arquivo)) {
                $lines = file($arquivo);

                # Inicia variáveis
                $contador = 0;
                $outroAno = 0;

                # Inicia a Tabela
                echo "<table border=1>";
                echo "<tr>";
                echo "<th>#</th>";
                echo "<th>IdFuncional</th>";
                echo "<th>Nome</th>";
                echo "<th>Ano Exercicio</th>";
                echo "<th>Data Inicial</th>";
                echo "<th>Data Final</th>";
                echo "<th>Dias</th>";
                echo "<th>Obs</th>";
                echo "</tr>";

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    $linha = htmlspecialchars($linha);

                    $parte = explode(",", $linha);

                    # Pega os dados
                    $idFuncional = $parte[0];                       // IdFuncional
                    $nomeImportado = $parte[1];                     // Nome
                    $dtInicial = substr($parte[2], 0, 10);            // Data Inicial
                    $dtFinal = substr($parte[3], 0, 10);              // Data Final
                    $dtInicialAquisitivo = substr($parte[4], 0, 10);  // Data Inicial Aquisitivo
                    $dtFinalAquisitivo = substr($parte[5], 0, 10);    // Data Final Aquisitivo
                    # Dados Tratados
                    $idServidor = $pessoal->get_idServidoridFuncional($idFuncional);        // IdServidor
                    $nome = $pessoal->get_nome($idServidor);                                // Nome
                    $numDias = dataDif($dtInicial, $dtFinal) + 1;
                    $anoExercicio = year($dtInicialAquisitivo);
                    $obs = "";

                    # Ano Exercício
                    if ($anoExercicio <> $anoImportacao) {
                        $obs .= "Outro Ano. ($anoExercicio)";
                        $outroAno++;
                    }

                    # Nome
                    if (plm($nomeImportado) <> retiraAcento(plm($nome))) {
                        $obs .= "Nome diferente !!!";
                    }

                    $contador++;
                    echo "<tr>";
                    echo "<td>$contador</td>";
                    echo "<td>$idServidor</td>";
                    echo "<td>$nome<br/>$nomeImportado</td>";
                    echo "<td>$anoExercicio</td>";
                    echo "<td>$dtInicial</td>";
                    echo "<td>$dtFinal</td>";
                    echo "<td>$numDias</td>";
                    echo "<td>$obs</td>";
                    echo "</tr>";
                }

                echo "</table>";

                echo "Registros analisados: " . $contador;
                br();
                echo "$outroAno Férias de outro ano encontrados";
                br();
                echo $contador - $outroAno . " Férias do ano correto";

                br(2);
                # Botão importar
                $linkBotao1 = new Link("Importar", '?fase=aguarda');
                $linkBotao1->set_class('button');
                $linkBotao1->set_title('Volta para a página anterior');
                $linkBotao1->set_accessKey('I');
                $linkBotao1->show();
            } else {
                echo "Arquivo de Férias não encontrado";
            }

            $painel->fecha();
            break;

        #########################################################################

        case "aguarda" :
            titulo('Importando ...');
            br(4);
            aguarde("Importando férias $anoImportacao");

            loadPage('?fase=importa');
            break;

        #########################################################################

        case "importa" :

            # Define o arquivo a ser importado
            $arquivo = "../importacao/$anoImportacao.csv";

            titulo('Importação da tabela de Férias $anoImportacao');

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Abre o banco de dados
            $pessoal = new Pessoal();

            # Verifica a existência do arquivo
            if (file_exists($arquivo)) {
                $lines = file($arquivo);

                # Inicia Variáveis
                $contador = 0;
                $ignorados = 0;
                $anoCorreto = 0;
                $outroAno = 0;

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {

                    $linha = htmlspecialchars($linha);

                    $parte = explode(",", $linha);

                    # Pega os dados
                    $idFuncional = $parte[0];                       // IdFuncional
                    $nomeImportado = $parte[1];                     // Nome
                    $dtInicial = substr($parte[2], 0, 10);            // Data Inicial
                    $dtFinal = substr($parte[3], 0, 10);              // Data Final
                    $dtInicialAquisitivo = substr($parte[4], 0, 10);  // Data Inicial Aquisitivo
                    $dtFinalAquisitivo = substr($parte[5], 0, 10);    // Data Final Aquisitivo
                    # IdServidor ## Problema aqui !!! Pega o primeiro que acha e não o idServidor atual ativo
                    $idServidor = $pessoal->get_idServidoridFuncional($idFuncional);

                    # Dados Tratados
                    $nome = $pessoal->get_nome($idServidor);                                // Nome
                    $numDias = dataDif($dtInicial, $dtFinal) + 1;
                    $anoExercicio = year($dtInicialAquisitivo);

                    # Grava na tabela
                    $campos = array("idServidor", "dtInicial", "anoExercicio", "numDias", "status");
                    $valor = array($idServidor, date_to_bd($dtInicial), $anoExercicio, $numDias, "fruída");
                    $pessoal->gravar($campos, $valor, NULL, "tbferias", "idFerias", FALSE);
                    $contador++;

                    if ($anoExercicio == $anoImportacao) {
                        $anoCorreto++;
                    } else {
                        $outroAno++;
                    }
                }

                # Rotina que altera fruída para Solicitada e vice versa
                $pessoal->mudaStatusFeriasSolicitadaFruida();

                # Informa sobre a importação
                echo "Registros importados: " . $contador;
                br();
                echo $outroAno . " registros de outro Ano.";
                br();
                echo $anoCorreto . " registros de Ano Correto.";
            } else {
                echo "Arquivo de Férias não encontrado";
            }
            br(2);
            # Botão voltar
            $linkBotao1 = new Link("Voltar", '?');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta');
            $linkBotao1->show();

            $painel->fecha();
            break;

        #########################################################################    
    }
    $grid->fechaColuna();
    $grid->fechaGrid();
    $page->terminaPagina();
}