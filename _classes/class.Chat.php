<?php
class Chat{
 /**
  * Abriga as várias rotina do Sistema de Cominicação
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  */
    
    private $projeto = NULL;
    
    ###########################################################
    
    /**
    * Método Construtor
    */
    public function __construct(){
        
    }

    ###########################################################
    
    public function exibe_mensagens($data = NULL){
    /**
     * Exibe as mensagens do dia informado
     * 
     * @param $data date NULL a data
     * 
     * @syntax $projeto->getDadosProjetos([$idProjeto]);  
     */
    
        # Pega os dados da mensagem
        $select = 'SELECT idChat,
                          idUsuario,
                          idSala,
                          data,
                          tipo,
                          mensagem
                     FROM tbchat';
        
        # Pega os dados
        $intra = new Intra();
        $row = $intra->select($select);
        
        # Inicia as variáveis
        $idUsuarioAnterior = NULL;
        
        # Inicia o grid
        $grid = new Grid();
        
        # Exibe de fato
        foreach($row as $mens){
                        
            # Pega o idPessoa do autor da mensagem
            $idPessoa = $intra->get_idPessoa($mens['idUsuario']);
            
            # Pega o nick do Usuário
            $nick = $intra->get_nickUsuario($mens['idUsuario']);
            
            if($idUsuarioAnterior <> $mens['idUsuario']){
                
                if(!is_null($idUsuarioAnterior)){
                    
                    $grid->fechaColuna();
                    $grid->fechaGrid(); 
                    hr("hrChat");
                     $grid = new Grid();
                }
               
                $grid->abreColuna(3,2,1);
                
                $figura = new Imagem(PASTA_FOTOS.$idPessoa.'.jpg',$nick,'40px','40px');
                $figura->set_id('chat');
                $figura->show();
                $idUsuarioAnterior = $mens['idUsuario'];
                               
                $grid->fechaColuna();
                $grid->abreColuna(9,10,11);
            }
            
            p($mens['mensagem'],"pchatMensagem");
           
        }
        $grid->fechaColuna();
        $grid->fechaGrid();        
    }
    
    ###########################################################
}