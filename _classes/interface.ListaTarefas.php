<?php

class ListaTarefas{
 /**
  * Exibe uma lista de tarefas do sistema de gestão de projetos seguindo os critérios fornecidos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  */
    
    private $titulo = NULL;
    private $etiqueta = NULL;
    private $solicitante = NULL;
    private $projeto = NULL;
    private $pendente = TRUE;
    private $datado = NULL;
    private $status = NULL;
    
     
    ###########################################################
    
    /**
    * Método Construtor
    */
    public function __construct($titulo = NULL){
        $this->titulo = $titulo;
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
    public function __call ($metodo, $parametros)
    {
        ## Se for set, atribui um valor para a propriedade
        if (substr($metodo, 0, 3) == 'set')
        {
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
        $select = 'SELECT idTarefa,
                          idTarefa,
                          idTarefa,
                          idTarefa
                     FROM tbprojetotarefa';
        
        # Pendente
        if($this->pendente){
            $select.= ' WHERE pendente';
        }else{
            $select.= ' WHERE NOT pendente';
        }
        
        
        # Etiquetas
        if(!is_null($this->etiqueta)){
            $select.= ' AND etiqueta = "'.$this->etiqueta.'"';
        }
        
        # Solicitante
        if(!is_null($this->solicitante)){
            $select.= ' AND solicitante = "'.$this->solicitante.'"';
        }
        
        # Projeto
        if(!is_null($this->projeto)){
            $select.= ' AND idProjeto = '.$this->projeto;
        }
        
        # Projeto
        if(!is_null($this->status)){
            $select.= ' AND status = "'.$this->status.'"';
        }
        
        $select .=' ORDER BY dataInicial, noOrdem desc';
        
        #echo $select;
        
        # Acessa o banco
        $intra = new Intra();
        $tarefas = $intra->select($select);
        $numTarefas = $intra->count($select);
        
        # Botão do Tick
        $botao1 = new BotaoGrafico();
        
        # o link para quando se é por projeto
        if(!is_null($this->projeto)){
            $botao1->set_url('?fase=mudaTarefa&idProjeto='.$this->projeto.'&idTarefa=');
        }

        # o link para quando se é por etiqueta
        if(!is_null($this->etiqueta)){
            $botao1->set_url('?fase=mudaTarefa&etiqueta='.$this->etiqueta.'&idTarefa=');
        }
        
        # o link para quando se é por solicitante
        if(!is_null($this->etiqueta)){
            $botao1->set_url('?fase=mudaTarefa&solicitante='.$this->solicitante.'&idTarefa=');
        }
        
        # o link para quando se é de hoje
        if($this->status == "fazendo"){
            $botao1->set_url('?fase=mudaTarefa&status=fazendo&idTarefa=');
        }
        
        # Verifica qual simbolo vai colocar
        if($this->pendente){
            $botao1->set_imagem(PASTA_FIGURAS.'tickVazio.png',20,20);
        }else{
            $botao1->set_imagem(PASTA_FIGURAS.'tickCheio.png',20,20);
        }
        
        # Botão de editar
        $botao2 = new BotaoGrafico();
        $botao2->set_url('?fase=tarefaNova&idTarefa=');
        $botao2->set_imagem(PASTA_FIGURAS_GERAIS.'bullet_edit.png',20,20);
        
        # Inicia a tabela
        $tabela = new Tabela("tableTarefas");
        
        # Verifica se tem título
        if(!is_null($this->titulo)){
            $tabela->set_titulo($this->titulo);
        }
        
        $label = array("","");
        $align = array("center","left");
        $width = array(5,95);
        $classe = array(NULL,"Gprojetos");
        $metodo = array(NULL,"showTarefa");
        $link = array($botao1);
                
        $tabela->set_conteudo($tarefas);
        $tabela->set_cabecalho($label,$width,$align);
        
        $tabela->set_classe($classe);
        $tabela->set_metodo($metodo);
        $tabela->set_scroll(FALSE);
        
        # Coloca o objeto link na tabela	
        $tabela->set_idCampo("idTarefa");
        $tabela->set_link($link);
        
        if($numTarefas > 0){
            $tabela->show();
        }
    }
    
    ###########################################################
    
}