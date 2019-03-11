<?php
/**
 * Inicial da área do Servidor
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Conecta ao Banco de Dados
$intra = new Intra();
$pessoal = new Pessoal();

# Verifica se o sistema está fora do ar em manutenção
$ipManutencao = $intra->get_variavel('ipAdmin');	// ip isento da mensagem
$ipMaquina = $_SERVER['REMOTE_ADDR'];			// ip da máquina

if (($intra->get_variavel('manutencao')) AND ($ipManutencao <> $ipMaquina)){
    loadPage("manutencao.php");    
}else{

# Define a senha padrão de acordo com o que está nas variáveis
define("SENHA_PADRAO",$intra->get_variavel('senhaPadrao'));

# Verifica a fase do programa
$fase = get('fase');

# Começa uma nova página
$page = new Page();
$page->iniciaPagina();

# Cabeçalho
AreaServidor::cabecalho("Login do Sistema");
br(2);

################################################################################

switch ($fase){
    case "":
        # Limpando todas as sessions
        session_destroy();
        unset( $_SESSION );
        
        # Login 
        $grid = new Grid("center");
        $grid->abreColuna(10,5);
        
        $callout = new Callout("secondary");
        $callout->abre();
        $form = new Form('?fase=valida','login');        
        
            # usuário
            $controle = new Input('usuario','texto','Usuário:',1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);       
            $controle->set_tabIndex(1);
            $controle->set_placeholder('usuário');
            $controle->set_title('O nome do usuário');
            $form->add_item($controle);
            
            # senha
            $controle = new Input('senha','password','Senha:',1);
            $controle->set_size(20);
            $controle->set_linha(2);
            $controle->set_required(TRUE);
            $controle->set_tabIndex(2);
            $controle->set_title('A senha da intranet');
            $controle->set_placeholder('senha');
            $form->add_item($controle);

            # submit
            $controle = new Input('submit','submit');
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
        
        if(($mes == 12) AND ($dia < 26)){
            $imagem = new Imagem(PASTA_FIGURAS.'natal.gif','Boas Festas Servidor',603,143);
            $imagem->show();
            $pulo = 1;
        }else{
            $pulo = 6;
        }
                
        $grid->fechaColuna();
        $grid->fechaGrid();        
        
        # Mensagem do Dia 
        br($pulo);
        $grid = new Grid("center");
        $grid->abreColuna(8); 
        
        span("Mensagem do dia:","mensagemLabel");
        $callout = new Callout("success");
        $callout->abre();
            $mensagem = $intra->escolheMensagem();
            P('"'.$mensagem.'"',"mensagem");
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
        $verifica = $intra->verificaLogin($usuario,$senha);

        # Verifica a senha
        switch ($verifica){
            case 0: // Login Incorreto: Usuário inexistente
                # Informa o Erro
                alert('Login Incorreto!');
                
                # Grava no log a atividade
                $intra->registraLog(NULL,date("Y-m-d H:i:s"),'Tentativa de Login com usuário ('.$usuario.') inexistente ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',NULL,NULL,5);
                
                # Retorna a página de login
                loadPage('login.php');
                break;
            
            ##################################################
            
            case 1: // Login Incorreto: Senha nula no servidor
                # Informa o Erro
                alert('Login Incorreto!');
                
                # Grava no log a atividade
                $intra->registraLog(NULL,date("Y-m-d H:i:s"),'Tentativa de Login com usuário ('.$usuario.') bloqueado (com senha nula) no servidor ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',NULL,NULL,5);
                                
                # Retorna a página de login
                loadPage('login.php');
                break;
            
            ##################################################
            
            case 2: // Login Incorreto: Senha Errada
                # Informa o Erro
                alert('Login Incorreto!');
                
                # Grava no log a atividade
                $intra->registraLog(NULL,date("Y-m-d H:i:s"),'Tentativa de Login com usuário ('.$usuario.') e com senha errada. ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',NULL,NULL,5);
                                
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
                $intra->gravar('ultimoAcesso',date("Y-m-d H:i:s"),$idUsuario,'tbusuario','idUsuario',FALSE);

                # Grava no log a atividade
                $intra->registraLog($idUsuario,date("Y-m-d H:i:s"),'Login ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')');

                # Verifica a data do último backup
                $backupData = $intra->get_variavel("backupData");

                # Pega a data de hoje
                $hoje = date("d/m/Y");       
                
                # Executa o backup
                if(($intra->get_variavel("backupAutomatico")) AND ($hoje <> $backupData)){
                    loadPage('?fase=backup1');
                }else{                
                    # Verifica se o servidor está aniversariando hoje
                    if($pessoal->aniversariante($idServidor)){
                        loadPage('?fase=parabens');
                    }else{
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
                $intra->gravar('ultimoAcesso',date("Y-m-d H:i:s"),$idUsuario,'tbusuario','idUsuario',FALSE);

                # Grava no log a atividade        
                $intra->registraLog($idUsuario,date("Y-m-d H:i:s"),'Login com senha padrão ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')');
                
                loadPage('trocarSenha.php?'); 
                break;
            
            ##################################################
            
            case 5: // Computador Não Autorizado
                # Informa o Erro
                alert('Este Computador não está autorizado a acessar o sistema! Entre em contato com o administrador do sistema.');
                
                # Grava no log a atividade
                $intra->registraLog(NULL,date("Y-m-d H:i:s"),'Tentativa de Login com usuário ('.$usuario.') em Computador não autorizado ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',NULL,NULL,5);
                
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
        if(Verifica::acesso($idUsuario,2)){
            $pagina = '../../grh/grhSistema/grh.php';
        }

        if(Verifica::acesso($idUsuario,1)){
            $pagina = 'areaServidor.php';
        }
        
        br();
        $grid = new Grid("center");
        $grid->abreColuna(6);
        
        # Define as várias imagens de parabéns que existem no diretório
        $imagens = array("parabens.jpg",
                         "parabens1.gif",
                         "parabens2.gif",
                         "parabens3.gif",
                         "parabens4.gif",
                         "parabens5.gif",
                         "parabens6.gif",
                         "parabens7.gif");
        
        # Escolhe aleatoriamente uma delas
        $escolhida = array_rand($imagens);
        
        $img = new Imagem(PASTA_FIGURAS.$imagens[$escolhida],"Parabéns Servidor",'100%','100%');
        $img->show();        
        
        $grid->fechaColuna();
        $grid->fechaGrid();
        
        br(2);
        
        $div = new Div("center");
        $div->abre();
        p('<h5>Querido Servidor, Feliz Aniversário !</h5>','center','center');
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
        $linkBotaoVoltar->set_accessKey('C');
        $menu->add_link($linkBotaoVoltar,"right");

        $menu->show();
        
        # Grava no log a atividade
        $intra->registraLog($idUsuario,date("Y-m-d H:i:s"),'Recebeu os parabéns do sistema pelo aniversário.',NULL,NULL,7);

        $grid->fechaColuna();
        $grid->fechaGrid();
        break;
        
################################################################################
        
    case "backup1":
        
        aguarde();
        br();

        # Limita a tela
        $grid1 = new Grid("center");
        $grid1->abreColuna(10);
            
            p("Você foi o primeiro a fazer login no sistema hoje!","center");
            p("O sistema está programado a fazer backup no primeiro login do dia!","center");
            p("Aguarde um pouco. Isso não irá demorar!!","center");
            p("Fazendo o backup ...","center");
            
            $div = new Div("center");
            $div->abre();
            
            $figura = new Imagem(PASTA_FIGURAS.'cafe.png','Vê se faz café de macho !!',100,100);
            $figura->show();
            
            $div->fecha();
            
            br(); 
            p("Aproveite esse instante para fazer o café !!","center");
        $grid1->fechaColuna();
        $grid1->fechaGrid();
       
        loadPage('?fase=backup2');
        break;
    
    case "backup2":

        # Realiza backup
        $backup = new BackupBancoDados($idUsuario);
        $backup->executa();
        
        # Verifica se o servidor está aniversariando hoje
        if($pessoal->aniversariante($idServidor)){
            loadPage('?fase=parabens');
        }else{
            loadPage('areaServidor.php');
        }
        break;
}

# Termina a Página
$page->terminaPagina();
}
