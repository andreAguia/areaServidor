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
    $parametroCargo = post('parametroCargo', get_session('parametroCargo', $pessoal->get_idCargo($idServidor)));
    $parametroComissao = post('parametroComissao', get_session('parametroComissao'));
    $parametroLotacao = post('parametroLotacao', get_session('parametroLotacao', $pessoal->get_idLotacao($idServidor)));
    $parametroMes = post('parametroMes', get_session('parametroMes', date('m')));

    # joga os parâmetros para a session
    set_session('parametroCargo', $parametroCargo);
    set_session('parametroComissao', $parametroComissao);
    set_session('parametroLotacao', $parametroLotacao);
    set_session('parametroMes', $parametroMes);

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
        ['Geral', 'Calendário de Pagamento', 'pgto'],
        ['Geral', 'Aniversariantes', 'aniversariantes']
    ];

    # Retira o menu de dados do servidor para quando o usuário for bolsista
    if ($perfilTipo <> "Outros") {
        array_push($array, ['Dados do Servidor', 'Histórico de Férias', 'historicoFerias', 'Exibe o Histórico de Férias do Servidor']);
        array_push($array, ['Dados do Servidor', 'Histórico de Afastamentos', 'afastamentoGeral']);
        array_push($array, ['Dados do Servidor', 'Histórico de Lic.Prêmio', 'historicoPremio']);
    }

    array_push($array, ['Listagem de Servidores', 'por Nome', 'nome']);
    array_push($array, ['Listagem de Servidores', 'em Férias no seu Setor', 'feriasSetor']);
    array_push($array, ['Listagem de Servidores', 'por Cargo em Comissão', 'cargoComissao']);
    array_push($array, ['Listagem de Servidores', 'por Cargo Efetivo', 'servidorCargo']);
    array_push($array, ['Listagem de Servidores', 'por Lotação', 'porLotacao']);
    #array_push($array, ['Dados da Universidade', 'Organograma', 'organograma']);
    # Acesso aos contatos dos servidores
    if (Verifica::acesso($idUsuario, [1, 11])) {
        array_push($array, ['Listagem de Servidores', 'com E-mails e Telefones', 'contatos']);
    }

    # Acesso aos contatos dos servidores com cpf
    if (Verifica::acesso($idUsuario, [1, 17])) {
        array_push($array, ['Listagem de Servidores', 'com CPF e Chefia Imediata', 'comCpf']);
    }

    # Somente Administradores
//    if (Verifica::acesso($idUsuario, 1)) {
//        array_push($array, ['Administração', 'Gestão de Usuários', 'usuarios']);
//        array_push($array, ['Administração', 'Sistema', 'sistema']);
//        array_push($array, ['Administração', 'Estrutura', 'estrutura']);
//        array_push($array, ['Administração', 'Projetos', 'projetos']);
//    }

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

##################################################################
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
            AreaServidor::moduloSispatri();

            $grid2->fechaColuna();
            $grid2->abreColuna(12, 12, 6);

            AreaServidor::moduloSistemasInternos($idUsuario);
            AreaServidor::moduloSistemasExternos($idUsuario);

            $grid1->fechaColuna();
            $grid1->fechaGrid();
            break;

##################################################################
        # Exibe o Menu Inicial
        case "pgto" :
//            $grid2 = new Grid();
//            $grid2->abreColuna(12, 12, 6);
            # Calendário de pgto
            $calend = new CalendarioPgto();
            $calend->exibeCalendario();

