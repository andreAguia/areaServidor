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

if($acesso)
{    

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

    titulo("Importação do Banco de Dados Antigo da UENF para o Novo");

    # Verifica a fase do programa
    $fase = get('fase');

    switch ($fase)
    {
        case "" :
            br(4);
            mensagemAguarde();
            loadPage('?fase=importa');
            break;

        case"importa" :
            $painel = new Callout();
            $painel->abre();
            
            # Conecta ao banco
            $uenf = new Uenf();
                    
            # Pega a quantidade de registros com os bolsistas
            $select = "select nm from fen001";
            $result = $uenf->select($select);
            $totalRegistros = count($result);            
            echo $totalRegistros." Registros incluindo os bolsistas;";
            br();
            
            # Pega a quantidade de registros sem os bolsistas
            $select = "select nm from fen001 where vinc<>9 order by matr";
            $result = $uenf->select($select);
            $totalRegistros = count($result);
            echo $totalRegistros." Registros sem os bolsistas;";
            br(2);
            
            # Variáveis
            $numItens = 0;            // Número de itens importados
            $numItensDescartados = 0; // Número de itens descartados
            
            # Inicia a Importação
            $select = "SELECT matr,nm,email,sit FROM fen001 WHERE vinc <> 9";
            
            $conteudo = $uenf->select($select,true);
            
            # Percorre o Fen001 e monta o sql
            foreach ($conteudo as $campo){
                # Verifica se matrícula está vazia
                if((empty($campo[0])) OR (empty($campo[1]))){
                    $numItensDescartados++;
                    echo "1 item descartado...";
                    br();
                    echo "------------------------";
                    br();
                }else{
                    # tbpessoa
                    $tbpessoa = 'INSERT INTO tbpessoa (nome,situacao) values ('.$campo[1].')';

                    # regra para a situação
                    switch ($campo[3]){
                        case 9:         // Demitido
                        case 38:        // Aposentadoria Integral Voluntária
                        case 39:        // Aposentadoria Integral por Invalidez
                        case 40:        // Aposentadoria Integral Compulsória
                        case 41:        // Aposentadoria Proporcional Voluntária   
                        case 42:        // Aposentadoria Proporcional por Invalidez
                        case 43:        // Aposentadoria Proporcional Compulsória
                        case 54:        // Falecimento
                        case 55:        // Recisão de Contrato
                        case 57:        // Suspensão de Contrato
                            $campo[3] = 2;
                            break;
                            
                        default :
                            $campo[3] = 1;
                    }
                    
                    # tbservidor                    
                    $tbservidor = 'INSERT INTO tbservidor (matricula,idPessoa,situacao) values ('.$campo[0].',idPessoa,'.$campo[3].')';
                    
                    # tbcontatos
                    if((is_null($campo[2])) OR ($campo[2] == "")){
                            $campo[2] = 'NULL';
                    }
                    $tbcontatos = 'INSERT INTO tbcontatos (idPessoa,tipo,numero) values (idPessoa,E-mail,'.$campo[2].')';
                    
                    echo $tbpessoa;
                    br();
                    echo $tbservidor;
                    br();
                    echo $tbcontatos;
                    br();
                    echo "------------------------";
                    br();
                    $numItens ++;
                }
            }
            # Exibe o número de itens importado
            echo $numItens." registros importados";
            br();
            echo $numItensDescartados." registros descartados";
    
            $painel->fecha();
            break;
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}