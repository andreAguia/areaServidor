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
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){

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
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    br();

    #########################################################################
    
    switch ($fase){       
        case "" : 
            # Botão voltar
            $linkVoltar = new Link("Voltar",'administracao.php');
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Volta para a página anterior');
            $linkVoltar->set_accessKey('V');
            
            # 2015 - 2019
            $link2015 = new Link("2015 - 2019","importacaoFerias.php?fase=aguarda");
            $link2015->set_class('button');
            $link2015->set_title('Importar');
            
            # 2014
            $link2014 = new Link("2014","importacaoFerias.php?fase=2014");
            $link2014->set_class('button');
            $link2014->set_title('Importar');

            # Cria um menu
            $menu = new MenuBar();
            $menu->add_link($linkVoltar,"left");
            $menu->add_link($link2015,"right"); 
            $menu->add_link($link2014,"right"); 
            
            $menu->show();
            
            titulo('Importação de Férias de arquivo do Excell para o banco de dados');
            break;
        
        #########################################################################
    
        case "aguarda" :
            titulo('Analisando ...');
            br(4);
            aguarde("Analisando o arquivo.");

            loadPage('?fase=analisa');
            break;
        
        #########################################################################

        case "analisa" :            
            # Cria um menu
            $menu = new MenuBar();            
    
            # Define o arquivo a ser importado
            $arquivo = "../importacao/ferias.csv";

            # Botão voltar
            $linkBotao1 = new Link("Voltar",'importacaoFerias.php');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1,"left");

            # Refazer
            $linkBotao2 = new Link("Refazer",'?fase=aguarda2');
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('R');
            $menu->add_link($linkBotao2,"right");
            $menu->show();

            titulo('Importação da tabela de Férias');

            # Cria um painel
            $painel = new Callout();
            $painel->abre();
            
            # Abre o banco de dados
            $pessoal = new Pessoal();

            # Verifica a existência do arquivo
            if(file_exists($arquivo)){
                $lines = file($arquivo);

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    $linha = htmlspecialchars($linha);

                    $parte = explode(";",$linha);
                    $idServidor = $pessoal->get_idServidoridFuncional($parte[0]);
                    $nome = $pessoal->get_nome($idServidor);

                    $diferenca = dataDif($parte[2], $parte[3]) + 1;
                    $anoExercicio = year($parte[4]);
                    if(isset($$anoExercicio)){
                        $$anoExercicio++;
                    }else{
                        $$anoExercicio = 1;
                    }
                    $tt++;
                    $contador++;
                }
               
                echo "Registros analisados: ".$tt;
                br();
                echo "Problemas encontrados: ".$problemas;
                br();
                $contador = 1;
            }else{
                echo "Arquivo de Férias não encontrado";
                br();
                $problemas++;
            }
            
            # Exibe as férias de qual ano base
            for ($i = 2000; $i <= 2019; $i++) {
                if(isset($$i)){
                    echo $i." = ".$$i;
                    br();
                }
            }
            
            echo "Podemos fazer a importação";
            br(2);
            # Botão importar
            $linkBotao1 = new Link("Importar",'?fase=aguarda2');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('I');
            $linkBotao1->show();

            br(2);

            # Inicia a Tabela
            echo "<table border=1>";
            echo "<tr>";
            echo "<th>#</th>";
            echo "<th>IdFuncional</th>";
            echo "<th>Nome</th>";
            echo "<th>Data Inicial</th>";
            echo "<th>Data Final</th>";
            echo "<th>Inicio Período Aq.</th>";
            echo "<th>Término Período Aq.</th>";
            echo "</tr>";

            $contador = 1;

            # Percorre o arquivo e guarda os dados em um array
            foreach ($lines as $linha) {
                $linha = htmlspecialchars($linha);

                $parte = explode(";",$linha);
                $idServidor = $pessoal->get_idServidoridFuncional($parte[0]);
                $nome = "nome";

                # Exibe os dados
                echo "<tr>";
                echo "<td rowspan='2'>".$contador."</td>";
                echo "<td>".$parte[0]."</td>";
                echo "<td>".$parte[1]."</td>";
                echo "<td>".$parte[2]."</td>";
                echo "<td>".$parte[3]."</td>";
                echo "<td>".$parte[4]."</td>";
                echo "<td>".$parte[5]."</td>";
                echo "</tr>";

                ################################

                $diferenca = dataDif($parte[2], $parte[3]) + 1;

                echo "<tr>";
                #echo "<td>".$contador."</td>";
                echo "<td>".$idServidor."</td>";
                echo "<td>".$nome."</td>";
                echo "<td>".$parte[2]."</td>";
                echo "<td>".$diferenca." dias</td>";
                echo "<td>".year($parte[4])."</td>";
                echo "<td></td>";
                echo "</tr>";

                $contador++;
            }                
            echo "</table>";

            br(2);
            # Botão importar
            $linkBotao1 = new Link("Importar",'?fase=importa');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('I');
            $linkBotao1->show();
            
            $painel->fecha();
            break;
            
        #########################################################################    
            
        case "importa" :
            titulo('Importando ...');
            br(4);
            aguarde("Importando o arquivo.");
            br();
            loadPage('?fase=importa2');
            break;
        
        #########################################################################
        
        case "importa2" :
            
            # Define o arquivo a ser importado
            $arquivo = "../importacao/ferias.csv";
            
            # Verifica a existência do arquivo
            if(file_exists($arquivo)){
                $lines = file($arquivo);
                
                # Abre o banco de dados
                $pessoal = new Pessoal();
                
                # Array para inserir os dados
                $conteúdo = array();

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    $linha = htmlspecialchars($linha);

                    $parte = explode(";",$linha);
                    $idServidor = $pessoal->get_idServidoridFuncional($parte[0]);
                    $diferenca = dataDif($parte[2], $parte[3]) + 1;
                    $anoExercicio = year($parte[4]);

                    # Grava na tabela
                    $campos = array("idServidor","dtInicial","anoExercicio","numDias","status");
                    $valor = array($idServidor,date_to_bd($parte[2]),$anoExercicio,$diferenca,"fruída");                    
                    $pessoal->gravar($campos,$valor,NULL,"tbferias","idFerias",FALSE);
                }
            }else{
                echo "Arquivo de Férias não encontrado";
            }
            loadPage("?fase=termina");
            break;
            
        #########################################################################    
            
        case "termina" :
            titulo('Importação Terminada');
            br(4);
            P("Importação executada com sucesso !!");
            br(2);
            
            # Botão importar
            $linkBotao1 = new Link("Ok",'?');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página Inicial');
            $linkBotao1->show();
            break;
        
        #########################################################################
    
        case "2014" :
            titulo('Analisando ...');
            br(4);
            aguarde("Analisando o arquivo.");

            loadPage('?fase=analisa2014');
            break;
        
        #########################################################################

        case "analisa2014" :

            # Define o arquivo a ser importado
            $arquivo = "../importacao/2014.csv";
            
            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar",'importacaoFerias.php');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1,"left");

            # Refazer
            $linkBotao2 = new Link("Refazer",'?fase=2014');
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('R');
            $menu->add_link($linkBotao2,"right");
            $menu->show();

            titulo('Importação da tabela de Férias 2014');

            # Cria um painel
            $painel = new Callout();
            $painel->abre();
            
            # Abre o banco de dados
            $pessoal = new Pessoal();

            # Verifica a existência do arquivo
            if(file_exists($arquivo)){
                $lines = file($arquivo);
                
                # Inicia contador
                $contador = 1;
                
                # Inicia a Tabela
                echo "<table border=1>";
                echo "<tr>";
                echo "<th>#</th>";
                echo "<th>IdFuncional</th>";
                echo "<th>Nome</th>";
                echo "<th>Ano Exercicio</th>";
                echo "<th>Data Inicial</th>";
                echo "<th>Dias</th>";
                echo "<th>Obs</th>";
                echo "</tr>";

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    $linha = htmlspecialchars($linha);

                    $parte = explode(";",$linha);
                    
                    # Pega o id Funcional a partir da matrícula
                    $idServidor = $pessoal->get_idServidor($parte[0]);
                    $idFuncional = $pessoal->get_idFuncional($idServidor);
                    
                    # Pega o nome
                    $nome = $pessoal->get_nome($idServidor);
                    
                    # Data Inicial
                    $dtInicial = $parte[1];
                    
                    # Ano Exercicio
                    $anoExercicio = $parte[2];
                    
                    # Obs
                    $obs = $parte[3];
                    
                    # Numero de dias
                    $numDias = $parte[4];
                    
                    echo "<tr>";
                    echo "<td>".$contador."</td>";
                    echo "<td>$idFuncional</td>";
                    echo "<td>$nome</td>";
                    echo "<td>$anoExercicio</td>";
                    echo "<td>$dtInicial</td>";
                    echo "<td>$numDias</td>";
                    echo "<td>$obs</td>";
                    echo "</tr>";
                    $contador++;
                    
                    echo $linha;
                    br(2);
                }
                
                echo "</table>";
               
                echo "Registros analisados: ".$tt;
            }else{
                echo "Arquivo de Férias não encontrado";
            }
            
            echo "Podemos fazer a importação";
            br(2);
            # Botão importar
            $linkBotao1 = new Link("Importar",'?fase=aguarda2014');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('I');
            $linkBotao1->show();
            
            $painel->fecha();
            break;
            
        #########################################################################    

    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}