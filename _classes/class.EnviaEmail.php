<?php
/**
 * classe EnviaEmail
 * 
 * Classe que encpsula a trefa de enviar email usando o email do sistema
 * 
 * By Alat
 */
class EnviaEmail
{   
    # Do email institucional
    #private $nomeUsuario = 'sistemagrh@uenf.br';   // Usuário do servidor SMTP
    #private $senha = '';                           // Senha do servidor SMTP
    private $nomeUsuario = 'sistemagrh@uenf.br';    // Usuário do servidor SMTP
    private $senha = 'syncmaster940bplus';          // Senha do servidor SMTP
    
    # Do remetente
    private $de = "sistemagrh@uenf.br";        // Email do sistema 
    private $deNome = "Sistema de Pessoal";    // Nome
    
    # Do destinatário
    private $para = array();
    private $comCopia = array();
    private $comCopiaOculta = array();
    
    # Da mensagem
    private $mensagem = NULL;       // A mensagem
    private $assunto = NULL;        // O assunto
    private $anexo = array();       // array com anexos
    
    ###########################################################
    
    /**
     * Método Construtor
     * 
     * @param $idUsuario string $idUsuario do servidor logado
     * @param $rotina integer codigo numérico da rotina a ser verificada
     */
    
    public function __construct($assunto,$mensagem)
    {       
        $this->mensagem = $mensagem;
        $this->assunto = $assunto;
    }
        
    ###########################################################
    
    /**
     * Método set_de
     */
    
    public function set_de($de)
    {  
        $this->de = $de; 
    }
        
    ###########################################################
    
    /**
     * Método set_deNome
     */
    
    public function set_deNome($deNome)
    {  
        $this->deNome = $deNome; 
    }
        
    ###########################################################
    
    
    /**
     * Método set_para
     */
    
    public function set_para($para)
    {  
        $this->para[] = $para; 
    }
        
    ###########################################################
    
    /**
     * Método set_comCopia
     */
    
    public function set_comCopia($comCopia)
    {  
        $this->comCopia[] = $comCopia; 
    }
        
    ###########################################################
    
    /**
     * Método set_comCopiaOculta
     */
    
    public function set_comCopiaOculta($comCopiaOculta)
    {  
        $this->comCopiaOculta[] = $comCopiaOculta;
    }
        
    ###########################################################
    
    /**
     * Método set_anexo
     */
    
    public function set_anexo($anexo)
    {  
        $this->anexo[] = $anexo;
    }
    
    ###########################################################
        
    public function envia(){

        /**
         * Emite email com o arquivo de backup em anexo
         * 
         * @syntax $this->emiteEmail();
         */

        $mail = new PHPMailer();    // Inicia a classe PHPMailer

        # Servidor de email
        $mail->IsSMTP();                        // Define que a mensagem será SMTP
        $mail->SMTPAuth = true;                 // Usa autenticação SMTP? (opcional)
        $mail->SMTPSecure = 'ssl';              // SSL REQUERIDO pelo GMail
        $mail->Host = 'smtp.gmail.com';         // SMTP utilizado
        $mail->Port = 465;                      // A porta deverá estar aberta em seu servidor

        # Do email institucional
        $mail->Username = $this->nomeUsuario;   // Usuário do servidor SMTP
        $mail->Password = $this->senha;         // Senha do servidor SMTP

        # Remetente
        $mail->From = $this->de;            // Email do sistema 
        $mail->FromName = $this->deNome;    // Nome

        # Destinatários
        foreach ($this->para as $destinatario){
             $mail->AddAddress($destinatario, NULL);
        }
        
        # Com Cópia
        foreach ($this->comCopia as $destinatario){
             $mail->AddCC($destinatario, NULL);
        }
        
        # Com Cópia Oculta
        foreach ($this->comCopiaOculta as $destinatario){
             $mail->AddBCC($destinatario, NULL);
        }

        # Mensagem
        $mail->IsHTML(true); // Define que o e-mail será enviado como HTML        
        $mail->Subject  = $this->assunto; // Assunto da mensagem
        $mail->Body = $this->mensagem;
        
        //$mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)
        //$mail->AltBody = "Este é o corpo da mensagem de teste, em Texto Plano! \r\n :)";
        
        # Anexo
        foreach ($this->anexo as $anexo){
             $mail->AddAttachment($anexo);  // Insere um anexo 
        }
        
        # Envia
        $enviado = $mail->Send();

        # Limpa os destinatários e os anexos
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();

        # Exibe uma mensagem de resultado
        if (!$enviado) {
            alert("Não foi possível enviar o e-mail. $mail->ErrorInfo");
        }
    }
}
