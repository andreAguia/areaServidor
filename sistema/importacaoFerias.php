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

    switch ($fase)
    {       
        case "" : 
            # Botão voltar
            $linkVoltar = new Link("Voltar",'administracao.php');
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Volta para a página anterior');
            $linkVoltar->set_accessKey('V');
            
            # 2014
            $link2014 = new Link("2014","importacaoFerias.php?fase=2014");
            $link2014->set_class('button');
            $link2014->set_title('Férias de 2014');

            # 2015
            $link2015 = new Link("2015","importacaoFerias.php?fase=2015");
            $link2015->set_class('button');
            $link2015->set_title('Férias de 2015');
            
            # 2016
            $link2016 = new Link("2016","importacaoFerias.php?fase=2016");
            $link2016->set_class('button');
            $link2016->set_title('Férias de 2016');

            # Cria um menu
            $menu = new MenuBar();
            $menu->add_link($linkVoltar,"left");
            $menu->add_link($link2014,"right");
            $menu->add_link($link2015,"right");
            $menu->add_link($link2016,"right"); 
            $menu->show();
            
            titulo('Importação do banco de dados');
            break;
    
        case "2014" : 
        case "2015" :
        case "2016" : 
            br(4);
            aguarde();
            br();
            
            # Limita a tela
            $grid1 = new Grid("center");
            $grid1->abreColuna(5);
                p("Analisando o arquivo do ano ".$fase,"center");
            $grid1->fechaColuna();
            $grid1->fechaGrid();

            loadPage('?fase=analisa&ano='.$fase);
            break;

        case "analisa" :            
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

            # Refazer
            $linkBotao2 = new Link("Refazer",'?fase='.$ano);
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('R');
            $menu->add_link($linkBotao2,"right");
            $menu->show();

            titulo('Importação da tabela de Férias de '.$ano);

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Verifica a existência do arquivo
            if(file_exists($arquivo)){
                $lines = file($arquivo);
                
                # Array para inserir os dados
                $conteúdo = array();

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    $linha = htmlspecialchars($linha);

                    $parte = explode(";",$linha);
                    $idServidor = $pessoal->get_idServidoridFuncional($parte[0]);
                    $nome = $pessoal->get_nome($idServidor);

                    if(is_null($nome)){
                        $problemas++;
                        $problemaLinha[] = $linha;
                    }else{
                        $conteudo[] = array($contador,$parte[0],$parte[1],$parte[2],$parte[3],$parte[4],$parte[5]);

                        $diferenca = dataDif($parte[2], $parte[3]) + 1;
                        
                        $conteudo[] = array($contador,$idServidor,$nome,$parte[2],$diferenca,year($parte[4]),"");
                        $tt++;
                        $contador++;
                    }
                }

               
                echo "Registros analisados: ".$tt;
                br();
                echo "Problemas encontrados: ".$problemas;
                br();
                $contador = 1;
                
                if($problemas > 0){
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
                    foreach ($problemaLinha as $linha2) {

                        $parte = explode(";",$linha2);
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
                }

            }else{
                echo "Arquivo de Férias não encontrado";
            }
            
            if($problemas == 0){
                echo "Podemos fazer a importação";
                br(2);
                # Botão importar
                $linkBotao1 = new Link("Importar",'?fase=importa&ano='.$ano);
                $linkBotao1->set_class('button');
                $linkBotao1->set_title('Volta para a página anterior');
                $linkBotao1->set_accessKey('I');
                $linkBotao1->show();
                
            }else{
                echo "Ainda temos problemas";
            }

            $painel->fecha();
            break;
            
        case "importa" :
            br(4);
            aguarde();
            br();
            
            # Limita a tela
            $grid1 = new Grid("center");
            $grid1->abreColuna(5);
                p("Importando o arquivo do ano ".$ano,"center");
            $grid1->fechaColuna();
            $grid1->fechaGrid();

            loadPage('?fase=importa2&ano='.$ano);
            break;
        
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

                    $conteudo[] = array($contador,$idServidor,$nome,$parte[2],$diferenca,year($parte[4]),"");
                    $tt++;
                    $contador++;

                    # Grava na tabela
                    $campos = array("idServidor","dtInicial","anoExercicio","numDias","status");
                    $valor = array($idServidor,date_to_bd($parte[2]),$ano,$diferenca,"fruída");                    
                    $pessoal->gravar($campos,$valor,NULL,"tbferias","idFerias",FALSE);
                }
            }else{
                echo "Arquivo de Férias não encontrado";
            }
            loadPage("?");
            break;
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}