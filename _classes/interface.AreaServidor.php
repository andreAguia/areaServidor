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
    * método listaDadosServidor
    * Exibe os dados principais do servidor logado
    * 
    * @param    string $idServidor -> idServidor do servidor
    */
    public static function listaDadosUsuario($idUsuario)
    {       
        # Conecta com o banco de dados
        $servidor = new Pessoal();

        $select ='SELECT idUsuario,
                         usuario,
                         if(tipoUsuario=2,areaServidor.tbusuario.nome,grh.tbpessoa.nome),
                         if(tipoUsuario=2,"Bolsista","Servidor")               
                    FROM areaServidor.tbusuario LEFT JOIN grh.tbservidor USING (idservidor)
                                                LEFT JOIN grh.tbpessoa USING (idPessoa)
                   WHERE idUsuario = '.$idUsuario;

        $conteudo = $servidor->select($select,true);
        
        $formatacaoCondicional = array( array('coluna' => 0,
                                              'valor' => $idUsuario,
                                              'operador' => '=',
                                              'id' => 'listaDados'));

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_conteudo($conteudo);
        $tabela->set_label(array("Id","Usuário","Nome","Perfil"));
        $tabela->set_width(array(5,20,45,30));        
        $tabela->set_totalRegistro(false);
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
    * método rodape
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
            p(BROWSER_NAME." - ".IP." (".MAC.")",'ip');
        $grid->fechaColuna();
        $grid->fechaGrid();
    }
}