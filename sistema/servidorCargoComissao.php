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
$acesso = Verifica::acesso($idUsuario, [1, 3, 9, 10, 11]);

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
    
    # Session do Relatório
    $select = get_session('sessionSelect');
    $titulo = get_session('sessionTitulo');
    $subTitulo = get_session('sessionSubTitulo');

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
            $menu1->show();

            # select
            $select = 'SELECT concat(tbtipocomissao.simbolo," - ",tbtipocomissao.descricao),
                              tbservidor.idFuncional,
                              tbpessoa.nome,
                              tbcomissao.idComissao,
                              tbcomissao.dtNom,
                              tbperfil.nome
                FROM tbservidor LEFT JOIN tbpessoa ON (tbservidor.idPessoa = tbpessoa.idPessoa) 
                                LEFT JOIN tbperfil ON (tbservidor.idPerfil = tbperfil.idPerfil)
                                LEFT JOIN tbcomissao ON(tbservidor.idServidor = tbcomissao.idServidor)
                                LEFT JOIN tbdescricaocomissao USING (idDescricaoComissao)
                                     JOIN tbtipocomissao ON(tbcomissao.idTipoComissao=tbtipocomissao.idTipoComissao)
                WHERE tbservidor.situacao = 1
                  AND tbcomissao.dtExo is null
             ORDER BY tbtipocomissao.idTipoComissao, tbdescricaocomissao.descricao, tbcomissao.dtNom';            

            $result = $pessoal->select($select);
            $label = array('IdFuncional', 'Nome', 'Cargo', 'Descrição', 'Nomeação', 'Perfil');
            $align = array("center", "left", "left", "left");
            $function = array(null, null, null, "descricaoComissao", "date_to_php");

            # Monta a tabela
            $tabela = new Tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label(['Cargo', 'IdFuncional', 'Nome', 'Descrição', 'Nomeação', 'Perfil']);
            $tabela->set_titulo("Servidores Ativos Com Cargo Em Comissão");
            $tabela->set_align(["left", "center", "left", "left"]);
            $tabela->set_funcao([null, null, null, null, "date_to_php"]);
            $tabela->set_classe([null, null, null, "CargoComissao"]);
            $tabela->set_metodo([null, null, null, "get_descricaoCargo"]);
            $tabela->set_rowspan(0);
            $tabela->set_grupoCorColuna(0);
            $tabela->show();

            $grid->fechaColuna();
            $grid->fechaGrid();

            # Grava no log a atividade
            $atividade = "Visualizou os servidores do cargo em comissão na área do servidor";
            $data = date("Y-m-d H:i:s");
            $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);
            break;

        ###############################
        # Cria um relatório com a seleção atual
        case "relatorio" :

            # Select
            $select = 'SELECT distinct tbservidor.idFuncional,
                              tbpessoa.nome,
                              tbcomissao.idComissao,
                              tbcomissao.dtNom,
                              tbperfil.nome,
                              concat(tbtipocomissao.simbolo," - ",tbtipocomissao.descricao)
                FROM tbservidor LEFT JOIN tbpessoa ON (tbservidor.idPessoa = tbpessoa.idPessoa) 
                                LEFT JOIN tbperfil ON (tbservidor.idPerfil = tbperfil.idPerfil)
                                LEFT JOIN tbcomissao ON(tbservidor.idServidor = tbcomissao.idServidor)
                                LEFT JOIN tbdescricaocomissao USING (idDescricaoComissao)
                                     JOIN tbtipocomissao ON(tbcomissao.idTipoComissao=tbtipocomissao.idTipoComissao)
              WHERE tbservidor.situacao = 1
                AND tbcomissao.dtExo is null
           ORDER BY tbtipocomissao.idTipoComissao, tbdescricaocomissao.descricao, tbcomissao.dtNom';

            $result = $pessoal->select($select);

            $relatorio = new Relatorio();
            $relatorio->set_titulo('Relatório de Servidores com Cargos em Comissão');
            $relatorio->set_subtitulo('Agrupados por Cargo - Ordenados pelo Nome');
            $relatorio->set_label(array('IdFuncional', 'Nome', 'Descrição', 'Nomeação', 'Perfil', ''));
            $relatorio->set_funcao(array(null, null, "descricaoComissao", "date_to_php"));
            #$relatorio->set_width(array(10,30,20,0,25,10));
            $relatorio->set_align(array("center", "left", "left", "center", "center"));
            #$relatorio->set_classe(array(null,null,null,null,"Pessoal"));
            #$relatorio->set_metodo(array(null,null,null,null,"get_Lotacao"));
            $relatorio->set_conteudo($result);
            $relatorio->set_numGrupo(5);
            $relatorio->show();
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