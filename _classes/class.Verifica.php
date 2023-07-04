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
    static function acesso($idUsuario, $rotina = null) {
        # Flag de permissão do acesso
        $acesso = false;
        $manutencao = false;

        $intra = new Intra();

        ###########################################################

        /**
         * Verifica se usuário Logou
         */
        # Verifica se foi logado se não redireciona para o login
        if (empty($idUsuario)) {
            $acesso = false;
        }

        # Verifica se $idUsuario é nula (usuário bloqueado)
        if (($intra->get_senha($idUsuario) == '') and ($idUsuario <> 0)) {
            $acesso = false;
        }

        # Verifica se o login foi feito ou se a sessão foi "recuperada" pelo browser
        if (($intra->get_ultimoAcesso($idUsuario)) <> date("Y-m-d")) {
            $acesso = false;
        }

        # Verifica de o usuário logado tem permissão para essa rotina 
        if (!empty($rotina)) {

            if (is_array($rotina)) {
                foreach ($rotina as $tt) {
                    if ($intra->verificaPermissao($idUsuario, $tt)) {
                        $acesso = true;
                        break;
                    }
                }
            } else {
                if ($intra->verificaPermissao($idUsuario, $rotina)) {
                    $acesso = true;
                }
            }
        }

        /**
         * Verifica se está em Manutenção
         */
        # Verifica se está em Manutenção
        if ($intra->get_variavel('manutencao')) {
            # Somente admin acessam o sistema em manutenção
            if (!$intra->verificaPermissao($idUsuario, 1))
                $manutencao = true;
        }

        # Exibe a mensagem de manutenção
        if ($manutencao) {
            loadPage("../sistema/manutencao.php");
        } else {
            return $acesso;
        }
    }
}
