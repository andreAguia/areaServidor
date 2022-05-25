<?php

/**
 * Cadastro de Processos
 *  
 * By Alat
 */
## Servidor logado 
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
    if (is_null(post('parametro'))) {                                     # Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro')); # passa o parametro da session para a variavel parametro retirando as aspas
    } else {
        $parametro = post('parametro');        # Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro', $parametro);      # transfere para a session para poder recuperá-lo depois
    }

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo
    $objeto->set_nome('Sistema de Controle de Processos');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar');
    $objeto->set_parametroValue($parametro);

    # select da lista
    $objeto->set_selectLista('SELECT data,
                                     numero,
                                     assunto,
                                     idProcesso
                                FROM tbprocesso
                               WHERE data LIKE "%' . $parametro . '%"
                                  OR numero LIKE "%' . $parametro . '%"
                                  OR assunto LIKE "%' . $parametro . '%"
                            ORDER BY 1 desc');

    # select do edita
    $objeto->set_selectEdita('SELECT data,
                                     numero,
                                     assunto						    
                                FROM tbprocesso
                               WHERE idProcesso = ' . $id);

    # botões
    $objeto->set_botaoEditar(false);  # Não exibe o botão editar
    $objeto->set_botaoExcluir(false); # Não exibe o botão excluir
    $objeto->set_botaoVoltarLista(false); # Não exibe o botão voltar
    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(array("Data", "Processo", "Assunto", "Mov."));
    $objeto->set_width(array(10, 25, 60));
    $objeto->set_align(array("center", "center", "left"));
    $objeto->set_funcao(array("date_to_php", null, "retiraAcento"));

    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new Link(null, 'processoMovimentacao.php?idProcesso=', 'Movimentação do Processo');
    $botao->set_imagem(PASTA_FIGURAS . 'movimentacao.png', 20, 20);
    $objeto->set_link(array(null, null, null, $botao));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbprocesso');

    # Nome do campo id
    $objeto->set_idCampo('idProcesso');

    # Campos para o formulario
    $objeto->set_campos(array(
        array('nome' => 'numero',
            'label' => 'Processo:',
            'tipo' => 'processo',
            'size' => 25,
            'autofocus' => true,
            'required' => true,
            'title' => 'O número do Processo.',
            'linha' => 1,
            'col' => 6),
        array('nome' => 'data',
            'label' => 'Data:',
            'tipo' => 'data',
            'size' => 12,
            'required' => true,
            'title' => 'Data de Nascimento.',
            'col' => 6,
            'linha' => 1),
        array('nome' => 'assunto',
            'label' => 'Assunto:',
            'tipo' => 'textarea',
            'required' => true,
            'size' => array(90, 5),
            'title' => 'Assunto do processo.',
            'col' => 12,
            'linha' => 2)
    ));


    # Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :
        case "excluir" :
        case "gravar" :
            $objeto->$fase($id);
            break;
    }
    $page->terminaPagina();
} else {
    loadPage("login.php");
}	
