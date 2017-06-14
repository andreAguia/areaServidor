<?php
/**
 * Sistema de Férias
 * 
 * Oferece uma interface ao usuário gerenciar as férias de uma determinada lotação
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,3);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $pessoal = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase');
    
    # pega o id (se tiver)
    $id = soNumeros(get('id'));
    
    # Pega os parâmetros
    $parametroAnoExercicio = post('parametroAnoExercicio',get_session('parametroAnoExercicio',date("Y")));
    $parametroLotacao = post('parametroLotacao',get_session('parametroLotacao'));
    
    # Joga os parâmetros par as sessions    
    set_session('parametroAnoExercicio',$parametroAnoExercicio);
    set_session('parametroLotacao',$parametroLotacao);
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
    # Limita o tamanho da tela
    $grid1 = new Grid();
    $grid1->abreColuna(12);
    
    # Cria um menu
    $menu1 = new MenuBar();

    # Voltar
    $botaoVoltar = new Link("Voltar","areaServidor.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Voltar a página anterior');
    $botaoVoltar->set_accessKey('V');
    $menu1->add_link($botaoVoltar,"left");

    $menu1->show();
    
    # Título
    titulo("Sistema de Gestão de Férias");
    
    ################################################################
    
    # Formulário de Pesquisa
    $form = new Form('?fase='.$fase);

    # anoExercicio                
    $anoExercicio = $pessoal->select('SELECT DISTINCT anoExercicio, anoExercicio FROM tbferias ORDER BY 1');
    
    # Verifica se existe o ano atual na combo e acrescenta caso não tenha
    if($anoExercicio[count($anoExercicio)-1][0] < date("Y")){
        array_push($anoExercicio,date("Y"));
    }

    $controle = new Input('parametroAnoExercicio','combo','Ano Exercício:',1);
    $controle->set_size(8);
    $controle->set_title('Filtra por Ano exercício');
    $controle->set_array($anoExercicio);
    $controle->set_valor(date("Y"));
    $controle->set_valor($parametroAnoExercicio);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(3);
    $form->add_item($controle);

    # Lotação
    $result = $pessoal->select('SELECT areaServidor.tblotacaoFerias.idlotacao, 
                                       concat(IFNULL(tblotacao.DIR,"")," - ",IFNULL(tblotacao.GER,"")," - ",IFNULL(tblotacao.nome,"")) lotacao
                                  FROM areaServidor.tblotacaoFerias LEFT JOIN grh.tblotacao USING (idlotacao)
                                 WHERE areaServidor.tblotacaoFerias.idUsuario = '.$idUsuario.' 
                              ORDER BY ativo desc,lotacao');
    
    $controle = new Input('parametroLotacao','combo','Lotação:',1);
    $controle->set_size(30);
    $controle->set_title('Filtra por Lotação');
    $controle->set_array($result);
    $controle->set_valor($parametroLotacao);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_linha(1);
    $controle->set_col(9);
    $form->add_item($controle);

    $form->show();
            
    ################################################################
    
    switch ($fase)
    {
        case "" :
            # lateral
            $grid2 = new Grid();
            $grid2->abreColuna(3);
            
            # por dias
            $lista1 = new listaFerias($parametroAnoExercicio);
            $lista1->set_lotacao($parametroLotacao);
            $lista1->showResumo();
            
            # por status
            $lista1 = new listaFerias($parametroAnoExercicio);
            $lista1->set_lotacao($parametroLotacao);
            $lista1->showResumoStatus();
            
            #######################################
            
            # Área Principal            
            $grid2->fechaColuna();
            $grid2->abreColuna(9);
            
            $lista1->showResumoServidor();
            $lista1->showDetalheServidor();
                        
            $grid2->fechaColuna();
            $grid2->fechaGrid();
            break;
        
    }

    $page->terminaPagina();
}else{
    loadPage("login.php");
}