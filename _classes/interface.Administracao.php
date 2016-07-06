<?php
 /**
 * classe Areaservidor
 * Encapsula as rotinas da Área do Servidor
 * 
 * By Alat
 */
 
 class Administracao
{	
          
    /**
    * método menu
    * Exibe o menu da área do servidor
    * 
    * @param $idUsuario integer Informa o usuario logado para exibir ou não alguns menus
    */
    public static function menu()
    {
        /**
         * Menu de Administração
         */
        
        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

            botaoVoltar('../../grh/grhSistema/grh.php');
            titulo('Administração');
       
        $grid->fechaColuna();
        $grid->fechaGrid();  
        
        # Define o tamanho do ícone
        $tamanhoImage = 60;
        $permissao = new Intra();
        
        # Cria o grid
        $grid = new Grid();
        
        # Área de Gestão de Usuários
        $grid->abreColuna(12,6);
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
        $grid->fechaColuna();
        
        # Área de Configuração
        $grid->abreColuna(12,6);
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
        $grid->fechaColuna();
        
        # Documentação
        $grid->abreColuna(12,6);
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
            $botao->set_label('Administração');
            $botao->set_title('Documentação da Área de Administração');
            $botao->set_image(PASTA_FIGURAS.'administracao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentaCodigo.php?fase=Administracao');
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
        $grid->fechaColuna();
        
        # Servidor
        $grid->abreColuna(12,6);
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
        $grid->fechaColuna();
        
        
        $grid->abreColuna(12,6);
        $fieldset = new Fieldset('Sistema de Gestão de Pessoas');
        $fieldset->abre();
        
            $menu = new MenuGrafico(4);
                        
            $botao = new BotaoGrafico();
            $botao->set_label('Banco');
            $botao->set_url("../../grh/grhSistema/cadastroBanco.php");
            #$botao->set_onClick("abreDivId('divMensagemAguarde'); fechaDivId('divMenu'); window.location='banco.php'");
            $botao->set_image(PASTA_FIGURAS.'banco.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Cadastro de Bancos');
            #$botao->set_accesskey('S');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Escolaridade');
            $botao->set_url("../../grh/grhSistema/cadastroEscolaridade.php");
            $botao->set_image(PASTA_FIGURAS.'diploma.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Cadastro de Escolaridades');
            #$botao->set_accesskey('S');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Estado Civil');
            $botao->set_url("../../grh/grhSistema/cadastroEstadoCivil.php");
            $botao->set_image(PASTA_FIGURAS.'licenca.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Cadastro de Estado Civil');
            #$botao->set_accesskey('S');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Parentesco');
            $botao->set_url("../../grh/grhSistema/cadastroParentesco.php");
            $botao->set_image(PASTA_FIGURAS.'parentesco.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Cadastro de Parentesco');
            #$botao->set_accesskey('S');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Situação');
            $botao->set_url("../../grh/grhSistema/cadastroSituacao.php");
            $botao->set_image(PASTA_FIGURAS.'usuarios.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Cadastro de Situação');
            #$botao->set_accesskey('S');
            $menu->add_item($botao);

            $botao = new BotaoGrafico();
            $botao->set_label('Progressão');
            $botao->set_url("../../grh/grhSistema/cadastroProgressao.php");
            $botao->set_image(PASTA_FIGURAS.'dinheiro.jpg',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Cadastro de Tipos de Progressões');
            #$botao->set_accesskey('S');
            $menu->add_item($botao);
            
            $menu->show();
            $fieldset->fecha();
        
        $grid->fechaColuna();
        $grid->fechaGrid();        
    }
    
    ##########################################################
    
    public static function menuDocumentacao($sistema)
    {
        /**
         * Menu de Documentação
         */
        
        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

            botaoVoltar("administracao.php");
            titulo('Documentação dos Sistemas');
       
        $grid->fechaColuna();
        $grid->fechaGrid();  
        
        # Define o tamanho do ícone
        $tamanhoImage = 60;

        # Cria 3 colunas
        $grid = new Grid();

        $grid->abreColuna(4);
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
        $grid->fechaColuna();


        $grid->abreColuna(4);
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
        $grid->fechaColuna();

        $grid->abreColuna(4);
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
        $grid->fechaColuna();
        $grid->fechaGrid();

        $grid->fechaColuna();
        $grid->fechaGrid();
    }

        
}
