<?php

/**
 * Sistema GRH
 * 
 * Relatório
 *   
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("../sistema/_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {
    # Conecta ao Banco de Dados
    $servico = new Doc();

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Pega o banco e a tabela
    $banco = get('banco');
    $tabela = get('tabela');

    ######

    $select = "SELECT ORDINAL_POSITION,
                      COLUMN_NAME,
                      COLUMN_KEY,
                      EXTRA,
                      COLUMN_COMMENT,
                      COLUMN_TYPE,
                      CHARACTER_MAXIMUM_LENGTH,
                      COLUMN_DEFAULT,
                      IS_nullABLE
                 FROM COLUMNS 
                WHERE TABLE_SCHEMA = 'uenf_{$banco}' 
                  AND TABLE_NAME = '{$tabela}' 
             ORDER BY ORDINAL_POSITION";

    $conteudo = $servico->select($select);

    $relatorio = new Relatorio();
    $relatorio->set_titulo("Estrutura da Tabela");
    $relatorio->set_subtitulo($banco . " / " . $tabela);    
    $relatorio->set_label(array("#", "Nome", "Chave", "Extra", "Descrição", "Tipo", "Tamanho", "Padrão", "Nulo"));
    $relatorio->set_align(array("center", "left", "center", "center", "left"));
    $relatorio->set_conteudo($conteudo);
    $relatorio->show();

    $page->terminaPagina();
}