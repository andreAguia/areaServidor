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
            $imagem = new Imagem(PASTA_FIGURAS.'uenf_natal.jpg','Feliz Natal',200,60);
        }else{
            $imagem = new Imagem(PASTA_FIGURAS.'uenf.jpg','Uenf - Universidade do Norte Fluminense',190,60);
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

    ###########################################################
}