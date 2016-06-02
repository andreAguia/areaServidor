<?php
/**
 * Rotina que exibe uma mensagem de aguarde
 * 
 * usado em 'quase' todos os relatórios
 *  
 * By Alat
 */

# Configurações
include ("../config.php");

# pega a próxima página
$pagina = get('pagina');

# pega a div se for por ajax
#$div = get('div');

# Começa uma nova página
$page = new Page();
$page->set_bodyOnLoad("abreDivId('divMensagemAguarde');");
$page->iniciaPagina();

# Exibe uma mensagem de aguarde
mensagemAguarde("Aguarde ...");

# carraga a página se for link
if(!is_null($pagina))
    loadPage($pagina);

$page->terminaPagina();	   
?>
