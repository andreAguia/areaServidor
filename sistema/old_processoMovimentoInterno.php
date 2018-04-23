<?php
/**
 * Cadastro Interno de Processos
 * Sistema de Controle dos processos internamente nos setores
 * 
 * Solicitado, a princípio, pela GRH para substituir o sistema em access
 * 
 * Esse sistema vem a ser um complemento do sistema do protocolo 
 *  
 * By Alat
 */

# Inicia as variáveis que receberão as sessions
$matricula = null;

# Configuração
include ("../config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($matricula);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');	
	
    # pega o id (se tiver)
    $id = soNumeros(get('id'));

    # Verifica a paginacao
    $paginacao = get('paginacao',get_session('sessionPaginacao',0));	# Verifica se a paginação vem por get, senão pega a session
    set_session('sessionPaginacao',$paginacao);

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro')))									# Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    else
    { 
        $parametro = post('parametro');					# Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro',$parametro);		# transfere para a session para poder recuperá-lo depois
        $paginacao = 0;

        # Log da pesquisa
        if($parametro <> '')
        {
            $data = date("Y-m-d H:i:s");
            $atividade = 'Pesquisou Internamente o processo: '.$parametro;
            $intra->registraLog($matricula,$data,$atividade,'tbmovimentointerno',null);
        }    
    }

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');   

    # Começa uma nova página
    $page = new Page();        
    $page->iniciaPagina();
	
    # Inicia a classe de interface da área do servidor
    $area = new AreaServidor();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Exibe o link da Versão
    #Intranet::versaoSistema(300,200,'right');

    # Abre um novo objeto Modelo
    $objeto = new Modelo();	

    ################################################################

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Consulta de Processos');
    $objeto->set_id('Processo');

    # Botões
    $objeto->set_voltarLista('areaServidor.php');
    $objeto->set_botaoIncluir(false);

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);
    $objeto->set_exibeLista(false);

    # ordenação
    if(is_null($orderCampo))
        $orderCampo = 1;

    if(is_null($orderTipo))
        $orderTipo = 'desc';

    # trata o parâmetro para busca avançada
    $palavras = explode(" ", $parametro);  // separa as palavras e as coloca em um array
    $numPalavras = count($palavras);

    ## Motor de Busca ##

    # Começa o select
    $select = 'SELECT  numero,
                       data,
                       assunto,
                       idProcesso                           
                  FROM tbprocesso
                  WHERE numero LIKE "%'.$parametro.'%"
                     OR DATE_FORMAT(data,"%d/%m/%Y") LIKE "%'.$parametro.'%"';


    # Entra com as palavras para busca por assunto
    $contador = 1;
    if($numPalavras == 1)
        $select .= ' OR assunto LIKE "%'.$parametro.'%"';
    else 
    {
        $select.='OR (';

         foreach ($palavras as $termos)
         {
             $select.='(assunto LIKE "%'.$termos.'%")';
             if($contador  < $numPalavras)
             {
                 $select.= 'AND';
                 $contador++;
             }
         }

        $select.=')';
    }

    # Finaliza o select com a ordenação
    $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo;
    #echo $select;

    # select da lista
    $objeto->set_selectLista ($select);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=exibeMovimentos');
    $objeto->set_linkListar('?fase=listar');

    # Parametros da tabela
    $objeto->set_label(array("Processo","Data","Assunto"));
    $objeto->set_width(array(20,10,60));
    $objeto->set_align(array("center","center","left"));
    $objeto->set_function(array ("","date_to_php"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbprocesso');

    # Nome do campo id
    $objeto->set_idCampo('idProcesso');

    # Nome do botão editar
    #$objeto->set_nomeColunaEditar('Ver');
    #$objeto->set_editarBotao('ver.png');

    # Matrícula para o Log
    $objeto->set_matricula($matricula);

    # Paginação
    $objeto->set_paginacao(true);
    $objeto->set_paginacaoInicial($paginacao);
    $objeto->set_paginacaoItens(13);

    ################################################################

    switch ($fase)
    {
        case "" :
        case "listar" :

            $objeto->listar();

            echo '<br />';
            echo '<br />';

            if($parametro == "")
            {
                $divMensagem = new Box('divDicaProcesso');
                $divMensagem->set_titulo('Como Fazer a Consulta');
                $divMensagem->abre();
                $p = new P('<br/>A consulta aos processos poderá ser efetuada de diversas formas:<br/><br/>
                            1. Pelo número do processo ou parte dele; <br/> 
                            2. Pelo assunto. Exemplo: diária, progressão, licença, etc;<br/>
                            3. Pelo nome do requerente ou parte dele;<br/>
                            4. Pelo ano de abertura.<br/><br/>

                            O sistema é novo e ainda não tem muitos processos cadastrados. 
                            É possível que a grande maioria das pesquisas ainda não encontrem nenhum registro,
                            mas a medida em que o protocolo for cadastrando os processos tramitados o banco de dados irá aumentar.');

                $p->show();
                $divMensagem->fecha();
            }
            break;   

        case "exibeMovimentos" :
            # Botão voltar
            Visual::botaoVoltar('?');			
			
			# Pega o id da lotação do servidor logado
			$lotacao = $servidor->get_idlotacao($matricula);
			
			# Incluir Movimento
			$divMovimentoInterno = new div('divMovimentoInterno');
			$divMovimentoInterno->abre();
			
			$menu = new MenuGrafico(2,'menuIncluiMovimentoInterno');
			$botao = new Button();
            $botao->set_label('Incluir Movimento'); 
            $botao->set_title('Incluir Movimento');
            $botao->set_onClick("abreDivId('divIncluirMovimentoInterno'); document.forms[1].elements[1].focus();");        
            #$botao->set_image(PASTA_FIGURAS.'botaoTramitar.png',90,50);                     
            $menu->add_item($botao);
			
			# Botão histórico somente para o GOD
            if ($matricula == GOD)
            {        
                $botao = new Button();
                $botao->set_label('Exibir Histórico'); 
                $botao->set_title('Acessa o histórico desse registro');
                $botao->set_onClick("abreFechaDivId('divHistoricoMovimentoInterno');");           
                #$botao->set_image(PASTA_FIGURAS.'botaoHistorico.png',90,50);                    
                $menu->add_item($botao);
            }      
            $menu->show();
			
			$divMovimentoInterno->fecha();
            
            echo '<br />';

            # Exibe os dados do Processo
            $fieldset = new Fieldset('Dados do Processo');
            $fieldset->abre();
                AreaServidor::listaProcesso($id);
            $fieldset->fecha();
			
			##################################################

            # Div do histórico (log)            
			$divHistoricoMovimentoInterno = new div('divHistoricoMovimentoInterno');
			$divHistoricoMovimentoInterno->abre();
			
            $fieldset = new Fieldset('Histórico dos Movimentos');
            $fieldset->abre();

            $select = 'SELECT tblog.data,
                              tblog.matricula,
                              tbpessoa.nome,
                              tblog.atividade,
                              tblog.idValor
                         FROM intra.tblog 
                    LEFT JOIN pessoal.tbfuncionario ON intra.tblog.matricula = pessoal.tbfuncionario.matricula
                    LEFT JOIN pessoal.tbpessoa ON pessoal.tbfuncionario.idpessoa = pessoal.tbpessoa.idpessoa 
                        WHERE tblog.tabela="tbmovimentointerno"
                          AND tblog.idauxiliar='.$id.' order by tblog.data desc';								

            # Conecta com o banco de dados
            $intra = new Intra();
            $result = $intra->select($select);
            $contadorHistorico = $intra->count($select); 

            # Parametros da tabela
            $label = array("Data","Matrícula","Nome","Atividade","id");
            $width = array(13,10,22,50,5);	
            $align = array("center","center","center","left");
            $funcao = array ("datetime_to_php","dv");								

            # Monta a tabela
            $tabela = new Tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label,$width,$align);
            $tabela->set_funcao($funcao);
            #$tabela->set_titulo($titulo);

            $tabela->show();		

            $fieldset->fecha();
           
			$divHistoricoMovimentoInterno->fecha();
			
            #####################################        

            # Exibe as tramitações desse processo           
            $numTramitacao = $intra->get_tramitacoesInternas($id, $lotacao);
            if ($numTramitacao == 0)
                $fieldset = new Fieldset('Nenhuma tramitação');
            elseif($numTramitacao == 1)
                $fieldset = new Fieldset('1 tramitação');
            else
                $fieldset = new Fieldset($numTramitacao.' tramitações');

            $fieldset->abre();
                AreaServidor::listaMovimentosInternos($id,$lotacao,'?fase=excluirMovimento&processo='.$id);
            $fieldset->fecha();
            
            #####################################

            # Rotina de Inclusao de movimento
            
            # Verifica se o processo já tem carga e pega a 'lotação atual'  do processo (id)
            $lotacaoAtual = $intra->get_ultimaMovimentacaoInterna($id,$lotacao);
            
            $Window = new Box('divIncluirMovimentoInterno');
            $Window->set_titulo('Incluir Movimento');
            $Window->abre();

                # Cria o Formulário
                $form = new Form('?fase=inserirMovimento','tramita');
                $form->set_onSubmit("?fase=inserirMovimento");        // insere rotina extra em jscript
                
                    # Número do Processo
                    $controle = new Input('processo','hidden');
                    $controle->set_valor($id);
                    $controle->set_linha(1);
                    $form->add_item($controle);
					
					# Origem
                    $controle = new Input('origem','texto','Origem:',1);
                    $controle->set_size(50);                                     
                    $controle->set_linha(2);
                    $controle->set_title('Origem');
					$controle->set_valor($lotacaoAtual);
					$controle->set_autofocus(true);
                    $form->add_item($controle);
					
					# Destino
					$controle = new Input('destino','texto','Destino:',1);
                    $controle->set_size(50);                                     
                    $controle->set_linha(3);
                    $controle->set_title('Destino');
                    $form->add_item($controle);
                    
					# Motivo
					$controle = new Input('motivo','texto','Motivo:',1);
                    $controle->set_size(50);                                     
                    $controle->set_linha(4);
                    $controle->set_title('Motivo');
                    $form->add_item($controle);
					
					# matricula logada
					$controle = new Input('matricula','hidden');
                    $controle->set_valor($matricula);
                    $controle->set_linha(5);
                    $form->add_item($controle);
					
					# setor
					$controle = new Input('setor','hidden');
                    $controle->set_valor($lotacao);
                    $controle->set_linha(5);
                    $form->add_item($controle);
					
                    # submit
                    $controle = new Input('submit','submit');
                    $controle->set_valor(' Cadastrar ');
                    $controle->set_size(20);
                    $controle->set_linha(1);
                    $controle->set_tabIndex(6);
                    $controle->set_accessKey('E');
                    $form->add_item($controle);

                $form->show();		

            $Window->fecha();
			break;
			
		#####################################

        case "inserirMovimento" :
			
            /**
            * Fase que valida e insere um movimento do processo
            */

            # Variáveis para tratamento de erros
            $erro = 0;	  	// flag de erro: 1 - tem erro; 0 - não tem	
            $msgErro = null; 	// repositório de mensagens de erro

            # Pega os valores digitados
            $numProcesso= post('processo');            
            $origem = post('origem');
			$destino = post('destino');
            $motivo = post('motivo');
            $servidor = post('matricula');
            $setor = post('setor');
			
            # Instancia um objeto de validação
            $valida = new Valida();            

            # verifica se a origem foi preenchida somente se não for inicial            
            if ($valida->vazio($origem))
            {
                $msgErro.='O campo de origem deve ser preenchido !!\n';
                $erro = 1;
            }
            

            # verifica se o destino foi preenchida
            if ($valida->vazio($destino))
            {
                $msgErro.='O campo destino deve ser preenchido !!\n';
                $erro = 1;
            }

            if ($erro == 1)
            {
                $alert = new Alert($msgErro) ;
                $alert->show();

                back(1);
            }
            else
            {   
                # prepara os dados a serem gravados
                # pega a data e hora atual
                $data = date("Y-m-d H:i:s");       

                # Faz a gravação no banco de dados
                $intra->set_tabela('tbmovimentointerno');
                $intra->gravar(array("processo","data","origem","destino","motivo","matricula","setor"), 
                               array($numProcesso,$data,$origem,$destino,$motivo,$matricula,$setor));

                # Pega o id
                $id = $intra->get_lastId();

                # Log
                $Objetolog = new Intra();
                $data = date("Y-m-d H:i:s");
                $Objetolog->registraLog($matricula,$data,'Tramitou internamente ('.$origem.' - '.$destino.' - '.$motivo.') o Processo '.$intra->get_numeroProcesso($numProcesso).'.','tbmovimentointerno',$id,null,null,null,$numProcesso);	

                loadPage('?fase=exibeMovimentos&id='.$numProcesso);
            } 
            break;   

        ##############################	

        case "excluirMovimento" :

            # pega as variaveis por get
            $id = get('id');			
			$processo = get('processo');

             # Conecta com o banco de dados
             $intra = new $intra();
             $intra->set_tabela('tbmovimentointerno');	# a tabela
             $intra->set_idCampo('idmovimentointerno');	# o nome do campo id

            if($intra->excluir($id));
            {		        
                $Objetolog = new Intra();
                $data = date("Y-m-d H:i:s");
                $Objetolog->registraLog($matricula,$data,'Excluiu movimentação interna do processo '.$intra->get_numeroProcesso($processo).'.','tbmovimentointerno',$id,null,null,null,$processo);	
            }

            loadPage('?fase=exibeMovimentos&id='.$processo);
            break;

        ###############################	
    }              

    $page->terminaPagina();
}
?>