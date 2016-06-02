<?php
/**
 * phpInfo
 *  
 * By Alat
 */

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($matricula,7);

if($acesso)
{    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    #AreaServidor::cabecalho(); // por algum motivo desconhecido quando se coloca o cabeçcalho o phpinfo fica desalinhado
            
    # Limita o tamanho da página
    $grid = new Grid();
    $grid->abreColuna(12);
    
    botaoVoltar('administracao.php');

    titulo('Informações sobre a Versão do PHP');

    phpinfo();    
   
    $grid->fechaColuna();
    $grid->fechaGrid();
    
    $page->terminaPagina();
}