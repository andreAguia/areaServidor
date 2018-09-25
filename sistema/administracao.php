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

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
    # Limita o tamanho da tela
    $grid1 = new Grid();
    $grid1->abreColuna(12);
    
    switch ($fase){	
        # Exibe o Menu Inicial
        case "menu" :
            botaoVoltar('areaServidor.php');
            titulo('Administração');
            br();

            # Define o tamanho do ícone
            $tamanhoImage = 60;
            $permissao = new Intra();

            # Cria o grid
            $grid2 = new Grid();

            # Gestão de Usuários
            $grid2->abreColuna(12,6);
            tituloTable('Gestão de Usuários');
            br(); 

            $menu = new MenuGrafico(4);

            # Administração de Usuários
            $botao = new BotaoGrafico();
            $botao->set_label('Usuários');
            $botao->set_url('usuarios.php');
            $botao->set_image(PASTA_FIGURAS.'usuarios.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Gerencia os Usuários');
            $menu->add_item($botao);

            # Regras
            $botao = new BotaoGrafico();
            $botao->set_label('Regras');
            $botao->set_url('regras.php');
            $botao->set_image(PASTA_FIGURAS.'regras.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Cadastro de Regras');
            $menu->add_item($botao);

            # Histórico Geral
            $botao = new BotaoGrafico();
            $botao->set_label('Histórico');
            $botao->set_title('Histórico Geral do Sistema');
            $botao->set_image(PASTA_FIGURAS.'historico.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('historico.php');
            $menu->add_item($botao);

            # Computadores (IPs)
            $botao = new BotaoGrafico();
            $botao->set_label('Acesso ao Sistema');
            $botao->set_title('Cadastro de computadores com acesso ao sistema');
            $botao->set_image(PASTA_FIGURAS.'computador.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('computador.php');
            $menu->add_item($botao);
            $menu->show();
            br();
            $grid2->fechaColuna();
            
            ############################################

            # Documentação
            $grid2->abreColuna(12,6);            
            tituloTable('Documentação do Sistema');
            
            $fieldset = new Fieldset('Codigo');
            $fieldset->abre();

            $menu = new MenuGrafico(4);

            # Framework
            $botao = new BotaoGrafico();
            $botao->set_label('FrameWork');
            $botao->set_title('Documentação do Framework');
            $botao->set_image(PASTA_FIGURAS.'code.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaCodigo.php?fase=Framework');
            $menu->add_item($botao);

            # Administração
            $botao = new BotaoGrafico();
            $botao->set_label('Área do Servidor');
            $botao->set_title('Documentação da Área de Administração');
            $botao->set_image(PASTA_FIGURAS.'code.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaCodigo.php?fase=areaServidor');
            $menu->add_item($botao);

            # Sistema de Pessoal
            $botao = new BotaoGrafico();
            $botao->set_label('Pessoal');
            $botao->set_title('Documentação do Sistema de Pessoal');
            $botao->set_image(PASTA_FIGURAS.'code.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaCodigo.php?fase=Grh');
            $menu->add_item($botao);
            $menu->show();
            
            $fieldset->fecha();
            
            $grid2->fechaColuna();
            
            ############################################

            # Sistema
            $grid2->abreColuna(12,6);
            tituloTable('Sistema');
            br(); 

            $menu = new MenuGrafico(4);

            # Variáveis de Configuração
            $botao = new BotaoGrafico();
            $botao->set_label('Configurações');
            $botao->set_url('configuracao.php');
            $botao->set_image(PASTA_FIGURAS.'configuracao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Edita as Variáveis de&#10;configuração da Intranet');
            $menu->add_item($botao);

            # Cadastro de Atualizações
            $botao = new BotaoGrafico();
            $botao->set_label('Atualizações');
            $botao->set_url('atualizacao.php');
            $botao->set_image(PASTA_FIGURAS.'atualizacao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Gerencia o cadastro de atualizações');
            $menu->add_item($botao);
            
            # Cadastro de Mensagens
            $botao = new BotaoGrafico();
            $botao->set_label('Mensagens');
            $botao->set_title('Cadastro de Mensagens');
            $botao->set_image(PASTA_FIGURAS.'mensagem.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_url('mensagem.php');
            $menu->add_item($botao);
            
            # Cadastro de Mensagens
            $botao = new BotaoGrafico();
            $botao->set_label('Projeto');
            $botao->set_title('Gestão de Projetos');
            $botao->set_image(PASTA_FIGURAS.'projeto.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('projeto.php');
            $menu->add_item($botao);
            
            $menu->show();
            br();
            $grid2->fechaColuna();
            
            ############################################

            # Servidor
            $grid2->abreColuna(12,6);
            $fieldset = new Fieldset('Banco de Dados');
            $fieldset->abre();
            
            $menu = new MenuGrafico(4);

            # Administração
            $botao = new BotaoGrafico();
            $botao->set_label('Área do Servidor');
            $botao->set_title('Documentação do Banco de Dados da Área do Servidor');
            $botao->set_image(PASTA_FIGURAS.'bdados.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaBd.php?banco=areaservidor');
            $menu->add_item($botao);

            # Sistema de Pessoal
            $botao = new BotaoGrafico();
            $botao->set_label('Pessoal');
            $botao->set_title('Documentação do Banco de Dados do Sistema de Pessoal');
            $botao->set_image(PASTA_FIGURAS.'bdados.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaBd.php?banco=grh');
            $menu->add_item($botao);
            $menu->show();
            
            $fieldset->fecha();
            $grid2->fechaColuna();
            
            ############################################

            # Banco de dados
            $grid2->abreColuna(12,6);
            tituloTable('Feramentas para o Banco de Dados');
            br();

            $menu = new MenuGrafico(4);

            # Importação
            $botao = new BotaoGrafico();
            $botao->set_label('Importação');
            $botao->set_title('Executa a rotina de importação');
            $botao->set_image(PASTA_FIGURAS.'importacao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('?fase=importacao');
            $menu->add_item($botao);

            # PhpMyAdmin
            $botao = new BotaoGrafico();
            $botao->set_label('PhpMyAdmin');
            $botao->set_title('Executa o PhpMyAdmin');
            $botao->set_target('_blank');
            $botao->set_image(PASTA_FIGURAS.'mysql.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('http://127.0.0.1/phpmyadmin');
            $menu->add_item($botao);

            # Backup Manual
            $botao = new BotaoGrafico();
            $botao->set_label('Backup Manual');
            $botao->set_title('Executa um backup manual a qualquer tempo');
            $botao->set_image(PASTA_FIGURAS.'backup.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('?fase=backup');
            $menu->add_item($botao);
            
            # Registros órfãos
            $botao = new BotaoGrafico();
            $botao->set_label('Registros Órfãos');
            $botao->set_title('Faz varredura para encontrar registros órfãos');
            $botao->set_image(PASTA_FIGURAS.'regOrf.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('registroOrfao.php');
            $menu->add_item($botao);
            $menu->show();
            
            br();
            $grid2->fechaColuna();
            
            ############################################
            
            # Servidor
            $grid2->abreColuna(12,6);
            tituloTable('Servidor');
            br();

            $menu = new MenuGrafico(4);

            # Informação do PHP
            $botao = new BotaoGrafico();
            $botao->set_label('PHP Info');
            $botao->set_title('Informações sobre&#10;a versão do PHP');
            $botao->set_image(PASTA_FIGURAS.'phpInfo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('phpInfo.php');
            $menu->add_item($botao);

            # Informação do Servidor Web
            $botao = new BotaoGrafico();
            $botao->set_label('Web Server');
            $botao->set_title('Informações sobre&#10;o servidor web');
            $botao->set_image(PASTA_FIGURAS.'webServer.png',$tamanhoImage,$tamanhoImage);
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

            $menu = new MenuGrafico(5);
            br();
            
            # Férias
            $botao = new BotaoGrafico();
            $botao->set_label('Férias');
            $botao->set_url('importacaoFerias.php');
            $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da Tabela de Férias do SigRH');
            $menu->add_item($botao);
            
            # Faltas
            $botao = new BotaoGrafico();
            $botao->set_label('Faltas');
            $botao->set_url('importacaoFaltas.php');
            $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da Tabela de Faltas do SigRH');
            $menu->add_item($botao);
                        
            # Contatos
            $botao = new BotaoGrafico();
            $botao->set_label('Contatos');
            $botao->set_url('?fase=contatos');
            $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da antiga tabela de contatos');
            $menu->add_item($botao);
            $menu->show();
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
            # Executa o backup no Linux
            shell_exec("./executaBackup");

            # Grava no log a atividade
            $intra->registraLog($idUsuario,date("Y-m-d H:i:s"),'Backup manual realizado',NULL,NULL,6);

            loadPage('?fase=backup3');
            break;
        
        case "backup3" :
            alert("Backup concluído! Acesse a pasta de backup para obter o arquivo.");
            loadPage('?');
            break;
        
    ########################################################################################
        
        case "contatos" :
            titulo('Importação dos contatos');
            
            $select = 'SELECT idPessoa
                         FROM tbpessoa
                     ORDER BY 1 desc';
                    
            $row = $servidor->select($select);
            
            $contador = 1;
            
            # Inicia a tabela
            echo "<table border=1>";

            echo "<tr>";
            echo "<th>#</th>";
            echo "<th>idPessoa</th>";
            echo "<th>Nome</th>";
            echo "<th>telResidencial</th>";
            echo "<th>telCelular</th>";
            echo "<th>telRecados</th>";
            echo "<th>emailUenf</th>";
            echo "<th>emailPessoal</th>";
            echo "</tr>";
            
            foreach ($row as $tt){
                echo "<tr>";
                echo "<td>$contador</td>";
                echo "<td>$tt[0]</td>";
                echo "<td>".$servidor->get_nomeidPessoa($tt[0])."</td>";
                
                $contatos = importaContatos($tt[0]);
               
                echo "<td>$contatos[0]</td>";                
                echo "<td>$contatos[1]</td>";
                echo "<td>$contatos[2]</td>";
                echo "<td>$contatos[3]</td>";
                echo "<td>$contatos[4]</td>";
                echo "</tr>";
                $contador++;
            }
            break;
        
    }
    $grid1->fechaColuna();
    $grid1->fechaGrid();    
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}