<?php

/**
 * Servidores por Lotação
 *  
 * By Alat
 */
# Reservado para o servidor logado
$idUsuario = null;

# Configuração
include ("_config.php");

# Verifica se o usuário está logado
$acesso = Verifica::acesso($idUsuario, [1, 11]);

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
    $parametroNome = post('parametroNome', get_session('parametroNome'));

    # Joga os parâmetros par as sessions
    set_session('parametroNome', $parametroNome);

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    ################################################################
    #
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
    $menu1->show();

    # Parâmetros
    $form = new Form('?');

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
                      tblotacao.ramais,
                      tbservidor.idServidor,
                      tbservidor.dtAdmissao
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
            $tabela->set_label(["ID/Matrícula", "Servidor", "Lotação", "Ramais", "E-mails", "Telefones"]);
            $tabela->set_width([10, 20, 10, 25, 15, 15]);
            $tabela->set_align(["center", "left", "left", "left", "left"]);
            $tabela->set_classe(["pessoal", "pessoal", "pessoal", null, "pessoal", "pessoal", "pessoal"]);
            $tabela->set_metodo(["get_idFuncionalEMatricula", "get_nomeECargo", "get_lotacao", null, "get_emails", "get_telefones"]);
            $tabela->set_funcao([null, null, null, "nl2br2"]);
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

    $grid->fechaColuna();
    $grid->fechaGrid();

    # Grava no log a atividade
    $atividade = "Pesquisou ({$parametroNome}) nos contatos dos servidores na área do servidor";
    $data = date("Y-m-d H:i:s");
    $intra->registraLog($idUsuario, $data, $atividade, null, null, 7);

    $page->terminaPagina();
} else {
    loadPage("../../areaServidor/sistema/login.php");
}
    