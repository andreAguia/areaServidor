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

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
    
    # Pega o idServidor do usuário logado
    $idServidor = $intra->get_idServidor($idUsuario);
    
    # Pega o idServidor Pesquisado da rotina de pasta digitaliozada
    $idServidorPesquisado = get("idServidorPesquisado");
    
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
    set_session('servidorCargoComissao');
    
    # Limpa as sessions usadas servidor geral
    set_session('parametroNomeMat');
    set_session('parametroCargo');
    set_session('parametroCargoComissao');
    set_session('parametroLotacao');
    set_session('parametroPerfil');
    set_session('parametroSituacao');
    set_session('sessionParametro');
    
    $grid1 = new Grid();
    $grid1->abreColuna(12);
    
    switch ($fase){	
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

            # Cria Grid
            $grid = new Grid();
            
            # Define os tamanho do grid
            $tamanhoGrid1 = 0;
            $tamanhoGrid2 = 0;
            $tamanhoGrid3 = 0;
            $tamanhoGrid4 = 12;
            
            ### GRID 1
            # Se exibe o icone do sistema de pessoal
            if(Verifica::acesso($idUsuario,2)){
                $tamanhoGrid1 = $tamanhoGrid1+3;
            }
            
            # Se exibe o icone do sistema de processos
            if(Verifica::acesso($idUsuario,5)){
                $tamanhoGrid1 = $tamanhoGrid1+3;
            }
            
            ### GRID 2
            # O tamanho do grid 2 depende do tamanho do grid 1
            if($tamanhoGrid1 > 0){
                $tamanhoGrid2 = 12 - $tamanhoGrid1;
            }else{
                $tamanhoGrid2 = 6;
            }
            
            ### GRID 3
            # O grid 3 será na mesma linha do grid 2 se não tiver o grid 1 
            if($tamanhoGrid1 > 0){
                $tamanhoGrid3 = 12;
            }else{
                $tamanhoGrid3 = 6;
            }
            
            # Verifica se usuário tem permissão de acesso a algum sistema
            if(Verifica::acesso($idUsuario,2)){
                
                # Cria coluna para o menu de sistemas
                $grid->abreColuna($tamanhoGrid1);

                # Título
                tituloTable('Sistemas');
                br();
                
                # Inicia o menu
                $menu = new MenuGrafico();
                
                # Sistema de Pessoal
                $botao = new BotaoGrafico();
                $botao->set_label('Sistema de Pessoal');
                $botao->set_url('../../grh/grhSistema/grh.php');
                $botao->set_image(PASTA_FIGURAS.'sistemaPessoal.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Acessa o Sistema de Pessoal');
                $botao->set_accesskey('P');
                $menu->add_item($botao);
            
            
                # Sistema de Processos
                if(Verifica::acesso($idUsuario,5)){                    
                    $botao = new BotaoGrafico();
                    $botao->set_label('Sistema de Processos');
                    $botao->set_url('processo.php');
                    $botao->set_image(PASTA_FIGURAS.'processo.png',$tamanhoImage,$tamanhoImage);
                    $botao->set_title('Sistema de controle de processos');
                    $botao->set_target("_blank");
                    $menu->add_item($botao);
                }
                
                # Controle de pastas Digitalizadas
                if(Verifica::acesso($idUsuario,4)){
                    $botao = new BotaoGrafico();
                    $botao->set_label('Pastas Digitalizadas');
                    $botao->set_url('?fase=pastasDigitalizadas');
                    $botao->set_image(PASTA_FIGURAS.'pasta.png',$tamanhoImage,$tamanhoImage);
                    $botao->set_title('Sistema de controle de pastas digitalizadas');
                    $botao->set_accesskey('D');
                    $menu->add_item($botao);
                }

                $menu->show();
                br();            
                $grid->fechaColuna();
            }
        
        ################################################################
           
            $grid->abreColuna($tamanhoGrid2);
            tituloTable('Sobre o Servidor');
            br(); 

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
            br();
            $grid->fechaColuna();
            
        ################################################################

            $grid->abreColuna($tamanhoGrid3);
            tituloTable('Servidores da Universidade');
            br(); 
            
            if(Verifica::acesso($idUsuario,3)){
                $menu = new MenuGrafico(4);
                
                $botao = new BotaoGrafico();
                $botao->set_label('Geral');
                $botao->set_url('servidorGeral.php');
                $botao->set_image(PASTA_FIGURAS.'admin.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Lista geral de servidores');
                $menu->add_item($botao);
            }else{
                $menu = new MenuGrafico(3);
            }

            $botao = new BotaoGrafico();
            $botao->set_label('por Lotação');
            $botao->set_url('servidorLotacao.php');
            $botao->set_image(PASTA_FIGURAS.'computador.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Lista de servidores por lotação');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('por Cargo Efetivo');
            $botao->set_url('servidorCargo.php');
            $botao->set_image(PASTA_FIGURAS.'cracha.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Lista de servidores por cargo efetivo');
            $menu->add_item($botao);
            
            $botao = new BotaoGrafico();
            $botao->set_label('por Cargo em Comissão');
            $botao->set_url('servidorCargoComissao.php');
            $botao->set_image(PASTA_FIGURAS.'comissao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Lista de servidores por cargo em comissão');
            $menu->add_item($botao);
            $menu->show();
            br();
            $grid->fechaColuna();

        ##########################################################
        
            # links externos
            $grid->abreColuna($tamanhoGrid4);
            
            tituloTable('Links Externos');
            br(); 
            
            $menu = new MenuGrafico(4);
            $largura = 100;
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
            br();
            $grid->fechaColuna();
            $grid->fechaGrid();
            
        #########################################################

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
            $select ='(SELECT CONCAT(tbtipolicenca.nome,"<br/>",IFNULL(tbtipolicenca.lei,"")),
                                     CASE tipo
                                        WHEN 1 THEN "Inicial"
                                        WHEN 2 THEN "Prorrogação"
                                        end,
                                     CASE alta
                                        WHEN 1 THEN "Sim"
                                        WHEN 2 THEN "Não"
                                        end,
                                     dtInicial,
                                     numdias,
                                     ADDDATE(dtInicial,numDias-1),
                                     CONCAT(tblicenca.idTpLicenca,"&",idLicenca),
                                     dtPublicacao,
                                     idLicenca
                                FROM tblicenca LEFT JOIN tbtipolicenca ON tblicenca.idTpLicenca = tbtipolicenca.idTpLicenca
                               WHERE idServidor='.$idServidor.')
                               UNION
                               (SELECT (SELECT CONCAT(tbtipolicenca.nome,"<br/>",IFNULL(tbtipolicenca.lei,"")) FROM tbtipolicenca WHERE idTpLicenca = 6),
                                       "",
                                       "",
                                       dtInicial,
                                       tblicencapremio.numdias,
                                       ADDDATE(dtInicial,tblicencapremio.numDias-1),
                                       CONCAT("6&",tblicencapremio.idServidor),
                                       tbpublicacaopremio.dtPublicacao,
                                       idLicencaPremio
                                  FROM tblicencapremio LEFT JOIN tbpublicacaopremio USING (idPublicacaoPremio)
                                 WHERE tblicencapremio.idServidor = '.$idServidor.')
                              ORDER BY 4 desc';

            $result = $servidor->select($select);
            
            $tabela = new Tabela();
            $tabela->set_titulo("Histórico de Licenças");
            $tabela->set_conteudo($result);
            $tabela->set_label(array("Licença ou Afastamento","Tipo","Alta","Inicio","Dias","Término","Processo","Publicação"));
            $tabela->set_align(array("left"));
            $tabela->set_funcao(array(NULL,NULL,NULL,'date_to_php',NULL,'date_to_php','exibeProcessoPremio','date_to_php'));
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
                      
        case "pastasDigitalizadas" :
            
            if(Verifica::acesso($idUsuario,4)){
                br(4);
                aguarde("Aguarde...");
                loadPage("?fase=pastasDigitalizadas1");
            }else{
                loadPage("?");
            }    
            break;

