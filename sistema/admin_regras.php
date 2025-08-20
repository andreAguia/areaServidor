<?php

/**
 * Cadastro de regras
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase', 'listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));
    
    # Pega o parametro de pesquisa (se tiver)
    $parametro = retiraAspas(post('parametro', get_session('sessionParametro')));
    set_session('sessionParametro', $parametro);

    # pega o id do computador para quando for emitir ficha de OS
    $computador = get('regra');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Inicia a classe de interface da área do servidor
    $area = new AreaServidor();

    # Cabeçalho da Página
    if ($fase <> 'servidoresPermissao'){
        AreaServidor::cabecalho();
        br();
    }

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Permissões');

    # botão de voltar da lista
    $objeto->set_voltarLista('areaServidor.php?fase=menuAdmin');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar');
    $objeto->set_parametroValue($parametro);

    # Parametros da tabela
    $objeto->set_label(["Num", "Nome", "Descrição", "Servidores", "Ver"]);
    $objeto->set_width([5, 25, 45, 10, 5]);
    $objeto->set_align(["center", "left", "left"]);

    # select da lista
    $objeto->set_selectLista('SELECT idRegra,
                                     nome,
                                     descricao,
                                     idRegra,
                                     idRegra
                                FROM tbregra
                               WHERE nome LIKE "%' . $parametro . '%"
                                  OR descricao LIKE "%' . $parametro . '%"
                            ORDER BY idRegra');

    $objeto->set_botaoExcluir(true);
    $objeto->set_botaoIncluir(true);

    # select do edita
    $objeto->set_selectEdita('SELECT nome,
                                     descricao
                                FROM tbregra
                               WHERE idRegra = ' . $id);

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');

    $objeto->set_classe([null, null, null, "Intra"]);
    $objeto->set_metodo([null, null, null, "get_numeroUsuariosPermissao"]);

    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new BotaoGrafico();
    $botao->set_label('');
    $botao->set_title('Servidores com permissão a essa regra');
    $botao->set_onClick("abreDivId('divPermissao'); ajaxLoadPage('?fase=servidoresPermissao&id=','divPermissao',");
    $botao->set_imagem(PASTA_FIGURAS . 'olho.png', 20, 20);

    # Coloca o objeto link na tabela			
    $objeto->set_link(array("", "", "", "", $botao));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbregra');

    # Nome do campo id
    $objeto->set_idCampo('idRegra');
    
    # Campos para o formulario
    $campos = array(array('nome' => 'nome',
            'label' => 'Nome:',
            'tipo' => 'texto',
            'size' => 90,
            'maxlength' => 45,
            'required' => true,
            'title' => 'Nome da Regra.',
            'col' => 12,
            'linha' => 1),
        array('nome' => 'descricao',
            'label' => 'Descrição:',
            'tipo' => 'textarea',
            'size' => array(90, 5),
            'required' => true,
            'title' => 'Descrição resumida da regra.',
            'col' => 12,
            'linha' => 2));

    $objeto->set_campos($campos);

    # Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :
            $objeto->listar();

            # Div de servidores com essa permissão
            $divPermissao = new Div('divPermissao');
            $divPermissao->abre();

            $divPermissao->fecha();
            break;

        case "editar" :
        case "excluir" :
        case "gravar" :
            $objeto->$fase($id);
            break;

        ##################################################################

        case "servidoresPermissao":
            # Informa os servidores com essa permissão
            $callout = new Callout();
            $callout->set_botaoFechar(true);
            $callout->abre();
            br();
            titulo('Permissões');

            # Monta a tabela        
            $select = 'SELECT tbusuario.usuario,
                                  tbusuario.idServidor,
                                  tbusuario.idServidor,
                                  tbusuario.idServidor,
                                  idPermissao
                             FROM tbpermissao JOIN tbusuario USING (idUsuario)
                            WHERE tbpermissao.idRegra = ' . $id . '
                         ORDER BY tbusuario.usuario';
            $result = $intra->select($select, true);
            $tabela = new Tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label(array("Usuário", "Nome", "Cargo", "Lotação"));
            #$tabela->set_width(array(10,30,30,20));
            $tabela->set_align(array("center", "left", "left"));

            #$tabela->set_funcao(array('dv'));
            $tabela->set_classe(array(null, "Pessoal", "Pessoal", "Pessoal"));
            $tabela->set_metodo(array(null, 'get_nome', 'get_cargo', 'get_lotacao'));

            $tabela->set_excluir('?fase=excluirPermissao');
            $tabela->set_idCampo('idPermissao');

            if (count($result) == 0) {
                p('<br/><br/>Não há Servidores.<br/><br/>', 'center');
            } else {
                $tabela->show();
            }

            $callout->fecha();
            break;

        ##################################################################	

        case "excluirPermissao" :
            # Pega os dados caso seja tbpermissao
            $intra = new Intra();
            $pessoal = new Pessoal();
            $permissao = $intra->get_permissao($id);
            $atividade = 'Excluiu a permissao de: ' . $permissao[1] . ' da matrícula ' . $permissao[0] . ' (' . $pessoal->get_nome($permissao[0]) . ')';

            # Conecta com o banco de dados
            $objeto = new Intra();
            $objeto->set_tabela('tbpermissao'); # a tabela
            $objeto->set_idCampo('idPermissao'); # o nome do campo id
            $objeto->excluir($id);   # executa a exclusão
            # Grava no log a atividade
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($matricula, $data, $atividade, 'tbpermissao', $id);

            loadPage('?');
            break;

        ##################################################################
    }

    $page->terminaPagina();
} else {
    loadPage("login.php");
}