<?php
/**
 * Cadastro de Log
 *  
 * By Alat
 */

# Reservado para a matrícula do servidor logado
$matricula = null;	

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($matricula,null,true);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Pega o parametro de pesquisa (se tiver)
    if(HTML5)
        $parametro = retiraAspas(post('parametro',get('parametro',date("Y-m-d"))));
    else
        $parametro = retiraAspas(post('parametro',get('parametro',date("d/m/Y"))));
    
    $servidorLog = post('servidorLog','*');
    $servidorIp = post('servidorIp','*');
    
    # Aparentemente a rotina acima só funciona a contento no chrome
    # Como é uma rotina de acesso restrito deixei para avaliar esse problema depois
    # pois eu somente uso o chrome mesmo.
    
    # Começa uma nova página
    $page = new Page();
    if(($parametro == date("Y-m-d")) OR ($parametro == date("d/m/Y")))
    {
        $page->set_refresh(true);
        $page->set_bodyOnLoad('contagemRegressiva(30,"divContagemInterna")');
    }
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Log');

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

    # select da lista
    $selectLista = 'SELECT tblog.tipo,
                           tblog.matricula,
                           tbpessoa.nome,
                           tblog.data,
                           tblog.ip,
                           tblog.tabela,
                           tblog.idValor,
                           tblog.atividade,                                      
                           tblog.idlog
                      FROM intra.tblog 
                 LEFT JOIN pessoal.tbfuncionario ON intra.tblog.matricula = pessoal.tbfuncionario.matricula
                 LEFT JOIN pessoal.tbpessoa ON pessoal.tbfuncionario.idpessoa = pessoal.tbpessoa.idpessoa 
                     WHERE date(tblog.data) = "'.$parametro.'"';
    
    if($servidorLog <> "*")
        $selectLista .=' AND tblog.matricula = "'.$servidorLog.'"';
    
    if($servidorIp <> "*")
        $selectLista .=' AND tblog.ip = "'.$servidorIp.'"';
       
    $selectLista .=' ORDER BY 9 desc';
    
    $objeto->set_selectLista ($selectLista);

    # select do edita
    $objeto->set_selectEdita('SELECT matricula,
                                     data,
                                     atividade
                                FROM tblog
                               WHERE idLog = '.$id);

    # Caminhos
    #$objeto->set_linkEditar('?fase=editar');
    #$objeto->set_linkExcluir('?fase=excluir');
    #$objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_botaoIncluir(false);

    # Parametros da tabela
    $objeto->set_label(array("","Matrícula","Usuário","Data","IP","Tabela","Id","Atividade"));
    $objeto->set_width(array(1,6,15,10,6,8,5,48));		
    $objeto->set_align(array("center","center","center","center","center","center","center","left"));
    $objeto->set_function(array (null,"dv",null,"datetime_to_php"));
    $objeto->set_formatacaoCondicional(array( array('coluna' => 0,
                                                    'valor' => 0,
                                                    'operador' => '=',
                                                    'id' => 'logOutros'),
                                              array('coluna' => 0,
                                                    'valor' => 1,
                                                    'operador' => '=',
                                                    'id' => 'logLogin'),
                                              array('coluna' => 0,
                                                    'valor' => 2,
                                                    'operador' => '=',
                                                    'id' => 'logRelatorio'),
                                              array('coluna' => 0,
                                                    'valor' => 3,
                                                    'operador' => '=',
                                                    'id' => 'logGrh'),
                                              array('coluna' => 0,
                                                    'valor' => 4,
                                                    'operador' => '=',
                                                    'id' => 'logGti'),
                                             array('coluna' => 0,
                                                    'valor' => 5,
                                                    'operador' => '=',
                                                    'id' => 'logProcesso')
                                                    ));
    
    # Imagem Condicional
    $imagemLogin = new Imagem(PASTA_FIGURAS.'login.gif','Usuário efetuou o login',15,15);
    $imagemRelatorio = new Imagem(PASTA_FIGURAS.'printer.png','Usuário Visualizou um Relatório',15,15);
    $imagemGti = new Imagem(PASTA_FIGURAS.'logGti.png','',15,15);
    $imagemGrh = new Imagem(PASTA_FIGURAS.'logGrh.png','Cadastro de Pessoal',15,15);
    $imagemProcesso = new Imagem(PASTA_FIGURAS.'logProcesso.png','Cadastro de Processo',15,15);
    $imagemOutros = new Imagem(PASTA_FIGURAS.'logOutros.png','Log',15,15);
    
    
    $objeto->set_imagemCondicional(array(array('coluna' => 0,
                                               'valor' => 0,
                                               'operador' => '=',
                                               'imagem' => $imagemOutros),
                                         array('coluna' => 0,
                                               'valor' => 1,
                                               'operador' => '=',
                                               'imagem' => $imagemLogin),
                                         array('coluna' => 0,
                                               'valor' => 2,
                                               'operador' => '=',
                                               'imagem' => $imagemRelatorio),
                                         array('coluna' => 0,
                                               'valor' => 3,
                                               'operador' => '=',
                                               'imagem' => $imagemGrh),
                                         array('coluna' => 0,
                                               'valor' => 4,
                                               'operador' => '=',
                                               'imagem' => $imagemGti),
                                         array('coluna' => 0,
                                               'valor' => 5,
                                               'operador' => '=',
                                               'imagem' => $imagemProcesso)));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tblog');

    # Nome do campo id
    $objeto->set_idCampo('idlog');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Foco do form
    $objeto->set_formFocus('matricula');

    # Paginação
    #$objeto->set_paginacao(true);
    #$objeto->set_paginacaoInicial($paginacao);
    #$objeto->set_paginacaoItens(20);

    ################################################################
    switch ($fase)
    {
        case "" :
        case "listar" :
            # Toca um som quando carrega a página
            #echo "<audio autoplay='autoplay'><source src='../audio/alerta.mp3' type='audio/mp3'></audio>"; ## funciona !!!
			
            # Rotina de listar
                # data
                $form = new Form('?fase=listar');

                $controle = new Input('parametro','data','Entre com a data',1);
                $controle->set_size(30);
                $controle->set_title('Insira a data');
                $controle->set_valor($parametro);
                $controle->set_autofocus(true);
                $controle->set_onChange('formPadrao.submit();');
                $controle->set_linha(1);
                $controle->set_col(3);
                $form->add_item($controle);
                
                # Pega os servidores
                $result = $servidor->select('SELECT DISTINCT tblog.matricula,
                                                    tbpessoa.nome
                                               FROM intra.tblog 
                                          LEFT JOIN pessoal.tbfuncionario ON intra.tblog.matricula = pessoal.tbfuncionario.matricula
                                          LEFT JOIN pessoal.tbpessoa ON pessoal.tbfuncionario.idpessoa = pessoal.tbpessoa.idpessoa 
                                              WHERE date(tblog.data) = "'.$parametro.'"								
                                           ORDER BY 2');
                array_push($result,array('*','-- Todos --'));
                
                $controle = new Input('servidorLog','combo','Filtra por Servidor',1);
                $controle->set_size(30);
                $controle->set_title('Servidor');
                $controle->set_array($result);
                $controle->set_valor($servidorLog);
                $controle->set_onChange('formPadrao.submit();');
                $controle->set_linha(1);
                $controle->set_col(6);
                $form->add_item($controle);
                
                # Pega os ips
                $result2 = $servidor->select('SELECT DISTINCT tblog.ip,
                                                    tblog.ip
                                               FROM intra.tblog 
                                          LEFT JOIN pessoal.tbfuncionario ON intra.tblog.matricula = pessoal.tbfuncionario.matricula
                                          LEFT JOIN pessoal.tbpessoa ON pessoal.tbfuncionario.idpessoa = pessoal.tbpessoa.idpessoa 
                                              WHERE date(tblog.data) = "'.$parametro.'"							
                                           ORDER BY 2');
                array_push($result2,array('*','-- Todos --'));
                
                $controle = new Input('servidorIp','combo','Filtra por IP',1);
                $controle->set_size(20);
                $controle->set_title('Ip do computador');
                $controle->set_array($result2);
                $controle->set_valor($servidorIp);
                $controle->set_onChange('formPadrao.submit();');
                $controle->set_linha(1);
                $controle->set_col(3);
                $form->add_item($controle);
                
                br();
                $form->show();
            $objeto->listar();
            
            # Div Contagem de refresh
            if(($parametro == date("Y-m-d")) OR ($parametro == date("d/m/Y")))
            {
                $div = new Div('divContagemExterna');
                $div->set_title('Atualização da página');
                $div->abre();
                    echo 'Atualização da página:';                
                    $divInterna = new Div('divContagemInterna');
                    $divInterna->abre(); 

                    $divInterna->fecha();
                    echo 'segundos';
                $div->fecha();
            }			
			
            break;			
    }									 	 		

    $page->terminaPagina();
}

