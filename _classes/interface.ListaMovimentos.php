<?php

class ListaMovimentos{
 /**
  * Exibe uma lista de Movimentos de um determinado processos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  */
    
    private $processo = NULL;
     
    ###########################################################
    
    /**
    * Método Construtor
    */
    public function __construct($processo = NULL){
        $this->processo = $processo;
    }

    ###########################################################
    
    public function show(){
        
        # Pega os movimemntos
        $select = 'SELECT status,
                          data,
                          setorCombo,
                          setorTexto,
                          motivo
                          idProcessoMovimento
                     FROM tbprocessomovimento
                    WHERE idProcesso = '.$this->processo.'
                 ORDER BY data';
        
        # Acessa o banco
        $intra = new Intra();
        $movimento = $intra->select($select);
        $numMovimento = $intra->count($select);
        
        $tabela = new Tabela();
        $tabela->set_titulo("Movimentos");
        $tabela->set_conteudo($movimento);
        $tabela->set_label(array("Status","Data","Setor Combo","Setor Texto","motivo"));
        #$tabela->set_width(array(80,10,10));
        $tabela->set_align(array("center","center","center","center","left"));
        $tabela->show();
    }
    
    ###########################################################
    
}