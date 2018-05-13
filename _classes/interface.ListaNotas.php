<?php

class ListaNotas{
 /**
  * Exibe uma lista de tarefas do sistema de gestão de projetos seguindo os critérios fornecidos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  */
    
    private $etiqueta = NULL;
    private $projeto = NULL;    
     
    ###########################################################
    
    /**
    * Método Construtor
    */
    public function __construct(){
    }

    ###########################################################

    /**
     * Métodos get e set construídos de forma automática pelo 
     * metodo mágico __call.
     * Esse método cria um set e um get para todas as propriedades da classe.
     * Se o método não estiver previsto no __call o php procura pela existência
     * do método na classe.
     * 
     * O formato dos métodos devem ser:
     * 	set_propriedade
     * 	get_propriedade
     * 
     * @param 	$metodo		O nome do metodo
     * @param 	$parametros	Os parâmetros inseridos  
     */
     public function __call ($metodo, $parametros){
        ## Se for set, atribui um valor para a propriedade
        if (substr($metodo, 0, 3) == 'set'){
            $var = substr($metodo, 4);
            $this->$var = $parametros[0];
        }

        # Se for Get, retorna o valor da propriedade
        #if (substr($metodo, 0, 3) == 'get')
        #{
        #  $var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
        #  return $this->$var;
        #}
    }

    ###########################################################
    
    public function show(){
    /**
     * Exibe a lista
     * 
     */
        # Define a data de hoje
        $hoje = date("d/m/Y");
        
        # Pega as tarefas
        $select = 'SELECT titulo,
                          idNota
                     FROM tbprojetonota
                    WHERE idProjeto = '.$this->projeto.' ORDER BY titulo';
        
        # Acessa o banco
        $intra = new Intra();
        $notas = $intra->select($select);
        $numNotas = $intra->count($select);
        
        # Inicia a tabela
        $tabela = new Tabela("tableTarefas");
        $tabela->set_titulo("Notas");
        
        $tabela->set_conteudo($notas);
        $tabela->set_label(array("Nota","Editar"));
        #$tabela->set_width(array(5,30,20,10,10,10));
        $tabela->set_align(array("left"));
        
        # Botão de editar
        $botao2 = new BotaoGrafico();
        $botao2->set_url('?fase=exibeNota&idNota=');
        $botao2->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',20,20);
        
        # Coloca o objeto link na tabela	
        $tabela->set_idCampo("idNota");
        $tabela->set_link(array(NULL,$botao2));
        
        if($numNotas > 0){
            $tabela->show();
        }
    }
        
}