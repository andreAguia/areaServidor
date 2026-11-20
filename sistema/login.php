<?php

/**
 * Inicial da área do Servidor
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Conecta ao Banco de Dados
$intra = new Intra();
$pessoal = new Pessoal();

####
# Verifica se o sistema está fora do ar em manutenção
$ipManutencao = $intra->get_variavel('ipAdmin'); // ip isento da mensagem
$ipMaquina = $_SERVER['REMOTE_ADDR'];   // ip da máquina
# Define a senha padrão de acordo com o que está nas variáveis
define("SENHA_PADRAO", $intra->get_variavel('senhaPadrao'));

# Verifica a fase do programa
$fase = get('fase');

# Começa uma nova página
$page = new Page();

# Define as rotinas comemorativas que vão receber os fogos
$arrayFogos = ["parabens", "diaServidor", "diaMulher"];

# Fogos
if (in_array($fase, $arrayFogos)) {
    $page->set_cssExtra("
    <style>
    body {
        margin: 0;
        padding: 0;
        /* A linha overflow: hidden; foi removida para liberar a rolagem */
        font-family: sans-serif;
    }
    canvas {
        position: fixed; /* Mantém os fogos fixos na tela mesmo ao rolar */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 9999; /* Opcional: garante que os fogos apareçam por cima do conteúdo */
    }
    .btn-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
    }
</style>");
}

$page->iniciaPagina();

# Cabeçalho
if ($fase <> "diaServidor" AND $fase <> "diaMulher") {
    AreaServidor::cabecalho("Login do Sistema");
    br(2);
}

# Fogos corpo
if (in_array($fase, $arrayFogos)) {
    echo "
    <canvas id=\"canvasFogos\"></canvas>

    <script>
        const canvas = document.getElementById('canvasFogos');
        const ctx = canvas.getContext('2d');

        function ajustarCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        ajustarCanvas();
        window.addEventListener('resize', ajustarCanvas);

        // CORREÇÃO: Array iniciado corretamente
        let particulas = [];
        const cores = ['#FF5733', '#33FF57', '#3357FF', '#F3FF33', '#FF33F3', '#33FFF0', '#FFAF33'];

        class Particula {
            constructor(x, y, cor) {
                this.x = x;
                this.y = y;
                this.cor = cor;
                this.raio = Math.random() * 3 + 1;
                
                const angulo = Math.random() * Math.PI * 2;
                const velocidade = Math.random() * 6 + 2;
                
                this.vx = Math.cos(angulo) * velocidade;
                // CORREÇÃO: Linha limpa e sem erros de sintaxe
                this.vy = Math.sin(angulo) * velocidade;
                
                this.alpha = 1;
                this.gravidade = 0.08;
            }

            desenhar() {
                ctx.save();
                ctx.globalAlpha = this.alpha;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.raio, 0, Math.PI * 2);
                ctx.fillStyle = this.cor;
                ctx.fill();
                ctx.restore();
            }

            atualizar() {
                this.x += this.vx;
                this.y += this.vy;
                this.vy += this.gravidade;
                this.alpha -= 0.015;
            }
        }

        function criarExplosao(x, y) {
            const quantidade = 80;
            const corAleatoria = cores[Math.floor(Math.random() * cores.length)];
            for (let i = 0; i < quantidade; i++) {
                particulas.push(new Particula(x, y, corAleatoria));
            }
        }

        function criarExplosaoManual() {
            criarExplosao(window.innerWidth / 2, window.innerHeight / 2);
            setTimeout(() => criarExplosao(window.innerWidth * 0.2, window.innerHeight * 0.4), 200);
            setTimeout(() => criarExplosao(window.innerWidth * 0.8, window.innerHeight * 0.4), 400);
        }

        function animar() {
            // Limpa o canvas deixando-o transparente
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            particulas = particulas.filter(p => p.alpha > 0);

            particulas.forEach(particula => {
                particula.atualizar();
                particula.desenhar();
            });

            requestAnimationFrame(animar);
        }

        // Fogos automáticos a cada 800 milissegundos
        setInterval(() => {
            const xAleatorio = Math.random() * canvas.width;
            const yAleatorio = Math.random() * (canvas.height * 0.6);
            criarExplosao(xAleatorio, yAleatorio);
        }, 200);

        animar();
    </script>";
}

