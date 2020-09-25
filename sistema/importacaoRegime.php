<?php

/**
 * Rotina de Importação
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Parâmetros da importação
    $tt = 0;                                    // contador de registros
    $problemas = 0;                             // contador de problemas
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    br();

    #########################################################################

    switch ($fase) {
        case "" :

            # Conecta o Banco de Dados
            $pessoal = new Pessoal;

            $select = "SELECT idServidor, dtTransfRegime FROM tbservidor JOIN tbconcurso USING(idConcurso) WHERE situacao = 1 AND regime = 'CLT'";
            $dados = $pessoal->select($select);

            foreach ($dados as $serv) {
                echo "UPDATE tbservidor SET dtTransfRegime = '2003-09-09' WHERE idServidor = {$serv['idServidor']}";

                # Grava na tabela
                $campos = array("dtTransfRegime");
                $valor = array('2003-09-09');
                $pessoal->gravar($campos, $valor, $serv['idServidor'], "tbservidor", "idServidor", false);
                
                br();
                $tt++;
            }

            hr();
            echo $tt," Registros afetados.";
            break;
        #########################################################################    
    }
    $grid->fechaColuna();
    $grid->fechaGrid();
    $page->terminaPagina();
}