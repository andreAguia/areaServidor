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
    private $banco = "areaServidor";        // nome do banco
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
        
        if(is_null($tabela))
                $tabela = $this->tabela;

            if(is_null($idCampo))
                $idCampo = $this->idCampo;

            parent::gravar($campos,$valor,$idValor,$tabela,$idCampo,$alerta);
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
        $count = parent::count($select);
        
        if($count>0){
            return $valor[0];
        }else{
            alert('Variável não existemte');
            return NULL;
        }
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
            
            # Verifica se o usuário existe
            if(is_null($idUsuario)){
                return 0;
            }
            
            # Verifica se a senha é nula. Seja pq nâo foi digitada ou pq o usuário está desabilitado.
            if(is_null($senhaServidor)){
                return 1;
            }

            # Verifica se a senha é vazia
            if($senhaServidor == ""){
                return 1;
            }

            # Pega a senha digitada e cripitografa
            $senha_md5 = md5($senha);
            
            if($senhaServidor <> $senha_md5){
                return 2;
            }
            
            # Verifica se o computador está autorizado para acesso
            $ip = getenv("REMOTE_ADDR");                    // Pega o ip do usuário
            $controle = $this->get_variavel("ipAcesso");    // Verifica se o controle por ip está habilitado
            
            if($controle){
                $verificaIp = $this->verificaComputador($ip);  // Verifica se esse computador está cadastrado
            
                if(!$verificaIp){
                    return 5;           // Retorna o valor 5 quando o computador não estiver cadastrado
                }
            }

            if($senhaServidor == $senha_md5){
                if ($senha == SENHA_PADRAO){
                    set_session('idUsuario',$idUsuario);	
                    return 4;
                }else{
                    set_session('idUsuario',$idUsuario);
                    return 3;			
                }
            }
	}
	
	###########################################################
	
	/**
	 * M�todo get_senha
	 * Informa a senha (criptografada) 
	 * 
	 * @param	string $usuario	O usuario
	 */
	public function get_senha($idUsuario)
        { 

            $select = "SELECT senha		  
                         FROM tbusuario
                        WHERE idUsuario = ".$idUsuario;

            # verifica se a idServidor foi informada
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
	 * M�todo set_senha
	 * muda a senha de um usu�rio
	 * 
	 * @param	string 	$idUsuario 	-> o usuario
	 * @param 	string	$senha		-> senha (n�o criptofrafada) a ser gravada (se nulo grava-se a senha padr�o)
	 */
	public function set_senha($idUsuario,$senha = NULL,$alert = true)
	{
            # Verifica se a senha foi informada
            if (is_null($senha)){
                # Define a senha com senha padrão
                $senha = SENHA_PADRAO;
                
                # Grava o acesso para o controle de dias com senha padrão
                parent::gravar('ultimoAcesso',date("Y-m-d H:i:s"),$idUsuario,'tbusuario','idUsuario',false);
            }			
            #criptografa a senha
            $senha = md5($senha);
            
            # Grava a senha
            parent::gravar('senha',$senha,$idUsuario,'tbusuario','idUsuario',$alert);
	}
	
        ###########################################################
	
	/**
	 * M�todo set_senha
	 * muda a senha de um usu�rio
	 * 
	 * @param	string 	$idUsuario 	-> o usuario
	 * @param 	string	$senha		-> senha (n�o criptofrafada) a ser gravada (se nulo grava-se a senha padr�o)
	 */
	public function set_senhaNull($idUsuario,$alert = true)
	{
            # Grava a senha
            parent::gravar('senha',NULL,$idUsuario,'tbusuario','idUsuario',$alert);
	}
	
        ###########################################################
        
	/**
     * Método get_tipoSenha
     * Informa o tipo da senha (padrão/bloqueada/Ok) 
     * 
     * @param	string $idUsuario	o usuario
     */

    function get_tipoSenha($idUsuario)
    { 

        $select = "SELECT senha		  
                         FROM tbusuario
                        WHERE idUsuario = ".$idUsuario;
        
        $result = parent::select($select,false);
        $padrao = MD5(SENHA_PADRAO);
        
        switch ($result[0])
        {
            # senha padrão
            case $padrao :
                return 1;
                break;
            
            # senha bloqueada
            case NULL :
                return 2;
                break;

            # senha ok
            default:
                return 3;
                break;
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
            
            # verifica se foi informado o usuário
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
	public function get_idServidor($idUsuario){ 

            $select = "SELECT idServidor		  
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
	 * M�todo get_usuario
	 * Informa a senha (criptografada) 
	 * 
	 * @param string $usuario	matricula do servidor
	 */
	public function get_usuario($idUsuario){ 

            $select = "SELECT usuario		  
                         FROM tbusuario
                        WHERE idUsuario = '".$idUsuario."'";
            
            $result = parent::select($select,false);
            return $result[0]; 
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
            
            #echo $idUsuario."-".$idRegra."-".$numReg."-".CHAMADOR;
            
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
	
    function get_numeroUsuariosPermissao($idRegra)

    /**
    * informa o número de Usuarios com permissão a uma regra
    * 
    * @param string $idRegra id da regra
    */
    
    {
        $select = 'SELECT idUsuario
                     FROM tbpermissao
                    WHERE idRegra = '.$idRegra;		

        $count = parent::count($select);
        return $count;
    }

###########################################################
	
    function get_numeroPermissaoUsuarios($idUsuario)

    /**
    * informa o número de Permissões de um idUsuario
    * 
    * @param string $idUsuario id da regra
    */
    
    {
        $select = 'SELECT idUsuario
                     FROM tbpermissao
                    WHERE idUsuario = '.$idUsuario;		

        $count = parent::count($select);
        return $count;
    }

###########################################################


    /**
     * Método registraLog
     * 
     * Método que registra na tabela tblog um evento
     * 
     * @param 	$idUsuario 		string		o usuário logado 
     * @param 	$data			datetime	a data e a hora do evento
     * @param	$atividade		string		um texto exibindo a ação Inserir/Editar/Excluir/Login
     * @param	$tabela			string		a tabela que sofreu o evento
     * @param	$idValor		string		o id quando do registro
     * @param	$idServidor		integer		o idServdor 
     * @param	$tipo			integer		o tipo de atividade
     * @param	$ip			string		o ip da máquina que fez a atividade
     * @param	$browser		string		o browser usado pelo usuário
	 
    */

    public function registraLog($idUsuario,$data,$atividade,$tabela = null,$idValor = null,$tipo = 0,$idServidor = null,$ip = null,$browser = null) {        
        $campos = array('idUsuario','data','atividade','tabela','idValor','tipo','idServidor','ip','browser');
        $valor = array($idUsuario,$data,$atividade,$tabela,$idValor,$tipo,$idServidor,IP,BROWSER_NAME.' '.BROWSER_VERSION);
        parent::gravar($campos,$valor,null,'tblog','idlog',false);
    }

    ###########################################################
	
    /**
     * Método get_ultimoAcesso
     * informa a data do último acesso a área do servidor de uma matrícula
     * 
     * @param	string idUsuario id do usuário
     */

    public function get_ultimoAcesso($idUsuario){
        $select = 'SELECT date(ultimoAcesso)
                     FROM tbusuario
                    WHERE idUsuario = '.$idUsuario;

        # verifica se a $idUsuario foi informada
        if(is_null($idUsuario)){
            $data[0] = "1900-01-01";
        }else{
            $data = parent::select($select,false);
        }
        return $data[0];
    }
	
    ###########################################################

    /**
    * Método get_permissao
    * 
    * Retorna o idUsuario e a regra de uma permissão (usada no log de inclusão e exclusão de permissões)
    * 
    * @param	integer	$idPermissao	-> o id da permissao
    */

    public function get_permissao($idPermissao)
    {
        $select = 'SELECT tbregra.nome
                     FROM tbpermissao LEFT JOIN tbregra ON (tbregra.idregra = tbpermissao.idregra) 
                    WHERE idPermissao = '.$idPermissao;
        $row = parent::select($select,false);
        return $row[0];
    }

    ###########################################################
  
	
	public function get_nomeUsuario($idUsuario){ 
            
            $select = 'SELECT grh.tbpessoa.nome
                         FROM tbusuario LEFT JOIN grh.tbservidor USING (idServidor)
                                        LEFT JOIN grh.tbpessoa USING (idPessoa)
                         WHERE idusuario = '.$idUsuario;
            
            # verifica se o id foi informado
            if(is_null($idUsuario))
                return 0;
            else
            {
                $result = parent::select($select,false);
                return $result[0]; 
            }
        }
			
	###########################################################
	
    function verificaComputador($ip)

    /**
    * Verifica se o computador com esse ip tem acesso so sistema
    * 
    * @param string $ip ip do computador
    */
    
    {
        $select = 'SELECT idComputador
                     FROM tbcomputador
                    WHERE ip = "'.$ip.'"';		

        $result = parent::select($select,false);
        if(is_null($result[0])){
            return FALSE;
        }else{
            return TRUE; 
        }
    }
    
    ###########################################################
	
    function escolheMensagem()

    /**
    * Escolhe aleatoriamnte uma mensagem da tabela de mensagens
    * 
    * 
    */
    
    {
        $select = 'SELECT mensagem
                     FROM tbmensagem
                    ORDER BY RAND() LIMIT 1';
        
        $result = parent::select($select,false);
        return $result[0];
    }

###########################################################
}