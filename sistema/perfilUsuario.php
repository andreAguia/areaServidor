<?php
/**
 * Perfil do Usuário
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase','menu'); # Qual a fase
    $metodo = get('sistema');	# Qual o sistema. Usado na rotina de Documentação

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
    $grid = new Grid();
    $grid->abreColuna(12);
    
    # Cria um menu
    $menu1 = new MenuBar();
    
    # Sair da Área do Servidor
    $linkBotao1 = new Link("Voltar",'../../grh/grhSistema/grh.php');
    $linkBotao1->set_class('button');
    $linkBotao1->set_title('Voltar ao Sistema de Pessoal');
    $linkBotao1->set_accessKey('V');
    $menu1->add_link($linkBotao1,"left");
    $menu1->show();
    
    titulo('Perfil do Usuário');
    br();
    
    # Exibe os dados do Servidor 
    AreaServidor::listaDadosUsuario($idUsuario);
    
    
    $tamanhoImage = 70;

    $menu = new MenuGrafico(3);
    
    $botao = new BotaoGrafico();
    $botao->set_label('Alterar Senha');
    $botao->set_url('trocarSenha.php');
    $botao->set_image(PASTA_FIGURAS.'alteraSenha.png',$tamanhoImage,$tamanhoImage);
    $botao->set_title('Alterar Senha');
    #$botao->set_accesskey('S');
    $menu->add_item($botao);
    $menu->show();       
    
    # Exibe o rodapé da página
    br();
    AreaServidor::rodape($idUsuario);
    
    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}

