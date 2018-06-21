<?php
/**
 * Cadastro de Processos
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,5);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));
    
    # Pega o processo
    $idProcessoGet = get("idProcesso");
    $idProcessoSession = get_session("idProcesso");
    
    if(is_null($idProcessoGet)){
        $idProcesso = $idProcessoSession;
    }else{
        $idProcesso = $idProcessoGet;
        set_session("idProcesso",$idProcesso);
    }

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################
    
    # Exibe os dados do Processo
    $tt = array($idProcesso,$idUsuario);
    $objeto->set_rotinaExtraListar("get_dadosProcesso");
    $objeto->set_rotinaExtraListarParametro($tt); 

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Movimentos');

    # botão de voltar da lista
    $objeto->set_voltarLista('processo.php');

    # select da lista
    $objeto->set_selectLista('SELECT IF(status = 1,"Entrada", "Saída"),
                                     data,
                                     idProcessoMovimento,
                                     motivo
                                FROM tbprocessomovimento
                               WHERE idProcesso = '.$idProcesso.'
                            ORDER BY data desc, 3 desc');	

    # select do edita
    $objeto->set_selectEdita('SELECT data,
                                     status,
                                     setorCombo,
                                     setorTexto,
                                     motivo,
                                     idProcesso
                                FROM tbprocessomovimento
                               WHERE idProcessoMovimento = '.$id);

    # Caminhos
    $objeto->set_linkEditar('?fase=editar&idProcesso='.$idProcesso);
    $objeto->set_linkGravar('?fase=gravar&idProcesso='.$idProcesso);
    $objeto->set_linkListar('?fase=listar&idProcesso='.$idProcesso);
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(array("Status","Data","Origem / Destino","Motivo"));
    $objeto->set_align(array("center","center","center","left"));
    $objeto->set_width(array(10,10,20,45));	
    $objeto->set_funcao(array(NULL,"date_to_php"));
    $objeto->set_classe(array(NULL,NULL,"Processo"));
    $objeto->set_metodo(array(NULL,NULL,"get_MovimentoSetor"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbprocessomovimento');

    # Nome do campo id
    $objeto->set_idCampo('idProcessoMovimento');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);
    
    # Pega os dados da combo setorCombo
    $selectLotacao = 'SELECT idlotacao, 
                             concat(IFNULL(tblotacao.UADM,"")," - ",IFNULL(tblotacao.DIR,"")," - ",IFNULL(tblotacao.GER,"")," - ",IFNULL(tblotacao.nome,"")) as lotacao
                        FROM tblotacao
                       WHERE ativo
                       ORDER BY lotacao';

    $comboSetor = $servidor->select($selectLotacao);
    array_unshift($comboSetor, array(NULL,NULL)); # Adiciona o valor de nulo

    # Campos para o formulario
    $objeto->set_campos(array( 
                        array ( 'linha' => 1,
                                'nome' => 'data',
                                'label' => 'Data:',
                                'tipo' => 'data',
                                'title' => 'A data do movimento',
                                'autofocus' => TRUE,
                                'required' => TRUE,
                                'col' => 4,
                                'size' => 50),
                        array ( 'nome' => 'status',
                                'label' => 'Status:',
                                'tipo' => 'combo',
                                'size' => 20,
                                'array' => array(array(NULL,NULL),array(1,"Entrada"),array(2,"Saída")),
                                'title' => 'Status do Movimento',
                                'required' => TRUE,
                                'col' => 4,
                                'linha' => 1),
                        array ( 'nome' => 'setorCombo',
                                'label' => 'Origem ou destino dentro da UENF:',
                                'tipo' => 'combo',
                                'size' => 20,
                                'title' => 'Setor dentro da UENF',
                                'array' => $comboSetor,
                                'col' => 6,
                                'linha' => 2),
                        array ( 'nome' => 'setorTexto',
                                'label' => 'Origem ou destino fora da UENF:',
                                'tipo' => 'texto',
                                'size' => 200,
                                'title' => 'Setor fora da UENF',
                                'col' => 6,
                                'linha' => 2),
                        array ( 'nome' => 'motivo',
                                'label' => 'Motivo:',
                                'tipo' => 'textarea',
                                'size' => array(90,5),
                                'title' => 'Motivo.',
                                'required' => TRUE,
                                'col' => 12,
                                'linha' => 3),
                        array ( 'nome' => 'idProcesso',
                                'label' => 'Processo:',
                                'tipo' => 'hidden',
                                'size' => 11,
                                'padrao' => $idProcesso,
                                'linha' => 4),
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
            $objeto->$fase($id);		
            break;
        
        case "gravar" :		
            $objeto->gravar($id,"processoMovimentacaoExtra.php");		
            break;		
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("login.php");
}