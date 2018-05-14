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
        
            $painel = new Callout();
            $painel->abre();
                
                # Botão Editar
                $grid = new Grid();
                $grid->abreColuna(9);
                $grid->fechaColuna();
                $grid->abreColuna(3);
                    $botao = new BotaoGrafico();
                    $botao->set_url('?fase=editar&idProcesso='.$idProcesso);
                    $botao->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',20,20);
                    $botao->show();
                $grid->fechaColuna();
                $grid->fechaGrid();
            
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