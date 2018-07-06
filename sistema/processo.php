<?php
/**
 * Cadastro de Processos
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,5);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));
    
    # Define como padrão a máscara do processo novo
    #$tipoProcesso = get("tipoProcesso","processoNovo");

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))){                                     # Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    }else{ 
        $parametro = post('parametro');								# Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro',$parametro);			 		# transfere para a session para poder recuperá-lo depois
    }

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('order_tipo');
    
    # jscript
    $jscript = '<script type="text/javascript">
                    $(document).ready(function () {
                    $("#novo").click(function () {
                       $("#numeroAntigo").hide("fast");
                       $("#labelnumeroAntigo").hide("fast");
                       $("#spannumeroAntigo").hide("fast");
                       $("#divnumeroAntigo").hide("fast");
                       
                       $("#numeroNovo").show("fast");
                       $("#labelnumeroNovo").show("fast");
                       $("#spannumeroNovo").show("fast");
                       $("#divnumeroNovo").show("fast");
                });
                
                $("#antigo").click(function () {
                      $("#numeroNovo").hide("fast");
                      $("#labelnumeroNovo").hide("fast");
                      $("#spannumeroNovo").hide("fast");
                      $("#divnumeroNovo").hide("fast");
                      
                      $("#numeroAntigo").show("fast");
                      $("#labelnumeroAntigo").show("fast");
                      $("#spannumeroAntigo").show("fast");
                      $("#divnumeroAntigo").show("fast");
                 });
               });
               
               function carrega(){
                    $("#numeroAntigo").hide("fast");
                    $("#labelnumeroAntigo").hide("fast");
                    $("#spannumeroAntigo").hide("fast");
                    $("#divnumeroAntigo").hide("fast");
                       
                    $("#numeroNovo").show("fast");
                    $("#labelnumeroNovo").show("fast");
                    $("#spannumeroNovo").show("fast");
                    $("#divnumeroNovo").show("fast");;
               }
               
               </script>';
    
    # Começa uma nova página
    $page = new Page();
    
    if($fase == "incluir"){
        $page->set_jscript($jscript);
        $page->set_bodyOnLoad("carrega();");
    }
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();
    
    $grid1 = new Grid();
    $grid1->abreColuna(12);
    
################################################################
    
    switch ($fase) {
        case "" :
        case "listar" :
            # Inicia o sesion do processo
            set_session('idProcesso');
            
            # Cria um menu
            $menu1 = new MenuBar();

            # Incluir
            $linkSobre = new Link("Incluir","?fase=incluir");
            $linkSobre->set_class('button');
            $linkSobre->set_title('Exibe informações do Sistema');
            $menu1->add_link($linkSobre,"right");

            $menu1->show();
            
            # Pesquisa
            $form = new Form('?');

            # campo de pesquisa
            $controle = new Input('parametro','texto','Pesquisar:',1);
            $controle->set_size(50);
            $controle->set_title('Pesquisa:');
            $controle->set_valor($parametro);
            $controle->set_autofocus(TRUE);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(6);
            $form->add_item($controle);

            $form->show();
            
            # Grava o log de pesquisa
            if(!vazio($parametro)){
                # Grava no log a atividade
                $data = date("Y-m-d H:i:s");
                $atividade = 'Pesquisou no controle de processos: '.$parametro;
                $tipoLog = 7;
                $intra->registraLog($idUsuario,$data,$atividade,"tbprocesso",NULL,$tipoLog);
            }

            # acessa o banco
            $select = 'SELECT data,
                              numero,
                              assunto,
                              idProcesso,
                              idProcesso
                         FROM tbprocesso
                        WHERE data LIKE "%'.$parametro.'%"
                           OR numero LIKE "%'.$parametro.'%"	
                           OR assunto LIKE "%'.$parametro.'%"
                     ORDER BY 1 desc';

            $row = $intra->select($select);

            # Tabela
            $tabela = new Tabela();
            $tabela->set_titulo("Sistema de Controle de Processos");
            $tabela->set_conteudo($row);
            $tabela->set_label(array("Data","Processo","Assunto","Mov."));
            $tabela->set_width(array(10,25,60));
            $tabela->set_align(array("center","center","left"));
            $tabela->set_funcao(array("date_to_php",NULL,"retiraAcento"));
            $tabela->set_textoRessaltado($parametro);

            # Botão de exibição dos servidores com permissão a essa regra
            $botao = new Link(NULL,'processoMovimentacao.php?idProcesso=','Movimentação do Processo');
            $botao->set_image(PASTA_FIGURAS.'movimentacao.png',20,20);
            $tabela->set_link(array(NULL,NULL,NULL,$botao));
            $tabela->set_idCampo('idProcesso');

            $tabela->show();
            break;

        case "incluir" :
            botaoVoltar("?");
            tituloTable("Incluir Novo Processo");
            
            $painel = new Callout();
            $painel->abre();
            
            # Inicia o formulário
            $form = new Form('?fase=valida');
            
            # tipo de processo
            $controle = new Input('tipo','radio','Novo:');
            $controle->set_valor("novo");
            $controle->set_id("novo");
            $controle->set_linha(1);
            $controle->set_col(2);
            $controle->set_checked(TRUE);
            $form->add_item($controle);
            
            $controle = new Input('tipo','radio','Antigo:');
            $controle->set_valor("antigo");
            $controle->set_id("antigo");
            $controle->set_linha(1);
            $controle->set_col(2);
            $form->add_item($controle);
            
            # Processo Novo
            $controle = new Input('numeroNovo','processoNovoReduzido','Processo (Num. Novo) *:',1);
            $controle->set_size(15);
            $controle->set_linha(2);
            $controle->set_col(6);                  
            $controle->set_title('Processo');
            $controle->set_inLine("E-");
            $form->add_item($controle);
            
            # Processo Antigo
            $controle = new Input('numeroAntigo','processoReduzido','Processo (Num. Antigo): *',1);
            $controle->set_size(15);
            $controle->set_linha(3);
            $controle->set_col(6);    
            $controle->set_title('Processo');
            $controle->set_inLine("E-");
            $form->add_item($controle);
            
            # Data
            $controle = new Input('data','data','Data:',1);
            $controle->set_size(10);
            $controle->set_linha(4);
            $controle->set_col(6);
            $controle->set_title('Data');
            $controle->set_required(TRUE);
            $form->add_item($controle);
            
            # Assunto
            $controle = new Input('assunto','textarea','Assunto:',1);
            $controle->set_size(array(90,5));
            $controle->set_linha(5);
            $controle->set_col(12);
            $controle->set_title('Assunto');
            $controle->set_required(TRUE);
            $form->add_item($controle);
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(4);
            $form->add_item($controle);
            
            $form->show();
            $painel->fecha();
            break;
        
        case "valida" :
            # Pega os dados digitados
            $numeroNovo = post("numeroNovo");
            $numeroAntigo = post("numeroAntigo");
            $data = post("data");
            $assunto = post("assunto");
            $tipo = post("tipo");
            
            # mensagem de erro
            $msgErro = NULL;
            $erro = 0;
            
            ###### Processo ######
            
            # Verifica que processo vai pegar
            if($tipo == "novo"){
                $processo = $numeroNovo;
            }elseif($tipo == "antigo"){
                $processo = $numeroAntigo;
            }
            
            # Verifica se processo está em branco
            if (vazio($processo)){
                $msgErro.='É necessário informa o número do Processo!\n';
                $erro = 1;
            }else{
            
                # Divide o processo em partes
                $partes = explode("/",$processo);

                # Preenche com zero a esquerda
                if($tipo == "novo"){
                    $partes[0] = str_pad($partes[0], 2, "0", STR_PAD_LEFT); 
                    $partes[1] = str_pad($partes[1], 3, "0", STR_PAD_LEFT); 
                    $partes[2] = str_pad($partes[2], 6, "0", STR_PAD_LEFT); 
                    $ano = $partes[3];
                }elseif($tipo == "antigo"){
                    $partes[0] = str_pad($partes[0], 2, "0", STR_PAD_LEFT);
                    $partes[1] = str_pad($partes[1], 6, "0", STR_PAD_LEFT); 
                    $ano = $partes[2];
                }

                # Verifica o ano
                if(strlen($ano) == 2){
                    if($ano > 70){
                        $ano = "19".$ano;
                    }else{
                        $ano = "20".$ano;
                    }
                }            

                # Ano com 3 números
                if((strlen($ano) == 3) OR (strlen($ano) == 1)){
                    $msgErro.='O ano deve ter 4 dígitos!\n';
                    $erro = 1;
                }

                # Ano com 4 números
                if(strlen($ano) == 4){
                    # Ano futuro
                    if($ano > date('Y')){
                        $msgErro.='O processo não pode ter ano futuro!\n';
                        $erro = 1;
                    }

                    # Ano muito antigo
                    if($ano < '1970'){
                        $msgErro.= 'Não se pode cadastrar processo anteriores a 1970!\n';
                        $erro = 1;
                    }
                }

                # Acerta o processo
                $processo = $partes[0]."/".$partes[1];
                if($tipo == "novo"){
                    $processo .= "/".$partes[2]."/".$ano;
                }elseif($tipo == "antigo"){
                    $processo .= "/".$ano;
                }
            }
            
            ##### Assunto #####
            
            # Verifica se o assunto está em branco
            if (vazio($assunto)){
                $msgErro.='É necessário informa o assunto do Processo!\n';
                $erro = 1;
            }
            
            ##### Data #####
            
            # Verifica se data está em branco
            if (vazio($data)){
                $msgErro.='É necessário informa a data!\n';
                $erro = 1;
            }else{
                if(HTML5){
                    $data = date_to_php($data);
                }
                
                if (!validaData($data)) {
                    $msgErro .= 'A data não é válida!\n';
                    $erro = 1;
                } else { # passa a data para o formato de gravação
                    $data = date_to_bd($data); 
                } 
            }
            
            if ($erro == 0){
                # Acrescenta o E-
                $processo = "E-".$processo;
                
                br();echo $processo;
                
                # Gravação
                $intra->set_tabela("tbprocesso");
                $intra->set_idCampo("idProcesso");
                
                $campoNome = array("data","assunto","numero");
                $campoValor = array($data,$assunto,$processo);
                $intra->gravar($campoNome,$campoValor);

                # Grava no log a atividade
                $data = date("Y-m-d H:i:s");
                $atividade = 'Incluiu processo '.$processo;
                $id = $intra->get_lastId();
                $tipoLog = 1;
                $intra->registraLog($idUsuario,$data,$atividade,"tbprocesso",$id,$tipoLog);
                
                aguarde();
                loadPage("?");
            }else{
                alert($msgErro);
                back(1);
            }		   	
            break;
            
        case "excluir" :
            # Exclui
            $intra->set_tabela("tbprocesso");
            $intra->set_idCampo("idProcesso");
            $processo = $intra->get_numProcesso($id);
            if($intra->excluir($id)){
                # Grava no log a atividade
                $data = date("Y-m-d H:i:s");
                $atividade = 'Excluiu o processo '.$processo;
                $tipoLog = 3;
                $intra->registraLog($idUsuario,$data,$atividade,"tbprocesso",$id,$tipoLog);
            }
            loadPage ("?");
            break;
            
         case "editar" :
             
            botaoVoltar("processoMovimentacao.php?idProcesso=".$id);
            tituloTable("Editar Processo");
            
            # acessa o banco
            $select = 'SELECT data,
                              numero,
                              assunto
                         FROM tbprocesso
                        WHERE idProcesso = '.$id;

            $row = $intra->select($select,FALSE);

            $painel = new Callout();
            $painel->abre();

            # Inicia o formulário
            $form = new Form('?fase=valida2&id='.$id);

            # Processo 
            $controle = new Input('processo','texto','Processo:',1);
            $controle->set_size(15);
            $controle->set_linha(2);
            $controle->set_col(6);
            $controle->set_valor($row[1]);
            if(Verifica::acesso($idUsuario,1)){   // Somente Administradores
                $controle->set_autofocus(TRUE);
            }else{
                $controle->set_readonly(TRUE);
            }
            $controle->set_required(TRUE);
            $controle->set_title('Processo');
            $form->add_item($controle);

            # Data
            $controle = new Input('data','data','Data:',1);
            $controle->set_size(10);
            $controle->set_linha(4);
            $controle->set_autofocus(TRUE);
            $controle->set_col(6);
            $controle->set_valor($row[0]);   
            $controle->set_title('Data');
            $controle->set_required(TRUE);
            $form->add_item($controle);

            # Assunto
            $controle = new Input('assunto','textarea','Assunto:',1);
            $controle->set_size(array(90,5));
            $controle->set_linha(5);
            $controle->set_col(12);
            $controle->set_valor($row[2]);   
            $controle->set_title('Assunto');
            $controle->set_required(TRUE);
            $form->add_item($controle);

            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(4);
            $form->add_item($controle);

            $form->show();
            $painel->fecha();
            break;
        
        case "valida2" :
            # Pega os dados digitados
            $processo = post("processo");
            $data = post("data");
            $assunto = post("assunto");
            
            # Pega os dados originais
            $select = 'SELECT data,
                              numero,
                              assunto
                         FROM tbprocesso
                        WHERE idProcesso = '.$id;
            echo $select;
            $row = $intra->select($select,FALSE);
            
            $atividade = 'Alterou o campo ';
            
            # Verifica se houve alteração
            # Data
            if($row[0] <> $data){
                $atividade .= '[data] '.date_to_php($row[0]).'->'.date_to_php($data);
            }
            
            # processo
            if($row[1] <> $processo){
                $atividade .= '[processo] '.$row[1].'->'.$processo;
            }
            
            # assunto
            if($row[2] <> $assunto){
                $atividade .= '[assunto] '.$row[2].'->'.$assunto;
            }
            
            # mensagem de erro
            $msgErro = NULL;
            $erro = 0;
            
            ###### Processo ######
            
            # Verifica que processo vai pegar
            $numeroBarra = substr_count($processo, '/');
            if($numeroBarra == 3){
                $tipo = "novo";
            }elseif($numeroBarra == 2){
                $tipo = "antigo";
            }
            
            # Verifica se processo está em branco
            if (vazio($processo)){
                $msgErro.='É necessário informa o número do Processo!\n';
                $erro = 1;
            }
            
            # Divide o processo em partes
            $partes = explode("/",$processo);
            echo $processo;
            # Preenche com zero a esquerda
            if($tipo == "novo"){
                $partes[0] = str_pad($partes[0], 2, "0", STR_PAD_LEFT); 
                $partes[1] = str_pad($partes[1], 3, "0", STR_PAD_LEFT); 
                $partes[2] = str_pad($partes[2], 6, "0", STR_PAD_LEFT); 
                $ano = $partes[3];
            }elseif($tipo == "antigo"){
                $partes[0] = str_pad($partes[0], 2, "0", STR_PAD_LEFT);
                $partes[1] = str_pad($partes[1], 6, "0", STR_PAD_LEFT); 
                $ano = $partes[2];
            }
            
            # Verifica o ano
            if(strlen($ano) == 2){
                if($ano > 70){
                    $ano = "19".$ano;
                }else{
                    $ano = "20".$ano;
                }
            }            
            
            # Ano com 3 números
            if((strlen($ano) == 3) OR (strlen($ano) == 1)){
                $msgErro.='O ano deve ter 4 dígitos!\n';
                $erro = 1;
            }
            
            # Ano com 4 números
            if(strlen($ano) == 4){
                # Ano futuro
                if($ano > date('Y')){
                    $msgErro.='O processo não pode ter ano futuro!\n';
                    $erro = 1;
                }
                
                # Ano muito antigo
                if($ano < '1970'){
                    $msgErro.= 'Não se pode cadastrar processo anteriores a 1970!\n';
                    $erro = 1;
                }
            }
            
            # Acerta o processo
            $processo = $partes[0]."/".$partes[1];
            if($tipo == "novo"){
                $processo .= "/".$partes[2]."/".$ano;
            }elseif($tipo == "antigo"){
                $processo .= "/".$ano;
            }
            
            ##### Assunto #####
            
            # Verifica se o assunto está em branco
            if (vazio($assunto)){
                $msgErro.='É necessário informa o assunto do Processo!\n';
                $erro = 1;
            }
            
            ##### Data #####
            
            # Verifica se data está em branco
            if (vazio($data)){
                $msgErro.='É necessário informa a data!\n';
                $erro = 1;
            }else{
                if(HTML5){
                    $data = date_to_php($data);
                }
                
                if (!validaData($data)) {
                    $msgErro .= 'A data não é válida!\n';
                    $erro = 1;
                } else { # passa a data para o formato de gravação
                    $data = date_to_bd($data); 
                } 
            }
            
            if ($erro == 0){
                
                # Gravação
                $intra->set_tabela("tbprocesso");
                $intra->set_idCampo("idProcesso");
                
                $campoNome = array("data","assunto","numero");
                $campoValor = array($data,$assunto,$processo);
                $intra->gravar($campoNome,$campoValor,$id);

                # Grava no log a atividade
                $data = date("Y-m-d H:i:s");
                $tipoLog = 1;
                $intra->registraLog($idUsuario,$data,$atividade,"tbprocesso",$id,$tipoLog);
                
                aguarde();
                loadPage("?");
            }else{
                alert($msgErro);
                back(1);
            }		   	
            break;
    }
    
    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}