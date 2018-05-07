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
                          idProcessoMovimento,
                          motivo
                     FROM tbprocessomovimento
                    WHERE idProcesso = '.$this->processo.'
                 ORDER BY data desc, 3 desc';
         
        # Acessa o banco
        $intra = new Intra();
        $movimento = $intra->select($select);
        $numMovimento = $intra->count($select);
        
        $tabela = new Tabela();
        $tabela->set_titulo("Movimentos");
        $tabela->set_conteudo($movimento);
        $tabela->set_label(array("Status","Data","Origem / Destino","Motivo"));
        #$tabela->set_width(array(80,10,10));
        $tabela->set_align(array("center","center","center","left"));
        $tabela->set_funcao(array(NULL,"date_to_php"));
        $tabela->set_classe(array(NULL,NULL,"Processo"));
        $tabela->set_metodo(array(NULL,NULL,"get_MovimentoSetor"));
        $tabela->set_idCampo("idProcessoMovimento");
        $tabela->set_nomeGetId("idProcessoMovimento");
        $tabela->set_editar('?fase=movimentacaoIncluir');
        $tabela->set_excluir('?fase=movimentacaoExcluir');
        $tabela->show();
    }
    
    ###########################################################
    
}