<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Realiza o backup
$backup = new BackupBancoDados($argv[2]);
$backup->set_tipo($argv[1]);
$backup->executa();
