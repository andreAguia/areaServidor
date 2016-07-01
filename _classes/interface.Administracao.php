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
    public static function menu($idUsuario)
    {
        /**
         * Menu de Administração
         */
        
        $tamanhoImage = 60;
        $permissao = new Intra();
        
        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12,6);

        $fieldset = new Fieldset('Geral');
        $fieldset->abre();
        
            $menu = new MenuGrafico(4);
                        
            # Administração de Usuários
            $botao = new BotaoGrafico();
            $botao->set_label('Usuários');
            $botao->set_url('usuarios.php');
            $botao->set_image(PASTA_FIGURAS.'usuarios.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Gerencia os Usuários na Intranet');
            $menu->add_item($botao);
            
            # Variáveis de Configuração
            $botao = new BotaoGrafico();
            $botao->set_label('Configurações');
            $botao->set_url('configuracao.php');
            $botao->set_image(PASTA_FIGURAS.'configuracao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_title('Edita as Variáveis de&#10;configuração da Intranet');
            $menu->add_item($botao);
            
            # Histórico Geral
            $botao = new BotaoGrafico();
            $botao->set_label('Histórico');
            $botao->set_title('Histórico Geral do Sistema');
            $botao->set_image(PASTA_FIGURAS.'historico.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('historico.php');
            $menu->add_item($botao);

            # Documentação
            $botao = new BotaoGrafico();
            $botao->set_label('Documentação');
            $botao->set_title('Documentação do Sistema');
            $botao->set_image(PASTA_FIGURAS.'documentacao.png',$tamanhoImage,$tamanhoImage);
            $botao->set_url('documentacao.php');
            $menu->add_item($botao);
            
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
            $menu->add_item($botao);
            
            $menu->show();
            $fieldset->fecha();
        
        
        $grid->fechaColuna();
        $grid->abreColuna(0,6);
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
}
