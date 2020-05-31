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
    $contador = 1;

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

            # Analisa
            $link2019 = new Link("Analisa", "?fase=inicia");
            $link2019->set_class('button');
            $link2019->set_title('Importar');

            # Cria um menu
            $menu = new MenuBar();
            $menu->add_link($linkVoltar, "left");
            $menu->add_link($link2019, "right");

            $menu->show();

            titulo('Importação de Progressão e Enquadramento de arquivo do Excell para o banco de dados');
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

            # Arquivo a ser importado
            $arquivo = "../importacao/fen005.csv";

            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar", '?');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1, "left");

            # Refazer
            $linkBotao2 = new Link("Refazer", '?fase=aguarda');
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('R');
            $menu->add_link($linkBotao2, "right");
            $menu->show();

            titulo("Importação da tabela FEN005");

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Abre o banco de dados
            $pessoal = new Pessoal();

            $problemaNome = 0;
            $problemaData = 0;
            $problemaPerfil = 0;
            $problemaAdmissao = 0;
            $problemaSalario = 0;

            # Verifica a existência do arquivo
            if (file_exists($arquivo)) {
                $lines = file($arquivo);

                # Inicia variáveis
                $contador = 0;
                $erro = 0;

                # Inicia a Tabela
                echo "<table border=1>";
                echo "<tr>";
                echo "<th>#</th>";
                echo "<th>idServidor</th>";
                echo "<th>idTpProgressao</th>";
                echo "<th>idClasse</th>";
                echo "<th>dtInicial</th>";
                echo "<th>Obs</th>";
                #echo "<th>OBS</th>";
                echo "</tr>";

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    $linha = htmlspecialchars($linha);

                    $parte = explode(",", $linha);

                    # Pega os dados
                    $MATR = $parte[0];
                    $DT = $parte[1];
                    $SAL = $parte[2];
                    $CLASS = $parte[3];
                    $CARGO = $parte[4];
                    $PERC = $parte[5];
                    #$OBS = $parte[6];
                    # Dados Tratados
                    $idServidor = $pessoal->get_idServidor($MATR);
                    $nome = $pessoal->get_nome($idServidor);
                    $perfil = $pessoal->get_idPerfil($idServidor);
                    $dtAdmissao = $pessoal->get_dtAdmissao($idServidor);
                    $idTpProgressao = 9;    // Tipo importado
                    $obs = NULL;

                    # Trata a Classe
                    $CL1 = substr($CLASS, -1);
                    $len = strlen($CLASS);
                    $CL2 = substr($CLASS, 0, $len - 1);
                    $CLASS2 = $CL2 . "-" . $CL1;

                    # Define a data limite da admissão do primeiro servidor concursado
                    $dtPrimeiro = date_to_bd("01/06/1998");
                    $dtComparacao = date_to_bd($DT);

                    # Inicia a linha
                    echo "<tr";

                    ############################################################################################
                    # Retira servidor com admissão  anterior a 01/06/1998
                    # Data do primeiro servidor concursado

                    if (!vazio($DT)) {
                        if ($dtComparacao < $dtPrimeiro) {
                            $tt = "ANTES de 01/06/1998";
                            $problemaData++;
                            continue;
                        } else {
                            $tt = NULL;
                        }
                    }

                    ############################################################################################
                    # Retira os servidores não estatutários, não celetistas e não cedidos

                    if (($perfil <> 1) AND ($perfil <> 4) AND ($perfil <> 2)) {
                        #echo " id='logExclusao'";
                        $problemaPerfil++;
                        continue;
                    }

                    ############################################################################################
                    # Verifica se o servidor existe no sistema novo
                    if (is_null($nome)) {
                        $problemaNome++;
                    }

                    ############################################################################################
                    # Servidor sem data da progressão será assumido a data da admissão
                    if (vazio($DT)) {
                        $obs .= "Sem data de início. Assumindo a data de admissão: " . $dtAdmissao . "<br/>";
                        $DT = $dtAdmissao;
                    }

                    ############################################################################################
                    # Verifica se a data da progressão é anterior a de admissão
                    if (date_to_bd($DT) < date_to_bd($dtAdmissao)) {
                        $problemaAdmissao++;
                        $obs .= "Servidor Admitido em $dtAdmissao.<br/>";
                    }

                    ############################################################################################
                    # Define o plano de Cargos ativo na data da progressão
                    $plano = new PlanoCargos();
                    $idPlano = $plano->get_planoVigente($DT, $idServidor);
                    $planoVigente = $plano->get_dadosPlano($idPlano);

                    ############################################################################################
                    # Informa o salário da classe e o plano indicados
                    $salario = $plano->get_salarioClasse($idPlano, $CLASS2);
                    $idClasse = $plano->get_idClasse($idPlano, $CLASS2);

                    # Conta quantos registros não encontrou salário compatível
                    if (vazio($salario)) {

                        # Verifica se matrícula é menor que 1000
                        if ($MATR < 1000) {

                            # Verifica se a data é entre agosto / 2001 e janeiro / 2002
                            $dtInicial = "01/08/2001";
                            $dtFinal = "01/01/2002";

                            if (entre($DT, $dtInicial, $dtFinal)) {
                                $CLASS2 = $CLASS . "-1";
                                $salario = $plano->get_salarioClasse($idPlano, $CLASS2);
                                $idClasse = $plano->get_idClasse($idPlano, $CLASS2);
                                $obs .= "Faixa $CLASS foi acrescentada -1 por causa do servidor ser matrícula menor que 1000 no período entre agosto / 2001 e janeiro / 2002.";
                            }
                        }
                    }

                    # Verfica de novo Pois a análise de cima pode ter resolvido
                    if (vazio($salario)) {
                        $problemaSalario++;
                        $obs .= "Valor $CLASS não encontrado no plano de cargos $planoVigente[0].";
                        $CLASS2 = "-";
                    }

                    echo ">";

                    $contador++;

                    echo "<td id='center'>$contador</td>";
                    echo "<td id='left'>$idServidor</td>";
                    echo "<td id='center'>" . $idTpProgressao . "</td>";
                    echo "<td id='center'>$idClasse</td>";
                    echo "<td id='center'>$DT</td>";
                    echo "<td id='left'>$obs</td>";
                    echo "</tr>";
                }

                echo "</table>";

                echo "Registros analisados: " . $contador;
                br();

                echo "$problemaNome Registros com nome não encontrado no sistema novo";
                br();
                echo "$problemaData Registros com data anterior a 01/06/1998";
                br();
                echo "$problemaPerfil Registros de não estatutérios e não celetistas";
                br();
                echo "$problemaAdmissao Registros com progressão/Enquadramento antes de ser admitido";
                br();
                echo "$problemaSalario Registros sem encontrar valor do salario";

                br(2);
                # Botão importar
                $linkBotao1 = new Link("Importar", '?fase=aguarda2');
                $linkBotao1->set_class('button');
                $linkBotao1->set_title('Volta para a página anterior');
                $linkBotao1->set_accessKey('I');
                $linkBotao1->show();
            } else {
                echo "Arquivo não encontrado";
            }

            $painel->fecha();
            break;

        #########################################################################

        case "aguarda2" :
            titulo('Importando ...');
            br(4);
            aguarde("Importando ...");

            loadPage('?fase=importa');
            break;

        #########################################################################

        case "importa" :

            # Arquivo a ser importado
            $arquivo = "../importacao/fen005.csv";

            titulo('Importação da tabela de Progressão & Enquadramento');

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Abre o banco de dados
            $pessoal = new Pessoal();

            $problemaNome = 0;
            $problemaData = 0;
            $problemaPerfil = 0;
            $problemaAdmissao = 0;
            $problemaSalario = 0;

            # Verifica a existência do arquivo
            if (file_exists($arquivo)) {
                $lines = file($arquivo);

                # Inicia variáveis
                $contador = 0;
                $erro = 0;

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {

                    $linha = htmlspecialchars($linha);

                    $parte = explode(",", $linha);

                    # Pega os dados
                    $MATR = $parte[0];
                    $DT = $parte[1];
                    $SAL = $parte[2];
                    $CLASS = $parte[3];
                    $CARGO = $parte[4];
                    $PERC = $parte[5];
                    #$OBS = $parte[6];
                    # Dados Tratados
                    $idServidor = $pessoal->get_idServidor($MATR);
                    $nome = $pessoal->get_nome($idServidor);
                    $perfil = $pessoal->get_idPerfil($idServidor);
                    $dtAdmissao = $pessoal->get_dtAdmissao($idServidor);
                    $idTpProgressao = 9;    // Tipo importado
                    $obs = NULL;

                    # Trata a Classe
                    $CL1 = substr($CLASS, -1);
                    $len = strlen($CLASS);
                    $CL2 = substr($CLASS, 0, $len - 1);
                    $CLASS2 = $CL2 . "-" . $CL1;

                    # Define a data limite da admissão do primeiro servidor concursado
                    $dtPrimeiro = date_to_bd("01/06/1998");
                    $dtComparacao = date_to_bd($DT);

                    ############################################################################################
                    # Retira servidor com admissão  anterior a 01/06/1998
                    # Data do primeiro servidor concursado

                    if (!vazio($DT)) {
                        if ($dtComparacao < $dtPrimeiro) {
                            $tt = "ANTES de 01/06/1998";
                            $problemaData++;
                            continue;
                        } else {
                            $tt = NULL;
                        }
                    }

                    ############################################################################################
                    # Retira os servidores não estatutários, não celetistas e não cedidos

                    if (($perfil <> 1) AND ($perfil <> 4) AND ($perfil <> 2)) {
                        #echo " id='logExclusao'";
                        $problemaPerfil++;
                        continue;
                    }

                    ############################################################################################
                    # Verifica se o servidor existe no sistema novo
                    if (is_null($nome)) {
                        $problemaNome++;
                    }

                    ############################################################################################
                    # Servidor sem data da progressão será assumido a data da admissão
                    if (vazio($DT)) {
                        $obs .= "Sem data de início. Assumindo a data de admissão: " . $dtAdmissao . "<br/>";
                        $DT = $dtAdmissao;
                    }

                    ############################################################################################
                    # Verifica se a data da progressão é anterior a de admissão
                    if (date_to_bd($DT) < date_to_bd($dtAdmissao)) {
                        $problemaAdmissao++;
                        $obs .= "Servidor Admitido em $dtAdmissao.<br/>";
                    }

                    ############################################################################################
                    # Define o plano de Cargos ativo na data da progressão
                    $plano = new PlanoCargos();
                    $idPlano = $plano->get_planoVigente($DT, $idServidor);
                    $planoVigente = $plano->get_dadosPlano($idPlano);

                    ############################################################################################
                    # Informa o salário da classe e o plano indicados
                    $salario = $plano->get_salarioClasse($idPlano, $CLASS2);
                    $idClasse = $plano->get_idClasse($idPlano, $CLASS2);

                    # Conta quantos registros não encontrou salário compatível
                    if (vazio($salario)) {

                        # Verifica se matrícula é menor que 1000
                        if ($MATR < 1000) {

                            # Verifica se a data é entre agosto / 2001 e janeiro / 2002
                            $dtInicial = "01/08/2001";
                            $dtFinal = "01/01/2002";

                            if (entre($DT, $dtInicial, $dtFinal)) {
                                $CLASS2 = $CLASS . "-1";
                                $salario = $plano->get_salarioClasse($idPlano, $CLASS2);
                                $idClasse = $plano->get_idClasse($idPlano, $CLASS2);
                                $obs .= "Faixa $CLASS foi acrescentada -1 por causa do servidor ser matrícula menor que 1000 no período entre agosto / 2001 e janeiro / 2002.";
                            }
                        }
                    }

                    # Verfica de novo Pois a análise de cima pode ter resolvido
                    if (vazio($salario)) {
                        $problemaSalario++;
                        $obs .= "Valor $CLASS não encontrado no plano de cargos $planoVigente[0].";
                        $CLASS2 = "-";
                    }

                    $contador++;

                    ############################################
                    # Grava na tabela
                    $campos = array("idServidor", "idTpProgressao", "idClasse", "dtInicial", "obs");
                    $valor = array($idServidor, $idTpProgressao, $idClasse, date_to_bd($DT), $obs);
                    $pessoal->gravar($campos, $valor, NULL, "tbprogressao", "idProgressao", FALSE);
                }############################################
                # Informa sobre a importação
                echo "Registros analisados: " . $contador;
                br();

                echo "$problemaNome Registros com nome não encontrado no sistema novo";
                br();
                echo "$problemaData Registros com data anterior a 01/06/1998";
                br();
                echo "$problemaPerfil Registros de não estatutérios e não celetistas";
                br();
                echo "$problemaAdmissao Registros com progressão/Enquadramento antes de ser admitido";
                br();
                echo "$problemaSalario Registros sem encontrar valor do salario";
            } else {
                echo "Arquivo não encontrado";
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
} else {
    loadPage("login.php");
}