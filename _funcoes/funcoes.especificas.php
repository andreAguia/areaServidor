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
            
        $link = new Link('Editar','processo.php?fase=editar&id='.$idProcesso);
        $link->set_image(PASTA_FIGURAS_GERAIS.'bullet_edit.png',20,20);
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
        $link->set_image(PASTA_FIGURAS.'pastaDigitalizada.png',20,20);
        $link->set_title("Pasta encontrada");
        $link->show();
    }
    
}

##########################################################

function importaContatos($idPessoa){
    
    # Pega os dados antigos
    $select = 'SELECT numero
                 FROM tbcontatos
                WHERE idPessoa='.$idPessoa.'
             ORDER BY tipo';

    $pessoal = new Pessoal;
    $row = $pessoal->select($select);
    $num = $pessoal->count($select);
    
    # Inicia as variáveis para guardar os novos dados
    $telResidencial = NULL;
    $telCelular = NULL;
    $telRecados = NULL;
    $emailUenf = NULL;
    $emailPessoal = NULL;
    
    $contatos[] = NULL;
    
    # Pega os dados e coloca no array $contatos
    foreach ($row as $value) {
        # Verifica se tem mais de um valor, ou seja se tem espaços
        $pos = stripos($value[0], " ");
        
        # Se não tiver joga no array contatos
        if($pos === false) {
            $contatos[] = mb_strtolower($value[0]);
        }else{  // Se tiver explode
            $pedaco = explode(" ",$value[0]);
            foreach ($pedaco as $pp) {
                $contatos[] = mb_strtolower($pp);
            }
        }
    }
    
    # Analisa cada elemento de $contatos e coloca nas variáveis de retorno
    foreach ($contatos as $gg) {
        # Verifica se é email
        $pos1 = stripos($gg, "@");
        
        if($pos1 === false) {    // é telefone
            $gg = soNumeros($gg);
            $primeiroC = substr($gg, 0, 1);
            
            if($primeiroC == "9"){
                $telCelular = $gg;
            }else{
                $telResidencial = $gg;
            }
        }else{  // é email
            $pos2 = stripos($gg, "@uenf");
            
            # Qual o tipo de email
            if($pos2 === false) {    // É email pessoal
                $emailPessoal = $gg;
            }else{ // É email uenf
                $emailUenf = $gg;
            }
        }
        
    }
    
    $retorno = [$telResidencial,$telCelular,$telRecados,$emailUenf,$emailPessoal];
    return $retorno;
}


    


    