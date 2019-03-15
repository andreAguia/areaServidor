<?php

class DocumentaClasse
{
 /**
  * Classe de documentação de classes ou funções
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  * 
  * @example exemplo.Documenta.php
  * 
  * @var private $tipo    string classe O tipo de arquivo: classe | funcao 
  */
    
    # da Classe
    private $nomeClasse = NULL;             // Guarda o nome da classe
    private $abstracaoClasse = NULL;        // Guarda a abstração da classe: abstract | final #######-> ainda falta implementar
    private $descricaoClasse = NULL;        // Guarda a descição da classe
    private $autorClasse = NULL;            // Autor da classe
    private $notaClasse = NULL;             // Array com as anotações importante. Repare que é array ou seja pode se ter mais de uma nota por classe / função
    private $deprecatedClasse = FALSE;      // Se a classe está sendo descontinuada
    private $variaveisClasse = NULL;        // Array com as variáveis da classe
    private $numVariaveis = 0;              // Inteiro que informa o número de variáveis de uma classe
    private $exemploClasse = NULL;          // Arquivo de exemplo da classe
    
    # dos Métodos
    private $numMetodo = 0;                 // Número de métodos de uma classe
    private $nomeMetodo = NULL;             // Array com os nomes dos métodos
    private $visibilidadeMetodo = NULL;     // Array com a visibilidade dos métodos (public, private ou protected)
    private $descricaoMetodo = NULL;        // Array com a descrição dos métodos
    private $syntaxMetodo = NULL;           // Array com a syntax do método
    private $retornoMetodo = NULL;          // Array com o valor retotnado do método
    private $notaMetodo = NULL;             // Array com uma nota do método
    private $deprecatedMetodo = NULL;       // Array informando se o método está sendo descontinuado
    private $parametrosMetodo = NULL;       // Array com os parâmetros de cada método
    private $exemploMetodo = NULL;          // Array com arquivos exemplos de códigos
    private $categoriaMetodo = NULL;        // Array com a categoria dos método
    private $autorMetodo = NULL;            // Array com o autor da função. Usado mais em funções de terceiros

###########################################################
    
