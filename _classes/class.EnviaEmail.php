<?php

/**
 * classe EnviaEmail
 * 
 * Classe que encpsula a trefa de enviar email usando o email do sistema
 * 
 * By Alat
 */
class EnviaEmail {
    # Do email institucional

    private $nomeUsuario = 'sistemagrh@uenf.br';
    private $senha = 'flatronw1643c';

    # Do remetente
    private $de = "sistemagrh@uenf.br";
    private $deNome = "Sistema de Pessoal";

    # Do destinatário
    private $para = array();
    private $comCopia = array();
    private $comCopiaOculta = array();

    # Da mensagem
    private $mensagem = null;
    private $assunto = null;
    private $anexo = array();
    
    # Erro
    public $erro;

    ###########################################################

    /**
     * Método Construtor
     * 
     * @param $assunto  string null O assunto do email
     * @param $mensagem string null A mensagem a ser enviada
     */
    public function __construct($assunto, $mensagem) {
        $this->mensagem = $mensagem;
        $this->assunto = $assunto;
    }

    ###########################################################

    /**
     * Método set_de
     */
    public function set_de($de) {
        $this->de = $de;
    }

    ###########################################################

    /**
     * Método set_deNome
     */
    public function set_deNome($deNome) {
        $this->deNome = $deNome;
    }

    ###########################################################

    /**
     * Método set_para
     */
    public function set_para($para) {
        $this->para[] = $para;
    }

    ###########################################################

    /**
     * Método set_comCopia
     */
    public function set_comCopia($comCopia) {
        $this->comCopia[] = $comCopia;
    }

    ###########################################################

    /**
     * Método set_comCopiaOculta
     */
    public function set_comCopiaOculta($comCopiaOculta) {
        $this->comCopiaOculta[] = $comCopiaOculta;
    }

    ###########################################################

    /**
     * Método set_anexo
     */
    public function set_anexo($anexo) {
        $this->anexo[] = $anexo;
    }

    ###########################################################

    public function envia() {

        /**
         * Emite email com o arquivo de backup em anexo
         * 
         * @syntax $this->emiteEmail();
         * 
         */
        # Inicia a classe PHPMailer
        $mail = new PHPMailer();

        # Define que a mensagem será SMTP
        $mail->IsSMTP();

        # Define charset
        $mail->CharSet = 'UTF-8';

        // Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 0;

        // Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';

        // Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';

        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        // Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;

        // Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';
        #$mail->SMTPSecure = 'ssl';              // SSL REQUERIDO pelo GMail
        // Whether to use SMTP authentication
        $mail->SMTPAuth = true;      // A porta deverá estar aberta em seu servidor
        # Do email institucional
        $mail->Username = $this->nomeUsuario;   // Usuário do servidor SMTP
        $mail->Password = $this->senha;         // Senha do servidor SMTP
        # Remetente
        $mail->From = $this->de;            // Email do sistema 
        $mail->FromName = $this->deNome;    // Nome
        # Destinatários
        foreach ($this->para as $destinatario) {
            $mail->AddAddress($destinatario, null);
        }

        # Com Cópia
        foreach ($this->comCopia as $copia) {
            $mail->AddCC($copia, null);
        }

        # Com Cópia Oculta
        foreach ($this->comCopiaOculta as $oculta) {
            $mail->AddBCC($oculta, null);
        }

        # Mensagem
        $mail->IsHTML(true); // Define que o e-mail será enviado como HTML        
        $mail->Subject = $this->assunto; // Assunto da mensagem
        $mail->Body = $this->mensagem;

        //$mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)
        //$mail->AltBody = "Este é o corpo da mensagem de teste, em Texto Plano! \r\n :)";
        # Anexo
        foreach ($this->anexo as $anexo) {
            $mail->AddAttachment($anexo);  // Insere um anexo 
        }

        # Envia
        $enviado = $mail->Send();

        # Limpa os destinatários e os anexos
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();

        # Exibe uma mensagem de resultado
        if (!$enviado) {
            $this->erro = $mail->ErrorInfo;
        }
    }

}
