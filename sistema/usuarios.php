<?php
/**
 * Gerencia de Usuários
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $pessoal = new Pessoal();
    
    # Define a senha padrão de acordo com o que está nas variáveis
    define("SENHA_PADRAO",$intra->get_variavel('senhaPadrao'));
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));
    $idPermissao = soNumeros(get('idPermissao')); 
    $idRegra = soNumeros(get('idRegra'));
    $ldLotacao = soNumeros(get('idLotacao'));

    # Pega o parametro de pesquisa (se tiver)
    if(is_null(post('parametro'))){								# Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    }else{
        $parametro = post('parametro');								# Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro',$parametro);			 		# transfere para a session para poder recuperá-lo depois
    }
    
    # Pega o parametro de pesquisa (se tiver)
    $parametroHistorico = post('historico');

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
    # Exibe os dados do Usuario
    $objeto->set_rotinaExtraEditar("get_DadosServidor");
    $objeto->set_rotinaExtraEditarParametro($intra->get_idServidor($id)); 

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Usuários');

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

    # controle de pesquisa
    #$objeto->set_parametroLabel('Pesquisar');
    #$objeto->set_parametroValue($parametro);

    # ordenação
    if(is_null($orderCampo)){
         $orderCampo = "1";
    }

    if(is_null($orderTipo)){
        $orderTipo = 'asc';
    }

    # select da lista
    $objeto->set_selectLista ('SELECT idUsuario,
                                      idUsuario,
                                      usuario,
                                      idServidor,
                                      ultimoAcesso,
                                      idServidor,
                                      idServidor,
                                      idServidor,
                                      idUsuario,
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
    #$objeto->set_label(array("Status","Id","Usuário","Nome","Último Acesso", "Lotação","Cargo","Padrão","Bloquear","Perm."));
    $objeto->set_label(array("Status","Id","Usuário","Nome","Último Acesso", "Lotação","Cargo","Email","Editar"));
    #$objeto->set_width(array(5,4,10,5,15,10,15,11,5,5,5));
    $objeto->set_align(array("center","center","center","left","center","center","left","left"));

    $objeto->set_classe(array(NULL,NULL,NULL,"pessoal",NULL,"pessoal","pessoal","pessoal"));
    $objeto->set_metodo(array(NULL,NULL,NULL,"get_nome",NULL,"get_lotacao","get_cargo","get_emailIUenfServidor"));
    $objeto->set_funcao(array("statusUsuario",NULL,NULL,NULL,"datetime_to_php"));
    
    $objeto->set_botaoExcluir(FALSE);
    $objeto->set_botaoEditar(FALSE);
    
    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new BotaoGrafico();
    $botao->set_label('');
    $botao->set_title('Editar Usuário');
    $botao->set_url('?fase=exibeAtividades&id='.$id);
    $botao->set_image(PASTA_FIGURAS.'bullet_edit.png',20,20);

    # Coloca o objeto link na tabela			
    $objeto->set_link(array("","","","","","","","",$botao));
    
    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbusuario');

    # Nome do campo id
    $objeto->set_idCampo('idUsuario');
    
    # Pega os dados da combo nome
    $result = $pessoal->select('SELECT idServidor, 
                                       tbpessoa.nome
                                  FROM tbservidor JOIN tbpessoa USING(idPessoa)
                                 WHERE tbservidor.situacao = 1
                              ORDER BY tbpessoa.nome');
    array_unshift($result, array(0,NULL)); # Adiciona o valor de nulo

    # Campos para o formulario
    $objeto->set_campos(array(
        array ('linha' => 1,
               'col' => 3, 
               'nome' => 'usuario',
               'label' => 'Usuário:',
               'tipo' => 'texto',
               'autofocus' => TRUE,
               'required' => TRUE,
               'unique' => TRUE,
               'size' => 15),
        array ('linha' => 1,
               'col' => 9, 
               'nome' => 'idServidor',
               'label' => 'Nome (servidor):',
               'tipo' => 'combo',
               'array' => $result,
               'size' => 20),
        array ('linha' => 2,
               'col' => 12,
               'nome' => 'obs',
               'label' => 'Observação:',
               'tipo' => 'texto',
               'size' => 30)
        ));

    # Log
    $objeto->set_idUsuario($idUsuario); 
    $objeto->set_exibeInfoObrigatoriedade(FALSE);
    
    ################################################################
    switch ($fase)
    {
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :
            $objeto->$fase($id);
            break;
        
        ##########################################################################################
            
        case "exibeAtividades" : 
            # Grid para o menu, os dados do usuário e informação sobre o status
            $grid = new Grid();
            $grid->abreColuna(12);
            
            ############################
            
            # Menu
            $menu1 = new MenuBar();
            
            # Verifica o status do usuário editado
            $statusUsuario = $intra->get_tipoSenha($id);

            # Voltar
            $linkVoltar = new Link("Voltar","?");
            $linkVoltar->set_class('button');
            $menu1->add_link($linkVoltar,"left");

            # Permissões
            $botaoPermissao = new Button("Permissões");
            $botaoPermissao->set_title("Gerencia as permissões desse usuário");
            $botaoPermissao->set_class('button');
            $botaoPermissao->set_url('?fase=exibePermissao&id='.$id);
            $menu1->add_link($botaoPermissao,"right");

            # Alterar Senha
            $linkSenha = new Link("Editar Nome","?fase=editar&id=".$id);
            $linkSenha->set_class('button');
            $linkSenha->set_title('Altera a senha do usuário logado');
            $menu1->add_link($linkSenha,"right");
            
            # Senha Padrão
            if($statusUsuario <> 1){  # Verifica se já está com senha padrão
                $botaoPadrao = new Button("Padrão",'?fase=senhaPadrao&id='.$id);
                $botaoPadrao->set_title("Passa para senha padrão");
                $botaoPadrao->set_class('success button');
                $menu1->add_link($botaoPadrao,"right");    
            }

            # Bloquear
            if($statusUsuario <> 2){ # Verifica se já está bloqueado
                if($id <> $idUsuario){ # Impede o usuário bloquear a si próprio
                    $botaoBloquear = new Button("Bloquear",'?fase=bloquear&id='.$id);
                    $botaoBloquear->set_title("Bloqueia o acesso desse servidor a área do servidor. (passa a senha para NULL)");
                    $botaoBloquear->set_class('alert button');
                    $menu1->add_link($botaoBloquear,"right");
                }
            }

            $menu1->show();
            
            ############################
            
            # Exibe os dados do Usuário
            AreaServidor::listaDadosUsuario($id);
            
            ############################
            
            # Informa o status do usuário editado
            switch($statusUsuario){
                case 1 :
                    # Exibe a mensagem
                    $callout = new Callout('warning');
                    $callout->abre();
                        echo "Usuário com senha padrão.";
                    $callout->fecha();
                    break;

                case 2 :
                    # Exibe a mensagem
                    $callout = new Callout('warning');
                    $callout->abre();
                        echo "Usuário Bloqueado.";
                    $callout->fecha();
                    break;
            }
                            
            $grid->fechaColuna();
            $grid->fechaGrid();
            
            ############################
            
            # área lateral da tela
            
            # Combo com a data
            if(!is_null($id)){
                
                # Limita a tela
                $grid1 = new Grid();
                $grid1->abreColuna(12);
            
                # Pega os dados da combo
                $parametroMulti = $intra->select('SELECT DISTINCT CONCAT(MONTH(data),"/",YEAR(data))
                                                 FROM tblog
                                                WHERE idusuario = '.$id.' ORDER BY YEAR(data) DESC,MONTH(data) DESC');
                
                $parametro = array();
                
                # Transforma $parametroMulti (array multi) em array simples
                for ($i = 0; $i < count($parametroMulti); $i++){
                    $parametro[$i] = $parametroMulti[$i][0];
                }
                
                if(count($parametro) > 0){
                    $grid2 = new Grid();
                    $grid2->abreColuna(3);
                    
                    # Controle do mês
                    $form = new Form('?fase=exibeAtividades&id='.$id);
                    $form->set_class('formHistorico');
                                
                    $controle = new Input('historico','combo');
                    $controle->set_size(30);
                    $controle->set_title('Informe o mês do histórico');
                    $controle->set_array($parametro);
                    if(!is_null($parametroHistorico)){
                        $controle->set_valor($parametroHistorico);
                    }
                    $controle->set_autofocus(TRUE);
                    $controle->set_onChange('formHistorico.submit();');
                    $controle->set_linha(1);
                    $controle->set_col(12);
                    $form->add_item($controle);

                    $form->show();
               
                    if(is_null($parametroHistorico)){
                        $parametroHistorico = $parametro[0];
                    }
                
                    # Trata o parâmetro                
                    $parametroTratado = explode("/", $parametroHistorico);
                    $mes = $parametroTratado[0];
                    $ano = $parametroTratado[1];

                    # Browsers Preferidos
                    $select = 'SELECT browser,
                               count(*) as tot
                          FROM tblog
                         WHERE idUsuario = '.$id.' 
                           AND tipo = 0 
                           AND YEAR(data) = '.$ano.'
                           AND MONTH(data) = '.$mes.'      
                      GROUP BY browser ORDER BY 2 desc';
                    
                    $conteudo = $intra->select($select,TRUE);
                    
                    # Pega a soma dos campos
                    $soma = 0;
                    foreach ($conteudo as $value){
                        $soma += $value['tot'];
                    }

                    $tabela = new Tabela();
                    $tabela->set_conteudo($conteudo);
                    $tabela->set_titulo("Browsers Preferidos");
                    $tabela->set_label(array("Browser","Logins"));
                    $tabela->set_align(array("center"));
                    $tabela->set_totalRegistro(FALSE);
                    $tabela->set_rodape("Total de Logins: ".$soma);
                    $tabela->show();

                    # iPs 
                    $select = 'SELECT ip,
                               count(*) as tot
                          FROM tblog
                         WHERE idUsuario = '.$id.'
                           AND tipo = 0                         
                           AND YEAR(data) = '.$ano.'
                           AND MONTH(data) = '.$mes.'      
                      GROUP BY ip ORDER BY 2 desc';

                    $conteudo = $intra->select($select,TRUE);
                    
                    # Pega a soma dos campos
                    $soma = 0;
                    foreach ($conteudo as $value){
                        $soma += $value['tot'];
                    }

                    $tabela = new Tabela();
                    $tabela->set_conteudo($conteudo);
                    $tabela->set_titulo("IPs Acessados");
                    $tabela->set_label(array("ip","Logins"));
                    $tabela->set_align(array("center"));
                    $tabela->set_totalRegistro(FALSE);
                    $tabela->set_rodape("Total de Logins: ".$soma);
                    $tabela->show();

                    $grid2->fechaColuna();
                    
                    ############################
                    # Área central
                    
                    # Exibe o histórico de atividades do usuário
                    $grid2->abreColuna(9);

                    # select
                    $select = 'SELECT tipo,
                               data,
                               ip,
                               tabela,
                               idValor,
                               idServidor,
                               atividade,                                      
                               idlog
                          FROM tblog
                         WHERE idUsuario = '.$id.'   
                           AND YEAR(data) = '.$ano.'
                           AND MONTH(data) = '.$mes;

                    $conteudo = $intra->select($select,TRUE);

                    $tabela = new Tabela();
                    $tabela->set_conteudo($conteudo);
                    $tabela->set_titulo("Atividade Mensal Detalhada");
                    $tabela->set_width(array(5,20,10,10,5,5,45));
                    $tabela->set_label(array("","Data","IP","Tabela","Id","IdServidor","Atividade"));
                    $tabela->set_align(array("center","center","center","center","center","center","left"));
                    $tabela->set_funcao(array(NULL,"datetime_to_php",NULL,NULL,NULL,"exibeNomeTitle"));

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
                                                               'imagem' => $imagemBackup)
                                                        ));
                    $tabela->show();
                    $grid2->fechaColuna();
                    $grid2->fechaGrid();
                }else{
                    callout('Parece que esse usuário nunca logou no sistema !!','secondary');
                }
                $grid1->fechaColuna();
                $grid1->fechaGrid();
            }
            break;
        
        ##########################################################################################
        
        case "exibePermissao" :
            $grid = new Grid();
            $grid->abreColuna(12);
           
            botaoVoltar('?fase=exibeAtividades&id='.$id);
            
            # Exibe os dados do Usuário
            Grh::listaDadosServidor($intra->get_idServidor($id));
            #titulo('Permissões');
            
            if(!is_null($id)){
                $grid = new Grid();
                $grid->abreColuna(6);

                # select
                $select = 'SELECT tbregra.idRegra,
                                  tbregra.nome,
                                  tbregra.descricao,									
                                  tbpermissao.idPermissao
                             FROM tbregra LEFT JOIN tbpermissao on tbregra.idRegra = tbpermissao.idRegra
                            WHERE tbpermissao.idUsuario = '.$id.'
                         ORDER BY tbregra.nome';

                $conteudo = $intra->select($select,TRUE);

                $tabela = new Tabela();
                $tabela->set_conteudo($conteudo);
                $tabela->set_titulo("Permissões Incluídas");
                $tabela->set_label(array("Num","Regra","Descrição","Excluir"));
                #$tabela->set_width(array(7,20,66));
                $tabela->set_align(array("center","left","left"));
                
                #$tabela->set_excluir('?fase=excluirPermissao&id='.$id);
                $tabela->set_idCampo('idPermissao');
                $tabela->set_nomeGetId("idPermissao");
                
                # Botão de exclusao
                $botao1 = new BotaoGrafico();
                $botao1->set_label('');
                $botao1->set_title('Excluir essa permissão');
                $botao1->set_url("?fase=excluirPermissao&id=$id&idPermissao=");
                $botao1->set_image(PASTA_FIGURAS.'bullet_cross.png',20,20);


                # Coloca o objeto link na tabela			
                $tabela->set_link(array("","","",$botao1));
                
                if(count($conteudo) > 0){
                    $tabela->show();
                }else{
                    tituloTable("Permissões Incluídas");
                    br();
                    callout('Usuário sem permissão incluída no sistema !!','secondary');
                }
                
                ###
                
                $grid->fechaColuna();
                $grid->abreColuna(6);
                
                # select
                $select = "SELECT distinct idRegra,
                                  nome,
                                  descricao,
                                  idRegra
                             FROM tbregra
                             WHERE idRegra NOT IN (SELECT idRegra 
                                         FROM tbpermissao
                                        WHERE idUsuario = $id
                                          AND tbregra.idRegra = tbpermissao.idRegra)
                         ORDER BY nome";

                $conteudo = $intra->select($select,TRUE);

                $tabela = new Tabela();
                $tabela->set_conteudo($conteudo);
                $tabela->set_titulo("Permissões Disponíveis");
                $tabela->set_label(array("Num","Regra","Descrição","Incluir"));
                #$tabela->set_width(array(7,20,66));
                $tabela->set_align(array("center","left","left"));

                #$tabela->set_excluir('?fase=gravarPermissao&id='.$id);
                $tabela->set_idCampo('idRegra');
                #$tabela->set_nomeGetId("idRegra");

                # Botão de inclusao
                $botao = new BotaoGrafico();
                $botao->set_label('');
                $botao->set_title('Servidores com permissão a essa regra');
                $botao->set_url("?fase=incluirPermissao&id=$id&idRegra=");
                $botao->set_image(PASTA_FIGURAS.'adicionar.png',20,20);

                # Coloca o objeto link na tabela			
                $tabela->set_link(array("","","",$botao));


                if(count($conteudo) > 0){
                    $tabela->show();
                }else{
                    tituloTable("Permissões Disponíveis");
                    br();
                    callout('Nenhuma permissão disponível !!','secondary');
                }

                $grid->fechaColuna();
                $grid->fechaGrid();                
            }
            
            $grid->fechaColuna();
            $grid->fechaGrid();
            break;
            
        case "excluir" :	
        case "gravar" :
            $objeto->$fase($id);
            break;
        
        ##########################################################################################

        case "senhaPadrao" :
            # Troca a senha
            $intra->set_senha($id);
            
            # Pega o idServidor desse usuário
            $idServidorSenhaPadrao = $intra->get_idServidor($id);

            # Grava no log a atividade
            $data = date("Y-m-d H:i:s");
            $atividade = 'Passou o usuário '.$intra->get_nickUsuario($id).' ('.$pessoal->get_nome($idServidorSenhaPadrao).') para senha padrão';
            $intra->registraLog($idUsuario,$data,$atividade,'tbservidor',$id,2,$idServidorSenhaPadrao);

            loadPage('?fase=exibeAtividades&id='.$id);

            break;
        
        ##########################################################################################

        case "bloquear" :
            # Troca a senha
            $intra->set_senhaNull($id);
            
            # Pega o idServidor desse usuário
            $idServidorBloqueado = $intra->get_idServidor($id);

            # Grava no log a atividade
            $log = new Intra();
            $data = date("Y-m-d H:i:s");
            $atividade = 'Bloqueou acesso do usuário '.$intra->get_nickUsuario($id).' ('.$pessoal->get_nome($idServidorBloqueado).')';
            $log->registraLog($idUsuario,$data,$atividade,'tbservidor',$id,2,$idServidorBloqueado);

            loadPage('?fase=exibeAtividades&id='.$id);

            break;

         ##########################################################################################	

        case "excluirPermissao" :
            # Pega os dados para o log
            $regra = $intra->get_permissao($idPermissao);
            $nomeUsuario = $intra->get_nickUsuario($id);
            $atividade = "Excluiu a permissao ao ($regra) do usuário $nomeUsuario";
            $servidor = $intra->get_idServidor($id);
            
            # Exclui a permissão
            $intra->set_tabela('tbpermissao');     # a tabela
            $intra->set_idCampo('idPermissao');    # o nome do campo id
            $intra->excluir($idPermissao);         # executa a exclusão

            # Grava no log a atividade
            $data = date("Y-m-d H:i:s");
            $intra->registraLog($idUsuario,$data,$atividade,'tbpermissao',$idPermissao,3,$servidor);	
            
            loadPage ('?fase=exibePermissao&id='.$id);
            break;

        ##########################################################################################	

        case "incluirPermissao" :
            $regra = $intra->get_regraNome($idRegra);
            $nomeUsuario = $intra->get_nickUsuario($id);
            $atividade = "Incluiu a permissao ao ($regra) do usuário $nomeUsuario";
            $servidor = $intra->get_idServidor($id);
            
            # Inclui a nova permissao
            $intra->set_tabela('tbpermissao');     # a tabela
            $intra->set_idCampo('idPermissao');    # o nome do campo id
            $campos = array("idRegra","idUsuario");
            $valor = array($idRegra,$id);
            $intra->gravar($campos,$valor,NULL,NULL,NULL,FALSE);
            
            $idPermissao = $intra->get_lastId();
            
            # Grava no log a atividade
            $data = date("Y-m-d H:i:s");
            $intra->registraLog($idUsuario,$data,$atividade,'tbpermissao',$idPermissao,1,$servidor);	
            
            loadPage ('?fase=exibePermissao&id='.$id);
            break;

        ##########################################################################################	
        
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("login.php");
}