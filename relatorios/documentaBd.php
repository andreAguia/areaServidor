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

    # Verifica o banco
    $banco = get('banco');

    ######

    $select = "SELECT TABLE_NAME,
                      TABLE_COMMENT,
                      ENGINE,
                      TABLE_ROWS,
                      AVG_ROW_LENGTH,
                      DATA_LENGTH,
                      AUTO_INCREMENT
                 FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'uenf_{$banco}'";

    $conteudo = $servico->select($select);

    $relatorio = new Relatorio();
    $relatorio->set_titulo("Lista de Tabelas do Banco de Dados");
    $relatorio->set_subtitulo($banco);
    $relatorio->set_label(["Nome", "Descrição", "Motor", "Num. Registros", "Tamanho Médio", "Tamanho Total", "Auto Incremento"]);
    $relatorio->set_align(["left", "left"]);
    $relatorio->set_conteudo($conteudo);
    $relatorio->show();

    $page->terminaPagina();
}