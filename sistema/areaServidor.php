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
    
    # Pega o idServidor do usuário logado
    $idServidor = $intra->get_idServidor($idUsuario);

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
            Grh::listaDadosServidor($idServidor);

            ################################################################

            $grid = new Grid();
            $grid->abreColuna(4);

            $fieldset = new Fieldset('Sistemas');
            $fieldset->abre();

            $menu = new MenuGrafico();

            # Sistema de Pessoal
            if(Verifica::acesso($idUsuario,2)){
                $botao = new BotaoGrafico();
                $botao->set_label('Sistema de Pessoal');
                $botao->set_url('../../grh/grhSistema/grh.php');
                $botao->set_image(PASTA_FIGURAS.'sistemaPessoal.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Acessa o Sistema de Pessoal');
                $botao->set_accesskey('P');
                $menu->add_item($botao);
            }
            
            # Sistema de Contratos
            if(Verifica::acesso($idUsuario,1)){
                $botao = new BotaoGrafico();
                $botao->set_label('Sistema de Contratos');
                $botao->set_url('sistemaContratos.php');
                $botao->set_image(PASTA_FIGURAS.'contratos.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Sistema de Gestão de Contratos');
                $botao->set_accesskey('C');
                #$menu->add_item($botao);
            }

            $menu->show();

            $fieldset->fecha();
            $grid->fechaColuna();

            ################################################################

            $grid->abreColuna(3);

            $fieldset = new Fieldset('Serviços');
            $fieldset->abre();

            $menu = new MenuGrafico();

            # Solicitação de Férias
            $botao = new BotaoGrafico();
            $botao->set_label('Solicitação de Férias');
            $botao->set_url('solicitaFerias.php');
            $botao->set_image(PASTA_FIGURAS.'ferias.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Rotina de Solicitação de Férias');
            $botao->set_accesskey('F');
            $menu->add_item($botao);

            if(Verifica::acesso($idUsuario,1)){
                $menu->show();
            }else{
                br();
                p("Área em contrução. Aguarde.","center");
                br(); 
            }

            $fieldset->fecha();
            $grid->fechaColuna();
            
        ##########################################################

            $grid->abreColuna(5);
            $fieldset = new Fieldset('Sobre o Servidor');
            $fieldset->abre();

            $menu = new MenuGrafico();

            $botao = new BotaoGrafico();
            $botao->set_label('Histórico de Licença');
            $botao->set_url('?fase=historicoLicenca');
            $botao->set_image(PASTA_FIGURAS.'licenca.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Exibe o seu histórico de licenças e afastamentos');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Histórico de Férias');
            $botao->set_url('?fase=historicoFerias');
            $botao->set_image(PASTA_FIGURAS.'ferias.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Exibe o seu histórico de férias');
            $menu->add_item($botao);
            
            $botao = new BotaoGrafico();
            $botao->set_label('Férias do seu Setor');
            $botao->set_url('?fase=feriasSetor');
            $botao->set_image(PASTA_FIGURAS.'feriasSetor.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Exibe as férias dos servidores do seu setor');
            $menu->add_item($botao);

            $menu->show();

            $fieldset->fecha();
            $grid->fechaColuna();
            
        ################################################################

            $grid->abreColuna(12);
            $fieldset = new Fieldset('Sobre a Universidade');
            $fieldset->abre();

            $menu = new MenuGrafico(4);

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

        ##########################################################
        
            # links externos
            $grid->abreColuna(12);
            
            $fieldset = new Fieldset('Links Externos');
            $fieldset->abre();
            
            $menu = new MenuGrafico(4);
            $largura = 120;
            $altura = 50;

            $botao = new BotaoGrafico();
            #$botao->set_label(SISTEMA_GRH);
            $botao->set_title('Portal do Sistema Integrado de Gestao de Recursos Humanos do Estado do Rio de Janeiro');
            $botao->set_image(PASTA_FIGURAS."sigrh.png",$largura,$altura);      
            $botao->set_url("http://www.entradasigrhn.rj.gov.br/");
            #$menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label("");
            $botao->set_image(PASTA_FIGURAS."do.png",$largura,$altura);  
            $botao->set_url("http://www.imprensaoficial.rj.gov.br/portal/modules/profile/user.php?xoops_redirect=/portal/modules/content/index.php?id=21");
            $botao->set_title("Imprensa Oficial do Estado do Rio de Janeiro");
            $menu->add_item($botao);
            
            $botao = new BotaoGrafico();
            #$botao->set_label(SISTEMA_GRH);
            $botao->set_title('Portal do Processo Digital');
            $botao->set_image(PASTA_FIGURAS."processoDigital.png",$largura,$altura);     
            $botao->set_url("https://www.processodigital.rj.gov.br/");
            $menu->add_item($botao);
            
            $botao = new BotaoGrafico();
            #$botao->set_label(SISTEMA_GRH);
            $botao->set_title('Site da UENF');
            $botao->set_image(PASTA_FIGURAS."uenf.jpg",$largura,$altura);       
            $botao->set_url("http://www.uenf.br/portal/index.php/br/");
            $menu->add_item($botao);
            
            $botao = new BotaoGrafico();
            #$botao->set_label(SISTEMA_GRH);
            $botao->set_title('Site da GRH');
            $botao->set_image(PASTA_FIGURAS."GRH.png",$largura,$altura);  
            $botao->set_url("http://uenf.br/dga/grh/");
            $menu->add_item($botao);

            $menu->show();
        
            $fieldset->fecha();
            $grid->fechaColuna();
        
        ################################################################

            # Exibe o rodapé da página
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
        
        case "historicoFerias" :
            botaoVoltar('?');
            
            # Exibe os dados do Servidor            
            Grh::listaDadosServidor($idServidor);
            
            # Pega os dados
            $select ='SELECT anoExercicio,
                             status,
                             dtInicial,
                             numDias,
                             idFerias,
                             ADDDATE(dtInicial,numDias-1)
                        FROM tbferias
                       WHERE idServidor = '.$idServidor.'
                    ORDER BY dtInicial desc';

            $result = $servidor->select($select);
            
            $tabela = new Tabela();
            $tabela->set_titulo("Histórico de Férias");
            $tabela->set_conteudo($result);
            $tabela->set_label(array("Exercicio","Status","Data Inicial","Dias","P","Data Final"));
            $tabela->set_align(array("center"));
            $tabela->set_funcao(array (NULL,NULL,'date_to_php',NULL,NULL,'date_to_php'));
            $tabela->set_classe(array(NULL,NULL,NULL,NULL,'pessoal'));
            $tabela->set_metodo(array(NULL,NULL,NULL,NULL,"get_feriasPeriodo"));
            $tabela->show();
            
            # Grava no log a atividade
            $atividade = 'Visualizou o próprio histórico de férias na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario,$data,$atividade,NULL,NULL,7);
            break;

