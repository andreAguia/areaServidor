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
#$page->set_bodyOnLoad("abreDivId('divMensagemAguarde');");
$page->iniciaPagina();

# Cabeçalho
AreaServidor::cabecalho("Login do Sistema");
br(2);

switch ($fase)
{
    case "":
        # Limpando todas as sessions
        session_destroy();
        unset( $_SESSION );
        
        # Login 
        $grid = new Grid("center");
        $grid->abreColuna(10,5);
        
        $callout = new Callout();
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

        $grid->fechaColuna();
        $grid->fechaGrid();        
        
        # Mensagem do Dia 
        br(6);
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

    case "valida":
        # Valida o Login

        # Pega os dados digitados
        $usuario = post('usuario');
        $senha = post('senha');        
        
        # Verifica o Login
        $verifica = $intra->verificaLogin($usuario,$senha);
                
        # Pega o ip da máquina que fez login
        $ip = getenv("REMOTE_ADDR");

        # Verifica a senha
        switch ($verifica)
        {
            case 0: // Login Incorreto: Usuário inexistente
                # Informa o Erro
                alert('Login Incorreto!');
                
                # Grava no log a atividade
                $intra->registraLog(NULL,date("Y-m-d H:i:s"),'Tentativa de Login com usuário ('.$usuario.') inexistente ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',NULL,NULL,5);
                
                # Envia um email informando
                if($intra->get_variavel("emiteEmailAlerta")){
                    
                    $assunto = "Alerta de Acesso ao Sistema de Pessoal";
                    
                    $mensagem = date("d-m-Y H:i:s");
                    $mensagem .= "<br/>";
                    $mensagem .= "Tentativa de Login com usuário ($usuario) inexistente.";
                    $mensagem .= "<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Porque estou recebendo esste email?";
                    $mensagem .= "<br/><br/>";
                    $mensagem .= "Você está recebendo esse email por estar cadastrado <br/>";
                    $mensagem .= "como administrador no sistema de Pessoal da GRH da UENF.<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Qualquer dúvida entre em comtato com a GRH.";
                    $mensagem .= "<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Não responda esse email.";
                    
                    $mail = new EnviaEmail($assunto, $mensagem);
                    $mail->set_para("alat@uenf.br");
                    $mail->set_deNome("Sistema de Pessoa - Alerta");
                    $mail->envia();
                }
                    
                loadPage('login.php');
                break;
            
            case 1: // Login Incorreto: Senha nula no servidor
                # Informa o Erro
                alert('Login Incorreto!');
                
                # Grava no log a atividade
                $intra->registraLog(NULL,date("Y-m-d H:i:s"),'Tentativa de Login com usuário ('.$usuario.') bloqueado (com senha nula) no servidor ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',NULL,NULL,5);
                
                # Envia um email informando
                if($intra->get_variavel("emiteEmailAlerta")){
                    
                    $assunto = "Alerta de Acesso ao Sistema de Pessoal";
                    
                    $mensagem = date("d-m-Y H:i:s");
                    $mensagem .= "<br/>";
                    $mensagem .= "Tentativa de Login com usuário ($usuario) bloqueado (com senha nula) no servidor.";
                    $mensagem .= "<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Porque estou recebendo esste email?";
                    $mensagem .= "<br/><br/>";
                    $mensagem .= "Você está recebendo esse email por estar cadastrado <br/>";
                    $mensagem .= "como administrador no sistema de Pessoal da GRH da UENF.<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Qualquer dúvida entre em comtato com a GRH.";
                    $mensagem .= "<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Não responda esse email.";
                    
                    $mail = new EnviaEmail($assunto, $mensagem);
                    $mail->set_para("alat@uenf.br");
                    $mail->set_deNome("Sistema de Pessoa - Alerta");
                    $mail->envia();
                }
                
                loadPage('login.php');
                break;
            
            case 2: // Login Incorreto: Senha Errada
                # Informa o Erro
                alert('Login Incorreto!');
                
                # Grava no log a atividade
                $intra->registraLog(NULL,date("Y-m-d H:i:s"),'Tentativa de Login com usuário ('.$usuario.') e com senha errada. ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',NULL,NULL,5);
                
                # Envia um email informando
                if($intra->get_variavel("emiteEmailAlerta")){
                    
                    $assunto = "Alerta de Acesso ao Sistema de Pessoal";
                    
                    $mensagem = date("d-m-Y H:i:s");
                    $mensagem .= "<br/>";
                    $mensagem .="Tentativa de Login com usuário ($usuario) e com senha errada.";
                    $mensagem .= "<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Porque estou recebendo esste email?";
                    $mensagem .= "<br/><br/>";
                    $mensagem .= "Você está recebendo esse email por estar cadastrado <br/>";
                    $mensagem .= "como administrador no sistema de Pessoal da GRH da UENF.<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Qualquer dúvida entre em comtato com a GRH.";
                    $mensagem .= "<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Não responda esse email.";
                    
                    $mail = new EnviaEmail($assunto, $mensagem);
                    $mail->set_para("alat@uenf.br");
                    $mail->set_deNome("Sistema de Pessoa - Alerta");
                    $mail->envia();
                }
                
                loadPage('login.php');
                break;

            Case 3: // Login Correto
                # Pega o idUsuario
                $idUsuario = $intra->get_idUsuario($usuario);
                
                # Pega o idServidor
                $idServidor = $intra->get_idServidor($idUsuario);
                
                # Grava o último acesso
                $intra->gravar('ultimoAcesso',date("Y-m-d H:i:s"),$idUsuario,'tbusuario','idUsuario',FALSE);

                # Grava no log a atividade
                $intra->registraLog($idUsuario,date("Y-m-d H:i:s"),'Login ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')');

                # Executa o backup
                if($intra->get_variavel("backupAutomatico")){
                    $backupData = $intra->get_variavel("backupData");   // Verifica a data do último backup
                    $backupPasta = str_replace("/","\\",$intra->get_variavel("backupPasta"));   // Pega o caminho do banco de dados invertendo a barra.
                    $hoje = date("d/m/Y");                              // Pega a data de hoje

                    # Verifica se foi feito backup hoje
                    if($hoje <> $backupData){
                        exec("backup.bat C:\\".$backupPasta);   // Executa o backup
                        $intra->set_variavel("backupData",$hoje);   // Atualiza a data do último backup

                        # Grava no log a atividade
                        $intra->registraLog($idUsuario,date("Y-m-d H:i:s"),'Backup automático realizado',NULL,NULL,6);
                    }
                }
                
                # Verifica se o servidor está aniversariando hoje
                if($pessoal->aniversariante($idServidor)){
                    loadPage('?fase=parabens');
                }else{
                    loadPage('areaServidor.php');
                }
                break;
            
            Case 4: // Senha Padrão
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
            
            case 5: // Computador Não Autorizado
                # Informa o Erro
                alert('Este Computador não está autorizado a acessar o sistema! Entre em contato com o administrador do sistema.');
                
                # Grava no log a atividade
                $intra->registraLog(NULL,date("Y-m-d H:i:s"),'Tentativa de Login com usuário ('.$usuario.') em Computador não autorizado ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',NULL,NULL,5);
                
                # Envia um email informando
                if($intra->get_variavel("emiteEmailAlerta")){
                    
                    $assunto = "Alerta de Acesso ao Sistema de Pessoal";
                    
                    $mensagem = date("d-m-Y H:i:s");
                    $mensagem .= "<br/>";
                    $mensagem .="Tentativa de Login com usuário ($usuario) em computador não autorizado.";
                    $mensagem .= "<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Porque estou recebendo esste email?";
                    $mensagem .= "<br/><br/>";
                    $mensagem .= "Você está recebendo esse email por estar cadastrado <br/>";
                    $mensagem .= "como administrador no sistema de Pessoal da GRH da UENF.<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Qualquer dúvida entre em comtato com a GRH.";
                    $mensagem .= "<br/>";
                    $mensagem .= str_repeat("-", 80)."<br/>";
                    $mensagem .= "Não responda esse email.";
                    
                    $mail = new EnviaEmail($assunto, $mensagem);
                    $mail->set_para("alat@uenf.br");
                    $mail->set_deNome("Sistema de Pessoa - Alerta");
                    $mail->envia();
                }
                
                loadPage('login.php');
                break;
        }
        break;

    Case "parabens":
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
        
        $img = new Imagem(PASTA_FIGURAS."parabens.jpg","Parabéns Servidor",'100%','100%');
        $img->show();        
        
        $grid->fechaColuna();
        $grid->fechaGrid();
        
        br(2);
        
        $div = new Div("center");
        $div->abre();
        p('<h5>Querido Servidor, Feliz Aniversário !</h5>','center','center');
        p('A DGA te deseja paz, alegrias, felicidades e muito sucesso.');
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

        $grid->fechaColuna();
        $grid->fechaGrid();
        break;
}

# Termina a Página
$page->terminaPagina();
}
