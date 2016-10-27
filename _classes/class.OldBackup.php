<?php
 /**
 * classe Backup
 * Realiza um backup de um banco de dados ou de um diretório
 * 
 * @param 	$tipo			string	-> pode ser: pasta ou banco define se será backup de uma pasta ou de um banco de dados
 * @param 	$pastaDestino           string	-> a pasta onde será feito o backup
 * @param 	$banco			string	-> o nome do banco de dados
 * @param 	$usuario		string	-> o nome do usuário
 * @param 	$senha			string	-> a senha de acesso
 * @param 	$servidor		string	-> o servidor onde está o banco
 * @param	$pastaOrigem	string	-> a pasta onde estão os arquivos originais 
 *  
 * By Alat
 */
class OldBackup
{
    private $tipo = null;
    private $pastaDestino = null;

    # quando for do tipo banco
    private $banco = null;
    private $usuario = null;
    private $senha = null;
    private $servidor = null;

    # quando for do tipo pasta
    private $pastaOrigem = null;

    # exibe ou não o alert
    private $alert = false;
		
	/**
     * método construtor
     *  
     * @param  	$tipo	string	-> define o tipo de backup
     */
	public function __construct($tipo)
	{
            # Verifica validade dos tipos
            $this->tipo = $tipo;
	}
	
	###########################################################
	
	/**
     * método set_pastaDestino
     *  
     * @param 	$pastaDestino	string	-> a pasta onde será feito o backup
     */
	public function set_pastaDestino($pastaDestino)
	{
		$this->pastaDestino = $pastaDestino;
	}
	
	###########################################################
	
	/**
     * método set_pastaOrigem
     *  
     * @param	$pastaOrigem	string	-> a pasta onde estão os arquivos originais 
     */
	public function set_pastaOrigem($pastaOrigem)
	{
		$this->pastaOrigem = $pastaOrigem;
	}
	
	###########################################################
	
	/**
     * método set_banco
     *  
     * @param 	$banco			string	-> o nome do banco de dados
     */
	public function set_banco($banco)
	{
		$this->banco = $banco;
	}
	
	###########################################################
	
	/**
     * método set_usuario
     *  
     * @param 	$usuario		string	-> o nome do usuário
     */
	public function set_usuario($usuario)
	{
		$this->usuario = $usuario;
	}
	
	###########################################################
	
	/**
     * método set_senha
     *  
     * @param 	$senha			string	-> a senha de acesso
     */
	public function set_senha($senha)
	{
		$this->senha = $senha;
	}
	
	###########################################################
	
	/**
     * método set_servidor
     *  
     * @param 	$servidor			string	-> o nome do servidor
     */
	public function set_servidor($servidor)
	{
		$this->servidor = $servidor;
	}
	
	###########################################################
	
	/**
     * método set_alert
     *  
     * @param 	$alert			bool	-> se true exibe o alert, se false não
     */
	public function set_alert($alert)
	{
		$this->alert = $alert;
	}
	
	###########################################################
	/**
     * método executa
     *  
     * Executa o backup
     */
	public function executa()
	{
		if(!is_dir($this->pastaDestino))
			mkdir($this->pastaDestino);
		
		if ($this->tipo == 'banco')
		{
			# --- Banco de Dados ---
			# cria uma pasta para o backup
			$pasta = $this->pastaDestino.'\\'.date('Y.m.d_g.ia').'_banco.'.$this->banco;
			if(!file_exists($pasta))
				mkdir($pasta);
			
			# Conecta ao servidor
			mysql_connect($this->servidor, $this->usuario, $this->senha) or die(mysql_error());
				
			# Conecta ao banco		
			mysql_select_db($this->banco) or die(mysql_error());
				
			# Abre o arquivo texto com o nome do banco.txt
			$back = fopen($pasta.'\\'.$this->banco.'.sql','w');
				
			# Pega a lista de todas as tabelas do banco
			$tabelas = mysql_list_tables($this->banco) or die(mysql_error());
				
			# Cabeçalho do arquivo de Backup
			fwrite($back,"-- FENORTE - Fundação Estadual Norte Fluminense\n");
			fwrite($back,"-- GTI - Gerência da Tecnologia da Informação\n");
			fwrite($back,"-- Rotina de Backup de Sistema 1.0\n");
			fwrite($back,"-- Realizado em: ".date("d-m-Y, g:i a\n\n"));
			
			# Codigo 
			fwrite ($back,"/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n");
			fwrite ($back,"/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n");
			fwrite ($back,"/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n");
			fwrite ($back,"/*!40101 SET NAMES utf8 */;\n\n");
			
			fwrite ($back,"/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n");
			fwrite ($back,"/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n");
			fwrite ($back,"/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n");
				
			# Informa o banco
			fwrite($back,"\n----  Banco:".$this->banco." ----\n");
			
			# Cria o Banco para recuperação caso não exista
			fwrite($back,"CREATE DATABASE IF NOT EXISTS ".$this->banco.";\n");
			fwrite($back,"USE ".$this->banco.";\n");
								
			# Percorre as tabelas
			while ($row = mysql_fetch_row($tabelas))
			{
				$table = $row[0];
				$res2 = mysql_query("SHOW CREATE TABLE $table");
					
				while ( $lin = mysql_fetch_row($res2))  // Para cada tabela
				{
					fwrite($back,"\n\n -- Tabela: $table --\n");
					
					# Apaga a tabela para recuperar a nova
					fwrite($back,"DROP TABLE IF EXISTS $table;\n");
					
					# Entra a estrutura
					fwrite($back,"$lin[1]\n\n-- Dados\n");
					$res3 = mysql_query("SELECT * FROM $table");
												
					while($r=mysql_fetch_row($res3)) // Dump de todos os dados das tabelas
					{ 
						$sql="INSERT INTO `$table` VALUES ('";
						$sql .= implode("','",$r);
						$sql .= "');\n";
						fwrite($back,$sql);
					}
				}
			}
			# Fim do Backup
			fwrite($back,"\n\n-- Fim do Backup --");
				
			# Fecha o arquivo
			fclose($back);
		}
		elseif ($this->tipo == 'pasta')
		{
			# --- Banco de Dados ---
			# pega o nome da pasta
			$nome = substr(strrchr($this->pastaOrigem, '\\'), 1 );
			
			# cria uma pasta para o backup
			$pasta = $this->pastaDestino.'\\'.date('Y.m.d_g.ia').'_pasta.'.$nome;
			if(!file_exists($pasta))
			mkdir($pasta);

			# cria a pasta usando o shell
			$output = shell_exec( "xcopy/e /s $this->pastaOrigem $pasta" );	// Copia os arquivos
			$tt = $output;											
		}
				
		if($this->alert)
		{
			$alert = new Alert('Backup concluído !!');
			$alert->show();
		}
	}
	
	
}