<?php
/**
 * Gerencia de Usuários
 *  
 * By Alat
 */

# Inicia as variáveis que receberão as sessions
$matricula = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idusuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));
    
    # Define a senha padrão de acordo com o que está nas variáveis
    define("SENHA_PADRAO",$intra->get_variavel('senha_padrao'));

    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Verifica a paginacao
    $paginacao = get('paginacao',get_session('sessionPaginacao',0));	# Verifica se a paginação vem por get, senão pega a session
    set_session('sessionPaginacao',$paginacao);				# Grava na session a nova página

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro')))					# Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    else
    { 
        $parametro = post('parametro');					# Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro',$parametro);	 		# transfere para a session para poder recuperá-lo depois
        $paginacao = 0;
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
    $objeto->set_nome('Usuários');

    # botões
    $objeto->set_voltarLista('administracao.php');
    $objeto->set_botaoIncluir(false);

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar nos campos Matrícula ou Nome:');
    $objeto->set_parametroValue($parametro);

    # ordenação
    if(is_null($orderCampo))
            $orderCampo = 3;

    if(is_null($orderTipo))
            $orderTipo = 'asc';

    # select da lista
    $objeto->set_selectLista('SELECT tbfuncionario.matricula,
                                     tbfuncionario.matricula,
                                     tbpessoa.nome,
                                     concat(tblotacao.UADM," - ",tblotacao.DIR," - ",tblotacao.GER) ll,
                                     ult_acesso,
                                     tbfuncionario.matricula,
                                     tbfuncionario.matricula
                                FROM tbfuncionario LEFT JOIN tbpessoa ON (tbfuncionario.idPessoa = tbpessoa.idPessoa)
                                                        JOIN tbhistlot ON (tbfuncionario.matricula = tbhistlot.matricula)
                                                        JOIN tblotacao ON (tbhistlot.lotacao = tblotacao.idLotacao)
                               WHERE sit = 1
                                 AND (tbfuncionario.matricula LIKE "%'.$parametro.'%" OR tbpessoa.nome LIKE "%'.$parametro.'%")
                                 AND tbhistlot.data = (select max(data) from tbhistlot where tbhistlot.matricula = tbfuncionario.matricula)						     
                            ORDER BY '.$orderCampo.' '.$orderTipo);	

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    #$objeto->set_linkEditar('centralUsuario.php?fase=inicial');
    #$objeto->set_linkExcluir('?fase=usuarios&metodo=excluir');
    #$objeto->set_linkGravar('?fase=usuarios&metodo=gravar');
    $objeto->set_linkListar('?fase=listar');
    $senhaPadrao = md5($intra->get_variavel('senha_padrao'));
    $objeto->set_trBackgroundColor('#64E986');
    $objeto->set_formatacaoCondicional(array(array('coluna' => 0,
                                                    'valor' => 2,
                                                    'operador' => '=',
                                                    'id' => 'senhaNula'),
                                            array('coluna' => 0,
                                                    'valor' => 1,
                                                    'operador' => '=',
                                                    'id' => 'senhaPadrao'),
                                            array('coluna' => 0,
                                                    'valor' => 3,
                                                    'operador' => '=',
                                                    'id' => 'senhaOk')));
    
    
    # Imagem Condicional (não funcionou) usando a função numtografic
    $imageSenhaPadrao = new Imagem(PASTA_FIGURAS.'exclamation.png','Senha padrão - Insegura');
    $imageAcessoBloqueado = new Imagem(PASTA_FIGURAS.'bloqueado2.png','Usuário com Acesso Bloqueado!!');
    $imageSenhaOk = new Imagem(PASTA_FIGURAS.'accept.png','Acesso Normal por tempo indeterminsado.');    
   
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
   
    
    # Primeiro link da tabela (senha padrão)
    $link1 = new BotaoGrafico();
    $link1->set_image(PASTA_FIGURAS.'senha.png',20,20);
    $link1->set_url('?fase=senhaPadrao&matricula='); 
    $link1->set_title('Redefine para senha padrão');
    $link1->set_confirma('Você deseja realmente redefinir esse senha para a senha padrão?');

    # Segundo link da tabela (passa para nulo)
    $link2 = new BotaoGrafico();
    $link2->set_image(PASTA_FIGURAS.'bloquear.png',20,20);
    $link2->set_url('?fase=senhaNula&matricula='); 
    $link2->set_title('Bloqueia o acesso desse servidor a área do servidor. (passa a senha para null)');
    $link2->set_confirma('Você deseja realmente bloquear o acesso desse servidor a área do servidor?');

    # Terceiro link da tabela (regras ou permissões)
    if($intra->verificaPermissao($matricula,12))
    {        
        $link3 = new BotaoGrafico();
        $link3->set_image(PASTA_FIGURAS.'bullet_edit.png',20,20);
        $link3->set_url('permissoes.php?matricula='); 
        $link3->set_title('Gerencia as permissões do usuário');
        
        # Parametros da tabela
        $objeto->set_label(array("","Matricula","Nome","Lotação","Ultimo Acesso","Senha Padrão","Bloquear Acesso","Editar"));
        $objeto->set_width(array(5,8,30,30,12,5,5,5));
        $objeto->set_align(array("center","center","left","left"));
        
        # Coloca o objeto link na tabela			
        $objeto->set_link(array("","","","","",$link1,$link2,$link3));	
    }
    else
    {
        # Parametros da tabela
        $objeto->set_label(array("","Matricula","Nome","Lotação","Ultimo Acesso","Senha Padrão","Bloquear Acesso"));
        $objeto->set_width(array(5,8,30,30,13,7,7));
        $objeto->set_align(array("center","center","left","left"));
        
        # Coloca o objeto link na tabela			
        $objeto->set_link(array("","","","","",$link1,$link2));	
    }
    
    $objeto->set_function(array ("","dv","","","datetime_to_php"));
    $objeto->set_classe(array ("pessoal"));
    $objeto->set_metodo(array ("get_tipoSenha"));    

    # Classe do banco de dados
    $objeto->set_classBd('Pessoal');

    # Nome da tabela
    $objeto->set_tabela('tbfuncionario');

    # Nome do campo id
    $objeto->set_idCampo('matricula');
    
    # Botão Regra
    $botaoRegra = new Button("Regras");
    $botaoRegra->set_title("Cadastro de Regras e Permissões");
    $botaoRegra->set_url('regras.php');
    $botaoRegra->set_accessKey('R');
    
    # Botão Regra
    $botaoLegenda = new Button("Legenda");
    $botaoLegenda->set_title("Legenda de Cores");
    $botaoLegenda->set_onclick("abreFechaDivId('divLegendaUsuarios');");
    $botaoLegenda->set_accessKey('L');
    
    $objeto->set_botaoListar(array($botaoRegra,$botaoLegenda));
    
    # Paginação
    #$objeto->set_paginacao(true);
    #$objeto->set_paginacaoInicial($paginacao);
    #$objeto->set_paginacaoItens(20);

    ###############################################################
    switch ($fase)
    {
        case "" :
        case "listar" :

        # Pega o Valor da ausência máxima de um usuario/servidor
        $Intra = new Intra();
        $ausenciaMaxima = $Intra->get_variavel('ausencia_maxima');
        $ausenciaPadrao = $Intra->get_variavel('ausencia_padrao');

        # Tabela da legenda
        $tabela = Array(Array('3','Senha OK','Acesso Normal por tempo indeterminsado.'),
                  Array('1','Senha Padrão','Acesso temporário! O usuário tem um periodo de '.$ausenciaPadrao.' dia(s) para alterar sua senha senão seu acesso será bloqueado!'),
                  Array('2','Senha Nula','Acesso Bloqueado! O usuário que ficou inativo por mais de '.$ausenciaMaxima.' dia(s) tera sua conta bloqueada!'));

        $label = array("Figura","Nome","Descrição");
        $width = array(10,30,50);
        $align = array("center","center","left");

        # Função que exibe o gráfico
        #$funcao = array ("get_tipoSenha");

        # Div da legenda
        $divAviso = new Div('divLegendaUsuarios');
        $divAviso->abre();
        
        $callout = new Callout();
        $callout->abre();
            titulo("Legenda");

            $legenda = new Tabela('tableLegenda');
            #$legenda->set_titulo('Legenda'); 
            $legenda->set_conteudo($tabela);
            $legenda->set_cabecalho($label,$width,$align);
            $legenda->set_totalRegistro(false);
            #$legenda->set_funcao($funcao);
            $legenda->set_footTexto('Para alterar esses valores, acesse as variáveis de configuração.');
            $legenda->set_formatacaoCondicional(array(array('coluna' => 0,
                                                        'valor' => 2,
                                                        'operador' => '=',
                                                        'id' => 'senhaNula'),
                                                array('coluna' => 0,
                                                        'valor' => 1,
                                                        'operador' => '=',
                                                        'id' => 'senhaPadrao'),
                                                array('coluna' => 0,
                                                        'valor' => 3,
                                                        'operador' => '=',
                                                        'id' => 'senhaOk')));


             $legenda->set_imagemCondicional(array(array('coluna' => 0,
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
            $legenda->show();
        $callout->fecha();    
        $divAviso->fecha();
        
        $objeto->listar();
        break;
        
        ###################################################################

        case "gravar" :
            $objeto->$metodo($id);
            break;

        ###################################################################

        case "senhaPadrao" :
            $matr = get('matricula');
            $trocaSenha = new Pessoal();
            $trocaSenha->set_senha($matr);

            # Grava no log a atividade
            $log = new Intra();
            $data = date("Y-m-d H:i:s");
            $atividade = 'Passou '.$matr.' para senha padrão';
            $log->registraLog($matricula,$data,$atividade,'tbfuncionario',$matr);

            loadPage('?fase=listar');

            break;

        ###################################################################	
        # Bloquear accesso

        case "senhaNula" :
            $matr = get('matricula');
            $trocaSenha = new Pessoal();
            $trocaSenha->set_senhaNull($matr);

            # Grava no log a atividade
            $log = new Intra();
            $data = date("Y-m-d H:i:s");
            $atividade = 'Bloqueou acesso de '.$matr;
            $log->registraLog($matricula,$data,$atividade,'tbfuncionario',$matr);

            loadPage('?fase=listar');

            break;

        ###################################################################	
    }		

    $page->terminaPagina();
}
