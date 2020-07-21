<?php

/**
 * classe Areaservidor
 * Encapsula as rotinas da Área do Servidor
 * 
 * By Alat
 */
class AreaServidor
{

    /**
     * Método cabecalho
     * 
     * Exibe o cabecalho
     */
    public static function cabecalho($titulo = null)
    {
        # tag do cabeçalho
        echo '<header>';

        # Verifica se a imagem é comemorativa
        $dia = date("d");
        $mes = date("m");

        if (($dia == 8)AND($mes == 3))
        {
            $imagem = new Imagem(PASTA_FIGURAS . 'uenf_mulher.jpg', 'Dia Internacional da Mulher', 190, 60);
        } elseif (($mes == 12) AND ($dia < 26))
        {
            $imagem = new Imagem(PASTA_FIGURAS . 'uenf_natal.png', 'Feliz Natal', 200, 60);
        } else
        {
            $imagem = new Imagem(PASTA_FIGURAS . 'uenf.png', 'Uenf - Universidade Estadual do Norte Fluminense', 190, 60);
        }

        $cabec = new Div('center');
        $cabec->abre();
        $imagem->show();
        $cabec->fecha();

        if (!(is_null($titulo)))
        {
            br();
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(12);

            # Topbar        
            $top = new TopBar($titulo);
            $top->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
        }

        echo '</header>';
    }

    ##########################################################

