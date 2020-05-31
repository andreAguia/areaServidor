<?php

class DocumentaFuncao {

    /**
     * Classe de documentação de classes ou funções
     * 
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @example exemplo.Documenta.php
     * 
     * @var private $tipo    string classe O tipo de arquivo: classe | funcao 
     */
    # das Funções
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

    public function __construct($arquivo = NULL) {
        /**
         * Inicia a classe e informa o arquivo da classe ou da função a ser documentada.
         * 
         * @param $arquivo string NULL O arquivo com o caminho para se documentado
         * @param $tipo    string NULL O tipo de arquivo: classe | funcao 
         * 
         * @syntax $documenta = new Documenta($arquivo);     
         */
        # Variáveis                     
        $linhaComentarioMetodo = NULL;  // Determina a linha do início do comentário do método
        $linhaMetodo = NULL;            // Determina a linha da declaração do método        

        $caracteresAceitos = '#(),.|/:çãõáéíúóâê1234567890';  // caracteres especiais aceitos nas descrições de variáveis e parâmetros
        # Define o arquivo e caminho da classe
        $lines = file($arquivo, FILE_TEXT);

        # Percorre o arquivo e guarda os dados em um array
        foreach ($lines as $line_num => $line) {

            # Métodos        
            if (stristr($line, "public function")) {
                $this->numMetodo++;                     // incrementa o número de métodos
                $posicao = stripos($line, 'function');   // marca posição da palavra function
                $posicaoFinal = stripos($line, '(');     // marca posição final do nome do método
                $tamanho = $posicaoFinal - $posicao - 9;    // define o tamanho 

                $this->nomeMetodo[$this->numMetodo] = substr($line, $posicao + 9, $tamanho);   // extrai o nome do método
                $this->visibilidadeMetodo[$this->numMetodo] = 'public';
                $linhaMetodo = $line_num;
            }

            if (stristr($line, "private function")) {
                $this->numMetodo++;                     // incrementa o número de métodos
                $posicao = stripos($line, 'function');   // marca posição da palavra function
                $posicaoFinal = stripos($line, '(');     // marca posição final do nome do método
                $tamanho = $posicaoFinal - $posicao - 9;    // define o tamanho 

                $this->nomeMetodo[$this->numMetodo] = substr($line, $posicao + 9, $tamanho);   // extrai o nome do método
                $this->visibilidadeMetodo[$this->numMetodo] = 'private';
                $linhaMetodo = $line_num;
            }

            if (stristr($line, "protected function")) {
                $this->numMetodo++;                     // incrementa o número de métodos
                $posicao = stripos($line, 'function');   // marca posição da palavra function
                $posicaoFinal = stripos($line, '(');     // marca posição final do nome do método
                $tamanho = $posicaoFinal - $posicao - 9;    // define o tamanho 

                $this->nomeMetodo[$this->numMetodo] = substr($line, $posicao + 9, $tamanho);   // extrai o nome do método
                $this->visibilidadeMetodo[$this->numMetodo] = 'protected';
                $linhaMetodo = $line_num;
            }

            # Verifica se é o começo de um comentário do método
            if (stristr($line, "/**") AND ($this->numMetodo > 0)) {
                $linhaComentarioMetodo = $line_num;
            }

            # Descrição do Método
            if (($line_num == ($linhaComentarioMetodo + 1)) AND ($this->numMetodo > 0)) {
                $posicao = stripos($line, '*');
                $this->descricaoMetodo[$this->numMetodo] = substr($line, $posicao + 2);
            }

            # Autor
            if (stristr($line, "@author")) {
                $posicao = stripos($line, '@');
                $this->autorMetodo[$this->numMetodo] = substr($line, $posicao + 7);
            }

            # Syntax do método
            if (stristr($line, "@syntax")) {
                $posicao = stripos($line, '@');
                $this->syntaxMetodo[$this->numMetodo] = substr($line, $posicao + 8);
            }

            # Return
            if (stristr($line, "@return")) {
                $posicao = stripos($line, '@');
                $this->retornoMetodo[$this->numMetodo] = substr($line, $posicao + 8);
            }

            # Category
            if (stristr($line, "@category")) {
                $posicao = stripos($line, '@');
                $this->categoriaMetodo[$this->numMetodo] = substr($line, $posicao + 10);
            }

            # Example
            if (stristr($line, "@example")) {
                $posicao = stripos($line, '@');
                $this->exemploMetodo[$this->numMetodo] = substr($line, $posicao + 9);
            }

            # Nota
            if (stristr($line, "@note")) {
                $posicao = stripos($line, '@');
                $this->notaMetodo[$this->numMetodo][] = substr($line, $posicao + 6);
            }

            # Deprecated (sendo descontinuado)
            if (stristr($line, "@deprecated")) {
                $this->deprecatedMetodo[$this->numMetodo] = TRUE;
            }

            # Parâmetros de um método
            if (stristr($line, "@param")) {

                $descParam = NULL;

                # Pega a linha de parâmetros
                $piecesParam = str_word_count($line, 1, $caracteresAceitos);

                # Rerira a palavra param do array
                array_shift($piecesParam);

                # Pega a descrição
                for ($i = 3; $i < count($piecesParam); $i++) {
                    $descParam .= $piecesParam[$i] . ' ';
                }

                # Joga a descrição para a quarta posição do array
                $piecesParam[3] = $descParam;

                # Joga para para o array de parâmetros
                $this->parametrosMetodo[$this->numMetodo][] = $piecesParam;
            }
        }
    }

###########################################################

