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
    $contador = 1;
    
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
            
            # Analisa
            $link2019 = new Link("Analisa","?fase=inicia");
            $link2019->set_class('button');
            $link2019->set_title('Importar');

            # Cria um menu
            $menu = new MenuBar();
            $menu->add_link($linkVoltar,"left");
            $menu->add_link($link2019,"right"); 
            
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

            # Define o arquivo a ser importado
            $arquivo = "../importacao/fen005.csv";
            
            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar",'?');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1,"left");

            # Refazer
            $linkBotao2 = new Link("Refazer",'?fase=inicia');
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('R');
            $menu->add_link($linkBotao2,"right");
            $menu->show();

            titulo("Importação da tabela FEN005");

            # Cria um painel
            $painel = new Callout();
            $painel->abre();
            
            # Abre o banco de dados
            $pessoal = new Pessoal();
            
            $problema = 0;

            # Verifica a existência do arquivo
            if(file_exists($arquivo)){
                $lines = file($arquivo);
                
                # Inicia variáveis
                $contador = 0;
                $erro = 0;
                
                # Inicia a Tabela
                echo "<table border=1>";
                echo "<tr>";
                echo "<th>#</th>";
                echo "<th>MATR</th>";
                echo "<th>DT</th>";
                echo "<th>SAL</th>";
                echo "<th>CLASS</th>";
                echo "<th>CARGO</th>";
                echo "<th>PERC</th>";
                #echo "<th>OBS</th>";
                echo "</tr>";

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    $linha = htmlspecialchars($linha);

                    $parte = explode(",",$linha);
                    
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
                    
                    $contador++;
                    
                    if(is_null($nome)){
                        $problema++;
                        echo "<tr id='logExclusao'>";
                    }else{
                        echo "<tr>";
                    }
                    
                    echo "<td id='center'>$contador</td>";
                    echo "<td id='left'>Matrícula: $MATR<br/>IdServidor: $idServidor<br/>Nome: $nome</td>";
                    echo "<td id='center'>$DT</td>";
                    echo "<td id='center'>$SAL</td>";
                    echo "<td id='center'>$CLASS</td>";
                    echo "<td id='center'>$CARGO</td>";
                    echo "<td id='center'>$PERC</td>";
                    #echo "<td>$OBS</td>";
                    echo "</tr>";     
                    echo "</tr>";
                }
                
                echo "</table>";
               
                echo "Registros analisados: ".$contador;
                br();
                
                echo "$problema Erro(s) encontrados";
                br();
                echo $contador - $problema." registros prontos para serem importados";
                
                /*
                br(2);
                if($erro == 0){
                    echo "Podemos fazer a importação";
                }else{
                    echo "Podemos fazer a importação, mas os $erro registros com problemas serão ignorados.";
                }
                
                br(2);
                # Botão importar
                $linkBotao1 = new Link("Importar",'?fase=aguarda');
                $linkBotao1->set_class('button');
                $linkBotao1->set_title('Volta para a página anterior');
                $linkBotao1->set_accessKey('I');
                $linkBotao1->show();
                 * 
                 */
            }else{
                echo "Arquivo de Férias não encontrado";
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
            if(file_exists($arquivo)){
                $lines = file($arquivo);
                
                # Inicia Variáveis
                $contador = 0;
                $ignorados = 0;

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    
                    $linha = htmlspecialchars($linha);

                    $parte = explode(",",$linha);
                    
                    # Pega os dados
                    $idFuncional = $parte[0];           // IdFuncional
                    $nomeImportado = $parte[1];         // Nome
                    $dtInicial = $parte[2];             // Data Inicial
                    $dtFinal = $parte[3];               // Data Final
                    $dtInicialAquisitivo = $parte[4];   // Data Inicial Aquisitivo
                    $dtFinalAquisitivo = $parte[5];     // Data Final Aquisitivo
                    
                    # IdServidor ## Problema aqui !!! Pega o primeiro que acha e não o idServidor atual ativo
                    $idServidor = $pessoal->get_idServidoridFuncional($idFuncional);   
                    
                    # Dados Tratados
                    $nome = $pessoal->get_nome($idServidor);                                // Nome
                    $numDias = dataDif($dtInicial, $dtFinal) + 1;
                    $anoExercicio = year($dtInicialAquisitivo);
                    
                    if($anoExercicio == $anoImportacao){
                        # Grava na tabela
                        $campos = array("idServidor","dtInicial","anoExercicio","numDias","status");
                        $valor = array($idServidor,date_to_bd($dtInicial),$anoExercicio,$numDias,"fruída");                    
                        $pessoal->gravar($campos,$valor,NULL,"tbferias","idFerias",FALSE);
                        $contador++;
                    }else{
                        $ignorados++;
                    }
                }
               
                # Rotina que altera fruída para Solicitada e vice versa
                $pessoal->mudaStatusFeriasSolicitadaFruida();
                
                # Informa sobre a importação
                echo "Registros importados: ".$contador;
                br();
                echo $ignorados." registros ignorados por não serem do ano desejado.";
            }else{
                echo "Arquivo de Férias não encontrado";
            }
            br(2);
            # Botão voltar
            $linkBotao1 = new Link("Voltar",'?');
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