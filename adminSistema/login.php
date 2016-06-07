<?php
/**
 * Inicial da área do Servidor
 *  
 * By Alat
 */

# Configurações
include ("_config.php");

# Limpa as sessions
set_session('intranet');            // Zera a session do usuário logado
set_session('matriculaGrh');        // Zera a session da pesquisa do sistema grh
set_session('sessionCpf');          // Zera a session usada na rotina de inclus�o de servidor do sistema grh

# Define a senha padrão de acordo com o que está nas variáveis
$config = new Intra();
define("SENHA_PADRAO",$config->get_variavel('senha_padrao'));

# Verifica a fase do programa
$fase = get('fase');

# Começa uma nova página
$page = new Page();
#$page->set_bodyOnLoad("abreDivId('divMensagemAguarde');");
$page->iniciaPagina();

# Cabeçalho
AreaServidor::cabecalho("Login do Sistema");
br(2);

echo '<div class="row align-center">';
echo '<div class="column small-5">';

switch ($fase)
{
    case "":
        $callout = new Callout();
        $callout->abre();
        $form = new Form('?fase=valida','login');        
        
            # usuário
            $controle = new Input('usuario','numero','Matrícula (sem o dígito):',1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_required(true);
            $controle->set_autofocus(true);       
            $controle->set_tabIndex(1);
            $controle->set_placeholder('matrícula');
            $controle->set_title('A matrícula do servidor sem o dígito verificador');
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
                 
        $servidor = new Pessoal();
        $verifica = $servidor->verificaLogin($usuario,$senha);  # pega situação do servidor
        $diasAusentes = $servidor->get_diasAusentes($usuario);	# pega número de dias ausentes do servidor

        # Exibe uma mensagem de aguarde
        #Visual::mensagemAguarde();

        # Verifica a senha
        switch ($verifica)
        {
            case 0: // Login Correto
                # Grava o último acesso
                $servidor->gravar('ult_acesso',date("Y-m-d H:i:s"),$usuario,'tbfuncionario','matricula',false);

                # Pega o ip da máquina que fez login
                #$ip = getenv("REMOTE_ADDR");

                # Grava no log a atividade
                $log = new Intra();
                $log->registraLog($usuario,date("Y-m-d H:i:s"),'Login ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',null,null,1);

                # Verifica se o servidor está aniversariando hoje
                if($servidor->aniversariante($usuario))
                    loadPage('?fase=parabens');
                else
                    loadPage('../../grh/grhSistema/grh.php');                
                break;

            Case 1: // Matrícula Inválida
                # Informa o Erro
                alert('Matrícula Inválida!');
                loadPage('login.php');
                break;

            Case 2: // Servidor Inativo
                # Informa o Erro
                alert('Servidor Inativo no Sistema. Procure a GRH!');
                loadPage('login.php');
                break;

            Case 3: // Matrícula Inexistente
                # Informa o Erro
                alert('Servidor Inexistente!');
                loadPage('login.php');
                break;

            Case 4: // Usuário Bloqueado
                # Informa o Erro
                alert('Servidor Bloqueado!!. Procure a GRH!');
                loadPage('login.php');
                break;

            Case 5: // Senha Errada
                # Informa o Erro
                alert('Usuário ou Senha Incorreta!!. Procure a GRH!');
                loadPage('login.php');
                break;

            Case 6: // Senha Padrão
                # altera a senha de início
                alert('Sua Senha não é Segura !! Favor Alterar !');
                
                # Pega o ip da máquina que fez login
                #$ip = getenv("REMOTE_ADDR");
                
                # Grava o último acesso
                $servidor->gravar('ult_acesso',date("Y-m-d H:i:s"),$usuario,'tbfuncionario','matricula',false);

                # Grava no log a atividade
                $log = new Intra();                
                $log->registraLog($usuario,date("Y-m-d H:i:s"),'Login com senha padrão ('.BROWSER_NAME.' '.BROWSER_VERSION.' - '.SO.')',null,null,1);

                loadPage('areaServidor.php?fase=trocaSenhaSV&metodo=editar');
                break;

            Case 7: // Senha Padrão Bloqueada por inatividade
                # Informa o Erro
                alert('Usuário Bloqueado!\nA Senha Padrão é Temporária!\nA Demora na Alteração Gera Bloqueio do Usuário!\nProcure a GTI!');
                loadPage('login.php');
                
                $servidor->set_senhaNull($usuario,false);
                break;

            Case 8: // Usuário Bloquaeado por inatividade
                # Informa o Erro
                alert('Usuário Bloqueado por inatividade! Procure a GTI!');
                loadPage('login.php');
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

echo '</div>';
echo '</div>';

# Termina a Página
$page->terminaPagina();
