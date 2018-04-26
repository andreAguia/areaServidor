<?php
/**
 * Administração
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("../sistema/_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
    $projeto = new Projeto();

    # Verifica a fase do programa
    $fase = get('fase','ínicial');
    $idProjeto = get('idProjeto');
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    # Cabeçalho
    AreaServidor::cabecalho();

    botaoVoltar('../sistema/administracao.php');
    titulo('Gestão de Projetos');
    br();
    
    switch ($fase){
        case "ínicial" :
            
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(3);
            
            $row = $projeto->listaProjetosAtivos();
            $numProjetos = $projeto->numeroProjetosAtivos();
            
            # Inicia o menu
            $menu1 = new Menu();
            $menu1->add_item('titulo','Projetos Ativos');
            
            # Se existir algum projeto percorre
            # os projetos e monta o menu

            if($numProjetos>0){
                # Percorre o array e preenche o $return
                foreach ($row as $valor) {                    
                    $menu1->add_item('link',$valor[1],'?fase=projeto&idProjeto='.$valor[0],$valor[2]);
                }
            }
            
            $menu1->show();
            
            br();
            $menu2 = new Menu();
            $menu2->add_item('link','+ Novo Projeto','?fase=projetoNovo');
            $menu2->show();
            
            $grid->fechaColuna();
            $grid->abreColuna(9);
            tituloTable('Resumo');
            br();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
            
        ###########################################################
            
        case "projeto" :
            
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(3);
            
            $row = $projeto->listaProjetosAtivos();
            $numProjetos = $projeto->numeroProjetosAtivos();
            
            # Inicia o menu
            $menu1 = new Menu();
            $menu1->add_item('titulo','Projetos Ativos');
            
            # Se existir algum projeto percorre
            # os projetos e monta o menu
            if($numProjetos>0){
                # Percorre o array e preenche o $return
                foreach ($row as $valor) {                    
                    $menu1->add_item('link',$valor[1],'?fase=projeto&idProjeto='.$valor[0],$valor[2]);
                }
            }
            
            $menu1->show();
            
            br();
            $menu2 = new Menu();
            $menu2->add_item('link','+ Novo Projeto','?fase=projetoNovo');
            $menu2->show();
            
            $grid->fechaColuna();
            $grid->abreColuna(6);
            
            # Pega os dados do projeto pesquisado
            $projetoPesquisado = $projeto->listaProjetosAtivos($idProjeto);
            
            # Pega o número de tarefas desse projeto
            $numeroTarefas = $projeto->numeroTarefas($idProjeto);
            
            # Pega as tarefas
            $tarefas = $projeto->listaTarefas($idProjeto);
            
            # Nome do projeto
            p($projetoPesquisado[1],"f20");
            hr();
            
            # Se existir alguma tarefa percorre
            # as tarefas e monta a lista
            if($numeroTarefas>0){
                # Percorre o array e preenche o $return
                foreach ($tarefas as $valor) {                    
                    echo $valor[1];
                }
            }
            
            # Monta a tabela
            $tabela = new Tabela();
            $tabela->set_conteudo($tarefas);
            $tabela->set_label(array("","","","",""));
            $tabela->set_align(array("center"));
            #$tabela->set_titulo($projetoPesquisado[1]);
            #$tabela->show();
            
            $grid->fechaColuna();
            $grid->abreColuna(3);
            
            callout($projetoPesquisado[2],"secondary");
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                 
        ###########################################################         
            
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();  
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}