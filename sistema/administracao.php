<?php
/**
 * Administração
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
    $fase = get('fase','menu'); # Qual a fase
    $metodo = get('sistema');	# Qual o sistema. Usado na rotina de Documentação
    
    $parametroAno = post('parametroAno',get_session('parametroAno',date("Y")));
    set_session('parametroAno',$parametroAno);

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
    # Zera sessions
    set_session('categoria');   // session de pesquisa da rotina de configuraçoes
    
    # Limita o tamanho da tela
    $grid1 = new Grid();
    $grid1->abreColuna(12);
    
    switch ($fase){	
        # Exibe o Menu Inicial
        case "menu" :
            # Apaga as session do sistema de projetos e notas
            set_session('idNota');
            set_session('idCaderno');
    
            botaoVoltar('areaServidor.php');
            titulo('Administração');
            br();

            # Define o tamanho do ícone
            $tamanhoImage = 60;
            $permissao = new Intra();

            # Cria o grid
            $grid2 = new Grid();

            # Gestão de Usuários
            $grid2->abreColuna(12,12,6);
            tituloTable('Gestão de Usuários');
            br(); 

            $menu = new MenuGrafico(4);

            # Administração de Usuários
            $botao = new BotaoGrafico();
            $botao->set_label('Usuários');
            $botao->set_url('usuarios.php');
            $botao->set_imagem(PASTA_FIGURAS.'usuarios.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Gerencia os Usuários');
            $menu->add_item($botao);

            # Regras
            $botao = new BotaoGrafico();
            $botao->set_label('Regras');
            $botao->set_url('regras.php');
            $botao->set_imagem(PASTA_FIGURAS.'regras.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Cadastro de Regras');
            $menu->add_item($botao);

            # Histórico Geral
            $botao = new BotaoGrafico();
            $botao->set_label('Histórico');
            $botao->set_title('Histórico Geral do Sistema');
            $botao->set_imagem(PASTA_FIGURAS.'historico.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('historico.php');
            $menu->add_item($botao);

            # Computadores (IPs)
            $botao = new BotaoGrafico();
            $botao->set_label('Acesso ao Sistema');
            $botao->set_title('Cadastro de computadores com acesso ao sistema');
            $botao->set_imagem(PASTA_FIGURAS.'computador.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('computador.php');
            $menu->add_item($botao);
            
            $menu->show();
            br(); 
            $grid2->fechaColuna();
            
            ############################################

            # Sistema
            $grid2->abreColuna(12,12,6);
            tituloTable('Sistema');
            br(); 

            $menu = new MenuGrafico(4);

            # Variáveis de Configuração
            $botao = new BotaoGrafico();
            $botao->set_label('Configurações');
            $botao->set_url('configuracao.php');
            $botao->set_imagem(PASTA_FIGURAS.'configuracao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Edita as Variáveis de&#10;configuração da Intranet');
            $menu->add_item($botao);

            # Cadastro de Atualizações
            $botao = new BotaoGrafico();
            $botao->set_label('Atualizações');
            $botao->set_url('atualizacao.php');
            $botao->set_imagem(PASTA_FIGURAS.'atualizacao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Gerencia o cadastro de atualizações');
            $menu->add_item($botao);
            
            # Cadastro de Mensagens
            $botao = new BotaoGrafico();
            $botao->set_label('Mensagens');
            $botao->set_title('Cadastro de Mensagens');
            $botao->set_imagem(PASTA_FIGURAS.'mensagem.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_url('mensagem.php');
            $menu->add_item($botao);
            
            $menu->show();
            br();
            $grid2->fechaColuna();
            
            ############################################

            # Documentação
            $grid2->abreColuna(12,12,6);       
            tituloTable('Documentação do Sistema');
            br();

            $menu = new MenuGrafico(5);

            # Framework
            $botao = new BotaoGrafico();
            $botao->set_label('FrameWork');
            $botao->set_title('Documentação do Framework');
            $botao->set_imagem(PASTA_FIGURAS.'code.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaCodigo.php?fase=Framework');
            $menu->add_item($botao);

            # Administração
            $botao = new BotaoGrafico();
            $botao->set_label('Área do Servidor');
            $botao->set_title('Documentação da Área de Administração');
            $botao->set_imagem(PASTA_FIGURAS.'code.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaCodigo.php?fase=areaServidor');
            $menu->add_item($botao);

            # Sistema de Pessoal
            $botao = new BotaoGrafico();
            $botao->set_label('Pessoal');
            $botao->set_title('Documentação do Sistema de Pessoal');
            $botao->set_imagem(PASTA_FIGURAS.'code.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaCodigo.php?fase=Grh');
            $menu->add_item($botao);
            
            # Administração
            $botao = new BotaoGrafico();
            $botao->set_label('Área do Servidor');
            $botao->set_title('Documentação do Banco de Dados da Área do Servidor');
            $botao->set_imagem(PASTA_FIGURAS.'bdados.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaBd.php?banco=areaservidor');
            $menu->add_item($botao);

            # Sistema de Pessoal
            $botao = new BotaoGrafico();
            $botao->set_label('Pessoal');
            $botao->set_title('Documentação do Banco de Dados do Sistema de Pessoal');
            $botao->set_imagem(PASTA_FIGURAS.'bdados.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaBd.php?banco=grh');
            $menu->add_item($botao);
            $menu->show();
            br();
            
            $grid2->fechaColuna();
            
            ############################################

            # Projetos
            $grid2->abreColuna(12,12,6);
            tituloTable('Gestão de Projetos');
            br(); 

            $menu = new MenuGrafico(4);

            # Variáveis de Configuração
            $botao = new BotaoGrafico();
            $botao->set_label('Tarefas');
            $botao->set_url('projeto.php');
            $botao->set_imagem(PASTA_FIGURAS.'atribuicoes.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Sistema de gestão de tarefas');
            $botao->set_target("_blank");
            $menu->add_item($botao);

            # Cadastro de Atualizações
            $botao = new BotaoGrafico();
            $botao->set_label('Notas');
            $botao->set_url('projetoNota.php');
            $botao->set_imagem(PASTA_FIGURAS.'contratos.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Sistema de notas dos sistemas');
            $botao->set_target("_blank");
            $menu->add_item($botao);
            
            $menu->show();
            br();
            $grid2->fechaColuna();
            
            ############################################
            
            # Banco de dados
            $grid2->abreColuna(12,12,6);
            tituloTable('Feramentas para o Banco de Dados');
            br();

            $menu = new MenuGrafico(4);
            
            # Backup
            $botao = new BotaoGrafico();
            $botao->set_label('Backup');
            $botao->set_title('Acessa a área de backup');
            $botao->set_imagem(PASTA_FIGURAS.'backup.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('?fase=pastaBackup');
            $menu->add_item($botao);

            # Importação
            $botao = new BotaoGrafico();
            $botao->set_label('Importação');
            $botao->set_title('Executa a rotina de importação');
            $botao->set_imagem(PASTA_FIGURAS.'importacao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('?fase=importacao');
            $menu->add_item($botao);

            # PhpMyAdmin
            $botao = new BotaoGrafico();
            $botao->set_label('PhpMyAdmin');
            $botao->set_title('Executa o PhpMyAdmin');
            $botao->set_target('_blank');
            $botao->set_imagem(PASTA_FIGURAS.'mysql.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('http://127.0.0.1/phpmyadmin');
            $menu->add_item($botao);
            
            # Registros órfãos
            $botao = new BotaoGrafico();
            $botao->set_label('Registros Órfãos');
            $botao->set_title('Faz varredura para encontrar registros órfãos');
            $botao->set_imagem(PASTA_FIGURAS.'regOrf.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('registroOrfao.php');
            $menu->add_item($botao);
            $menu->show();
            
            br();
            $grid2->fechaColuna();
            
            ############################################
            
            # Servidor
            $grid2->abreColuna(12,12,6);
            tituloTable('Servidor');
            br();

            $menu = new MenuGrafico(4);

            # Informação do PHP
            $botao = new BotaoGrafico();
            $botao->set_label('PHP Info');
            $botao->set_title('Informações sobre&#10;a versão do PHP');
            $botao->set_imagem(PASTA_FIGURAS.'phpInfo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('phpInfo.php');
            $menu->add_item($botao);

            # Informação do Servidor Web
            $botao = new BotaoGrafico();
            $botao->set_label('Web Server');
            $botao->set_title('Informações sobre&#10;o servidor web');
            $botao->set_imagem(PASTA_FIGURAS.'webServer.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('infoWebServer.php');
            $menu->add_item($botao);
            $menu->show();
            br();
            $grid2->fechaColuna();
            
            ############################################
            
            $grid2->fechaGrid();    
            
            # Exibe o rodapé da página
            AreaServidor::rodape($idUsuario);
            break;
        
########################################################################################
        
        case "importacao" :
            botaoVoltar("administracao.php");
            titulo('Importação do banco de dados');

            # Define o tamanho do ícone
            $tamanhoImage = 60;

            #$menu = new MenuGrafico(5);
            br();
            
            # Férias
            $botao = new BotaoGrafico();
            $botao->set_label('Férias');
            $botao->set_url('importacaoFerias.php');
            $botao->set_imagem(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da Tabela de Férias do SigRH');
            #$menu->add_item($botao);
            
            # Progressão e Enquadramento
            $botao = new BotaoGrafico();
            $botao->set_label('Progressão e Enquadramento');
            $botao->set_url('importaProgressao.php');
            $botao->set_imagem(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da Tabela de Progressão e enquadramento');
            #$menu->add_item($botao);
            
            # Faltas
            $botao = new BotaoGrafico();
            $botao->set_label('Faltas');
            $botao->set_url('importacaoFaltas.php');
            $botao->set_imagem(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da Tabela de Faltas do SigRH');
            #$menu->add_item($botao);
                        
            # Contatos
            $botao = new BotaoGrafico();
            $botao->set_label('Contatos');
            $botao->set_url('?fase=contatos');
            $botao->set_imagem(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da antiga tabela de contatos');
            #$menu->add_item($botao);
            
            # sispatri
            $botao = new BotaoGrafico();
            $botao->set_label('Sispatri');
            $botao->set_url('?fase=sispatri');
            $botao->set_imagem(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Insere o idServidor na tabela do sispatri importada por Gustavo');
            #$menu->add_item($botao);
            #$menu->show();
            break;
        
########################################################################################
        
        case "backup" :
            br(4);
            aguarde();
            br();
            
            # Limita a tela
            $grid1 = new Grid("center");
            $grid1->abreColuna(5);
                p("Fazendo o backup ...","center");
            $grid1->fechaColuna();
            $grid1->fechaGrid();

            loadPage('?fase=backup2');
            break;

        case "backup2" :
            
            # Realiza o backup
            $backup = new BackupBancoDados($idUsuario);
            $backup->set_tipo(2);
            $backup->executa();

            loadPage('?fase=pastaBackup');
            break;
        
    ########################################################################################
        
        case "pastaBackup" :
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);
            
            ##############################
            
            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar",'?');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1,"left");

            # Formulário de Pesquisa
            $form = new Form('?fase=pastaBackup'); 

            # Cria um array com os anos possíveis
            $anoAtual = date('Y');
            $anoInicial = $anoAtual-1;
            $anoExercicio = array($anoInicial,$anoAtual);

            $controle = new Input('parametroAno','combo');
            $controle->set_size(50);
            $controle->set_title('Filtra por Ano exercício');
            $controle->set_array($anoExercicio);
            $controle->set_valor($parametroAno);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_id("controlePesquisa");
            $form->add_item($controle);
            $menu->add_link($form,"left");
            
            # Backup Manual
            $linkBotao2 = new Link("Backup Manual",'?fase=backup');
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Backup manual do banco de dados');            
            $menu->add_link($linkBotao2,"right");
            
            $menu->show();
            
            ##############################
            
            # Título
            tituloTable('Pasta de Backup');
            br();
            
            # Executa o backup no Linux
            #$output = shell_exec('ls ../../_backup');
            #echo "<pre>$output</pre>";
            
            # Define a pasta
            $pasta = '/var/www/html/_backup/';
            
            if(is_dir($pasta)){
             $diretorio = dir($pasta);

                # Percorre os arquivos
                while($arquivo = $diretorio->read()){
                    
                    # Retira os diretórios
                    if($arquivo <> '..' AND $arquivo <> '.'){
                        
                        # Cria um Array com todos os Arquivos encontrados
                        $arrayArquivos[] = $arquivo;
                    }
                }
                $diretorio->close();
            }
            
            # Cria títulos de ordenação
            $mes = NULL;
            $mesAnterior = NULL;

            if(isset($arrayArquivos)){
                # Limita ainda mais
                $grid = new Grid("center");
                $grid->abreColuna(12);
            
                # Classificar os arquivos para a Ordem Crescente
                sort($arrayArquivos, SORT_STRING);
                
                $grid = new Grid("center");    

                # Mostra a listagem dos Arquivos
                foreach($arrayArquivos as $valorArquivos){
                    
                    # Pega o ano do arquivo
                    $ano = substr($valorArquivos, 0,4);
                    $mes = substr($valorArquivos, 5,2);
                    $dia = substr($valorArquivos, 8,2);
                    $hora = substr($valorArquivos, 11,2);
                    $minuto = substr($valorArquivos, 14,2);
                    $segundo = substr($valorArquivos, 17,2);
                                        
                    # Compara se já teve título do mês
                    if($mes <> $mesAnterior){
                        
                        # Fecha o fieldset se não for o primeiro
                        if(!is_null($mesAnterior)){
                            $field->fecha();
                            $grid->fechaColuna();
                        }
                    
                        $mesAnterior = $mes;                                                
                        $grid->abreColuna(3);
                        
                        $field = new Fieldset(get_nomeMes($mes));
                        $field->set_class('fieldset');
                        $field->abre();
                    }
                    
                    if($ano == $parametroAno){
                        echo "<a href=/_backup/$valorArquivos>Dia $dia - $hora:$minuto:$segundo</a><br />";
                    }
                }
                
                $grid->fechaColuna();
                $grid->fechaGrid();
                
            }else{
                br(3);
                p("Não existe nenhum arquivo de backup!","center");
            }

            $grid->fechaColuna();
            $grid->fechaGrid();
            
            break;
        
    ########################################################################################
        
        case "contatos" :
            titulo('Importação dos contatos');
            
            br(4);
            aguarde('Importando ...');
            
            $select = 'SELECT idPessoa
                         FROM tbpessoa
                     ORDER BY 1 desc';
                    
            $row = $servidor->select($select);
            
            foreach ($row as $tt){
                # Pega os contatos antigos
                $contatos = importaContatos($tt[0]);
                
                echo "idPessoa: ".$tt[0];
                
                # Grava na tabela tbpessoa
                $campos = array("telResidencial","telCelular","telRecados","emailUenf","emailPessoal");
                $valor = array($contatos[0],$contatos[1],$contatos[2],$contatos[3],$contatos[4]);                    
                $servidor->gravar($campos,$valor,$tt[0],"tbpessoa","idPessoa");
            }
            loadPage("?");
            break;
            
    ########################################################################################
        
        case "sispatri" :
            botaoVoltar("?fase=importacao");
            titulo('Insere o idServidor na Tabela do Sispatri');
            
            br();            
            $select = 'SELECT idSispatri,
                              nome,
                              cpf
                         FROM tbsispatri
                     ORDER BY nome';
                    
            $row = $servidor->select($select);
            
            echo "<table>";
            
            echo "<tr>";
            echo "<th>idSispatri</th>";
            echo "<th>Nome</th>";
            echo "<th>CPF</th>";
            echo "<th>CPF Tratado</th>";
            echo "<th>idServidor</th>";
            
            echo "</tr>";
            
            $contador = 0;
                        
            foreach ($row as $tt){
                
                echo "<tr>";
                echo "<td>$tt[0]</td>";
                echo "<td>$tt[1]</td>";
                echo "<td>$tt[2]</td>";
                
                $novoCpf = $tt[2];
                $len = strlen($novoCpf);
                
                $novoCpf = str_pad($novoCpf, 11 , "0", STR_PAD_LEFT);
                
                # CPF XXX.XXX.XXX-XX
                
                $parte1 = substr($novoCpf, 0,3);
                $parte2 = substr($novoCpf, 3,3);
                $parte3 = substr($novoCpf, 6,3);
                $parte4 = substr($novoCpf, -2);
                
                $cpfFinalizado = "$parte1.$parte2.$parte3-$parte4";
                
                $select2 = "SELECT idPessoa
                              FROM tbdocumentacao
                             WHERE CPF = '$cpfFinalizado'";
                    
                $row2 = $servidor->select($select2,FALSE);
                
                if(is_null($row2[0])){
                    echo "<td></td>";
                    echo "<td></td>";
                }else{
                    echo "<td>$cpfFinalizado</td>";
                    $idServidorPesquisado = $servidor->get_idServidoridPessoa($row2[0]);
                    echo "<td>".$idServidorPesquisado."</td>";
                    
                    # Grava na tabela tbpessoa
                    $campos = array("idServidor");
                    $valor = array($idServidorPesquisado);                    
                    $servidor->gravar($campos,$valor,$tt[0],"tbsispatri","idSispatri");
                }
                
                echo "</tr>";
            }
            
            echo "</table>";
            #loadPage("?");
            break;
        
    }
    $grid1->fechaColuna();
    $grid1->fechaGrid();    
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}