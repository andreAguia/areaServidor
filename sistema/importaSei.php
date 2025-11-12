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

            titulo("Importação do Arquivo do SEI");
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
            $arquivo = "../../importacao/sei.csv";

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

            titulo("Importação do Arquivo do SEI");

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
                #echo "<col style = 'width:10%'>";
                echo "<col style = 'width:10%'>";
                echo "<col style = 'width:40%'>";
                echo "<col style = 'width:10%'>";
                echo "<col style = 'width:10%'>";

                echo "<tr>";
                echo "<th>#</th>";
                #echo "<th>Órgão</th>";
                echo "<th>Login</th>";
                echo "<th>Nome</th>";
                echo "<th>CPF</th>";
                echo "<th>Ativo</th>";
                echo "</tr>";

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {

                    # retira os caracteres especiaus
                    $linha = htmlspecialchars($linha);

                    # Separa as colunas usando a virgula
                    $parte = explode(",", $linha);
                    
                    $orgao = $parte[0];
                    $login = $parte[1];
                    $nome = $parte[2];
                    $ativo = $parte[4];
                    
                    # Procura o nome no sistema
                    $idPessoa = $pessoal->get_idPessoaNome($nome);
                    
                    # Pega o cpf
                    if(!empty($idPessoa)){
                        $cpf = preg_replace('/\D/', '', $pessoal->get_cpf($idPessoa));
                    }else{
                        $cpf = "";
                        $problemas++;
                    }

                    # Inicia a linha
                    echo "<tr>";

                    $contador++;

                    echo "<td id='center'>{$contador}</td>";
                    #echo "<td id='center'>{$orgao}</td>";
                    echo "<td id='center'>{$login}</td>";
                    echo "<td id='left'>{$nome}</td>";
                    echo "<td id='center'>{$cpf}</td>";
                    echo "<td id='center'>{$ativo}</td>";
                    echo "</tr>";
                }

                echo "</table>";

                echo "Registros analisados: {$contador}";
                echo "<br/>Nomes Não Encontrados: {$problemas}";

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
            
            echo "Não cabe";
            break;

        #########################################################################    
    }
    $grid->fechaColuna();
    $grid->fechaGrid();
    $page->terminaPagina();
} else {
    loadPage("login.php");
}