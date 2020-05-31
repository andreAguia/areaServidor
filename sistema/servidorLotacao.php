<?php

/**
 * Servidores por Lotação
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Verifica se o usuário está logado
$acesso = Verifica::acesso($idUsuario);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $pessoal = new Pessoal();

    # Pega o idServidor desse usuário
    $idServidor = $intra->get_idServidor($idUsuario);

    # Pega a Lotação atual do usuário
    $idLotacao = $pessoal->get_idLotacao($idServidor);

    # Verifica a fase do programa
    $fase = get('fase');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));

    # Pega os parâmetros
    $parametroLotacao = post('parametroLotacao', get_session('servidorLotacao', $idLotacao));

    # Agrupamento do Relatório
    $agrupamentoEscolhido = post('agrupamento', 0);

    # Session do Relatório
    $select = get_session('sessionSelect');
    $titulo = get_session('sessionTitulo');
    $subTitulo = get_session('sessionSubTitulo');

    # Joga os parâmetros par as sessions
    set_session('servidorLotacao', $parametroLotacao);

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    if ($fase <> "relatorio") {
        AreaServidor::cabecalho();
    }

    ################################################################

    switch ($fase) {
        # Lista os Servidores
        case "" :
            br(10);
            aguarde();
            br();
            loadPage('?fase=pesquisar');
            break;

        case "pesquisar" :
            # Cadastro de Servidores 
            $grid = new Grid();
            $grid->abreColuna(12);

            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $linkBotao1 = new Link("Voltar", "areaServidor.php");
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Voltar a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu1->add_link($linkBotao1, "left");

            # Lotação Ativas
            $imagem = new Imagem(PASTA_FIGURAS . 'lista.png', NULL, 15, 15);
            $botaoLot = new Button();
            $botaoLot->set_title("Listagem de lotações ativas");
            $botaoLot->set_imagem($imagem);
            $botaoLot->set_url('../../grh/grhRelatorios/lotacao.php');
            $botaoLot->set_target("_blank");
            $menu1->add_link($botaoLot, "right");

            # Organograma
            $imagem3 = new Imagem(PASTA_FIGURAS . 'organograma2.png', NULL, 15, 15);
            $botaoOrg = new Button();
            $botaoOrg->set_title("Exibe o Organograma da UENF");
            $botaoOrg->set_imagem($imagem3);
            $botaoOrg->set_url('../../grh/_img/organograma.png');
            $botaoOrg->set_target("_blank");
            $menu1->add_link($botaoOrg, "right");

            # Relatórios
            $imagem = new Imagem(PASTA_FIGURAS . 'print.png', NULL, 15, 15);
            $botaoRel = new Button();
            $botaoRel->set_title("Relatório dessa pesquisa");
            $botaoRel->set_imagem($imagem);
            $botaoRel->set_url("?fase=relatorio");
            $botaoRel->set_target("_blank");
            $menu1->add_link($botaoRel, "right");
            $menu1->show();

            # Parâmetros
            $form = new Form('servidorLotacao.php');

            # Lotação
            $result = $pessoal->select('(SELECT idlotacao, concat(IFNULL(tblotacao.DIR,"")," - ",IFNULL(tblotacao.GER,"")," - ",IFNULL(tblotacao.nome,"")) lotacao
                                              FROM tblotacao
                                             WHERE ativo) UNION (SELECT distinct DIR, DIR
                                              FROM tblotacao
                                             WHERE ativo)
                                          ORDER BY 2');

            array_unshift($result, array('*', '-- Todos --'));

            $controle = new Input('parametroLotacao', 'combo', 'Lotação:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Lotação');
            $controle->set_array($result);
            $controle->set_valor($parametroLotacao);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(12);
            $form->add_item($controle);
            $form->show();

            # Lista de Servidores Ativos
            $lista = new ListaServidores('Servidores por Lotação');

            # Somente servidores ativos
            $lista->set_situacao(1);

            if ($parametroLotacao <> "*") {
                $lista->set_lotacao($parametroLotacao);
            }

            # Edição
            $lista->set_permiteEditar(FALSE);

            # Retira o detalhado
            $lista->set_detalhado(FALSE);

            $lista->showTabela();

            $grid->fechaColuna();
            $grid->fechaGrid();

            # Grava no log a atividade
            $atividade = "Visualizou os servidores da lotação: " . $pessoal->get_nomeLotacao($parametroLotacao) . " na área do servidor";
            $data = date("Y-m-d H:i:s");
            $intra->registraLog($idUsuario, $data, $atividade, NULL, NULL, 7);
            break;

        ###############################
        # Cria um relatório com a seleção atual
        case "relatorio" :
            # Lista de Servidores Ativos
            $lista = new ListaServidores('Servidores');

            if ($parametroLotacao <> "*") {
                $lista->set_lotacao($parametroLotacao);
            }

            # Somente servidores ativos
            $lista->set_situacao(1);

            $lista->showRelatorio();
            break;

        ###############################
    }
    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
