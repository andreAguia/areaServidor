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
    
    switch ($fase)
    {	
        # Exibe o Menu Inicial
        case "menu" :       
            Administracao::menu(); 
            
            $callout = new Callout();
            $callout->set_botaoFechar(true);
            $callout->abre();
                echo "oi";
            $callout->fecha();
            break;
        
        # Exibe o Menu de Documentação
        case "documentacao" :          
            Administracao::menuDocumentacao();            
            break;
    }
    $page->terminaPagina();
}