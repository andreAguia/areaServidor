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
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

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

    titulo('Importação da tabela de Afastamentos');
    
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
    $select = "SELECT * FROM fen004 ORDER BY TP_AFA";

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

            case 51:
                echo "Lic sem vencimentos. Importar para tipo 16.";
                $numItensImportados++;
                break;

            case 52:
                label("Afastamento por prisão. Ver o que fazer.","alert");
                $numItensDescartados++;
                break;

            case 53:
                label("Pedido exoneração. Não importar.","alert");
                $numItensDescartados++;
                break;

            case 54:
                label("Falecimento. Não importar.","alert");
                $numItensDescartados++;
                break;

            case 55:
                label("Rescisão do contrato. Não importar.","alert");
                $numItensDescartados++;
                break;

            case 56:
                echo "Afastamento do pais. Importar para tipo 23.";
                $numItensImportados++;
                break;

            case 57:
                label("Suspensão do contrato. Não importar.","alert");
                $numItensDescartados++;
                break;

            case 58:
                label("Acidente de trabalho com alta. Ver o que fazer.","alert");
                $numItensDescartados++;
                break;

            case 59:
                label("Impontualidade. Ver o que fazer.","alert");
                $numItensDescartados++;
                break;

            case 60:
                echo "Doação de sangue. Importar para tipo 20.";
                $numItensImportados++;
                break;

            case 61:
                label("Resolução da Sare 3434/4. Ver o que fazer.","alert");
                $numItensDescartados++;
                break;

            case 62:
                echo "Licença para estudo. Importar para tipo 7.";
                $numItensImportados++;
                break;

            case 63:
                label("Acidente de trabalho sem alta. Ver o que fazer.","alert");
                $numItensDescartados++;
                break;

            case 64:
                label("Suspensão preventiva. Não importar.","alert");
                $numItensDescartados++;
                break;

            case 65:
                echo "Afastamento do pais. Importar para tipo 23.";
                $numItensImportados++;
                break;

            case 66:
                echo "Pos doc. Importar para tipo 7.";
                $numItensImportados++;
                break;

            case 67:
                echo "Licença Luto. Importar para tipo 12.";
                $numItensImportados++;
                break;

            case 68:
                echo "Estagio senior. Importar para tipo 7.";
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

    $painel->fecha();
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}