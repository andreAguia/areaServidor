<?php
/**
 * documentaClasse
 * 
 * Gera documentação de uma classe
 */

# Configuração
include ("_config.php");

# Cabeçalho
AreaServidor::cabecalho();

# Limita o tamanho da tela
$grid = new Grid();
$grid->abreColuna(12);

# Pega a classe a ser documentada
$arquivoClasse = get('classe');

# Verifica o método a ser exibido
$metodo = get('metodo');

# Começa uma nova página
$page = new Page();
$page->iniciaPagina();

# Botão voltar
$linkBotao1 = new Link("Voltar",'documentacao.php');
$linkBotao1->set_class('button');
$linkBotao1->set_title('Volta para a página anterior');
$linkBotao1->set_accessKey('V');

# Botão codigo
$linkBotao2 = new Link("Código","?classe=$arquivoClasse&metodo=codigo");
if($metodo == "codigo"){
    $linkBotao2->set_class('disabled button');
}
else{
    $linkBotao2->set_class('button');
}
$linkBotao2->set_title('Exibe o código fonte');
$linkBotao2->set_accessKey('C');

# Cria um menu
$menu = new MenuBar();
$menu->add_link($linkBotao1,"left");
$menu->add_link($linkBotao2,"right");
$menu->show();

# Variáveis
$nomeClasse = NULL;             // Guarda o nome da classe
$linhaComentarioClasse = NULL;  // Determina a linha do início do comentário da classe
$descricaoClasse = NULL;        // Guarda a descição da classe
$autorClasse = NULL;            // Autor da classe
$notaClasse = NULL;             // Nota da classe
$deprecatedClasse = FALSE;      // Se a classe está sendo descontinuada
$variaveisClasse[] = NULL;      // Variáveis da classe
$exemploClasse = NULL;          // Arquivo de exemplo da classe
$numVariaveis = 0;              // Número de variáveis de uma classe

$numMetodo = 0;                 // Número de métodos de uma classe
$linhaComentarioMetodo = NULL;  // Determina a linha do início do comentário do método
$nomeMetodo[] = NULL;           // Array com os nomes dos métodos
$visibilidadeMetodo[] = NULL;   // Array com a visibilidade dos métodos (public, private ou protected)
$descricaoMetodo[] = NULL;      // Array com a descrição dos métodos
$syntaxMetodo[] = null;         // Array com a syntax do método
$retornoMetodo[] = null;        // Array com o valor retotnado do método
$notaMetodo[] = null;           // Array com uma nota do método
$deprecatedMetodo[] = NULL;     // Array informando se o método está sendo descontinuado
$parametrosMetodo[] = NULL;     // Array com os parâmetros de cada método
$exemploMetodo[] = NULL;        // Array com arquivos exemplos de códigos

$linhaMetodo = null;      // Determina a linha da declaração do método
$numParMetodo = 0;        // Determina o número de parâmetros de um método
$metodoNome = null;       // Nome do método que está sendo exibido
$temTabelaAberta = FALSE; // Flag que informa que existe uma tabela da lista de parâmetros aberta se a tag não for @param terá que fechá-la
$temClasseAberta = FALSE; // Informa se é de uma classe ou de um método a tag

$caracteresAceitos = '#(),.|/:çãõáéíúóâê1234567890';  // caracteres aceitos nas descrições de variáveis e parâmetros

# Define o arquivo e caminho da classe
$lines = file(PASTA_CLASSES_GERAIS."{$arquivoClasse}.php",FILE_TEXT);

