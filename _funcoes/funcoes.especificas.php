<?php

/* 
 * Funções Específicas dos sistema
 * 
 */

function exibeNomeTitle($idServidor){
/**
 * Retorna o idServidor com o nome do servidor no on mouse over
 * 
 * @note Usado na rotina de histórico 
 * 
 * @syntax exibeNomeTitle($idServidor);
 * 
 * @param $idServidor integer null id do servidor.
 */
    
    $pessoal = new Pessoal();
    $nomeServidor = $pessoal->get_nome($idServidor);
    p($idServidor,null,null,$nomeServidor);
}