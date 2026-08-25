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

# aumenta o tempo de execução 
ini_set('max_execution_time', '600'); // 10 minutos

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
            $arquivo = "../../importacao/links.csv";

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

            # Título da rotina
            titulo("Importação da tabela de Links");

            # Inicia os arrays de exibição
            $arrayProblemas = [];
            $arrayCertos = [];

            # Contadores
            $contador = 0;
            $naoEncontrado = 0;

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Abre o banco de dados
            $pessoal = new Pessoal();

            # Verifica a existência do arquivo
            if (file_exists($arquivo)) {
                $lines = file($arquivo);

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

                    # Verifica se é o cabeçalho
                    if ($nome == "nome") {
                        continue;
                    }

                    $contador++;

                    # Varifica se foi encontrado o idPessoa dessa pasta
                    if (!empty($idPessoa)) {
                        $idServidor = $pessoal->get_idServidoridPessoa($idPessoa);
                        $nomeSistema = $pessoal->get_nomeidPessoa($idPessoa);
                        $arrayCertos[] = [$nome, $link, $id, $idPessoa, $nomeSistema];
                    } else {
                        $nomeSistema = "<span label class='label warning'>Não Encontrado</span>";
                        $naoEncontrado++;
                        $arrayErrado[] = [$nome, $link, $id, $idPessoa, $nomeSistema];
                    }
                }

                # Tabela do problemas
                $tabela = new Tabela();
                $tabela->set_titulo("Problemas Encontrados");
                $tabela->set_conteudo($arrayErrado);
                $tabela->set_label(["Nome", "Link", "Id", "idPessoa", "Servidor"]);
                #$tabela->set_width(array(80, 10, 10));
                $tabela->set_align(["left", "left", "left", "center", "left"]);
                $tabela->show();

                # Tabela do certo
                $tabela = new Tabela();
                $tabela->set_titulo("Problemas Encontrados");
                $tabela->set_conteudo($arrayCertos);
                $tabela->set_label(["Nome", "Link", "Id", "idPessoa", "Servidor"]);
                #$tabela->set_width(array(80, 10, 10));
                $tabela->set_align(["left", "left", "left", "center", "left"]);
                $tabela->show();

                echo "Registros analisados: {$contador}<br/>";
                echo "Registros Não encontrador: {$naoEncontrado}<br/>";
                echo "Registros Encontradoe: " . $contador - $naoEncontrado;

                br(2);
                # Botão importar
                $linkBotao1 = new Link("Importar", '?fase=aguarda');
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

        case "aguarda" :
            titulo('Importando ...');
            br(4);
            aguarde("Importando Links do Google Drive");

            loadPage('?fase=importa');
            break;

        #########################################################################

        case "importa" :

            # Define o arquivo a ser importado
            $arquivo = "../../importacao/links.csv";

            titulo('Importação a tabela de Links do google Drive para o Sistema');

            # Inicia os arrays de exibição
            $arrayProblemas = [];
            $arrayCertos = [];

            # Contadores
            $contador = 0;
            $naoEncontrado = 0;

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Abre o banco de dados
            $pessoal = new Pessoal();

            # Verifica a existência do arquivo
            if (file_exists($arquivo)) {
                $lines = file($arquivo);

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

                    # Verifica se é o cabeçalho
                    if ($nome == "nome") {
                        continue;
                    }

                    $contador++;

                    # Varifica se foi encontrado o idPessoa dessa pasta
                    if (!empty($idPessoa)) {
                        $idServidor = $pessoal->get_idServidoridPessoa($idPessoa);

                        # Grava na tabela
                        $campos = array("pastaFuncional");
                        $valor = array($link);
                        $pessoal->gravar($campos, $valor, $idServidor, "tbservidor", "idServidor", false);
                    } else {
                        $naoEncontrado++;
                    }
                }

                echo "Registros analisados: {$contador}<br/>";
                echo "Registros Ignorados: {$naoEncontrado}<br/>";
                echo "Registros Importados: " . $contador - $naoEncontrado;

                br(2);
                # Botão importar
                $linkBotao1 = new Link("Importar", '?fase=aguarda');
                $linkBotao1->set_class('button');
                $linkBotao1->set_title('Volta para a página anterior');
                $linkBotao1->set_accessKey('I');
                $linkBotao1->show();
            } else {
                echo "Arquivo não encontrado";
            }

            $painel->fecha();

   
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