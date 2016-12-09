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
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){
    
    # cria um cronômetro a ser exibido na tela
    $script = '<script>
        function formatatempo(segs) {
        min = 0;
        hr = 0;
        /*
        if hr < 10 then hr = "0"&hr
        if min < 10 then min = "0"&min
        if segs < 10 then segs = "0"&segs
        */
        while(segs>=60) {
        if (segs >=60) {
        segs = segs-60;
        min = min+1;
        }
        }

        while(min>=60) {
        if (min >=60) {
        min = min-60;
        hr = hr+1;
        }
        }

        if (hr < 10) {hr = "0"+hr}
        if (min < 10) {min = "0"+min}
        if (segs < 10) {segs = "0"+segs}
        fin = hr+":"+min+":"+segs
        return fin;
        }
        var segundos = 0; //inicio do cronometro
        function conta() {
        segundos++;
        document.getElementById("counter").innerHTML = formatatempo(segundos);
        }

        function inicia(){
        interval = setInterval("conta();",1000);
        }

        function para(){
        clearInterval(interval);
        }

        function zera(){
        clearInterval(interval);
        segundos = 0;
        document.getElementById("counter").innerHTML = formatatempo(segundos);
        }
        </script>';

    # Começa uma nova página
    $page = new Page();
    $page->set_jscript($script);
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

    # Botão voltar
    $linkBotao1 = new Link("Voltar",'administracao.php');
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

    titulo('Importação da tabela de Afastamentos');
    titulo('<span id=\'counter\'>00:00:00</span>');
    
    # Verifica a fase do programa
    $fase = get('fase');

    switch ($fase)
    {
        case "" :
            br(4);
            aguarde();
            br();
            echo '<script>inicia();</script>';

            loadPage('?fase=importa');
            break;

        case"importa" :
            # Começa a medir o tempo
            $time_start = microtime(true);
            
            # Cria um painel
            $painel = new Callout();
            $painel->abre();
            
            # Conecta ao banco
            $uenf = new Uenf();
            $pessoal = new Pessoal();
                    
            # Pega a quantidade de registros com os bolsistas
            $select = "select matr from fen004";
            $result = $uenf->select($select);
            $totalRegistros = count($result);            
            echo $totalRegistros." Registros";
            
            # Variáveis
            $numItens = 0;              // Número de itens importados
            $numItensDescartados = 0;   // Número de itens descartados   
            $numItensImportados = 0;    // Número de itens importados
            $numItensFerias = 0;        // Número de itens importados
            $numItensNAnalisados = 0;   // Número de itens não analisados
            
            # Inicia a Importação
            $select = "SELECT * FROM fen004";

            $conteudo = $uenf->select($select,true);
            
            echo "<table class='tabelaPadrao'>";
            echo "<tr>";
            echo "<th>Matrícula</th>";
            echo "<th>Data Inicial</th>";
            echo "<th>Data Final</th>";
            echo "<th>Tipo</th>"; 
            echo "<th>Importar para</th>"; 
            echo "</tr>";
            
            # Percorre a tabela
            foreach ($conteudo as $campo){
                echo "<tr>";
                echo "<td>".$campo[0]."</td>";
                echo "<td>".datetime_to_php($campo[1])."</td>";
                echo "<td>".datetime_to_php($campo[3])."</td>";
                echo "<td>".$campo[2]."</td>"; 
                
                echo "<td>";
                switch ($campo[2]){
                    case 1:
                        label("Ativo.Não importar.","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 2:
                        echo "Licença Sem Vencimento. Importar para tipo 16.";
                        $numItensImportados++;
                        break;
                    
                    case 3:
                        label("Acidente de trabalho. Ainda não sei.","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 4:
                        echo "Serviço milirar. Importar para tipo 4.";
                        $numItensImportados++;
                        break;
                    
                    case 5:
                        echo "Licença Gestante. Importar para tipo 18.";
                        $numItensImportados++;
                        break;
                    
                    case 6:
                        echo "Afastado por doença. Importar para tipo 21.";
                        $numItensImportados++;
                        break;
                    
                    case 7:
                    case 8:
                    case 9:
                    case 0:
                        label("Não importar.","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 10:
                        echo "Faltas. Importar para tipo 25.";
                        $numItensImportados++;
                        break;
                    
                    case 11:
                        echo "Afastamento por Luto. Importar para tipo 12.";
                        $numItensImportados++;
                        break;
                    
                    case 12:
                        echo "Afastamento por casamento. Importar para tipo 11.";
                        $numItensImportados++;
                        break;
                    
                    case 13:
                        label("Afastamento obrigatório. Não sei o que fazer.","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 14:
                        echo "Afastamento para juri. Importar para tipo 22.";
                        $numItensImportados++;
                        break;
                    
                    case 15:
                        echo "Afastamento para TRE. Importar para tabela do TRE.";
                        $numItensImportados++;
                        break;
                    
                    case 16:
                        echo "Afastamento para Campanha eleitoral. Importar para tipo 17.";
                        $numItensImportados++;
                        break;
                    
                    case 17:
                        echo "Falta abonada.Atestado. Importar para tabela de atestados.";
                        $numItensImportados++;
                        break;
                    
                    case 18:
                    case 19:
                        label("Férias. Importar para tabela de férias..","success");
                        $numItensFerias++;
                        break;
                    
                    case 20:
                        echo "Lic Especial (premio). Importar para tipo 6.";
                        $numItensImportados++;
                        break;
                    
                    case 21:
                        echo "Lic Paternidade. Importar para tipo 13.";
                        $numItensImportados++;
                        break;
                    
                    case 22:
                        echo "Lic Saúde INSS. Importar para tipo 21.";
                        $numItensImportados++;
                        break;
                    
                    case 23:
                    case 24:
                    case 25:
                    case 26: 
                        label("Lic Saúde INSS. Ver o que fazer.","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 27:
                        echo "Lic Saúde INSS família. Importar para tipo 2.";
                        $numItensImportados++;
                        break;
                    
                    case 28:
                        echo "Lic Adoção. Importar para tipo 14 ou 15. Dependendo do gênero.";
                        $numItensImportados++;
                        break;
                    
                    case 29:
                        echo "Lic Amamentação. Importar para tipo 10.";
                        $numItensImportados++;
                        break;
                    
                    case 30:
                    case 31:    
                        label("Ignora.","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 32:
                        echo "Suspensao. Importar para tipo 26.";
                        $numItensImportados++;
                        break;
                    
                    case 33:
                        echo "Faltas. Importar para tipo 25.";
                        $numItensImportados++;
                        break;
                    
                    case 34:
                        label("Disp outro orgao.Não Importar.","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 35:
                        label("Inq. Administrativo.Não Importar.","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 36:
                        echo "Afastamento para TRE. Importar para tabela do TRE.";
                        $numItensImportados++;
                        break;
                    
                    case 37:
                        echo "Afastamento para estudo. Importar para tipo 7.";
                        $numItensImportados++;
                        break;
                    
                    case 38:
                    case 39:
                    case 40:
                    case 41:
                    case 42:
                    case 43:
                        label("Aposentadoria. Não importar.","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 44:
                        label("Estagio Experimental. Não importar.","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 45:
                        label("Falta para prova. Ainda não sei","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 46:
                        label("Falta por greve. Ainda não sei","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 47:
                        label("Abandono de serviço. Não importar","alert");
                        $numItensDescartados++;
                        break;
                    
                    case 48:
                        echo "Afastamento para exercer mandato. Importar para tipo 8.";
                        $numItensImportados++;
                        break;
                    
                    case 49:
                        echo "Lic serv Militar. Importar para tipo 4.";
                        $numItensImportados++;
                        break;
                    
                    case 50:
                        echo "Lic acompanhar conjuge. Importar para tipo 5.";
                        $numItensImportados++;
                        break;
                    
                    case 50:
                        echo "Lic sem vencimentos. Importar para tipo 16.";
                        $numItensImportados++;
                        break;
                    
                    default:
                        label("Ainda não analisado.");
                        $numItensNAnalisados++;
                        break;
                }	
                echo "</td>";
                echo "</tr>";
                
                $numItens++;
                }
            
            echo "</table>";
            
            # Exibe o número de itens importado
            echo $numItens." itens";br();
            echo $numItensDescartados." itens descartados";br();
            echo $numItensImportados." itens importados";br();
            echo $numItensFerias." itens Férias";br();
            echo $numItensNAnalisados." itens Não analisados";br();
            
            # Pega o tempo final
            $time_end = microtime(true);
            
            # Calcula  e exibe o tempo
            $time = $time_end - $time_start;
            br();
            echo ($time/60).":".fmod($time,60)."  minutos";
    
            $painel->fecha();
            break;
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}