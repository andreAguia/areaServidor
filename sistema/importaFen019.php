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
    
    # Verifica a fase do programa
    $fase = get('fase');
    
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

        # Conecta ao banco
        $uenf = new Uenf();
        $pessoal = new Pessoal();

        # Pega a quantidade de registros com os bolsistas
        $select = "select matr FROM `fen019` where dt > '1993-01-01 00:00:00' order by dt";
        $result = $uenf->select($select);
        $totalRegistros = count($result); 

        titulo('Importação da tabela de Férias - '.$totalRegistros." Registros");

        # Cria um painel
        $painel = new Callout();
        $painel->abre();

        # Variáveis
        $numItensImportados = 0;    // Número de itens importados
        $numIdInvalido = 0;         // Número de ids inválidos
        $item = 1;

        # Inicia a Importação
        $select = "SELECT matr,dt,obs FROM `fen019` where dt > '1993-01-01 00:00:00' order by dt";

        $conteudo = $uenf->select($select,true);

        echo "<table class='tabelaPadrao'>";
        echo "<tr>";
        echo "<th>#</th>";
        echo "<th>Matrícula</th>";
        echo "<th>IdServidor</th>";
        echo "<th>Data</th>";
        echo "<th>Obs</th>";
        echo "</tr>";

        # Percorre a tabela
        foreach ($conteudo as $campo){

            # Define os valores
            $matricula = $campo[0];
            $dt = substr(datetime_to_php($campo[1]),0,10); 
            $obs = $campo[2];

            # Dados para gravação quando for para tblicença
            $idServidor = $pessoal->get_idServidor($matricula);
            $tabela = 'tbferias';
            $idCampo = 'idFerias';

            # Inicia a tabela que exibe o processo de impotação
            echo "<tr>";
            
            if(is_null($idServidor)){
                echo "<td> - </td>";
            }else{
                echo "<td>".$item."</td>";
            }
            echo "<td>".$matricula."</td>";
            echo "<td>".$idServidor."</td>";
            echo "<td>".$dt."</td>";
            echo "<td>".$obs."</td>";
            echo "<td>";

            # Volta a data ao formato de gravação
            $dt = date_to_bd($dt);

            # Verifica validade do idServidor
            if(is_null($idServidor)){
                $numIdInvalido++;
                label("idInválido. Não é possível importar","alert");
                echo "</td>";
                continue;
            }

            $tipo = 16;
            echo "Férias Importada.";
            $numItensImportados++;

            # Grava na tabela
            $campos = array("idServidor","dtInicial","obs");
            $valor = array($idServidor,$dt,$obs);                    
            $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
            
            echo "</td>";
            echo "</tr>";
            $item++;
            }

        echo "</table>";

        # Exibe o número de itens importado
        echo $numItensImportados." itens importados";br();
        echo $numIdInvalido." ids inválidos";br();
                    
        $painel->fecha();
        break;
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}