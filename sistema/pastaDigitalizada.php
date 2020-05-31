<?php

/**
 * Pasta Digitalizadas
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

    # Pega o idServidor Pesquisado da rotina de pasta digitaliozada
    $idServidorPesquisado = get("idServidorPesquisado");

    # Pega os parâmetros
    $parametroNomeMat = retiraAspas(post('parametroNomeMat', get_session('parametroNomeMat')));
    $parametroCargo = post('parametroCargo', get_session('parametroCargo', '*'));
    $parametroCargoComissao = post('parametroCargoComissao', get_session('parametroCargoComissao', '*'));
    $parametroLotacao = post('parametroLotacao', get_session('parametroLotacao', '*'));
    $parametroPerfil = post('parametroPerfil', get_session('parametroPerfil', '*'));
    $parametroSituacao = post('parametroSituacao', get_session('parametroSituacao', 1));

    # Joga os parâmetros par as sessions
    set_session('parametroNomeMat', $parametroNomeMat);
    set_session('parametroCargo', $parametroCargo);
    set_session('parametroCargoComissao', $parametroCargoComissao);
    set_session('parametroLotacao', $parametroLotacao);
    set_session('parametroPerfil', $parametroPerfil);
    set_session('parametroSituacao', $parametroSituacao);

    # Verifica a fase do programa
    $fase = get('fase', 'lista');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho
    AreaServidor::cabecalho();

    $grid1 = new Grid();
    $grid1->abreColuna(12);

    switch ($fase) {
        case "lista" :
            $grid = new Grid();
            $grid->abreColuna(9);

            $menu1 = new MenuBar();

            # Pega o time inicial
            $time_start = microtime(TRUE);

            # Sair da Área do Servidor
            $linkVoltar = new Link("Voltar", "areaServidor.php");
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Voltar para Ãrea do Servidor');
            $menu1->add_link($linkVoltar, "left");
            $menu1->show();

            # Parâmetros
            $form = new Form('?');

            # Nome ou Matrícula
            $controle = new Input('parametroNomeMat', 'texto', 'Nome, Mat. ou Id:', 1);
            $controle->set_size(55);
            $controle->set_title('Nome, matrícula ou ID:');
            $controle->set_valor($parametroNomeMat);
            $controle->set_autofocus(TRUE);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(3);
            $form->add_item($controle);

            # Situação
            $result = $servidor->select('SELECT idsituacao, situacao
                                          FROM tbsituacao                                
                                      ORDER BY 1');
            array_unshift($result, array('*', '-- Todos --'));

            $controle = new Input('parametroSituacao', 'combo', 'Situação:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Situação');
            $controle->set_array($result);
            $controle->set_valor($parametroSituacao);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(2);
            $form->add_item($controle);

            # Cargos
            $result = $servidor->select('SELECT tbcargo.idCargo,
                                           concat(tbtipocargo.cargo," - ",tbarea.area," - ",tbcargo.nome)
                                      FROM tbcargo LEFT JOIN tbtipocargo USING (idTipoCargo)
                                                   LEFT JOIN tbarea USING (idArea)
                                  ORDER BY 2');
            array_unshift($result, array('*', '-- Todos --'));

            $controle = new Input('parametroCargo', 'combo', 'Cargo - Área - Função:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Cargo');
            $controle->set_array($result);
            $controle->set_valor($parametroCargo);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(7);
            $form->add_item($controle);

            # Cargos em Comissão
            $result = $servidor->select('SELECT tbtipocomissao.idTipoComissao,concat(tbtipocomissao.simbolo," - ",tbtipocomissao.descricao)
                                          FROM tbtipocomissao
                                          WHERE ativo
                                      ORDER BY tbtipocomissao.simbolo');
            array_unshift($result, array('*', '-- Todos --'));

            $controle = new Input('parametroCargoComissao', 'combo', 'Cargo em Comissão:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Cargo em Comissão');
            $controle->set_array($result);
            $controle->set_valor($parametroCargoComissao);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(2);
            $controle->set_col(4);
            $form->add_item($controle);

            # Lotação
            $result = $servidor->select('(SELECT idlotacao, concat(IFNULL(tblotacao.DIR,"")," - ",IFNULL(tblotacao.GER,"")," - ",IFNULL(tblotacao.nome,"")) lotacao
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
            $controle->set_linha(2);
            $controle->set_col(5);
            $form->add_item($controle);

            # Perfil
            $result = $servidor->select('SELECT idperfil, nome
                                          FROM tbperfil                                
                                      ORDER BY 1');
            array_unshift($result, array('*', '-- Todos --'));

            $controle = new Input('parametroPerfil', 'combo', 'Perfil:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Perfil');
            $controle->set_array($result);
            $controle->set_valor($parametroPerfil);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(2);
            $controle->set_col(3);
            $form->add_item($controle);

            # submit
            #$controle = new Input('submit','submit');
            #$controle->set_valor('Pesquisar');
            #$controle->set_size(20);
            #$controle->set_accessKey('P');
            #$controle->set_linha(3);
            #$controle->set_col(2);
            #$form->add_item($controle);

            $form->show();

            $grid->fechaColuna();
            $grid->abreColuna(3);

            # Define a pasta
            $pasta = "../../_arquivo/";
            $numPasta = 0;

            # Define o array da tabela
            $result = array();

            # Exibe um quadro com o resumo
            if (file_exists($pasta)) {        // Verifica se a pasta existe
                # Calcula o número de pastas no diretótio de pastas
                $s = scandir($pasta);
                foreach ($s as $k) {
                    if (($k <> ".") AND ($k <> "..")) {
                        $numPasta++;

                        # Divide o nome da pasta
                        $partes = explode('-', $k);

                        # IdFuncional
                        $idFuncionalServ = $partes[0];

                        # IdServidor
                        $idServidorServ = $servidor->get_idServidoridFuncional($idFuncionalServ);

                        if (is_null($idServidorServ)) {
                            $nome = "Servidor Não Encontrado";
                            $cargo = NULL;
                            $lotacao = NULL;
                            $perfil = NULL;
                            $admissao = NULL;
                        } else {
                            # Nome
                            $nome = $servidor->get_nome($idServidorServ);

                            # Cargo
                            $cargo = $servidor->get_cargo($idServidorServ);

                            # Lotação
                            $lotacao = $servidor->get_lotacao($idServidorServ);

                            # Perfil
                            $perfil = $servidor->get_perfil($idServidorServ);

                            # Admissao
                            $admissao = $servidor->get_dtAdmissao($idServidorServ);
                        }

                        $result[] = array($idFuncionalServ, $nome, $cargo, $lotacao, $perfil, $admissao, $idServidorServ);
                    }
                }
            }

            $numServidores = $servidor->get_numServidoresAtivos();
            $total = $numServidores - $numPasta;

            $conteudo = array(array("Quantidade", $numServidores),
                array("Com Pasta Digitalizada", $numPasta),
                array("Falta Digitalizar:", $total));

            $tabela1 = new Tabela();
            $tabela1->set_titulo("Total Geral");
            $tabela1->set_conteudo($conteudo);
            $tabela1->set_label(array("Servidores Ativos", "Quantidade"));
            $tabela1->set_align(array("left", "center"));
            $tabela1->set_totalRegistro(FALSE);
            $tabela1->set_scroll(FALSE);
            $tabela1->show();
            $grid->fechaColuna();
            $grid->fechaGrid();

            $select = 'SELECT tbservidor.idFuncional,
                      tbservidor.matricula,
                      tbpessoa.nome,
                      tbservidor.idServidor,
                      concat(IFNULL(tblotacao.UADM,"")," - ",IFNULL(tblotacao.DIR,"")," - ",IFNULL(tblotacao.GER,"")) lotacao,
                      tbperfil.nome,
                      tbservidor.dtAdmissao,
                      tbsituacao.situacao,
                      tbservidor.idServidor
                 FROM tbservidor LEFT JOIN tbpessoa ON (tbservidor.idPessoa = tbpessoa.idPessoa)
                                      JOIN tbhistlot ON (tbservidor.idServidor = tbhistlot.idServidor)
                                      JOIN tblotacao ON (tbhistlot.lotacao=tblotacao.idLotacao)
                                 LEFT JOIN tbsituacao ON (tbservidor.situacao = tbsituacao.idsituacao)
                                 LEFT JOIN tbperfil ON (tbservidor.idPerfil = tbperfil.idPerfil)
                                 LEFT JOIN tbcargo ON (tbservidor.idCargo = tbcargo.idCargo)
                                 LEFT JOIN tbtipocargo ON (tbcargo.idTipoCargo = tbtipocargo.idTipoCargo)';

            if ($parametroCargoComissao <> "*") {
                $select .= ' LEFT JOIN tbcomissao ON (tbservidor.idServidor = tbcomissao.idServidor)
                             LEFT JOIN tbtipocomissao ON (tbcomissao.idTipoComissao = tbtipocomissao.idTipoComissao)';
            }

            $select .= ' WHERE tbhistlot.data = (select max(data) from tbhistlot where tbhistlot.idServidor = tbservidor.idServidor)';

            # Matrícula, nome ou id
            if ($parametroNomeMat <> "*") {
                if (is_numeric($parametroNomeMat)) {
                    $select .= ' AND ((';
                } else {
                    $select .= ' AND (';
                }

                $select .= 'tbpessoa.nome LIKE "%' . $parametroNomeMat . '%")';

                if (is_numeric($parametroNomeMat)) {
                    $select .= ' OR (tbservidor.matricula LIKE "%' . $parametroNomeMat . '%")
                                 OR (tbservidor.idfuncional LIKE "%' . $parametroNomeMat . '%"))';
                }
            }

            # situação
            if ($parametroSituacao <> "*") {
                $select .= ' AND (tbsituacao.idsituacao = "' . $parametroSituacao . '")';
            }

            # perfil
            if ($parametroPerfil <> "*") {
                $select .= ' AND (tbperfil.idperfil = "' . $parametroPerfil . '")';
            }

            # cargo
            if ($parametroCargo <> "*") {
                $select .= ' AND (tbcargo.idcargo = "' . $parametroCargo . '")';
            }

            # cargo em comissão
            if ($parametroCargoComissao <> "*") {
                $select .= ' AND tbcomissao.dtExo is NULL AND tbtipocomissao.idTipoComissao = "' . $parametroCargoComissao . '"';
            }

            # lotacao
            if ($parametroLotacao <> "*") {
                # Verifica se o que veio é numérico
                if (is_numeric($parametroLotacao)) {
                    $select .= ' AND (tblotacao.idlotacao = "' . $parametroLotacao . '")';
                } else { # senão é uma diretoria genérica
                    $select .= ' AND (tblotacao.DIR = "' . $parametroLotacao . '")';
                }
            }

            # ordenação
            $select .= ' ORDER BY tbpessoa.nome';

            # Dados da Tabela        
            $label = array("IDFuncional", "Matrícula", "Servidor", "Cargo - Função (Comissão)", "Lotação", "Perfil", "Admissão", "Situação", "Pasta");
            $align = array("center", "center", "left", "left", "left");
            $function = array(NULL, "dv", NULL, NULL, NULL, NULL, "date_to_php", NULL, "verificaPasta");
            $classe = array(NULL, NULL, NULL, "pessoal");
            $metodo = array(NULL, NULL, NULL, "get_Cargo");

            # Executa o select juntando o selct e o select de paginacao
            $conteudo = $servidor->select($select);

            # Monta a tabela
            $tabela = new Tabela();

            #$tabela->set_titulo($this->nomeLista);
            $tabela->set_conteudo($conteudo);
            $tabela->set_label($label);
            #$tabela->set_width($width);
            $tabela->set_align($align);
            #$tabela->set_titulo($this->nomeLista);
            $tabela->set_classe($classe);
            $tabela->set_metodo($metodo);
            $tabela->set_funcao($function);
            $tabela->set_totalRegistro(TRUE);
            $tabela->set_idCampo('idServidor');

            if (!is_null($parametroNomeMat)) {
                $tabela->set_textoRessaltado($parametroNomeMat);
            }

            $tabela->show();

            # Pega o time final
            $time_end = microtime(TRUE);

            # Calcula e exibe o tempo
            $time = $time_end - $time_start;
            p(number_format($time, 4, '.', ',') . " segundos", "right", "f10");
            break;
    }
    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}

