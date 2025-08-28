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

    # Nome da rotina
    $titulo = "Dados Bancários - Importa da Tabela Antiga para a Tabela Nova";

    #########################################################################

    switch ($fase) {

        case "" :

            # Cria um menu
            $menu = new MenuBar();

            # Analisa
            $linkAnalisa = new Link("Analisa", "?fase=inicia");
            $linkAnalisa->set_class('button');
            $linkAnalisa->set_title('Importar');
            $menu->add_link($linkAnalisa, "right");

            $menu->show();

            titulo($titulo);
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

            titulo($titulo);

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Abre o banco de dados
            $pessoal = new Pessoal();
            $select = "SELECT idServidor, idServidor, idServidor, idServidor FROM tbservidor JOIN tbpessoa USING (idPessoa) ORDER BY tbpessoa.nome";
            $row = $pessoal->select($select);

            # Monta a tabela
            $tabela = new Tabela();
            $tabela->set_titulo("Servidores");
            $tabela->set_conteudo($row);
            $tabela->set_label(["Servidor", "Tabela Antiga", "Tabela Atual", "Analisa"]);
            $tabela->set_align(["left"]);
            #$tabela->set_width([10, 10, 10, 25, 15, 25]);
            $tabela->set_classe(["Pessoal", "Pessoal", "Pessoal", "Pessoal"]);
            $tabela->set_metodo(["get_nomeECargo", "exibe_contaBancariaAntiga", "exibe_contaBancaria", "analisaImportacaoContaBancaria"]);
            $tabela->show();

            # Botão importar
            $linkBotao1 = new Link("Importar", '?fase=aguarda2');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('I');
            $linkBotao1->show();

            $painel->fecha();
            break;

        #########################################################################

        case "aguarda2" :
            titulo('Importando ...');
            br(4);
            aguarde("Importando ...");

            #loadPage('?fase=importa');
            break;

        #########################################################################

        case "importa" :

            # Arquivo a ser importado
            $arquivo = "../../importacao/banco.csv";

            titulo($titulo);

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
                    } else {
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

        case "importaBaseAntiga" :
            # Pega o Servidor
            $idServidor = get("idServidor");
            $padrao = get("padrao","s");

            # Pega os dados da tabela antiga
            $baseAntiga = $servidor->get_contaBancariaAntiga($idServidor);

            # Salva na nova
            $campos = array("idServidor", "idBanco", "agencia", "conta", "padrao", "obs");
            $valor = array($idServidor, $baseAntiga[0], $baseAntiga[1], $baseAntiga[2], $padrao, "Importado em " . date('d/m/Y'));
            $servidor->gravar($campos, $valor, null, "tbhistbanco", "idHistBanco", false);
            
            loadPage("?fase=inicia");
            break;

        #########################################################################    
    }
    $grid->fechaColuna();
    $grid->fechaGrid();
    $page->terminaPagina();
} else {
    loadPage("login.php");
}