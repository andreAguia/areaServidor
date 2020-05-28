<?php

/**
 * Área do Servidor
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Pega o idServidor do usuário logado
    $idServidor = $intra->get_idServidor($idUsuario);
    $idPerfil = $servidor->get_idPerfil($idServidor);

    # Pega o idServidor Pesquisado da rotina de pasta digitaliozada
    $idServidorPesquisado = get("idServidorPesquisado");

    # Verifica a fase do programa
    $fase = get('fase', 'menu'); # Qual a fase
    # Pega os parâmetros
    $parametroAno = post('parametroAno', get_session('parametroAno', date("Y")));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho
    AreaServidor::cabecalho();

    # Limpa as sessions usadas nos sistemas e módulos
    set_session('servidorCargo');
    set_session('feriasAnoExercicio');
    set_session('feriasLotacao');
    set_session('servidorLotacao');
    set_session('servidorCargoComissao');

    # Limpa as sessions usadas servidor geral
    set_session('parametroNomeMat');
    set_session('parametroCargo');
    set_session('parametroCargoComissao');
    set_session('parametroLotacao');
    set_session('parametroPerfil');
    set_session('parametroSituacao');
    set_session('sessionParametro');

    $grid1 = new Grid();
    $grid1->abreColuna(12);

    switch ($fase) {
        # Exibe o Menu Inicial
        case "menu" :
            # Cria um menu
            $menu1 = new MenuBar();

            # Sair da Área do Servidor
            $linkVoltar = new Link("Sair", "login.php");
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Sair do Sistema');
            $linkVoltar->set_confirma('Tem certeza que deseja sair do sistema?');
            $menu1->add_link($linkVoltar, "left");

            # Administração do Sistema
            if (Verifica::acesso($idUsuario, 1)) {   // Somente Administradores
                $linkAdm = new Link("Administração", "administracao.php");
                $linkAdm->set_class('button success');
                $linkAdm->set_title('Administração dos Sistemas');
                $menu1->add_link($linkAdm, "right");
            }

            # Alterar Senha
            $linkSenha = new Link("Alterar Senha", "trocarSenha.php");
            $linkSenha->set_class('button');
            $linkSenha->set_title('Altera a senha do usuário logado');
            $menu1->add_link($linkSenha, "right");

            # Sobre
            $linkSobre = new Link("Sobre", "?fase=sobre");
            $linkSobre->set_class('button');
            $linkSobre->set_title('Exibe informações do Sistema');
            #$menu1->add_link($linkSobre,"right");

            $menu1->show();

            titulo('Área do Servidor');

            # Exibe os dados do Servidor            
            Grh::listaDadosServidor($idServidor);

            #########################################################
            # Exibe o Menu
            AreaServidor::menuPrincipal($idUsuario);
            br();

            #########################################################
            # Exibe o rodapé da página
            AreaServidor::rodape($idUsuario);
            break;

##################################################################

        case "organograma" :
            botaoVoltar('?');
            titulo("Organograma da UENF");
            br();
            $figura = new Imagem(PASTA_FIGURAS_GRH . 'organograma.png', 'Organograma da UENF', '100%', '100%');
            $figura->show();

            # Grava no log a atividade
            $atividade = 'Visualizou o organograma da Uenf na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario, $data, $atividade, NULL, NULL, 7);
            break;

##################################################################

        case "historicoFerias" :
            botaoVoltar('?');

            # Exibe os dados do Servidor            
            Grh::listaDadosServidor($idServidor);

            # Pega os dados
            $select = 'SELECT anoExercicio,
                             status,
                             dtInicial,
                             numDias,
                             idFerias,
                             ADDDATE(dtInicial,numDias-1)
                        FROM tbferias
                       WHERE idServidor = ' . $idServidor . '
                    ORDER BY anoExercicio desc, dtInicial desc';

            $result = $servidor->select($select);

            $tabela = new Tabela();
            $tabela->set_titulo("Histórico de Férias");
            $tabela->set_conteudo($result);
            $tabela->set_label(array("Exercicio", "Status", "Data Inicial", "Dias", "P", "Data Final"));
            $tabela->set_align(array("center"));
            $tabela->set_funcao(array(NULL, NULL, 'date_to_php', NULL, NULL, 'date_to_php'));
            $tabela->set_classe(array(NULL, NULL, NULL, NULL, 'pessoal'));
            $tabela->set_metodo(array(NULL, NULL, NULL, NULL, "get_feriasPeriodo"));
            $tabela->set_rowspan(0);
            $tabela->set_grupoCorColuna(0);
            $tabela->show();

            # Grava no log a atividade
            $atividade = 'Visualizou o próprio histórico de férias na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario, $data, $atividade, NULL, NULL, 7);
            break;

##################################################################

        case "afastamentoGeral" :
            botaoVoltar('?');

            # Exibe os dados do Servidor            
            Grh::listaDadosServidor($idServidor);

            # Formulário de Pesquisa
            $form = new Form('?fase=afastamentoGeral');

            # Cria um array com os anos possíveis
            $anoInicial = 1999;
            $anoAtual = date('Y');
            $anos = arrayPreenche($anoInicial, $anoAtual + 2);

            $controle = new Input('parametroAno', 'combo', 'Ano:', 1);
            $controle->set_size(8);
            $controle->set_title('Filtra por Ano');
            $controle->set_array($anos);
            $controle->set_valor($parametroAno);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(3);
            $form->add_item($controle);

            $form->show();

            $afast = new Afastamento();
            $afast->set_idServidor($idServidor);
            $afast->set_ano($parametroAno);
            $afast->exibeTabela();
            #$afast->exibeTimeline();
            # Grava no log a atividade
            $atividade = 'Visualizou o próprio histórico de afastamento na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario, $data, $atividade, NULL, NULL, 7);
            break;

##################################################################

        case "feriasSetor" :
            botaoVoltar('?');

            # Exibe os dados do Servidor            
            Grh::listaDadosServidor($idServidor);

            # Pega o ano
            $ano = date("Y");

            # Pega a Lotação atual do usuário
            $idLotacao = $servidor->get_idLotacao($idServidor);

            # Conecta com o banco de dados
            $servidor = new Pessoal();

            $select = "SELECT month(tbferias.dtInicial),
                         tbpessoa.nome,
                         tbservidor.idServidor,
                         tbferias.anoExercicio,
                         tbferias.dtInicial,
                         tbferias.numDias,
                         date_format(ADDDATE(tbferias.dtInicial,tbferias.numDias-1),'%d/%m/%Y') as dtf,
                         idFerias,
                         tbferias.status,
                         tbsituacao.situacao
                    FROM tbservidor LEFT JOIN tbpessoa ON (tbservidor.idPessoa = tbpessoa.idPessoa)
                                         JOIN tbhistlot ON (tbservidor.idServidor = tbhistlot.idServidor)
                                         JOIN tblotacao ON (tbhistlot.lotacao=tblotacao.idLotacao)
                                         JOIN tbferias ON (tbservidor.idServidor = tbferias.idServidor)
                                         JOIN tbsituacao ON (tbservidor.situacao = tbsituacao.idSituacao)
                   WHERE tbhistlot.data =(select max(data) from tbhistlot where tbhistlot.idServidor = tbservidor.idServidor)
                     AND YEAR(tbferias.dtInicial) = $ano
                     AND (tblotacao.idlotacao = $idLotacao)
                ORDER BY dtInicial";

            $result = $servidor->select($select);

            $tabela = new Tabela();
            $tabela->set_titulo("Férias dos Servidores da " . $servidor->get_nomeLotacao($idLotacao) . " em $ano");
            $tabela->set_label(array('Mês', 'Nome', 'Lotação', 'Exercício', 'Inicio', 'Dias', 'Fim', 'Período', 'Status', 'Situação'));
            $tabela->set_align(array("center", "left", "left"));
            $tabela->set_funcao(array("get_nomeMes", NULL, NULL, NULL, "date_to_php", NULL, NULL, NULL, NULL));
            $tabela->set_classe(array(NULL, NULL, "pessoal", NULL, NULL, NULL, NULL, "pessoal"));
            $tabela->set_metodo(array(NULL, NULL, "get_lotacaoSimples", NULL, NULL, NULL, NULL, "get_feriasPeriodo"));
            $tabela->set_conteudo($result);
            $tabela->set_rowspan(0);
            $tabela->set_grupoCorColuna(0);
            $tabela->show();

            # Grava no log a atividade
            $atividade = 'Visualizou os servidores em férias do próprio setor na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario, $data, $atividade, NULL, NULL, 7);
            break;

##################################################################
    }

    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}

