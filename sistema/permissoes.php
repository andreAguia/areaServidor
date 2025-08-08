<?php

/**
 * Cadastro de Permissoes
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

    # pega os ids
    $id = soNumeros(get('id'));
    $idUsuarioPesquisado = soNumeros(get('idUsuarioPesquisado'));
    $idServidorPesquisado = $intra->get_idServidor($idUsuarioPesquisado);

    # Pega os parâmetros do historico
    $anoBase = post('anoBase', date('Y'));
    $mesBase = post('mesBase', date('m'));

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Inicia a classe de interface da área do servidor
    $area = new AreaServidor();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    # Nome do Modelo
    $objeto->set_nome('Permissões');

    # Exibe os dados do Servidor
    $objeto->set_rotinaExtra("get_DadosServidor");
    $objeto->set_rotinaExtraParametro($idServidorPesquisado);

    # botão de voltar da lista
    $objeto->set_voltarLista('usuarios.php');
    $objeto->set_voltarForm('?idUsuarioPesquisado=' . $idUsuarioPesquisado);

    # select da lista
    $objeto->set_selectLista("SELECT tbregra.idRegra,
                                     tbregra.nome,
                                     tbregra.descricao,									
                                     tbpermissao.idPermissao
                                FROM tbregra LEFT JOIN tbpermissao on tbregra.idRegra = tbpermissao.idRegra
                               WHERE tbpermissao.idUsuario = {$idUsuarioPesquisado}
                            ORDER BY 2, asc");

    # Não exibe o botão editar
    $objeto->set_botaoEditar(false);
    
    # Caminhos
    $objeto->set_linkEditar('?fase=editar&idUsuarioPesquisado=' . $idUsuarioPesquisado);
    $objeto->set_linkExcluir('?fase=excluir&idUsuarioPesquisado=' . $idUsuarioPesquisado);
    $objeto->set_linkGravar('?fase=gravar&idUsuarioPesquisado=' . $idUsuarioPesquisado);
    $objeto->set_linkListar('?fase=listar&idUsuarioPesquisado=' . $idUsuarioPesquisado);

    # Parametros da tabela
    $objeto->set_label(array("Num", "Regra", "Descrição"));
    $objeto->set_width(array(5, 30, 60));
    $objeto->set_align(array("center", "left", "left"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbpermissao');

    # Nome do campo id
    $objeto->set_idCampo('idPermissao');

    # Pega as regras para colocar no combo
    $selectCombo = "SELECT distinct idregra,
                                      tbregra.nome as regra 
                                 FROM areaServidor.tbregra
                     WHERE NOT EXISTS (SELECT idRegra 
                                         FROM areaServidor.tbpermissao
                                        WHERE idUsuario = $idUsuarioPesquisado
                                          AND tbregra.idRegra = tbpermissao.idRegra)
                                     ORDER BY regra";

    # Verifica a quantidade de registros
    $conteudo = $servidor->select($selectCombo, true);
    if (count($conteudo) == 0) {
        # Retira o botão de incluir
        $objeto->set_botaoIncluir(false);

        # Informa o porquê
        $mensagem = "O botão de incluir sumiu! Porque? Esse usuário já possui todas as permissões.<br/>"
                . "Não existem permissões a serem incluídas.";
        $objeto->set_rotinaExtraListar("callout");
        $objeto->set_rotinaExtraListarParametro($mensagem);
    }

    $result2 = $intra->select($selectCombo);

    # Campos para o formulario
    $objeto->set_campos(array(
        array('nome' => 'idRegra',
            'label' => 'Regra:',
            'tipo' => 'combo',
            'array' => $result2,
            'size' => 90,
            'autofocus' => true,
            'title' => 'Regra para esse grupo.',
            'linha' => 1,
            'col' => 6),
        array('nome' => 'idUsuario',
            'label' => 'idUsuario:',
            'padrao' => $idUsuarioPesquisado,
            'size' => 90,
            'linha' => 2,
            'tipo' => 'hidden')));


    # Log
    $objeto->set_idUsuario($idUsuario);
    $objeto->set_idServidorPesquisado($idServidorPesquisado);

    ################################################################
    switch ($fase) {
        case "" :
        case "listar" :
        case "editar" :
            $objeto->$fase();
            break;

        case "excluir" :
        case "gravar" :
            $objeto->$fase($id);
            break;
    }
    $page->terminaPagina();
} else {
    loadPage("login.php");
}	