################################################################################

switch ($fase) {
    case "":
        # Limpando todas as sessions
        session_destroy();
        unset($_SESSION);

        # Login 
        $grid = new Grid("center");

        if ($intra->get_variavel('manutencao')) {
            $grid->abreColuna(10);
            calloutAlert("Somente usuários administradores podem acessar<br/>Aguarde alguns minutos e tente novamente", "Atenção!<br/>Sistema em Manutenção!", "center");
            br();
            $grid->fechaColuna();
        }

        $grid->abreColuna(10, 5, 4);

        $callout = new Callout("secondary");
        $callout->abre();

        $form = new Form('?fase=valida', 'login');

        # usuário
        $controle = new Input('usuario', 'texto', 'Usuário:', 1);
        $controle->set_size(20);
        $controle->set_linha(1);
        $controle->set_required(true);
        $controle->set_autofocus(true);
        $controle->set_tabIndex(1);
        $controle->set_placeholder('usuário');
        $controle->set_title('O nome do usuário');
        $form->add_item($controle);

        # senha
        $controle = new Input('senha', 'password', 'Senha:', 1);
        $controle->set_size(20);
        $controle->set_linha(2);
        $controle->set_required(true);
        $controle->set_tabIndex(2);
        $controle->set_title('A senha da intranet');
        $controle->set_placeholder('senha');
        $form->add_item($controle);

        # submit
        $controle = new Input('submit', 'submit');
        $controle->set_valor('Entrar');
        $controle->set_linha(3);
        $controle->set_tabIndex(3);
        $controle->set_accessKey('E');
        $form->add_item($controle);

        $form->show();
        $callout->fecha();

        # Verifica se tem imagem comemorativa
        $dia = date("d");
        $mes = date("m");

        if (($mes == 12) AND ($dia < 26)) {
            $imagem = new Imagem(PASTA_FIGURAS . 'natal.gif', 'Boas Festas Servidor', 603, 143);
            $imagem->show();
        }

        $grid->fechaColuna();
        $grid->fechaGrid();

        # Mensagem do Dia 
        br();
        $grid = new Grid("center");
        $grid->abreColuna(8);

        span("Mensagem do dia:", "mensagemLabel");
        $callout = new Callout("success");
        $callout->abre();
        $mensagem = $intra->escolheMensagem();
        P('"' . $mensagem . '"', "mensagem");
        $callout->fecha();

        $grid->fechaColuna();
        $grid->fechaGrid();
        break;

    ################################################################################

    case "valida":
        # Valida o Login
        # Pega os dados digitados
        $usuario = post('usuario');
        $senha = post('senha');

        # Verifica o Login
        $verifica = $intra->verificaLogin($usuario, $senha);

        # Verifica a senha
        switch ($verifica) {
            case 0: // Login Incorreto: Usuário inexistente
                # Informa o Erro
                alert('Login Incorreto!');

                # Grava no log a atividade
                $intra->registraLog(null, date("Y-m-d H:i:s"), 'Tentativa de Login com usuário (' . $usuario . ') inexistente (' . BROWSER_NAME . ' ' . BROWSER_VERSION . ' - ' . SO . ')', null, null, 5);

                # Retorna a página de login
                loadPage('login.php');
                break;

            ##################################################

            case 1: // Login Incorreto: Senha nula no servidor
                # Informa o Erro
                alert('Login Incorreto!');

                # Grava no log a atividade
                $intra->registraLog(null, date("Y-m-d H:i:s"), 'Tentativa de Login com usuário (' . $usuario . ') bloqueado (com senha nula) no servidor (' . BROWSER_NAME . ' ' . BROWSER_VERSION . ' - ' . SO . ')', null, null, 5);

                # Retorna a página de login
                loadPage('login.php');
                break;

            ##################################################

            case 2: // Login Incorreto: Senha Errada
                # Informa o Erro
                alert('Login Incorreto!');

                # Grava no log a atividade
                $intra->registraLog(null, date("Y-m-d H:i:s"), 'Tentativa de Login com usuário (' . $usuario . ') e com senha errada. (' . BROWSER_NAME . ' ' . BROWSER_VERSION . ' - ' . SO . ')', null, null, 5);

                # Retorna a página de login
                loadPage('login.php');
                break;

            ##################################################

            case 3: // Login Correto
                # Pega o idUsuario
                $idUsuario = $intra->get_idUsuario($usuario);

                # Pega o idServidor
                $idServidor = $intra->get_idServidor($idUsuario);

                # Grava o último acesso
                $intra->gravar('ultimoAcesso', date("Y-m-d H:i:s"), $idUsuario, 'tbusuario', 'idUsuario', false);

                # Grava no log a atividade
                $intra->registraLog($idUsuario, date("Y-m-d H:i:s"), 'Login (' . BROWSER_NAME . ' ' . BROWSER_VERSION . ' - ' . SO . ')');

                # Verifica se o servidor está aniversariando hoje
                if ($pessoal->aniversariante($idServidor)) {
                    loadPage('?fase=parabens');
                } elseif (date("m-d") == "10-28") {
                    loadPage('?fase=diaServidor');
                } elseif (date("m-d") == "03-08") {
                    loadPage('?fase=diaMulher');
                } else {
                    # Redireciona para o sistema GRH
                    if (Verifica::acesso($idUsuario, [2, 12])) {
                        loadPage("../../grh/grhSistema/grh.php");
                    }

                    # Redireciona para a área do Servidor
                    if (Verifica::acesso($idUsuario, [1, 3, 9, 10, 11])) {
                        loadPage('areaServidor.php');
                    }
                }
                break;

            ##################################################

            case 4: // Senha Padrão
                # altera a senha de início
                alert('Sua Senha não é Segura !! Favor Alterar !');

                # Pega o idUsuario desse servidor
                $idUsuario = $intra->get_idUsuario($usuario);

                # Pega o idServidor
                $idServidor = $intra->get_idServidor($idUsuario);

                # Grava o último acesso
                $intra->gravar('ultimoAcesso', date("Y-m-d H:i:s"), $idUsuario, 'tbusuario', 'idUsuario', false);

                # Grava no log a atividade        
                $intra->registraLog($idUsuario, date("Y-m-d H:i:s"), 'Login com senha padrão (' . BROWSER_NAME . ' ' . BROWSER_VERSION . ' - ' . SO . ')');

                loadPage('trocarSenha.php?');
                break;

            ##################################################

            case 5: // Computador Não Autorizado
                # Informa o Erro
                alert('Este Computador não está autorizado a acessar o sistema! Entre em contato com o administrador do sistema.');

                # Grava no log a atividade
                $intra->registraLog(null, date("Y-m-d H:i:s"), 'Tentativa de Login com usuário (' . $usuario . ') em Computador não autorizado (' . BROWSER_NAME . ' ' . BROWSER_VERSION . ' - ' . SO . ')', null, null, 5);

                # Retorna a página de login
                loadPage('login.php');
                break;

            ##################################################

            case 6: // Usuário não ativo
                # Informa o Erro
                alert('O Usuário não é servidor ativo da Uenf.');

                # Grava no log a atividade
                $intra->registraLog(null, date("Y-m-d H:i:s"), 'Tentativa de Login com usuário (' . $usuario . ') não ativo na Uenf (' . BROWSER_NAME . ' ' . BROWSER_VERSION . ' - ' . SO . ')', null, null, 5);

                # Retorna a página de login
                loadPage('login.php');
                break;

            ##################################################
        }
        break;

    ################################################################################

    case "parabens":
        # Acesso ao sistema GRH
        $pagina = 'areaServidor.php';
        if (Verifica::acesso($idUsuario, [2, 12])) {
            $pagina = '../../grh/grhSistema/grh.php';
        } else {
            $pagina = 'areaServidor.php';
        }

        br();
        $grid = new Grid("center");
        $grid->abreColuna(6);

        # Define as várias imagens de parabéns que existem no diretório
        $imagens = [
            "parabens.jpg",
            "parabens1.gif",
            "parabens2.gif",
            "parabens3.gif",
            "parabens4.gif",
            "parabens5.gif",
            "parabens6.gif",
            "parabens7.gif"];

        # Escolhe aleatoriamente uma delas
        $escolhida = array_rand($imagens);

        $img = new Imagem(PASTA_FIGURAS . $imagens[$escolhida], "Parabéns Servidor", '100%', '100%');
        $img->show();

        $grid->fechaColuna();
        $grid->fechaGrid();

        br(2);

        $div = new Div("center");
        $div->abre();
        p('<h5>Querido Servidor, Feliz Aniversário !</h5>', 'center', 'center');
        p('A GRH te deseja paz, alegrias, felicidades e muito sucesso.');
        $div->fecha();
        br(2);

        # Botão
        $grid = new Grid();
        $grid->abreColuna(12);
        $menu = new MenuBar();

        # Botão 
        $linkBotaoVoltar = new Button('Continua');
        $linkBotaoVoltar->set_title('Continua');
        $linkBotaoVoltar->set_url($pagina);
        $menu->add_link($linkBotaoVoltar, "right");

        $menu->show();

        # Grava no log a atividade
        $intra->registraLog($idUsuario, date("Y-m-d H:i:s"), 'Recebeu os parabéns do sistema pelo aniversário.', null, null, 7);

        $grid->fechaColuna();
        $grid->fechaGrid();
        break;

    ################################################################################

    case "diaServidor":
        # Acesso ao sistema GRH
        $pagina = 'areaServidor.php';
        if (Verifica::acesso($idUsuario, 2)) {
            $pagina = '../../grh/grhSistema/grh.php';
        } else {
            $pagina = 'areaServidor.php';
        }

        # Define a largura da página
        $grid = new Grid();
        $grid->abreColuna(12);
        br();
        $menu = new MenuBar();

        # Botão 
        $linkBotaoVoltar = new Button('Continua');
        $linkBotaoVoltar->set_title('Continua');
        $linkBotaoVoltar->set_url($pagina);
        $menu->add_link($linkBotaoVoltar, "right");

        $menu->show();

        $div = new Div("center");
        $div->abre();
        p('<h5>Querido servidor, parabéns pelo</h5>');

        $img = new Imagem(PASTA_FIGURAS . "servidor.jpg", "Parabéns Servidor", '500', '500');
        $img->show();

        $div->fecha();
        br();

        p('O serviço público é uma vocação, é trabalhar todos os dias por uma sociedade melhor !!<br>A GRH te deseja um Feliz Dia do Servidor Público.', 'center');

        # Grava no log a atividade
        $intra->registraLog($idUsuario, date("Y-m-d H:i:s"), 'Recebeu os parabéns do sistema pelo dia do servidor público.', null, null, 7);

        $grid->fechaColuna();
        $grid->fechaGrid();
        break;

    ################################################################################

    case "diaMulher":
        # Acesso ao sistema GRH
        $pagina = 'areaServidor.php';
        if (Verifica::acesso($idUsuario, 2)) {
            $pagina = '../../grh/grhSistema/grh.php';
        } else {
            $pagina = 'areaServidor.php';
        }

        # Define a largura da página
        $grid = new Grid();
        $grid->abreColuna(12);
        br();
        $menu = new MenuBar();

        # Botão 
        $linkBotaoVoltar = new Button('Continua');
        $linkBotaoVoltar->set_title('Continua');
        $linkBotaoVoltar->set_url($pagina);
        $menu->add_link($linkBotaoVoltar, "right");

        $menu->show();

        $div = new Div("center");
        $div->abre();
        $img = new Imagem(PASTA_FIGURAS . "mulher.jpg", "Parabéns Mulheres", '500', '500');
        $img->show();

        $div->fecha();
        br();

        p('⁠No Dia Internacional da Mulher, eu troco os parabéns, as flores e os chocolates<br/>por respeito, igualdade e voz, todos os outros dias do ano.', 'center');

        # Grava no log a atividade
        $intra->registraLog($idUsuario, date("Y-m-d H:i:s"), 'Recebeu os parabéns do sistema pelo dia das mulheres.', null, null, 7);

        $grid->fechaColuna();
        $grid->fechaGrid();
        break;

    ################################################################################
}

# Termina a Página
$page->terminaPagina();

