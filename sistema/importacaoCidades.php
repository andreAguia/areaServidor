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
        $pessoal = new Pessoal();

        # Pega a quantidade de registros com os bolsistas
        $select = "select matr,  FROM `fen019` where dt > '1993-01-01 00:00:00' order by dt";
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
        $select = "SELECT nome,
                          endereco,
                          complemento,
                          bairro,
                          cidade,
                          tbcidade.nome,
                          uf 
                    FROM tbpessoa LEF JOIN tbcidade USING (idCidade)
                ORDER BY cidade";
        $conteudo = $pessoal->select($select,TRUE);

        echo "<table class='tabelaPadrao'>";
        echo "<tr>";
        echo "<th>#</th>";
        echo "<th>nome</th>";
        echo "<th>Endereço</th>";
        echo "<th>Complemento</th>";
        echo "<th>Bairro</th>";
        echo "<th>Cidade</th>";
        echo "<th>IdCidade</th>";
        echo "<th>UF</th>";
        echo "</tr>";
        
        # Contador
        $item = 1;

        # Percorre a tabela
        foreach ($conteudo as $campo){

            # Ve qual é a cidade
            $cidade = $campo[4];
            
            # Percorre a tabela de cidades e verifica cida 
            
            # Inicia a tabela que exibe o processo de impotação
            echo "<tr>";
            
            echo "<td>".$item."</td>";
            echo "<td>".$campo[0]."</td>";
            echo "<td>".$campo[1]."</td>";
            echo "<td>".$campo[2]."</td>";
            echo "<td>".$campo[3]."</td>";
            echo "<td>".$campo[4]."</td>";
            echo "<td>".$campo[5]."</td>";
            echo "<td>".$campo[6]."</td>";
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