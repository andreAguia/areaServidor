<?php

class ListaTarefas2{
 /**
  * Exibe uma lista de tarefas do sistema de gestão de projetos seguindo os critérios fornecidos
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  */
    
    private $titulo = NULL;
    private $etiqueta = NULL;
    private $projeto = NULL;
    private $pendente = TRUE;
    private $datado = TRUE;
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
    
    public function showPendenteAtrasada(){
    /**
     * Exibe a lista
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
                      AND dataInicial <> "0000-00-00" 
                      AND ((dataInicial < CURDATE()) AND (dataFinal < CURDATE()))
                 ORDER BY dataInicial, noOrdem';
        
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
            p("Tarefas Atrasadas","f14");
            
            # Percorre o array
            foreach ($tarefas as $valor) {
                
                # Muda a cor da tarefa pendente de acordo com a situação
                
                # Define a cor padrão
                $idDiv = "divTarefasAtrasadas";
                
                # Cria uma div para o mouseover
                $div = new Div("divTarefasAtrasadas");
                $div->abre();
                
                # Inicia a grid interna
                $grid = new Grid();
                
                #####################################################
                # Ticked
                $grid->abreColuna(1);

                    # Inicia o botão
                    $botao = new BotaoGrafico();
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
                    $grid = new Grid();
                    # Etiqueta
                    if(is_null($this->etiqueta)){
                        if((!is_null($valor[7])) AND ($valor[7] <> 0)){
                            $grid->abreColuna(12,12,6);
                                    $dadosEtiqueta = $classProjeto->get_dadosEtiqueta($valor[7]);
                                    echo "<li>".label($dadosEtiqueta[1],$dadosEtiqueta[2])."</li>";
                            $grid->fechaColuna();
                        }
                    }
                    #####################################################
                    # Projeto
                    if(is_null($this->projeto)){
                        if(!is_null($valor[8])){
                            $grid->abreColuna(12,12,6);
                                $nome = $classProjeto->get_nomeProjeto($valor[8]);
                                $cor = $classProjeto->get_corProjeto($valor[8]);
                                echo "<li>".label($nome,$cor)."</li>";
                            $grid->fechaColuna();
                        }
                    }
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
                    $botao->set_url('?fase=tarefaNova&idTarefa='.$valor[0].'&idProjeto='.$valor[8]);
                    $botao->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',15,15);
                    $botao->show();
                $grid->fechaColuna();
                $grid->fechaGrid(); 
                
                $div->fecha();
                
                #hr("projetosTarefas");   
            }
        }
        echo '</ul>';
    }
    
    ###########################################################
    
    public function showPendenteDatado(){
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
                          idTarefa
                     FROM tbprojetotarefa
                    WHERE pendente
                      AND dataInicial <> "0000-00-00"';
        
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
        
        # Exemplo de tabela simples
        $tabela = new Tabela();
        #$tabela->set_titulo("Tarefas");
        $tabela->set_conteudo($tarefas);
        $tabela->set_label(array("Pendente","Tarefa","Projeto","Etiqueta","Data"));
        #$tabela->set_width(array(5,30,20,10,10,10));
        $tabela->set_align(array("center","left","center","center"));
        $tabela->set_classe(array(NULL,NULL,"Gprojetos","Gprojetos","Gprojetos"));
        $tabela->set_metodo(array(NULL,NULL,"showProjeto","showEtiqueta","showData"));
        $tabela->show();
    }
    
    ###########################################################
    
    public function showPendenteSemData(){
    /**
     * Exibe a lista
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
                      AND dataInicial = "0000-00-00"';
        
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
        $numeroTarefas = $intra->count($select);
        #echo $select;
        
        # Inicia classe projeto
        $classProjeto = new Projeto();
        
        # Inicia a lista
        echo '<ul id="projetosTarefas">';
            
        # Verifica se tem algum registro
        if($numeroTarefas>0){
            
            # Exibe o título
            p("Tarefas Pendentes sem Data","f14");
            
            # Percorre o array
            foreach ($tarefas as $valor) {
                
                #$callout = new Callout();
                #$callout->abre();
                
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
                    
                    # o link para quando se é por projeto
                    if(!is_null($this->projeto)){
                        $botao->set_url('?fase=mudaTarefa&idTarefa='.$valor[0].'&idProjeto='.$this->projeto);
                    }
                    
                    # o link para quando se é por etiqueta
                    if(!is_null($this->etiqueta)){
                        $botao->set_url('?fase=mudaTarefa&idTarefa='.$valor[0].'&idEtiqueta='.$this->etiqueta);
                    }
                    
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
                    $grid = new Grid();
                    # Etiqueta
                    if(is_null($this->etiqueta)){
                        if((!is_null($valor[7])) AND ($valor[7] <> 0)){
                            $grid->abreColuna(12,12,6);
                                    $dadosEtiqueta = $classProjeto->get_dadosEtiqueta($valor[7]);
                                    echo "<li>".label($dadosEtiqueta[1],$dadosEtiqueta[2])."</li>";
                            $grid->fechaColuna();
                        }
                    }
                    #####################################################
                    # Projeto
                    if(is_null($this->projeto)){
                        if(!is_null($valor[8])){
                            $grid->abreColuna(12,12,6);
                                $nome = $classProjeto->get_nomeProjeto($valor[8]);
                                $cor = $classProjeto->get_corProjeto($valor[8]);
                                echo "<li>".label($nome,$cor)."</li>";
                            $grid->fechaColuna();
                        }
                    }
                    $grid->fechaGrid();
                $grid->fechaColuna();
                #####################################################
                # Editar
                $grid->abreColuna(2,2,1);
                    $botao = new BotaoGrafico();
                    $botao->set_url('?fase=tarefaNova&idTarefa='.$valor[0].'&idProjeto='.$valor[8]);
                    $botao->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',15,15);
                    $botao->show();
                $grid->fechaColuna();
                $grid->fechaGrid(); 
                
                $div->fecha();
                #$callout->fecha();
                
                #hr("projetosTarefas");   
            }
        }
        echo '</ul>';
    }
    
    ###########################################################
    
    public function showCompletadas(){
    /**
     * Exibe a lista
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
                    WHERE NOT pendente';
        
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
        $numeroTarefas = $intra->count($select);
        #echo $select;
        
        # Inicia classe projeto
        $classProjeto = new Projeto();
        
        # Inicia a lista
        echo '<ul id="projetosTarefas">';
            
        # Verifica se tem algum registro
        if($numeroTarefas>0){
            
            p("Tarefas Completadas","f14");
            
            # Percorre o array
            foreach ($tarefas as $valor) {
                
                # Cria uma div para o mouseover
                $div = new Div("divTarefasCompletadas");
                $div->abre();
                
                # Inicia a grid interna
                $grid = new Grid();
                
                #####################################################
                # Ticked
                $grid->abreColuna(1);

                    # Inicia o botão
                    $botao = new BotaoGrafico();
                    
                    # o link para quando se é por projeto
                    if(!is_null($this->projeto)){
                        $botao->set_url('?fase=mudaTarefa&idTarefa='.$valor[0].'&idProjeto='.$this->projeto);
                    }
                    
                    # o link para quando se é por etiqueta
                    if(!is_null($this->etiqueta)){
                        $botao->set_url('?fase=mudaTarefa&idTarefa='.$valor[0].'&idEtiqueta='.$this->etiqueta);
                    }

                    $botao->set_image(PASTA_FIGURAS.'tickCheio.png',15,15);
                    $botao->show();

                $grid->fechaColuna();
                
                #####################################################
                # Tarefa
                $grid->abreColuna(5,5,6);
                    echo "<li title='$valor[2]'>$valor[1]</li>";
                $grid->fechaColuna();
                #####################################################
                $grid->abreColuna(4);                    
                    $grid = new Grid();
                    # Etiqueta
                    if(is_null($this->etiqueta)){
                        if((!is_null($valor[7])) AND ($valor[7] <> 0)){
                            $grid->abreColuna(12,12,6);
                                    $dadosEtiqueta = $classProjeto->get_dadosEtiqueta($valor[7]);
                                    echo "<li>".label($dadosEtiqueta[1],$dadosEtiqueta[2])."</li>";
                            $grid->fechaColuna();
                        }
                    }
                    #####################################################
                    # Projeto
                    if(is_null($this->projeto)){
                        if(!is_null($valor[8])){
                            $grid->abreColuna(12,12,6);
                                $nome = $classProjeto->get_nomeProjeto($valor[8]);
                                $cor = $classProjeto->get_corProjeto($valor[8]);
                                echo "<li>".label($nome,$cor)."</li>";
                            $grid->fechaColuna();
                        }
                    }
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
                    $botao->set_url('?fase=tarefaNova&idTarefa='.$valor[0].'&idProjeto='.$valor[8]);
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