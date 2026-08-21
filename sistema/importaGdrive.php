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
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Verifica a fase do programa
    $fase = get('fase');

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
            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkVoltar = new Link("Voltar", 'administracao.php');
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Volta para a página anterior');
            $linkVoltar->set_accessKey('V');
            #$menu->add_link($linkVoltar, "left");
            # Começar
            $link2019 = new Link("Começar", "?fase=inicia");
            $link2019->set_class('button');
            $link2019->set_title('Importar');
            $menu->add_link($link2019, "right");

            $menu->show();

            titulo('Importação do Link das Pastas Funcionais de arquivo do Excell para o banco de dados');
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
            $arquivo = "../importacao/links.csv";

            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar", '?');
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

            titulo("Importação da tabela de Links");

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
                $naoEncontrado = 0;

                # Inicia a Tabela
                echo "<table border=1>";
                echo "<tr>";
                echo "<th>#</th>";
                echo "<th>Nome</th>";
                echo "<th>Link</th>";
                echo "<th>Id</th>";
                echo "<th>IdPessoa</th>";
                echo "<th>Análise</th>";
                echo "</tr>";

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {

                    # Pega os dados
                    $linha = htmlspecialchars($linha);
                    $parte = explode(",", $linha);

                    # Inicia as variáveis de retorno
                    $idServidor = null;
                    $idPessoa = null;
                    $nomeSistema = null;

                    # Pega os dados
                    $nome = $parte[0];  // Nome
                    $link = $parte[1];  // Link
                    $id = $parte[2];    // Id
                    $idPessoa = $pessoal->get_idPessoaNome($nome);

                    if (!empty($idPessoa)) {
                        $idServidor = $pessoal->get_idServidoridPessoa($idPessoa);
                        $nomeSistema = $pessoal->get_nomeidPessoa($idPessoa);
                    } else {
                        $nomeSistema = "<span label class='label warning'>Não Encontrado</span>";                        
                        $naoEncontrado++;
                    }

                    # Verifica se é o cabeçalho
                    if ($nome == "nome") {
                        continue;
                    }

                    $contador++;
                    echo "<tr>";
                    echo "<td style='text-align: center;'>$contador</td>";
                    echo "<td>$nome</td>";
                    echo "<td>$link</td>";
                    echo "<td>$id</td>";
                    echo "<td style='text-align: center;'>$idPessoa</td>";
                    echo "<td>$nomeSistema</td>";

                    echo "</tr>";
                }

                echo "</table>";
                br(2);

                echo "Registros analisados: {$contador}<br/>";
                echo "Registros Não encontrador: {$naoEncontrado}<br/>";
                echo "Registros Encontradoe: " . $contador - $naoEncontrado;

                br(2);
                # Botão importar
                $linkBotao1 = new Link("Importar", '?fase=aguarda');
                $linkBotao1->set_class('button');
                $linkBotao1->set_title('Volta para a página anterior');
                $linkBotao1->set_accessKey('I');
                #$linkBotao1->show();
            } else {
                echo "Arquivo não encontrado";
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
                    $pessoal->gravar($campos, $valor, null, "tbferias", "idFerias", false);
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