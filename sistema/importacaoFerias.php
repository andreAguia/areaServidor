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
    
    # Parâmetros da importação
    $arquivo = "../importacao/ferias2014.csv";  // Arquivo a ser carregado
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
            br(4);
            aguarde();
            br();    
           
            loadPage('?fase=importa');
            break;

        case"importa" :            
        # Cria um menu
        $menu = new MenuBar();
            
        $pessoal = new Pessoal();

        # Botão voltar
        $linkBotao1 = new Link("Voltar",'administracao.php?fase=importacao');
        $linkBotao1->set_class('button');
        $linkBotao1->set_title('Volta para a página anterior');
        $linkBotao1->set_accessKey('V');
        $menu->add_link($linkBotao1,"left");

        # Refazer
        $linkBotao2 = new Link("Refazer","?");
        $linkBotao2->set_class('button');
        $linkBotao2->set_title('Refazer a Importação');
        $linkBotao2->set_accessKey('R');
        $menu->add_link($linkBotao2,"right");
        $menu->show();

        titulo('Importação da tabela de Férias');

        # Cria um painel
        $painel = new Callout();
        $painel->abre();

        # Verifica a existência do arquivo
        if(file_exists($arquivo)){
            $lines = file($arquivo);
            
            echo "<table>";
            
            echo "<tr>";
            echo "<th>#</th>";
            echo "<th>IdFuncional</th>";
            echo "<th>Nome</th>";
            echo "<th>Data Inicial</th>";
            echo "<th>Data Final</th>";
            echo "<th>Inicio Período Aq.</th>";
            echo "<th>Término Período Aq.</th>";
            echo "</trs>";

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
                    echo "<td>".$parte[4]."</td>";
                    echo "<td>".$parte[5]."</td>";
                    echo "</tr>";

                    $tt++;
                    $contador++;
                }
            }
            
            echo "</table>";
            echo "Registros importados:".$tt;
            br();
            echo "Problemas encontrados:".$problemas;
            br();
            $contador = 1;
            echo "<table border=1>";
            
            echo "<tr>";
            echo "<th>#</th>";
            echo "<th>IdFuncional</th>";
            echo "<th>Nome</th>";
            echo "<th>Data Inicial</th>";
            echo "<th>Data Final</th>";
            echo "<th>Inicio Período Aq.</th>";
            echo "<th>Término Período Aq.</th>";
            echo "</trs>";
            
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
                echo "<td>".$parte[4]."</td>";
                echo "<td>".$parte[5]."</td>";
                echo "</tr>";

                $contador++;
                }
            
        }else{
            echo "Arquivo de exemplo não encontrado";
        }
        
        $painel->fecha();
        break;
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}