    public function get_nomeFuncao() {
        /**
         * Informa o nome da classe.
         *
         * @syntax $documenta->get_nomeClasse();
          ` */
        return $this->nomeFuncao;
    }

###########################################################     

    public function get_abstracaoClasse() {
        /**
         * Informa a abstração da classe.
         *
         * @syntax $documenta->get_abstracaoClasse();
          ` */
        return $this->abstracaoClasse;
    }

###########################################################          

    public function get_descricaoClasse() {
        /**
         * Informa a descrição da classe.
         *
         * @syntax $documenta->get_descricaoClasse();
          ` */
        return $this->descricaoClasse;
    }

###########################################################     

    public function get_autorClasse() {
        /**
         * Informa o autor da classe.
         *
         * @syntax $documenta->get_autorClasse();
          ` */
        return $this->autorClasse;
    }

###########################################################     

    public function get_notaClasse() {
        /**
         * Informa, se tiver, uma nota explicativa da classe.
         *
         * @syntax $documenta->get_notaClasse();
          ` */
        return $this->notaClasse;
    }

###########################################################     

    public function get_deprecatedClasse() {
        /**
         * Informa se a classe está ou não deprecated, ou seja, em descontinuação.
         *
         * @syntax $documenta->get_deprecatedClasse();
          ` */
        return $this->deprecatedClasse;
    }

###########################################################     

    public function get_variaveisClasse() {
        /**
         * Retorna um array com  as informações das variávels da classe.
         *
         * @syntax $documenta->get_variaveisClasse();
          ` */
        return $this->variaveisClasse;
    }

##########################################################     

    public function get_numVariaveis() {
        /**
         * Informa a quantidade de variáveis da classe.
         *
         * @syntax $documenta->get_numVariaveis();
          ` */
        return $this->numVariaveis;
    }

##########################################################     

    public function get_exemploClasse() {
        /**
         * Informa o nome do arqwuivo na pasta de exemplos a ser exibido.
         *
         * @syntax $documenta->get_exemploClasse();
          ` */
        return $this->exemploClasse;
    }

##########################################################     

    public function get_numMetodo() {
        /**
         * Informa o número de métodos de uma classe.
         *
         * @syntax $documenta->get_numMetodo();
          ` */
        return $this->numMetodo;
    }

##########################################################     

    public function get_nomeMetodo() {
        /**
         * Fornece um array com os nomes dos métodos
         *
         * @syntax $documenta->get_nomeMetodo();
          ` */
        return $this->nomeMetodo;
    }

##########################################################     

    public function get_visibilidadeMetodo() {
        /**
         * Fornece um array com a visibilidade de cada método
         *
         * @syntax $documenta->get_visibilidadeMetodo();
          ` */
        return $this->visibilidadeMetodo;
    }

##########################################################     

    public function get_descricaoMetodo() {
        /**
         * Fornece um array com a descrição de cada método
         *
         * @syntax $documenta->get_descricaoMetodo();
          ` */
        return $this->descricaoMetodo;
    }

##########################################################     

    public function get_deprecatedMetodo() {
        /**
         * Fornece array de TRUE OR FALSE para informar se o método está sendo, ou não, descontinuado
         *
         * @syntax $documenta->get_deprecatedMetodo();
          ` */
        return $this->deprecatedMetodo;
    }

##########################################################     

    public function get_syntaxMetodo() {
        /**
         * Fornece array com a syntax do método
         *
         * @syntax $documenta->get_syntaxMetodo();
          ` */
        return $this->syntaxMetodo;
    }

##########################################################     

    public function get_retornoMetodo() {
        /**
         * Fornece array com o retorno de cada método
         *
         * @syntax $documenta->get_retornoMetodo();
          ` */
        return $this->retornoMetodo;
    }

##########################################################     

    public function get_notaMetodo() {
        /**
         * Fornece array com a nota dos métidos
         *
         * @syntax $documenta->get_notaMetodo();
          ` */
        return $this->notaMetodo;
    }

##########################################################     

    public function get_parametrosMetodo() {
        /**
         * Fornece array com os parametros do método
         *
         * @syntax $documenta->get_parametrosMetodo();
          ` */
        return $this->parametrosMetodo;
    }

##########################################################     

    public function get_exemploMetodo() {
        /**
         * Fornece array com os exemplos do método
         *
         * @syntax $documenta->get_exemploMetodo();
          ` */
        return $this->exemploMetodo;
    }

###########################################################     

    public function get_categoriaMetodo() {
        /**
         * Fornece array com a categoria do método
         *
         * @syntax $documenta->get_categoriaMetodo();
          ` */
        return $this->categoriaMetodo;
    }

###########################################################     

    public function get_autorMetodo() {
        /**
         * Fornece array com o autor da função de terceiros
         *
         * @syntax $documenta->get_autorMetodo();
          ` */
        return $this->autorMetodo;
    }

###########################################################
}
