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
        
            $painel = new Callout("primary");
            $painel->abre();
                # Dados do Processo
                p($row[1],"pNumeroProcesso");
                p(date_to_php($row[2]),"pDataProcesso");
                p($row[3],"pAssuntoProcesso");
            $painel->fecha();
        
    }
   
    #########################################################
    
    public static function exibeMovimentacao($idProcesso){
    /**
    * Exibe uma tela com a movimentação de um processo
    * 
    * @syntax Gprocessos::exibeMovimentacao($isProcesso);
    * 
    * @param $idProcesso integer NULL o id do processo a ser exibido.
    */    
        
        # Pega os dados
        $select = 'SELECT status,
                          data,
                          setorCombo,
                          motivo
                     FROM tbprocessomovimento
                     WHERE idProcesso = '.$idProcesso;
        
        $intra = new Intra();
        $row = $intra->select($select,FALSE);
        
            $div = new Div("divTituloProcesso");
            $div->abre();
                p($row[1],"pNumeroProcesso");
                p(date_to_php($row[2]),"pDataProcesso");
                p($row[3],"pAssuntoProcesso");
            $div->fecha();
        
    }
}