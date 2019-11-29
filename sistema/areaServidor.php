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
    $idPerfil = $servidor->get_idPerfil($idServidor);
    
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

            # Exibe os dados do Servidor            
            Grh::listaDadosServidor($idServidor);

            #########################################################

            # Exibe o Menu
            AreaServidor::menuPrincipal($idUsuario);
            br();
            
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
                    ORDER BY anoExercicio desc, dtInicial desc';

            $result = $servidor->select($select);
            
            $tabela = new Tabela();
            $tabela->set_titulo("Histórico de Férias");
            $tabela->set_conteudo($result);
            $tabela->set_label(array("Exercicio","Status","Data Inicial","Dias","P","Data Final"));
            $tabela->set_align(array("center"));
            $tabela->set_funcao(array (NULL,NULL,'date_to_php',NULL,NULL,'date_to_php'));
            $tabela->set_classe(array(NULL,NULL,NULL,NULL,'pessoal'));
            $tabela->set_metodo(array(NULL,NULL,NULL,NULL,"get_feriasPeriodo"));
            $tabela->set_rowspan(0);
            $tabela->set_grupoCorColuna(0);
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
            $idLotacao = $servidor->get_idLotacao($idServidor);
            
            # Conecta com o banco de dados
            $servidor = new Pessoal();

            $select ="SELECT month(tbferias.dtInicial),
                         tbpessoa.nome,
                         tbservidor.idServidor,
                         tbferias.anoExercicio,
                         tbferias.dtInicial,
                         tbferias.numDias,
                         date_format(ADDDATE(tbferias.dtInicial,tbferias.numDias-1),'%d/%m/%Y') as dtf,
                         idFerias,
                         tbferias.status,
                         tbsituacao.situacao
                    FROM tbservidor LEFT JOIN tbpessoa ON (tbservidor.idPessoa = tbpessoa.idPessoa)
                                         JOIN tbhistlot ON (tbservidor.idServidor = tbhistlot.idServidor)
                                         JOIN tblotacao ON (tbhistlot.lotacao=tblotacao.idLotacao)
                                         JOIN tbferias ON (tbservidor.idServidor = tbferias.idServidor)
                                         JOIN tbsituacao ON (tbservidor.situacao = tbsituacao.idSituacao)
                   WHERE tbhistlot.data =(select max(data) from tbhistlot where tbhistlot.idServidor = tbservidor.idServidor)
                     AND YEAR(tbferias.dtInicial) = $ano
                     AND (tblotacao.idlotacao = $idLotacao)
                ORDER BY dtInicial";

            $result = $servidor->select($select);

            $tabela = new Tabela();
            $tabela->set_titulo("Férias dos Servidores da ".$servidor->get_nomeLotacao($idLotacao)." em $ano");
            $tabela->set_label(array('Mês','Nome','Lotação','Exercício','Inicio','Dias','Fim','Período','Status','Situação'));
            $tabela->set_align(array("center","left","left"));
            $tabela->set_funcao(array("get_nomeMes",NULL,NULL,NULL,"date_to_php",NULL,NULL,NULL,NULL));
            $tabela->set_classe(array(NULL,NULL,"pessoal",NULL,NULL,NULL,NULL,"pessoal"));
            $tabela->set_metodo(array(NULL,NULL,"get_lotacaoSimples",NULL,NULL,NULL,NULL,"get_feriasPeriodo"));
            $tabela->set_conteudo($result);
            $tabela->set_rowspan(0);
            $tabela->set_grupoCorColuna(0);
            $tabela->show();
            
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
            $idFuncional = intval($servidor->get_idFuncional($idServidorPesquisado));
            
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
                        $botao->set_imagem(PASTA_FIGURAS.'pasta.png',$tamanhoImage,$tamanhoImage);
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
                
                $numeroArquivos = 0;
            
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
                        $numeroArquivos++;
                        $botao = new BotaoGrafico();
                        $botao->set_label($partesArquivo[0]);
                        $botao->set_url($achei.'/'.$arquivo);
                        $botao->set_target('_blank');
                        $botao->set_imagem(PASTA_FIGURAS.'processo.png',$tamanhoImage,$tamanhoImage);
                        $menu->add_item($botao);
                    }
                }
                if($numeroArquivos>0){
                    $menu->show();
                }else{
                    br(2);
                    p("Nenhum arquivo encontrado.","center");
                }
            }else{
                br(2);
                p("Nenhum arquivo encontrado.","center");
            }
            
            #$callout->fecha();
            $grid->fechaColuna();
            $grid->fechaGrid();
            
    #############################################################
        
    }
    
    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}

