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

    switch ($fase){
        case "" :
            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar",'administracao.php?fase=importacao');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1,"left");

            # Importar
            $linkBotao2 = new Link("Importar","?fase=inicia");
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('I');
            $menu->add_link($linkBotao2,"right");
            $menu->show();
            break;
        
        case "inicia":
            br(4);
            aguarde();
            br();    
           
            loadPage('?fase=importa');
            break;

        case "importa" :
            # Conecta ao banco
            $pessoal = new Pessoal();
            
            # select
            $select = 'SELECT processo,
                              idServidor
                         FROM tbpublicacaopremio';
                    
            $conteudo = $pessoal->select($select);
            
            $pessoal->set_tabela("tbservidor");
            $pessoal->set_idCampo("idServidor");
            
            foreach ($conteudo as $pp){
                $pessoal->gravar("processoPremio",$pp[0],$pp[1]);
            }
           
            loadPage('?fase=acabou');
            break;
        
         case "acabou" :
             botaoVoltar("?");
             echo "acabou";
             break;
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}