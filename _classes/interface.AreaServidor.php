<?php
 /**
 * classe Areaservidor
 * Encapsula as rotinas da Área do Servidor
 * 
 * By Alat
 */
 
 class AreaServidor{	
    /**
     * Método cabecalho
     * 
     * Exibe o cabecalho
     */     
    public static function cabecalho($titulo = NULL){        
        # tag do cabeçalho
        echo '<header>';
        
        # Verifica se a imagem é comemorativa
        $dia = date("d");
        $mes = date("m");
        
        if(($dia == 8)AND($mes == 3)){
            $imagem = new Imagem(PASTA_FIGURAS.'uenf_mulher.jpg','Dia Internacional da Mulher',190,60);
        }elseif(($mes == 12) AND ($dia < 26)){
            $imagem = new Imagem(PASTA_FIGURAS.'uenf_natal.png','Feliz Natal',200,60);
        }else{
            $imagem = new Imagem(PASTA_FIGURAS.'uenf.png','Uenf - Universidade do Norte Fluminense',190,60);
        }
		
        $cabec = new Div('center');
        $cabec->abre();            
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

    ##########################################################
    
    /**
    * Método rodape
    * Exibe oo rodapé
    * 
    * @param    string $idUsuario -> Usuário logado
    */
    public static function rodape($idUsuario) {
       
        # Exibe faixa azul
        $grid = new Grid();
        $grid->abreColuna(12);        
            titulo();        
        $grid->fechaColuna();
        $grid->fechaGrid();

        # Exibe a versão do sistema
        $intra = new Intra();
        $grid = new Grid();
        $grid->abreColuna(4);
            p('Usuário : '.$intra->get_usuario($idUsuario),'usuarioLogado');
        $grid->fechaColuna();
        $grid->abreColuna(4);
                p('Versão: '.VERSAO,'versao');
        $grid->fechaColuna();
        $grid->abreColuna(4);
            p(BROWSER_NAME." - ".IP,'ip');            
        $grid->fechaColuna();
        $grid->fechaGrid();
    }
    
    ###########################################################
    
    /**
    * Método listaDadosUsuario
    * Exibe os dados principais do servidor logado
    * 
    * @param    string $idServidor -> idServidor do servidor
    */
    public static function listaDadosUsuario($idUsuario)
    {       
        # Conecta com o banco de dados
        $servidor = new Pessoal();
        $intra = new Intra();
        
        $idServidor = $intra->get_idServidor($idUsuario);
        $nomeUsuario = $intra->get_nickUsuario($idUsuario);

        $select ='SELECT "'.$nomeUsuario.'",
                         tbpessoa.nome,
                         tbperfil.nome,
                         tbservidor.idServidor,
                         tbservidor.dtAdmissao,
                         tbservidor.idServidor,
                         tbservidor.idServidor,
                         tbservidor.dtDemissao
                    FROM tbservidor LEFT JOIN tbpessoa ON tbservidor.idPessoa = tbpessoa.idPessoa
                                       LEFT JOIN tbsituacao ON tbservidor.situacao = tbsituacao.idsituacao
                                       LEFT JOIN tbperfil ON tbservidor.idPerfil = tbperfil.idPerfil
                   WHERE idServidor = '.$idServidor;

        $conteudo = $servidor->select($select,TRUE);
        $label = array("Usuário","Servidor","Perfil","Cargo","Admissão","Lotação","Situação");
        $function = array(NULL,NULL,NULL,NULL,"date_to_php");
        $classe = array(NULL,NULL,NULL,"pessoal",NULL,"pessoal","pessoal");
        $metodo = array(NULL,NULL,NULL,"get_Cargo",NULL,"get_Lotacao","get_Situacao");
        
        $formatacaoCondicional = array( array('coluna' => 0,
                                              'valor' => $nomeUsuario,
                                              'operador' => '=',
                                              'id' => 'listaDados'));

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_conteudo($conteudo);
        $tabela->set_label($label);
        $tabela->set_funcao($function);
        $tabela->set_classe($classe);
        $tabela->set_metodo($metodo);
        $tabela->set_totalRegistro(FALSE);
        $tabela->set_formatacaoCondicional($formatacaoCondicional);
        
        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);      
        
            $tabela->show();

        $grid->fechaColuna();
        $grid->fechaGrid();        
    }

    ##########################################################
    
    /**
    * Método menuPrincipal
    * Exibe oo rodapé
    * 
    * @param    string $idUsuario -> Usuário logado
    */
    public static function menuPrincipal($idUsuario) {
       
        # Cria Grid
        $grid = new Grid();
        
        # Primeira Coluna
        $grid->abreColuna(12,6,4);
        
        # Módulos
        self::moduloSistemas($idUsuario);
                
        $grid->fechaColuna();
        
        # Terceira Coluna
        $grid->abreColuna(12,6,4);
        
        # Módulos
        self::moduloServidoresUniversidade($idUsuario);
                
        $grid->fechaColuna();
        
        # Terceira Coluna
        $grid->abreColuna(12,6,4);

        # Módulos
        self::moduloSobreServidor();
        
        $grid->fechaColuna();
        $grid->fechaGrid();
        
        
    }
        
    ###########################################################
    
    /**
     * Método moduloTabelaAuxiliares
     * 
     * Exibe o menu de Legislação
     */
    
    private static function moduloSistemas($idUsuario){
        
        $painel = new Callout();
        $painel->abre();
        
        # Título
        titulo('Sistemas');
        $tamanhoImage = 64;
        br();
        
        # Inicia o menu
        $menu = new MenuGrafico();

        # Sistema de Pessoal
        $botao = new BotaoGrafico();
        $botao->set_label('Sistema de Pessoal');
        $botao->set_url('../../grh/grhSistema/grh.php');
        $botao->set_imagem(PASTA_FIGURAS.'sistemaPessoal.png',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Acessa o Sistema de Pessoal');
        $botao->set_accesskey('P');
        $menu->add_item($botao);


        # Sistema de Processos
        if(Verifica::acesso($idUsuario,5)){                    
            $botao = new BotaoGrafico();
            $botao->set_label('Sistema de Processos');
            $botao->set_url('processo.php');
            $botao->set_imagem(PASTA_FIGURAS.'processo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Sistema de controle de processos');
            $botao->set_target("_blank");
            $menu->add_item($botao);
        }

        # Controle de pastas Digitalizadas
        if(Verifica::acesso($idUsuario,4)){
            $botao = new BotaoGrafico();
            $botao->set_label('Pastas Digitalizadas');
            $botao->set_url('?fase=pastasDigitalizadas');
            #$botao->set_url('pastaDigitalizada.php');
            $botao->set_imagem(PASTA_FIGURAS.'pasta.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Sistema de controle de pastas digitalizadas');
            $botao->set_accesskey('D');
            $menu->add_item($botao);
        }

        $menu->show();
        $painel->fecha();
    }
        
    ###########################################################
    
    /**
     * Método moduloTabelaAuxiliares
     * 
     * Exibe o menu de Legislação
     */
    
    private static function moduloServidoresUniversidade($idUsuario){
        
        $painel = new Callout();
        $painel->abre();
        
        titulo('Servidores da Universidade');
        $tamanhoImage = 64;
        br();
            
        if(Verifica::acesso($idUsuario,3)){
            $menu = new MenuGrafico(4);

            $botao = new BotaoGrafico();
            $botao->set_label('Geral');
            $botao->set_url('servidorGeral.php');
            $botao->set_imagem(PASTA_FIGURAS.'admin.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Lista geral de servidores');
            $menu->add_item($botao);
        }else{
            $menu = new MenuGrafico(3);
        }

        $botao = new BotaoGrafico();
        $botao->set_label('por Lotação');
        $botao->set_url('servidorLotacao.php');
        $botao->set_imagem(PASTA_FIGURAS.'computador.png',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Lista de servidores por lotação');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('por Cargo Efetivo');
        $botao->set_url('servidorCargo.php');
        $botao->set_imagem(PASTA_FIGURAS.'cracha.png',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Lista de servidores por cargo efetivo');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('por Cargo em Comissão');
        $botao->set_url('servidorCargoComissao.php');
        $botao->set_imagem(PASTA_FIGURAS.'comissao.png',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Lista de servidores por cargo em comissão');
        $menu->add_item($botao);
        $menu->show();
            
        $painel->fecha();
    }
        
    ###########################################################
    
    /**
     * Método moduloTabelaAuxiliares
     * 
     * Exibe o menu de Legislação
     */
    
    private static function moduloSobreServidor(){
        
        $painel = new Callout();
        $painel->abre();
        
        titulo('Sobre o Servidor');
        $tamanhoImage = 64;
        br();

        $menu = new MenuGrafico();

        $botao = new BotaoGrafico();
        $botao->set_label('Histórico de Licença');
        $botao->set_url('?fase=historicoLicenca');
        $botao->set_imagem(PASTA_FIGURAS.'licenca.jpg',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Exibe o seu histórico de licenças e afastamentos');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Histórico de Férias');
        $botao->set_url('?fase=historicoFerias');
        $botao->set_imagem(PASTA_FIGURAS.'ferias.jpg',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Exibe o seu histórico de férias');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Férias do seu Setor');
        $botao->set_url('?fase=feriasSetor');
        $botao->set_imagem(PASTA_FIGURAS.'feriasSetor.png',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Exibe as férias dos servidores do seu setor');
        $menu->add_item($botao);

        $menu->show();            
        $painel->fecha();
    }
        
    ###########################################################
    
    /**
     * Método moduloTabelaAuxiliares
     * 
     * Exibe o menu de Legislação
     */
    
    private static function modulolinksExternos(){
        
        $painel = new Callout();
        $painel->abre();
        
        tituloTable('Sobre o Servidor');
        $tamanhoImage = 64;

        $menu = new MenuGrafico();

        $botao = new BotaoGrafico();
        $botao->set_label('Histórico de Licença');
        $botao->set_url('?fase=historicoLicenca');
        $botao->set_imagem(PASTA_FIGURAS.'licenca.jpg',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Exibe o seu histórico de licenças e afastamentos');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Histórico de Férias');
        $botao->set_url('?fase=historicoFerias');
        $botao->set_imagem(PASTA_FIGURAS.'ferias.jpg',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Exibe o seu histórico de férias');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Férias do seu Setor');
        $botao->set_url('?fase=feriasSetor');
        $botao->set_imagem(PASTA_FIGURAS.'feriasSetor.png',$tamanhoImage,$tamanhoImage);
        $botao->set_title('Exibe as férias dos servidores do seu setor');
        $menu->add_item($botao);

        $menu->show();            
        $painel->fecha();
    }
    
    ###########################################################
}