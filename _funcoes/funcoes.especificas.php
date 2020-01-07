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

        switch($tipoSenha){
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
         * Usada na rotina de importação de cidades
         */
        
        $pessoal = new Pessoal();
        
        $select = "SELECT idCidade "
                . "  FROM tbcidade JOIN tbestado USING (idEstado)"
                . " WHERE LCASE(TRIM(tbcidade.nome)) = '".mb_strtolower(trim($cidade))."'"
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
            
        $link = new Link(NULL,'processo.php?fase=editar&id='.$idProcesso);
        $link->set_imagem(PASTA_FIGURAS_GERAIS.'bullet_edit.png',20,20);
        $link->set_title('Editar Processo');
        $link->show();

        if(Verifica::acesso($idUsuario,1)){   // Somente Administradores
            # Verifica se tem movimentação com esse processo
            $select = 'SELECT idProcessoMovimento
                         FROM tbprocessomovimento
                         WHERE idProcesso = '.$idProcesso;
            $numMov = $intra->count($select);
            
            # Somente exibe o link se não tiver movimentação
            if($numMov == 0){
                echo "&nbsp;&nbsp;&nbsp;&nbsp;";

                $link = new Link(NULL,'processo.php?fase=excluir&id='.$idProcesso);
                $link->set_imagem(PASTA_FIGURAS_GERAIS.'lixo.png',20,20);
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

##########################################################
/**
 * Função que informa a pasta digitalizada de um servidor
 */

function verificaPasta($idServidorPesquisado){
    # Pega o idfuncional
    $pessoal = new Pessoal();
    $idFuncional = intval($pessoal->get_idFuncional($idServidorPesquisado));

    # Define a pasta
    $pasta = "../../_arquivo/";

    $achei = NULL;

    # Encontra a pasta
    foreach (glob($pasta.$idFuncional."*") as $escolhido) {
        $achei = $escolhido;
    }

    # Verifica se tem pasta desse servidor
    if(file_exists($achei)){        
        $link = new Link('Editar','?fase=pasta&idServidorPesquisado='.$idServidorPesquisado);
        $link->set_imagem(PASTA_FIGURAS.'pastaDigitalizada.png',20,20);
        $link->set_title("Pasta encontrada");
        $link->show();
    }
    
}

##########################################################
/**
 * Função que exibe em forma de texto as regras(permissões) para esse servidorum servidor
 */

function get_permissoes($idUsuario){
    
    $select = "SELECT nome 
                 FROM tbregra JOIN tbpermissao USING (idRegra)
                WHERE tbpermissao.idUsuario = $idUsuario
                ORDER BY nome";
    
    $intra = new Intra();
    $row = $intra->select($select);
    $count = $intra->count($select);
    
    # Prepara a variável
    $retorno = NULL;
    $contador = 0;
    
    # Exibe as permissões
    foreach($row as $pp){
        $retorno .= $pp[0];
        $contador++;
        if($contador < $count){
            $retorno.= "<br/>";
        }
    }
    
    echo $retorno;
}

##########################################################

