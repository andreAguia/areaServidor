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
    $grid = new Grid("center");
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
            $linkBotao2 = new Link("Importa processo da tblicenca","?fase=inicia");
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('I');
            $menu->add_link($linkBotao2,"right");
            $menu->show();
            break;
        
        case "inicia":
            br(4);
            aguarde("Importando da Tabela tblicenca");
            br();    
           
            loadPage('?fase=importa1');
            break;
        
        case "importa1" :
            # Importa os processos de licença premio da tblicenca
            # Conecta ao banco
            $pessoal = new Pessoal();
            
            # select
            $select = 'SELECT processo,
                              idServidor
                         FROM tblicenca
                        WHERE processo IS NOT NULL
                          AND idTpLicenca = 6';
                    
            $conteudo = $pessoal->select($select);
            
            $grid = new Grid("center");
            $grid->abreColuna(8);
            
            # Exibe a tabela
            $tabela = new Tabela();
            $tabela->set_conteudo($conteudo);
            $tabela->set_label(array("Processo","IdServidor"));
            $tabela->set_align(array("center"));
            $tabela->set_numeroOrdem(TRUE);
            $tabela->set_titulo("Processos na tblicenca");
            $tabela->show();
            
            # Passa os valores para tbservidor
            $pessoal->set_tabela("tbservidor");
            $pessoal->set_idCampo("idServidor");
            
            $contador = 0;
            
            foreach ($conteudo as $pp){
                $pessoal->gravar("processoPremio",$pp[0],$pp[1]);
                $contador++;
            }
            
            br();
            echo "$contador registros afetados";
            br(2);
            
            # Continua
            $link = new Button("Continua","?fase=inicia2");
            $link->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();     
            break;
            
        case "inicia2":
            br(4);
            aguarde("Importando da Tabela tbpublicacaoPremio");
            br();    
           
            loadPage('?fase=importa2');
            break;    

        case "importa2" :
            # Importa os processos de licença premio da tbpublicacaoPremio
            # Conecta ao banco
            $pessoal = new Pessoal();
            
            # select
            $select = 'SELECT processo,
                              idServidor
                         FROM tbpublicacaopremio';
                    
            $conteudo = $pessoal->select($select);
            
            $grid = new Grid("center");
            $grid->abreColuna(8);
            
            # Exibe a tabela
            $tabela = new Tabela();
            $tabela->set_conteudo($conteudo);
            $tabela->set_label(array("Processo","IdServidor"));
            $tabela->set_align(array("center"));
            $tabela->set_numeroOrdem(TRUE);
            $tabela->set_titulo("Processos na tbpublicacaoPremio");
            $tabela->show();
            
            # Passa os valores para tbservidor
            $pessoal->set_tabela("tbservidor");
            $pessoal->set_idCampo("idServidor");
            
            $contador = 0;
            
            foreach ($conteudo as $pp){
                $pessoal->gravar("processoPremio",$pp[0],$pp[1]);
                $contador++;
            }
            
            br();
            echo "$contador registros afetados";
            br(2);
            
            # Continua
            $link = new Button("Termina","?");
            $link->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();     
            break;
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}