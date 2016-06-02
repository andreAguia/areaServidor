<?php
 /**
 * classe Areaservidor
 * Encapsula as rotinas da Área do Servidor
 * 
 * By Alat
 */
 
 class AreaServidor
{	
    /**
     * Método cabecalho
     * 
     * Exibe o cabecalho
     */     
    public static function cabecalho($titulo = NULL)
    {        
        # tag do cabeçalho
        echo '<header>';
        
        $cabec = new Div('center');
        $cabec->abre();
            $imagem = new Imagem(PASTA_FIGURAS.'uenf.jpg','Área do Servidor da Uenf',190,60);
            $imagem->show();
        $cabec->fecha();       
        
        if(!(is_null($titulo))){
             br();
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);

            # Topbar        
            $top = new TopBar($titulo);
            $top->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
        }

        echo '</header>';
    }
    
    ###########################################################
       
    /**
    * método menu
    * Exibe o menu da área do servidor
    * 
    * @param $nivel string  informa o nível do usuario logado para ver 
    *                       se exibe ou não alguns menus
    */
    public static function menu($matricula)
    {
        /**
         * Menu de serviços
         */
        
        $tamanhoImage = 60;
        $permissao = new Intra();
        
        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

        $fieldset = new Fieldset('Serviços');
        $fieldset->abre();

            $menu = new MenuGrafico(3);

            $pessoa = new Pessoal();
            $perfil = $pessoa->get_idPerfil($matricula);
            
            # Sistema GRH
            if($permissao->verificaPermissao($matricula,13))
            {
                $botao = new BotaoGrafico();
                $botao->set_label('Sistema de Pessoal');
                $botao->set_title('Acessa o Sistema de Pessoal');
                $botao->set_image(PASTA_FIGURAS.'sistemaPessoal.png',$tamanhoImage,$tamanhoImage);              
                $botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='../grh/grh.php'");
                $menu->add_item($botao);
            }
                       
            # Solicitação de Férias
            if($pessoa->get_perfilFerias($perfil) == "Sim")
            {
                $botao = new BotaoGrafico();
                $botao->set_label('Solicitação de Férias');
                $botao->set_url('solicitaFerias.php');
                #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='solicitaFerias.php'");
                $botao->set_image(PASTA_FIGURAS.'solicitaFerias.jpg',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Solicita férias');                
                $menu->add_item($botao);
            }
            
            # Rotina de Alterar Senha
            $botao = new BotaoGrafico();
            $botao->set_label('Alterar Senha');
            $botao->set_url('trocarSenha.php');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='trocarSenha.php'");
            $botao->set_image(PASTA_FIGURAS.'trocarSenha.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Altera a senha de acesso');            
            $menu->add_item($botao);

            $menu->show();

        $fieldset->fecha();

        /**
        * Menu de dados do Servidor
        */
        $fieldset = new Fieldset('Dados do Servidor');
        $fieldset->abre();

            $menu = new MenuGrafico(5);

            $botao = new BotaoGrafico();
            $botao->set_label('Férias');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='?fase=grh&metodo=listaFerias'");
            $botao->set_url('?fase=grh&metodo=listaFerias');
            $botao->set_image(PASTA_FIGURAS.'ferias.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Histórico das Férias');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Licenças');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='?fase=grh&metodo=listaLicenca'");
            $botao->set_url('?fase=grh&metodo=listaLicenca');
            $botao->set_image(PASTA_FIGURAS.'licenca.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Histórico das Licenças');
            $botao->set_accessKey('L');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Progressão / Enquadramento');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='?fase=grh&metodo=listaProgressao'");
            $botao->set_url('?fase=grh&metodo=listaProgressao');
            $botao->set_image(PASTA_FIGURAS.'progressao.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Histórico das Progressões&#10;e Enquadramentos');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Triênio');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='?fase=grh&metodo=listaTrienio'");
            $botao->set_url('?fase=grh&metodo=listaTrienio');
            $botao->set_image(PASTA_FIGURAS.'trienio.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Histórico dos Triênios');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Lotações');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='?fase=grh&metodo=listaLotacao'");
            $botao->set_url('?fase=grh&metodo=listaLotacao');
            $botao->set_image(PASTA_FIGURAS.'lotacao.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Histórico das Lotações');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Cargos em Comissão');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='?fase=grh&metodo=listaComissao'");
            $botao->set_url('?fase=grh&metodo=listaComissao');
            $botao->set_image(PASTA_FIGURAS.'comissao.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Histórico dos Cargos em Comissão');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Gratificação');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='?fase=grh&metodo=listaGraticifacao'");
            $botao->set_url('?fase=grh&metodo=listaGratificacao');
            $botao->set_image(PASTA_FIGURAS.'gratificacao.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Histórico das Gratificações');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Email / Telefones');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='?fase=contatos&metodo=listar'");
            $botao->set_url('?fase=contatos&metodo=listar');
            $botao->set_image(PASTA_FIGURAS.'telefone.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Email / Telefones');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Atestados');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='?fase=grh&metodo=listaAtestados'");
            $botao->set_url('?fase=grh&metodo=listaAtestados');
            $botao->set_image(PASTA_FIGURAS.'atestado.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Histórico de Atestados');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Ficha Cadastral');
            $botao->set_image(PASTA_FIGURAS.'ficha.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_target('_blank');
            $botao->set_url('../relatorios/feriasServidoresGerencia.php');
            #$botao->set_url('?fase=fichaCadastral');
            $botao->set_title('Exibe a Ficha Cadastral&#10;do Servidor');
            $menu->add_item($botao);

            $menu->show();

        $fieldset->fecha();
        
        /**
         * Menu Administração
         */
        
        if ($matricula == GOD){
            $fieldset = new Fieldset('Administração');
            $fieldset->abre();
        
            $menu = new MenuGrafico(5);
                        
            # Cadastro de Mensagem
            $botao = new BotaoGrafico();
            $botao->set_label('Avisos');
            $botao->set_title('Acessa o Sistema&#10;de Avisos e Mensagens');
            $botao->set_image(PASTA_FIGURAS.'aviso.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('avisos.php');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='../../../novaIntra/intranet/sistema/avisos.php'");
            $menu->add_item($botao);
            
            # Variáveis de Configuração
            $botao = new BotaoGrafico();
            $botao->set_label('Configurações');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='configuracoes.php'");
            $botao->set_url('variaveisSistema.php');
            $botao->set_image(PASTA_FIGURAS.'configuracao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Edita as Variáveis de&#10;configuração da Intranet');
            $menu->add_item($botao);
            
            # Administração de Usuários
            $botao = new BotaoGrafico();
            $botao->set_label('Usuários');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='usuarios.php'");
            $botao->set_url('usuarios.php');
            $botao->set_image(PASTA_FIGURAS.'usuarios.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Gerencia os Usuários na Intranet');
            $menu->add_item($botao);
            
            # Histórico Geral
            $botao = new BotaoGrafico();
            $botao->set_label('Histórico');
            $botao->set_title('Histórico Geral do Sistema');
            $botao->set_image(PASTA_FIGURAS.'historico.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('historico.php');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='log.php'");
            $menu->add_item($botao);

            # Documentação
            $botao = new BotaoGrafico();
            $botao->set_label('Documentação');
            $botao->set_title('Documentação do Sistema');
            $botao->set_image(PASTA_FIGURAS.'documentacao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentacao.php');
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='../../../novaIntra/intranet/documentacao.php'");
            $menu->add_item($botao);
            
            $menu->show();
            $fieldset->fecha();
        }
        
        # Exibe o IP
        p(BROWSER_NAME.' '.BROWSER_VERSION.' - '.$_SERVER["REMOTE_ADDR"].' - '.SO,'exibeIP');
        
        $grid->fechaColuna();
        $grid->fechaGrid();        
    }

    ###########################################################
    
    public static function listaOcorrencias($matricula)
            
    /**
     * Exibe as ocorrências dessa matrícula como
     * pendências com o grh, felicitações de aniversário, etc
     * 
     * @param   integer $matricula  -> a matrícula do servidor
     */
    
    {
        /*
         *  Verifica se está fazendo aniversário
         */
        $pessoal = new Pessoal();

        if($pessoal->aniversariante($matricula)){

            $msg = '<br/><b>Querido Servidor, Feliz Aniversário !</b><br/>A Gerência da Tecnologia da Informação te deseja<br/>paz, alegrias, felicidades e muito sucesso.';
            $panel = new Panel($msg);
        } 
        
        /*
         * Verifica pendencia de motorista com carteira vencida no sistema grh
         */
        $perfil = $pessoal->get_perfil($matricula);
        $cargo = $pessoal->get_cargo($matricula);
        $idPessoa = $pessoal->get_idPessoa($matricula);
        $dataCarteira = $pessoal->get_dataVencimentoCarteiraMotorista($idPessoa);
        
        if($perfil == 'Estatutário') // se é estatutário
        {
            if($cargo == 'Motorista') // se é motorista
            {
                if(Data::jaPassou($dataCarteira))
                {
                    # Gera o aviso
                    $mensagem = '<br/>Existe uma pendência cadastral com a Gerência de Recursos Humanos.<br/>Compareça à GRHPAG com a cópia da sua carteira de Habilitação para atualização do cadastro.';
                    $alertaAviso = new Alerta('divAviso');
                    $alertaAviso->set_titulo('Atenção Servidor: '.$pessoal->get_nome($matricula));
                    $alertaAviso->set_mensagem($mensagem);
                    $alertaAviso->show();
                }
            }
        }
        
        /*
         * Verifica Avisos
         */
    
        # Pega os avisos ativos dessa matricula
        $aviso = new Intra();
        $result = $aviso->get_avisosAtivos($matricula);
        $contagem = $aviso->get_numeroAvisosAtivos($matricula);
        $linha = 1;
        $mensagem = null;
        
        if($contagem <> 0)
        {
            # Inicia o objeto e define o título
            $alertaAviso = new Alerta('divAviso');
            $alertaAviso->set_titulo('Atenção Servidor: '.$pessoal->get_nome($matricula));
            
            # Exibe a/as mensagem(ns)
            foreach ($result as $row)
            {
                # acumula as mensagens
                $mensagem .= '<br/>'.$row['aviso'].'<br/><br/>'.$row['autor'].'<br/><br/>';
                
                # coloca uma linha divisória caso seja mais de uma mensagam
                if($contagem > $linha)
                {
                    $mensagem .= '-----------------------------------';
                    $linha++;
                }
            }
            
            $alertaAviso->set_mensagem($mensagem);
            $alertaAviso->show();
        }
    }
    ###########################################################
    
    /**
     * método linkVersao
     * Rotina que exibe um link para a versão do sistema
     */
    
    public static function linkVersao($origem = 'intranet')
    {
        
        # do link
        
       # if ($origem == 'intranet')
       #  $div = new Div("divVersao align-center");    // altera a posição da div de acordo com o sistema
        #else
        #    $div = new Div("divVersaoGrh"); // coloca a div um pouco mais abaixo

        #$div->abre();
        #    $p = new P('Versão: '.VERSAO);
        #    $p->set_onClick('abreDivId("divMensagemVersao");');
        ##    $p->set_title('Clique para saber detalhes da versão do sistema');
        #    $p->show();
        #$div->fecha();
        
        echo '<p id="center"> Versão: '.VERSAO.'</p>';
        
        # da janela
        $div = new Box("divMensagemVersao");
        $div->set_titulo('Sobre o Sistema');
        $div->abre();
        
        br();

        # Exibe o Nome do sistema
        if ($origem == 'intranet')
            $p = new P(SISTEMA,'f18');
        else
            $p = new P(SISTEMA_GRH,'f18');
        $p->show();	

        br();
        
        # Informa a versão
        $p = new P('Versão: '.VERSAO,'f12');
        $p->show();	

        $p = new P('atualizado em: '.ATUALIZACAO,'f10');
        $p->show();	

        br();

        $p = new P('GTI - Gerência da Tecnologia da Informação','f14');        
        $p->show();	

        br();

        $p = new P('Desenvolvedor: André Luís Águia Tavares (Alat)','f12');
        $p->show();
        
        # Informa se a data está em modo html5 ou não
        if(HTML5)
        {
            $p = new P('(HTML5)','f12');
            $p->show();
        }	
        
        #$menu = new MenuGrafico(1,'logoFenorte');            

        # Logo Fenorte // Dando erro de localização
        #$botao = new BotaoGrafico();
        #$botao->set_label('');
        #$botao->set_title('Fenorte');
        #$botao->set_image(PASTA_FIGURAS.'logoFenorte.gif');
        #$menu->add_item($botao);
            
        #$menu->show();
        
        $div->fecha();
    }    
    
    ###########################################################


    /**
    * método listaFerias
    * Exibe o histórico de férias de um servidor
    * 
    * @param    $matricula  string  a matricula do servidor que será exibido o histórico
    * @param    $ordem      string  ordenação da tabela
    */
    public static function listaFerias($matricula,
                                       $orderCampo = null,
                                       $orderTipo = null,
                                       $orderChamador = null,                                       
                                       $botaoExcluir = false)
    
    {
        $titulo = 'Histórico de Férias';
        $select = 'SELECT anoExercicio,
                          status,
                          dtInicial,
                          numDias,
                          periodo,
                          ADDDATE(dtInicial,numDias-1),
                          documento,
                          folha,
                          status,
                          idFerias
             FROM tbferias
            WHERE matricula='.$matricula;

        $orderCampoPadrao = 3;
        $orderTipoPadrao = 'desc'   ;

        $label = array("Exercicio","Status","Data Inicial","Dias","P","Data Final","Documento 1/3","Folha","2º Via");
        $width = array(10,10,15,5,5,15,20,10,5);    
        $align = array("center");
        $function = array(null,null,'date_to_php',null,null,'date_to_php');
        $idCampo = 'idFerias';
        
        # Define se terá botão excluir ou não
        if($botaoExcluir)
        {   # com botão excluir - Rotina de solicitação de férias normal
            #$excluirCondicional = 'solicitaFerias.php?fase=apagaFerias';
            #$excluirColuna = 1;
            #$excluirCondicao = 'solicitada'; 
            
            # Rotina acima foi alterada a pedido do grh 
            # que não mais deseja que o servidor exclua
            # as férias cadadastradas. Somente o GRH poderá excluir.
            # Assim sendo essa rotina somente exibe o link para segunda via da solicitação
            $excluirCondicional = null;
            $excluirColuna = null;
            $excluirCondicao = null;
                
            # link para segunda via da solicitação
            $linkObjeto = new Link('padrao','?fase=emitePdf&id=');
            $linkObjeto->set_image(PASTA_FIGURAS.'printer.png');
            $linkObjeto->set_title('Emite uma segunda via da solicitação');
            #$link->set_confirma('Você deseja realmente redefinir esse senha para a senha padrão?');

            # Coloca o objeto link na tabela            
            $link = array("","","","","","","","",$linkObjeto);
            $linkCondicional = array("","","","","","","","","solicitada");
        }
        else
        {   # sem botão de excluir - rotina de exibição de histórico de férias
            $excluirCondicional = null;
            $excluirColuna = null;
            $excluirCondicao = null;
            
            $link = null;
            $linkCondicional = null;
            
            $label = array("Exercicio","Status","Data Inicial","Dias","P","Data Final","Documento 1/3","Folha");
            
            # log
            $data = date("Y-m-d H:i:s");
            $atividade = 'Vizualizou a sua própria listagem de férias';
            $intra = new Intra();
            $intra->registraLog($matricula,$data,$atividade);
        }
        
        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);
        
        # Botão voltar
        AreaServidor::botaoVoltar('areaServidor.php');

        # Conecta com o banco de dados
        $servidor = new Pessoal();

        if(is_null($orderCampo))
            $orderCampo = $orderCampoPadrao;

        if(is_null($orderTipo))
            $orderTipo = $orderTipoPadrao;

        $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo; 

        $result = $servidor->select($select,true,$orderCampo,$orderTipo);

        # Verifica se tem registros a serem exibidos
        if(count($result) == 0){
            $msg = new Alert('Nenhum item encontrado !!','center');
            $msg->show();
        }
        else{
            # Monta a tabela
            $tabela = new tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label);
            $tabela->set_width($width);
            $tabela->set_funcao($function);
            $tabela->set_titulo($titulo);
            $tabela->set_order($orderCampo,$orderTipo,$orderChamador);
            $tabela->set_excluirCondicional($excluirCondicional,$excluirCondicao,$excluirColuna);
            $tabela->set_idCampo($idCampo);

            if(!is_null($link))
                $tabela->set_link($link);

            if(!is_null($linkCondicional))
                $tabela->set_linkCondicional($linkCondicional);

            $tabela->show();
        }        
        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    /**
    * método listaLicenca
    * Exibe o histórico de licenca de um servidor
    * 
    * @param    $matricula  string  a matricula do servidor que será exibido o histórico
    */
    public static function listaLicenca($matricula,$orderCampo = null,$orderTipo = null,$orderChamador = null)
    {

        $titulo = 'Histórico de Licenças';
        $select = 'SELECT tbtipolicenca.nome,
                          tblicenca.dtInicial,
                          tblicenca.numdias,
                          ADDDATE(tblicenca.dtInicial,tblicenca.numDias-1),
                          tblicenca.processo,
                          concat(date_format(tblicenca.dtPublicacao,"%d/%m/%Y")," - Pag.",pgPublicacao)
                     FROM tblicenca LEFT JOIN tbtipolicenca ON tblicenca.idTpLicenca = tbtipolicenca.idTpLicenca
                    WHERE matricula = '.$matricula;

        $orderCampoPadrao = 2;
        $orderTipoPadrao = 'desc'   ;
        $label = array("Licença","Inicio","Dias","Término","Processo","Publicação DOERJ");
        $width = array(30,10,10,10,15,20);      
        $align = array("center");
        $function = array(null,"date_to_php",null,"date_to_php");
        
        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);
        
        # Botão voltar
        AreaServidor::botaoVoltar('areaServidor.php');        
        
        # Conecta com o banco de dados
        $servidor = new Pessoal();

        if(is_null($orderCampo))
            $orderCampo = $orderCampoPadrao;

        if(is_null($orderTipo))
            $orderTipo = $orderTipoPadrao;

        $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo; 

        $result = $servidor->select($select,true,$orderCampo,$orderTipo);
        
        # log
        $data = date("Y-m-d H:i:s");
        $atividade = 'Vizualizou a sua própria listagem de licenças';
        $intra = new Intra();
        $intra->registraLog($matricula,$data,$atividade);
            
        # Verifica se tem registros a serem exibidos
        if(count($result) == 0){
            $msg = new Alert('Nenhum item encontrado !!','center');
            $msg->show();
        }
        else{
            # Monta a tabela
            $tabela = new tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label);
            $tabela->set_width($width);
            $tabela->set_funcao($function);
            $tabela->set_titulo($titulo);
            $tabela->set_order($orderCampo,$orderTipo,$orderChamador);
            $tabela->show();
        }              
        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    /**
    * método listaProgressao
    * Exibe o histórico de progressao de um servidor
    * 
    * @param    $matricula  string  a matricula do servidor que será exibido o histórico
    */
    public static function listaProgressao($matricula,$orderCampo = null,$orderTipo = null,$orderChamador = null)
    {

        $titulo = 'Histórico de Progressões e Enquadramentos';
        $select = 'SELECT tbprogressao.dtInicial,
                          tbtipoprogressao.nome,
                          CONCAT(tbclasse.faixa," - ",tbclasse.valor) as vv,
                          numProcesso,
                          concat(date_format(dtPublicacao,"%d/%m/%Y")," - Pag ",pgPublicacao)
                     FROM tbprogressao, tbtipoprogressao, tbclasse
                    WHERE tbprogressao.idTpProgressao = tbtipoprogressao.idTpProgressao AND
                          tbprogressao.idClasse = tbclasse.idClasse AND
                          matricula = '.$matricula;

        $orderCampoPadrao = 1;
        $orderTipoPadrao = 'desc'   ;

        $label = array("Data Inicial","Tipo de aumento","Valor","Processo","DOERJ");
        $width = array(15,25,20,20,20); 
        $align = array("center");
        $function = array('date_to_php');

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);
        
        # Botão voltar
        AreaServidor::botaoVoltar('areaServidor.php');   

        # Conecta com o banco de dados
        $servidor = new Pessoal();

        if(is_null($orderCampo))
            $orderCampo = $orderCampoPadrao;

        if(is_null($orderTipo))
            $orderTipo = $orderTipoPadrao;

        $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo; 

        $result = $servidor->select($select,true,$orderCampo,$orderTipo);
        
        # log
        $data = date("Y-m-d H:i:s");
        $atividade = 'Vizualizou a sua própria listagem de progressão';
        $intra = new Intra();
        $intra->registraLog($matricula,$data,$atividade);
            
        # Verifica se tem registros a serem exibidos
        if(count($result) == 0){
            $msg = new Alert('Nenhum item encontrado !!','center');
            $msg->show();
        }
        else{
            # Monta a tabela
            $tabela = new tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label);
            $tabela->set_width($width);
            $tabela->set_funcao($function);
            $tabela->set_titulo($titulo);
            $tabela->set_order($orderCampo,$orderTipo,$orderChamador);
            $tabela->show();
        }
        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    /**
    * método listaTrienio
    * Exibe o histórico de trienio de um servidor
    * 
    * @param    $matricula  string  a matricula do servidor que será exibido o histórico
    */
    public static function listaTrienio($matricula,$orderCampo = null,$orderTipo = null,$orderChamador = null)
    {
        $titulo = 'Histórico de Triênios';
        $select = 'SELECT dtInicial,
                          percentual,
                          Concat(date_format(dtInicioPeriodo,"%d/%m/%Y")," - ",date_format(dtFimPeriodo,"%d/%m/%Y")),
                          numProcesso,
                          concat(date_format(dtPublicacao,"%d/%m/%Y")," - Pag ",pgPublicacao)
                     FROM tbtrienio
                    WHERE matricula = '.$matricula;

        $orderCampoPadrao = 1;
        $orderTipoPadrao = 'desc'   ;

        $label = array("a partir de","%","Período Aquisitivo","Processo","DOERJ");
        $width = array(10,5,25,20,20,20);
        $align = array("center");
        $function = array('date_to_php');

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);
        
        # Botão voltar
        AreaServidor::botaoVoltar('areaServidor.php'); 
        
        # Conecta com o banco de dados
        $servidor = new Pessoal();

        if(is_null($orderCampo))
            $orderCampo = $orderCampoPadrao;

        if(is_null($orderTipo))
            $orderTipo = $orderTipoPadrao;

        $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo; 

        $result = $servidor->select($select,true,$orderCampo,$orderTipo);
        
        # log
        $data = date("Y-m-d H:i:s");
        $atividade = 'Vizualizou a sua própria listagem de triênio';
        $intra = new Intra();
        $intra->registraLog($matricula,$data,$atividade);
            
        # Verifica se tem registros a serem exibidos
        if(count($result) == 0){
            $msg = new Alert('Nenhum item encontrado !!','center');
            $msg->show();
        }
        else{
            # Monta a tabela
            $tabela = new tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label);
            $tabela->set_width($width);
            $tabela->set_funcao($function);
            $tabela->set_titulo($titulo);
            $tabela->set_order($orderCampo,$orderTipo,$orderChamador);
            $tabela->show();
        }
        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    /**
    * método listaGratificacao
    * Exibe o histórico de trienio de um servidor
    * 
    * @param    $matricula  string  a matricula do servidor que será exibido o histórico
    */
    public static function listaGratificacao($matricula,$orderCampo = null,$orderTipo = null,$orderChamador = null)
    {
        $titulo = 'Histórico de Gratificação';
        $select = 'SELECT dtInicial,
                          dtFinal,
                          valor,
                          processo
                     FROM tbgratif
                    WHERE matricula = '.$matricula;

        $orderCampoPadrao = 1;
        $orderTipoPadrao = 'desc'   ;

        $label = array("Data Inicial","Data Final","Valor","Processo");
        $width = array(25,25,25,25);
        $align = array("center");
        $function = array('date_to_php','date_to_php','get_Moeda');

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);
        
        # Botão voltar
        AreaServidor::botaoVoltar('areaServidor.php');

        # Conecta com o banco de dados
        $servidor = new Pessoal();

        if(is_null($orderCampo))
            $orderCampo = $orderCampoPadrao;

        if(is_null($orderTipo))
            $orderTipo = $orderTipoPadrao;

        $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo; 

        $result = $servidor->select($select,true,$orderCampo,$orderTipo);
        
        # log
        $data = date("Y-m-d H:i:s");
        $atividade = 'Vizualizou a sua própria listagem de gratificação especial';
        $intra = new Intra();
        $intra->registraLog($matricula,$data,$atividade);
            
        # Verifica se tem registros a serem exibidos
        if(count($result) == 0){
            $msg = new Alert('Nenhum item encontrado !!','center');
            $msg->show();
        }
        else{
            # Monta a tabela
            $tabela = new tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label);
            $tabela->set_width($width);
            $tabela->set_funcao($function);
            $tabela->set_titulo($titulo);
            $tabela->set_order($orderCampo,$orderTipo,$orderChamador);
            $tabela->show();
        }
        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    /**
    * método listaLotacao
    * Exibe o histórico de lotações de um servidor
    * 
    * @param    $matricula  string  a matricula do servidor que será exibido o histórico
    */
    public static function listaLotacao($matricula,$orderCampo = null,$orderTipo = null,$orderChamador = null)
    {
        $titulo = 'Histórico de Lotações';
        $select = 'SELECT tbhistlot.data,
                          concat(tblotacao.UADM,"-",tblotacao.DIR,"-",tblotacao.GER) as lotacao,
                          tbhistlot.motivo
                     FROM tblotacao left join tbhistlot on tbhistlot.lotacao = tblotacao.idLotacao
                    WHERE tbhistlot.matricula = '.$matricula;

        $orderCampoPadrao = 1;
        $orderTipoPadrao = 'desc'   ;

        $label = array("a partir de","%","Período Aquisitivo","Processo","DOERJ");
        $label = array("Data","Lotação","Motivo");
        $width = array(10,40,40);
        $align = array("center");
        $function = array('date_to_php');

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);
        
        # Botão voltar
        AreaServidor::botaoVoltar('areaServidor.php'); 

        # Conecta com o banco de dados
        $servidor = new Pessoal();

        if(is_null($orderCampo))
            $orderCampo = $orderCampoPadrao;

        if(is_null($orderTipo))
            $orderTipo = $orderTipoPadrao;

        $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo; 

        $result = $servidor->select($select,true,$orderCampo,$orderTipo);
        
        # log
        $data = date("Y-m-d H:i:s");
        $atividade = 'Vizualizou a sua própria listagem de lotações';
        $intra = new Intra();
        $intra->registraLog($matricula,$data,$atividade);
            
        # Verifica se tem registros a serem exibidos
        if(count($result) == 0){
            $msg = new Alert('Nenhum item encontrado !!','center');
            $msg->show();
        }
        else{
            # Monta a tabela
            $tabela = new tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label);
            $tabela->set_width($width);
            $tabela->set_funcao($function);
            $tabela->set_titulo($titulo);
            $tabela->set_order($orderCampo,$orderTipo,$orderChamador);
            $tabela->show();
        }
        $grid->fechaColuna();
        $grid->fechaGrid();
        
    }

    ###########################################################

    /**
    * método listaComissao
    * Exibe o histórico de cargo em comissão de um servidor
    * 
    * @param    $matricula  string  a matricula do servidor que será exibido o histórico
    */
    public static function listaComissao($matricula,$orderCampo = null,$orderTipo = null,$orderChamador = null)
    {
        $titulo = 'Histórico de Cargo em Comissão';
        $select = 'SELECT concat(tbtipocomissao.descricao," - (",tbtipocomissao.simbolo,")") as comissao,
                          tbcomissao.descricao,
                          tbcomissao.dtNom,
                          tbcomissao.dtExo
                 FROM tbcomissao left join tbtipocomissao on tbcomissao.idTipoComissao = tbtipocomissao.idTipoComissao
                WHERE matricula ='.$matricula;

        $orderCampoPadrao = 3;
        $orderTipoPadrao = 'desc'   ;

        $label = array("Cargo","Descrição","Data de Nomeação","Data de Exoneração");
        $width = array(30,30,10,10);
        $align = array("center");
        $function = array(null,null,'date_to_php','date_to_php');

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);
        
        # Botão voltar
        AreaServidor::botaoVoltar('areaServidor.php'); 

        # Conecta com o banco de dados
        $servidor = new Pessoal();

        if(is_null($orderCampo))
            $orderCampo = $orderCampoPadrao;

        if(is_null($orderTipo))
            $orderTipo = $orderTipoPadrao;

        $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo; 

        $result = $servidor->select($select,true,$orderCampo,$orderTipo);
            
        # log
        $data = date("Y-m-d H:i:s");
        $atividade = 'Vizualizou a sua própria listagem de cargos em comissão';
        $intra = new Intra();
        $intra->registraLog($matricula,$data,$atividade);

        # Verifica se tem registros a serem exibidos
        if(count($result) == 0){
            $msg = new Alert('Nenhum item encontrado !!','center');
            $msg->show();
        }
        else{
            # Monta a tabela
            $tabela = new tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label);
            $tabela->set_width($width);
            $tabela->set_funcao($function);
            $tabela->set_titulo($titulo);
            $tabela->set_order($orderCampo,$orderTipo,$orderChamador);
            $tabela->show();
        }
        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    /**
    * método listaAtestados
    * Exibe os contatos (telefones, email, etc) de um servidor
    * 
    * @param    $matricula  string  a matricula do servidor que será exibido o histórico
    */
    public static function listaAtestados($matricula,$orderCampo = null,$orderTipo = null,$orderChamador = null)
    {
        $titulo = 'Histórico de Atestados';
        $select = 'SELECT dtInicio,
                          numDias,
                          ADDDATE(dtInicio,numDias-1),
                          tipo,
                          nome_medico,
                          especi_medico
                     FROM tbatestado 
                    WHERE matricula = '.$matricula;

        $orderCampoPadrao = 1;
        $orderTipoPadrao = 'desc'   ;

        $label = array("Data Inicial","Dias","Data Término","Tipo","Médico","Especialidade"); 
        $width = array(15,5,15,15,30,20);
        $align = array("center");
        $function = array('date_to_php',null,'date_to_php');

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);
        
        # Botão voltar
        AreaServidor::botaoVoltar('areaServidor.php'); 

        # Conecta com o banco de dados
        $servidor = new Pessoal();

        if(is_null($orderCampo))
            $orderCampo = $orderCampoPadrao;

        if(is_null($orderTipo))
            $orderTipo = $orderTipoPadrao;

        $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo; 

        $result = $servidor->select($select,true,$orderCampo,$orderTipo);
        
        # log
        $data = date("Y-m-d H:i:s");
        $atividade = 'Vizualizou a sua própria listagem de atestados médicos';
        $intra = new Intra();
        $intra->registraLog($matricula,$data,$atividade);
            
        # Verifica se tem registros a serem exibidos
        if(count($result) == 0){
            $msg = new Alert('Nenhum item encontrado !!','center');
            $msg->show();
        }
        else{
            # Monta a tabela
            $tabela = new tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label);
            $tabela->set_width($width);
            $tabela->set_funcao($function);
            $tabela->set_titulo($titulo);
            $tabela->set_order($orderCampo,$orderTipo,$orderChamador);
            $tabela->show();
        }
        $grid->fechaColuna();
        $grid->fechaGrid();
    }   

    ###########################################################

    /**
    * método listaContatos
    * gera uma lista dos contatos de uma matrícula
    *
    * @param    $matricula  matrícula do servidor
    * @param    $metodo     o método da classe modelo
    * @param    $id         id quando edita ou exclui para passar para classe modelo
    */

    public static function listaContatos($matricula,$metodo,$id,$orderCampo=null,$orderTipo=null)
    {

        # Acessa o banco de dados
        $servidor = new Pessoal();

        # Pega o idPessoa da matricula
        $idPessoa = $servidor->get_idPessoa($matricula);

        # abre um novo objeto 
        $objeto = new Modelo();

        # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
        $objeto->set_nome('Emails, Telefones e Celulares do Servidor');

        # botão de voltar da lista
        $objeto->set_voltarLista('?');
        
        # botão salvar
        $objeto->set_botaoSalvarGrafico(false);

        # ordenação
        if(is_null($orderCampo))
                $orderCampo = 1;

        if(is_null($orderTipo))
                $orderTipo = 'asc';

        # select da lista
        $objeto->set_selectLista('SELECT tipo,
                                         numero,
                                         idContatos
                                    FROM tbcontatos
                                   WHERE idPessoa = '.$idPessoa.'                                 
                                ORDER BY '.$orderCampo.' '.$orderTipo); 

        # select do edita
        $objeto->set_selectEdita('SELECT tipo,
                                         numero,
                                         idPessoa
                                    FROM tbcontatos
                                   WHERE idContatos = '.$id);

        # ordem da lista
        $objeto->set_orderCampo($orderCampo);
        $objeto->set_orderTipo($orderTipo);
        $objeto->set_orderChamador('?fase=contatos&metodo=listar');

        # botão de voltar do form
        $objeto->set_voltarForm('?fase=contatos&metodo=listar');

        # Caminhos
        $objeto->set_linkEditar('?fase=contatos&metodo=editar');
        $objeto->set_linkExcluir('?fase=contatos&metodo=excluir');
        $objeto->set_linkGravar('?fase=contatos&metodo=gravar');
        $objeto->set_linkListar('?fase=contatos&metodo=listar');

        # Parametros da tabela
        $objeto->set_label(array("Tipo","Número"));
        $objeto->set_width(array(40,40));       
        $objeto->set_align(array("left","left"));

        # Classe do banco de dados
        $objeto->set_classBd('Pessoal');

        # Nome da tabela
        $objeto->set_tabela('tbcontatos');

        # Nome do campo id
        $objeto->set_idCampo('idContatos');

        # Tipo de label do formulário
        $objeto->set_formLabelTipo(1);

        # Foco do form
        $objeto->set_formFocus('tipo');

        # Campos para o formulario
        $objeto->set_campos(array( 
                            array ('nome' => 'tipo',
                                   'label' => 'Tipo:',
                                   'tipo' => 'combo',
                                   'array' => array("","Celular","E-mail","Residencial","Trabalho","Outros"),
                                   'size' => 50,
                                   'title' => 'Tipo do Contato. Exemplo: Email, Telrfone Residencial, Celular, etc',
                                   'required' => true,
                                   'linha' => 1),
                            array ('nome' => 'numero',
                                   'label' => 'Número:',
                                   'tipo' => 'texto',
                                   'size' => 50,
                                   'title' => 'Numero do Telefone, Celular o o endereço de Email.',
                                   'required' => true,
                                   'linha' => 2),
                            array ('nome' => 'idPessoa',
                                   'label' => 'IdPessoa:',
                                   'tipo' => 'hidden',
                                   'size' => 50,
                                   'linha' => 3,
                                   'padrao' => $idPessoa)                    
                        ));


        # Matrícula para o Log
        $objeto->set_matricula($matricula); 

        ################################################################
        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);
        
        switch ($metodo)
        {
            case "" :
            case "listar" :
                $info = new Alert('<h6>Caro Servidor !</h6>
                                   O Cadastro de Emails e Telefones é o único cadastro da GRH
                                   em que é permitida a sua alteração completa pela intranet. 
                                   Mantenha o seu cadastro sempre atualizado.','calloutRamalServidor');
                $info->set_tipo('warning');
                $info->show();
                
                # log
                $data = date("Y-m-d H:i:s");
                $atividade = 'Vizualizou a sua própria listagem de contatos';
                $intra = new Intra();
                $intra->registraLog($matricula,$data,$atividade);
            
                $objeto->listar();             
                break;

            case "editar" : 
            case "excluir" :    
            case "gravar" :
                $objeto->$metodo($id);
                break;

        }
        $grid->fechaColuna();
        $grid->fechaGrid();
    }
    
    ###########################################################
    
    /**
     * método linkTagHtml
     * Rotina que exibe um link para abrir um quadro(div) com as tags aceitas em um campo (html)
     */
    
    public static function linkTagHtml()
    {
        
        # do link
        $div = new Div("divLinkTagHtml"); 
        $div->abre();
            $p = new P('Tags Aceitas','f11');
            $p->set_onClick('abreDivId("divMensagemTag");');
            $p->set_title('Clique para saber as tags HTML aceitas');
            $p->show();
        $div->fecha();
        
        # da janela
        $div = new Box("divMensagemTag");
        $div->set_titulo('Tags aceitas');
        $div->abre();
        
        $controle = new Input('tag','textarea','',1);
        $controle->set_size(array(60,8));
        $controle->set_linha(1);
        $controle->set_valor(TAGS);
        $controle->set_readonly(true);
        $controle->show();
        
        $div->fecha();
    }    
    
    ###########################################################
    
    public static function botaoVoltar($caminho)
    {
        # Botão voltar
        $linkBotaoVoltar = new Link("Voltar",$caminho);
        $linkBotaoVoltar->set_class('button float-left');
        $linkBotaoVoltar->set_title('Volta para a página anterior');
        $linkBotaoVoltar->set_accessKey('V');

        # Cria um menu
        $menu = new MenuBar();
        $menu->add_link($linkBotaoVoltar,"left");
        $menu->show();
    }
}
