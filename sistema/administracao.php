<?php

/**
 * Administração
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

    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase', 'menu'); # Qual a fase
    $metodo = get('sistema'); # Qual o sistema. Usado na rotina de Documentação

    $parametroAno = post('parametroAno', get_session('parametroAno', date("Y")));
    $parametroMes = post('parametroMes', date("m"));
    set_session('parametroAno', $parametroAno);
    set_session('parametroMes', $parametroMes);

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho
    if ($fase <> "servidorPhp") {
        AreaServidor::cabecalho();
    }

    # Zera sessions
    set_session('categoria');

    # Limita o tamanho da tela
    $grid1 = new Grid();
    $grid1->abreColuna(12);

    switch ($fase) {

########################################################################################

        case "menu" :
            # Apaga as session do sistema de projetos e notas
            set_session('idNota');
            set_session('idCaderno');

            # Cria um menu
            $menu = new MenuBar();

            # Voltar
            $linkVoltar = new Link("Voltar", "areaServidor.php");
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Voltar');
            $menu->add_link($linkVoltar, "left");

            # Área do Servidor
            $linkArea = new Link("Área do Servidor", "areaServidor.php");
            $linkArea->set_class('button');
            $linkArea->set_title('Área do Servidor');
            #$menu->add_link($linkArea, "right");

            $menu->show();

            titulo('Administração');
            br();

            #########################################################
            # Exibe o Menu
            AreaServidor::menuAdministracao($idUsuario);
            br();

            #########################################################
            # Exibe o rodapé da página
            AreaServidor::rodape($idUsuario);
            break;

########################################################################################

        case "servidorWeb" :
            botaoVoltar("?");
            AreaServidor::moduloServidorWeb();
            break;

########################################################################################

        case "servidorPhp" :
            botaoVoltar("?");
            phpinfo();
            break;

########################################################################################

        case "importacao" :
            botaoVoltar("administracao.php");
            titulo('Importação do banco de dados');

            # Define o tamanho do ícone
            $tamanhoImage = 60;

//            $menu = new MenuGrafico(5);
//            br();
//            
//            # Atualização do plano
//            $botao = new BotaoGrafico();
//            $botao->set_label('Atualização do plano');
//            $botao->set_url('atualizacaoPlano.php');
//            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
//            $botao->set_title('Atualiza a tabela de acordo com o Decreto 47933 / 2022');
//            #$menu->add_item($botao);
//            
//            # Transformação de Regime
//            $botao = new BotaoGrafico();
//            $botao->set_label('Regime');
//            $botao->set_url('importacaoRegime.php');
//            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
//            $botao->set_title('Preenche com a data da transformação do regime');
//            #$menu->add_item($botao);
//
//            # Dependentes
//            $botao = new BotaoGrafico();
//            $botao->set_label('Dependentes');
//            $botao->set_url('importacaoDependentes.php');
//            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
//            $botao->set_title('Importação da Tabela de Dependentes do SigRH');
//            #$menu->add_item($botao);
//            
//            # Férias
//            $botao = new BotaoGrafico();
//            $botao->set_label('Férias');
//            $botao->set_url('importacaoFerias.php');
//            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
//            $botao->set_title('Importação da Tabela de Férias do SigRH');
//            #$menu->add_item($botao);
//
//            # Progressão
//            $botao = new BotaoGrafico();
//            $botao->set_label('Progressão');
//            $botao->set_url('importaProgressao.php');
//            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
//            $botao->set_title('Importação da Tabela de Progressão');
//            #$menu->add_item($botao);
//            # Faltas
//            $botao = new BotaoGrafico();
//            $botao->set_label('Faltas');
//            $botao->set_url('importacaoFaltas.php');
//            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
//            $botao->set_title('Importação da Tabela de Faltas do SigRH');
//            #$menu->add_item($botao);
//            # Contatos
//            $botao = new BotaoGrafico();
//            $botao->set_label('Contatos');
//            $botao->set_url('?fase=contatos');
//            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
//            $botao->set_title('Importação da antiga tabela de contatos');
//            #$menu->add_item($botao);
//            # sispatri
//            $botao = new BotaoGrafico();
//            $botao->set_label('Sispatri');
//            $botao->set_url('?fase=sispatri');
//            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
//            $botao->set_title('Insere o idServidor na tabela do sispatri importada por Gustavo');
//            #$menu->add_item($botao);
//            $menu->show();
            break;

########################################################################################

        case "backup" :
            $processo = new Processo();
            $processo->run("php backup.php 2 $idUsuario");

            loadPage('?fase=pastaBackup');
            break;

########################################################################################

        case "pastaBackup" :
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);

            ##############################
            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar", '?');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1, "left");

            # Backup Manual
            $linkBotao2 = new Link("Backup Manual", '?fase=backup');
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Backup manual do banco de dados');
            $menu->add_link($linkBotao2, "right");

            $menu->show();

            # Formulário de Pesquisa
            $form = new Form('?fase=pastaBackup');

            # Cria um array com os anos possíveis
            $anoAtual = date('Y');
            $anoInicial = $anoAtual - 1;
            $anoExercicio = array($anoInicial, $anoAtual);

            $controle = new Input('parametroAno', 'combo', 'Ano:', 1);
            $controle->set_size(50);
            $controle->set_title('Filtra por Ano exercício');
            $controle->set_array($anoExercicio);
            $controle->set_valor($parametroAno);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_col(2);
            $controle->set_linha(1);
            $form->add_item($controle);

            $controle = new Input('parametroMes', 'combo', "Mês", 1);
            $controle->set_size(30);
            $controle->set_title('O mês do backup');
            $controle->set_array($mes);
            $controle->set_valor($parametroMes);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_col(3);
            $controle->set_linha(1);
            $form->add_item($controle);

            $form->show();

            ##############################
            # Título
            tituloTable('Pasta de Backup');
            br();

            # Executa o backup no Linux
            #$output = shell_exec('ls ../../_backup');
            #echo "<pre>$output</pre>";
            # Define a pasta
            $pasta = '/var/www/html/_backup/';

            if (is_dir($pasta)) {
                $diretorio = dir($pasta);

                # Percorre os arquivos
                while ($arquivo = $diretorio->read()) {

                    # Retira os diretórios
                    if ($arquivo <> '..' AND $arquivo <> '.') {

                        # Cria um Array com todos os Arquivos encontrados
                        $arrayArquivos[] = $arquivo;
                    }
                }
                $diretorio->close();
            }

            # Cria títulos de ordenação
            $mes = null;
            $diaselec = 0;

            if (isset($arrayArquivos)) {
                # Limita ainda mais
                $grid = new Grid();
                $grid->abreColuna(12);

                # Classificar os arquivos para a Ordem Crescente
                sort($arrayArquivos, SORT_STRING);

                # Abre a grid
                $grid = new Grid("center");

                # Mostra a listagem dos Arquivos
                foreach ($arrayArquivos as $valorArquivos) {

                    # Pega o ano do arquivo
                    $ano = substr($valorArquivos, 0, 4);
                    $mes = substr($valorArquivos, 5, 2);
                    $dia = substr($valorArquivos, 8, 2);
                    $hora = substr($valorArquivos, 11, 2);
                    $minuto = substr($valorArquivos, 14, 2);
                    $segundo = substr($valorArquivos, 17, 2);

                    # Compara se é o ano desejado
                    if ($ano == $parametroAno) {

                        # Compara se já teve título do mês
                        if ($mes == $parametroMes) {

                            if ($dia <> $diaselec) {
                                # Verifica se o dia é zero e não fechao fieldset
                                if ($diaselec <> 0) {
                                    $field->fecha();
                                    $grid->fechaColuna();
                                }

                                # muda o dia selecionado
                                $diaselec = $dia;
                                $grid->abreColuna(3);

                                $field = new Fieldset($dia);
                                $field->set_class('fieldset');
                                $field->abre();
                            }
                            # Exibe o arquivo
                            echo "<a href=/_backup/$valorArquivos>Dia $dia - $hora:$minuto:$segundo</a><br />";
                        }
                    }
                }

                $grid->fechaColuna();
                $grid->fechaGrid();
            } else {
                br(3);
                p("Não existe nenhum arquivo de backup!", "center");
            }

            $grid->fechaColuna();
            $grid->fechaGrid();

            break;

########################################################################################

        case "contatos" :
            titulo('Importação dos contatos');

            br(4);
            aguarde('Importando ...');

            $select = 'SELECT idPessoa
                         FROM tbpessoa
                     ORDER BY 1 desc';

            $row = $servidor->select($select);

            foreach ($row as $tt) {
                # Pega os contatos antigos
                $contatos = importaContatos($tt[0]);

                echo "idPessoa: " . $tt[0];

                # Grava na tabela tbpessoa
                $campos = array("telResidencial", "telCelular", "telRecados", "emailUenf", "emailPessoal");
                $valor = array($contatos[0], $contatos[1], $contatos[2], $contatos[3], $contatos[4]);
                $servidor->gravar($campos, $valor, $tt[0], "tbpessoa", "idPessoa");
            }
            loadPage("?");
            break;

########################################################################################

        case "sispatri" :
            botaoVoltar("?fase=importacao");
            titulo('Insere o idServidor na Tabela do Sispatri');

            br();
            $select = 'SELECT idSispatri,
                              nome,
                              cpf
                         FROM tbsispatri
                     ORDER BY nome';

            $row = $servidor->select($select);

            echo "<table>";

            echo "<tr>";
            echo "<th>idSispatri</th>";
            echo "<th>Nome</th>";
            echo "<th>CPF</th>";
            echo "<th>CPF Tratado</th>";
            echo "<th>idServidor</th>";

            echo "</tr>";

            $contador = 0;

            foreach ($row as $tt) {

                echo "<tr>";
                echo "<td>$tt[0]</td>";
                echo "<td>$tt[1]</td>";
                echo "<td>$tt[2]</td>";

                $novoCpf = $tt[2];
                $len = strlen($novoCpf);

                $novoCpf = str_pad($novoCpf, 11, "0", STR_PAD_LEFT);

                # CPF XXX.XXX.XXX-XX

                $parte1 = substr($novoCpf, 0, 3);
                $parte2 = substr($novoCpf, 3, 3);
                $parte3 = substr($novoCpf, 6, 3);
                $parte4 = substr($novoCpf, -2);

                $cpfFinalizado = "$parte1.$parte2.$parte3-$parte4";

                $select2 = "SELECT idPessoa
                              FROM tbdocumentacao
                             WHERE CPF = '$cpfFinalizado'";

                $row2 = $servidor->select($select2, false);

                if (is_null($row2[0])) {
                    echo "<td></td>";
                    echo "<td></td>";
                } else {
                    echo "<td>$cpfFinalizado</td>";
                    $idServidorPesquisado = $servidor->get_idServidoridPessoa($row2[0]);
                    echo "<td>" . $idServidorPesquisado . "</td>";

                    # Grava na tabela tbsispatri
                    $campos = array("idServidor");
                    $valor = array($idServidorPesquisado);
                    $servidor->gravar($campos, $valor, $tt[0], "tbsispatri", "idSispatri");
                }

                echo "</tr>";
            }

            echo "</table>";
            #loadPage("?");
            break;
    }
    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}