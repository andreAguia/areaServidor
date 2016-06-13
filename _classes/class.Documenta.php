<?php

class Documenta
{
 /**
  * Classe de documentação de classes ou funções
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  * 
  * @example exemplo.Documenta.php
  * 
  * @var private $arquivo string NULL O caminho e o arquivo a ser documentado
  * @var private $tipo    string NULL O tipo de arquivo: classe | funcao 
  */
    
    # da Classe
    private $nomeClasse = NULL;             // Guarda o nome da classe
    private $descricaoClasse = NULL;        // Guarda a descição da classe
    private $autorClasse = NULL;            // Autor da classe
    private $notaClasse = NULL;             // Nota da classe
    private $deprecatedClasse = FALSE;      // Se a classe está sendo descontinuada
    private $variaveisClasse = NULL;        // Array com as variáveis da classe
    private $exemploClasse = NULL;          // Arquivo de exemplo da classe
    
    # dos Métodos e/ Funções
    private $nomeMetodo = NULL;             // Array com os nomes dos métodos
    private $visibilidadeMetodo = NULL;     // Array com a visibilidade dos métodos (public, private ou protected)
    private $descricaoMetodo = NULL;        // Array com a descrição dos métodos
    private $syntaxMetodo = null;           // Array com a syntax do método
    private $retornoMetodo = null;          // Array com o valor retotnado do método
    private $notaMetodo = null;             // Array com uma nota do método
    private $deprecatedMetodo = NULL;       // Array informando se o método está sendo descontinuado
    private $parametrosMetodo = NULL;       // Array com os parâmetros de cada método
    private $exemploMetodo = NULL;          // Array com arquivos exemplos de códigos

###########################################################
    
    public function __construct($arquivo = NULL, $tipo = NULL){
    /**
     * Inicia a classe e informa o arquivo da classe ou da função a ser documentada.
     * 
     * @param $arquivo string NULL O arquivo com o caminho para se documentado
     * @param $tipo    string NULL O tipo de arquivo: classe | funcao 
     * 
     * @syntax $documenta = new Documenta($arquivo);     
     */    
    
    	# Variáveis
        
        $linhaComentarioClasse = NULL;  // Determina a linha do início do comentário da classe
        $numVariaveis = 0;              // Número de variáveis de uma classe
        $numMetodo = 0;                 // Número de métodos de uma classe
        $linhaComentarioMetodo = NULL;  // Determina a linha do início do comentário do método
        $linhaMetodo = null;      // Determina a linha da declaração do método
        $numParMetodo = 0;        // Determina o número de parâmetros de um método
        $metodoNome = null;       // Nome do método que está sendo exibido
        $temTabelaAberta = FALSE; // Flag que informa que existe uma tabela da lista de parâmetros aberta se a tag não for @param terá que fechá-la
        $temClasseAberta = FALSE; // Informa se é de uma classe ou de um método a tag
        $caracteresAceitos = '#(),.|/:çãõáéíúóâê1234567890';  // caracteres aceitos nas descrições de variáveis e parâmetros
        
        # Define o arquivo e caminho da classe
        $lines = file($arquivo,FILE_TEXT);

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
    }

###########################################################
    
    public function get_nomeClasse(){        
        /**
         * Informa o nome da classe.
         *
         * @syntax $documenta->get_nomeClasse();
        `*/
        
        return $this->nomeClasse;
    }
    
###########################################################     
    
    public function get_descricaoClasse(){        
        /**
         * Informa a descrição da classe.
         *
         * @syntax $documenta->get_descricaoClasse();
        `*/
        
        return $this->descricaoClasse;
    }
    
###########################################################       
      
    
    
}