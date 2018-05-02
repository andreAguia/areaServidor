<?php

class Gprocessos{
 /**
  * Encapsula as rotinas da interface com o usuário do sistema de Controle de Processos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  */
     
#########################################################
    
    public static function exibeProcesso($idProcesso){
    /**
    * Exibe uma tela com os dados de um processo
    * 
    * @syntax Gprocessos::exibeProcesso($isProcesso);
    * 
    * @param $idProcesso integer NULL o id do processo a ser exibido.
    */    
        
        # Pega os dados
        $select = 'SELECT idProcesso,
                          numero,
                          data,
                          assunto
                     FROM tbprocesso
                     WHERE idProcesso = '.$idProcesso;
        
        $intra = new Intra();
        $row = $intra->select($select,FALSE);
        
        $grid = new Grid("center");
        $grid->abreColuna(4);
        $grid->fechaColuna();
        $grid->abreColuna(4);
            $div = new Div("divTituloProcesso");
            $div->abre();
                p($row[1],"pNumeroProcesso");
                p(date_to_php($row[2]." "),"right","f14");
            $div->fecha();
        $grid->fechaColuna();
        $grid->abreColuna(4);
        $grid->fechaColuna();
        $grid->fechaGrid();
    }
   
    
}