##################################################################
                      
        case "pastasDigitalizadas1" :
            # Voltar
            $grid = new Grid();
            $grid->abreColuna(8);
            botaoVoltar('?');
            br(0);
            
            # Pega os parâmetros
            $parametro = retiraAspas(post('parametro'));
            
            # Parâmetros
            $form = new Form('?fase=pastasDigitalizadas1');

                # Pesquisa por nome
                $controle = new Input('parametro','texto','Pesquisa por nome:',1);
                $controle->set_size(55);
                $controle->set_title('Pesquisa por nome');
                $controle->set_valor($parametro);
                $controle->set_autofocus(TRUE);
                $controle->set_onChange('formPadrao.submit();');
                $controle->set_linha(1);
                $controle->set_col(10);
                $form->add_item($controle);
                
            $form->show();
            
            $grid->fechaColuna();
            $grid->abreColuna(4);
            
                # Define a pasta
                $pasta = "../../_arquivo/";
                $numPasta = 0;
                
                # Define o array da tabela
                $result = array();
                
                # Exibe um quadro com o resumo
                if(file_exists($pasta)){        // Verifica se a pasta existe
                
                    # Calcula o número de pastas no diretótio de pastas
                    $s = scandir($pasta);
                    foreach($s as $k){
                        if(($k <> ".") AND ($k <> "..")){
                            $numPasta++;
                            
                            # Divide o nome da pasta
                            $partes = explode('-',$k);
                            
                            # IdFuncional
                            $idFuncionalServ = $partes[0];
                            
                            # IdServidor
                            $idServidorServ = $servidor->get_idServidoridFuncional($idFuncionalServ);
                            
                            if(is_null($idServidorServ)){
                                $nome = "Servidor Não Encontrado";
                                $cargo = NULL;
                                $lotacao = NULL;
                                $perfil = NULL;
                                $admissao = NULL;
                            }else{
                                # Nome
                                $nome = $servidor->get_nome($idServidorServ);

                                # Cargo
                                $cargo = $servidor->get_cargo($idServidorServ);

                                # Lotação
                                $lotacao = $servidor->get_lotacao($idServidorServ);

                                # Perfil
                                $perfil = $servidor->get_perfil($idServidorServ);

                                # Admissao
                                $admissao = $servidor->get_dtAdmissao($idServidorServ);
                            }
                            
                            # verifica o parametro
                            if(vazio($parametro)){
                                $result[] = array($idFuncionalServ,$nome,$cargo,$lotacao,$perfil,$admissao,$idServidorServ);
                            }else{
                                # Conta quantas vezes o parametro aparece no nome
                                if(substr_count(strtolower(retiraAcento($nome)),strtolower(retiraAcento($parametro))) > 0){
                                    $result[] = array($idFuncionalServ,$nome,$cargo,$lotacao,$perfil,$admissao,$idServidorServ);
                                }
                            }
                        }
                    }
                }
                
                $numServidores = $servidor->get_numServidoresAtivos();
                $total = $numServidores - $numPasta;
                
                $conteudo = array(array("Quantidade",$numServidores),
                                  array("Com Pasta Digitalizada",$numPasta),
                                  array("Falta Digitalizar:",$total));
            
                $tabela1 = new Tabela();
                $tabela1->set_conteudo($conteudo);
                $tabela1->set_label(array("Servidores Ativos","Quantidade"));
                $tabela1->set_align(array("left","center"));
                $tabela1->set_totalRegistro(FALSE);
                $tabela1->set_scroll(FALSE);
                $tabela1->show();
            $grid->fechaColuna();
            $grid->fechaGrid();
            
            $tabela = new Tabela();
            $tabela->set_titulo("Servidores Com Pasta Digitalizada");
            $tabela->set_conteudo($result);
            $tabela->set_label(array("IdFuncional","Nome","Cargo","Lotação","Perfil","Admissão","Pasta"));
            #$tabela->set_label(array("IdFuncional","Nome","Cargo","Lotação","Perfil","Admissão","Pasta"));
            $tabela->set_align(array("center","left","left","left"));
            $tabela->set_funcao(array (NULL,NULL,NULL,NULL,NULL,NULL,'verificaPasta'));
            #$tabela->set_classe(array(NULL,NULL,"pessoal"));
            #$tabela->set_metodo(array(NULL,NULL,"get_Cargo"));
            if(Verifica::acesso($idUsuario,4)){
                $tabela->show();
            }else{
                loadPage("?");
            }    
            break;

