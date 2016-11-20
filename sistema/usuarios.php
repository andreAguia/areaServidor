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
    
    # Define a senha padrão de acordo com o que está nas variáveis
    define("SENHA_PADRAO",$intra->get_variavel('senhaPadrao'));
	
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
    $objeto->set_selectLista ('(SELECT idUsuario,
                                      idUsuario,
                                      usuario,
                                      if(tipoUsuario = 1,"S","B"),
                                      idServidor,
                                      ultimoAcesso,
                                      idServidor,
                                      idServidor,
                                      idUsuario,
                                      idUsuario,
                                      idUsuario,
                                      idUsuario
                                 FROM tbusuario
                                WHERE tipoUsuario = 1 AND usuario LIKE "%'.$parametro.'%")
                                UNION
                             (SELECT idUsuario,
                                     idUsuario,
                                      usuario,
                                      if(tipoUsuario = 1,"S","B"),
                                      nome,
                                      ultimoAcesso,
                                      idServidor,
                                      idServidor,
                                      idUsuario,
                                      idUsuario,
                                      idUsuario,
                                      idUsuario
                                 FROM tbusuario
                                WHERE tipoUsuario = 2 AND usuario LIKE "%'.$parametro.'%")
                             ORDER BY '.$orderCampo.' '.$orderTipo);

    # select do edita
    $objeto->set_selectEdita('SELECT usuario,
                                     tipoUsuario,
                                     idServidor,
                                     nome,
                                     obs
                                FROM tbusuario
                               WHERE idUsuario = '.$id);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');

    # Parametros da tabela
    $objeto->set_label(array("Status","Id","Usuário","Tipo","Nome","Último Acesso", "Lotação","Cargo","Padrão","Bloquear","Perm."));
    #$objeto->set_width(array(5,4,10,5,15,10,15,11,5,5,5));
    $objeto->set_align(array("center","center","center","center","left","center","center","left"));

    $objeto->set_classe(array(null,null,null,null,"pessoal",null,"pessoal","pessoal"));
    $objeto->set_metodo(array(null,null,null,null,"get_nome",null,"get_lotacao","get_cargo"));
    $objeto->set_function(array("statusUsuario",null,null,"badgeTipoUsuario",null,"datetime_to_php"));
    
    # Imagem Condicional 
    $imageSenhaPadrao = new Imagem(PASTA_FIGURAS.'exclamation.png','Usuário com senha padrão.');
    $imageAcessoBloqueado = new Imagem(PASTA_FIGURAS.'bloqueado2.png','Usuário Bloqueado.');
    $imageSenhaOk = new Imagem(PASTA_FIGURAS.'accept.png','Usuário Habilitado.');    
   
    /*
    $objeto->set_imagemCondicional(array(array('coluna' => 0,
                                               'valor' => 1,
                                               'operador' => '=',
                                               'imagem' => $imageSenhaPadrao),
                                         array('coluna' => 0,
                                               'valor' => 2,
                                               'operador' => '=',
                                               'imagem' => $imageAcessoBloqueado),
                                         array('coluna' => 0,
                                               'valor' => 3,
                                               'operador' => '=',
                                               'imagem' => $imageSenhaOk)));
   */
    
    
    # Passar usuário para senha Padrão
    $botao1 = new BotaoGrafico();
    $botao1->set_title('Redefine para senha padrão');
    $botao1->set_label('');
    $botao1->set_url('?fase=senhaPadrao&idUsuarioSenhaPadrao=');
    #$botao1->set_confirma('Você deseja realmente redefinir esse senha para a senha padrão?');    
    $botao1->set_image(PASTA_FIGURAS.'senha.png',20,20);
    
    # Bloquear usuário    
    $botao2 = new BotaoGrafico();
    $botao2->set_title('Bloqueia o acesso desse servidor a área do servidor. (passa a senha para null)');
    $botao2->set_label('');
    $botao2->set_url('?fase=bloquear&idUsuarioBloqueado=');
    #$botao1->set_confirma('Você deseja realmente bloquear o acesso desse servidor a área do servidor?');
    $botao2->set_image(PASTA_FIGURAS.'bloquear.png',20,20);
    
    # Permisões    
    $botao3 = new BotaoGrafico();
    $botao3->set_title('Gerencia as permissões do usuário');
    $botao3->set_label('');
    $botao3->set_url('permissoes.php?idUsuarioPesquisado=');
    $botao3->set_image(PASTA_FIGURAS.'group_edit.png',20,20);
    
    # Coloca o objeto link na tabela			
    $objeto->set_link(array(null,null,null,null,null,null,null,null,$botao1,$botao2,$botao3));	

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
    array_unshift($result, array(0,null)); # Adiciona o valor de nulo

    # Campos para o formulario
    $objeto->set_campos(array(
        array ('linha' => 1,
               'col' => 5, 
               'nome' => 'usuario',
               'label' => 'Usuário:',
               'tipo' => 'texto',
               'autofocus' => true,
               'required' => true,
               'unique' => true,
               'size' => 15),
        array ('linha' => 1,
               'col' => 2, 
               'nome' => 'tipoUsuario',
               'label' => 'Tipo:',
               'tipo' => 'combo',
               'array' => array(array(null,"--"),array(1,"Servidor"),array(2,"Bolsista")),
               'required' => true,
               'size' => 10),
        array ('linha' => 2,
               'col' => 6, 
               'nome' => 'idServidor',
               'label' => 'Nome (servidor):',
               'tipo' => 'combo',
               'array' => $result,
               'size' => 20),
        array ('linha' => 2,
               'col' => 6, 
               'nome' => 'nome',
               'label' => 'Nome (bolsista):',
               'tipo' => 'texto',
               'size' => 100),
        array ('linha' => 3,
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
            $idUsuarioSenhaPadrao = get('idUsuarioSenhaPadrao');
            
            # Troca a senha
            $intra->set_senha($idUsuarioSenhaPadrao);
            
            # Pega o idServidor desse usuário
            $idServidorSenhaPadrao = $intra->get_idServidor($idUsuarioSenhaPadrao);

            # Grava no log a atividade
            $data = date("Y-m-d H:i:s");
            $atividade = 'Passou '.$pessoal->get_nome($idServidorSenhaPadrao).' para senha padrão';
            $intra->registraLog($idUsuario,$data,$atividade,'tbservidor',$idServidorSenhaPadrao,2,$idServidorSenhaPadrao);

            loadPage('?fase=listar');

            break;
        
        ###################################################################	
        # Bloquear accesso

        case "bloquear" :
            # Pega o usuário que vai alterar senha
            $idUsuarioBloqueado = get('idUsuarioBloqueado');
            
            # Troca a senha
            $intra->set_senhaNull($idUsuarioBloqueado);
            
            # Pega o idServidor desse usuário
            $idServidorBloqueado = $intra->get_idServidor($idUsuarioBloqueado);

            # Grava no log a atividade
            $log = new Intra();
            $data = date("Y-m-d H:i:s");
            $atividade = 'Bloqueou acesso de '.$pessoal->get_nome($idServidorBloqueado);
            $log->registraLog($idUsuario,$data,$atividade,'tbservidor',$idUsuarioBloqueado,2,$idServidorBloqueado);

            loadPage('?fase=listar');

            break;

        ###################################################################	
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("login.php");
}