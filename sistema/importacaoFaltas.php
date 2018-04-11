<?php
/**
 * Rotina de Importação de Faltas
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
            # Cria menu
            $menu = new MenuBar();
            
            # Botão voltar
            $link = new Link("Voltar",'administracao.php?fase=importacao');
            $link->set_class('button');
            $link->set_title('Volta para a página anterior');
            $link->set_accessKey('V');
            $menu->add_link($link,"left");
            
            # 2017
            $link = new Link("Analisar","?fase=aguarda");
            $link->set_class('button');
            $link->set_title('Importando tabela de afastamento os lançamentos tipo 10 e 33');
            $menu->add_link($link,"right"); 
            
            # Cria um menu
            $menu->show();
            
            titulo('Importação de Faltas de arquivo do Excell para o banco de dados');
            break;
        
        #########################################################################
    
        case "aguarda" :
            titulo('Analisando ...');
            br(4);
            aguarde("Analisando o arquivo de faltas");

            loadPage('?fase=analisa');
            break;
        
        #########################################################################

        case "analisa" :            
            # Cria um menu
            $menu = new MenuBar();
            
            # Define o arquivo a ser importado
            $arquivo = "../importacao/fen004.csv"; 

            # Abre o banco de dados
            $pessoal = new Pessoal();

            # Botão voltar
            $linkBotao1 = new Link("Voltar",'?fase=importacao.php');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1,"left");

            # Refazer
            $linkBotao2 = new Link("Refazer",'?fase=analisa');
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('R');
            $menu->add_link($linkBotao2,"right");
            $menu->show();

            titulo('Importação da tabela de Faltas');

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Verifica a existência do arquivo
            if(file_exists($arquivo)){
                $lines = file($arquivo);
                
                # Inicia a tabela
                echo "<table border=1>";

                echo "<tr>";
                echo "<th>#</th>";
                echo "<th>Matrícula</th>";
                echo "<th>Nome</th>";
                echo "<th>Data Inicial</th>";
                echo "<th>Data Final</th>";
                echo "<th>Dias</th>";
                echo "<th>Tipo</th>";
                echo "</tr>";

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    # Retira lixos de formatação
                    $linha = htmlspecialchars($linha);

                    # Divide as colunas
                    $parte = explode(";",$linha);
                    
                    # Verifica se o tipo é 10 ou 33                    
                    if(($parte[2]==10)OR($parte[2]==33)){
                    
                        # Pega o idServidor da matrícula
                        $idServidor = $pessoal->get_idServidor($parte[0]);

                        # Pega o nome
                        $nome = $pessoal->get_nome($idServidor);

                        # Calcula a quantidade de dias
                        $diferenca = dataDif($parte[1], $parte[3]) + 1;

                        # Exibe os dados
                        echo "<tr>";
                        echo "<td>".$contador."</td>";
                        echo "<td>".$parte[0]."</td>";
                        echo "<td>".$nome."</td>";
                        echo "<td>".$parte[1]."</td>";
                        echo "<td>".$parte[3]."</td>";
                        echo "<td>".$diferenca."</td>";
                        echo "<td>".$parte[2]."</td>";
                        echo "</tr>";
                        $contador++;
                    }
                }
               
                echo "Registros analisados: ".$tt;
                br();
                echo "Problemas encontrados: ".$problemas;
                
            }else{
                echo "Arquivo de Faltas não encontrado";
                br();
                $problemas++;
            }
            
            if($problemas == 0){
                echo "Podemos fazer a importação";
                br(2);
                # Botão importar
                $linkBotao1 = new Link("Importar",'?fase=aguarda2&ano='.$ano);
                $linkBotao1->set_class('button');
                $linkBotao1->set_title('Volta para a página anterior');
                $linkBotao1->set_accessKey('I');
                $linkBotao1->show();
                
            }else{
                echo "Temos problemas";
            }

            $painel->fecha();
            break;
            
         #########################################################################
    
        case "aguarda2" :
            titulo('Analisando ...');
            br(4);
            aguarde("Analisando o arquivo do ano ".$ano);

            loadPage('?fase=analisa2&ano='.$ano);
            break;
        
        #########################################################################    
            
        case "analisa2" :            
            # Cria um menu
            $menu = new MenuBar();
            
            # Define o arquivo a ser importado
            $arquivo = "../importacao/ferias".$ano.".csv"; 

            # Abre o banco de dados
            $pessoal = new Pessoal();

            # Botão voltar
            $linkBotao1 = new Link("Voltar",'importacaoFerias.php');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1,"left");
            $menu->show();

            titulo('Verifique se está tudo certo - Férias '.$ano);

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Verifica a existência do arquivo
            if(file_exists($arquivo)){
                $lines = file($arquivo);
                
                # Array para inserir os dados
                $conteúdo = array();
                $contador = 1;
                
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

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    $linha = htmlspecialchars($linha);

                    $parte = explode(";",$linha);
                    $idServidor = $pessoal->get_idServidoridFuncional($parte[0]);
                    $nome = $pessoal->get_nome($idServidor);
                    
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
            }
            
            br(2);
            # Botão importar
            $linkBotao1 = new Link("Importar",'?fase=importa&ano='.$ano);
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
            aguarde("Importando o arquivo do ano ".$ano);
            br();
            loadPage('?fase=importa2&ano='.$ano);
            break;
        
        #########################################################################
        
        case "importa2" :
            # Define o arquivo a ser importado
            $arquivo = "../importacao/ferias".$ano.".csv"; 

            # Verifica a existência do arquivo
            if(file_exists($arquivo)){
                $lines = file($arquivo);

                # Array para inserir os dados
                $conteúdo = array();

                # Abre o banco de dados
                $pessoal = new Pessoal();

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    $linha = htmlspecialchars($linha);

                    $parte = explode(";",$linha);
                    $idServidor = $pessoal->get_idServidoridFuncional($parte[0]);
                    $nome = $pessoal->get_nome($idServidor);

                    $conteudo[] = array($contador,$parte[0],$parte[1],$parte[2],$parte[3],$parte[4],$parte[5]);

                    $diferenca = dataDif($parte[2], $parte[3]) + 1;
                    
                    $anoExercicio = year($parte[4]);

                    $conteudo[] = array($contador,$idServidor,$nome,$parte[2],$diferenca,year($anoExercicio),"");
                    $tt++;
                    $contador++;

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
            $linkBotao1->set_accessKey('I');
            $linkBotao1->show();
            break;
        
        #########################################################################
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}