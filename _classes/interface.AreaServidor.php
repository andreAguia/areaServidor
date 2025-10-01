<?php

/**
 * classe Areaservidor
 * Encapsula as rotinas da Área do Servidor
 * 
 * By Alat
 */
class AreaServidor {
    #################################################################

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
        } elseif ($mes == 10) {
            $imagem = new Imagem(PASTA_FIGURAS . 'uenf_outubro.png', 'Outubro Rosa', 200, 120);
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
     * Método menuPrincipal
     * Exibe o manu principal
     * 
     * @param    string $idUsuario -> Usuário logado
     */
    public static function menuPrincipal($fase = null, $idUsuario = null) {

        # Conecta ao Banco de Dados
        $intra = new Intra();
        $pessoal = new Pessoal();

        # Pega informaćões do usuário logado
        $idServidor = $intra->get_idServidor($idUsuario);
        $idPerfil = $pessoal->get_idPerfil($idServidor);
        $perfilTipo = $pessoal->get_perfilTipo($idPerfil);

        # Array do menu
        $array = [
            ['Geral', 'Inicial', 'inicial'],
            ['Geral', 'Calendário de Pagamento', 'pgto'],
            ['Geral', 'Aniversariantes', 'aniversariantes']
        ];

        # Somente admin
        if (Verifica::acesso($idUsuario, 1)) {
            array_push($array, ['Geral', 'Serviços da GRH', 'exibeServicos']);
            array_push($array, ['Geral', 'Administração', 'menuAdmin']);
            array_push($array, ['Geral', 'Banco de Dados', 'menuBanco']);
            array_push($array, ['Geral', 'Procedimentos', 'menuProcedimentos']);
        }

        # Retira o menu de dados do servidor para quando o usuário for bolsista
        if ($perfilTipo <> "Outros") {
            array_push($array, ['Dados do Servidor', 'Histórico de Férias', 'historicoFerias', 'Exibe o Histórico de Férias do Servidor']);
            array_push($array, ['Dados do Servidor', 'Histórico de Afastamentos', 'afastamentoGeral']);
            array_push($array, ['Dados do Servidor', 'Histórico de Lic.Prêmio', 'historicoPremio']);
        }

        # Acrescenta outros itens
        array_push($array, ['Listagem de Servidores', 'por Nome', 'nome']);

        # Acesso aos contatos dos servidores com foto
        if (Verifica::acesso($idUsuario, [1, 18])) {
            array_push($array, ['Listagem de Servidores', 'por Nome com Foto', 'nomeFoto']);
        }

        array_push($array, ['Listagem de Servidores', 'em Férias no seu Setor', 'feriasSetor']);
        array_push($array, ['Listagem de Servidores', 'por Cargo em Comissão', 'cargoComissao']);
        array_push($array, ['Listagem de Servidores', 'por Cargo Efetivo', 'servidorCargo']);
        array_push($array, ['Listagem de Servidores', 'por Lotação', 'porLotacao']);

        # Acesso aos contatos dos servidores
        if (Verifica::acesso($idUsuario, [1, 11])) {
            array_push($array, ['Listagem de Servidores', 'com E-mails e Telefones', 'contatos']);
        }

        # Acesso aos contatos dos servidores com cpf
        if (Verifica::acesso($idUsuario, [1, 17])) {
            array_push($array, ['Listagem de Servidores', 'com CPF e Chefia Imediata', 'comCpf']);
        }

        # Zera o agruppamento para a rotina que monta o menu
        $agrupamento = "";

        # Menu
        titulo("Menu");

        $menu = new Menu();
        #$menu->add_item('titulo', 'Menu');

        foreach ($array as $item) {
            # Verifica se mudou o agrupamento
            if ($agrupamento <> $item[0]) {
                $menu->add_item('titulo1', $item[0]);
                $agrupamento = $item[0];
            }

            # Adiciona o link verificando se é o ativo
            if ($fase == $item[2]) {
                $menu->add_item('link', "<b>| {$item[1]} |</b>", "?fase={$item[2]}", isset($item[3]) ? $item[3] : null, null, isset($item[4]) ? $item[4] : null);
            } else {
                $menu->add_item('link', $item[1], "?fase={$item[2]}", isset($item[3]) ? $item[3] : null, null, isset($item[4]) ? $item[4] : null);
            }
            #add_item($tipo = 'link', $label = null, $url = '#', $title = null, $accessKey = null, $target = null)
        }

        # Exibe o menu
        $menu->show();
    }

    ###########################################################

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
        br(2);

        # Classes
        $pessoal = new Pessoal();
        $intra = new Intra();

        # Inicia o menu
        $menu = new MenuGrafico(3);
        $menu->set_espacoEntreLink(true);

        # Sei
        $botao = new BotaoGrafico();
        $botao->set_title('Sistema Eletrônico de Informações');
        #$botao->set_label("Sei");
        $botao->set_imagem(PASTA_FIGURAS . "sei.png", 220, 60);
        $botao->set_url("https://sei.rj.gov.br/");
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
     * Método moduloSispatri
     */
    public static function moduloSispatri() {

        titulo("Sispatri");
        br();

        $botao = new BotaoGrafico();
        $botao->set_label();
        $botao->set_url("https://www.rj.gov.br/servico/acessar-sispatri-declaracao-patrimonial-2023169");
        $botao->set_imagem(PASTA_FIGURAS . 'Sispatri2025.jpg', '100%', '100%');
        $botao->set_title('Sistema de Registros de Bens dos Agentes Públicos');
        $botao->set_target("_blank");
        $botao->show();
    }

    #################################################################

    /**
     * Método moduloSobre
     */
    public static function moduloSobre() {

        titulo(SISTEMA);

        # Exibe a Versão e o usuário logado
        br(2);
        p(SISTEMA, 'grhTitulo');

        # Div
        $div = new Div("center");
        $div->abre();

        p("Versão: " . VERSAO, 'versao');
        p("(Atualizado em: " . ATUALIZACAO . ")", 'versao');

        $div->fecha();
        br(2);
    }

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
        $label = ["Usuário", "Servidor", "Perfil", "Cargo", "Admissão", "Lotação", "Situação"];
        $function = [null, null, null, null, "date_to_php"];
        $classe = [null, null, null, "pessoal", null, "pessoal", "pessoal"];
        $metodo = [null, null, null, "get_Cargo", null, "get_Lotacao", "get_Situacao"];

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
}
