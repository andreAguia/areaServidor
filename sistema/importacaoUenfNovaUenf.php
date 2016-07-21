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
                    
            # Pega a quantidade de registros antes da paginação
            $select = "select nm from fen001";
            $result = $uenf->select($select);
            $totalRegistros = count($result);            
            echo $totalRegistros." Registros incluindo os bolsistas;";
            br();
            
            # Pega a quantidade de registros antes da paginação            
            $select = "select nm from fen001 where vinc<>9";
            $result = $uenf->select($select);
            $totalRegistros = count($result);
            echo $totalRegistros." Registros sem os bolsistas;";
            
            $painel->fecha();
            break;
    }

    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}