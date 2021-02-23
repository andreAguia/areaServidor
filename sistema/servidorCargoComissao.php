<?php

/**
 * Servidores por Cargo em Comissão
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

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

    # Verifica a fase do programa
    $fase = get('fase');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));

    # Pega os parâmetros
    $parametroCargoComissao = post('parametroCargoComissao', get_session('servidorCargoComissao', 13)); // Se não tiver sido escolhido exibe o reitor (13)
    # Agrupamento do Relatório
    $agrupamentoEscolhido = post('agrupamento', 0);

    # Session do Relatório
    $select = get_session('sessionSelect');
    $titulo = get_session('sessionTitulo');
    $subTitulo = get_session('sessionSubTitulo');

    # Joga os parâmetros par as sessions
    set_session('servidorCargoComissao', $parametroCargoComissao);

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

            # Vagas
            $linkBotao1 = new Link("Vagas", "?fase=vagas");
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Voltar a página anterior');
            $menu1->add_link($linkBotao1, "right");

            # Relatórios
            $imagem = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
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
            $result = $pessoal->select('SELECT tbtipocomissao.idTipoComissao,concat(tbtipocomissao.simbolo," - ",tbtipocomissao.descricao)
                                              FROM tbtipocomissao
                                              WHERE ativo
                                          ORDER BY tbtipocomissao.simbolo');

            $controle = new Input('parametroCargoComissao', 'combo', 'Cargo em Comissão:', 1);
            $controle->set_size(30);
            $controle->set_title('Filtra por Cargo');
            $controle->set_array($result);
            $controle->set_valor($parametroCargoComissao);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(6);
            $form->add_item($controle);
            $form->show();

            # select
            $select = 'SELECT DISTINCT tbservidor.idFuncional,
                            tbservidor.matricula,
                            tbpessoa.nome,
                            tbcomissao.dtNom,
                            idComissao,
                            concat(tbtipocomissao.simbolo," - ",tbtipocomissao.descricao)
                       FROM tbservidor LEFT JOIN tbpessoa ON (tbservidor.idPessoa = tbpessoa.idPessoa)
                                       LEFT JOIN tbcomissao ON(tbservidor.idServidor = tbcomissao.idServidor)
                                       LEFT JOIN tbdescricaocomissao USING (idDescricaoComissao)
                                            JOIN tbtipocomissao ON(tbcomissao.idTipoComissao=tbtipocomissao.idTipoComissao)
                       WHERE tbcomissao.dtExo is null AND tbtipocomissao.idTipoComissao = "' . $parametroCargoComissao . '"                
                  ORDER BY tbpessoa.nome';

            $result = $pessoal->select($select);
            $label = array('IdFuncional', 'Matrícula', 'Nome', 'Nomeação', 'Nome do Cargo');
            $align = array("center", "center", "left", "center", "left");
            $function = array(null, "dv", null, "date_to_php", "descricaoComissao");

            # Monta a tabela
            $tabela = new Tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label);
            $tabela->set_titulo("Servidores Ativos nomeados para o cargo");
            $tabela->set_align($align);
            $tabela->set_funcao($function);
            $tabela->show();

            $grid->fechaColuna();
            $grid->fechaGrid();

            # Grava no log a atividade
            $atividade = "Visualizou os servidores do cargo em comissão: " . $pessoal->get_nomeCargoComissao($parametroCargoComissao) . " na área do servidor";
            $data = date("Y-m-d H:i:s");
            $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

        ###############################
        # Cria um relatório com a seleção atual
        case "relatorio" :
            # Lista de Servidores Ativos
            $lista = new ListaServidores('Servidores');

            if ($parametroCargoComissao <> "*") {
                $lista->set_cargoComissao($parametroCargoComissao);
            }

            # Somente servidores ativos
            $lista->set_situacao(1);

            $lista->showRelatorio();
            break;

        ##################################################################

        case "vagas" :
            $grid = new Grid();
            $grid->abreColuna(12);

            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $linkBotao1 = new Link("Voltar", "?");
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Voltar a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu1->add_link($linkBotao1, "left");

            # Relatórios
            $imagem = new Imagem(PASTA_FIGURAS . 'print.png', null, 15, 15);
            $botaoRel = new Button();
            $botaoRel->set_title("Relatório");
            $botaoRel->set_imagem($imagem);
            $botaoRel->set_url('../../grh/grhRelatorios/cargoComissaoAtivos.php');
            $botaoRel->set_target("_blank");
            $menu1->add_link($botaoRel, "right");
            $menu1->show();

            # Pega os dados
            $select = 'SELECT descricao,
                             simbolo,
                             valsal,
                             vagas,
                             idTipoComissao,
                             idTipoComissao
                        FROM tbtipocomissao
                       WHERE ativo
                    ORDER BY simbolo asc';

            $result = $pessoal->select($select);

            $tabela = new Tabela();
            $tabela->set_titulo("Cargos em Comissão");
            $tabela->set_conteudo($result);
            $tabela->set_label(array("Cargo", "Simbolo", "Valor (R$)", "Vagas", "Servidores Nomeados", "Vagas Disponíveis"));
            #$tabela->set_width(array(80,10,10));
            $tabela->set_align(array("left", "center", "center"));
            $tabela->set_funcao(array(null, null, "formataMoeda"));
            $tabela->set_classe(array(null, null, null, null, 'CargoComissao', 'CargoComissao'));
            $tabela->set_metodo(array(null, null, null, null, 'get_numServidoresNomeados', 'get_vagasDisponiveis'));
            $tabela->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;
    }
    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}