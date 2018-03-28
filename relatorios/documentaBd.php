<?php
/**
 * Sistema GRH
 * 
 * Relatório
 *   
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("../sistema/_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){    
    # Conecta ao Banco de Dados
    $servico = new Doc();

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();
    
    # Verifica o banco
    $banco = get('banco');

    ######

    $select = "SELECT TABLE_NAME,
                      TABLE_COMMENT,
                      TABLE_TYPE,
                      ENGINE,
                      TABLE_ROWS,
                      AVG_ROW_LENGTH,
                      DATA_LENGTH,
                      AUTO_INCREMENT
                 FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$banco'"; 
    
    $conteudo = $servico->select($select);

    $relatorio = new Relatorio();
    $relatorio->set_titulo($banco);
    #$relatorio->set_subtitulo('Agrupados por Cargo - Ordenados pelo Nome');
    $relatorio->set_label(array("Nome","Descrição","Tipo","Motor","Num. Registros","Tamanho Médio","Tamanho Total","AI"));
    $relatorio->set_align(array("left","left"));
    $relatorio->set_conteudo($conteudo);
    $relatorio->show();

    $page->terminaPagina();
}