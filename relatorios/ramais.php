<?php
/**
 * Sistema GRH
 * 
 * Relat?io
 *   
 * By Alat
 */

# Inicia as vari?eis que receber? as sessions
$matricula = NULL;		  # Reservado para a matr?ula do servidor logado

# Configura?o
include ("../sistema/_config.php");

# Permiss? de Acesso
$acesso = Verifica::acesso($matricula);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $servidor = new Pessoal();

    # Come? uma nova p?ina
    $page = new Page();			
    $page->iniciaPagina();
    
    ######  FENORTE

    $select ='SELECT DIR,
                     CONCAT(GER," - ",nome),
                     ramais
                FROM tblotacao
               WHERE UADM = "FENORTE"
                 AND ativo = "Sim"
                 AND GER <> "CEDIDO"
                 AND ramais <> ""
               ORDER BY DIR,GER';

    $result = $servidor->select($select);

    # Mensagem
    $box = new P('<br/>
    - Os ramais são os quatro últimos díitos (em parêtesis na tabela);<br/>
    - Para acessar linha externa, nos ramais previamente liberados, tecla 9 + Nº desejado;<br/>
    - Para transferêcia de ligação, tecla FLASH + Nº do ramal a ser direcionada a chamada;<br/>
    - Para utilizar cadeado eletrôico basta digitar 71 + CODIGO DE BLOQUEIO;<br/>
    - Para desativar cadeado eletrôico basta digitar 701 + CODIGO DE BLOQUEIO;<br/>
    - Para Fax será utilizado o Nº(22) 2738-0868. Sendo necessáio originar chamada para o mesmo;<br/>
    - Utilizar sempre a operadora 41.<br/><br/>','divMensagemRamal');

    $relatorio = new Relatorio('relatorioRamal');
    $relatorio->set_titulo('FENORTE');
    $relatorio->set_subtitulo('Telefones e Ramais');
    $relatorio->set_label(array('Diretoria','Setor','Ramais'));
    $relatorio->set_width(array(0,40,30));
    $relatorio->set_align(array("center","left"));
    $relatorio->set_conteudo($result);
    $relatorio->set_numGrupo(0);
    $relatorio->set_totalRegistro(FALSE);
    $relatorio->set_dataImpressao(FALSE);
    $relatorio->set_subTotal(FALSE);
    $relatorio->set_objetoAntesTitulo($box);
    $relatorio->show();

    ######  TECNORTE

    echo '<br/>';

    $servidor = new Pessoal();
    $select ='SELECT DIR,
                     CONCAT(GER," - ",nome),
                     ramais
                FROM tblotacao
               WHERE UADM = "TECNORTE" 
                 AND ativo = "Sim"
                 AND GER <> "CEDIDO"
                 AND ramais <> ""
               ORDER BY DIR,GER';

    $result = $servidor->select($select);

    $relatorio = new Relatorio('relatorioRamal');
    $relatorio->set_titulo('TECNORTE');
    $relatorio->set_subtitulo('Telefones e Ramais');
    $relatorio->set_label(array('Diretoria','Setor','Ramais'));
    $relatorio->set_width(array(0,40,30));
    $relatorio->set_align(array("center","left"));
    $relatorio->set_conteudo($result);
    $relatorio->set_numGrupo(0);
    $relatorio->set_cabecalhoRelatorio(FALSE);
    $relatorio->set_menuRelatorio(FALSE);
    $relatorio->set_subTotal(FALSE);
    $relatorio->set_totalRegistro(FALSE);
    $relatorio->set_log(FALSE);
    $relatorio->show();

    $page->terminaPagina();
}
?>
