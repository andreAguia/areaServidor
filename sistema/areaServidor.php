<?php
/**
 * Área do Servidor
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase','menu'); # Qual a fase

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
    # Limpa as sessions usadas nos sistemas e módulos
    set_session('servidorCargo');
    set_session('feriasAnoExercicio');
    set_session('feriasLotacao');
    set_session('servidorLotacao');
    
    $grid = new Grid();
    $grid->abreColuna(12);
    
    switch ($fase)
    {	
        # Exibe o Menu Inicial
        case "menu" :
    
            # Cria um menu
            $menu1 = new MenuBar();

            # Sair da Área do Servidor
            $linkVoltar = new Link("Sair","login.php");
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Sair do Sistema');
            $linkVoltar->set_confirma('Tem certeza que deseja sair do sistema?');
            $menu1->add_link($linkVoltar,"left");

            # Administração do Sistema
            if(Verifica::acesso($idUsuario,1)){   // Somente Administradores
                $linkAdm = new Link("Administração","administracao.php");
                $linkAdm->set_class('button success');
                $linkAdm->set_title('Administração dos Sistemas');
                $menu1->add_link($linkAdm,"right");
            }

            # Alterar Senha
            $linkSenha = new Link("Alterar Senha","trocarSenha.php");
            $linkSenha->set_class('button');
            $linkSenha->set_title('Altera a senha do usuário logado');
            $menu1->add_link($linkSenha,"right");
            
            # Sobre
            $linkSobre = new Link("Sobre","?fase=sobre");
            $linkSobre->set_class('button');
            $linkSobre->set_title('Exibe informações do Sistema');
            $menu1->add_link($linkSobre,"right");

            $menu1->show();

            titulo('Área do Servidor');
            $tamanhoImage = 64;

            # Exibe os dados do Servidor
            Grh::listaDadosServidor($intra->get_idServidor($idUsuario));

            ################################################################

            $grid = new Grid();
            $grid->abreColuna(6);

            $fieldset = new Fieldset('Sistemas');
            $fieldset->abre();

            $menu = new MenuGrafico();

            if(Verifica::acesso($idUsuario,2)){   // Verifica acesso ao sistema
                $botao = new BotaoGrafico();
                $botao->set_label('Sistema de Pessoal');
                $botao->set_url('../../grh/grhSistema/grh.php');
                $botao->set_image(PASTA_FIGURAS.'sistemaPessoal.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Acessa o Sistema de Pessoal');
                $botao->set_accesskey('P');
                $menu->add_item($botao);
            }

            if(Verifica::acesso($idUsuario,3)){   // Acesso ao sistema de férias
                $botao = new BotaoGrafico();
                $botao->set_label('Sistema de Férias');
                $botao->set_url('sistemaFerias.php');
                $botao->set_image(PASTA_FIGURAS.'ferias.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Sistema de Controle da Solicitação de Férias');
                $botao->set_accesskey('F');
                $menu->add_item($botao);
            }

            $menu->show();

            $fieldset->fecha();
            $grid->fechaColuna();

            ################################################################

            $grid->abreColuna(6);
            $fieldset = new Fieldset('Listagem de Servidores');
            $fieldset->abre();

            $menu = new MenuGrafico();

            $botao = new BotaoGrafico();
            $botao->set_label('Servidores por Lotação');
            $botao->set_url('servidorLotacao.php');
            $botao->set_image(PASTA_FIGURAS.'servidores.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Lista de Servidores por Lotação');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Servidores por Cargo');
            $botao->set_url('servidorCargo.php');
            $botao->set_image(PASTA_FIGURAS.'cracha.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Lista de Servidores por Lotação');
            $menu->add_item($botao);

            $menu->show();

            $fieldset->fecha();
            $grid->fechaColuna();

            ################################################################

            $grid->abreColuna(6);
            $fieldset = new Fieldset('Informações Gerais');
            $fieldset->abre();

            $menu = new MenuGrafico();

            $botao = new BotaoGrafico();
            $botao->set_label('Cargos em Comissão');
            $botao->set_url('?fase=cargoComissao');
            $botao->set_image(PASTA_FIGURAS.'comissao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Lista de Servidores por Lotação');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Organograma da UENF');
            $botao->set_url('?fase=organograma');
            $botao->set_image(PASTA_FIGURAS.'diagrama.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Lista de Servidores por Lotação');
            $menu->add_item($botao);

            $menu->show();

            $fieldset->fecha();
            $grid->fechaColuna();

            $grid->fechaGrid();

            ################################################################

            # Exibe o rodapé da página
            br();
            AreaServidor::rodape($idUsuario);
            break;

##################################################################
            
        case "organograma" :
            botaoVoltar('?');
            titulo("Organograma da UENF");
            br();
            $figura = new Imagem(PASTA_FIGURAS_GRH.'organograma.png','Organograma da UENF','100%','100%');
            $figura->show();

            # Grava no log a atividade
            $atividade = 'Visualizou o organograma da Uenf na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario,$data,$atividade,NULL,NULL,7);
            break;

##################################################################
        
        case "cargoComissao" :
            botaoVoltar('?');
            
            # Pega os dados
            $select ='SELECT descricao,
                             simbolo,
                             valsal,
                             vagas,
                             idTipoComissao,
                             idTipoComissao
                        FROM tbtipocomissao
                       WHERE ativo
                    ORDER BY simbolo asc';

            $result = $servidor->select($select);
            
            $tabela = new Tabela();
            $tabela->set_titulo("Cargos em Comissão");
            $tabela->set_conteudo($result);
            $tabela->set_label(array("Cargo","Simbolo","Valor (R$)","Vagas","Servidores Nomeados","Vagas Disponíveis"));
            #$tabela->set_width(array(80,10,10));
            $tabela->set_align(array("left","center","center"));
            $tabela->set_funcao(array(NULL,NULL,"formataMoeda"));
            $tabela->set_classe(array(NULL,NULL,NULL,NULL,'pessoal','pessoal'));
            $tabela->set_metodo(array(NULL,NULL,NULL,NULL,'get_servidoresCargoComissao','get_cargoComissaoVagasDisponiveis'));
            $tabela->show();
            
            # Grava no log a atividade
            $atividade = 'Visualizou os cargos em comissão na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario,$data,$atividade,NULL,NULL,7);
            break;

##################################################################
        
        case "sobre" :

            # Limita o tamanho da tela
            br(3);
            $grid = new Grid("center");
            $grid->abreColuna(6);
            
            # Cria um menu
            $menu2 = new MenuBar();
            
            $painel2 = new Callout();
            $painel2->set_title('Sobre o Sistema');
            #$painel2->set_botaoFechar(TRUE);
            $painel2->abre();
            
            br();
            p(SISTEMA,'grhTitulo');
            p('Versão: '.VERSAO.'<br/>Atualizado em: '.ATUALIZACAO,'versao');

            br();
            p('Desenvolvedor: '.AUTOR,'versao');
            p(EMAILAUTOR,'versao');
            
            # detalhes
            $linkFecha = new Link("Detalhes","?fase=atualizacoes");
            $linkFecha->set_class('button');
            $linkFecha->set_title('Exibe os detalhes das atualizações');
            $menu2->add_link($linkFecha,"left");
            
            # ok
            $linkFecha = new Link("Ok","?");
            $linkFecha->set_class('button');
            $linkFecha->set_title('fecha esta janela');
            $menu2->add_link($linkFecha,"right");
            $menu2->show();
            
            $painel2 ->fecha();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;
            
##################################################################
        
        case "atualizacoes" :
            
            # Limita a tela
            $grid = new Grid();
            $grid->abreColuna(12);
            
            # botão voltar
            botaoVoltar("?","Voltar","Volta ao Menu principal");
            
            # Título
            titulo("Detalhes das Atualizações");
            #p("Detalhes das Atualizações","center","f16");
            br();
            
            # Limita ainda mais a tela paara o painel
            $grid = new Grid("center");
            $grid->abreColuna(11);
            
            # Pega os dados 
            $atualizacoes = $intra->get_atualizacoes();
            
            # Percorre os dados
            foreach ($atualizacoes as $valor) {
                $painel3 = new Callout();
                $painel3->set_title('Alterações');
                $painel3->abre();
                
                p("Versão:".$valor[0],"f16");
                p(date_to_php($valor[1]),"right","f10");                
                p("<pre>".$valor[2]."</pre>");
                #hr();
                
                $painel3 ->fecha();
            }
            
            
            $grid->fechaColuna();
            $grid->fechaGrid();
            
            $grid->fechaColuna();
            $grid->fechaGrid();
            break;
        
##################################################################
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}

