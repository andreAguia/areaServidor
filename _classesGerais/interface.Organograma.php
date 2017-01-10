<?php
class Organograma
{	    
/**
 * Monta um Organograma
 * 
 * @author André Águia (Alat) - alataguia@gmail.com
 * 
 * @var private $dados array  NULL  Os dados do gráfico.
 * @var private $tipo  string NULL  O tipo do gráfico: Pie | Line | Area
 * @var private $tresd bool   FALSE Informa se o gráfico será ou não em 3 dimensões
 * 
 * @example exemplo.button.php
 */

    private $dados = NULL;
    private $tipo = NULL;
    private $tresd = FALSE;
    
    private $idDiv = "org";

###########################################################

    public function __construct($dados = NULL){
        
    /**
     * Inicia a classe
     * 
     * @param $tipo  string NULL O tipo do gráfico: pie|
     * @param $dados array  NULL Os dados do gráfico
     * 
     * @syntax $button = new Chart($tipo,$dados);
     */
    
       $this->dados = $dados;
    }

###########################################################

    public function set_idDiv($idDiv = NULL){
    /**
     * Informa se o gráfico será em 3d
     * 
     * @syntax $button->set_tresd($tresd);
     * 
     * @param $tresd bool NULL Informa se o gráfico será ou não em 3 dimensões
     */
    
        $this->idDiv = $idDiv;
    }

###########################################################

     public function show(){
    /**
     * Exibe o gráfico
     * 
     * @syntax $button->show();
     */ 
         
         # Inicia a rotina e jscript
         echo "<script type='text/javascript'>
                       google.charts.load('current', {packages:['orgchart']});
                       google.charts.setOnLoadCallback(drawChart);

      function drawChart(){
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Sigla');
        data.addColumn('string', 'Nome');
        data.addColumn('string', 'Chefe');";
         
         # Inicia o contador
         $contador = 0;
         $numItens = count($this->dados);
         
         echo "data.addRows([";
         
         # Percorre o array de dados
         foreach ($this->dados as $item){
            echo "['$item[0]','','$item[2]'],";
         }
            
         echo "]);";
         
         # Create the chart.
         echo "var chart = new google.visualization.OrgChart(document.getElementById('$this->idDiv'));";
         # Draw the chart, setting the allowHtml option to true for the tooltips.
         echo "chart.draw(data, {allowHtml:true});";
         echo "}";

         echo "</script>";
         echo '<div id="'.$this->idDiv.'"></div>';
     }
            
}