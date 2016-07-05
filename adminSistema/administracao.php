<?php
/**
 * Administração
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Resseta a session da flag origem
    set_session('origem'); 

    # Verifica a fase do programa
    $fase = get('fase','menu');         # Qual a classe
    $metodo = get('metodo','menu');	# Qual o método da classe

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');

    # Começa uma nova página
    $page = new Page();
    #$page->set_bodyOnLoad("fechaDivId('divMensagemAguarde');");
    $page->iniciaPagina();
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
     # Limita o tamanho da tela
     $grid = new Grid();
     $grid->abreColuna(12);

    botaoVoltar('../../grh/grhSistema/grh.php');
    titulo('Administração dos Sistemas');
    
    switch ($fase)
    {	
        # Exibe o Menu Inicial
        case "menu" :            
            #AreaServidor::listaOcorrencias($idUsuario);            
            Administracao::menu($idUsuario);            
            break;
    }
}