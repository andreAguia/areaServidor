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
 * @param $idServidor integer NULL id do servidor.
 */
    
    $pessoal = new Pessoal();
    $nomeServidor = $pessoal->get_nome($idServidor);
    echo "<abbr title='$nomeServidor'>$idServidor</abbr>";
    #p($idServidor,NULL,NULL,$nomeServidor);
}

##################################################################

    function statusUsuario($idUsuario){
    /**
     * Exibe na tabela o tipo de usuario usando a função badge do Fundation
     * 
     * @note Usado na rotina de cadastro de usuários 
     * 
     * @syntax statusUsuario($idUsuario);
     * 
     * @param $tipoUsuario string NULL o tipo de usuario
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
    
    ##################################################################
    
    function encontraCidade($cidade){
        /**
         * Informa o idCidade na tabela tbcidade de uma cidade
         * 
         * Usada na otina de importação de cidades
         */
        
        $pessoal = new Pessoal();
        
        $select = "SELECT idCidade "
                . "  FROM tbcidade JOIN tbestado USING (idEstado)"
                . " WHERE LCASE(TRIM(tbcidade.nome)) = '".strtolower(trim($cidade))."'"
                . " ORDER BY proximidade, tbestado.nome, tbcidade.nome";
        
        $escolhida = $pessoal->select($select,FALSE);
        return $escolhida[0];
        
    }
    
    ##################################################################
    
    function formataDataTarefa($dataInicial,$dataFinal = NULL){
        
        # Pega a data de hoje
        $d = date("d"); 
        $m = date("m"); 
        $Y = date("Y");
        
        $hoje = ajeita(date("m-d-Y", mktime(0, 0, 0, $m, $d, $Y))); 
        $ontem = ajeita(date("m-d-Y", mktime(0, 0, 0, $m, $d-1, $Y))); 
        $amanha = ajeita(date("m-d-Y", mktime(0, 0, 0, $m, $d+1, $Y))); 
        
        # Inicia as Variáveis de retorno
        $inicialRetorno = ajeita2($dataInicial);
        $finalRetorno = ajeita2($dataFinal);
        
        # Se alguma data é hoje
        if($dataInicial == $hoje){
            $inicialRetorno = "Hoje";
        }
        
        if($dataFinal == $hoje){
            $finalRetorno = "Hoje";
        }
        
        # Se alguma data é amanhã
        if($dataInicial == $amanha){
            $inicialRetorno = "Amanhã";
        }
        
        if($dataFinal == $amanha){
            $finalRetorno = "Amanhã";
        }
        
        # Se alguma data é ontem
        if($dataInicial == $ontem){
            $inicialRetorno = "Ontem";
        }
        
        if($dataFinal == $ontem){
            $finalRetorno = "Ontem";
        }
        
        if(vazio($finalRetorno)){
            $textoRetorno = $inicialRetorno;
        }else{
            $textoRetorno = $inicialRetorno." até ".$finalRetorno;
        }
        return $textoRetorno;
    }
    
    ##################################################################
    
    function ajeita($data){
        $dt1 = explode("-",$data);
        $dt2 = $dt1[1].'/'.$dt1[0].'/'.$dt1[2];
        
        if(validaData($dt2)){
            return $dt2;
        }else{
            return NULL;
        }
    }
    
##################################################################
    
    function ajeita2($data){
        if(validaData($data)){
            $dt1 = explode("/",$data);
            $dt2 = $dt1[0].'/'.$dt1[1];
            return $dt2;
        }else{
            return NULL;
        }
    }