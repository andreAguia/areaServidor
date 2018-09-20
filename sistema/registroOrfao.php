<?php
/**
 * Administração
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase'); # Qual a fase

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
    # Limita o tamanho da tela
    $grid1 = new Grid();
    $grid1->abreColuna(12);
    
    switch ($fase)
    {	
        # Exibe o Menu Inicial
        case "" :
            
            br(6);
            aguarde();
            
            loadPage('?fase=fazendo');
            break;
        
        case "fazendo" :
            # Botão voltar
            $linkBotao1 = new Link("Voltar",'administracao.php');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');

            # Refazer
            $linkBotao2 = new Link("Refazer","?");
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refaz a procura por registro órfãos');
            $linkBotao2->set_accessKey('R');

            # Cria um menu
            $menu = new MenuBar();
            $menu->add_link($linkBotao1,"left");
            $menu->add_link($linkBotao2,"right");
            $menu->show();
            
            titulo('Descobrindo Registros Órfãos');
            br();
            
            ### tbpessoa
            tituloTable('Tabela tbpessoa');
            br();
            
            $grid2 = new Grid();
            $tabelasPessoa = ["tbdependente","tbdocumentacao","tbformacao"];
            foreach ($tabelasPessoa as $tt) {
                $grid2->abreColuna(4);
                tituloTable($tt);
                $select = "SELECT * FROM $tt WHERE idPessoa NOT IN (SELECT idPessoa FROM tbpessoa)";
                $row = $servidor->select($select,FALSE);
                if(count($row[0]) == 0){
                    br(2);
                    p("Nenhum registro órfão encontrado !","center");
                    br(2);
                }else{
                    print_r($row);
                }
                $grid2->fechaColuna();
                br();
            }
            $grid2->fechaGrid();
            hr();
            
            ### tbservidor
            tituloTable('Tabela tbservidor');
            br();
            
            $grid2 = new Grid();
            $tabelasPessoa = ["tbatestado","tbaverbacao","tbcedido","tbcomissao","tbdiaria","tbelogio","tbferias","tbfolga","tbgratificacao","tbhistcessao","tbhistlot","tblicenca","tbprogressao","tbpublicacaopremio","tbtrabalhotre","tbtrienio"];
            foreach ($tabelasPessoa as $tt) {
                $grid2->abreColuna(4);
                tituloTable($tt);
                $select = "SELECT * FROM $tt WHERE idServidor NOT IN (SELECT idServidor FROM tbservidor)";
                $row = $servidor->select($select,FALSE);
                if(count($row[0]) == 0){
                    br(2);
                    p("Nenhum registro órfão encontrado !","center");
                    br(2);
                }else{
                    print_r($row);
                }
                $grid2->fechaColuna();
                br();
            }
            $grid2->fechaGrid();
            break;
    }
    $grid1->fechaColuna();
    $grid1->fechaGrid();    
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}