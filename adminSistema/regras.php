<?php
/**
 * Cadastro de regras
 *  
 * By Alat
 */

# Inicia as variáveis que receberão as sessions
$matricula = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idusuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # pega o id do computador para quando for emitir ficha de OS
    $computador = get('regra');

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();

    # Inicia a classe de interface da área do servidor
    $area = new AreaServidor();

    # Cabeçalho da Página
    if($fase <> 'servidoresPermissao')
        AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Regras e Permissões');

    # botão salvar
    $objeto->set_botaoSalvarGrafico(false);

    # botão de voltar da lista   
    $objeto->set_voltarLista('usuarios.php');

    # ordenação
    if(is_null($orderCampo))
        $orderCampo = 1;

    if(is_null($orderTipo))
        $orderTipo = 'asc';

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');
    
    # Parametros da tabela
    $objeto->set_label(array("Num","Nome","Descrição","Servidores","Ver","Visível?"));
    $objeto->set_width(array(5,25,40,10,5,5));		
    $objeto->set_align(array("center","left","left"));

    # select da lista
    $objeto->set_selectLista('SELECT idRegra,
                                     nome,
                                     descricao,
                                     idRegra,
                                     idRegra,
                                     IF(visivel,"Sim","Não")
                                FROM tbregra
                            ORDER BY '.$orderCampo.' '.$orderTipo);	

    $objeto->set_botaoExcluir(true);
    $objeto->set_botaoIncluir(true);

    # select do edita
    $objeto->set_selectEdita('SELECT nome,
                                     descricao,
                                     visivel
                                FROM tbregra
                               WHERE idRegra = '.$id);

    $objeto->set_formatacaoCondicional(array(array('coluna' => 5,
                                                   'valor' => 'Sim',
                                                   'operador' => '=',
                                                   'id' => 'regraVisivel'),
                                             array('coluna' => 5,
                                                   'valor' => 'Não',
                                                   'operador' => '=',
                                                   'id' => 'regraInvisivel')));
    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkExcluir('?fase=excluir');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');    

    $objeto->set_classe(array(null,null,null,"Intra"));
    $objeto->set_metodo(array(null,null,null,"get_numeroServidoresPermissao")); 

    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new BotaoGrafico();
    $botao->set_label('');
    $botao->set_title('Servidores com permissão a essa regra');
    $botao->set_onClick("abreDivId('divPermissao'); ajaxLoadPage('?fase=servidoresPermissao&id=','divPermissao',");       
    $botao->set_image(PASTA_FIGURAS.'ver.png',20,20);
    

    # Coloca o objeto link na tabela			
    $objeto->set_link(array("","","","",$botao));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbregra');

    # Nome do campo id
    $objeto->set_idCampo('idRegra');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $campos = array(array ( 'nome' => 'nome',
                            'label' => 'Nome:',
                            'tipo' => 'texto',
                            'size' => 90,
                            'maxlength' => 45,
                            'required' => true,
                            'title' => 'Nome da Regra.',                            
                            'col' => 8,
                            'linha' => 1),
                    array ( 'nome' => 'visivel',
                            'label' => 'Visivel:',
                            'tipo' => 'combo',
                            'array' => array(array(1,"Sim"),array(0,"Não")),
                            'title' => 'Se é Visível ou não',
                            'padrao' => 0,
                            'size' => 10,
                            'col' => 4,
                            'linha' => 1),        
                    array ( 'nome' => 'descricao',
                            'label' => 'Descrição:',
                            'tipo' => 'textarea',
                            'size' => array(90,5),
                            'required' => true,
                            'title' => 'Descrição resumida da regra.',                            
                            'col' => 12,
                            'linha' => 2));

    $objeto->set_campos($campos);

    # Matrícula para o Log
    $objeto->set_matricula($matricula);

    ################################################################
    switch ($fase)
    {
        case "" :
        case "listar" :               
            $objeto->listar();

            # Div de servidores com essa permissão
            $divPermissao = new Div('divPermissao');
            $divPermissao->abre();
            
            $divPermissao->fecha();
            break;

        case "editar" :
        case "excluir" :	
        case "gravar" :        
            $objeto->$fase($id);
            break;

        ##################################################################

        case "servidoresPermissao":
            # Informa os servidores com essa permissão
            $callout = new Callout();
            $callout->abre();
                titulo('Permissões');
            
                # Monta a tabela        
                $select = 'SELECT tbpermissao.matricula,
                                  pessoal.tbpessoa.nome,
                                  tbpermissao.matricula,							  
                                  tbpermissao.matricula,
                                  idpermissao
                             FROM tbpermissao JOIN pessoal.tbfuncionario on(tbpermissao.matricula = pessoal.tbfuncionario.matricula)
                                              JOIN pessoal.tbpessoa on(pessoal.tbfuncionario.idPessoa = pessoal.tbpessoa.idPessoa)
                            WHERE idregra = '.$id.'
                         ORDER BY pessoal.tbpessoa.nome';
                $result = $intra->select($select,true);
                $tabela = new tabela();
                $tabela->set_conteudo($result);
                $tabela->set_label(array("Matricula","Nome","Lotação","Cargo"));
                $tabela->set_width(array(10,25,30,30));
                $tabela->set_align(array("center","left","center","left"));
                $tabela->set_funcao(array('dv'));
                
                $tabela->set_classe(array(null,null,"Pessoal","Pessoal"));
                $tabela->set_metodo(array(null,null,'get_lotacao','get_cargo')); 
    
                $tabela->set_excluir('?fase=excluirPermissao');
                $tabela->set_idCampo('idpermissao');

                if(count($result) == 0){        
                    p('<br/><br/>Não há Servidores.<br/><br/>','center');
                }else{
                    $tabela->show();
                }

            $callout->fecha();
            break;

        ##################################################################	

        case "excluirPermissao" :
            # Pega os dados caso seja tbpermissao
            $intra = new Intra();
            $pessoal = new Pessoal();
            $permissao = $intra->get_permissao($id);
            $atividade = 'Excluiu a permissao de: '.$permissao[1].' da matrícula '.$permissao[0].' ('.$pessoal->get_nome($permissao[0]).')';

            # Conecta com o banco de dados
            $objeto = new Intra();
            $objeto->set_tabela('tbpermissao');	# a tabela
            $objeto->set_idCampo('idPermissao');	# o nome do campo id
            $objeto->excluir($id);			# executa a exclusão

            # Grava no log a atividade
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($matricula,$data,$atividade,'tbpermissao',$id);	

            loadPage ('?');
            break;

        ##################################################################
    }									 	 		

    $page->terminaPagina();
}