    /**
     * Método rodape
     * Exibe oo rodapé
     * 
     * @param    string $idUsuario -> Usuário logado
     */
    public static function rodape($idUsuario)
    {

        # Exibe faixa azul
        $grid = new Grid();
        $grid->abreColuna(12);
        titulo();
        $grid->fechaColuna();
        $grid->fechaGrid();

        # Exibe a versão do sistema
        $intra = new Intra();
        $grid = new Grid();
        $grid->abreColuna(4);
        p('Usuário : ' . $intra->get_usuario($idUsuario), 'usuarioLogado');
        $grid->fechaColuna();
        $grid->abreColuna(4);
        p('Versão: ' . VERSAO, 'versao');
        $grid->fechaColuna();
        $grid->abreColuna(4);
        p(BROWSER_NAME . " - " . IP, 'ip');
        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    /**
     * Método listaDadosUsuario
     * Exibe os dados principais do servidor logado
     * 
     * @param    string $idServidor -> idServidor do servidor
     */
    public static function listaDadosUsuario($idUsuario)
    {
        # Conecta com o banco de dados
        $servidor = new Pessoal();
        $intra = new Intra();

        $idServidor = $intra->get_idServidor($idUsuario);
        $nomeUsuario = $intra->get_nickUsuario($idUsuario);

        $select = 'SELECT "' . $nomeUsuario . '",
                         tbpessoa.nome,
                         tbperfil.nome,
                         tbservidor.idServidor,
                         tbservidor.dtAdmissao,
                         tbservidor.idServidor,
                         tbservidor.idServidor,
                         tbservidor.dtDemissao
                    FROM tbservidor LEFT JOIN tbpessoa ON tbservidor.idPessoa = tbpessoa.idPessoa
                                       LEFT JOIN tbsituacao ON tbservidor.situacao = tbsituacao.idsituacao
                                       LEFT JOIN tbperfil ON tbservidor.idPerfil = tbperfil.idPerfil
                   WHERE idServidor = ' . $idServidor;

        $conteudo = $servidor->select($select, true);
        $label = array("Usuário", "Servidor", "Perfil", "Cargo", "Admissão", "Lotação", "Situação");
        $function = array(null, null, null, null, "date_to_php");
        $classe = array(null, null, null, "pessoal", null, "pessoal", "pessoal");
        $metodo = array(null, null, null, "get_Cargo", null, "get_Lotacao", "get_Situacao");

        $formatacaoCondicional = array(array('coluna' => 0,
                'valor' => $nomeUsuario,
                'operador' => '=',
                'id' => 'listaDados'));

        # Monta a tabela
        $tabela = new Tabela();
        $tabela->set_conteudo($conteudo);
        $tabela->set_label($label);
        $tabela->set_funcao($function);
        $tabela->set_classe($classe);
        $tabela->set_metodo($metodo);
        $tabela->set_totalRegistro(false);
        $tabela->set_formatacaoCondicional($formatacaoCondicional);

        # Limita o tamanho da tela
        $grid = new Grid();
        $grid->abreColuna(12);

        $tabela->show();

        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ##########################################################

    /**
     * Método menuPrincipal
     * Exibe oo rodapé
     * 
     * @param    string $idUsuario -> Usuário logado
     */
    public static function menuPrincipal($idUsuario)
    {

        # Cria Grid
        $grid = new Grid();

        # Primeira Coluna
        $grid->abreColuna(12, 4);
        self::moduloSobreServidor();
        $grid->fechaColuna();

        # Segunda Coluna
        $grid->abreColuna(12, 4);
        self::moduloServidoresUniversidade($idUsuario);
        $grid->fechaColuna();

        # Sistemas
        $grid->abreColuna(12, 4);
        self::moduloSistemas($idUsuario);
        $grid->fechaColuna();
        
        $grid->fechaGrid();
    }

    ##########################################################

    /**
     * Método menuAdministracao
     * Exibe oo rodapé
     * 
     * @param    string $idUsuario -> Usuário logado
     */
    public static function menuAdministracao($idUsuario)
    {

        # Cria Grid
        $grid = new Grid();

        # Primeira Coluna
        $grid->abreColuna(12, 6);

        # Módulos
        self::moduloUsuarios($idUsuario);
        self::moduloAdministracaoSistemas($idUsuario);
        self::moduloBanco($idUsuario);

        $grid->fechaColuna();

        # Segunda Coluna
        $grid->abreColuna(12, 6);

        # Módulos
        self::moduloProjetos($idUsuario);
        self::moduloServidor($idUsuario);

        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################

    /**
     * Método moduloServidoresUniversidade
     * 
     * Exibe o menu de Servidores da Universidade
     */
    private static function moduloServidoresUniversidade($idUsuario)
    {

        $painel = new Callout();
        $painel->abre();

        titulo('Servidores da Universidade');
        $tamanhoImage = 64;
        br();

        $menu = new MenuGrafico(2);

        if (Verifica::acesso($idUsuario, 3))
        {
            $botao = new BotaoGrafico();
            $botao->set_label('Geral');
            $botao->set_url('servidorGeral.php');
            $botao->set_imagem(PASTA_FIGURAS . 'admin.png', $tamanhoImage, $tamanhoImage);
            $botao->set_title('Lista geral de servidores');
            $menu->add_item($botao);
        }

        $botao = new BotaoGrafico();
        $botao->set_label('por Lotação');
        $botao->set_url('servidorLotacao.php');
        $botao->set_imagem(PASTA_FIGURAS . 'computador.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Lista de servidores por lotação');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('por Cargo Efetivo');
        $botao->set_url('servidorCargo.php');
        $botao->set_imagem(PASTA_FIGURAS . 'cracha.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Lista de servidores por cargo efetivo');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('por Cargo em Comissão');
        $botao->set_url('servidorCargoComissao.php');
        $botao->set_imagem(PASTA_FIGURAS . 'comissao.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Lista de servidores por cargo em comissão');
        $menu->add_item($botao);
        $menu->show();

        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloTabelaAuxiliares
     * 
     * Exibe o menu de Legislação
     */
    private static function moduloSobreServidor()
    {

        $painel = new Callout();
        $painel->abre();

        titulo('Sobre o Servidor');
        $tamanhoImage = 64;
        br();

        $menu = new MenuGrafico(2);

        $botao = new BotaoGrafico();
        $botao->set_label('Histórico de Afastamentos');
        $botao->set_url('?fase=afastamentoGeral');
        $botao->set_imagem(PASTA_FIGURAS . 'afastamento.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Exibe o seu histórico de licenças e afastamentos');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Histórico de Férias');
        $botao->set_url('?fase=historicoFerias');
        $botao->set_imagem(PASTA_FIGURAS . 'ferias.jpg', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Exibe o seu histórico de férias');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Férias do seu Setor');
        $botao->set_url('?fase=feriasSetor');
        $botao->set_imagem(PASTA_FIGURAS . 'feriasSetor.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Exibe as férias dos servidores do seu setor');
        $menu->add_item($botao);

        $menu->show();
        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloTabelaAuxiliares
     * 
     * Exibe o menu de Legislação
     */
    private static function modulolinksExternos()
    {

        $painel = new Callout();
        $painel->abre();

        tituloTable('Sobre o Servidor');
        $tamanhoImage = 64;

        $menu = new MenuGrafico(3);

        $botao = new BotaoGrafico();
        $botao->set_label('Histórico de Licença');
        $botao->set_url('?fase=historicoLicenca');
        $botao->set_imagem(PASTA_FIGURAS . 'licenca.jpg', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Exibe o seu histórico de licenças e afastamentos');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Histórico de Férias');
        $botao->set_url('?fase=historicoFerias');
        $botao->set_imagem(PASTA_FIGURAS . 'ferias.jpg', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Exibe o seu histórico de férias');
        $menu->add_item($botao);

        $botao = new BotaoGrafico();
        $botao->set_label('Férias do seu Setor');
        $botao->set_url('?fase=feriasSetor');
        $botao->set_imagem(PASTA_FIGURAS . 'feriasSetor.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Exibe as férias dos servidores do seu setor');
        $menu->add_item($botao);

        $menu->show();
        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloUsuarios
     * 
     * Exibe o menu de Gestão de Usuários
     */
    private static function moduloUsuarios($idUsuario)
    {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Usuários');
        $tamanhoImage = 64;
        br();

        # Inicia o menu
        $menu = new MenuGrafico(4);

        # Administração de Usuários
        $botao = new BotaoGrafico();
        $botao->set_label('Usuários');
        $botao->set_url('usuarios.php');
        $botao->set_imagem(PASTA_FIGURAS . 'usuarios.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Gerencia os Usuários');
        $menu->add_item($botao);

        # Regras
        $botao = new BotaoGrafico();
        $botao->set_label('Regras');
        $botao->set_url('regras.php');
        $botao->set_imagem(PASTA_FIGURAS . 'regras.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Cadastro de Regras');
        $menu->add_item($botao);

        # Histórico Geral
        $botao = new BotaoGrafico();
        $botao->set_label('Histórico');
        $botao->set_title('Histórico Geral do Sistema');
        $botao->set_imagem(PASTA_FIGURAS . 'historico.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url('historico.php');
        $menu->add_item($botao);

        # Computadores (IPs)
        $botao = new BotaoGrafico();
        $botao->set_label('Acesso ao Sistema');
        $botao->set_title('Cadastro de computadores com acesso ao sistema');
        $botao->set_imagem(PASTA_FIGURAS . 'computador.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url('computador.php');
        $menu->add_item($botao);

        $menu->show();
        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloAdministracaoSistemas
     * 
     * Exibe o menu de Gestão da configuraçõa dos Sistemas
     */
    private static function moduloAdministracaoSistemas($idUsuario)
    {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Sistema');
        $tamanhoImage = 64;
        br();

        # Inicia o menu
        $menu = new MenuGrafico(4);

        # Variáveis de Configuração
        $botao = new BotaoGrafico();
        $botao->set_label('Configurações');
        $botao->set_url('configuracao.php');
        $botao->set_imagem(PASTA_FIGURAS . 'configuracao.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Edita as Variáveis de&#10;configuração da Intranet');
        $menu->add_item($botao);

        # Cadastro de Atualizações
        $botao = new BotaoGrafico();
        $botao->set_label('Atualizações');
        $botao->set_url('atualizacao.php');
        $botao->set_imagem(PASTA_FIGURAS . 'atualizacao.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Gerencia o cadastro de atualizações');
        $menu->add_item($botao);

        # Cadastro de Mensagens
        $botao = new BotaoGrafico();
        $botao->set_label('Mensagens');
        $botao->set_title('Cadastro de Mensagens');
        $botao->set_imagem(PASTA_FIGURAS . 'mensagem.jpg', $tamanhoImage, $tamanhoImage);
        $botao->set_url('mensagem.php');
        $menu->add_item($botao);

        # Documentação
        $botao = new BotaoGrafico();
        $botao->set_label('Documentação');
        #$botao->set_target('blank');
        $botao->set_title('Documentação do Sistema');
        $botao->set_imagem(PASTA_FIGURAS . 'documentacao.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url('documentacao.php');
        $menu->add_item($botao);

        $menu->show();
        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloBanco
     * 
     * Exibe o menu de Gestão do Banco de dados
     */
    private static function moduloBanco($idUsuario)
    {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Banco de Dados');
        $tamanhoImage = 64;
        br();

        # Inicia o menu
        $menu = new MenuGrafico(5);

        # Backup
        $botao = new BotaoGrafico();
        $botao->set_label('Backup');
        $botao->set_title('Acessa a área de backup');
        $botao->set_imagem(PASTA_FIGURAS . 'backup.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url('?fase=pastaBackup');
        $menu->add_item($botao);

        # Importação
        $botao = new BotaoGrafico();
        $botao->set_label('Importação');
        $botao->set_title('Executa a rotina de importação');
        $botao->set_imagem(PASTA_FIGURAS . 'importacao.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url('?fase=importacao');
        $menu->add_item($botao);

        # PhpMyAdmin
        $botao = new BotaoGrafico();
        $botao->set_label('PhpMyAdmin');
        $botao->set_title('Executa o PhpMyAdmin');
        $botao->set_target('_blank');
        $botao->set_imagem(PASTA_FIGURAS . 'mysql.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url('http://127.0.0.1/phpmyadmin');
        $menu->add_item($botao);

        # Registros órfãos
        $botao = new BotaoGrafico();
        $botao->set_label('Registros Órfãos');
        $botao->set_title('Faz varredura para encontrar registros órfãos');
        $botao->set_imagem(PASTA_FIGURAS . 'regOrf.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url('registroOrfao.php');
        $menu->add_item($botao);

        # Documentação
        $botao = new BotaoGrafico();
        $botao->set_label('Documentação');
        #$botao->set_target('blank');
        $botao->set_title('Documentação do Banco de Dados');
        $botao->set_imagem(PASTA_FIGURAS . 'documentacao.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url('documentaBd.php');
        $menu->add_item($botao);

        $menu->show();
        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloServidor
     * 
     * Exibe o menu de Informações do Servidor Web
     */
    private static function moduloServidor($idUsuario)
    {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Servidores');
        $tamanhoImage = 64;
        br();

        # Inicia o menu
        $menu = new MenuGrafico(4);

        # Informação do PHP
        $botao = new BotaoGrafico();
        $botao->set_label('Servidor PHP');
        $botao->set_title('Informações sobre&#10;a versão do PHP');
        $botao->set_imagem(PASTA_FIGURAS . 'phpInfo.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url('?fase=servidorPhp');
        $menu->add_item($botao);

        # Informação do Servidor Web
        $botao = new BotaoGrafico();
        $botao->set_label('Servidor Web');
        $botao->set_title('Informações sobre&#10;o servidor web');
        $botao->set_imagem(PASTA_FIGURAS . 'webServer.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url('?fase=servidorWeb');
        $menu->add_item($botao);

        $menu->show();
        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloProjetos
     * 
     * Exibe o menu de Gestão de Projetos
     */
    private static function moduloProjetos($idUsuario)
    {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Projetos');
        $tamanhoImage = 64;
        br();

        # Inicia o menu
        $menu = new MenuGrafico(4);

        # Controle de procedimentos
        $botao = new BotaoGrafico();
        $botao->set_label('Procedimentos');
        $botao->set_url('procedimentos.php');
        #$botao->set_url('pastaDigitalizada.php');
        $botao->set_imagem(PASTA_FIGURAS . 'procedimentos.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Sistema de procedimentos');
        $menu->add_item($botao);

        # Variáveis de Configuração
        $botao = new BotaoGrafico();
        $botao->set_label('Tarefas');
        $botao->set_url('projeto.php');
        $botao->set_imagem(PASTA_FIGURAS . 'atribuicoes.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Sistema de gestão de tarefas');
        $botao->set_target("_blank");
        $menu->add_item($botao);

        # Cadastro de Atualizações
        $botao = new BotaoGrafico();
        $botao->set_label('Notas');
        $botao->set_url('projetoNota.php');
        $botao->set_imagem(PASTA_FIGURAS . 'contratos.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Sistema de notas dos sistemas');
        $botao->set_target("_blank");
        $menu->add_item($botao);

        # Controle de Rotinas 2
        $botao = new BotaoGrafico();
        $botao->set_label('Rotinas');
        $botao->set_url('rotina.php');
        #$botao->set_url('pastaDigitalizada.php');
        $botao->set_imagem(PASTA_FIGURAS . 'rotina.jpg', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Sistema de controle de manuais de procedimentos');
        $menu->add_item($botao);

        $menu->show();
        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloServidorWeb
     * 
     * Exibe informações do Servidor Web
     */
    public static function moduloServidorWeb()
    {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Servidor Web');
        br();

        $indicesServer = array('PHP_SELF',
            'argv',
            'argc',
            'GATEWAY_INTERFACE',
            'SERVER_ADDR',
            'SERVER_NAME',
            'SERVER_SOFTWARE',
            'SERVER_PROTOCOL',
            'REQUEST_METHOD',
            'REQUEST_TIME',
            'REQUEST_TIME_FLOAT',
            'QUERY_STRING',
            'DOCUMENT_ROOT',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_CHARSET',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_CONNECTION',
            'HTTP_HOST',
            'HTTP_REFERER',
            'HTTP_USER_AGENT',
            'HTTPS',
            'REMOTE_ADDR',
            'REMOTE_HOST',
            'REMOTE_PORT',
            'REMOTE_USER',
            'REDIRECT_REMOTE_USER',
            'SCRIPT_FILENAME',
            'SERVER_ADMIN',
            'SERVER_PORT',
            'SERVER_SIGNATURE',
            'PATH_TRANSLATED',
            'SCRIPT_NAME',
            'REQUEST_URI',
            'PHP_AUTH_DIGEST',
            'PHP_AUTH_USER',
            'PHP_AUTH_PW',
            'AUTH_TYPE',
            'PATH_INFO',
            'ORIG_PATH_INFO');

        echo '<table cellpadding="10">';
        foreach ($indicesServer as $arg)
        {
            if (isset($_SERVER[$arg]))
            {
                echo '<tr><td>' . $arg . '</td><td>' . $_SERVER[$arg] . '</td></tr>';
            } else
            {
                echo '<tr><td>' . $arg . '</td><td>-</td></tr>';
            }
        }
        echo '</table>';
        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloServidorPhp
     * 
     * Exibe informações do Servidor Php
     */
    public static function moduloServidorPhp()
    {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Servidor Web');
        br();

        $indicesServer = array('PHP_SELF',
            'argv',
            'argc',
            'GATEWAY_INTERFACE',
            'SERVER_ADDR',
            'SERVER_NAME',
            'SERVER_SOFTWARE',
            'SERVER_PROTOCOL',
            'REQUEST_METHOD',
            'REQUEST_TIME',
            'REQUEST_TIME_FLOAT',
            'QUERY_STRING',
            'DOCUMENT_ROOT',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_CHARSET',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_CONNECTION',
            'HTTP_HOST',
            'HTTP_REFERER',
            'HTTP_USER_AGENT',
            'HTTPS',
            'REMOTE_ADDR',
            'REMOTE_HOST',
            'REMOTE_PORT',
            'REMOTE_USER',
            'REDIRECT_REMOTE_USER',
            'SCRIPT_FILENAME',
            'SERVER_ADMIN',
            'SERVER_PORT',
            'SERVER_SIGNATURE',
            'PATH_TRANSLATED',
            'SCRIPT_NAME',
            'REQUEST_URI',
            'PHP_AUTH_DIGEST',
            'PHP_AUTH_USER',
            'PHP_AUTH_PW',
            'AUTH_TYPE',
            'PATH_INFO',
            'ORIG_PATH_INFO');

        echo '<table cellpadding="10">';
        foreach ($indicesServer as $arg)
        {
            if (isset($_SERVER[$arg]))
            {
                echo '<tr><td>' . $arg . '</td><td>' . $_SERVER[$arg] . '</td></tr>';
            } else
            {
                echo '<tr><td>' . $arg . '</td><td>-</td></tr>';
            }
        }
        echo '</table>';
        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloSistemas
     * 
     * Exibe o menu de Informações do Servidor Web
     */
    private static function moduloSistemas($idUsuario)
    {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Sistemas');
        $tamanhoImage = 64;
        br();

        # Inicia o menu
        $menu = new MenuGrafico(1);
        
        # Sei
        $botao = new BotaoGrafico();
        $botao->set_title('Sistema Eletrônico de informações');
        $botao->set_imagem(PASTA_FIGURAS . "sei.png", 220, 72);
        $botao->set_url("https://sei.fazenda.rj.gov.br/sip/login.php?sigla_orgao_sistema=ERJ&sigla_sistema=SEI&infra_url=L3NlaS8=");
        $botao->set_target("_blank");
        $menu->add_item($botao);

        # Sistema de gestão de contratos
        if (Verifica::acesso($idUsuario, 9))
        {
            $botao = new BotaoGrafico();
            $botao->set_label("Sistema de Gestão<br/>de Coontratos");
            $botao->set_title("Sistema de Gestão de Coontratos");
            $botao->set_imagem(PASTA_FIGURAS . 'contratos.png', $tamanhoImage, $tamanhoImage);
            $botao->set_url('../../../contratos/sistema/cadastroContrato.php');
            $menu->add_item($botao);
        }

        $menu->show();
        $painel->fecha();
    }

    ###########################################################
}
