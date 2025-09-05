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
    $fase = get('fase', 'importacao');
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho
    AreaServidor::cabecalho();


    # Limita o tamanho da tela
    $grid1 = new Grid();
    $grid1->abreColuna(12);

    switch ($fase) {

########################################################################################
        case "importacao" :
            
            # Botão Voltar
            botaoVoltar('areaServidor.php?fase=menuAdmin');
            
            # Título
            titulo('Importação do banco de dados');

            # Define o tamanho do ícone
            $tamanhoImage = 60;

            $menu = new MenuGrafico(5);
            br();
            
            # Atualização do plano
            $botao = new BotaoGrafico();
            $botao->set_label('Atualização do plano');
            $botao->set_url('atualizacaoPlano.php');
            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Atualiza a tabela de acordo com o Decreto 47933 / 2022');
            #$menu->add_item($botao);
            
            # Transformação de Regime
            $botao = new BotaoGrafico();
            $botao->set_label('Regime');
            $botao->set_url('importacaoRegime.php');
            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Preenche com a data da transformação do regime');
            #$menu->add_item($botao);

            # Dependentes
            $botao = new BotaoGrafico();
            $botao->set_label('Dependentes');
            $botao->set_url('importacaoDependentes.php');
            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Importação da Tabela de Dependentes do SigRH');
            #$menu->add_item($botao);
            
            # Férias
            $botao = new BotaoGrafico();
            $botao->set_label('Férias');
            $botao->set_url('importacaoFerias.php');
            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Importação da Tabela de Férias do SigRH');
            #$menu->add_item($botao);

            # Progressão
            $botao = new BotaoGrafico();
            $botao->set_label('Progressão');
            $botao->set_url('importaProgressao.php');
            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Importação da Tabela de Progressão');
            #$menu->add_item($botao);
            
            # Faltas
            $botao = new BotaoGrafico();
            $botao->set_label('Faltas');
            $botao->set_url('importacaoFaltas.php');
            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Importação da Tabela de Faltas do SigRH');
            #$menu->add_item($botao);
            
            # Contatos
            $botao = new BotaoGrafico();
            $botao->set_label('Contatos');
            $botao->set_url('?fase=contatos');
            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Importação da antiga tabela de contatos');
            #$menu->add_item($botao);
            
            # sispatri
            $botao = new BotaoGrafico();
            $botao->set_label('Sispatri');
            $botao->set_url('?fase=sispatri');
            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Insere o idServidor na tabela do sispatri importada por Gustavo');
            #$menu->add_item($botao);
            
            # AuxEducacao
            $botao = new BotaoGrafico();
            $botao->set_label('Auxílio Educação');
            $botao->set_url('?fase=auxEducacao');
            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Prepara o banco de dados para o cadastro do Aux Educação');
            #$menu->add_item($botao);
            
            # Contas bancárias
            $botao = new BotaoGrafico();
            $botao->set_label('Contas Bancárias');
            $botao->set_url('?fase=contasBancarias');
            $botao->set_imagem(PASTA_FIGURAS . 'codigo.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Prepara o banco de dados para a importação de contas bancárias');
            $menu->add_item($botao);

            $menu->show();
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

        ########################################################################################

        case "auxEducacao" :

            # Inicia a classe
            $auxEducacao = new AuxilioEducacao();

            # Acessa o banc de dados
            $select = 'SELECT * FROM tbdependente ORDER BY nome';
            $row = $servidor->select($select);

            foreach ($row as $dados) {

                # Pega os parentescos com direito au auxEducação
                $tipos = $auxEducacao->get_arrayTipoParentescoAuxEduca();

                # Verifica se tem direito
                if (in_array($dados["idParentesco"], $tipos)) {

                    # Pega as datas limites
                    $anos21 = get_dataIdade(date_to_php($dados["dtNasc"]), 21);
                    $anos24 = get_dataIdade(date_to_php($dados["dtNasc"]), 24);

                    # Data Histórica Inicial
                    $intra = new Intra();
                    $dataHistoricaInicial = $intra->get_variavel('dataHistoricaInicialAuxEducacao');

                    # Verifica se perdeu o direito antes da data histórica
                    if (dataMenor($dataHistoricaInicial, $anos24) == $anos24) {
                        # Grava na tabela
                        $campos = array("auxEducacao");
                        $valor = array("Não");
                        $servidor->gravar($campos, $valor, $dados["idDependente"], "tbdependente", "idDependente");
                    } else {
                        # Grava na tabela
                        $campos = array("auxEducacao");
                        $valor = array("Sim");
                        $servidor->gravar($campos, $valor, $dados["idDependente"], "tbdependente", "idDependente");
                    }
                }
            }
            loadPage("?");
            break;

        ########################################################################################

        case "contasBancarias" :
            
            # Botão voltar
            botaoVoltar("?");
            titulo('Importação de Dados Bancários');
            
            br(6);
            construcao("Rotina em desenvolvimento.");
            break;

########################################################################################
    }
    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}