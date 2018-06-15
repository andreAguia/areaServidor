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

    # Começa uma nova página
    $page = new Page();			
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

            # Exemplo com mais itens
            $tabela = new Tabela();
            $tabela->set_titulo("Sistema de Controle de Processos");
            $tabela->set_conteudo($row);
            $tabela->set_label(array("Data","Processo","Assunto","Movimentação"));
            $tabela->set_width(array(10,20,65));
            $tabela->set_align(array("center","center","left"));
            $tabela->set_funcao(array("date_to_php",NULL,"retiraAcento"));

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
            $controle = new Input('tipo','radio','Processo:',1);
            
            # Processo Novo
            $controle = new Input('numeroNovo','processoNovo','Processo:',1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_col(4);
            $controle->set_autofocus(TRUE);       
            $controle->set_title('Processo');
            $form->add_item($controle);
            
            
            #<INPUT TYPE="radio" NAME="OPCAO" VALUE="op1" CHECKED> opção1
            #<INPUT TYPE="radio" NAME="OPCAO" VALUE="op2"> opção2
            #<INPUT TYPE="radio" NAME="OPCAO" VALUE="op3"> opção3
            
            # Processo Antigo
            $controle = new Input('numeroNovo','processo','Processo:',1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_col(4);
            $controle->set_autofocus(TRUE);       
            $controle->set_title('Processo');
            $form->add_item($controle);
            
            # Data
            $controle = new Input('data','data','Data:',1);
            $controle->set_size(10);
            $controle->set_linha(1);
            $controle->set_col(3);
            $controle->set_title('Data');
            $form->add_item($controle);
            
            # Assunto
            $controle = new Input('assunto','textarea','Assunto:',1);
            $controle->set_size(array(90,5));
            $controle->set_linha(2);
            $controle->set_col(12);
            $controle->set_title('Assunto');
            $form->add_item($controle);
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(4);
            $form->add_item($controle);
            
            $form->show();
            $painel->fecha();
            break;		
    }
    
    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}