# Percorre o arquivo e guarda os dados em um array
foreach ($lines as $line_num => $line) {
    $line = htmlspecialchars($line);
  
    # Classe
    if (stristr($line, "class") AND ($line_num < 3)){
        $posicao = stripos($line,'class');
        $nomeClasse = substr($line, $posicao+6);
    }
    
    # Verifica se é o começo de um comentário da classe
    if(stristr($line, "/**") AND ($numMetodo == 0)){
        $linhaComentarioClasse = $line_num;
    }
    
    # Descrição da classe
    if (($line_num == ($linhaComentarioClasse+1)) AND ($numMetodo == 0)){
        $posicao = stripos($line,'*');
        $descricaoClasse = substr($line, $posicao+2);
    }
    
    # Autor
    if (stristr($line, "@author")){
        $posicao = stripos($line,'@');
        $autorClasse = substr($line, $posicao+7);
    }
    
    # Nota
    if ((stristr($line, "@note")) AND ($numMetodo == 0)){
        $posicao = stripos($line,'@');
        $notaClasse = substr($line, $posicao+5);
    }
    
    # Deprecated
    if ((stristr($line, "@deprecated")) AND ($numMetodo == 0))
        $deprecatedClasse = TRUE;
    
    # Nome do grupo de variáveis
    if ((stristr($line, "@group")) AND ($numMetodo == 0)){
        $posicao = stripos($line,'@');
        $grupo = substr($line, $posicao+6);
        $variaveisClasse[] = array("group",$grupo);
    }
    
    # Variáveis da Classe
    if ((stristr($line, "@var")) AND ($numMetodo == 0)){
        
        # inicia a variável que será guardada a descrição 
        $descricao = null;
        
        # divide a linha em um array de palavras
        $piecesVar = str_word_count($line,1,$caracteresAceitos);
        
        # retira a palavra var
        array_shift($piecesVar);
        
        # verifica quantas palavras tem na linha       
        $numPalavra = count($piecesVar);
        
        # agrupa as palavras da descrição
        for($i=4;$i<$numPalavra;$i++){
            $descricao .= $piecesVar[$i]." ";
        }
        
        # Junta a variavel no novo array
        $variaveisClasse[] = array($piecesVar[0],$piecesVar[1],$piecesVar[2],$piecesVar[3],$descricao);          
        
        # incremente o número de variáveis
        $numVariaveis++;
    }
    
    # Example
    if ((stristr($line, "@example")) AND ($numMetodo == 0)){
        $posicao = stripos($line,'@');
        $exemploClasse = substr($line, $posicao+9);
    }    

    # Métodos
    # Nome do Método
    if (stristr($line, "public function")){
        $numMetodo++;                           // incrementa o número de métodos
        $posicao = stripos($line,'function');   // marca posição da palavra function
        $posicaoFinal = stripos($line,'(');     // marca posição final do nome do método
        $tamanho = $posicaoFinal-$posicao-9;    // define o tamanho 
        
        $nomeMetodo[$numMetodo] = substr($line, $posicao+9,$tamanho);   // extrai o nome do método
        $visibilidadeMetodo[$numMetodo] = 'public';
        $linhaMetodo = $line_num;
    }
    
    if (stristr($line, "private function")){
        $numMetodo++;                           // incrementa o número de métodos
        $posicao = stripos($line,'function');   // marca posição da palavra function
        $posicaoFinal = stripos($line,'(');     // marca posição final do nome do método
        $tamanho = $posicaoFinal-$posicao-9;    // define o tamanho 
        
        $nomeMetodo[$numMetodo] = substr($line, $posicao+9,$tamanho);   // extrai o nome do método
        $visibilidadeMetodo[$numMetodo] = 'private';
        $linhaMetodo = $line_num;
    }
    
    if (stristr($line, "protected function")){
        $numMetodo++;                           // incrementa o número de métodos
        $posicao = stripos($line,'function');   // marca posição da palavra function
        $posicaoFinal = stripos($line,'(');     // marca posição final do nome do método
        $tamanho = $posicaoFinal-$posicao-9;    // define o tamanho 
        
        $nomeMetodo[$numMetodo] = substr($line, $posicao+9,$tamanho);   // extrai o nome do método
        $visibilidadeMetodo[$numMetodo] = 'protected';
        $linhaMetodo = $line_num;
    }
    
    # Verifica se é o começo de um comentário do método
    if (stristr($line, "/**") AND ($numMetodo > 0)){
        $linhaComentarioMetodo = $line_num;
    }
    
    # Descrição do Método
    if (($line_num == ($linhaComentarioMetodo+1)) AND ($numMetodo > 0)){
        $posicao = stripos($line,'*');
        $descricaoMetodo[$numMetodo] = substr($line, $posicao+2);
    }
    
    # Syntax do método
    if (stristr($line, "@syntax")){
        $posicao = stripos($line,'@');
        $syntaxMetodo[$numMetodo] = substr($line, $posicao+8);
    }
    
    # Return
    if (stristr($line, "@return")){
        $posicao = stripos($line,'@');
        $retornoMetodo[$numMetodo] = substr($line, $posicao+8);
    }
    
    # Example
    if (stristr($line, "@example")){
        $posicao = stripos($line,'@');
        $exemploMetodo[$numMetodo] = substr($line, $posicao+9);
    }    
    
    # Nota
    if (stristr($line, "@note")){
        $posicao = stripos($line,'@');
        $notaMetodo[$numMetodo] = substr($line, $posicao+6);
    }
      
    # Deprecated (sendo descontinuado)
    if (stristr($line, "@deprecated")){
        $deprecatedMetodo[$numMetodo] = TRUE;
    }
    
    # Parâmetros de um método
    if (stristr($line, "@param")){
    
        $descParam = null;
        
        # Pega a linha de parâmetros
        $piecesParam = str_word_count($line,1,$caracteresAceitos);
    
        # Rerira a palavra param do array
        array_shift($piecesParam);
        
        # Pega a descrição
        for($i=3; $i<count($piecesParam); $i++){
            $descParam .= $piecesParam[$i].' ';
        }
        
        # Joga a descrição para a quarta posição do array
        $piecesParam[3] = $descParam;
        
        # Joga para para o array de parâmetros
        $parametrosMetodo[$numMetodo][] = $piecesParam;
      }
}

