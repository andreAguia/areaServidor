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
                          descricao,
                          idSecao,
                          dataInicial,
                          dataFinal,
                          pendente,
                          idEtiqueta,
                          idProjeto
                     FROM tbprojetotarefa';
        
        # Verifica se é terefas pendentes
        if($this->pendente){
            $select.= ' WHERE pendente';
        }else{
            $select.= ' WHERE NOT pendente';
        }
        
        # Etiquetas
        if(!is_null($this->etiqueta)){
            $select.= ' AND idEtiqueta = '.$this->etiqueta;
        }
        
        # Projeto
        if(!is_null($this->projeto)){
            $select.= ' AND idProjeto = '.$this->projeto;
        }
        
        # HOJE
        if($this->hoje){
            $select.= ' AND dataInicial = "'.date_to_bd($hoje).'"';
        }
        
        # Verifica se é listagem de tarefa datada e pendente
        if(($this->pendente) AND ($this->datado)){
            $select.= ' AND dataInicial <> "0000-00-00"';
        }
        
        if(($this->pendente) AND (!$this->datado)){
            $select.= ' AND dataInicial = "0000-00-00"';
        }
        
        $select .=' ORDER BY dataInicial, noOrdem';
        
        # Acessa o banco
        $intra = new Intra();
        $tarefas = $intra->select($select);
        $numeroTarefas = $intra->count($select);
        #echo $select;
        
        # Inicia a lista
        echo '<ul id="projetosTarefas">';
            
        # Verifica se tem algum registro
        if($numeroTarefas>0){
            
            # Exibe o título
            if(!is_null($this->titulo)){
                p($this->titulo,"f14");
            }
            
            # Percorre o array
            foreach ($tarefas as $valor) {
                
                # Muda a cor da tarefa pendente de acordo com a situação
                
                # Define a cor padrão
                $idDiv = "divTarefas";
                
                # Verifica se é pendente
                if($this->pendente){
                    
                    # Passa para o padrão brasileiro para exigencia das funções
                    $dataInicial = date_to_php($valor[4]);
                    $dataFinal = date_to_php($valor[5]);
                    
                    
                    # Pega somente datas vélidas
                    if($dataInicial <> "00/00/0000"){
                        
                        # Datas atrasadas somente com data inicial preenchida
                        if(($dataFinal == "00/00/0000") AND (jaPassou($dataInicial))){
                            $idDiv = "divTarefasAtrasadas";
                        }
                        
                        # Datas atrasadas com data inicial e final preenchida
                        if($dataFinal <> "00/00/0000"){
                            if((jaPassou($dataFinal)) AND (jaPassou($dataInicial))){
                                $idDiv = "divTarefasAtrasadas";
                            }
                        }
                        
                        # é hoje? com apenas a data inicial
                        if(eHoje($dataInicial)){
                            $idDiv = "divTarefasHoje";
                        }
                        
                        # é hoje? com as duas datas
                        if(entre($hoje,$dataInicial,$dataFinal)){
                            $idDiv = "divTarefasHoje";
                        }
                    }
                }
                
                # Cria uma div para o mouseover
                $div = new Div($idDiv);
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
                    
                    # o link para quando se é tarefas de hoje
                    if($this->hoje){
                        $botao->set_url('?fase=mudaTarefa&idTarefa='.$valor[0]);
                    }

                    # Exibe i botão ticado ou não de acordo com o valor
                    if($valor[6] == 0){
                        $botao->set_image(PASTA_FIGURAS.'tickCheio.png',15,15);
                    }else{
                        $botao->set_image(PASTA_FIGURAS.'tickVazio.png',15,15);
                    }
                    $botao->show();

                $grid->fechaColuna();
                
                #####################################################
                # Tarefa
                $grid->abreColuna(6);
                    echo "<li title='$valor[2]'>$valor[1]</li>";
                $grid->fechaColuna();                
                
                # Inicia classe projeto
                $projEtiq = new Projeto();
                
                #####################################################
                # Etiqueta - Exibe spmente se for a lista por projeto
                if(is_null($this->etiqueta)){
                    $grid->abreColuna(2);
                        if($valor[7] <> 0){
                            $dadosEtiqueta = $projEtiq->get_dadosEtiqueta($valor[7]);
                            echo "<li>".label($dadosEtiqueta[1],$dadosEtiqueta[2])."</li>";
                        }
                    $grid->fechaColuna();
                }
                
                #####################################################
                # Projeto - Exibe spmente se for a lista por etiqueta
                if(is_null($this->projeto)){
                $grid->abreColuna(2);
                    if(!vazio($valor[8])){
                        $nome = $projEtiq->get_nomeProjeto($valor[8]);
                        $cor = $projEtiq->get_corProjeto($valor[8]);
                        echo "<li>".label($nome,$cor)."</li>";
                    }
                $grid->fechaColuna();
                }
                
                #####################################################
                # Datas
                $grid->abreColuna(2);
                    if($this->datado){
                        # Define as datas
                        $dataInicial = date_to_php($valor[4]);
                        $dataFinal = date_to_php($valor[5]);

                        # Exibe as datas
                        echo "<li id='projetoDataInicial'>".formataDataTarefa($dataInicial,$dataFinal)."</li>";
                        #echo "<li id='projetoDataInicial'>".$dataInicial.'-'.$dataFinal."</li>";
                    }
                $grid->fechaColuna();
                
                #####################################################
                # Editar
                $grid->abreColuna(1);
                    $botao = new BotaoGrafico();
                    $botao->set_url('?fase=tarefaNova&idTarefa='.$valor[0].'&idProjeto='.$valor[8]);
                    $botao->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',15,15);
                    $botao->show();
                $grid->fechaColuna();
                $grid->fechaGrid(); 
                
                $div->fecha();
                
                hr("projetosTarefas");   
            }
        }
        echo '</ul>';
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
                      AND dataInicial = CURDATE()
                 ORDER BY noOrdem';
        
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
                $grid->abreColuna(6);
                    echo "<li title='$valor[2]'>$valor[1]</li>";
                $grid->fechaColuna();
                #####################################################
                # Etiqueta
                $grid->abreColuna(2);
                    if($valor[7] <> 0){
                        $dadosEtiqueta = $classProjeto->get_dadosEtiqueta($valor[7]);
                        echo "<li>".label($dadosEtiqueta[1],$dadosEtiqueta[2])."</li>";
                    }
                $grid->fechaColuna();
                #####################################################
                # Projeto
                $grid->abreColuna(2);
                    if(!vazio($valor[8])){
                        $nome = $classProjeto->get_nomeProjeto($valor[8]);
                        $cor = $classProjeto->get_corProjeto($valor[8]);
                        echo "<li>".label($nome,$cor)."</li>";
                    }
                $grid->fechaColuna();
                #####################################################
                # Editar
                $grid->abreColuna(1);
                    $botao = new BotaoGrafico();
                    $botao->set_url('?fase=tarefaNova&idTarefa='.$valor[0].'&idProjeto='.$valor[8].'&hoje=TRUE');
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
                          descricao,
                          idSecao,
                          dataInicial,
                          dataFinal,
                          pendente,
                          idEtiqueta,
                          idProjeto
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
        $numeroTarefas = $intra->count($select);
        #echo $select;
        
        # Inicia classe projeto
        $classProjeto = new Projeto();
        
        # Inicia a lista
        echo '<ul id="projetosTarefas">';
            
        # Verifica se tem algum registro
        if($numeroTarefas>0){
            
            # Exibe o título
            p("Tarefas Pendentes com Data","f14");
            
            # Percorre o array
            foreach ($tarefas as $valor) {
                
                # Muda a cor da tarefa pendente de acordo com a situação
                
                # Define a cor padrão
                $idDiv = "divTarefas";
                    
                # Passa para o padrão brasileiro para exigencia das funções
                $dataInicial = date_to_php($valor[4]);
                $dataFinal = date_to_php($valor[5]);

                # Datas atrasadas somente com data inicial preenchida
                if(($dataFinal == "00/00/0000") AND (jaPassou($dataInicial))){
                    $idDiv = "divTarefasAtrasadas";
                }

                # Datas atrasadas com data inicial e final preenchida
                if($dataFinal <> "00/00/0000"){
                    if((jaPassou($dataFinal)) AND (jaPassou($dataInicial))){
                        $idDiv = "divTarefasAtrasadas";
                    }
                }

                # é hoje? com apenas a data inicial
                if(eHoje($dataInicial)){
                    $idDiv = "divTarefasHoje";
                }

                # é hoje? com as duas datas
                if(entre($hoje,$dataInicial,$dataFinal)){
                    $idDiv = "divTarefasHoje";
                }
                
                # Cria uma div para o mouseover
                $div = new Div($idDiv);
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
                $grid->abreColuna(6);
                    echo "<li title='$valor[2]'>$valor[1]</li>";
                $grid->fechaColuna();                
                #####################################################
                # Etiqueta - Exibe spmente se for a lista por projeto
                if(is_null($this->etiqueta)){
                    $grid->abreColuna(2);
                        if($valor[7] <> 0){
                            $dadosEtiqueta = $classProjeto->get_dadosEtiqueta($valor[7]);
                            echo "<li>".label($dadosEtiqueta[1],$dadosEtiqueta[2])."</li>";
                        }
                    $grid->fechaColuna();
                }
                #####################################################
                # Projeto - Exibe spmente se for a lista por etiqueta
                if(is_null($this->projeto)){
                    $grid->abreColuna(2);
                        if(!vazio($valor[8])){
                            $nome = $classProjeto->get_nomeProjeto($valor[8]);
                            $cor = $classProjeto->get_corProjeto($valor[8]);
                            echo "<li>".label($nome,$cor)."</li>";
                        }
                    $grid->fechaColuna();
                }
                #####################################################
                # Datas
                $grid->abreColuna(2);
                    # Define as datas
                    $dataInicial = date_to_php($valor[4]);
                    $dataFinal = date_to_php($valor[5]);

                    # Exibe as datas
                    echo "<li id='projetoDataInicial'>".formataDataTarefa($dataInicial,$dataFinal)."</li>";
                    #echo "<li id='projetoDataInicial'>".$dataInicial.'-'.$dataFinal."</li>";
                $grid->fechaColuna();
                #####################################################
                # Editar
                $grid->abreColuna(1);
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
                $grid->abreColuna(6);
                    echo "<li title='$valor[2]'>$valor[1]</li>";
                $grid->fechaColuna();
                #####################################################
                # Etiqueta - Exibe spmente se for a lista por projeto
                if(is_null($this->etiqueta)){
                    $grid->abreColuna(2);
                        if($valor[7] <> 0){
                            $dadosEtiqueta = $classProjeto->get_dadosEtiqueta($valor[7]);
                            echo "<li>".label($dadosEtiqueta[1],$dadosEtiqueta[2])."</li>";
                        }
                    $grid->fechaColuna();
                }
                #####################################################
                # Projeto - Exibe spmente se for a lista por etiqueta
                if(is_null($this->projeto)){
                    $grid->abreColuna(2);
                        if(!vazio($valor[8])){
                            $nome = $classProjeto->get_nomeProjeto($valor[8]);
                            $cor = $classProjeto->get_corProjeto($valor[8]);
                            echo "<li>".label($nome,$cor)."</li>";
                        }
                    $grid->fechaColuna();
                }
                #####################################################
                # Datas
                $grid->abreColuna(2);
                    # Não tem data mas mantem o grido para ficar alinhado
                $grid->fechaColuna();
                #####################################################
                # Editar
                $grid->abreColuna(1);
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
                $grid->abreColuna(6);
                    echo "<li title='$valor[2]'>$valor[1]</li>";
                $grid->fechaColuna();
                #####################################################
                # Etiqueta - Exibe spmente se for a lista por projeto
                if(is_null($this->etiqueta)){
                    $grid->abreColuna(2);
                        if($valor[7] <> 0){
                            $dadosEtiqueta = $classProjeto->get_dadosEtiqueta($valor[7]);
                            echo "<li>".label($dadosEtiqueta[1],$dadosEtiqueta[2])."</li>";
                        }
                    $grid->fechaColuna();
                }
                #####################################################
                # Projeto - Exibe spmente se for a lista por etiqueta
                if(is_null($this->projeto)){
                    $grid->abreColuna(2);
                        if(!vazio($valor[8])){
                            $nome = $classProjeto->get_nomeProjeto($valor[8]);
                            $cor = $classProjeto->get_corProjeto($valor[8]);
                            echo "<li>".label($nome,$cor)."</li>";
                        }
                    $grid->fechaColuna();
                }
                #####################################################
                # Datas
                $grid->abreColuna(2);
                    if($this->datado){
                        # Define as datas
                        $dataInicial = date_to_php($valor[4]);
                        $dataFinal = date_to_php($valor[5]);

                        # Exibe as datas
                        echo "<li id='projetoDataInicial'>".formataDataTarefa($dataInicial,$dataFinal)."</li>";
                        #echo "<li id='projetoDataInicial'>".$dataInicial.'-'.$dataFinal."</li>";
                    }
                $grid->fechaColuna();
                #####################################################
                # Editar
                $grid->abreColuna(1);
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