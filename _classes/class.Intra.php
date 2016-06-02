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
    private $banco = "intra";               // nome do banco
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
    * Método get_tabelaPgto
    * 
    * Método que retorna um array contendo a tabela de pagamento
    * 
    */

    public function get_tabelaPgto()
    {

        $select = 'SELECT mes_referencia,		
                          data,
                          mes_pgto		          		  
                     FROM tbpgto
                          order by data';
        $result = parent::select($select);
        return $result;
    }

    ###########################################################

    /**
    * Método get_noticiasAtivas
    * 
    * Exibe as noticias ativas, ou seja as que não expiraram 
    */

    public function get_noticiasAtivas($noticia = null)
    {
        # Verifica se exibe os resumos ou a notícia full
        if (is_null($noticia))
        {
            # retorna um array com várias notícias
            $many = true;	
            $select = 'SELECT dtInicial,
                                dtFim,
                                titulo,
                                autor,
                                resumo,
                                noticia,
                                foto,
                                largura_Foto,
                                altura_Foto,
                                idNoticias
                           FROM tbnoticias
                          WHERE current_date() BETWEEN cast(dtInicial as date) AND cast(dtFim as date)                                 
                       ORDER BY dtInicial desc';
        }
        else 
        {
            # retorna somente a notícia solicitada
            $many = false;
            $select = 'SELECT dtInicial,
                                dtFim,
                                titulo,
                                autor,
                                resumo,
                                noticia,
                                foto,
                                largura_Foto,
                                altura_Foto,
                                idNoticias
                           FROM tbnoticias
                          WHERE idNoticias = '.$noticia.'
                       ORDER BY dtInicial desc';
        }
        $result = parent::select($select,$many);
        return $result;
    }

    ###########################################################

    /**
    * Método get_numeroNoticiasAtivas
    * 
    * Exibe o número de noticias ativas, ou seja as que não expiraram 
    */

    public function get_numeroNoticiasAtivas()
    {
        $select = 'SELECT dtInicial,
                            dtFim,
                            titulo,
                            autor,
                            resumo,
                            noticia,
                            foto,
                            largura_Foto,
                            altura_Foto,
                            idNoticias
                       FROM tbnoticias
                      WHERE dtFim > current_date()
                   ORDER BY dtInicial desc';
        $numero = parent::count($select);
        return $numero;
    }

    ###########################################################

    /**
    * M�todo get_variavel
    * 
    * M�todo que exibe o conte�do de uma vari�vel de configura��o
    * 
    * @param	string	$var	-> o nome da vari�vel
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
    * M�todo set_variavel
    * 
    * M�todo que grava um conte�do em uma vari�vel de configura��o
    * 
    * @param	string	$var	-> o nome da vari�vel
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
     * M�todo registraLog
     * 
     * M�todo que registra na tabela tblog um evento
     * 
     * @param 	$matricula 		string		a matr�cula do usu�rio logado 
     * @param 	$data			datetime	a data e a hora do evento
     * @param	$atividade		string		um texto exibindo a a��o Inserir/Editar/Excluir/Login
     * @param	$tabela			string		a tabela que sofreu o evento
     * @param	$idValor		string		o id quando do registro
     * @param	$tipo			integer		o tipo de atividade
	 * @param	$ip				string		o ip da m�quina que fez a atividade
	 * @param	$browser		string		o browser usado pelo usu�rio
	 * @param	$idAuxiliar		integer		guarda o id de uma tabela auxiliar (quando necess�rio) Ex. usado para guardar o id do processo quando log de movimento do processo	 
    */

    public function registraLog($matricula,$data,$atividade,$tabela=null,$idValor=null,$tipo=0,$ip=null,$browser=null,$idAuxiliar=null)
    {
        # Verifica o tipo do log
        switch ($tabela)
        {
            # Acesso da GTI
             case "tbcomputador" :
             case "tbservico" :
             case "tbmovimento" :
             case "tbgrupo" :
                $tipo = 4;
                break;

            # Acesso do Protocolo
            case "tbprocesso" :
            case "tbprocessomovimento" :
            case "tbprocessoMovimento" :
                $tipo = 5;
                break;

            # Acesso Noticias 
            case "tbNoticias" :
                $tipo = 6;
                break;
        }

        
        $campos = array('matricula','data','atividade','tabela','idValor','ip','browser','tipo','idAuxiliar');
        $valor = array($matricula,$data,$atividade,$tabela,$idValor,IP,BROWSER_NAME.' '.BROWSER_VERSION,$tipo,$idAuxiliar);
        parent::gravar($campos,$valor,null,'tblog','idlog',false);
    }

    ###########################################################

    /**
    * M�todo verificaPermissao
    * 
    * verifica se um usu�rio pode executar uma determinada tarefa (regra)
    * retorna true ou false 
    * 
    * @param	integer $matricula	-> a matr�cula
    * @param	integer	$idRegra	-> o id da regra
    */

    public function verificaPermissao($matricula,$idRegra)
    {
        $select = 'SELECT idPermissao
                     FROM tbpermissao
                    WHERE matricula = '.$matricula.' AND idRegra = '.$idRegra;

        if (($matricula == GOD) AND ($idRegra <> 8)) // Permite acesso a matricula GOD
            return true;                         // menos a regra 8 que ? restritiva
        elseif(is_null($matricula))
            return false;
        else
        {
            $result = parent::count($select);
            if($result > 0)
                return true;
            else 
                return false;	
        }
    }


    ###########################################################

    /**
    * M�todo get_movimentos
    * 
    * M�todo que exibe a quantidade de movimentos de um servi�o GTI
    * 
    * @param	integer	$idservico	-> o id do servi�o
    */

    public function get_movimentos($idservico)
    {
        $select = 'SELECT idmovimento
                    FROM tbmovimento
                   WHERE idServico = '.$idservico;
        $result = parent::count($select);
        return $result;
    }

    ###########################################################

    /**
    * M�todo get_movimento
    * 
    * M�todo que exibe os dados de um movimento
    * 
    * Usado na rotina de log
    * 
    * @param	integer	$idmovimento	-> o id do movimento
    */

    public function get_movimento($idmovimento)
    {
        $select = 'SELECT tecnico,
                          idServico,
                          data
                     FROM tbmovimento
                    WHERE idmovimento = '.$idmovimento;
        $result = parent::select($select,false);
        return $result;
    }

    ###########################################################

    /**
    * M�todo get_statusServico
    * 
    * M�todo que informa o status de um servi�o (pendente, aguardando ou terminado)
    * Esse m�todo � utilizado na grava��o da mensagem de um usu�rio
    * n�o tecnico na tabela de movimento. serve para gravar o status origonal
    * do servi�o, mantendo o mesmo inalterado quando da grava��o de uma mensagem
    *   
    * @param	integer	$idservico	-> o id do servi�o
    */

    public function get_statusServico($idservico)
    {
        $select = 'SELECT status
                     FROM tbservico
                    WHERE idServico = '.$idservico;
        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################

    /**
    * M�todo get_solicitanteServico
    * 
    * M�todo que informa a matr�cula do solicitante de um servi�o
    * 
    * Esse m�todo � utilizado para informar, na rotina que exibe os movimentos, se
    * � uma mensagem de um usu�rio ou a de um t�cnico
    *      *   
    * @param	integer	$idservico	-> o id do servi�o
    */

    public function get_solicitanteServico($idservico)
    {
        $select = 'SELECT matricula
                     FROM tbservico
                    WHERE idServico = '.$idservico;
        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################

    /**
    * M�todo get_ultimoMovimentos
    * 
    * M�todo que exibe a data do �ltimo movimentos de um servi�o GTI
    * 
    * @param	integer	$idservico	-> o id do servi�o
    */

    public function get_ultimoMovimento($idservico)
    {
        $select = 'SELECT data
                     FROM tbmovimento
                    WHERE idServico = '.$idservico.'
                 ORDER BY data desc';
        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################

    /**
    * M�todo get_ultimoOSAtendida
    * 
    * M�todo que exibe a OS que est� sendo atendida
    *  
    */

    public function get_ultimoOSAtendida()
    {
        $select = 'SELECT idServico
                     FROM tbservico
                    WHERE status = "pendente"
                    ORDER BY idServico';
        $result = parent::select($select,false);
        if(is_null($result[0]))
            return 'Todas as OS foram atendidas.';
        else
            return $result[0];
    }

    ###########################################################

    /**
    * M�todo get_permissao
    * 
    * M�todo que exibe o informa��es de uma permissao
    * Usado na rotina de log quando se exclui uma permiss�o
    * para fornecer o nome da regra e da matr�cula da permiss�o excluida
    * 
    * @param	integer	$idPermissao	-> o id da permissao
    */

    public function get_permissao($idPermissao)
    {
        $select = 'SELECT tbpermissao.matricula,
                          tbregra.nome
                     FROM tbpermissao LEFT JOIN tbregra ON (tbregra.idregra = tbpermissao.idregra) 
                    WHERE idPermissao = '.$idPermissao;
        $row = parent::select($select,false);
        return $row;
    }

    ###########################################################

    /**
    * M�todo get_servicosPendentes
    * 
    * M�todo que exibe a quantidade de servi�os pendentes
    * 
    * @param	integer	$numMes	-> o n�mero do m�s (opcional)
    * 							   se omitido ele exibe o total
    * 
    */

    public function get_servicosPendentes($numMes=null)
    {
        $select = 'SELECT idservico
                     FROM tbservico
                    WHERE status = "pendente"';
        
        if (!is_null($numMes))
            $select .= ' AND month(data)='.$numMes;
        
        $result = parent::count($select);
        return $result;
    }

    ###########################################################

    /**
    * M�todo get_servicosAguardando
    * 
    * M�todo que exibe a quantidade de servi�os Aguardando
    * 
    * @param	integer	$numMes	-> o n�mero do m�s (opcional)
    * 							   se omitido ele exibe o total
    * 
    */

    public function get_servicosAguardando($numMes=null)
    {
        $select = 'SELECT idservico
                     FROM tbservico
                    WHERE status = "aguardando"';

        if (!is_null($numMes))
            $select .= ' AND month(data)='.$numMes;

        $result = parent::count($select);
        return $result;
    }

    ###########################################################

    /**
        * M�todo get_servicosTerminados
        * 
        * M�todo que exibe a quantidade de servi�os Terminados
        * 
        * @param	integer	$numMes	-> o n�mero do m�s (opcional)
        * 							   se omitido ele exibe o total
        * 
        */

    public function get_servicosTerminados($numMes=null)
    {
            $select = 'SELECT idservico
                                    FROM tbservico
                                WHERE status = "terminado"';

            if (!is_null($numMes))
                    $select .= ' AND month(data)='.$numMes;

            $result = parent::count($select);

            return $result;
    }

    ###########################################################


    /**
        * M�todo get_imagemNoticia
        * 
        * M�todo que fornece a figura de uma noticia
        * 
        * @param	integer	$idNoticia	-> o id da not�cia
        */

    public function get_imagemNoticia($idNoticia)
    {
            $select = 'SELECT foto
                            FROM tbnoticias
                        WHERE idNoticias = '.$idNoticia;

            $valor = parent::select($select,false);

            if(is_null($valor[0]))
                $valor[0] = '_semImagem.jpg';

            return $valor[0];
    }

    ###########################################################


    /**
        * M�todo get_encarregado
        * 
        * M�todo que fornece o encarregado de um servi�o
        * 
        * @param	integer	$idServico	-> o id do servi�o
        */

    public function get_encarregado($idServico)
    {
            $select = 'SELECT encarregado
                                    FROM tbservico
                                WHERE idServico = '.$idServico;

            $valor = parent::select($select,false);

            return $valor[0];
    }

    ###########################################################

    /**
        * M�todo get_patrimonio
        * 
        * M�todo que informa o patrimonio de computador
        * @param	integer	$id	-> o id do computador
        */

    public function get_patrimonio($id)
    {
        $select = 'SELECT patrimonio
                        FROM tbcomputador
                    WHERE idComputador = '.$id;

        $result = parent::select($select,false);

        return $result[0];
    }

    ###########################################################


    /**
    * M�todo get_patrimonioServico
    * 
    * M�todo que informa o patrimonio do computador de um servi�o
    * 
    * @param	integer	$idServico	-> o id do servi�o
    */

    public function get_patrimonioServico($idServico)
    {
        $select = 'SELECT patrimonio
                     FROM tbservico
                    WHERE idServico = '.$idServico;
        $valor = parent::select($select,false);
        return $valor[0];
    }

    ###########################################################


    /**
    * M�todo get_equipamentoServi�o
    * 
    * M�todo que informa o tipo de equipamento de um servi�o
    * 
    * @param	integer	$idServico	-> o id do servi�o
    */

    public function get_equipamentoServico($idServico)
    {
        $select = 'SELECT equipamento
                     FROM tbservico
                    WHERE idServico = '.$idServico;
        $valor = parent::select($select,false);
        return $valor[0];
    }

    ###########################################################


    /**
        * M�todo get_numeroProcesso
        * 
        * M�todo que informa o n�mero de um processo pelo id
        * 
        * @param	integer	$id	-> o id do processo
        */

    public function get_numeroProcesso($id)
    {
            $select = 'SELECT numero
                            FROM tbprocesso
                        WHERE idProcesso = '.$id;

            $valor = parent::select($select,false);

            return $valor[0];
    }

    ###########################################################


    /**
        * M�todo get_ultimaCarga
        * 
        * M�todo que informa o setor da �ltima carga de um processo pelo id
        * 
        * @param	integer	$id	-> o id do processo
        */

    public function get_ultimaCarga($id)
    {
            $select = 'SELECT destino
                            FROM tbprocessoMovimento
                        WHERE processo = '.$id.'
                        ORDER BY idProcessoMovimento DESC LIMIT 1';

            $valor = parent::select($select,false);

            return $valor[0];
    }

    ###########################################################


    /**
        * M�todo get_idProcesso
        * 
        * M�todo que informa o id pelo n�mero de um processo
        * 
        * @param	string	$numero	-> o n�mero do processo no formato E-xx/xxxxxx/xxxx
        */

    public function get_idProcesso($numero)
    {
            $select = 'SELECT idProcesso
                            FROM tbprocesso
                        WHERE numero = "'.$numero.'"';

            $valor = parent::select($select,false);

            return $valor[0];
    }

    ###########################################################


    /**
        * M�todo get_tramitacoes
        * 
        * M�todo que informa o n�mero de tramita��es que um processo tem
        * 
        * @param	integer	$id	-> o id do processo
        */

    public function get_tramitacoes($id)
    {
            $select = 'SELECT idprocessoMovimento
                            FROM tbprocessoMovimento
                        WHERE processo = '.$id;

            $result = parent::count($select);

            return $result;
    }

    ###########################################################


    /**
        * M�todo get_projeto
        * 
        * M�todo que informa o Nome e a descri��o de um projeto
        * 
        * @param	integer	$id	-> o id do projeto
        */

    public function get_projeto($id)
    {
            $select = 'SELECT projeto, descricao
                            FROM tbprojeto
                        WHERE idProjeto = '.$id;

            $result = parent::select($select);

            return $result;
    }

    ###########################################################

    public function get_tarefaStatus($idTarefa)

    /**
        * M�todo que informa o Status de uma tarefa (feito ou pendente)
        * 
        * @param	integer	$id	-> o id da tarefa
        */

    {
        $select = 'SELECT status
                        FROM tbtarefa
                    WHERE idTarefa = '.$idTarefa;

        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################

    public function get_tarefaNumOrdem($idTarefa)

    /**
        * M�todo que informa o numOrdem de uma tarefa
        * 
        * @param	integer	$idTarefa	-> o id da tarefa
        */

    {
        $select = 'SELECT numOrdem
                        FROM tbtarefa
                    WHERE idTarefa = '.$idTarefa;

        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################

    public function get_tarefaNumOrdemTop($idProjeto, $prioridade = null)

    /**
        * M�todo que informa o numOrdem Top de um Projeto
        * 
        * @param   integer $idProjeto	-> o id do Projeto
        * @param   integer $prioridade -> a prioridade
        */

    {
        $select = 'SELECT numOrdem
                        FROM tbtarefa
                    WHERE idProjeto = '.$idProjeto;

        if(!is_null($prioridade))
            $select.=' AND prioridade = '.$prioridade;

        $select .= ' ORDER BY numOrdem asc';

        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################

    public function get_tarefaNumOrdemBottom($idProjeto, $prioridade=null)

    /**
        * M�todo que informa o numOrdem Bottom de um Projeto
        * 
        * @param   integer $idProjeto	-> o id do Projeto
        * @param   integer $prioridade -> a prioridade
        */

    {
        $select = 'SELECT numOrdem
                        FROM tbtarefa
                    WHERE idProjeto = '.$idProjeto;

        if(!is_null($prioridade))
            $select.=' AND prioridade = '.$prioridade;

        $select .= ' ORDER BY numOrdem desc';

        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################

    public function get_idProjeto($idTarefa)

    /**
        * M�todo que informa o idProjeto de uma tarefa
        * 
        * @param	integer	$idTarefa	-> o id da tarefa
        */

    {
        $select = 'SELECT idProjeto
                        FROM tbtarefa
                    WHERE idTarefa = '.$idTarefa;

        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################	

    public function get_tarefaPrioridade($idTarefa)

    /**
        * M�todo que informa a prioridade de uma tarefa (baixa, normal, alta ou terminado)
        * 
        * @param	integer	$id	-> o id da tarefa
        */

    {
        $select = 'SELECT prioridade
                        FROM tbtarefa
                    WHERE idTarefa = '.$idTarefa;

        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################	

    public function get_tarefaAcima($idTarefa,$prioridade)

    /**
        * M�todo que retorna um array o id e o numOrdem da tarefa imediatamente acima da mesma prioridade
        * 
        * @param	integer	$id	-> o id da tarefa
        */

    {
        $select = 'SELECT idTarefa,numOrdem
                        FROM tbtarefa
                    WHERE prioridade = '.$prioridade.'
                        ORDER BY numOrdem desc';

        $result = parent::select($select);

        $marcador = false; // marca quando se encontra o id igual ao par�metro
        $registro = null;  // registra o registro acima do id do par�metro
        foreach ($result as $tarefa)
        {                
            if ($marcador) // verifica se foi marcado no loop anterior
            {
                $marcador = false;  // se for desmarca
                $registro = $tarefa;// passa o registro atual como o proxiomo
            }

            if($tarefa[0] == $idTarefa)
                $marcador = true; // marca quando o registro � igual ao do par�metro

        }
        return $registro;
    }

    ###########################################################	

    public function get_tarefaAbaixo($idTarefa,$prioridade)

    /**
        * M�todo que retorna um array o id e o numOrdem da tarefa imediatamente abaixo da mesma prioridade
        * 
        * @param	integer	$id	-> o id da tarefa
        */

    {
        $select = 'SELECT idTarefa,numOrdem
                        FROM tbtarefa
                    WHERE prioridade = '.$prioridade.'
                        ORDER BY numOrdem asc';

        $result = parent::select($select);

        $marcador = false; // marca quando se encontra o id igual ao par�metro
        $registro = null;  // registra o registro acima do id do par�metro
        foreach ($result as $tarefa)
        {                
            if ($marcador) // verifica se foi marcado no loop anterior
            {
                $marcador = false;  // se for desmarca
                $registro = $tarefa;// passa o registro atual como o proxiomo
            }

            if($tarefa[0] == $idTarefa)
                $marcador = true; // marca quando o registro � igual ao do par�metro

        }
        return $registro;
    }

    ###########################################################

    public function get_numTarefas($idProjeto)

    /**	 
        * M�todo que informa o n�mero de tarefas de um processo
        * 
        * @param	integer	$id	-> o id do processo
        */


    {
        # Pega o n�mero de tarefas
        $select = 'SELECT idtarefa
                     FROM tbtarefa
                    WHERE idProjeto = '.$idProjeto;

        $NumTarefa = parent::count($select);
        
        # Pega o n�mero de tarefas Pendentes
        $select = 'SELECT idtarefa
                     FROM tbtarefa
                    WHERE status = 0
                      AND idProjeto = '.$idProjeto;

        $NumTarefaPendentes = parent::count($select);
        
        $result = '('.$NumTarefa.'/'.$NumTarefaPendentes.')';

        return $result;
    }

    ###########################################################

    public function get_projetoTipo($idProjeto)

    /**
        * M�todo que informa o tipo do projeto
        * 
        * @param	integer	$idProjeto	-> o id do projeto
        */

    {
        $select = 'SELECT tipo
                        FROM tbprojeto
                    WHERE idProjeto = '.$idProjeto;

        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################

    function get_idMemo($idProjeto)

    /**
    *
    * informa o id da tabela tdmemo
    * 
    * @param   integer  $idProjeto  o id do Projeto
    */


    {
            $select = 'SELECT idMemo
                            FROM tbmemo
                        WHERE idProjeto = '.$idProjeto;

            $row = parent::select($select,false);
            $count = parent::count($select);

            if($count == 0)
                return null;
            else
                return $row[0];
    }

    ###########################################################

    public function get_memoVazio($idProjeto)

    /**	 
    * M�todo que informa se o memo do projeto est� vazio
    * 
    * @param	integer	$id	-> o id do processo
    */


    {
            $select = 'SELECT IF(length(memo)>0,"Texto","Vazio")
                            FROM tbmemo
                        WHERE idProjeto = '.$idProjeto;

            $result = parent::select($select, false);

            return $result[0];
    }

    ###########################################################

    public function get_avisosAtivos($matricula)

    /**
    * Exibe os avisos ativos dessa matr�cula  
    */

    {
        # Monta o select
        $select = 'SELECT aviso,
                          matricula,
                          autor
                    FROM tbaviso
                WHERE matricula = '.$matricula.'
                    AND ativo = 1';

        $result = parent::select($select);
        return $result;
    }

    ###########################################################

    public function get_numeroAvisosAtivos($matricula)

    /**
    * Exibe os avisos ativos dessa matr�cula  
    */

    {
        # Monta o select
        $select = 'SELECT aviso,
                          matricula,
                          autor
                    FROM tbaviso
                WHERE matricula = '.$matricula.'
                    AND ativo = 1';

        $result = parent::count($select);
        return $result;
    }

    ###########################################################

    public function get_avisosId($id)

    /**
    * Exibe os avisos por id
    */
            
    {
        # Monta o select
        $select = 'SELECT aviso,
                          matricula,
                          autor
                     FROM tbaviso
                    WHERE idAviso = '.$id;

    $result = parent::select($select);
    return $result;
    }

    ###########################################################
	
    function get_numeroServidoresPermissao($idRegra)

    /**
    * informa o n�mero de servidores com permiss�o a uma regra
    * 
    * @param string $idRegra id da regra
    */
    
    {
        $select = 'SELECT matricula
                     FROM tbpermissao
                    WHERE idRegra = '.$idRegra;		

        $count = parent::count($select);
        return $count;
    }

###########################################################
	
    function get_nomeRegra($idRegra)

    /**
    * informa o nome da regra
    * 
    * @param string $idRegra id da regra
    */
    
    {
        $select = 'SELECT nome
                     FROM tbregra
                    WHERE idRegra = '.$idRegra;		

        $result = parent::select($select,false);
        return $result[0];
    }

    ###########################################################

    /**
     * M�todo get_dadosUltimoMovimentos
     * 
     * M�todo que exibe os dados do �ltimo movimento para exibi��o expecial
     * usado na rotina de servi�os pendentes para exibi��o dos
     * dados do �ltimo movimento sem clicar no editar
     * 
     * @param	integer	$idservico	-> o id do servi�o
     */

    public function get_dadosUltimoMovimentos($idservico)
    {
            $select = 'SELECT data,descricao,pendencia
                         FROM tbmovimento
                        WHERE idservico = '.$idservico.'
                        ORDER BY data desc
                        LIMIT 1';

            $result = parent::select($select,false);


            return $result;
    }

    ###########################################################


    /**
     * M?todo get_processoData
     * 
     * M�todo que informa a data de abertura de um processo pelo id
     * 
     * @param	integer	$id	-> o id do processo
     */

    public function get_processoData($id)
    {
            $select = 'SELECT date_format(data,"%d/%m/%Y")
                         FROM tbprocesso
                        WHERE idProcesso = '.$id;
            
            $valor = parent::select($select,false);
            
            return $valor[0];
    }

    ##########################################################################################

    public function get_codigoAssuntoPorId($id)
    
     /**
      * 
      * Retorna codigo do assunto UPO pelo id
      * 
      * @param $id integer o id do assunto
      * 
      */
            
    {
        # Monta o select
        $select = "SELECT codigo
                     FROM tbupo
                    WHERE idupo = '$id'";
        
        $row = parent::select($select,false);
        return $row[0];
    }
    
    ##########################################################################################

    public function get_nomeArquivoDoerj($id)
    
     /**
      * 
      * Retorna o nome do arquivo de um id do cadastro de doerj
      * 
      * @param $id integer o id do doerj
      * 
      */
            
    {
        # Monta o select
        $select = "SELECT arquivo
                     FROM tbdoerj
                    WHERE iddoerj = '$id'";
        
        $row = parent::select($select,false);
        return $row[0];
    }
    
    ##########################################################################################

    public function get_numeroDocumentoTipo($tipo)
    
     /**
      * 
      * Retorna o n�mero de documentos por tipo
      * 
      * @param $tipo o id do tipo do documento
      * 
      */
            
    {
        # Monta o select
        $select = "SELECT iddoerj
                     FROM tbdoerj
                    WHERE tipoDocumento = '$tipo'";
        
        $count = parent::count($select);
        return $count;
    }
	
	###########################################################


    /**
     * M�todo get_tramitacoesInternas
     * 
     * M�todo que informa o n�mero de tramita��es Internas (em uma ger�ncia) 
	 * no cadastro de movimentos Internosque um processo tem
     * 
     * @param	integer	$id			-> o id do processo
	 * @param	text	$lotacao	-> o id da lotacao 
     */

    public function get_tramitacoesInternas($id, $lotacao)
    {
            $select = 'SELECT idmovimentoInterno
                            FROM tbmovimentoInterno
                        WHERE processo = '.$id.'
                          AND setor = '.$lotacao;

            $result = parent::count($select);

            return $result;
    }

    ###########################################################


    /**
     * M�todo get_ultimaMovimentacaoInterna
     * 
     * M�todo que informa o �ltimo locar de movimenta��o interna de um processo pelo id
     * 
     * @param	integer	$id			-> o id do processo
	 * @param	ineger	$lotacao	-> o id da lotacao para exibir a �ltima movimenta��o dessa lota��o
     */

    public function get_ultimaMovimentacaoInterna($id, $lotacao)
    {
            $select = 'SELECT destino
                         FROM tbmovimentointerno
                        WHERE processo = '.$id.'
                          AND setor = '.$lotacao.'
                        ORDER BY idmovimentoInterno DESC LIMIT 1';

            $valor = parent::select($select,false);

            return $valor[0];
    }

    ###########################################################
}
?>