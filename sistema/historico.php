<?php
/**
 * Cadastro de Log
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,2);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));
    
    # pega o idServidor (se tiver) quando for exibir somente o histórico de um servidor
    $idServidor = soNumeros(get('idServidor'));

    # Pega o parametro de pesquisa (se tiver)
    $parametro = retiraAspas(post('parametro',get('parametro',date("Y-m-d"))));
    
    $usuarioLog = post('usuarioLog');
    $usuarioIp = post('usuarioIp');
    $idTabela = post('idTabela');
    $tabela = post('tabela');
    $idServidorPesquisado = post('idServidorPesquisado');
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);  
    
    # botão de voltar da lista
    if(is_null($idServidor)){
        botaoVoltar('administracao.php');
    }else{
        botaoVoltar('../../grh/grhSistema/servidorMenu.php');
    }
    
    # Informa o dia da semana
    if(is_null($idServidor)){
        p(diaSemana(date_to_php($parametro)),"f18","center");
    }
    
    # Formulário de Pesquisa
    $form = new Form('?fase=listar&idServidor='.$idServidor);

    $controle = new Input('parametro','date','Entre com a data',1);
    $controle->set_size(30);
    $controle->set_title('Insira a data');
    $controle->set_valor($parametro);
    $controle->set_autofocus(TRUE);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(4);
    if (is_null($idServidor)){
        $form->add_item($controle);
    }

    # Pega os Usuarios
    $select = 'SELECT DISTINCT tblog.idUsuario,
                        tbusuario.usuario
                        FROM tblog JOIN tbusuario ON (tblog.idUsuario = tbusuario.idUsuario)
                        JOIN grh.tbservidor ON (tbusuario.idServidor = grh.tbservidor.idServidor)
                        JOIN grh.tbpessoa ON (grh.tbservidor.idPessoa = grh.tbpessoa.idPessoa)
                 WHERE ';

    if(is_null($idServidor)){
        $select .=' date(data) = "'.$parametro.'"';
    }else{
        $select .=' tblog.idServidor = '.$idServidor;
    }

    $select .= ' AND tblog.idUsuario IS NOT NULL  
                      ORDER BY 2';

    $result = $intra->select($select);

    $usuariosLogados = $result;
    array_push($result,array(NULL,'-- Todos --'));

    $controle = new Input('usuarioLog','combo','Filtra por Usuário',1);
    $controle->set_size(30);
    $controle->set_title('Servidor');
    $controle->set_array($result);
    $controle->set_valor($usuarioLog);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(4);
    $form->add_item($controle);

    # Pega os ips
    $select2 = 'SELECT DISTINCT ip,
                       ip
                  FROM tblog
                 WHERE ';

    if(is_null($idServidor)){
        $select2 .=' date(data) = "'.$parametro.'"';
    }else{
        $select2 .=' idServidor = '.$idServidor;
    }

    $select2 .= ' AND ip IS NOT NULL  
                      ORDER BY 2';

    $result2 = $intra->select($select2);

    array_push($result2,array(NULL,'-- Todos --'));

    $controle = new Input('usuarioIp','combo','Filtra por IP',1);
    $controle->set_size(20);
    $controle->set_title('Ip do computador');
    $controle->set_array($result2);
    $controle->set_valor($usuarioIp);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(4);
    $form->add_item($controle);

    # Pega as tabelas
    $select3 = 'SELECT DISTINCT tabela,
                       tabela
                  FROM tblog
                 WHERE ';

    if(is_null($idServidor)){
        $select3 .=' date(data) = "'.$parametro.'"';
    }else{
        $select3 .=' idServidor = '.$idServidor;
    }

    $select3 .= ' AND tabela IS NOT NULL  
                      ORDER BY 2';

    $result3 = $intra->select($select3);

    array_push($result3,array(NULL,'-- Todos --'));

    $controle = new Input('tabela','combo','Tabela',1);
    $controle->set_size(20);
    $controle->set_title('Tabela');
    $controle->set_array($result3);
    $controle->set_valor($tabela);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(2);
    $controle->set_col(4);
    $form->add_item($controle);

    # Pega o id das tabelas
    $select4 = 'SELECT DISTINCT idValor,
                       idValor
                  FROM tblog
                 WHERE ';

    if(is_null($idServidor)){
        $select4 .=' date(data) = "'.$parametro.'"';
    }else{
        $select4 .=' idServidor = '.$idServidor;
    }

    $select4 .= ' AND idValor IS NOT NULL  
                      ORDER BY 2';

    $result4 = $intra->select($select4);

    array_push($result4,array(NULL,'-- Todos --'));

    $controle = new Input('idTabela','combo','Id',1);
    $controle->set_size(20);
    $controle->set_title('Id da tabela');
    $controle->set_array($result4);
    $controle->set_valor($idTabela);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(2);
    $controle->set_col(4);
    $form->add_item($controle);

    # Pega o id Servidor
    $result5 = $intra->select('SELECT DISTINCT idServidor,
                                        idServidor
                                   FROM tblog
                                  WHERE date(data) = "'.$parametro.'"
                                    AND idServidor IS NOT NULL  
                               ORDER BY 2');
    array_push($result5,array(NULL,'-- Todos --'));

    $controle = new Input('idServidorPesquisado','combo','Id Servidor',1);
    $controle->set_size(20);
    $controle->set_title('id Servidor');
    $controle->set_array($result5);
    $controle->set_valor($idServidorPesquisado);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(2);
    $controle->set_col(4);

    if(is_null($idServidor)){
        $form->add_item($controle);
    }
    
    $form->show();
    
    # select
    $select = 'SELECT tipo,
                      idUsuario,
                      data,
                      ip,
                      tabela,
                      idValor,
                      idServidor,
                      atividade,                                      
                      idlog
                 FROM tblog
                WHERE';
    
    # Quando for histórico de um único servidor
    if(is_null($idServidor)){
        $select .=' date(data) = "'.$parametro.'"';
    }else{
        $select .=' idServidor = '.$idServidor;
    }
    
    # usuário
    if(!vazio($usuarioLog)){
        $select .=' AND idUsuario = '.$usuarioLog;
    }
    
    # IP
    if(!vazio($usuarioIp)){
        $select .=' AND ip = "'.$usuarioIp.'"';
    }
    
    # idTabela
    if(!vazio($idTabela)){
        $select .=' AND idValor = "'.$idTabela.'"';
    }
    
    # Tabela
    if(!vazio($tabela)){
        $select .=' AND tabela = "'.$tabela.'"';
    }
    
    # Id Servidor
    if(!vazio($idServidorPesquisado)){
        $select .=' AND idServidor = "'.$idServidorPesquisado.'"';
    }
    
    $select .=' ORDER BY data desc';
    
    # Pega os dados
    $row = $intra->select($select);
    
    # Monta a tabela
    $tabela = new Tabela();
    $tabela->set_titulo("Histórico do Dia");
    $tabela->set_conteudo($row);
    $tabela->set_label(array("","Usuário","Data","IP","Tabela","Id","IdServidor","Atividade"));
    $tabela->set_width(array(5,15,15,10,10,5,5,35));
    $tabela->set_align(array("center","center","center","center","center","center","center","left"));
    $tabela->set_funcao(array(NULL,NULL,"datetime_to_php",NULL,NULL,NULL,"exibeNomeTitle"));
    $tabela->set_classe(array(NULL,"intra"));
    $tabela->set_metodo(array(NULL,"get_usuario"));
    
    $tabela->set_formatacaoCondicional(array( array('coluna' => 0,
                                                    'valor' => 0,
                                                    'operador' => '=',
                                                    'id' => 'logLogin'),                                              
                                              array('coluna' => 0,
                                                    'valor' => 3,
                                                    'operador' => '=',
                                                    'id' => 'logExclusao'),
                                              array('coluna' => 0,
                                                    'valor' => 5,
                                                    'operador' => '=',
                                                    'id' => 'logLoginIncorreto')                                              
                                                    ));
    
    # Imagem Condicional
    $imagemLogin = new Imagem(PASTA_FIGURAS.'login.png','Usuário efetuou o login',15,15);
    $imagemInclusao = new Imagem(PASTA_FIGURAS.'logInclusao.png','Inclusão de Registro',15,15);
    $imagemAlterar = new Imagem(PASTA_FIGURAS.'logAlterar.png','Alteração de Registro',15,15);
    $imagemExclusao = new Imagem(PASTA_FIGURAS.'logExclusao.png','Exclusão de Registro',15,15);
    $imagemRelatorio = new Imagem(PASTA_FIGURAS.'logRelatorio.png','Visualizou Relatório',15,15);
    $imagemLoginIncorreto = new Imagem(PASTA_FIGURAS.'loginIncorreto.png','Login Incorreto',15,15);
    $imagemBackup = new Imagem(PASTA_FIGURAS.'backup2.png','Backup',15,15);
    $imagemVer = new Imagem(PASTA_FIGURAS.'visualizar.png','Visualizou',15,15);
    
    $tabela->set_imagemCondicional(array(array('coluna' => 0,
                                               'valor' => 0,
                                               'operador' => '=',
                                               'imagem' => $imagemLogin),
                                         array('coluna' => 0,
                                               'valor' => 1,
                                               'operador' => '=',
                                               'imagem' => $imagemInclusao),
                                         array('coluna' => 0,
                                               'valor' => 2,
                                               'operador' => '=',
                                               'imagem' => $imagemAlterar),
                                         array('coluna' => 0,
                                               'valor' => 3,
                                               'operador' => '=',
                                               'imagem' => $imagemExclusao),
                                         array('coluna' => 0,
                                               'valor' => 4,
                                               'operador' => '=',
                                               'imagem' => $imagemRelatorio),
                                         array('coluna' => 0,
                                               'valor' => 5,
                                               'operador' => '=',
                                               'imagem' => $imagemLoginIncorreto),
                                         array('coluna' => 0,
                                               'valor' => 6,
                                               'operador' => '=',
                                               'imagem' => $imagemBackup),
                                         array('coluna' => 0,
                                               'valor' => 7,
                                               'operador' => '=',
                                               'imagem' => $imagemVer)
                                        ));
    
    $tabela->show();
        
    $grid->fechaColuna();
    $grid->fechaGrid();  

    $page->terminaPagina();
}else{
    loadPage("login.php");
}