    public function __construct($arquivo = NULL){
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
        $linhaComentarioMetodo = NULL;  // Determina a linha do início do comentário do método
        $linhaMetodo = NULL;            // Determina a linha da declaração do método
        
        $marcadorParametro = 0;         // indica se o modo de coleta de parâmetros está ligado ou não. 1 -> Ligado | 0 -> Desligado
        
        $numParMetodo = 0;              // Determina o número de parâmetros de um método
        $metodoNome = NULL;             // Nome do método que está sendo exibido
        $temTabelaAberta = FALSE;       // Flag que informa que existe uma tabela da lista de parâmetros aberta se a tag não for @param terá que fechá-la
        $temClasseAberta = FALSE;       // Informa se é de uma classe ou de um método a tag
        
        $caracteresAceitos = '#(),.|/:çãõáéíúóâê1234567890';  // caracteres especiais aceitos nas descrições de variáveis e parâmetros
        
        # Define o arquivo e caminho da classe
        $lines = file($arquivo,FILE_TEXT);
        
        # Percorre o arquivo e guarda os dados em um array
        foreach ($lines as $line_num => $line) {
            $line = htmlspecialchars($line);
            
            # Classe
            if (stristr($line, "class") AND ($line_num < 3)){
                $posicao = stripos($line,'class');
                $this->nomeClasse = substr($line, $posicao+6);
            }
            
            # Abstração
            if (stristr($line, "abstract") AND ($line_num < 3)){
                $this->abstracaoClasse = "abstract";
            }
            
            if (stristr($line, "final") AND ($line_num < 3)){
                $this->abstracaoClasse = "final";
            }

            # Verifica se é o começo de um comentário da classe
            if(stristr($line, "/**") AND ($this->numMetodo == 0)){
                $linhaComentarioClasse = $line_num;
            }

            # Descrição da classe
            if (($line_num == ($linhaComentarioClasse+1)) AND ($this->numMetodo == 0)){
                $posicao = stripos($line,'*');
                $this->descricaoClasse = substr($line, $posicao+2);
            }

            # Autor
            if (stristr($line, "@author")){
                $posicao = stripos($line,'@');
                $this->autorClasse = substr($line, $posicao+7);
            }

            # Nota
            if ((stristr($line, "@note")) AND ($this->numMetodo == 0)){
                $posicao = stripos($line,'@');
                $this->notaClasse[] = substr($line, $posicao+5);
            }

            # Deprecated
            if ((stristr($line, "@deprecated")) AND ($this->numMetodo == 0)){
                $this->deprecatedClasse = TRUE;
            }

            # Variáveis da Classe
            if ((stristr($line, "private")) AND ($this->numMetodo == 0)){

                # Retira aspas
                $line = preg_replace('/(")/', '', $line);
                $line = trim($line);
                
                # inicia a variável que será guardada a descrição 
                $descricao = NULL;

                $posicao = stripos($line,'private');

                # divide a linha em um array de palavras
                $piecesVar = str_word_count($line,1,$caracteresAceitos);

                # retira a palavra private
                #array_shift($piecesVar);

                # verifica quantas palavras tem na linha       
                $numPalavra = count($piecesVar);

                # agrupa as palavras da descrição
                for($i=5; $i<$numPalavra; $i++){
                    $descricao .= $piecesVar[$i]." ";
                }

                # Junta a variavel no novo array
                $this->variaveisClasse[] = array($piecesVar[0],$piecesVar[1],$piecesVar[4],$piecesVar[2],$descricao);          

                # incremente o número de variáveis
                $this->numVariaveis++;
            }

            # Example
            if ((stristr($line, "@example")) AND ($this->numMetodo == 0)){
                $posicao = stripos($line,'@');
                $this->exemploClasse = substr($line, $posicao+9);
            }
            
            # Métodos            
            if (stristr($line, "function")){
                $this->numMetodo++;                     // incrementa o número de métodos
                $posicao = stripos($line,'function');   // marca posição da palavra function
                $posicaoFinal = stripos($line,'(');     // marca posição final do nome do método
                $tamanho = $posicaoFinal-$posicao-9;    // define o tamanho 

                $this->nomeMetodo[$this->numMetodo] = substr($line, $posicao+9,$tamanho);   // extrai o nome do método
                $this->visibilidadeMetodo[$this->numMetodo] = trim(substr($line, 1, $posicao-2));
                $linhaMetodo = $line_num;
                
                if(substr($line, $posicaoFinal+1,1) <> ")"){
                    
                    # Liga o marcador
                    $marcadorParametro = 1;

                    # Coleta o primeiro parâmetro
                    $textoParametro = substr($line,$posicaoFinal+1);

                    # Divide o texto em pedaços
                    $pedaco = str_word_count($textoParametro,1,$caracteresAceitos);

                    # Inicia a variável da descrição
                    $descParam = NULL;

                    # Pega a descrição
                    for($i = 5; $i < count($pedaco); $i++){
                        $descParam .= $pedaco[$i].' ';
                    }

                    # Identifica o ultimo caractere do padrao. Que pode ser vírgula ou parenteses
                    $ultimaLetra = substr($pedaco[1], -1);

                    if($ultimaLetra == ")"){
                        $marcadorParametro = 0;
                    }

                    # Retira esse caractere
                    $pedaco[1] = substr_replace($pedaco[1], "", -1);
                    
                    ## Dando erro quando o parâmetro não tem valor padrão !!! REsolver

                    # Joga esse primeiro parâmetro para o array
                    $this->parametrosMetodo[$this->numMetodo][] = [$pedaco[0],$pedaco[3],$pedaco[1],$descParam];
                }
                
            }
            
            # Coleta os parâmetros
            
            
            # Parâmetros de um método
            if (stristr($line, "@param")){
                $descParam = NULL;

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
                $this->parametrosMetodo[$this->numMetodo][] = $piecesParam;
            }

            # Verifica se é o começo de um comentário do método
            if (stristr($line, "/**") AND ($this->numMetodo > 0)){
                $linhaComentarioMetodo = $line_num;
            }

            # Descrição do Método
            if (($line_num == ($linhaComentarioMetodo+1)) AND ($this->numMetodo > 0)){
                $posicao = stripos($line,'*');
                $this->descricaoMetodo[$this->numMetodo] = substr($line, $posicao+2);
            }

            # Autor
            if (stristr($line, "@author")){
                $posicao = stripos($line,'@');
                $this->autorMetodo[$this->numMetodo] = substr($line, $posicao+7);
            }

            # Syntax do método
            if (stristr($line, "@syntax")){
                $posicao = stripos($line,'@');
                $this->syntaxMetodo[$this->numMetodo] = substr($line, $posicao+8);
            }

            # Return
            if (stristr($line, "@return")){
                $posicao = stripos($line,'@');
                $this->retornoMetodo[$this->numMetodo] = substr($line, $posicao+8);
            }
            
            # Category
            if (stristr($line, "@category")){
                $posicao = stripos($line,'@');
                $this->categoriaMetodo[$this->numMetodo] = substr($line, $posicao+10);
            }

            # Example
            if (stristr($line, "@example")){
                $posicao = stripos($line,'@');
                $this->exemploMetodo[$this->numMetodo] = substr($line, $posicao+9);
            }    

            # Nota
            if (stristr($line, "@note")){
                $posicao = stripos($line,'@');
                $this->notaMetodo[$this->numMetodo][] = substr($line, $posicao+6);
            }

            # Deprecated (sendo descontinuado)
            if (stristr($line, "@deprecated")){
                $this->deprecatedMetodo[$this->numMetodo] = TRUE;
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
    
    public function get_abstracaoClasse(){        
        /**
         * Informa a abstração da classe.
         *
         * @syntax $documenta->get_abstracaoClasse();
        `*/
        
        return $this->abstracaoClasse;
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
    
    public function get_autorClasse(){        
        /**
         * Informa o autor da classe.
         *
         * @syntax $documenta->get_autorClasse();
        `*/
        
        return $this->autorClasse;
    }
    
###########################################################     
    
    public function get_notaClasse(){        
        /**
         * Informa, se tiver, uma nota explicativa da classe.
         *
         * @syntax $documenta->get_notaClasse();
        `*/
        
        return $this->notaClasse;
    }
    
###########################################################     
    
    public function get_deprecatedClasse(){        
        /**
         * Informa se a classe está ou não deprecated, ou seja, em descontinuação.
         *
         * @syntax $documenta->get_deprecatedClasse();
        `*/
        
        return $this->deprecatedClasse;
    }
    
###########################################################     
    
    public function get_variaveisClasse(){        
        /**
         * Retorna um array com  as informações das variávels da classe.
         *
         * @syntax $documenta->get_variaveisClasse();
        `*/
        
        return $this->variaveisClasse;
    }
    
##########################################################     
    
    public function get_numVariaveis(){        
        /**
         * Informa a quantidade de variáveis da classe.
         *
         * @syntax $documenta->get_numVariaveis();
        `*/
        
        return $this->numVariaveis;
    }
    
##########################################################     
    
    public function get_exemploClasse(){        
        /**
         * Informa o nome do arqwuivo na pasta de exemplos a ser exibido.
         *
         * @syntax $documenta->get_exemploClasse();
        `*/
        
        return $this->exemploClasse;
    }
    
##########################################################     
    
    public function get_numMetodo(){        
        /**
         * Informa o número de métodos de uma classe.
         *
         * @syntax $documenta->get_numMetodo();
        `*/
        
        return $this->numMetodo;
    }
    
##########################################################     
    
    public function get_nomeMetodo(){        
        /**
         * Fornece um array com os nomes dos métodos
         *
         * @syntax $documenta->get_nomeMetodo();
        `*/
        
        return $this->nomeMetodo;
    }
    
##########################################################     
    
    public function get_visibilidadeMetodo(){        
        /**
         * Fornece um array com a visibilidade de cada método
         *
         * @syntax $documenta->get_visibilidadeMetodo();
        `*/
        
        return $this->visibilidadeMetodo;
    }
    
##########################################################     
    
    public function get_descricaoMetodo(){        
        /**
         * Fornece um array com a descrição de cada método
         *
         * @syntax $documenta->get_descricaoMetodo();
        `*/
        
        return $this->descricaoMetodo;
    }
    
##########################################################     
    
    public function get_deprecatedMetodo(){        
        /**
         * Fornece array de TRUE OR FALSE para informar se o método está sendo, ou não, descontinuado
         *
         * @syntax $documenta->get_deprecatedMetodo();
        `*/
        
        return $this->deprecatedMetodo;
    }
    
##########################################################     
    
    public function get_syntaxMetodo(){        
        /**
         * Fornece array com a syntax do método
         *
         * @syntax $documenta->get_syntaxMetodo();
        `*/
        
        return $this->syntaxMetodo;
    }
    
##########################################################     
    
    public function get_retornoMetodo(){        
        /**
         * Fornece array com o retorno de cada método
         *
         * @syntax $documenta->get_retornoMetodo();
        `*/
        
        return $this->retornoMetodo;
    }
    
##########################################################     
    
    public function get_notaMetodo(){        
        /**
         * Fornece array com a nota dos métidos
         *
         * @syntax $documenta->get_notaMetodo();
        `*/
        
        return $this->notaMetodo;
    }
    
##########################################################     
    
    public function get_parametrosMetodo(){        
        /**
         * Fornece array com os parametros do método
         *
         * @syntax $documenta->get_parametrosMetodo();
        `*/
        
        return $this->parametrosMetodo;
    }
    
##########################################################     
    
    public function get_exemploMetodo(){        
        /**
         * Fornece array com os exemplos do método
         *
         * @syntax $documenta->get_exemploMetodo();
        `*/
        
        return $this->exemploMetodo;
    }
    
###########################################################     
    
    public function get_categoriaMetodo(){        
        /**
         * Fornece array com a categoria do método
         *
         * @syntax $documenta->get_categoriaMetodo();
        `*/
        
        return $this->categoriaMetodo;
    }
    
###########################################################     
    
    public function get_autorMetodo(){        
        /**
         * Fornece array com o autor da função de terceiros
         *
         * @syntax $documenta->get_autorMetodo();
        `*/

        return $this->autorMetodo;
    }

###########################################################
}