##################################################################
        
        case "historicoLicenca" :
            botaoVoltar('?');
            
            # Exibe os dados do Servidor            
            Grh::listaDadosServidor($idServidor);
            
            # Pega os dados
            $select ='SELECT CONCAT(tbtipolicenca.nome,"@",IFNULL(lei,"")),
                            CASE tipo
                               WHEN 1 THEN "Inicial"
                               WHEN 2 THEN "Prorrogação"
                               end,
                            IF(alta = 1,"Alta",NULL),
                            dtInicial,
                            numdias,
                            ADDDATE(dtInicial,numDias-1),
                            tblicenca.processo,
                            dtInicioPeriodo,
                            dtFimPeriodo,
                            dtPublicacao
                       FROM tblicenca LEFT JOIN tbtipolicenca ON tblicenca.idTpLicenca = tbtipolicenca.idTpLicenca
                      WHERE idServidor='.$idServidor.'
                   ORDER BY tblicenca.dtInicial desc';

            $result = $servidor->select($select);
            
            $tabela = new Tabela();
            $tabela->set_titulo("Histórico de Licenças");
            $tabela->set_conteudo($result);
            $tabela->set_label(array("Licença ou Afastamento","Tipo","Alta","Inicio","Dias","Término","Processo","P.Aq. Início","P.Aq. Término","Publicação"));
            $tabela->set_align(array("left"));
            $tabela->set_funcao(array("exibeLeiLicenca",NULL,NULL,'date_to_php',NULL,'date_to_php',NULL,'date_to_php','date_to_php','date_to_php'));
            $tabela->show();
            
            # Grava no log a atividade
            $atividade = 'Visualizou o próprio histórico de Licença na área do servidor';
            $Objetolog = new Intra();
            $data = date("Y-m-d H:i:s");
            $Objetolog->registraLog($idUsuario,$data,$atividade,NULL,NULL,7);
            break;

##################################################################
        
        case "feriasSetor" :
            botaoVoltar('?');
            
            # Exibe os dados do Servidor            
            Grh::listaDadosServidor($idServidor);
            
            # Pega o ano
            $ano = date("Y");
            
            # Pega a Lotação atual do usuário
            $idLotacao = $servidor->get_idlotacao($idServidor);
            
            $lista1 = new ListaFerias($ano);
            $lista1->set_lotacao($idLotacao);
            $lista1->showPorSolicitacao("Férias de $ano dos Servidores da ".$servidor->get_nomeLotacao($idLotacao));
            
            # Grava no log a atividade
            $atividade = 'Visualizou os servidores em férias do próprio setor na área do servidor';
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
            $painel3 = new Callout();
            $painel3->set_title('Alterações');
            $painel3->abre();
            
            # Percorre os dados
            foreach ($atualizacoes as $valor) {
                $grid2 = new Grid("center");
                $grid2->abreColuna(6);
                    p("Versão:".$valor[0],"f14");
                $grid2->fechaColuna();
                $grid2->abreColuna(6);
                    p(date_to_php($valor[1]),"right","f10"); 
                $grid2->fechaColuna();
                $grid2->fechaGrid();
                
                p("<pre>".$valor[2]."</pre>");
                #hr();
             }
                
            $painel3 ->fecha();
           
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

