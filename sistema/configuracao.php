<?php
/**
 * Gerencia as Variáveis do Sistema
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');
    
    # Varifica a Categoria
    $categoria = get("categoria","Sistema");

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Ordem da tabela
    $orderCampo = get('orderCampo',2);
    $orderTipo = get('order_tipo','asc');

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    if(is_null($categoria)){
        $objeto->set_nome('Configurações do Sistema');
    }else{
        $objeto->set_nome('Configurações do Sistema - '.$categoria);
    }

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

    # ordenação
    if(is_null($orderCampo))
            $orderCampo = 1;

    if(is_null($orderTipo))
            $orderTipo = 'asc';

    # select da lista
    $select = 'SELECT categoria,
                      nome,                      
                      comentario,
                      valor,
                      idVariaveis
                 FROM tbvariaveis';
    
    if(!is_null($categoria)){
        $select .= ' WHERE categoria = "'.$categoria.'" ';
    }
    
    $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo;	

    $objeto->set_selectLista($select);
    # select do edita
    $objeto->set_selectEdita('SELECT categoria,
                                     nome,                                     
                                     comentario,	
                                     valor							    
                                FROM tbvariaveis
                               WHERE idVariaveis = '.$id);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(array("Categoria","Nome","Comentário","Valor"));
    #$objeto->set_width(array(10,10,10,60));		
    $objeto->set_align(array("center","left","left","left"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbvariaveis');

    # Nome do campo id
    $objeto->set_idCampo('idVariaveis');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array(
                        array ( 'nome' => 'categoria',
                                'label' => 'Categoria:',
                                'tipo' => 'texto',
                                'required' => TRUE,
                                'size' => 50,
                                'title' => 'Categoria da Variável.',
                                'autofocus' => TRUE,
                                'col' => 4,
                                'linha' => 1),
                        array ( 'nome' => 'nome',
                                'label' => 'Nome:',
                                'tipo' => 'texto',
                                'size' => 90,
                                'title' => 'Nome da Variável.',
                                'required' => TRUE,
                                'col' => 8,
                                'linha' => 1),
                        array ( 'nome' => 'comentario',
                                'label' => 'Comentário:',
                                'tipo' => 'textarea',
                                'size' => array(90,5),
                                'required' => TRUE,
                                'title' => 'Descrição resumida da utilidade da variável.',
                                'col' => 12,
                                'linha' => 2),
                        array ( 'nome' => 'valor',
                                'label' => 'Valor:',
                                'tipo' => 'texto',
                                'size' => 90,
                                'title' => 'Valor da Variável.',
                                'required' => TRUE,
                                'col' => 12,
                                'linha' => 3),	 	 	 	 	 	 
                    ));

    # Log
    $objeto->set_idUsuario($idUsuario);
    
    ################################################################
    switch ($fase)
    {
        case "" :
        case "listar" :
            # Filtra por cateforia
                # Por controle
                $form = new Form('?');
                # Situação
                $result = $intra->select('SELECT distinct categoria, categoria
                                              FROM tbvariaveis                                
                                          ORDER BY 1');

                $controle = new Input('categoria','combo','Categoria:',1);
                $controle->set_size(30);
                $controle->set_title('Filtra por Situação');
                $controle->set_array($result);
                $controle->set_valor($categoria);
                $controle->set_onChange('formPadrao.submit();');
                $controle->set_linha(1);
                $controle->set_col(4);
                $form->add_item($controle);

                #$form->show();
                
                # Por Botòes
                $menu = array();
                foreach ($result as $value) {
                    $botao = new Link($value[0],"?categoria=".$value[0]);
                    $botao->set_class('button success');
                    $botao->set_title('Filtra para categoria: '.$value[0]);
                    $menu[] = $botao;
                }
                $objeto->set_botaoListarExtra($menu);
            br();    
            $objeto->listar();
            break;

        case "editar" :	
        case "excluir" :	
        case "gravar" :		
            $objeto->$fase($id);		
            break;		
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("login.php");
}