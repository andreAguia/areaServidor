<?php
/**
 * Administração
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = null;

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

            # Área de Gestão de Usuários
            $grid2->abreColuna(12,6);
            $fieldset = new Fieldset('Gestão de Usuários');
            $fieldset->abre();

                $menu = new MenuGrafico(4);

                # Administração de Usuários
                $botao = new BotaoGrafico();
                $botao->set_label('Usuários');
                $botao->set_url('usuarios.php');
                $botao->set_image(PASTA_FIGURAS.'usuarios.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Gerencia os Usuários na Intranet');
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

                $menu->show();

            $fieldset->fecha();
            $grid2->fechaColuna();

            # Área de Configuração
            $grid2->abreColuna(12,6);
            $fieldset = new Fieldset('Configuração');
            $fieldset->abre();

                $menu = new MenuGrafico(4);

                # Variáveis de Configuração
                $botao = new BotaoGrafico();
                $botao->set_label('Configurações');
                $botao->set_url('configuracao.php');
                $botao->set_image(PASTA_FIGURAS.'configuracao.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Edita as Variáveis de&#10;configuração da Intranet');
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

                # Importação
                $botao = new BotaoGrafico();
                $botao->set_label('Importação');
                $botao->set_title('Executa a rotina de importação');
                $botao->set_image(PASTA_FIGURAS.'importacao.png',$tamanhoImage,$tamanhoImage);
                $botao->set_url('importacao.php');
                #$menu->add_item($botao);

                # PhpMyAdmin
                $botao = new BotaoGrafico();
                $botao->set_label('PhpMyAdmin');
                $botao->set_title('Executa o PhpMyAdmin');
                $botao->set_target('_blank');
                $botao->set_image(PASTA_FIGURAS.'mysql.png',$tamanhoImage,$tamanhoImage);
                $botao->set_url('http://localhost/phpmyadmin');
                $menu->add_item($botao);

                $menu->show();

            $fieldset->fecha();  
            $grid2->fechaColuna();
            $grid2->fechaGrid();        
            break;
        
        # Exibe o Menu de Documentação
        case "documentacao" :
            botaoVoltar("administracao.php");
            titulo('Documentação dos Sistemas');

            # Define o tamanho do ícone
            $tamanhoImage = 60;

            # Cria 3 colunas
            $grid3 = new Grid();

            $grid3->abreColuna(4);
            $fieldset = new Fieldset('Framework');
            $fieldset->abre();

                $menu = new MenuGrafico(3);

                # Código
                $botao = new BotaoGrafico();
                $botao->set_label('Código');
                $botao->set_url('documentaCodigo.php?fase=Framework');
                $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Classes e Funções');
                $menu->add_item($botao);

                # Variáveis de Configuração
                $botao = new BotaoGrafico();
                $botao->set_label('Banco de Dados');
                $botao->set_url('documentaBd.php?fase=Framework');
                $botao->set_image(PASTA_FIGURAS.'bd.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Exibe informações do banco de dados');
                $menu->add_item($botao);

                # Histórico Geral
                $botao = new BotaoGrafico();
                $botao->set_label('Diagrama');
                $botao->set_url('documentaDiagrama.php?fase=Framework');
                $botao->set_title('Diagramas do sistema');
                $botao->set_image(PASTA_FIGURAS.'diagrama.jpg',$tamanhoImage,$tamanhoImage);    
                $menu->add_item($botao);

                $menu->show();

            $fieldset->fecha();
            $grid3->fechaColuna();


            $grid3->abreColuna(4);
            $fieldset = new Fieldset('Administração');
            $fieldset->abre();

                $menu = new MenuGrafico(3);

                # Código
                $botao = new BotaoGrafico();
                $botao->set_label('Código');
                $botao->set_url('documentaCodigo.php?fase=Administracao');
                $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Classes e Funções');
                $menu->add_item($botao);

                # Variáveis de Configuração
                $botao = new BotaoGrafico();
                $botao->set_label('Banco de Dados');
                $botao->set_url('documentaBd.php?fase=Administracao');
                $botao->set_image(PASTA_FIGURAS.'bd.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Exibe informações do banco de dados');
                $menu->add_item($botao);

                # Histórico Geral
                $botao = new BotaoGrafico();
                $botao->set_label('Diagrama');
                $botao->set_url('documentaDiagrama.php?fase=Administracao');
                $botao->set_title('Diagramas do sistema');
                $botao->set_image(PASTA_FIGURAS.'diagrama.jpg',$tamanhoImage,$tamanhoImage);    
                $menu->add_item($botao);

                $menu->show();

            $fieldset->fecha();
            $grid3->fechaColuna();

            $grid3->abreColuna(4);
            $fieldset = new Fieldset('GRH');
            $fieldset->abre();

                $menu = new MenuGrafico(3);

                # Código
                $botao = new BotaoGrafico();
                $botao->set_label('Código');
                $botao->set_url('documentaCodigo.php?fase=Grh');
                $botao->set_image(PASTA_FIGURAS.'codigo.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Classes e Funções');
                $menu->add_item($botao);

                # Variáveis de Configuração
                $botao = new BotaoGrafico();
                $botao->set_label('Banco de Dados');
                $botao->set_url('documentaBd.php?fase=Grh');
                $botao->set_image(PASTA_FIGURAS.'bd.png',$tamanhoImage,$tamanhoImage);
                $botao->set_title('Exibe informações do banco de dados');
                $menu->add_item($botao);

                # Histórico Geral
                $botao = new BotaoGrafico();
                $botao->set_label('Diagrama');
                $botao->set_url('documentaDiagrama.php?fase=Grh');
                $botao->set_title('Diagramas do sistema');
                $botao->set_image(PASTA_FIGURAS.'diagrama.jpg',$tamanhoImage,$tamanhoImage);    
                $menu->add_item($botao);

                $menu->show();

            $fieldset->fecha();
            $grid3->fechaColuna();
            $grid3->fechaGrid();       
            break;
    }
    $grid1->fechaColuna();
    $grid1->fechaGrid();    
    
    $page->terminaPagina();
}