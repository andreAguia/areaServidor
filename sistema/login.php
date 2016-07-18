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

# Define a senha padrão de acordo com o que está nas variáveis
define("SENHA_PADRAO",$intra->get_variavel('senha_padrao'));

# Verifica a fase do programa
$fase = get('fase');

# Começa uma nova página
$page = new Page();
#$page->set_bodyOnLoad("abreDivId('divMensagemAguarde');");
$page->iniciaPagina();

# Cabeçalho
AreaServidor::cabecalho("Login do Sistema");
br(2);

# Login 
$grid = new Grid("center");
$grid->abreColuna(5);

switch ($fase)
{
    case "":
        $callout = new Callout();
        $callout->abre();
        $form = new Form('?fase=valida','login');        
        
            # usuário
            $controle = new Input('usuario','texto','Usuário:',1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_required(true);
            $controle->set_autofocus(true);       
            $controle->set_tabIndex(1);
            $controle->set_placeholder('usuário');
            $controle->set_title('O nome do usuário');
            $form->add_item($controle);
            
            # senha
            $controle = new Input('senha','password','Senha:',1);
            $controle->set_size(20);
            $controle->set_linha(2);
            $controle->set_required(true);
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
        break;

    case "valida":
        # Valida o Login

        # Pega os dados digitados
        $usuario = post('usuario');
        $senha = post('senha');        
        
        # Verifica o Login
        $verifica = $intra->verificaLogin($usuario,$senha);        
        
        #$diasAusentes = $intra->get_diasAusentes($usuario);	# pega número de dias ausentes do servidor

        # Exibe uma mensagem de aguarde
        #Visual::mensagemAguarde();

        # Verifica a senha
        switch ($verifica)
        {
            case 0: // Login Incorreto
                # Informa o Erro
                alert('Login Incorreto!');
                loadPage('login.php');
                break;

            Case 1: // Login Correto
                # Pega o ip da máquina que fez login
                $ip = getenv("REMOTE_ADDR");
                
                # Pega o idUsuario desse servidor
                $idUsuario = $intra->get_idUsuario($usuario);
                
                # Pega o idServidor
                $idServidor = $intra->get_idServidor($idUsuario);
                
                # Grava o último acesso
                $intra->gravar('ultimoAcesso',date("Y-m-d H:i:s"),$idUsuario,'tbusuario','idUsuario',false);

                # Grava no log a atividade
                $intra->registraLog($idUsuario,date("Y-m-d H:i:s"),'Login ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',null,null,0,$idServidor);

                # Acesso ao sistema GRH
                $pagina = 'areaServidor.php';
                if(Verifica::acesso($idUsuario,2)){
                    $pagina = '../../grh/grhSistema/grh.php';
                }
                
                if(Verifica::acesso($idUsuario,1)){
                    $pagina = 'areaServidor.php';
                }
                
                # Verifica se o servidor está aniversariando hoje
                #if($servidor->aniversariante($usuario))
                 #   loadPage('?fase=parabens');
                #else
                    loadPage($pagina);                
                break;
            
            Case 2: // Senha Padrão
                # altera a senha de início
                alert('Sua Senha não é Segura !! Favor Alterar !');
                
                # Pega o ip da máquina que fez login
                $ip = getenv("REMOTE_ADDR");
                
                # Pega o idUsuario desse servidor
                $idUsuario = $intra->get_idUsuario($usuario);
                
                # Pega o idServidor
                $idServidor = $intra->get_idServidor($idUsuario);
                
                # Grava o último acesso
                $intra->gravar('ultimoAcesso',date("Y-m-d H:i:s"),$idUsuario,'tbusuario','idUsuario',false);

                # Grava no log a atividade        
                $intra->registraLog($idUsuario,date("Y-m-d H:i:s"),'Login com senha padrão ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',null,null,0,$idServidor);
                
                loadPage('trocarSenha.php'); 
                break;
        }
        break;

    Case "parabens":
        br();
        $img = new Imagem(PASTA_FIGURAS."parabens.jpg","Parabéns Servidor",300,100);
        $img->show();
        br(2);
        $msg = '<h5>Querido Servidor, Feliz Aniversário !</h5><br/>A DGA te deseja paz, alegrias, felicidades e muito sucesso.';
        $alerta = new Alert($msg,"secondary");
        $alerta->set_page('areaServidor.php');
        $alerta->show();        
        break;
}

$grid->fechaColuna();
$grid->fechaGrid();

# Termina a Página
$page->terminaPagina();
