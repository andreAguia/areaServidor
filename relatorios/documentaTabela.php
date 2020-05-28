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
                      IS_NULLABLE
                 FROM COLUMNS 
                WHERE TABLE_SCHEMA = '" . $banco . "' 
                  AND TABLE_NAME = '" . $tabela . "'";

    $conteudo = $servico->select($select);

    $relatorio = new Relatorio();
    $relatorio->set_titulo($banco . " / " . $tabela);
    #$relatorio->set_subtitulo('Agrupados por Cargo - Ordenados pelo Nome');
    $relatorio->set_label(array("#", "Nome", "Chave", "Extra", "Descrição", "Tipo", "Tamanho", "Padrão", "Nulo"));
    $relatorio->set_align(array("center", "left", "center", "center", "left"));
    $relatorio->set_conteudo($conteudo);
    $relatorio->show();

    $page->terminaPagina();
}