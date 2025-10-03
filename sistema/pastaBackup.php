<?php

/**
 * Administração
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 1);

$parametroAno = post('parametroAno', get_session('parametroAno', date("Y")));
$parametroMes = post('parametroMes', date("m"));
set_session('parametroAno', $parametroAno);
set_session('parametroMes', $parametroMes);

if ($acesso) {
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase'); # Qual a fase
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid1 = new Grid();
    $grid1->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

    # Botão voltar
    $linkBotao1 = new Link("Voltar", 'areaServidor.php?fase=menuAdmin');
    $linkBotao1->set_class('button');
    $linkBotao1->set_title('Volta para a página anterior');
    $linkBotao1->set_accessKey('V');
    $menu->add_link($linkBotao1, "left");

    # Backup Manual
    $linkBotao2 = new Link("Backup Manual", 'areaServidor.php?fase=backup');
    $linkBotao2->set_class('button');
    $linkBotao2->set_title('Backup manual do banco de dados');
    $menu->add_link($linkBotao2, "right");

    $menu->show();

    # Formulário de Pesquisa
    $form = new Form('?fase=pastaBackup');

    # Cria um array com os anos possíveis
    $anoAtual = date('Y');
    $anoInicial = $anoAtual - 1;
    $anoExercicio = array($anoInicial, $anoAtual);

    $controle = new Input('parametroAno', 'combo', 'Ano:', 1);
    $controle->set_size(50);
    $controle->set_title('Filtra por Ano exercício');
    $controle->set_array($anoExercicio);
    $controle->set_valor($parametroAno);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_col(2);
    $controle->set_linha(1);
    $form->add_item($controle);

    $controle = new Input('parametroMes', 'combo', "Mês", 1);
    $controle->set_size(30);
    $controle->set_title('O mês do backup');
    $controle->set_array($mes);
    $controle->set_valor($parametroMes);
    $controle->set_onChange('formPadrao.submit();');
    $controle->set_col(3);
    $controle->set_linha(1);
    $form->add_item($controle);

    $form->show();

    ##############################
    # Título
    tituloTable('Pasta de Backup');
    br();

    # Executa o backup no Linux
    #$output = shell_exec('ls ../../_backup');
    #echo "<pre>$output</pre>";
    # Define a pasta
    $pasta = '/var/www/html/_backup/';

    if (is_dir($pasta)) {
        $diretorio = dir($pasta);

        # Percorre os arquivos
        while ($arquivo = $diretorio->read()) {

            # Retira os diretórios
            if ($arquivo <> '..' AND $arquivo <> '.') {

                # Cria um Array com todos os Arquivos encontrados
                $arrayArquivos[] = $arquivo;
            }
        }
        $diretorio->close();
    }

    # Cria títulos de ordenação
    $mes = null;
    $diaselec = 0;

    if (isset($arrayArquivos)) {
        # Limita ainda mais
        $grid = new Grid();
        $grid->abreColuna(12);

        # Classificar os arquivos para a Ordem Crescente
        sort($arrayArquivos, SORT_STRING);

        # Abre a grid
        $grid = new Grid();

        # Mostra a listagem dos Arquivos
        foreach ($arrayArquivos as $valorArquivos) {

            # Pega o ano do arquivo
            $ano = substr($valorArquivos, 0, 4);
            $mes = substr($valorArquivos, 5, 2);
            $dia = substr($valorArquivos, 8, 2);
            $hora = substr($valorArquivos, 11, 2);
            $minuto = substr($valorArquivos, 14, 2);
            $segundo = substr($valorArquivos, 17, 2);

            # Compara se é o ano desejado
            if ($ano == $parametroAno) {

                # Compara se já teve título do mês
                if ($mes == $parametroMes) {

                    if ($dia <> $diaselec) {

                        # Fecha a janela
                        if ($diaselec <> 0) {
                            br();
                            $field->fecha();
                            $grid->fechaColuna();
                        }

                        # muda o dia selecionado
                        $diaselec = $dia;

                        # Abre a coçuna
                        $grid->abreColuna(12, 6, 4);
                        $field = new Fieldset();
                        $field->abre();
                        titulotable($dia . " - " . diaSemana("{$dia}/{$mes}/{$ano}"));
                        br();
                    }
                    # Exibe o arquivo
                    echo "<a href=/_backup/$valorArquivos>Dia $dia - $hora:$minuto:$segundo</a><br />";
                }
            }
        }

        br();

        if (isset($field)) {
            $field->fecha();
        }
        $grid->fechaGrid();
    } else {
        br(3);
        p("Não existe nenhum arquivo de backup!", "center");
    }

    $grid1->fechaColuna();
    $grid1->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}