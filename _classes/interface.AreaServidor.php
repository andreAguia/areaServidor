<?php

/**
 * classe Areaservidor
 * Encapsula as rotinas da Área do Servidor
 * 
 * By Alat
 */
class AreaServidor {

    /**
     * Método cabecalho
     * 
     * Exibe o cabecalho
     */
    public static function cabecalho($titulo = null) {
        # tag do cabeçalho
        echo '<header>';

        # Verifica se a imagem é comemorativa
        $dia = date("d");
        $mes = date("m");

        if (($dia == 8) AND ($mes == 3)) {
            $imagem = new Imagem(PASTA_FIGURAS . 'uenf_mulher.jpg', 'Dia Internacional da Mulher', 190, 60);
        } elseif (($mes == 12) AND ($dia < 26)) {
            $imagem = new Imagem(PASTA_FIGURAS . 'uenf_natal.png', 'Feliz Natal', 200, 60);
        } else {
            $imagem = new Imagem(PASTA_FIGURAS . 'uenf.png', 'Uenf - Universidade Estadual do Norte Fluminense', 190, 60);
        }

        $cabec = new Div('center');
        $cabec->abre();
        $imagem->show();
        $cabec->fecha();

        if (!(is_null($titulo))) {
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

    #################################################################

    /**
     * Método rodape
     * Exibe oo rodapé
     * 
     * @param    string $idUsuario -> Usuário logado
     */
    public static function rodape($idUsuario, $barra = true) {
        # Limita a tela
        $grid = new Grid();

        # Exibe faixa azul
        if ($barra) {
            $grid->abreColuna(12);
            titulo();
            $grid->fechaColuna();
        }

        # Exibe a versão do sistema
        $grid->abreColuna(6);
        $intra = new Intra();
        p('Usuário : ' . $intra->get_usuario($idUsuario), 'usuarioLogado');
        $grid->fechaColuna();

        # Exibe o desenvolvedor
        $grid->abreColuna(6);
        #p("Desenvolvido por André Águia", 'pauthor');
        p("UENF - Universidade Estadual do Norte Fluminense Darcy Ribeiro", 'pauthor');
        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    #################################################################

    /**
     * Método moduloSistemasInternos
     * 
     * Exibe o menu de Sistemas Internos
     */
    public static function moduloSistemasInternos($idUsuario) {

        # Título
        titulo('Sistemas Internos');
        $tamanhoImage = 64;
        br();

        # Inicia o menu
        if (Verifica::acesso($idUsuario, 1)) {
            $menu = new MenuGrafico(2);
        }

        if (Verifica::acesso($idUsuario, [9, 10])) {
            $menu = new MenuGrafico(1);
        }
        $menu->set_espacoEntreLink(true);

        # Sistema de gestão de contratos
        if (Verifica::acesso($idUsuario, [1, 9, 10])) {
            $botao = new BotaoGrafico();
            $botao->set_label("Sistema de Contratos");
            $botao->set_title("Sistema de Gestão de Contratos");
            $botao->set_imagem(PASTA_FIGURAS . 'contratos.png', $tamanhoImage, $tamanhoImage);
            $botao->set_url('../../../contratos/sistema/cadastroContrato.php');
            $menu->add_item($botao);
        }

        if (Verifica::acesso($idUsuario, 1)) {
            $botao = new BotaoGrafico();
            $botao->set_label('Sistema de Pessoal');
            $botao->set_title('Sistema de Pessoal');
            $botao->set_url('../../../grh/grhSistema/grh.php');
            $botao->set_imagem(PASTA_FIGURAS . 'servidores.png', $tamanhoImage, $tamanhoImage);
            $menu->add_item($botao);
        }
        $menu->show();
    }

    #################################################################

    /**
     * Método moduloSistemasExternos
     * 
     * Exibe o menu de de Sistemas Externos
     */
    public static function moduloSistemasExternos($idUsuario) {

        # Título
        titulo('Sistemas Externos');
        br();

        # Classes
        $pessoal = new Pessoal();
        $intra = new Intra();

        # Verifica se é bolsista / estagiário           
        $tipoPerfil = $pessoal->get_perfilTipo($pessoal->get_idPerfil($intra->get_idServidor($idUsuario)));

        # Altera o menu de acordo com o usuário
        if ($tipoPerfil == "Outros") {
            $itens = 2;
        } else {
            $itens = 1;
        }

        # Inicia o menu
        $menu = new MenuGrafico($itens);
        $menu->set_espacoEntreLink(true);

        # Sei
        $botao = new BotaoGrafico();
        $botao->set_title('Sistema Eletrônico de Informações');
        #$botao->set_label("Sei");
        $botao->set_imagem(PASTA_FIGURAS . "sei.png", 220, 60);
        $botao->set_url("https://sei.fazenda.rj.gov.br/sip/login.php?sigla_orgao_sistema=ERJ&sigla_sistema=SEI&infra_url=L3NlaS8=");
        $botao->set_target("_blank");
        $menu->add_item($botao);

        # SigFis
        $botao = new BotaoGrafico();
        $botao->set_title('Sistema Integrado de Gestão Fiscal');
        #$botao->set_label("SigFis");
        $botao->set_imagem(PASTA_FIGURAS . "tce.png", 180, 50);
        #$botao->set_url("https://www.tce.rj.gov.br/sigfisest/");
        $botao->set_url("https://www.tce.rj.gov.br/etcerj/");
        $botao->set_target("_blank");
        $menu->add_item($botao);

        # Siafe
        $botao = new BotaoGrafico();
        $botao->set_title('Sistema Integrado de Gestão Orçamentária, Financeira e Contábil do Rio de Janeiro');
        #$botao->set_label("Siafe");
        $botao->set_imagem(PASTA_FIGURAS . "siafe.png", 180, 50);
        $botao->set_url("https://www5.fazenda.rj.gov.br/SiafeRio/faces/login.jsp;jsessionid=FfPAOZiFLVOws9w_lr7lfkdC1rdXFlgoZ4b0lI9DofE59ZJZilH4!-1875128395");
        $botao->set_target("aba");
        $menu->add_item($botao);

        if (Verifica::acesso($idUsuario, 9)) {
            # SigFis Antigo
            $botao = new BotaoGrafico();
            $botao->set_title('Sistema Antigo');
            #$botao->set_label("SigFis");
            $botao->set_imagem(PASTA_FIGURAS . "sigfis.jpg", 180, 50);
            $botao->set_url("https://www.tce.rj.gov.br/sigfisest/");
            $botao->set_target("_blank");
            $menu->add_item($botao);
        }

        $menu->show();
    }

    #################################################################

    /**
     * Método moduloNoticias
     */
    public static function moduloNoticias() {

        titulo("Notícias");
        br();

        $botao = new BotaoGrafico();
        $botao->set_label();
        $botao->set_url("https://www.servidor.rj.gov.br/portal-web/portal/publico/Noticia/detalhar?hdnNoticia=1152");
        $botao->set_imagem(PASTA_FIGURAS . 'sispatri2.png', '100%', '100%');
        $botao->set_title('Sistema de Registros de Bens dos Agentes Públicos');
        #$botao->set_target("_blank");
        $botao->show();

        br();

        $botao = new BotaoGrafico();
        $botao->set_label();
        $botao->set_url("../../_arquivos/documentos/42.pdf");
        $botao->set_imagem(PASTA_FIGURAS . 'semanaServidor.png', '100%', '100%');
        $botao->set_title('Semana do Servidor');
        $botao->set_target("_blank");
        $botao->show();
        br(2);
    }

    #################################################################

    /**
     * Método moduloSobre
     */
    public static function moduloSobre() {
        
        titulo(SISTEMA);
        
        # Exibe a Versão e o usuário logado
        br(8);
        p(SISTEMA, 'grhTitulo');

        # Div
        $div = new Div("center");
        $div->abre();
        
        $linkAdm = new Link("Versão: " . VERSAO . " - Atualizado em: " . ATUALIZACAO, null);
        $linkAdm->set_id('versao');
        $linkAdm->set_target('_blank');
        $linkAdm->show();
        
        $div->fecha();
    }

    #################################################################
    #################################################################

    /**
     * Método listaDadosUsuario
     * Exibe os dados principais do servidor logado
     * 
     * @param    string $idServidor -> idServidor do servidor
     */
    public static function listaDadosUsuario($idUsuario) {
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
     * Método menuAdministracao
     * Exibe oo rodapé
     * 
     * @param    string $idUsuario -> Usuário logado
     */
    public static function menuAdministracao($idUsuario) {

        # Cria Grid
        $grid = new Grid();

        # Primeira Coluna
        $grid->abreColuna(12, 4);
        self::moduloUsuarios($idUsuario);
        $grid->fechaColuna();

        $grid->abreColuna(12, 4);
        self::moduloAdministracaoSistemas($idUsuario);
        $grid->fechaColuna();

        $grid->abreColuna(12, 4);
        self::moduloProjetos($idUsuario);
        $grid->fechaColuna();

        $grid->abreColuna(12, 4);
        self::moduloServidor($idUsuario);
        $grid->fechaColuna();

        $grid->abreColuna(12, 8);
        self::moduloBanco($idUsuario);
        $grid->fechaColuna();

        $grid->fechaGrid();
    }

    ###########################################################

    /**
     * Método moduloUsuarios
     * 
     * Exibe o menu de Gestão de Usuários
     */
    private static function moduloUsuarios($idUsuario) {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Usuários');
        $tamanhoImage = 64;
        br();

        # Inicia o menu
        $menu = new MenuGrafico(2);

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
    private static function moduloAdministracaoSistemas($idUsuario) {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Sistema');
        $tamanhoImage = 64;
        br();

        # Inicia o menu
        $menu = new MenuGrafico(2);

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

        # Menu de Documentos
        $botao = new BotaoGrafico();
        $botao->set_label('Menu de Documentos');
        #$botao->set_target('blank');
        $botao->set_title('Menu de Documentos do sistema GRH');
        $botao->set_imagem(PASTA_FIGURAS . 'documentacao.png', $tamanhoImage, $tamanhoImage);
        $botao->set_url("../../grh/grhSistema/cadastroMenuDocumentos.php");
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
    private static function moduloBanco($idUsuario) {

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
    public static function moduloServidor($idUsuario) {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Servidores');
        $tamanhoImage = 64;
        br();

        # Inicia o menu
        $menu = new MenuGrafico(2);

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
    private static function moduloProjetos($idUsuario) {

        $painel = new Callout();
        $painel->abre();

        # Título
        titulo('Projetos');

        br();
        tituloTable('Ativos');
        br();
        $tamanhoImage = 64;

        # Inicia o menu
        $menu = new MenuGrafico(2);

        # Controle de procedimentos
        $botao = new BotaoGrafico();
        $botao->set_label('Procedimentos');
        $botao->set_url('procedimentos.php');
        #$botao->set_url('pastaDigitalizada.php');
        $botao->set_imagem(PASTA_FIGURAS . 'procedimentos.png', $tamanhoImage, $tamanhoImage);
        $botao->set_title('Sistema de procedimentos');
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

        br();
        tituloTable('Arquivados');
        br();

        # Inicia o menu
        $menu = new MenuGrafico(2);

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

        $menu->show();
        $painel->fecha();
    }

    ###########################################################

    /**
     * Método moduloServidorWeb
     * 
     * Exibe informações do Servidor Web
     */
    public static function moduloServidorWeb() {

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
        foreach ($indicesServer as $arg) {
            if (isset($_SERVER[$arg])) {
                echo '<tr><td>' . $arg . '</td><td>' . $_SERVER[$arg] . '</td></tr>';
            } else {
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
    public static function moduloServidorPhp() {

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
        foreach ($indicesServer as $arg) {
            if (isset($_SERVER[$arg])) {
                echo '<tr><td>' . $arg . '</td><td>' . $_SERVER[$arg] . '</td></tr>';
            } else {
                echo '<tr><td>' . $arg . '</td><td>-</td></tr>';
            }
        }
        echo '</table>';
        $painel->fecha();
    }

    #################################################################
}
