<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

# Configuração
include ("_config.php");

# Começa uma nova página
$page = new Page();

$jscript = "
    alert('Ola mundo');
";

$page->set_ready($jscript);
$page->iniciaPagina();


$page->terminaPagina();