<?php

class BackupBancoDados {

    /**
     * Executa o backup do banco de dados do sistema
     * 
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $idUsuario integer  null   O idUsuário do servidor que fez o backup para o log
     * @var private $tipo      integer  1      O tipo do backup: 1 - automático ou 2 - manual 
     */
    private $idUsuario = null;
    private $tipo = 1;

    ###########################################################

    /**
     * Método Construtor
     *
     * @param $idUsuario integer null O idUsuário do servidor que fez o backup para o log
     * 
     */
    public function __construct($idUsuario = null) {

        if (!is_null($idUsuario)) {
            $this->idUsuario = $idUsuario;
        }
    }

    ###########################################################

    public function set_tipo($tipo = 1) {
        /**
         * Altera o tipo do backup
         * 
         * @syntax BackuBancoDados->set_tipo($tipo);
         * 
         * @param $tipo integer 1 O tipo do backup: 1 - automático ou 2 - manual 
         */
        $this->tipo = $tipo;
    }

    ###########################################################

    public function executa() {
        /**
         * Exibe a lista
         * 
         */
        # Conecta ao Banco de Dados
        $intra = new Intra();

        # Grava o dia e a hora do backup
        $intra->set_variavel("backupDia", date("d"));
        $intra->set_variavel("backupHora", date("H"));          

        # Define o nome do arquivo
        $pedaco1 = date("Y.m.d");
        $pedaco2 = date("H:i:s");
        $arquivo = $pedaco1 . "_" . $pedaco2;

        # Executa o backup no Linux
        shell_exec("/var/www/html/areaServidor/sistema/executaBackup $arquivo");

        # Pega o tipo do backup
        if ($this->tipo == 1) {
            $textoTipo = "Automático";
        } else {
            $textoTipo = "Manual";
        }

        # Grava no log a atividade
        $intra->registraLog($this->idUsuario, date("Y-m-d H:i:s"), "Backup $textoTipo realizado", null, null, 6);
    }

    ###########################################################
}
