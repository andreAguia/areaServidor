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

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
    # Limita o tamanho da tela
    $grid1 = new Grid();
    $grid1->abreColuna(12);
    
    switch ($fase)
    {	
        # Exibe o Menu Inicial
        case "menu" :
            botaoVoltar('areaServidor.php');
            titulo('Administração');

            # Define o tamanho do ícone
            $tamanhoImage = 60;
            $permissao = new Intra();

            # Cria o grid
            $grid2 = new Grid();

            # Gestão de Usuários
            $grid2->abreColuna(12,6);
            $fieldset = new Fieldset('Gestão de Usuários');
            $fieldset->abre();

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

            $fieldset->fecha();
            $grid2->fechaColuna();

            # Área do Sistema
            $grid2->abreColuna(12,6);
            $fieldset = new Fieldset('Sistema');
            $fieldset->abre();

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

                $menu->show();

            $fieldset->fecha();
            $grid2->fechaColuna();

            # Documentação
            $grid2->abreColuna(12,6);
            $fieldset = new Fieldset('Documentação');
            $fieldset->abre();

                $menu = new MenuGrafico(4);

                # Framework
                $botao = new BotaoGrafico();
                $botao->set_label('FrameWork');
                $botao->set_title('Documentação do Framework');
                $botao->set_image(PASTA_FIGURAS.'framework.png',$tamanhoImage,$tamanhoImage);
                $botao->set_url('documentaCodigo.php?fase=Framework');
                $menu->add_item($botao);

                # Administração
                $botao = new BotaoGrafico();
                $botao->set_label('Área do Servidor');
                $botao->set_title('Documentação da Área de Administração');
                $botao->set_image(PASTA_FIGURAS.'administracao.png',$tamanhoImage,$tamanhoImage);
                $botao->set_url('documentaCodigo.php?fase=areaServidor');
                $menu->add_item($botao);

                # Sistema de Pessoal
                $botao = new BotaoGrafico();
                $botao->set_label('Pessoal');
                $botao->set_title('Documentação do Sistema de Pessoal');
                $botao->set_image(PASTA_FIGURAS.'servidores.png',$tamanhoImage,$tamanhoImage);
                $botao->set_url('documentaCodigo.php?fase=Grh');
                $menu->add_item($botao);

                $menu->show();

            $fieldset->fecha();
            $grid2->fechaColuna();

            # Servidor
            $grid2->abreColuna(12,6);
            $fieldset = new Fieldset('Servidor');
            $fieldset->abre();

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

            $fieldset->fecha();  
            $grid2->fechaColuna();

            # Banco de dados
            $grid2->abreColuna(12,6);
            $fieldset = new Fieldset('Banco de Dados');
            $fieldset->abre();

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
                $botao->set_url('http://localhost/phpmyadmin');
                $menu->add_item($botao);
                
                # Backup Manual
                $botao = new BotaoGrafico();
                $botao->set_label('Backup Manual');
                $botao->set_title('Executa um backup manual a qualquer tempo');
                $botao->set_image(PASTA_FIGURAS.'backup.png',$tamanhoImage,$tamanhoImage);
                $botao->set_url('?fase=backup');
                $menu->add_item($botao);

                $menu->show();

            $fieldset->fecha();  
            $grid2->fechaColuna();
            
            # Outros
            $grid2->abreColuna(12,6);
            $fieldset = new Fieldset('Outros');
            $fieldset->abre();

                $menu = new MenuGrafico(4);

                # Cadastro de Mensagens
                $botao = new BotaoGrafico();
                $botao->set_label('Mensagens');
                $botao->set_title('Cadastro de Mensagens');
                $botao->set_image(PASTA_FIGURAS.'mensagem.jpg',$tamanhoImage,$tamanhoImage);
                $botao->set_url('mensagem.php');
                $menu->add_item($botao);
                
                # Google Task
                $botao = new BotaoGrafico();
                $botao->set_label('Tarefas');
                $botao->set_title('Acesso o Google Task');
                $botao->set_image(PASTA_FIGURAS.'tarefas.png',$tamanhoImage,$tamanhoImage);
                $botao->set_onClick("window.open('https://mail.google.com/tasks/ig','_blank','menubar=no,scrollbars=yes,location=no,directories=no,status=no,width=460,height=500');");
                #$botao->set_url('https://mail.google.com/tasks/ig');
                $menu->add_item($botao);
                
                # Administração do Site da GRH
                $botao = new BotaoGrafico();
                $botao->set_label('Administração do Site da GRH');
                $botao->set_title('Acesso a área de administraçao do site da GRH');
                $botao->set_image(PASTA_FIGURAS.'admin.png',$tamanhoImage,$tamanhoImage);
                $botao->set_onClick("window.open('http://uenf.br/dga/grh/admin','_blank','menubar=no,scrollbars=yes,location=no,directories=no,status=no,width=1024,height=768');");
                #$botao->set_url('http://uenf.br/dga/grh/admin');
                $menu->add_item($botao);
                
                $menu->show();

            $fieldset->fecha(); 
            $grid2->fechaColuna();
            $grid2->fechaGrid();    
            
            # Exibe o rodapé da página
            br();
            AreaServidor::rodape($idUsuario);
            break;
        
        # Exibe o Menu de Documentação
        case "importacao" :
            botaoVoltar("administracao.php");
            titulo('Importação do banco de dados');

            # Define o tamanho do ícone
            $tamanhoImage = 60;
            
            $fieldset = new Fieldset('Importação');
            $fieldset->abre();

            $menu = new MenuGrafico(4);
            
            # Férias
            $botao = new BotaoGrafico();
            $botao->set_label('Férias');
            $botao->set_url('importacaoFerias.php');
            $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da Tabela de Férias do SigRH');
            $menu->add_item($botao);

            # FEN001
            $botao = new BotaoGrafico();
            $botao->set_label('FEN001');
            $botao->set_url('importacaoUenfNovaUenf.php');
            $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da Tabela de Servidores FEN001');
            $menu->add_item($botao);
            
            # FEN004
            $botao = new BotaoGrafico();
            $botao->set_label('FEN004');
            $botao->set_url('importaFen004.php');
            $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da Tabela de Afastamentos');
            $menu->add_item($botao);
            
            # FEN019
            $botao = new BotaoGrafico();
            $botao->set_label('FEN019');
            $botao->set_url('importaFen019.php');
            $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Importação da Tabela de Férias');
            $menu->add_item($botao);
            $menu->show();  
            $fieldset->fecha();  
            break;
        
        case "backup" :
                $backupData = $intra->get_variavel("backupData");   // Verifica a data do último backup
                $backupPasta = $intra->get_variavel("backupPasta"); // Pega a pasta do mysql
                $backupPasta = str_replace("/","\\",$backupPasta);
                $hoje = date("d/m/Y");                              // Pega a data de hoje

                exec("C:\\".$backupPasta."\\backup.bat");   // Executa o backup
                
                # Grava no log a atividade
                $intra->registraLog($idUsuario,date("Y-m-d H:i:s"),'Backup manual realizado',NULL,NULL,6);

                loadPage("?");
            break;
        
        
    }
    $grid1->fechaColuna();
    $grid1->fechaGrid();    
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}