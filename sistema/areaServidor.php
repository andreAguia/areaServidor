<?php

/**
 * Área do Servidor
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 3, 9, 10, 11]);

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
    $fase = get('fase', 'menu');

    # Pega os parâmetros do calendário
    $anoCalendario = post('ano', date("Y"));
    $mesCalendario = post('mes', date("m"));

    # Valida os valores
    if ($anoCalendario < 1900 OR $anoCalendario > 2100) {
        $anoCalendario = date("Y");
    }

    if ($mesCalendario < 1) {
        $mesCalendario = 1;
    }

    if ($mesCalendario > 12) {
        $mesCalendario = 12;
    }

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

    # Limpa as sessions usadas servidor geral e contatos
    set_session('parametroNomeMat');
    set_session('parametroNome');
    set_session('parametroCargo');
    set_session('parametroCargoComissao');
    set_session('parametroLotacao');
    set_session('parametroPerfil');
    set_session('parametroSituacao');
    set_session('sessionParametro');

    # Limpa as sessions do sistema de contratos
    set_session('parametroAno');
    set_session('parametroStatus');
    set_session('parametroModalidade');
    set_session('parametroEmpresa');
    set_session('parametroNatureza');
    set_session('parametroObjeto');
    set_session('parametroSetorRequisitante');
    set_session('inclusaoEmpresa');

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
            AreaServidor::menuPrincipal($idUsuario, $mesCalendario, $anoCalendario);
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
            $Objetolog->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

##################################################################

        case "historicoFerias" :
            botaoVoltar('?');

            # Exibe os dados do Servidor            
            Grh::listaDadosServidor($idServidor);

            # Pega os dados
            $select = "SELECT anoExercicio,
                             status,
                             dtInicial,
                             numDias,
                             ADDDATE(dtInicial,numDias-1),
                             idFerias                             
                        FROM tbferias
                       WHERE idServidor = {$idServidor}
                    ORDER BY anoExercicio desc, dtInicial desc";

            $result = $servidor->select($select);

            $tabela = new Tabela();
            $tabela->set_titulo("Histórico de Férias");
            $tabela->set_conteudo($result);
            $tabela->set_label(["Exercicio", "Status", "Data Inicial", "Dias", "Data Final", "P"]);
            $tabela->set_align(["center"]);
            $tabela->set_funcao([null, null, 'date_to_php', null, 'date_to_php']);
            $tabela->set_classe([null, null, null, null, null, 'pessoal']);
            $tabela->set_metodo([null, null, null, null, null, "get_feriasPeriodo"]);
            $tabela->set_width([10, 10, 15, 5, 15, 5]);
            $tabela->set_rowspan(0);
            $tabela->set_grupoCorColuna(0);
            $tabela->show();

            # Grava no log a atividade
            $atividade = 'Visualizou o próprio histórico de férias na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

##################################################################

        case "afastamentoGeral" :
            botaoVoltar('?');

            # Exibe os dados do Servidor            
            Grh::listaDadosServidor($idServidor);

            $afast = new ListaAfastamentosServidor($idServidor);
            $afast->exibeObs(false);
            $afast->exibeTabela();

            # Grava no log a atividade
            $atividade = 'Visualizou o próprio histórico de afastamento na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario, $data, $atividade, null, null, 7);
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
            $tabela->set_label(['Mês', 'Nome', 'Lotação', 'Exercício', 'Inicio', 'Dias', 'Fim', 'Período', 'Status', 'Situação']);
            $tabela->set_align(["center", "left", "left"]);
            $tabela->set_funcao(["get_nomeMes", null, null, null, "date_to_php", null, null, null, null]);
            $tabela->set_classe([null, null, "pessoal", null, null, null, null, "pessoal"]);
            $tabela->set_metodo([null, null, "get_lotacaoSimples", null, null, null, null, "get_feriasPeriodo"]);
            $tabela->set_conteudo($result);
            $tabela->set_rowspan(0);
            $tabela->set_grupoCorColuna(0);
            $tabela->show();

            # Grava no log a atividade
            $atividade = 'Visualizou os servidores em férias do próprio setor na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

##################################################################

        case "mudaData" :

            # Muda a data
            $intra->set_variavel('dataBackupArquivos', date("d/m/Y H:i:s"));
            
            loadPage("?");
            break;

##################################################################
    }

    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}