$grid->fechaColuna();
$grid->fechaGrid();

# Divide a tela
$grid2 = new Grid();
$grid2->abreColuna(4,3);

# Coluna de atalhos para os métodos da classe
$callout = new Callout();
$callout->abre();

# Classe
echo '<h4>';
echo '<a href="?classe='.$arquivoClasse.'">';
echo $nomeClasse;
echo '</a>';
echo '</h4>';

# Percorre os métodos
for ($i=1; $i <= $numMetodo;$i++){
    # link
    echo '<a href="?classe='.$arquivoClasse.'&metodo='.$i.'" title="'.$descricaoMetodo[$i].'">';
    if((isset($deprecatedMetodo[$i])) AND ($deprecatedMetodo[$i]))
        echo '<del>'.$nomeMetodo[$i].'</del>';
    else
        echo $nomeMetodo[$i];    
    
    echo '</a>';
    br();
}

echo '</div>';  // callout
$grid2->fechaColuna();

# Coluna da documentação detalhada
$grid2->abreColuna(8,9);

switch ($metodo)
{
    case "" :
        ### Classe
        echo '<div class="callout success">';
        
        # Nome
        echo '<h4>'.$nomeClasse.'</h4>';
        
        # Decrição
        echo $descricaoClasse;
        br(2);
        
        # Autor
        if(!is_null($autorClasse))
            echo '<small>Autor: '.$autorClasse.'</small>';
            
        hr();
        
        # Nota
        if(!is_null($notaClasse)){
            echo 'Nota:';
            echo '<div class="callout warning">';
            echo $notaClasse;
            echo '</div>';
        }
        
        # Deprecated
        if($deprecatedClasse){
            echo '<div class="callout alert">';
            echo '<h6>DEPRECATED</h6> Esta classe deverá ser descontiuada nas próximas versões.<br/>Seu uso é desaconselhado.';
            echo '</div>';
        }
        
        # Variáveis da Classe
        if($numVariaveis > 0){
            echo 'Variáveis da Classe:';
            br();
            $novoArray = null;      // Armazena a tabela
            $grupoAnterior = null;  // Guarda o nome do grupo anterior
            $grupo = 0;             // Qual grupo será exibido

            foreach ($variaveisClasse as $vc){
                if($vc[0] == "group"){
                    if($grupo == 0){
                        $grupoAnterior = $vc[1];
                        $grupo++;
                    }
                    elseif($grupo > 0) {
                        echo $grupoAnterior;
                        br();

                        if($grupo == 1){
                            array_shift($novoArray);
                        }

                        $tabela = new Tabela();
                        $tabela->set_conteudo($novoArray);
                        $tabela->set_label(array('Visibilidade','Nome','Tipo','Padrão','Descrição'));
                        $tabela->set_align(array("center","center","center","center","left"));
                        $tabela->set_width(array(10,10,10,10,60));
                        $tabela->show(); 

                        $grupoAnterior = $vc[1];
                        $novoArray = null;
                        $grupo++;
                    }                 
                }
                else{
                    $novoArray[] = $vc;
                }   
            }
            # Exibe a lista de variáveis quando não se definiu grupos    
            if($grupo == 0){        
                $tabela = new Tabela();
                array_shift($variaveisClasse);     
                $tabela->set_conteudo($variaveisClasse);
                $tabela->set_label(array('Visibilidade','Nome','Tipo','Padrão','Descrição'));
                $tabela->set_align(array("center","center","center","center","left"));
                $tabela->set_width(array(10,10,10,10,60));
                $tabela->show();
            }
            else{
                # Exibe o último grupo de variáveis
                echo $grupoAnterior;
                br();
                $tabela = new Tabela();
                #array_shift($variaveisClasse);     
                $tabela->set_conteudo($novoArray);
                $tabela->set_label(array('Visibilidade','Nome','Tipo','Padrão','Descrição'));
                $tabela->set_align(array("center","center","center","center","left"));
                $tabela->set_width(array(10,10,10,10,60));
                $tabela->show(); 
            }            
        }
        
        # Exemplo
        if(isset($exemploClasse)){
            # Define o arquivo de exemplo
            $arquivoExemplo = PASTA_CLASSES_GERAIS."exemplos/".rtrim($exemploClasse);
            
            # Verifica se o arquivo existe
            if(file_exists($arquivoExemplo)){
            
                # Exibe o exemplo
                echo 'Exemplo:';
                
                # Cria borda para o exemplo
                $calloutExemplo = new Callout();
                $calloutExemplo->abre();
                
                include PASTA_CLASSES_GERAIS."exemplos/".rtrim($exemploClasse);
                
                $calloutExemplo->fecha();

                # Exibe o código do exemplo
                echo 'Código do exemplo:';
                echo '<pre>';

                # Variável que conta o número da linha
                $numLinhaExemplo = 1;
            
                # Percorre o arquivo
                $linesExample = file($arquivoExemplo);

                # Percorre o arquivo e guarda os dados em um array
                foreach ($linesExample as $linha) {
                    $linha = htmlspecialchars($linha);
                    
                    # Exibe o número da linha
                    echo "<span id='numLinhaCodigo'>".formataNumLinha($numLinhaExemplo)."</span> ";
                    
                    # Exibe o código
                    echo $linha;
                    
                    # Incrementa o ~umero da linha
                    $numLinhaExemplo++;
                }
                echo '</pre>';
            }
            else{
                echo 'Exemplo:';
                $callout1 = new Callout();
                $callout1->abre();
                echo "Arquivo de exemplo não encontrado";
                $callout1->fecha();
            }
            
            
            br();
        }            
        break;
        
    case "codigo" :
        echo '<pre>';
            
        # Define o arquivo da classe
        $arquivoExemplo = PASTA_CLASSES_GERAIS.rtrim($arquivoClasse).".php";
        
        # Exibe o nome do arquivo
        echo str_repeat("#", 80);
        br();
        echo '# Arquivo:'.$arquivoExemplo;
        br();       
        echo str_repeat("#", 80);
        br(2);

        # variável que conta o número da linha
        $numLinha = 1;
        
        # Verifica a existência do arquivo
        if(file_exists($arquivoExemplo)){
            $linesCodigo = file($arquivoExemplo);

            # Percorre o arquivo e guarda os dados em um array
            foreach ($linesCodigo as $linha) {
                $linha = htmlspecialchars($linha);
                    
                    # Exibe o número da linha
                    echo "<span id='numLinhaCodigo'>".formataNumLinha($numLinha)."</span> ";
                    
                    # Exibe o código
                    echo $linha;
                    
                    # Incrementa o ~umero da linha
                    $numLinha++;
            }
        }
        else{
            echo "Arquivo de exemplo não encontrado";
        }

        echo '</pre>';
        break;
    
    default:
        ### Método
        echo '<div class="callout primary">';
        
        # Nome
        echo '<h5> Método '.$nomeMetodo[$metodo].'</h5>';
        
        # Visibilidade
        echo '<small>('.$visibilidadeMetodo[$metodo].')</small>';
        
        # Descrição
        br(2);
        echo $descricaoMetodo[$metodo];
        
        # Deprecated        
        if((isset($deprecatedMetodo[$metodo])) AND ($deprecatedMetodo[$metodo])) {
            br(2);
            echo '<div class="callout alert">';
            echo '<h6>DEPRECATED</h6> Este método deverá ser descontiuado nas próximas versões.<br/>Seu uso é desaconselhado.';
            echo '</div>';
        }
        
        hr();
        
        # Syntax do método
        if(isset($syntaxMetodo[$metodo])){
            echo 'Sintaxe:';
            #echo '<div class="callout secondary">';
            echo '<pre>'.$syntaxMetodo[$metodo].'</pre>';
           # echo '</div>';
            br();
        }
        
        # Return
        if(isset($retornoMetodo[$metodo])){
          echo 'Valor Retornado:';
          echo '<div class="callout secondary">';
          echo $retornoMetodo[$metodo];
          echo '</div>';
        }
        
        # Nota
        if(isset($notaMetodo[$metodo])){
            $posicao = stripos($line,'@');
            echo 'Nota:';
            echo '<div class="callout warning">';
            echo $notaMetodo[$metodo];
            echo '</div>';
        }
      
        # Parâmetros de um método
        if(isset($parametrosMetodo[$metodo])){
            echo 'Parâmetros:';

            $tabela = new Tabela();
            #array_shift($lista);     
            $tabela->set_conteudo($parametrosMetodo[$metodo]);
            $tabela->set_label(array('Nome','Tipo','Padrão','Descrição'));
            $tabela->set_align(array("center","center","center","left"));
            $tabela->set_width(array(10,10,10,60));
            $tabela->show();
        }
        
        # Exemplo
        if(isset($exemploMetodo[$metodo])){
            echo 'Exemplo:';
            #echo '<div class="callout secondary">';
            echo '<pre>';
            $linesExample = file(PASTA_CLASSES_GERAIS."exemplos/".rtrim($exemploMetodo[$metodo]));
            
            # Percorre o arquivo e guarda os dados em um array
            foreach ($linesExample as $linha) {
                $linha = htmlspecialchars($linha);
                echo $linha;
            }
            echo '</pre>';
           # echo '</div>';
            br();
        }
        break;     
}

$callout->fecha();

$grid2->fechaColuna();
$grid2->fechaGrid();

$page->terminaPagina();