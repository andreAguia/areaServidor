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

##################################################################

function badgeTipoUsuario($tipoUsuario){
/**
 * Exibe na tabela o tipo de usuario usando a função badge do Fundation
 * 
 * @note Usado na rotina de cadastro de usuários 
 * 
 * @syntax badgeTipoUsuario($tipoUsuario);
 * 
 * @param $tipoUsuario string null o tipo de usuario
 */
    
    switch ($tipoUsuario)
    {
        case "S" :
            badge($tipoUsuario,"primary",NULL,"Usuário Servidor");
            break;
        
        case "B" :
            badge($tipoUsuario,"success",NULL,"Usuário Bolsista");
            break;
    }
}
    
    ##################################################################

    function statusUsuario($idUsuario){
    /**
     * Exibe na tabela o tipo de usuario usando a função badge do Fundation
     * 
     * @note Usado na rotina de cadastro de usuários 
     * 
     * @syntax badgeTipoUsuario($tipoUsuario);
     * 
     * @param $tipoUsuario string null o tipo de usuario
     */

        $intra = new Intra();
        $tipoSenha = $intra->get_tipoSenha($idUsuario);

        switch($tipoSenha)
        {
            case 1 :
                badge("!","secondary",NULL,"Usuário com senha padrão.");
                break;

            case 2 :
                badge("X","alert",NULL,"Usuário Bloqueado.");
                break;

            case 3 :
                badge("OK","success",NULL,"Usuário Habilitado.");
                break;
        }
        
        # Informa ainda se é usuário admin
        if($intra->verificaPermissao($idUsuario,1)){
            badge("A","primary",NULL,"Usuário Administrador.");
        }
    }
