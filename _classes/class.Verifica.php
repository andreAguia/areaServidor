<?php

/**
 * classe VerificaAcesso
 * 
 * Classe que verifica o acesso as rotinas
 * 
 * By Alat
 */
class Verifica {

    /**
     * Método Construtor
     * 
     * @param $idUsuario string $idUsuario do servidor logado
     * @param $rotina integer codigo numérico da rotina a ser verificada
     */
    static function acesso($idUsuario, $rotina = NULL) {
        # Flag de permissão do acesso
        $acesso = TRUE;
        $manutencao = FALSE;

        $intra = new Intra();

        ###########################################################

        /**
         * Verifica se usuário Logou
         */
        # Verifica se foi logado se não redireciona para o login
        if (is_null($idUsuario)) {
            $acesso = FALSE;
        }

        # Verifica se $idUsuario é nula acesso bloqueado para a área do servidor
        if (($intra->get_senha($idUsuario) == '') and ($idUsuario <> 0)) {
            $acesso = FALSE;
        }

        # Verifica se o login foi feito ou se a sessão foi "recuperada" pelo browser
        if (($intra->get_ultimoAcesso($idUsuario)) <> date("Y-m-d")) {
            $acesso = FALSE;
        }

        # Verifica de o usuário logado tem permissão para essa rotina 
        if (!is_null($rotina)) {
            if (!($intra->verificaPermissao($idUsuario, $rotina))) {
                $acesso = FALSE;
            }
        }

        /**
         * Verifica se está em Manutenção
         */
        # Verifica o ip da máquina
        $ipManutencao = $intra->get_variavel('ipAdmin'); // ip isento da mensagem
        $ipMaquina = $_SERVER['REMOTE_ADDR'];         // ip da máquina
        # Verifica se está em Manutenção

        if ($intra->get_variavel('manutencao')) {
            if ($ipManutencao <> $ipMaquina) {
                $manutencao = TRUE;
            }
        }

        # Exibe a mensagem de manutenção
        if ($manutencao) {
            loadPage("../manutencao.php");
        } else {
            return $acesso;
        }
    }

}
