<?php

class BackupBancoDados{
 /**
  * Executa o backup do banco de dados do sistema
  * 
  * @author André Águia (Alat) - alataguia@gmail.com
  * 
  * @var private $idUsuario integer  NULL   O idUsuário do servidor que fez o backup para o log
  * @var private $tipo      integer  1      O tipo do backup: 1 - automático ou 2 - manual 
  */
    
    private $idUsuario = NULL;
    private $tipo = 1;
     
    ###########################################################
    
    /**
     * Método Construtor
     *
     * @param $idUsuario integer NULL O idUsuário do servidor que fez o backup para o log
     * 
     */
    
    public function __construct(){
        
    }

    ###########################################################

    public function set_tipo($tipo = 1){
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
    
    public function executa(){
    /**
     * Exibe a lista
     * 
     */
        
        # Conecta ao Banco de Dados
        $intra = new Intra();

        # Define o nome do arquivo
        $pedaco1 = date("Y.m.d");
        $pedaco2 = date("H:i:s");
        $arquivo = $pedaco1."_".$pedaco2;

        # Executa o backup no Linux
        shell_exec("./executaBackup $arquivo");

        # Envia o arquivo por email
        $pedaco1 = date_to_php($pedaco1,".");

        $assunto = "Backup de ".$pedaco1." as ".$pedaco2;
        
        # Pega o tipo do backup
        if($this->tipo == 1){
            $textoTipo = "Automático";
        }else{
            $textoTipo = "Manual";
        }
        
        # Pega o nome do sistema nas variáveis
        $nomeSistema = $intra->get_variavel("sistemaGrh");

        $mensagem =  "UENF - Universidade Estadual do Norte Fluminense<br/>";
        $mensagem .= "GRH - Gerência de Recursos Humanos<br/>";
        $mensagem .= $nomeSistema."<br/><br/>";
        $mensagem .= "Este é um e-mail automático. Não é necessário respondê-lo.";
        $mensagem .= "<br/><br/>";
        $mensagem .= "Backup da Base de Dados realizado.";
        $mensagem .= "<br/>";
        $mensagem .= "Tipo: $textoTipo<br/>";
        $mensagem .= "Data: $pedaco1<br/>";
        $mensagem .= "Hora: $pedaco2<br/>";
        $mensagem .= str_repeat("-", 80)."<br/>";
        $mensagem .= "Segue, em anexo, os arquivos do backup<br/>";
        $mensagem .= "Qualquer dúvida entre em contato com a GRH.";
        
        # Pega os email a serem usados nas variáveis
        $para = $intra->get_variavel("backupEmailPara");
        $copia = $intra->get_variavel("backupEmailCopia");
        
        # Inicia o email
        $mail = new EnviaEmail($assunto, $mensagem);
        
        # Define o endereço de origem
        $numPara = explode(",",$para);
        if(count($numPara) > 1){
            foreach ($numPara as $destinatario){
                $mail->set_para($destinatario);
            }
        }else{
            $mail->set_para($para);
        }
        
        # Define o endereço para cópia
        if(!is_null($copia)){
            $numCopia = explode(",",$copia);
            if(count($numCopia) > 1){
                foreach ($numCopia as $comCopia){
                    $mail->set_comCopia($comCopia);
                }
            }else{
                $mail->set_comCopia($copia);
            }
        }
        
        $mail->set_deNome("Sistema de Pessoal");

        $arquivo = $arquivo.'.tar';

        #$caminho = '../../_backup/';            
        $caminho = '/var/www/html/_backup/';
        $mail->set_anexo($caminho.$arquivo);

        $mail->envia();
        
        # Data e Hora
        $hoje = date("d/m/Y"); 
        $hora = date("H");   

        # Atualiza a data do último backup
        $intra->set_variavel("backupData",$hoje);
        $intra->set_variavel("backupHora",$hora);

        # Grava no log a atividade
        $intra->registraLog(NULL,date("Y-m-d H:i:s"),"Backup $textoTipo realizado",NULL,NULL,6);
    }
    
    ###########################################################
    
}