<?php

/*
 * Verifica qual é o campo de origem
 * 
 */

# pega o setorCombo
$setorCombo = $campoValor[2];

# pega o setorTexto
$setorTexto = $campoValor[3];

# Verifica se os setores estão em banco
if ((is_null($setorTexto)) AND (is_null($setorCombo))) {
    $msgErro .= 'O Campo de setor está em branco!\n';
    $erro = 1;
}

# Escolhe o combo quando os dois estão preenchidos
if ((!is_null($setorTexto)) AND (!is_null($setorCombo))) {
    $campoValor[3] = null;
}