##################################################################	
        
        case "pasta" :
            # Voltar
            botaoVoltar('?fase=pastasDigitalizadas');
            br(0);
            
            # Pasta Funcional
            $grid = new Grid();
            $grid->abreColuna(4);
            
            # Título
            tituloTable('Pasta Funcional');
            
            br();
                        
            # Pega o idfuncional
            $idFuncional = $servidor->get_idFuncional($idServidorPesquisado);
            
            # Define a pasta
            $pasta = "../../_arquivo/";
            
            $achei = NULL;
            
            # Encontra a pasta
            foreach (glob($pasta.$idFuncional."*") as $escolhido) {
                $achei = $escolhido;
            }
            
            # Verifica se tem pasta desse servidor
            if(file_exists($achei)){
                
                $grupoarquivo = NULL;
                $contador = 0;
                
                # Inicia o menu
                $tamanhoImage = 60;
                $menu = new MenuGrafico(1);
            
                # pasta
                $ponteiro  = opendir($achei."/");
                while ($arquivo = readdir($ponteiro)) {

                    # Desconsidera os diretorios 
                    if($arquivo == ".." || $arquivo == "."){
                        continue;
                    }
                    
                    # Verifica a codificação do nome do arquivo
                    if(codificacao($arquivo) == 'ISO-8859-1'){
                        $arquivo = utf8_encode($arquivo);
                    }

                    # Divide o nome do arquivos
                    $partesArquivo = explode('.',$arquivo);
                    
                    # Verifica se arquivo é da pasta
                    if(substr($arquivo, 0, 5) == "Pasta"){
                        $botao = new BotaoGrafico();
                        $botao->set_label($partesArquivo[0]);
                        $botao->set_url($achei.'/'.$arquivo);
                        $botao->set_target('_blank');
                        $botao->set_image(PASTA_FIGURAS.'pasta.png',$tamanhoImage,$tamanhoImage);
                        $menu->add_item($botao);
                        
                        $contador++;
                    }
                }
                if($contador >0){
                    $menu->show();
                }
            }else{                
                p("Nenhum arquivo encontrado.","center");
            }
            
            #$callout->fecha();
            $grid->fechaColuna();
            $grid->abreColuna(8);
            
            #############################################################
            
            tituloTable('Processos');
            br();
            
            # Verifica se tem pasta desse servidor
            if(file_exists($achei)){
                
                $grupoarquivo = NULL;
                 
                # Inicia o menu
                $tamanhoImage = 60;
                $menu = new MenuGrafico(4);
            
                # pasta
                $ponteiro  = opendir($achei."/");
                while ($arquivo = readdir($ponteiro)) {

                    # Desconsidera os diretorios 
                    if($arquivo == ".." || $arquivo == "."){
                        continue;
                    }

                    # Verifica a codificação do nome do arquivo
                    if(codificacao($arquivo) == 'ISO-8859-1'){
                        $arquivo = utf8_encode($arquivo);
                    }
                    
                    # Divide o nome do arquivos
                    $partesArquivo = explode('.',$arquivo);
                    
                    
                    # Verifica se arquivo é da pasta
                    if(substr($arquivo, 0, 5) <> "Pasta"){
                        $botao = new BotaoGrafico();
                        $botao->set_label($partesArquivo[0]);
                        $botao->set_url($achei.'/'.$arquivo);
                        $botao->set_target('_blank');
                        $botao->set_image(PASTA_FIGURAS.'processo.png',$tamanhoImage,$tamanhoImage);
                        $menu->add_item($botao);
                    }
                }
                $menu->show();
            }else{               
                p("Nenhum arquivo encontrado.","center");
            }
            
            #$callout->fecha();
            $grid->fechaColuna();
            $grid1->fechaGrid();
            
    #############################################################
        
    }
    
    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}

