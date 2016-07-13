<?php
/**
 * Área do Servidor
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
    
    # Verifica se tem acesso ao sistema de grh
    if(Verifica::acesso($idUsuario,2)){
        botaoVoltar('../../grh/grhSistema/grh.php');
    }
    
    # Exibe os dados do Servidor    
    Grh::listaDadosServidor($intra->get_idServidor($idUsuario));

    titulo('Área do Servidor');
    br();
    $tamanhoImage = 70;

    $menu = new MenuGrafico(1);
    
    $botao = new BotaoGrafico();
    $botao->set_label('Alterar Senha');
    $botao->set_url('trocarSenha.php');
    $botao->set_image(PASTA_FIGURAS.'senha.png',$tamanhoImage,$tamanhoImage);
    $botao->set_title('Alterar Senha');
    #$botao->set_accesskey('S');
    $menu->add_item($botao);

    $menu->show();
    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}
