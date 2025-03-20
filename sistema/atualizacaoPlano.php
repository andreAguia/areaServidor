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
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Verifica a fase do programa
    $fase = get('fase');

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # contador de registros
    $tt = 0;

    #contador de problemas
    $problemas = 0;

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

    # Voltar
    $linkVoltar = new Link("Voltar", "administracao.php?fase=importacao");
    $linkVoltar->set_class('button');
    $linkVoltar->set_title('Voltar');
    $menu->add_link($linkVoltar, "left");

    $menu->show();

    ################################################################

    switch ($fase) {
        case "" :

            titulo("Atualização de Salário");

            br(4);
            aguarde();
            br();

            # Limita a tela
            $grid1 = new Grid("center");
            $grid1->abreColuna(5);
            p("Aguarde...", "center");
            $grid1->fechaColuna();
            $grid1->fechaGrid();

            loadPage('?fase=exibeLista');
            break;

################################################################

        case "exibeLista" :
            # Conecta o Banco de Dados
            $pessoal = new Pessoal;

            $select = "SELECT idServidor,
                              idServidor,
                              idServidor,
                              idServidor
                         FROM tbservidor JOIN tbpessoa USING (idPessoa)
                        WHERE tbservidor.idPerfil = 1
                          AND situacao = 1
                     ORDER BY tbpessoa.nome";

            $dados = $pessoal->select($select);

            # Exibe a tabela
            $tabela = new Tabela();
            $tabela->set_titulo("Atualização de Salário");
            $tabela->set_conteudo($dados);
            $tabela->set_label(["Servidor", "Salário Atual", "Salário Novo", "Lançamento"]);
            $tabela->set_align(["left", "left", "left", "left"]);
            $tabela->set_width([35, 15, 15, 35]);
            $tabela->set_funcao([null, null]);
            $tabela->set_classe(["pessoal", "Progressao", "Progressao", "Progressao"]);
            $tabela->set_metodo(["get_nomeECargoELotacaoEId", "exibeDadosSalarioAtual", "exibeDadosSalarioNovo", "exibeLancamento"]);
            $tabela->show();
            break;
        #########################################################################    
    }
    $grid->fechaColuna();
    $grid->fechaGrid();
    $page->terminaPagina();
}