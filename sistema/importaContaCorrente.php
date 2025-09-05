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
    $fase = get('fase');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Parâmetros da importação
    $contador = 0;
    $problemas = 0;    

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
            # Analisa
            $linkAnalisa = new Link("Analisa", "?fase=inicia");
            $linkAnalisa->set_class('button');
            $linkAnalisa->set_title('Importar');
            $menu->add_link($linkAnalisa, "right");

            $menu->show();

            titulo('Importação da Conta Corrente de arquivo do Excell para o banco de dados');
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
            $arquivo = "../../importacao/banco.csv";

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

            titulo("Importação do Arquivo de Contas Bancárias");

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Abre o banco de dados
            $pessoal = new Pessoal();

            $problemaNome = 0;

            # Verifica a existência do arquivo
            if (file_exists($arquivo)) {

                # Pega as linha do arquivo
                $lines = file($arquivo);

                # Inicia a Tabela
                echo "<table border=1>";

                echo "<col style = 'width:5%'>";
                echo "<col style = 'width:20%'>";
                echo "<col style = 'width:20%'>";
                echo "<col style = 'width:20%'>";
                echo "<col style = 'width:30%'>";

                echo "<tr>";
                echo "<th>#</th>";
                echo "<th>CPF</th>";
                echo "<th>Nome Planilha</th>";
                echo "<th>Nome Sistema</th>";
                echo "<th>Conta</th>";
                echo "</tr>";

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {

                    # retira os caracteres especiaus
                    $linha = htmlspecialchars($linha);

                    # Separa as colunas usando a virgula
                    $parte = explode(",", $linha);

                    # Pega os dados
                    $cpf = $parte[0];
                    $nome = $parte[1];
                    $conta = $parte[2];

                    # Separa a conta
                    $item = explode(" - ", $parte[2]);

                    # Retira os zeros à esquerda do número da conta
                    $item[2] = intval($item[2]);

                    # Formata o CPF
                    $cpfTratado = formatCnpjCpf($cpf);

                    # Valida o cpf
                    if (!validaCpf($cpfTratado)) {
                        $nomeSistema = "CPF INVÁLIDO !!";
                    } else {
                        # Pega o idPessoa e idServidor
                        $idPessoa = $pessoal->get_idPessoaCpf($cpfTratado);
                        $idServidor = $pessoal->get_idServidoridPessoa($idPessoa);

                        if (empty($idPessoa)) {
                            $nomeSistema = "<b>IdPessoa Vazio</b>";
                            $problemas++;
                        } else {
                            # Pega o nome
                            $nomeSistema = $pessoal->get_nomeidPessoa($idPessoa);

                            if (empty($nomeSistema)) {
                                echo "Nome nÃo Encontrado";
                            }
                        }
                    }

                    # Inicia a linha
                    echo "<tr>";

                    $contador++;

                    echo "<td id='center'>{$contador}</td>";
                    echo "<td id='center'>{$cpf}<br/>{$cpfTratado}</td>";
                    echo "<td id='left'>{$nome}<br/>IdServidor:{$idServidor}</td>";
                    echo "<td id='left'>{$nomeSistema}</td>";
                    echo "<td id='left'>{$conta}<br/>{$item[0]} / {$item[1]} / {$item[2]}</td>";
                    echo "</tr>";
                }

                echo "</table>";

                echo "Registros analisados: {$contador}";
                echo "<br/>Problemas com idPessoa: {$problemas}";

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
            $arquivo = "../../importacao/banco.csv";

            titulo('Importação das contas Correntes');

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Abre o banco de dados
            $pessoal = new Pessoal();

            # Verifica a existência do arquivo
            if (file_exists($arquivo)) {

                # Pega as linha do arquivo
                $lines = file($arquivo);

                # Inicia variáveis
                $contador = 0;

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {

                    # retira os caracteres especiaus
                    $linha = htmlspecialchars($linha);

                    # Separa as colunas usando a virgula
                    $parte = explode(",", $linha);

                    # Pega os dados
                    $cpf = $parte[0];
                    $nome = $parte[1];
                    $conta = $parte[2];

                    # Separa a conta
                    $item = explode(" - ", $parte[2]);

                    # Retira os zeros à esquerda do número da conta
                    $item[2] = intval($item[2]);

                    # Formata o CPF
                    $cpfTratado = formatCnpjCpf($cpf);

                    # Pega o idPessoa e idServidor
                    $idPessoa = $pessoal->get_idPessoaCpf($cpfTratado);
                    $idServidor = $pessoal->get_idServidoridPessoa($idPessoa);

                    $contador++;

                    /*
                     *  Grava na tabela
                     */

                    if (!empty($idServidor)) {
                        $campos = array("idServidor", "idBanco", "agencia", "conta", "padrao", "obs");
                        $valor = array($idServidor, 4, $item[1], $item[2], "s", "Importado em " . date('d/m/Y'));
                        $pessoal->gravar($campos, $valor, null, "tbhistbanco", "idHistBanco", false);
                    }else{
                        $problemas++;
                    }
                }
            } else {
                echo "Arquivo não encontrado";
            }
            
            echo "Registros Importados: {$contador}";
            echo "<br/>Problemas encontrados: {$problemas}";

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