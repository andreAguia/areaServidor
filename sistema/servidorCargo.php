<?php

/**
 * Servidores por Cargo
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
    $idCargo = $pessoal->get_idCargo($idServidor);

    # Verifica a fase do programa
    $fase = get('fase');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));

    # Pega os parâmetros
    $parametroCargo = post('parametroCargo', get_session('servidorCargo', $idCargo));

    # Agrupamento do Relatório
    $agrupamentoEscolhido = post('agrupamento', 0);

    # Session do Relatório
    $select = get_session('sessionSelect');
    $titulo = get_session('sessionTitulo');
    $subTitulo = get_session('sessionSubTitulo');

    # Joga os parâmetros par as sessions
    set_session('servidorCargo', $parametroCargo);

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

            # Cargos Atuais
            $imagem = new Imagem(PASTA_FIGURAS . 'lista.png', NULL, 15, 15);
            $botaoLot = new Button();
            $botaoLot->set_title("Listagem de cargos");
            $botaoLot->set_url("../../grh/grhRelatorios/cargoNivel.php");
            $botaoLot->set_imagem($imagem);
            $botaoLot->set_target("_blank");
            $menu1->add_link($botaoLot, "right");

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
            $form = new Form('?');

            # Cargos
            $result1 = $pessoal->select('SELECT tbcargo.idCargo, 
                                                concat(tbtipocargo.cargo," - ",tbarea.area," - ",tbcargo.nome) as cargo
                                          FROM tbcargo LEFT JOIN tbtipocargo USING (idTipoCargo)
                                                       LEFT JOIN tbarea USING (idArea)    
                                  ORDER BY 2');

            # cargos por nivel
            $result2 = $pessoal->select('SELECT cargo,cargo FROM tbtipocargo WHERE cargo <> "Professor Associado" AND cargo <> "Professor Titular" ORDER BY 2');

            # junta os dois
            $result = array_merge($result2, $result1);

            # acrescenta Professor
            array_unshift($result, array('Professor', 'Professores'));

            $controle = new Input('parametroCargo', 'combo', 'Cargo - Área - Função:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Cargo');
            $controle->set_array($result);
            $controle->set_valor($parametroCargo);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(12);
            $form->add_item($controle);
            $form->show();

            # Lista de Servidores Ativos
            $lista = new ListaServidores('Servidores por Cargo');

            # Somente servidores ativos
            $lista->set_situacao(1);

            if ($parametroCargo <> "*") {
                $lista->set_cargo($parametroCargo);
            }

            # Edição
            $lista->set_permiteEditar(FALSE);

            # Retira o detalhado
            $lista->set_detalhado(FALSE);

            $lista->showTabela();

            $grid->fechaColuna();
            $grid->fechaGrid();

            # Grava no log a atividade
            $atividade = "Visualizou os servidores do cargo: " . $pessoal->get_nomeCargo($parametroCargo) . " na área do servidor";
            $data = date("Y-m-d H:i:s");
            $intra->registraLog($idUsuario, $data, $atividade, NULL, NULL, 7);
            break;

        ###############################
        # Cria um relatório com a seleção atual
        case "relatorio" :
            # Lista de Servidores Ativos
            $lista = new ListaServidores('Servidores');

            if ($parametroCargo <> "*") {
                $lista->set_cargo($parametroCargo);
            }

            # Somente servidores ativos
            $lista->set_situacao(1);

            $lista->showRelatorio();
            break;
    }
    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}