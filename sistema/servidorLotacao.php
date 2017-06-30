<?php
/**
 * Servidores por Lotação
 *  
 * By Alat
 */

# Reservado para o servidor logado
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Verifica se o usuário está logado
$acesso = Verifica::acesso($idUsuario);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $pessoal = new Pessoal();
    
    # Pega o idServidor desse usuário
    $idServidor = $intra->get_idServidor($idUsuario);
    
    # Pega a Lotação atual do usuário
    $idLotacao = $pessoal->get_idlotacao($idServidor);
	
    # Verifica a fase do programa
    $fase = get('fase');

    # pega o id (se tiver)
    $id = soNumeros(get('id'));
    
    # Pega os parâmetros
    $parametroLotacao = post('parametroLotacao',get_session('servidorLotacao',$idLotacao));
    
    # Agrupamento do Relatório
    $agrupamentoEscolhido = post('agrupamento',0);
    
    # Session do Relatório
    $select = get_session('sessionSelect');
    $titulo = get_session('sessionTitulo');
    $subTitulo = get_session('sessionSubTitulo');
        
    # Joga os parâmetros par as sessions
    set_session('servidorLotacao',$parametroLotacao);

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho da Página
    if($fase <> "relatorio"){
        AreaServidor::cabecalho();
    }
    
    ################################################################
    
    switch ($fase)
    {
        # Lista os Servidores
        case "" :
            br(10);
            aguarde();
            br();
            loadPage('?fase=pesquisar');
            break;
        
        case "pesquisar" :
            # Cadastro de Servidores 
            $grid = new Grid();
            $grid->abreColuna(12);

            # Cria um menu
            $menu1 = new MenuBar();

            # Voltar
            $linkBotao1 = new Link("Voltar","areaServidor.php");
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Voltar a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu1->add_link($linkBotao1,"left");
            
            # Relatórios
            $imagem = new Imagem(PASTA_FIGURAS.'print.png',NULL,15,15);
            $botaoRel = new Button();
            $botaoRel->set_title("Relatório dessa pesquisa");
            $botaoRel->set_onClick("window.open('?fase=relatorio','_blank','menubar=no,scrollbars=yes,location=no,directories=no,status=no,width=750,height=600');");
            $botaoRel->set_imagem($imagem);
            $menu1->add_link($botaoRel,"right");
            $menu1->show();

            # Parâmetros
            $form = new Form('servidorLotacao.php');
            
                # Lotação
                $result = $pessoal->select('SELECT idlotacao, concat(IFNULL(tblotacao.DIR,"")," - ",IFNULL(tblotacao.GER,"")," - ",IFNULL(tblotacao.nome,"")) lotacao
                                              FROM tblotacao
                                             WHERE ativo
                                          ORDER BY lotacao');

                $controle = new Input('parametroLotacao','combo','Lotação:',1);
                $controle->set_size(30);
                $controle->set_title('Filtra por Lotação');
                $controle->set_array($result);
                $controle->set_valor($parametroLotacao);
                $controle->set_onChange('formPadrao.submit();');
                $controle->set_linha(1);
                $controle->set_col(12);
                $form->add_item($controle);
                $form->show();

                # Lista de Servidores Ativos
                $lista = new listaServidores('Servidores por Lotação');
                
                # Somente servidores ativos
                $lista->set_situacao(1);
                
                if($parametroLotacao <> "*"){
                    $lista->set_lotacao($parametroLotacao);
                }
                
                # Edição
                $lista->set_permiteEditar(FALSE);
                $lista->showTabela();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;
        
        ###############################

        # Cria um relatório com a seleção atual
        case "relatorio" :
            # Lista de Servidores Ativos
            $lista = new listaServidores('Servidores');

            if($parametroLotacao <> "*"){
                $lista->set_lotacao($parametroLotacao);
            }

            # Somente servidores ativos
            $lista->set_situacao(1);
            
            $lista->showRelatorio();
            break; 
    }
    $page->terminaPagina();
}else{
    loadPage("../../areaServidor/sistema/login.php");
}