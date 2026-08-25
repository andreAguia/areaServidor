<?php

/**
 * Cadastro de Log
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, [1, 2]);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();

    # Verifica a fase do programa
    $fase = get('fase');

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # pega o idServidor (se tiver) quando for exibir somente o histórico de um servidor
    $idServidor = soNumeros(get('idServidor', get_session('idServidor')));
    set_session('idServidor', $idServidor);

    # Pega o parametro de pesquisa (se tiver)
    $parametroMes = post('mes', get_session('parametroMes', date("m")));
    $parametroAno = post('ano', get_session('parametroAno', date("Y")));
    $usuarioLog = post('usuarioLog', get_session('usuarioLog'));
    $usuarioIp = post('usuarioIp', get_session('usuarioIp'));
    $idTabela = post('idTabela', get_session('idTabela'));
    $tabela = post('tabela', get_session('tabela'));
    $idServidorPesquisado = post('idServidorPesquisado', get_session('idServidorPesquisado'));

    set_session('parametroMes', $parametroMes);
    set_session('parametroAno', $parametroAno);
    set_session('usuarioLog', $usuarioLog);
    set_session('usuarioIp', $usuarioIp);
    set_session('idTabela', $idTabela);
    set_session('tabela', $tabela);
    set_session('idServidorPesquisado', $idServidorPesquisado);

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    switch ($fase) {
        case "":
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

        case "exibeLista":

            # botão de voltar da lista
            if (is_null($idServidor)) {
                botaoVoltar('areaServidor.php?fase=menuAdmin');
            } else {
                botaoVoltar('../../grh/grhSistema/servidorMenu.php');
            }

            # Formulário de Pesquisa
            $form = new Form('?');

            # Cria array dos meses
            $mes = array(
                array("1", "Janeiro"),
                array("2", "Fevereiro"),
                array("3", "Março"),
                array("4", "Abril"),
                array("5", "Maio"),
                array("6", "Junho"),
                array("7", "Julho"),
                array("8", "Agosto"),
                array("9", "Setembro"),
                array("10", "Outubro"),
                array("11", "Novembro"),
                array("12", "Dezembro"));

            $controle = new Input('mes', 'combo', 'Mês', 1);
            $controle->set_size(10);
            $controle->set_title('Filtra por Mês');
            $controle->set_valor($parametroMes);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_array($mes);
            $controle->set_linha(1);
            $controle->set_col(4);
            $form->add_item($controle);

            # Cria um array com os anos
            $ano = arrayPreenche(2000, date('Y'));

            $controle = new Input('ano', 'combo', 'Ano', 1);
            $controle->set_size(4);
            $controle->set_title('Filtra por Ano');
            $controle->set_valor($parametroAno);
            $controle->set_array($ano);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(4);
            $form->add_item($controle);

            # Pega os Usuarios
            $select = "SELECT DISTINCT tblog.idUsuario,
                                       tbusuario.usuario
                         FROM tblog JOIN tbusuario ON (tblog.idUsuario = tbusuario.idUsuario)
                                    JOIN uenf_grh.tbservidor ON (tbusuario.idServidor = uenf_grh.tbservidor.idServidor)
                                    JOIN uenf_grh.tbpessoa ON (uenf_grh.tbservidor.idPessoa = uenf_grh.tbpessoa.idPessoa)
                        WHERE ";

            if (is_null($idServidor)) {
                $select .= " month(data) = '{$parametroMes}' AND year(data) = '{$parametroAno}'";
            } else {
                $select .= " tblog.idServidor = {$idServidor}";
            }

            $select .= " AND tblog.idUsuario IS NOT null ORDER BY 2";

            $result = $intra->select($select);
            
            array_unshift($result, array(null, '---'));

            $usuariosLogados = $result;

            $controle = new Input('usuarioLog', 'combo', 'Filtra por Usuário', 1);
            $controle->set_size(30);
            $controle->set_title('Servidor');
            $controle->set_array($result);
            $controle->set_valor($usuarioLog);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(4);
            $form->add_item($controle);

            # Pega as tabelas
            $select3 = "SELECT DISTINCT tabela,
                                        tabela
                          FROM tblog
                         WHERE ";

            if (is_null($idServidor)) {
                $select3 .= " month(data) = '{$parametroMes}' AND year(data) = '{$parametroAno}'";
            } else {
                $select3 .= " tblog.idServidor = {$idServidor}";
            }

            $select3 .= " AND tabela IS NOT null ORDER BY 2";

            $result3 = $intra->select($select3);

            array_unshift($result3, array(null, '-- Todos --'));

            $controle = new Input('tabela', 'combo', 'Tabela', 1);
            $controle->set_size(20);
            $controle->set_title('Tabela');
            $controle->set_array($result3);
            $controle->set_valor($tabela);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(2);
            $controle->set_col(4);
            $form->add_item($controle);

            # Pega o id das tabelas
            $select4 = "SELECT DISTINCT idValor,
                                        idValor
                          FROM tblog
                         WHERE ";

            if (is_null($idServidor)) {
                $select4 .= " month(data) = '{$parametroMes}' AND year(data) = '{$parametroAno}'";
            } else {
                $select4 .= " tblog.idServidor = {$idServidor}";
            }

            $select4 .= " AND idValor IS NOT null ORDER BY 2";

            $result4 = $intra->select($select4);

            array_unshift($result4, array(null, '-- Todos --'));

            $controle = new Input('idTabela', 'combo', 'Id', 1);
            $controle->set_size(20);
            $controle->set_title('Id da tabela');
            $controle->set_array($result4);
            $controle->set_valor($idTabela);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(2);
            $controle->set_col(4);
            $form->add_item($controle);

            # Pega o id Servidor
            $result5 = $intra->select("SELECT DISTINCT idServidor,
                                              tbpessoa.nome
                                         FROM tblog JOIN uenf_grh.tbservidor USING (idServidor)
                                                    JOIN uenf_grh.tbpessoa USING (idPessoa)
                                        WHERE month(data) = '{$parametroMes}' 
                                          AND year(data) = '{$parametroAno}'
                                          AND idServidor IS NOT null  
                                     ORDER BY 2");
            array_unshift($result5, array(null, '-- Todos --'));

            $controle = new Input('idServidorPesquisado', 'combo', 'Servidor', 1);
            $controle->set_size(20);
            $controle->set_title('id Servidor');
            $controle->set_array($result5);
            $controle->set_valor($idServidorPesquisado);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(2);
            $controle->set_col(4);

            if (is_null($idServidor)) {
                $form->add_item($controle);
            }

            $form->show();

            ##################################################           
            # select
            $select = "SELECT idUsuario,
                              data,
                              ip,
                              tabela,
                              idValor,
                              idServidor,
                              tipo,
                              atividade,                                      
                              idlog
                         FROM tblog
                        WHERE";

            if (is_null($idServidor)) {
                $select .= " month(data) = '{$parametroMes}' AND year(data) = '{$parametroAno}'";
            } else {
                $select .= " idServidor = {$idServidor}";
            }

            # usuário
            if (!empty($usuarioLog)) {
                $select .= " AND idUsuario = {$usuarioLog}";
            }

            # IP
            if (!empty($usuarioIp)) {
                $select .= " AND ip = '{$usuarioIp}'";
            }

            # idTabela
            if (!empty($idTabela)) {
                $select .= " AND idValor = '{$idTabela}'";
            }

            # Tabela
            if (!empty($tabela)) {
                $select .= " AND tabela = '{$tabela}'";
            }

            # Id Servidor
            if (!empty($idServidorPesquisado)) {
                $select .= " AND idServidor = '{$idServidorPesquisado}'";
            }

            $select .= " ORDER BY data desc";

            # Pega os dados
            $row = $intra->select($select);

            # Monta a tabela
            $tabela = new Tabela();
            $tabela->set_titulo("Histórico do Dia");

            # Informa o dia da semana
            if (is_null($idServidor)) {
                $tabela->set_subtitulo("{$parametroMes} / {$parametroAno}");
            }

            $tabela->set_conteudo($row);
            $tabela->set_label(["Usuário", "Data", "IP", "Tabela", "Id", "Servidor", "Tipo", "Atividade"]);
            $tabela->set_width([8, 8, 8, 10, 5, 15, 5, 36]);
            $tabela->set_align(["center", "center", "center", "center", "center", "left", "center", "left"]);
            $tabela->set_funcao([null, "datetime_to_php"]);
            $tabela->set_classe(["intra", null, null, null, null, "Pessoal"]);
            $tabela->set_metodo(["get_usuario", null, null, null, null, "get_nome"]);

            $tabela->set_formatacaoCondicional(array(
                array('coluna' => 6,
                    'valor' => 0,
                    'operador' => '=',
                    'id' => 'logLogin'),
                array('coluna' => 6,
                    'valor' => 3,
                    'operador' => '=',
                    'id' => 'logExclusao'),
                array('coluna' => 6,
                    'valor' => 6,
                    'operador' => '=',
                    'id' => 'logBackup'),
                array('coluna' => 6,
                    'valor' => 5,
                    'operador' => '=',
                    'id' => 'logLoginIncorreto')
            ));

            # Imagem Condicional
            $imagemLogin = new Imagem(PASTA_FIGURAS . 'login.png', 'Usuário efetuou o login', 20, 20);
            $imagemInclusao = new Imagem(PASTA_FIGURAS . 'logInclusao.png', 'Inclusão de Registro', 20, 20);
            $imagemAlterar = new Imagem(PASTA_FIGURAS . 'logAlterar.png', 'Alteração de Registro', 20, 20);
            $imagemExclusao = new Imagem(PASTA_FIGURAS . 'logExclusao.png', 'Exclusão de Registro', 20, 20);
            $imagemRelatorio = new Imagem(PASTA_FIGURAS . 'logRelatorio.png', 'Visualizou Relatório', 20, 20);
            $imagemLoginIncorreto = new Imagem(PASTA_FIGURAS . 'loginIncorreto.png', 'Login Incorreto', 20, 20);
            $imagemBackup = new Imagem(PASTA_FIGURAS . 'backup2.png', 'Backup', 20, 20);
            $imagemVer = new Imagem(PASTA_FIGURAS_GERAIS . 'olho.png', 'Visualizou', 20, 20);
            $imagemUpload = new Imagem(PASTA_FIGURAS . 'upload.png', 'Fez Upload', 20, 20);

            $tabela->set_imagemCondicional(array(
                array('coluna' => 6,
                    'valor' => 0,
                    'operador' => '=',
                    'imagem' => $imagemLogin),
                array('coluna' => 6,
                    'valor' => 1,
                    'operador' => '=',
                    'imagem' => $imagemInclusao),
                array('coluna' => 6,
                    'valor' => 2,
                    'operador' => '=',
                    'imagem' => $imagemAlterar),
                array('coluna' => 6,
                    'valor' => 3,
                    'operador' => '=',
                    'imagem' => $imagemExclusao),
                array('coluna' => 6,
                    'valor' => 4,
                    'operador' => '=',
                    'imagem' => $imagemRelatorio),
                array('coluna' => 6,
                    'valor' => 5,
                    'operador' => '=',
                    'imagem' => $imagemLoginIncorreto),
                array('coluna' => 6,
                    'valor' => 6,
                    'operador' => '=',
                    'imagem' => $imagemBackup),
                array('coluna' => 6,
                    'valor' => 7,
                    'operador' => '=',
                    'imagem' => $imagemVer),
                array('coluna' => 6,
                    'valor' => 8,
                    'operador' => '=',
                    'imagem' => $imagemUpload)
            ));

            if (empty($usuarioLog)) {
                br(4);
                p("Informe o usuário", "center");
            } else {
                $tabela->show();
            }
    }
    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
