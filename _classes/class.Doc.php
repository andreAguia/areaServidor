<?php
 /**
 * Classe Doc
 * Classe de acesso as tabelas do banco de dados 
 * 
 * By Alat
 */
class Doc extends Bd{
    private $servidor = "localhost";        // servidor
    private $usuario = "root";              // usuário
    #private $senha = "chewbacca";           // senha
    private $senha = NULL;           // senha
    private $banco = "information_schema";  // nome do banco
    private $sgdb = "mysql";                // sgdb
    private $tabela;                        // tabela
    private $idCampo;                       // o nome do campo id
    private $log = TRUE;                    // habilita o log

    /**
    * Método Construtor
    */
    public function __construct(){
        parent::__construct($this->servidor,$this->usuario,$this->senha,$this->banco,$this->sgdb);
    }

    ###########################################################

    /**
    * Método set_tabela
    * 
    * @param  	$nomeTabela	-> Nome da tabela do banco de dados intra que ser� utilizada
    */
    public function set_tabela($nomeTabela){
        $this->tabela = $nomeTabela;
    }

    ###########################################################

    /**
    * Método set_idCampo
    * 
    * @param  	$idCampo	-> Nome do campo chave da tabela
    */
    public function set_idCampo($idCampo){
        $this->idCampo = $idCampo;
    }

    ###########################################################

    /**
    * Método set_senha
    * 
    * @param  	$senha	-> senha  no nbanco do usuario root
    */
    public function set_senha($senha){
        $this->senha = $senha;
    }

    ###########################################################


    /**
    * Método Gravar
    */
    public function gravar($campos = NULL,$valor = NULL,$idValor = NULL,$tabela = NULL,$idCampo = NULL,$alerta = TRUE){
        parent::gravar($campos,$valor,$idValor,$this->tabela,$this->idCampo,$alerta);

        # Grava o status na tabela servi�o sempre que a tabela movimento for atualizada
        if ($this->tabela == 'tbmovimento'){
            $lastId = parent::get_lastId();		# salva o last id da primeira grava��o (a que importa)
            parent::gravar(array('status','encarregado'),array($valor[3],$valor[4]),$valor[6],'tbservico','idservico',FALSE);
            parent::set_lastId($lastId);		# recupera o last id para o arquivo de log
        }
    }

    ###########################################################

    /**
    * Método Excluir
    */

    public function excluir($idValor = NULL,$tabela = NULL,$idCampo = 'id'){
        $erro = FALSE;		// Flag de erro
        $msgErro = NULL;	// Recipiente das mensagens de erro

        if ($this->tabela == 'tbregra'){
            # Verifica se existe alguma permissão com a regra a ser excluída
            $select = 'SELECT idPermissao
                         FROM tbpermissao
                        WHERE idRegra = '.$idValor;
            $numRows = parent::count($select);
            if($numRows > 0){
                $erro = TRUE;
                $msgErro = 'Existem '.$numRows.' permissão(ões) cadastrada(s) para essa regra. A mesma não pode ser excluída!!';
            }
        }

        # Executa a exclusão se não tiver erro
        if($erro){
            # Exibe o alerta
            $alert = new Alert($msgErro);
            $alert->show();
            return 0; # False -> o FALSE não funcionou então colocou 0
        }else{
            # efetua a exclusão
            parent::excluir($idValor,$this->tabela,$this->idCampo);
            return 1; # True 		
        }
    }

    ###########################################################
}