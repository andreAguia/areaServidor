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
            badge("A","warning",NULL,"Usuário Administrador.");
        }
    }
    
    ###########################################################

    function verificaBackupHoje(){
        $return = FALSE;
        $pasta = "../_backup/".date('Y.m.d');
        
        # Verifica se a pasta existe
        if (is_dir($pasta)) {
            $ponteiro  = opendir($pasta);
           
           # percorre os arquivos da pasta
           while ($arquivo = readdir($ponteiro)){ 

                if($arquivo == ".." || $arquivo == ".")continue; // Desconsidera os diretorios 
                if($arquivo == "Thumbs.db")continue; // Desconsidera o thumbs.db

                # Divide o nome do arquivo
                $partesArquivo = explode('.',$arquivo);

                # Organiza as partes
                $dia = $partesArquivo[2]."/".$partesArquivo[1]."/".$partesArquivo[0];
                $hora = $partesArquivo[3].":".$partesArquivo[4];
                $tipo = $partesArquivo[5];
                $banco = $partesArquivo[6];
                $extensao = $partesArquivo[7];

                if($extensao == "sql"){
                    $return = TRUE;                
                }
            }
        }
        echo $return;
        return $return;
    }
