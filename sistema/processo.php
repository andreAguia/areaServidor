<?php
/**
 * Cadastro de Processos
 *  
  By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
    
    # Verifica a fase do programa
    $fase = get('fase');

    # pega o número do processo (se tiver)
    $processo = get('processo');

    # Pega o parametro de pesquisa (se tiver)
    #if (is_null(post('parametro')))					# Se o parametro n�o vier por post (for nulo)
    #    $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    #else
    #{ 
    #    $parametro = post('parametro');			# Se vier por post, retira as aspas e passa para a variavel parametro			
    #    set_session('sessionParametro',$parametro);	# transfere para a session para poder recuper�-lo depois
    #}

    # Insere jscript extra de valida��o dos campos de inclus�o de tramita��o
    $jscript = '<script language="JavaScript" >

                function enviardados()
                {
                    // valida se o dia est� em branco
                    if(document.inclusao.dia.value=="")
                    {
                        alert( "O campo DIA est� vazio!" );
                        document.inclusao.dia.focus();
                        return false;
                    }

                    // valida se o m�s est� em branco
                    if (document.inclusao.mes.value=="")
                    {
                        alert( "O campo M�S est� vazio!" );
                        document.inclusao.mes.focus();
                        return false;
                    }

                    // valida se o ano est� em branco
                    if (document.inclusao.ano.value=="")
                    {
                        alert( "O campo ANO est� vazio!" );
                        document.inclusao.ano.focus();
                        return false;
                    }

                    // valida se o assunto est� em branco
                    if (document.inclusao.assunto.value=="")
                    {
                        alert( "O campo ASSUNTO est� vazio!" );
                        document.inclusao.assunto.focus();
                        return false;
                    }

                    return true;
                }
                </script>';

    # Come�a uma nova p�gina
    $page = new Page();
    $page->set_jscript($jscript);
    $page->iniciaPagina();

    # Cabeçalho
    AreaServidor::cabecalho();
    
    # Limita o tamanho da tela
    $grid1 = new Grid();
    $grid1->abreColuna(12);
    
    switch ($fase){
        case "" :
            botaoVoltar('areaServidor.php');
            titulo('Cadastro de Processos');
            br();
            
            
            
            $grid2 = new Grid();
            $grid2->abreColuna(3);
            
            # Rotina de pesquisa
            $fieldset = new Fieldset('Pesquisar Processo','pesquisarProcesso');
            $fieldset->abre();
            
            # Formulário custom
            echo '
            <form action="?fase=valida">
                <div class="input-group">
                    <span class="input-group-label">E-</span>
                    <input class="input-group-field" type="number">
                    
                    <span class="input-group-label">/</span>
                    <input class="input-group-field" type="number">
                    
                    <span class="input-group-label">/</span>
                    <input class="input-group-field" type="number">
                    
                    <span class="input-group-label">/</span>
                    <input class="input-group-field" type="number">
                    
                    <div class="input-group-button">
                        <input type="submit" class="button" value="Pesquisar">
                    </div>
                </div>
            </form>';
            
            $fieldset->fecha();    
            
            $grid2->fechaColuna();
            $grid2->fechaGrid();
            break;

        ###############################	

        case "valida" :
            # Valida o n�mero de processo da pesquisa

            # Vari�veis para tratamento de erros
            $erro = 0;	  	// flag de erro: 1 - tem erro; 0 - n�o tem	
            $msgErro = null; 	// reposit�rio de mensagens de erro

            # Pega os valores digitados
            $origem = post('origemProcesso');
            $origem2 = post('origemProcesso2');
            $numero = post('numeroProcesso');
            $ano = post('anoProcesso');

            # Instancia um objeto de valida��o
            $valida = new Valida();

            # verifica se algum campo est� vazio
            if (($valida->vazio($origem)) or ($valida->vazio($numero)) or ($valida->vazio($ano)))
            {
                $msgErro.='O n�mero do processo est� errado !!\n';
                $erro = 1;
            }

            # Se o campos origem2 estiver vazio desconsidera, mas se n�o acrescenta zeros
            if (!$valida->vazio($origem2))
            {
                if (strlen($origem2)==1)
                    $origem2 = '00'.$origem2;

                if (strlen($origem2)==2)
                    $origem2 = '0'.$origem2;
            }

            # preenche com zero a esquerda o tamanho de origem quando for menor que 2
            if (strlen($origem)==1)
                $origem = '0'.$origem;

           # preenche com zero a esquerda o tamanho do n�mero quando for menor que 6
            $tamanhoNumero = strlen($numero);
            $diferenca = 6-$tamanhoNumero;

            if ($diferenca <>0)
                $numero = str_repeat("0", $diferenca).$numero;

            # valida o ano
            if (($ano < '1990') or ($ano > date('Y')))
            {
                $msgErro.='O ano est� errado !!\n';
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
                if ($valida->vazio($origem2))
                    $numeroProcesso2 = "E-".$origem."/".$numero."/".$ano;
                else
                    $numeroProcesso2 = "E-".$origem."/".$origem2."/".$numero."/".$ano;

                $idProcesso = $intra->get_idProcesso($numeroProcesso2);
                if(is_null($idProcesso))
                    loadPage('?fase=incluir&processo='.$numeroProcesso2);
                else
                    loadPage('processosMovimentos.php?processo='.$idProcesso); 
            } 
            break;

        ###############################	

        case "incluir" : 
            # Rotina que faz a inclus�o de um processo n�o encontrado na pesquisa

            # Bot�o voltar
            botaoVoltar('?','Cancelar e Voltar','Volta para a p�gina inicial da intranet');

            # Mensagem
            $fieldset = new Fieldset('Aten��o','fieldsetProcessoNaoEncontrado');
            $fieldset->abre();
            $mensagem = '<br /><b>'.$processo.'</b><br /><br />
                         N�o foi encontrado esse processo no sistema !!<br/><br/>
                         Voc� poder� inclu�-lo como um novo processo ou voltar para fazer uma nova pesquisa.<br/><br/>';

            $p = new P($mensagem);
            $p->show();					
            $fieldset->fecha();

            # Formul�rio de inclus�o
            $fieldset = new Fieldset('Incluir Processo','fieldsetIncluirProcesso');        
            $fieldset->abre();

            $form = new Form('?fase=validaInclusao','inclusao');
            $form->set_table(false);
            $form->set_onSubmit("return enviardados();");        // insere rotina extra em jscript
            
                # Processo
                $controle = new Input('processo','texto','Processo',1);
                $controle->set_size(30);
                $controle->set_readonly(true);            
                $controle->set_title('O N�mero do Processo');
                $controle->set_valor($processo);
                $controle->set_linha(1);
                $controle->set_col(3);
                $form->add_item($controle); 

                # Data de Abertura do Processo (dia)
                $controle = new Input('dia','numero','Data de Abertura do Processo',1);
                #if(BROWSER_NAME == "MSIE")
                #    $controle->set_size(3); // conserta bug do IE
                #else
                    $controle->set_size(2);                      
                $controle->set_title('Dia da abertura do processo');
                $controle->set_pularPara('mes');
                $controle->set_autofocus(true);
                $controle->set_linha(1);
                $controle->set_col(3);
                $form->add_item($controle);

                # Data de Abertura do Processo (m�s)
                $controle = new Input('mes','numero','/ ',2);
                $controle->set_size(2);          
                $controle->set_title('M�s da abertura do processo');
                $controle->set_pularPara('assunto');
                $controle->set_linha(1);
                $controle->set_col(3);
                $form->add_item($controle);

                # pega o ano do processo
                $ano = substr($processo, -4);

                # Data de Abertura do Processo (ano)
                $controle = new Input('ano','numero','/ ',2);
                $controle->set_size(4);
                $controle->set_linha(1);
                $controle->set_col(3);
                $controle->set_readonly(true);   
                $controle->set_title('Ano do Processo');
                $controle->set_valor($ano);
                $form->add_item($controle);

                # Data de Abertura do Processo (m�s)
                $controle = new Input('assunto','textarea','Assunto:',1);
                $controle->set_size(array(60,5));
                $controle->set_linha(5);            
                $controle->set_title('Do que se trata o processo');
                $form->add_item($controle);

                # submit
                $controle = new Input('submit','submit');

                # Verifica qual o tipo de usu�rio est� cadastrando
                if($intra->verificaPermissao($matricula,8))
                    $controle->set_valor(' Cadastrar o Processo como Arquivado no '.$servidor->get_lotacao($matricula));
                else
                    $controle->set_valor(' Cadastrar ');

                $controle->set_size(20);
                $controle->set_formLinha(1);

                $controle->set_formAlign('center');
                $controle->set_linha(6);
                $controle->set_accessKey('E');
                $form->add_item($controle);

            $form->show();	

            $fieldset->fecha();

            # Post it
            $postit = new Postit('Procure colocar no campo
                                  assunto uma descri��o detalhada
                                  do processo, se poss�vel, 
                                  com o nome do solicitante e a que se refere o processo para ajudar em uma busca posterior.','postitInclusaoProcesso');
            
            $postit->show();

            break;

        ###############################	

        case "validaInclusao" :
            # Valida a inclus�o de processo

            # Vari�veis sobre um erro fatal (que n�o pode prosseguir com ele)
            $erro = 0;	  	// flag de erro: 1 - tem erro; 0 - n�o tem	
            $msgErro = null; 	// reposit�rio de mensagens de erro

            # Pega os valores digitados
            $processo = post('processo');
            $dia = post('dia');
            $mes = post('mes');        
            $ano = post('ano');
            $assunto = post('assunto');

            # Transforma aspas simples para dupla        
            $assunto = str_replace("'",'"',$assunto);	

            # Instancia um objeto de valida��o
            $valida = new Valida();

            # verifica se algum campo est� vazio
            if (($valida->vazio($dia)) or ($valida->vazio($mes)) or ($valida->vazio($ano)) or ($valida->vazio($assunto)))
            {
                $msgErro.='Todos os campos s�o obrigat�rios !!\n';
                $erro = 1;
            }

            $dataprocesso = $dia.'/'.$mes.'/'.$ano;

            # verifica a validade da data
            if (!Data::validaData($dataprocesso)){
                $msgErro.='A data n�o � v�lida !!\n';
                $erro = 1;
            }else{
                $dataprocesso = date_to_bd($dataprocesso);
            }


            # Verifica (de novo) se n�o tem realmente algum processo com esse numero
            $idProcesso = $intra->get_idProcesso($processo);
            if(!is_null($idProcesso))
            {
                $msgErro.='Ja existe um processo com esse n�mero !!\n';
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
                $intra->set_tabela('tbprocesso');
                $intra->gravar(array("numero","assunto","data"), 
                               array($processo,$assunto,$dataprocesso));

                # Pega o id
                $novoProcesso = $intra->get_lastId();

                # Log
                $Objetolog = new Intra();
                $data = date("Y-m-d H:i:s");
                $Objetolog->registraLog($matricula,$data,'Cadastrou o Processo '.$processo.'.','tbprocesso',$novoProcesso);	

                # Cadastra o setor onde o processo est� arquivado (se for usuario com regra 8)
                if($intra->verificaPermissao($matricula,8)){
                    # Faz a grava��o no banco de dados
                    $intra->set_tabela('tbprocessoMovimento');
                    $intra->gravar(array("processo","data","origem","destino"), 
                                   array($novoProcesso,$data,NULL,$servidor->get_idlotacao($matricula)),null,null,null,false);

                    # Pega o id
                    $id = $intra->get_lastId();

                    # Log
                    $Objetolog = new Intra();               
                    $Objetolog->registraLog($matricula,$data,'Processo '.$processo.' arquivado em '.$servidor->get_lotacao($matricula),'tbprocessomovimento',$id);

                }

                loadPage('processosMovimentos.php?processo='.$intra->get_idProcesso($processo));
            } 
            break; 
    }

    ################################################################		

    $grid1->fechaColuna();
    $grid1->fechaGrid();    
    
    $page->terminaPagina();
}