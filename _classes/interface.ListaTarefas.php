<?php

class ListaTarefas{
 /**
  * Exibe uma lista de tarefas do sistema de gestão de projetos seguindo os critérios fornecidos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  */
    
    private $titulo = NULL;
    private $etiqueta = NULL;
    private $projeto = NULL;
    private $pendente = TRUE;
    private $datado = NULL;
    private $hoje = TRUE;
    
     
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
                          tarefa,
                          idProjeto,
                          idEtiqueta,
                          idTarefa,
                          idTarefa
                     FROM tbprojetotarefa';
        
        # Pendente
        if($this->pendente){
            $select.= ' WHERE pendente';
        }else{
            $select.= ' WHERE NOT pendente';
        }
        
        # Com Data (datado)
        if(!is_null($this->datado)){  // se tiver null exibe os dois
            if($this->datado){
                $select.= ' AND dataInicial <> "0000-00-00"'; // Exibe os datados
            }else{
                $select.= ' AND dataInicial = "0000-00-00"';  // Exibe os sem data
            }
        }
                 
        # Etiquetas
        if(!is_null($this->etiqueta)){
            $select.= ' AND idEtiqueta = '.$this->etiqueta;
        }
        
        # Projeto
        if(!is_null($this->projeto)){
            $select.= ' AND idProjeto = '.$this->projeto;
        }
        
        $select .=' ORDER BY dataInicial, noOrdem';
        
        # Acessa o banco
        $intra = new Intra();
        $tarefas = $intra->select($select);
        $numTarefas = $intra->count($select);
        
        # Inicia a tabela
        $tabela = new Tabela();
        
        # Verifica se tem título
        if(!is_null($this->titulo)){
            $tabela->set_titulo($this->titulo);
        }
        
        $tabela->set_conteudo($tarefas);
        $tabela->set_label(array("","Tarefa","Projeto","Etiqueta","Data","Editar"));
        #$tabela->set_width(array(5,30,20,10,10,10));
        $tabela->set_align(array("center","left","center","center"));
        
        # Tacha o texto quando completado
        if(!$this->pendente){
            $tabela->set_funcao(array(NULL,"del"));
        }
        
        $tabela->set_classe(array(NULL,NULL,"Gprojetos","Gprojetos","Gprojetos"));
        $tabela->set_metodo(array(NULL,NULL,"showProjeto","showEtiqueta","showData"));
        
        # Botão  do Tick
        $botao1 = new BotaoGrafico();                    
        # o link para quando se é por projeto
        if(!is_null($this->projeto)){
            $botao1->set_url('?fase=mudaTarefa&idProjeto='.$this->projeto.'&idTarefa=');
        }

        # o link para quando se é por etiqueta
        if(!is_null($this->etiqueta)){
            $botao1->set_url('?fase=mudaTarefa&idEtiqueta='.$this->etiqueta.'&idTarefa=');
        }
        
        # Verifica qual simbolo vai colocar
        if($this->pendente){
            $botao1->set_image(PASTA_FIGURAS.'tickVazio.png',20,20);
        }else{
            $botao1->set_image(PASTA_FIGURAS.'tickCheio.png',20,20);
        }
        
        # Botão de editar
        $botao2 = new BotaoGrafico();
        $botao2->set_url('?fase=tarefaNova&idTarefa=');
        $botao2->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',20,20);
        
        # Coloca o objeto link na tabela	
        $tabela->set_idCampo("idTarefa");
        $tabela->set_link(array($botao1,NULL,NULL,NULL,NULL,$botao2));
        
        if($numTarefas > 0){
            $tabela->show();
        }
    }
    
    ###########################################################
    
    public function showPendenteHoje(){
    /**
     * Exibe a lista de tarefas pendentes de hoje
     * 
     */
        
        # Pega as tarefas
        $select = 'SELECT idTarefa,
                          tarefa,
                          descricao,
                          idSecao,
                          dataInicial,
                          dataFinal,
                          pendente,
                          idEtiqueta,
                          idProjeto
                     FROM tbprojetotarefa
                    WHERE pendente
                      AND ((dataInicial = CURDATE()) OR (CURDATE() BETWEEN dataInicial AND dataFinal))
                 ORDER BY dataInicial,noOrdem';
        
        # Acessa o banco
        $intra = new Intra();
        $tarefas = $intra->select($select);
        $numeroTarefas = $intra->count($select);
        #echo $select;
        
        # Inicia classe projeto
        $classProjeto = new Projeto();
        
        # Inicia a lista
        echo '<ul id="projetosTarefas">';
            
        # Verifica se tem algum registro
        if($numeroTarefas>0){
            # Exibe o título
            p("Tarefas de Hoje","f14");
            
            # Percorre o array
            foreach ($tarefas as $valor) {
                
                # Cria uma div para o mouseover
                $div = new Div("divTarefas");
                $div->abre();
                
                # Inicia a grid interna
                $grid = new Grid();
                
                #####################################################
                # Ticked
                $grid->abreColuna(1);
                    # Inicia o botão
                    $botao = new BotaoGrafico();
                    $botao->set_url('?fase=mudaTarefa&idTarefa='.$valor[0].'&hoje=TRUE');
                    $botao->set_image(PASTA_FIGURAS.'tickVazio.png',15,15);
                    $botao->show();
                $grid->fechaColuna();
                #####################################################
                # Tarefa
                $grid->abreColuna(5,5,6);
                    echo "<li title='$valor[2]'>$valor[1]</li>";
                $grid->fechaColuna();
                #####################################################
                $grid->abreColuna(4);
                    # Etiqueta
                    $grid = new Grid();
                    $grid->abreColuna(12,12,6);
                        if($valor[7] <> 0){
                            $dadosEtiqueta = $classProjeto->get_dadosEtiqueta($valor[7]);
                            echo "<li>".label($dadosEtiqueta[1],$dadosEtiqueta[2])."</li>";
                        }
                    $grid->fechaColuna();
                    #####################################################
                    # Projeto
                    $grid->abreColuna(12,12,6);
                        if(!vazio($valor[8])){
                            $nome = $classProjeto->get_nomeProjeto($valor[8]);
                            $cor = $classProjeto->get_corProjeto($valor[8]);
                            echo "<li>".label($nome,$cor)."</li>";
                        }
                    $grid->fechaColuna();
                #####################################################
                    # Datas
                    $grid->abreColuna(12,12,6);
                        # Define as datas
                        $dataInicial = date_to_php($valor[4]);
                        $dataFinal = date_to_php($valor[5]);

                        # Exibe as datas
                        echo "<li id='projetoDataInicial'>".formataDataTarefa($dataInicial,$dataFinal)."</li>";
                        #echo "<li id='projetoDataInicial'>".$dataInicial.'-'.$dataFinal."</li>";
                    $grid->fechaColuna();
                    $grid->fechaGrid();
                $grid->fechaColuna();
                #####################################################
                # Editar
                $grid->abreColuna(2,2,1);
                    $botao = new BotaoGrafico();
                    $botao->set_url('?fase=tarefaNova&idTarefa='.$valor[0].'&idProjeto='.$valor[8].'&hoje=TRUE');
                    $botao->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',15,15);
                    $botao->show();
                $grid->fechaColuna();
                $grid->fechaGrid(); 
                
                $div->fecha();
            }
        }
        echo '</ul>';
    }
    
    ###########################################################
    
}