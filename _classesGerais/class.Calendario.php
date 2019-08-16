<?php
class Calendario
{
 /**
  * Cria um fieldset. 
  * 
  * @author Talianderson Dias - talianderson.web@gmail.com
  */

    private $mes = NULL;
    private $ano = NULL;
    private $tamanho = "m";

###########################################################    

    public function __construct($mes,$ano,$tamanho = "m"){
    /**
     * Inicia a classe atribuindo um valor do legend e do id
     * 
     * @param $legend   string NULL O texto a ser exibido
     * @param $id       string NULL O id para o css
     * 
     * @syntax $field = new Fieldset([$legend], [$id]);
     */
    
    	$this->mes = $mes;
        $this->ano = $ano;
        $this->tamanho = $tamanho;
    }
    
###########################################################
    
    public function show(){
        
        # Verifica quantos dias tem o mês específico
        $dias = date("j",mktime(0,0,0,$this->mes+1,0,$this->ano));
        
        # Array dom os nomes do dia da semana
        switch ($this->tamanho){
            case "p":
                 $diaSemana = array("D","S","T","Q","Q","S","S");
                break;
            
            case "m":
                $diaSemana = array("Domingo","2º feira","3° feira","4° feira","5° feira","6° feira","Sabado");
                break;
            
            case "g":
                $diaSemana = array("Domingo","Segunda feira","Terça feira","Quarta feira","Quinta feira","Sexta feira","Sabado");
                break;
        }
        
        
        # Determina o dia da semana do dia primeiro
        $tstamp=mktime(0,0,0,$this->mes,1,$this->ano);
        $Tdate = getdate($tstamp);
        $wday=$Tdate["wday"];
        
        # Inicia a tabela
        echo '<table class="tabelaPadrao">';
        
        # Título Mês/Ano
        echo '<caption>'.get_nomeMes($this->mes).' / '.$this->ano.'</caption>';

        echo '<col style="width:14%">';
        echo '<col style="width:14%">';
        echo '<col style="width:14%">';
        echo '<col style="width:14%">';
        echo '<col style="width:14%">';
        echo '<col style="width:14%">';
        echo '<col style="width:14%">';

        # Cabeçalho dias da semana
        echo '<tr>';
        foreach($diaSemana as $ds){
            echo "<th>$ds</th>";
        }
        echo '</tr>';
        
        # Contador do dia
        $dia = 1;
        
        # Corpo do calendário
        echo '<tr>';
        do {            
            for ($i = 1; $i <= 7; $i++) {
                # Verifica o dia inicial do mes
                if($dia == 1){
                    if($wday+1 == $i){
                        echo "<td align='center'";
                        
                        # Verifica se é hoje
                        if(($this->ano == date('Y')) AND ($this->mes == date('m')) AND ($dia == date('d'))){
                            echo " id='hoje'";
                        }else{
                            # Verifica se é Sábado ou Domeingo                                                
                            if(($i == 1) OR ($i == 7)){
                                echo " id='domingo'";
                            }
                        }
                        
                        # Exibe o dia
                        echo ">$dia</td>";
                        $dia++;
                    }else{
                        echo "<td align='center'";
                        if(($i == 1) OR ($i == 7)){
                            echo " id='domingo'";
                        }         
                        echo"> --- </td>";
                    }
                }else{
                    if($dia <= $dias){
                        echo "<td align='center'";
                        
                        # Verifica se é hoje
                        if(($this->ano == date('Y')) AND ($this->mes == date('m')) AND ($dia == date('d'))){
                            echo " id='hoje'";
                        }else{
                            # Verifica se é Sábado ou Domeingo                                                
                            if(($i == 1) OR ($i == 7)){
                                echo " id='domingo'";
                            }
                        }
                        
                        # Exibe o dia
                        echo ">$dia</td>";
                        $dia++;
                    }else{
                        echo "<td align='center'";
                        if(($i == 1) OR ($i == 7)){
                            echo " id='domingo'";
                        }         
                        echo"> --- </td>";
                    }
                }
            }
            echo '</tr><tr>';
        } while ($dia <= $dias);
        echo '</tr>';
        
        # termina a tabela
        echo '</table>';
    }
    
}