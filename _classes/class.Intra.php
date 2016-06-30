<?php
 /**
 * Classe Intra
 * Classe de acesso as tabelas do banco de dados Intra
 * 
 * By Alat
 */
class Intra extends Bd
{
    private $servidor = "localhost";        // servidor
    private $usuario = "intranet";          // usuário
    private $senha = "txzVHnMdh53ZWX9p";    // senha
    private $banco = "admin";               // nome do banco
    private $sgdb = "mysql";                // sgdb
    private $tabela;                        // tabela
    private $idCampo;                       // o nome do campo id
    private $log = true;                    // habilita o log

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
    public function set_tabela($nomeTabela)
    {
        $this->tabela = $nomeTabela;
    }

    ###########################################################

    /**
    * Método set_idCampo
    * 
    * @param  	$idCampo	-> Nome do campo chave da tabela
    */
    public function set_idCampo($idCampo)
    {
        $this->idCampo = $idCampo;
    }

    ###########################################################


    /**
    * Método Gravar
    */
    public function gravar($campos = NULL,$valor = NULL,$idValor = NULL,$tabela = NULL,$idCampo = NULL,$alerta = TRUE){
    
        parent::gravar($campos,$valor,$idValor,$this->tabela,$this->idCampo,$alerta);

        # Grava o status na tabela servi�o sempre que a tabela movimento for atualizada
        if ($this->tabela == 'tbmovimento')
        {
            $lastId = parent::get_lastId();		# salva o last id da primeira grava��o (a que importa)
            parent::gravar(array('status','encarregado'),array($valor[3],$valor[4]),$valor[6],'tbservico','idservico',false);
            parent::set_lastId($lastId);		# recupera o last id para o arquivo de log
        }
    }

    ###########################################################

    /**
    * Método Excluir
    */

    public function excluir($idValor = NULL,$tabela = NULL,$idCampo = 'id'){
    
        $erro = false;		// Flag de erro
        $msgErro = NULL;	// Recipiente das mensagens de erro

        if ($this->tabela == 'tbregra')
        {
            # Verifica se existe alguma permissão com a regra a ser excluída
            $select = 'SELECT idPermissao
                         FROM tbpermissao
                        WHERE idRegra = '.$idValor;
            $numRows = parent::count($select);
            if($numRows > 0)
            {
                $erro = true;
                $msgErro = 'Existem '.$numRows.' permissão(ões) cadastrada(s) para essa regra. A mesma não pode ser excluída!!';
            }
        }

        # Executa a exclusão se não tiver erro
        if($erro)
        {
            # Exibe o alerta
            $alert = new Alert($msgErro);
            $alert->show();
            return 0; # False -> o false não funcionou então colocou 0
        }
        else
        {
            # efetua a exclusão
            parent::excluir($idValor,$this->tabela,$this->idCampo);
            return 1; # True 		
        }
    }
    
    ###########################################################

    /**
    * Método get_variavel
    * 
    * Método que exibe o conteúdo de uma variável de configuração
    * 
    * @param	string	$var	-> o nome da variável
    */

    public function get_variavel($variavel)
    {
        $select = 'SELECT valor
                     FROM tbvariaveis
                    WHERE nome = "'.$variavel.'"';
        $valor = parent::select($select,false);
        return $valor[0];
    }

    ###########################################################

    /**
    * Método set_variavel
    * 
    * Método que grava um conteúdo em uma variável de configuração
    * 
    * @param	string	$var	-> o nome da variável
    */

    public function set_variavel($variavel,$valor)
    {
        $valor = retiraAspas($valor);
        $this->set_tabela('tbvariaveis');
        $this->set_idCampo('idVariaveis');
        $this->gravar(array('valor'),array($valor),$variavel,null,null,false);
    }

    ###########################################################
	
	/**
	 * Método verificaLogin
	 * Verifica a senha do servidor
	 * 
	 * @param	string $matricula  matricula do servidor
	 * @param	string $senha  senha a ser verificada com a verdadeira
	 *
	 * return	0 -> Login Incorreto
         *              1 -> Login Correto
         *              2 -> Login Correto senha padrão
	 */
	
	
	public function verificaLogin($usuario,$senha){	
			
            # Pega a senha e o idUsuario					
            $idUsuario = $this->get_idUsuario($usuario);
            $senhaServidor = $this->get_senha($idUsuario);
            
            # Verifica se a senha é nula
            if(is_null($senhaServidor)){
                    return 0;
            }

            # Verifica se a senha é vazia
            if($senhaServidor == ""){
                    return 0;
            }

            # Pega a senha digitada e cripitografa
            $senha_md5 = md5($senha);

            if($senhaServidor <> $senha){
                return 0;
            }

            if($senhaServidor == $senha){
                if ($senha == SENHA_PADRAO){
                    set_session('idUsuario',$idUsuario);	
                    return 2;
                }else{
                    set_session('idUsuario',$idUsuario);
                    return 1;			
                }
            }
	}
	
	###########################################################
  
	/**
	 * M�todo get_senha
	 * Informa a senha (criptografada) 
	 * 
	 * @param string $usuario	matricula do servidor
	 */
	public function get_senha($idUsuario){ 

            $select = "SELECT senha		  
                         FROM tbusuario
                        WHERE idUsuario = '".$idUsuario."'";
            
            # verifica se o id foi fornecido
            if(is_null($idUsuario))
                return 0;
            else
            {
                $result = parent::select($select,false);
                return $result[0]; 
            }
        }
	###########################################################
  
	/**
	 * M�todo get_senha
	 * Informa a senha (criptografada) 
	 * 
	 * @param string $usuario	matricula do servidor
	 */
	public function get_idUsuario($usuario){ 

            $select = "SELECT idUsuario		  
                         FROM tbusuario
                        WHERE usuario = '".$usuario."'";
            
            # verifica se a matricula foi informada
            if(is_null($usuario))
                return 0;
            else
            {
                $result = parent::select($select,false);
                return $result[0]; 
            }
        }
	
	###########################################################
  
	/**
	 * M�todo get_senha
	 * Informa a senha (criptografada) 
	 * 
	 * @param string $usuario	matricula do servidor
	 */
	public function get_usuario($idUsuario){ 

            $select = "SELECT usuario		  
                         FROM tbusuario
                        WHERE idUsuario = '".$idUsuario."'";
            
            # verifica se a matricula foi informada
            if(is_null($idUsuario))
                return 0;
            else
            {
                $result = parent::select($select,false);
                return $result[0]; 
            }
        }
	
	###########################################################

    /**
    * Método verificaPermissao
    * 
    * verifica se um usuário pode executar uma determinada tarefa (regra)
    * retorna true ou false 
    * 
    * @param	integer $matricula	-> a matrícula
    * @param	integer	$idRegra	-> o id da regra
    */

    public function verificaPermissao($idUsuario,$idRegra)
    {
        $select = 'SELECT idPermissao
                     FROM tbpermissao
                    WHERE idUsuario = '.$idUsuario.' AND idRegra = '.$idRegra;
        
        # verifica se o id foi preenchido
        if(is_null($idUsuario)){
            return false;
        }else{
            $numReg = parent::count($select);
            
            # verifica se tem permissão para esse usuário e essa regra 
            if($numReg > 0){
                return true; 
            }else{
                # se não tiver para essa regra pode ter para admin
                if($idRegra == 1){
                    return false;
                }else{
                    # verifica se tem para admin
                    if($this->verificaPermissao($idUsuario,1)){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        }
    }
    
    ###########################################################
        
}