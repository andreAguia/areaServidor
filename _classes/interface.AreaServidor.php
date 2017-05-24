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
        
        # Verifica se a imagem é comemorativa
        if(date("d.m") == "08.03"){
        	$imagem = new Imagem(PASTA_FIGURAS.'uenf_mulher.jpg','Dia Internacional da Mulher',190,60);
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
            if(HTML5){
                p('Versão: '.VERSAO.' (HTML5)','versao');
            }else{
                p('Versão: '.VERSAO,'versao');
            }
        $grid->fechaColuna();
        $grid->abreColuna(4);
            p(BROWSER_NAME." - ".IP." (".MAC.") - ".HOST,'ip');
        $grid->fechaColuna();
        $grid->fechaGrid();
    }
}