<?php
/**
 * Gerencia de Usuários
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $pessoal = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));    
    
    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro')))					# Se o parametro n?o vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    else
    { 
        $parametro = post('parametro');                # Se vier por post, retira as aspas e passa para a variavel parametro
        set_session('sessionParametro',$parametro);    # transfere para a session para poder recuperá-lo depois
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

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Usuários');	

    # botão salvar
    $objeto->set_botaoSalvarGrafico(false);

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

    # controle de pesquisa
    #$objeto->set_parametroLabel('Pesquisar');
    #$objeto->set_parametroValue($parametro);

    # ordenação
    if(is_null($orderCampo))
         $orderCampo = "1";

    if(is_null($orderTipo))
        $orderTipo = 'asc';

    # select da lista
    $objeto->set_selectLista ('SELECT usuario,
                                      idServidor,
                                      ultimoAcesso,
                                      obs,
                                      idUsuario
                                 FROM tbusuario
                                WHERE usuario LIKE "%'.$parametro.'%"
                             ORDER BY '.$orderCampo.' '.$orderTipo);

    # select do edita
    $objeto->set_selectEdita('SELECT usuario,
                                     idServidor,
                                     obs
                                FROM tbusuario
                               WHERE idUsuario = '.$id);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    #$objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');

    # Parametros da tabela
    $objeto->set_label(array("Usuário","Nome","Último Acesso", "Obs","Padrão"));
    $objeto->set_width(array(10,30,10,30,5));
    $objeto->set_align(array("center","left","center","left"));

    $objeto->set_classe(array(null,"pessoal"));
    $objeto->set_metodo(array(null,"get_nome"));
    #$objeto->set_function(array(null,null,null,null,null,null,null,"get_lotacaoNumServidores"));
    
    # Botão da solicitação de férias
    $botao1 = new BotaoGrafico();
    $botao1->set_title('Redefine para senha padrão');
    $botao1->set_label('');
    $botao1->set_url('?fase=senhaPadrao&idSenhaPadrao=');
    #$botao1->set_confirma('Você deseja realmente redefinir esse senha para a senha padrão?');    
    $botao1->set_image(PASTA_FIGURAS.'senha.png',20,20);;
    
    # Coloca o objeto link na tabela			
    $objeto->set_link(array("","","","",$botao1));	

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbusuario');

    # Nome do campo id
    $objeto->set_idCampo('idUsuario');
    
    # Pega os dados da combo nome
    $result = $pessoal->select('SELECT idServidor, 
                                     tbpessoa.nome
                                FROM tbservidor JOIN tbpessoa ON(tbservidor.idPessoa = tbPessoa.idPessoa)
                                WHERE tbservidor.situacao = 1
                            ORDER BY tbpessoa.nome');
    array_push($result, array(0,null)); # Adiciona o valor de nulo

    # Campos para o formulario
    $objeto->set_campos(array(
        array ('linha' => 1,
               'col' => 4, 
               'nome' => 'usuario',
               'label' => 'Usuário:',
               'tipo' => 'texto',
               'autofocus' => true,
               'size' => 15),
        array ('linha' => 1,
               'col' => 6, 
               'nome' => 'idServidor',
               'label' => 'Servidor:',
               'tipo' => 'combo',
               'array' => $result,
               'size' => 20),
        array ('linha' => 2,
               'col' => 12,
               'nome' => 'obs',
               'label' => 'Observação:',
               'tipo' => 'textarea',
               'size' => array(80,5))
        ));

    # Log
    $objeto->set_idUsuario($idUsuario);

    ################################################################
    switch ($fase)
    {
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :
        case "excluir" :	
        case "gravar" :
            $objeto->$fase($id);
            break;
        
        ###################################################################

        case "senhaPadrao" :
            # Pega o usuário que vai alterar senha
            $idSenhaPadrao = get('idSenhaPadrao');
            
            # Troca a senha
            $intra->set_senha($idSenhaPadrao);
            
            # Pega o idServidor desse usuário
            $idServidorSenhaPadrao = $intra->get_idServidor($idSenhaPadrao);

            # Grava no log a atividade
            $log = new Intra();
            $data = date("Y-m-d H:i:s");
            $atividade = 'Passou '.$idServidorSenhaPadrao.' para senha padrão';
            $log->registraLog($idSenhaPadrao,$data,$atividade,'tbservidor',$idServidorSenhaPadrao);

            loadPage('?fase=listar');

            break;

        ###################################################################	
    }									 	 		

    $page->terminaPagina();
}