<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    # Servidor logado 
    $idUsuario = NULL;

    # Configuração
    include ("_config.php");
    
    # Realiza o backup
    $backup = new BackupBancoDados($idUsuario);
    $backup->set_tipo(2);
    $backup->executa();