//            $grid2->fechaColuna();
//            $grid2->abreColuna(12, 12, 6);
//            
//
//            $grid1->fechaColuna();
//            $grid1->fechaGrid();
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

        case "nome" :

            # Pega os parâmetros
            $parametroNomeMat = retiraAspas(post('parametroNomeMat', get_session('parametroNomeMat')));
            $parametroSituacao = post('parametroSituacao', get_session('parametroSituacao', 1));

            # Joga os parâmetros par as sessions
            set_session('parametroNomeMat', $parametroNomeMat);
            set_session('parametroSituacao', $parametroSituacao);

            # Parâmetros
            $form = new Form('?fase=nome');

            # Nome ou Matrícula
            $controle = new Input('parametroNomeMat', 'texto', 'Nome, Mat. ou Id:', 1);
            $controle->set_size(55);
            $controle->set_title('Nome, matrícula ou ID:');
            $controle->set_valor($parametroNomeMat);
            $controle->set_autofocus(true);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(8);
            $form->add_item($controle);

            # Situação
            $result = $pessoal->select('SELECT idsituacao, situacao
                                          FROM tbsituacao                                
                                      ORDER BY 1');

            $controle = new Input('parametroSituacao', 'combo', 'Situação:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Situação');
            $controle->set_array($result);
            $controle->set_valor($parametroSituacao);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(4);
            $form->add_item($controle);

            $form->show();

            # Lista de Servidores Ativos
            $lista = new ListaServidores2('Servidores');
            if (!is_null($parametroNomeMat)) {
                $lista->set_matNomeId($parametroNomeMat);
                $lista->set_paginacao(false);
                $lista->set_situacao($parametroSituacao);

                # Retira a edição
                $lista->set_permiteEditar(false);
                $lista->set_paginacao(false);

                $lista->showTabela();
            } else {
                tituloTable("Servidores");
                $callout = new Callout();
                $callout->abre();
                br(2);
                p("Informe o Nome, Matrícula ou idFuncional", 'f14', 'center');
                br();
                $callout->fecha();
            }
            break;

##################################################################

        case "contatos" :

            # Permissão de Acesso
            $acesso = Verifica::acesso($idUsuario, [1, 11]);

            if ($acesso) {

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
                WHERE tbhistlot.data = (select max(data) from tbhistlot where tbhistlot.idServidor = tbservidor.idServidor)  
                  AND situacao = 1";

                # Verifica se tem espaços
                if (!empty($parametroNome)) {
                    if (strpos($parametroNome, " ") !== false) {
                        # Separa as palavras
                        $palavras = explode(' ', $parametroNome);

                        # Percorre as palavras
                        foreach ($palavras as $item) {
                            $select .= ' AND (tbpessoa.nome LIKE "%' . $item . '%")';
                        }
                    } else {
                        $select .= " AND tbpessoa.nome LIKE '%{$parametroNome}%'";
                    }
                }

                $select .= " ORDER BY tbpessoa.nome";

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
            }
            break;

##################################################################

        case "comCpf" :

            # Permissão de Acesso
            $acesso = Verifica::acesso($idUsuario, [1, 17]);

            if ($acesso) {

                # Pega os parâmetros
                $parametroNome = post('parametroNome', get_session('parametroNome'));

                # Joga os parâmetros par as sessions
                set_session('parametroNome', $parametroNome);

                # Parâmetros
                $form = new Form('?fase=comCpf');

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
                                  tbservidor.idServidor,
                                  tbservidor.idServidor,
                                  tbservidor.idPessoa
                             FROM tbservidor LEFT JOIN tbpessoa ON (tbservidor.idPessoa = tbpessoa.idPessoa)
                            WHERE situacao = 1";

                # Verifica se tem espaços
                if (!empty($parametroNome)) {
                    if (strpos($parametroNome, " ") !== false) {
                        # Separa as palavras
                        $palavras = explode(' ', $parametroNome);

                        # Percorre as palavras
                        foreach ($palavras as $item) {
                            $select .= ' AND (tbpessoa.nome LIKE "%' . $item . '%")';
                        }
                    } else {
                        $select .= " AND tbpessoa.nome LIKE '%{$parametroNome}%'";
                    }
                }

                $select .= " ORDER BY tbpessoa.nome";

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
                        $tabela->set_label(["ID/Matrícula", "Servidor", "Chefia Imediata", "Contatos", "Cpf"]);
                        $tabela->set_width([10, 25, 25, 25, 10]);
                        $tabela->set_align(["center", "left", "left", "left"]);
                        $tabela->set_classe(["pessoal", "pessoal", 'pessoal', "pessoal", "pessoal", "pessoal"]);
                        $tabela->set_metodo(["get_idFuncionalEMatricula", "get_nomeECargoELotacao", "get_chefiaImediataNomeCargo", "get_contatos", "get_cpf"]);
                        #$tabela->set_funcao([null, null, "nl2br2"]);
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
            }
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

