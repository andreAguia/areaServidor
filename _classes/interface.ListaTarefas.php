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
    private $hoje = FALSE;      // Somente as tarefas até hoje
    
     
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
        
        # Com Data (datado)
        if(!is_null($this->datado)){  // se tiver null exibe os dois
            if($this->datado){
                $select.= ' AND dataInicial IS NOT NULL'; // Exibe os datados
            }else{
                $select.= ' AND dataInicial IS NULL';  // Exibe os sem data
            }
        }
        
        # De hoje (com as atrasadas)
        if($this->hoje){
            $select.= ' AND dataInicial <= NOW()';  // Exibe os sem data
        }
                 
        # Etiquetas
        if(!is_null($this->etiqueta)){
            $select.= ' AND etiqueta = "'.$this->etiqueta.'"';
        }
        
        # Projeto
        if(!is_null($this->projeto)){
            $select.= ' AND idProjeto = '.$this->projeto;
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
        
        # o link para quando se é de hoje
        if($this->hoje){
            $botao1->set_url('?fase=mudaTarefa&hoje=TRUE&idTarefa=');
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
        
        $label = array("","","");
        $align = array("center","left");
        $width = array(5,80,15);
        $classe = array(NULL,"Gprojetos","Gprojetos");
        $metodo = array(NULL,"showTarefa","showData");
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
    
    public function showTimeline(){
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
                          etiqueta,
                          dataInicial,
                          dataFinal
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
                $select.= ' AND dataInicial IS NOT NULL'; // Exibe os datados
            }else{
                $select.= ' AND dataInicial IS NULL';  // Exibe os sem data
            }
        }
        
        # De hoje (com as atrasadas)
        if($this->hoje){
            $select.= ' AND dataInicial <= NOW()';  // Exibe os sem data
        }
                 
        # Etiquetas
        if(!is_null($this->etiqueta)){
            $select.= ' AND etiqueta = '.$this->etiqueta;
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
        
        tituloTable("Timeline");
        
        if($numTarefas > 0){
        
            # Carrega a rotina do Google
            echo '<script type="text/javascript" src="'.PASTA_FUNCOES_GERAIS.'/loader.js"></script>';

            # Inicia o script
            echo "<script type='text/javascript'>";
            echo "google.charts.load('current', {'packages':['timeline']});
                        google.charts.setOnLoadCallback(drawChart);
                        function drawChart() {
                          var container = document.getElementById('timeline');
                          var chart = new google.visualization.Timeline(container);
                          var dataTable = new google.visualization.DataTable();";

            echo "dataTable.addColumn({ type: 'string', id: 'Tarefa' });
                  dataTable.addColumn({ type: 'date', id: 'Inicio' });
                  dataTable.addColumn({ type: 'date', id: 'Fim' });";

            echo "dataTable.addRows([";

            $separador = '-';

            foreach ($tarefas as $row){

                # Trata a data inicial
                $dt1 = explode($separador,$row['dataInicial']);

                if($row['dataFinal'] == '0000-00-00'){
                    $dt2 = $dt1;
                    $dt2[2]++;
                }else{
                    $dt2 = explode($separador,$row['dataFinal']);
                }


                echo "[ '".$row['tarefa']."', new Date($dt1[0], $dt1[1]-1, $dt1[2]), new Date($dt2[0], $dt2[1]-1, $dt2[2]) ],";
            }
            echo "]);";
            echo "chart.draw(dataTable);";
            echo "}";
            echo "</script>";

            $altura = ($numTarefas * 40) + 60;

            #[ 'Washington', new Date(1789, 3, 30), new Date(1797, 2, 4) ],
            #[ 'Adams',      new Date(1797, 2, 4),  new Date(1801, 2, 4) ],
            #[ 'Jefferson',  new Date(1801, 2, 4),  new Date(1809, 2, 4) ]]);

                echo '<div id="timeline" style="height: '.$altura.'px;"></div>';
        }else{
            br(3);
            p("Não há tarefas Pendentes com data para gerar uma timeline.","f16","center");
        }
    }
    
    ###########################################################
    
}