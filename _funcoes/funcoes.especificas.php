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
        
##########################################################
/**
 * Função que exibe dados de um processo
 * 
 */

function get_dadosProcesso($tt){
    # Pega os argumentod que veem como array 
    $idProcesso = $tt[0];
    $idUsuario = $tt[1];
    
    # Pega os dados
    $select = 'SELECT idProcesso,
                      numero,
                      data,
                      assunto
                 FROM tbprocesso
                WHERE idProcesso = '.$idProcesso;

    $intra = new Intra();
    $row = $intra->select($select,FALSE);
    
    $grid = new Grid();
    $grid->abreColuna();

    $painel = new Callout("primary");
    $painel->set_id("right");
    $painel->abre();
            
        $link = new Link('Editar','processo.php?fase=editar&id='.$idProcesso);
        $link->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',20,20);
        $link->set_title('Editar Processo');
        $link->show();
        
        $processo = new Processo();
        if($processo->get_numMovimentos($idProcesso) == 0){
            if(Verifica::acesso($idUsuario,1)){   // Somente Administradores
                echo "&nbsp;&nbsp;&nbsp;&nbsp;";

                $link = new Link('Excluir','processo.php?fase=excluir&id='.$idProcesso);
                $link->set_image(PASTA_FIGURAS_GERAIS.'lixo.png',20,20);
                $link->set_title('Excluir Processo');
                $link->set_confirma('Deseja mesmo excluir?');
                $link->show();
            }
        }
        
        # Dados do Processo
        p($row[1],"pNumeroProcesso");
        p(date_to_php($row[2]),"pDataProcesso");
        p($row[3],"pAssuntoProcesso");
    $painel->fecha();
    
    $grid->fechaColuna();
    $grid->fechaGrid(); 
}


    


    