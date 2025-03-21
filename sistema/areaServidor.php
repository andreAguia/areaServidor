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

if (Verifica::acesso($idUsuario, [1, 3, 9, 10, 11])) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $pessoal = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase', 'inicial');

    # Pega o idServidor do usuário logado
    $idServidor = $intra->get_idServidor($idUsuario);
    $idPerfil = $pessoal->get_idPerfil($idServidor);
    $perfilTipo = $pessoal->get_perfilTipo($idPerfil);
    $idCargo = $pessoal->get_idCargo($idServidor);
    $idLotacao = $pessoal->get_idLotacao($idServidor);

    # Pega os parâmetros
    $parametroCargo = post('parametroCargo', get_session('parametroCargo', $idCargo));
    $parametroComissao = post('parametroComissao', get_session('parametroComissao'));
    $parametroLotacao = post('parametroLotacao', get_session('parametroLotacao', $idLotacao));
    $parametroMes = post('parametroMes', get_session('parametroMes', date('m')));

    # joga os parâmetros para a session
    if ($fase == "geral") {
        set_session('parametroCargo');
        set_session('parametroComissao');
        set_session('parametroLotacao');
        set_session('parametroMes');
    } else {
        set_session('parametroCargo', $parametroCargo);
        set_session('parametroComissao', $parametroComissao);
        set_session('parametroLotacao', $parametroLotacao);
        set_session('parametroMes', $parametroMes);
    }

    # Pega o idServidor Pesquisado da rotina de pasta digitaliozada
    $idServidorPesquisado = get("idServidorPesquisado");

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho
    AreaServidor::cabecalho();

    $grid1 = new Grid();
    $grid1->abreColuna(12);

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

    $grid1->fechaColuna();

    #########################################################
    # Exibe o Menu
    $grid1->abreColuna(12, 3);

    # Array do menu
    $array = [
        ['Geral', 'Inicial', 'inicial'],
        ['Geral', 'Aniversariantes', 'aniversariantes']
    ];

    # Retira o menu de dados do servidor para quando o usuário for bolsista
    if ($perfilTipo <> "Outros") {
        array_push($array, ['Dados do Servidor', 'Histórico de Férias', 'historicoFerias', 'Exibe o Histórico de Férias do Servidor']);
        array_push($array, ['Dados do Servidor', 'Histórico de Afastamentos', 'afastamentoGeral']);
        array_push($array, ['Dados do Servidor', 'Histórico de Lic.Prêmio', 'historicoPremio']);
    }

    # Acesso a pesquisa geral
    if (Verifica::acesso($idUsuario, [1, 3])) {
        array_push($array, ['Listagem de Servidores', 'Geral', 'geral']);
    }

    array_push($array, ['Listagem de Servidores', 'em Férias no seu Setor', 'feriasSetor']);
    array_push($array, ['Listagem de Servidores', 'por Cargo em Comissão', 'cargoComissao']);
    array_push($array, ['Listagem de Servidores', 'por Cargo Efetivo', 'servidorCargo']);
    array_push($array, ['Listagem de Servidores', 'por Lotação', 'porLotacao']);
    #array_push($array, ['Dados da Universidade', 'Organograma', 'organograma']);

    # Acesso aos contatos dos servidores
    if (Verifica::acesso($idUsuario, [1, 11])) {
        array_push($array, ['Listagem de Servidores', 'com E-mails e Telefones', 'contatos']);
    }

    # Somente Administradores
    if (Verifica::acesso($idUsuario, 1)) {
        array_push($array, ['Administração', 'Gestão de Usuários', 'usuarios']);
        array_push($array, ['Administração', 'Sistema', 'sistema']);
        array_push($array, ['Administração', 'Estrutura', 'estrutura']);
        array_push($array, ['Administração', 'Projetos', 'projetos']);
    }

    $agrupamento = "";

    # Menu
    titulo("Menu");

    $menu = new Menu();
    #$menu->add_item('titulo', 'Menu');

    foreach ($array as $item) {
        # Verifica se mudou o agrupamento
        if ($agrupamento <> $item[0]) {
            $menu->add_item('titulo1', $item[0]);
            $agrupamento = $item[0];
        }

        if ($fase == $item[2]) {
            $menu->add_item('link', "<b>| {$item[1]} |</b>", "?fase={$item[2]}", isset($item[3]) ? $item[3] : null, null, isset($item[4]) ? $item[4] : null);
        } else {
            $menu->add_item('link', $item[1], "?fase={$item[2]}", isset($item[3]) ? $item[3] : null, null, isset($item[4]) ? $item[4] : null);
        }
        #add_item($tipo = 'link', $label = null, $url = '#', $title = null, $accessKey = null, $target = null)
    }

    $menu->show();
    br();

    $grid1->fechaColuna();
    $grid1->abreColuna(12, 9);

    switch ($fase) {
        # Exibe o Menu Inicial
        case "inicial" :

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

            $grid2 = new Grid();
            $grid2->abreColuna(12, 12, 6);

            AreaServidor::moduloSobre();

            $grid2->fechaColuna();
            $grid2->abreColuna(12, 12, 6);

            AreaServidor::moduloSistemasInternos($idUsuario);
            AreaServidor::moduloSistemasExternos($idUsuario);

            # Calendário de pgto
            $calend = new CalendarioPgto();
            $calend->exibeCalendario();

            $grid1->fechaColuna();
            $grid1->fechaGrid();
            break;

##################################################################

        case "aniversariantes" :            

            # Mês 
            $form = new Form('?fase=aniversariantes');

            $controle = new Input('parametroMes', 'combo', "Mês", 1);
            $controle->set_size(30);
            $controle->set_title('O mês dos aniversários');
            $controle->set_array($mes);
            $controle->set_valor($parametroMes);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_col(3);
            $controle->set_linha(1);
            $form->add_item($controle);

            # Lotação
            $result = $pessoal->select('(SELECT idlotacao, concat(IFnull(tblotacao.DIR,"")," - ",IFnull(tblotacao.GER,"")," - ",IFnull(tblotacao.nome,"")) lotacao
                                          FROM tblotacao
                                         WHERE ativo) UNION (SELECT distinct DIR, DIR
                                          FROM tblotacao
                                         WHERE ativo)
                                      ORDER BY 2');
            array_unshift($result, array(null, '-- Todos --'));

            $controle = new Input('parametroLotacao', 'combo', 'Lotação:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Lotação');
            $controle->set_array($result);
            $controle->set_valor($parametroLotacao);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_col(9);
            $controle->set_linha(1);
            $form->add_item($controle);
            $form->show();

            # Exibe a tabela            
            $select = "SELECT DAY(tbpessoa.dtNasc),
                     tbpessoa.nome,
                     tbservidor.idServidor,
                     tbservidor.idServidor,
                     tbservidor.idServidor
                FROM tbpessoa LEFT JOIN tbservidor ON (tbpessoa.idPessoa = tbservidor.idPessoa)
                                   JOIN tbhistlot ON (tbservidor.idServidor = tbhistlot.idServidor)
                                   JOIN tblotacao ON (tbhistlot.lotacao=tblotacao.idLotacao)
               WHERE tbservidor.situacao = 1
                 AND MONTH(tbpessoa.dtNasc) = {$parametroMes}
                 AND tbhistlot.data = (select max(data) from tbhistlot where tbhistlot.idServidor = tbservidor.idServidor)";

            # lotacao
            if (!empty($parametroLotacao)) {
                # Verifica se o que veio é numérico
                if (is_numeric($parametroLotacao)) {
                    $select .= " AND (tblotacao.idlotacao = '{$parametroLotacao}')";
                } else { # senão é uma diretoria genérica
                    $select .= " AND (tblotacao.DIR = '{$parametroLotacao}')";
                }
            }

            $select .= " ORDER BY month(tbpessoa.dtNasc), day(tbpessoa.dtNasc)";

            $result = $pessoal->select($select);
            $count = $pessoal->count($select);
            $titulo = "Aniversariantes de " . get_nomeMes($parametroMes);

            # Tabela
            $tabela = new Tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label(["Dia", "Nome", "Lotação", "Cargo", "Perfil"]);
            $tabela->set_align(["center", "left", "left", "left"]);
            $tabela->set_classe([null, null, 'Pessoal', 'Pessoal', 'Pessoal']);
            $tabela->set_metodo([null, null, 'get_lotacao', 'get_cargo', 'get_perfilSimples']);
            $tabela->set_titulo($titulo);
            if (intval(date("m")) == intval($parametroMes)) {
                $tabela->set_formatacaoCondicional(
                        array(
                            array(
                                'coluna' => 0,
                                'valor' => date("d"),
                                'operador' => '=',
                                'id' => 'aniversariante'
                )));
            }
            $tabela->set_rowspan(0);
            $tabela->set_grupoCorColuna(0);
            $tabela->show();
            break;

##################################################################

        case "cargoComissao" :
            # Formulário
            $form = new Form('?fase=cargoComissao');

            # Cargos
            $result = $pessoal->select('SELECT idTipoComissao,
                                               concat(simbolo," - ",descricao) 
                                          FROM tbtipocomissao 
                                          WHERE ativo
                                      ORDER BY 2');

            array_unshift($result, array(null, 'Todos'));

            $controle = new Input('parametroComissao', 'combo', 'Cargo - Área - Função:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Cargo');
            $controle->set_array($result);
            $controle->set_valor($parametroComissao);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_autofocus(true);
            $controle->set_linha(1);
            $controle->set_col(6);
            $form->add_item($controle);
            $form->show();

            # select
            $select = "SELECT concat(tbtipocomissao.simbolo,' - ',tbtipocomissao.descricao),
                              tbservidor.idservidor,
                              tbcomissao.dtNom,
                              tbperfil.nome
                FROM tbservidor LEFT JOIN tbpessoa ON (tbservidor.idPessoa = tbpessoa.idPessoa) 
                                LEFT JOIN tbperfil ON (tbservidor.idPerfil = tbperfil.idPerfil)
                                LEFT JOIN tbcomissao ON(tbservidor.idServidor = tbcomissao.idServidor)
                                LEFT JOIN tbdescricaocomissao USING (idDescricaoComissao)
                                     JOIN tbtipocomissao ON(tbcomissao.idTipoComissao=tbtipocomissao.idTipoComissao)
                WHERE tbservidor.situacao = 1
                  AND tbcomissao.dtExo is null";

            if (!empty($parametroComissao)) {
                $select .= " AND tbtipocomissao.idTipoComissao = {$parametroComissao}";
            }

            $select .= " ORDER BY tbtipocomissao.idTipoComissao, tbpessoa.nome";

            $result = $pessoal->select($select);

            # Monta a tabela
            $tabela = new Tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label(['Cargo', 'Servidor', 'Nomeação', 'Perfil']);
            $tabela->set_titulo("Servidores Ativos Com Cargo Em Comissão");
            $tabela->set_align(["left", "left"]);
            $tabela->set_funcao([null, null, "date_to_php"]);
            $tabela->set_classe([null, "pessoal"]);
            $tabela->set_metodo([null, "get_nomeEDescricaoCargo"]);
            $tabela->set_rowspan(0);
            $tabela->set_grupoCorColuna(0);
            $tabela->show();

            # Grava no log a atividade
            $atividade = "Visualizou os servidores do cargo em comissão na área do servidor";
            $data = date("Y-m-d H:i:s");
            $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

##################################################################

        case "servidorCargo" :
            $form = new Form('?fase=servidorCargo');

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
            $controle->set_autofocus(true);
            $controle->set_linha(1);
            $controle->set_col(12);
            $form->add_item($controle);
            $form->show();

            # Lista de Servidores Ativos
            $lista = new ListaServidores2('Servidores por Cargo');

            # Somente servidores ativos
            $lista->set_situacao(1);

            if ($parametroCargo <> "*") {
                $lista->set_cargo($parametroCargo);
            }

            # Edição
            $lista->set_permiteEditar(false);

            # Retira o detalhado
            $lista->set_detalhado(false);

            $lista->showTabela();

            # Grava no log a atividade
            $atividade = "Visualizou os servidores do cargo: " . $pessoal->get_nomeCargo($parametroCargo) . " na área do servidor";
            $data = date("Y-m-d H:i:s");
            $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

##################################################################

        case "organograma" :
            titulo("Organograma da UENF");
            br();
            
            loadPage('../../_arquivos/documentos/25.pdf', 'Organograma da UENF', '100%', '100%');
            $figura->show();

            # Grava no log a atividade
            $atividade = 'Visualizou o organograma da Uenf na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

##################################################################

        case "historicoFerias" :

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

            $result = $pessoal->select($select);

            $tabela = new Tabela();
            $tabela->set_titulo("Histórico de Férias");
            $tabela->set_subtitulo($pessoal->get_nome($idServidor));
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

        case "historicoPremio" :

            tituloTable("Histórico de Licença Prêmio", "", $pessoal->get_nome($idServidor));
            br();

            # Inicia a classe de licença
            $licenca = new LicencaPremio();

            # Exibe as publicações de Licença Prêmio
            $licenca->exibePublicacoes($idServidor, true);

            # Exibe as Licenças Fruídas
            $licenca->exibeLicencaPremio($idServidor, false);

            # Grava no log a atividade
            $atividade = 'Visualizou o próprio histórico de férias na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

##################################################################

        case "afastamentoGeral" :

            $afast = new ListaAfastamentosServidor($idServidor, "Histórico de Afastamentos", $pessoal->get_nome($idServidor));
            $afast->exibeObs(false);
            $afast->exibeTabela();

            # Grava no log a atividade
            $atividade = 'Visualizou o próprio histórico de afastamento na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

##################################################################

        case "geral" :
            
            // melhorar

            loadPage("servidorGeral.php");
            break;

##################################################################

        case "contatos" :

            # Pega os parâmetros
            $parametroNome = post('parametroNome', get_session('parametroNome'));

            # Joga os parâmetros par as sessions
            set_session('parametroNome', $parametroNome);

            # Parâmetros
            $form = new Form('?fase=contatos');

            # Nome ou Matrícula
            $controle = new Input('parametroNome', 'texto', 'Nome:', 1);
            $controle->set_size(55);
            $controle->set_title('Nome do servidor:');
            $controle->set_valor($parametroNome);
            $controle->set_autofocus(true);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(6);
            $form->add_item($controle);
            $form->show();

            $select = "SELECT tbservidor.idServidor,
                      tbservidor.idServidor,
                      tblotacao.ramais,
                      tbservidor.idServidor
                 FROM tbservidor LEFT JOIN tbpessoa ON (tbservidor.idPessoa = tbpessoa.idPessoa)
                                 LEFT JOIN tbhistlot ON (tbservidor.idServidor = tbhistlot.idServidor)
                                 LEFT JOIN tblotacao ON (tbhistlot.lotacao=tblotacao.idLotacao)
                WHERE tbpessoa.nome LIKE '%{$parametroNome}%'
                  AND tbhistlot.data = (select max(data) from tbhistlot where tbhistlot.idServidor = tbservidor.idServidor)  
                  AND situacao = 1  
             ORDER BY tbpessoa.nome";

            if (!empty($parametroNome)) {
                # Executa o select 
                $conteudo = $pessoal->select($select);
                $totReg = $pessoal->count($select);

                if ($totReg == 0) {
                    tituloTable("Contatos dos Servidores Ativos");
                    $callout = new Callout();
                    $callout->abre();
                    br(2);
                    p('Nenhum item encontrado !!', 'center');
                    br();
                    $callout->fecha();
                } else {
                    # Monta a tabela
                    $tabela = new Tabela();

                    $tabela->set_titulo("Contatos dos Servidores Ativos");
                    $tabela->set_conteudo($conteudo);
                    $tabela->set_label(["ID/Matrícula", "Servidor", "Ramais", "Contatos"]);
                    $tabela->set_width([10, 25, 40, 25]);
                    $tabela->set_align(["center", "left", "left", "left"]);
                    $tabela->set_classe(["pessoal", "pessoal", null, "pessoal", "pessoal"]);
                    $tabela->set_metodo(["get_idFuncionalEMatricula", "get_nomeECargoELotacao", null, "get_contatos"]);
                    $tabela->set_funcao([null, null, "nl2br2"]);
                    $tabela->set_totalRegistro(true);
                    $tabela->set_textoRessaltado($parametroNome);
                    $tabela->show();
                }
            } else {
                tituloTable("Contatos dos Servidores Ativos");
                $callout = new Callout();
                $callout->abre();
                br(2);
                p('Digite um nome para pesquisar', 'center');
                br();
                $callout->fecha();
            }

            # Grava no log a atividade
            $atividade = "Pesquisou ({$parametroNome}) nos contatos dos servidores na área do servidor";
            $data = date("Y-m-d H:i:s");
            $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

##################################################################

        case "feriasSetor" :

            # Pega o ano
            $ano = date("Y");

            # Pega a Lotação atual do usuário
            $idLotacao = $pessoal->get_idLotacao($idServidor);

            $select = "SELECT month(tbferias.dtInicial),
                         tbservidor.idServidor,
                         tbferias.anoExercicio,
                         tbferias.dtInicial,
                         tbferias.numDias,
                         date_format(ADDDATE(tbferias.dtInicial,tbferias.numDias-1),'%d/%m/%Y') as dtf,
                         idFerias,
                         tbferias.status
                    FROM tbservidor LEFT JOIN tbpessoa ON (tbservidor.idPessoa = tbpessoa.idPessoa)
                                         JOIN tbhistlot ON (tbservidor.idServidor = tbhistlot.idServidor)
                                         JOIN tblotacao ON (tbhistlot.lotacao=tblotacao.idLotacao)
                                         JOIN tbferias ON (tbservidor.idServidor = tbferias.idServidor)
                                         JOIN tbsituacao ON (tbservidor.situacao = tbsituacao.idSituacao)
                   WHERE tbhistlot.data =(select max(data) from tbhistlot where tbhistlot.idServidor = tbservidor.idServidor)
                     AND YEAR(tbferias.dtInicial) = $ano
                     AND (tblotacao.idlotacao = $idLotacao)
                ORDER BY dtInicial";

            $result = $pessoal->select($select);

            $tabela = new Tabela();
            $tabela->set_titulo("Servidores em Férias em {$ano}");
            $tabela->set_subtitulo($pessoal->get_nomeLotacao($idLotacao));
            $tabela->set_label(['Mês', 'Servidor', 'Exercício', 'Inicio', 'Dias', 'Fim', 'Período', 'Status']);
            $tabela->set_align(["center", "left"]);
            $tabela->set_funcao(["get_nomeMes", null, null, "date_to_php"]);
            $tabela->set_classe([null, "pessoal", null, null, null, null, "pessoal"]);
            $tabela->set_metodo([null, "get_nomeECargoELotacao", null, null, null, null, "get_feriasPeriodo"]);
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

        case "porLotacao" :
            $form = new Form('?fase=porLotacao');

            # Lotação
            $result = $pessoal->select('(SELECT idlotacao, concat(IFnull(tblotacao.DIR,"")," - ",IFnull(tblotacao.GER,"")," - ",IFnull(tblotacao.nome,"")) lotacao
                                              FROM tblotacao
                                             WHERE ativo) UNION (SELECT distinct DIR, DIR
                                              FROM tblotacao
                                             WHERE ativo)
                                          ORDER BY 2');

            $controle = new Input('parametroLotacao', 'combo', 'Lotação:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Lotação');
            $controle->set_array($result);
            $controle->set_valor($parametroLotacao);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_autofocus(true);
            $controle->set_col(12);
            $form->add_item($controle);
            $form->show();

            # Lista de Servidores Ativos
            $lista = new ListaServidores2('Servidores por Lotação');

            # Somente servidores ativos
            $lista->set_situacao(1);
            $lista->set_lotacao($parametroLotacao);

            # Edição
            $lista->set_permiteEditar(false);

            # Retira o detalhado
            $lista->set_detalhado(false);

            $lista->showTabela();

            # Grava no log a atividade
            $atividade = "Visualizou os servidores do cargo: " . $pessoal->get_nomeCargo($parametroCargo) . " na área do servidor";
            $data = date("Y-m-d H:i:s");
            $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

##################################################################
#   Administração
##################################################################
        
        case "usuarios" :
            if (Verifica::acesso($idUsuario, 1)) {
                # Título
                titulo('Usuários');
                $tamanhoImage = 64;
                br();

                # Inicia o menu
                $menu = new MenuGrafico(4);

                # Administração de Usuários
                $botao = new BotaoGrafico();
                $botao->set_label('Usuários');
                $botao->set_url('usuarios.php');
                $botao->set_imagem(PASTA_FIGURAS . 'usuarios.png', $tamanhoImage, $tamanhoImage);
                $botao->set_title('Gerencia os Usuários');
                $menu->add_item($botao);

                # Regras
                $botao = new BotaoGrafico();
                $botao->set_label('Regras');
                $botao->set_url('regras.php');
                $botao->set_imagem(PASTA_FIGURAS . 'regras.png', $tamanhoImage, $tamanhoImage);
                $botao->set_title('Cadastro de Regras');
                $menu->add_item($botao);

                # Histórico Geral
                $botao = new BotaoGrafico();
                $botao->set_label('Histórico');
                $botao->set_title('Histórico Geral do Sistema');
                $botao->set_imagem(PASTA_FIGURAS . 'historico.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url('historico.php');
                $menu->add_item($botao);

                # Computadores (IPs)
                $botao = new BotaoGrafico();
                $botao->set_label('Acesso ao Sistema');
                $botao->set_title('Cadastro de computadores com acesso ao sistema');
                $botao->set_imagem(PASTA_FIGURAS . 'computador.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url('computador.php');
                $menu->add_item($botao);

                $menu->show();

                # Grava no log a atividade
                $atividade = "Visualizou a área de Usuários na área do servidor";
                $data = date("Y-m-d H:i:s");
                $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
            } else {
                loadPage("login.php");
            }
            break;

##################################################################

        case "sistema" :

            if (Verifica::acesso($idUsuario, 1)) {
                # Título
                titulo('Sistema');
                $tamanhoImage = 64;
                br();

                # Inicia o menu
                $menu = new MenuGrafico(3);

                # Menu de Documentos
                $botao = new BotaoGrafico();
                $botao->set_label('Menu de Documentos');
                #$botao->set_target('blank');
                $botao->set_title('Menu de Documentos do sistema GRH');
                $botao->set_imagem(PASTA_FIGURAS . 'documentacao.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url("../../grh/grhSistema/cadastroMenuDocumentos.php");
                $menu->add_item($botao);

                # Cadastro de Atualizações
                $botao = new BotaoGrafico();
                $botao->set_label('Atualizações');
                $botao->set_url('atualizacao.php');
                $botao->set_imagem(PASTA_FIGURAS . 'atualizacao.png', $tamanhoImage, $tamanhoImage);
                $botao->set_title('Gerencia o cadastro de atualizações');
                $menu->add_item($botao);

                # Variáveis de Configuração
                $botao = new BotaoGrafico();
                $botao->set_label('Configurações');
                $botao->set_url('configuracao.php');
                $botao->set_imagem(PASTA_FIGURAS . 'configuracao.png', $tamanhoImage, $tamanhoImage);
                $botao->set_title('Edita as Variáveis de&#10;configuração da Intranet');
                $menu->add_item($botao);

                # Cadastro de Mensagens
                $botao = new BotaoGrafico();
                $botao->set_label('Mensagens');
                $botao->set_title('Cadastro de Mensagens');
                $botao->set_imagem(PASTA_FIGURAS . 'mensagem.jpg', $tamanhoImage, $tamanhoImage);
                $botao->set_url('mensagem.php');
                $menu->add_item($botao);

                # Documentação
                $botao = new BotaoGrafico();
                $botao->set_label('Documentação');
                #$botao->set_target('blank');
                $botao->set_title('Documentação do Sistema');
                $botao->set_imagem(PASTA_FIGURAS . 'documentacao.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url('documentacao.php');
                $menu->add_item($botao);

                $menu->show();

                # Grava no log a atividade
                $atividade = "Visualizou a área de Sistema na área do servidor";
                $data = date("Y-m-d H:i:s");
                $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
            } else {
                loadPage("login.php");
            }
            break;

##################################################################

        case "estrutura" :

            if (Verifica::acesso($idUsuario, 1)) {

                # Título
                titulo('Banco de Dados');
                $tamanhoImage = 64;
                br();

                # Inicia o menu
                $menu = new MenuGrafico(3);

                # Backup
                $botao = new BotaoGrafico();
                $botao->set_label('Backup');
                $botao->set_title('Acessa a área de backup');
                $botao->set_imagem(PASTA_FIGURAS . 'backup.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url('?fase=pastaBackup');
                $menu->add_item($botao);

                # Importação
                $botao = new BotaoGrafico();
                $botao->set_label('Importação');
                $botao->set_title('Executa a rotina de importação');
                $botao->set_imagem(PASTA_FIGURAS . 'importacao.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url('?fase=importacao');
                $menu->add_item($botao);

                # PhpMyAdmin
                $botao = new BotaoGrafico();
                $botao->set_label('PhpMyAdmin');
                $botao->set_title('Executa o PhpMyAdmin');
                $botao->set_target('_blank');
                $botao->set_imagem(PASTA_FIGURAS . 'mysql.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url('http://127.0.0.1/phpmyadmin');
                $menu->add_item($botao);

                # Registros órfãos
                $botao = new BotaoGrafico();
                $botao->set_label('Registros Órfãos');
                $botao->set_title('Faz varredura para encontrar registros órfãos');
                $botao->set_imagem(PASTA_FIGURAS . 'regOrf.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url('registroOrfao.php');
                $menu->add_item($botao);

                # Documentação
                $botao = new BotaoGrafico();
                $botao->set_label('Documentação');
                #$botao->set_target('blank');
                $botao->set_title('Documentação do Banco de Dados');
                $botao->set_imagem(PASTA_FIGURAS . 'documentacao.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url('documentaBd.php');
                $menu->add_item($botao);

                $menu->show();

                # Título
                br();
                titulo('Servidores');
                br();

                # Inicia o menu
                $menu = new MenuGrafico(2);

                # Informação do PHP
                $botao = new BotaoGrafico();
                $botao->set_label('Servidor PHP');
                $botao->set_title('Informações sobre&#10;a versão do PHP');
                $botao->set_imagem(PASTA_FIGURAS . 'phpInfo.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url('?fase=servidorPhp');
                $menu->add_item($botao);

                # Informação do Servidor Web
                $botao = new BotaoGrafico();
                $botao->set_label('Servidor Web');
                $botao->set_title('Informações sobre&#10;o servidor web');
                $botao->set_imagem(PASTA_FIGURAS . 'webServer.png', $tamanhoImage, $tamanhoImage);
                $botao->set_url('?fase=servidorWeb');
                $menu->add_item($botao);

                $menu->show();

                # Grava no log a atividade
                $atividade = "Visualizou a área de Banco de Dados na área do servidor";
                $data = date("Y-m-d H:i:s");
                $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
            } else {
                loadPage("login.php");
            }
            break;

##################################################################

        case "projetos" :

            if (Verifica::acesso($idUsuario, 1)) {

                # Título
                titulo('Projetos');

                $grid2 = new Grid();
                $grid2->abreColuna(12, 12, 6);

                br();
                tituloTable('Ativos');
                br();
                $tamanhoImage = 64;

                # Inicia o menu
                $menu = new MenuGrafico(2);

                # Controle de procedimentos
                $botao = new BotaoGrafico();
                $botao->set_label('Procedimentos');
                $botao->set_url('procedimentos.php');
                $botao->set_target("_blank");
                #$botao->set_url('pastaDigitalizada.php');
                $botao->set_imagem(PASTA_FIGURAS . 'procedimentos.png', $tamanhoImage, $tamanhoImage);
                $botao->set_title('Sistema de procedimentos');
                $menu->add_item($botao);

                # Controle de Rotinas 2
                $botao = new BotaoGrafico();
                $botao->set_label('Rotinas');
                $botao->set_url('rotina.php');
                #$botao->set_url('pastaDigitalizada.php');
                $botao->set_imagem(PASTA_FIGURAS . 'rotina.jpg', $tamanhoImage, $tamanhoImage);
                $botao->set_title('Sistema de controle de manuais de procedimentos');
                $menu->add_item($botao);

                $menu->show();

                $grid2->fechaColuna();
                $grid2->abreColuna(12, 12, 6);

                br();
                tituloTable('Arquivados');
                br();

                # Inicia o menu
                $menu = new MenuGrafico(2);

                # Variáveis de Configuração
                $botao = new BotaoGrafico();
                $botao->set_label('Tarefas');
                $botao->set_url('projeto.php');
                $botao->set_imagem(PASTA_FIGURAS . 'atribuicoes.png', $tamanhoImage, $tamanhoImage);
                $botao->set_title('Sistema de gestão de tarefas');
                $botao->set_target("_blank");
                $menu->add_item($botao);

                # Cadastro de Atualizações
                $botao = new BotaoGrafico();
                $botao->set_label('Notas');
                $botao->set_url('projetoNota.php');
                $botao->set_imagem(PASTA_FIGURAS . 'contratos.png', $tamanhoImage, $tamanhoImage);
                $botao->set_title('Sistema de notas dos sistemas');
                $botao->set_target("_blank");
                $menu->add_item($botao);

                $menu->show();

                $grid1->fechaColuna();
                $grid1->fechaGrid();

                # Grava no log a atividade
                $atividade = "Visualizou a área de Projetos na área do servidor";
                $data = date("Y-m-d H:i:s");
                $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
            } else {
                loadPage("login.php");
            }
            break;

##################################################################
    }

    $grid1->fechaColuna();

    # Exibe o rodapé da página
    $grid1->abreColuna(12);
    AreaServidor::rodape($idUsuario);

    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}

