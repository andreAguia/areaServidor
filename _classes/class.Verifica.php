<?php
/**
 * classe VerificaAcesso
 * 
 * Classe que verifica o acesso as rotinas
 * 
 * By Alat
 */
class Verifica
{
    /**
     * Método Construtor
     * 
     * @param $matricula string matrícula do servidor logado
     * @param $rotina integer codigo numérico da rotina a ser verificada
     * @param $god bool flag que indica se somente usuário God tem acesso
     */
    static function acesso($matricula,$rotina = null,$somenteGod = false)
    {        
        # Flag de permissão do acesso
        $acesso = true;
        $manutencao = false;
        
        $intra = new Intra();
        
        ###########################################################

        /**
         * Verifica se usuário Logou
         */
        
        # Verifica se foi logado se não redireciona para o login
        if(is_null($matricula))
            $acesso = false;
        
        # Verifica se matricula é nula acesso bloqueado para a área do servidor
        $servidor = new Pessoal();
        if(($servidor->get_senha($matricula) == '') and ($matricula <>0))
            $acesso = false;

        # Verifica se o login foi feito ou se a sessão foi "recuperada" pelo browser
        if (($servidor->get_ultimoAcesso($matricula)) <> date("Y-m-d"))
            $acesso = false;

        # Verifica de o usuário logado tem permissão para essa rotina        
        if(($somenteGod) AND ($matricula <> GOD))
            $acesso = false;
        elseif(!(is_null($rotina)))
        {
            if(!($intra->verificaPermissao($matricula,$rotina)))
                $acesso = false;
        }
        
        /**
         * Verifica se está em Manutenção
         */
        
        # Verifica o ip da máquina
        $ipManutencao = $intra->get_variavel('manutencao_ip');	// ip isento da mensagem
        $ipMaquina = $_SERVER['REMOTE_ADDR'];       		// ip da máquina  
        
        # Verifica se está em Manutenção
        if ($intra->get_variavel('manutencao_intranet'))
        {
            if($ipManutencao <> $ipMaquina)
                $manutecao = true;
        }
        
        # Exibe a mensagem de manutenção
        if($manutencao)
            loadPage("../manutencao.php");
        elseif($acesso)
            return $acesso;
        else
        {
            loadPage("../../admin/adminSistema/login.php");           
            echo'<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL '.$_SERVER['PHP_SELF'].' was not found on this server.</p></body></html>';
        }            
    }    
}
?>
