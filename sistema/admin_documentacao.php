<?php

/**
 * Manual de Procedimentos
 *  
 * By Alat
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {
    # Conecta ao Banco de Dados
    $procedimento = new Procedimento();
    $intra = new Intra();

    # Verifica a fase do programa
    $fase = get('fase');
    $sistema = get('sistema', "Framework");

    # Pega od Ids
    $idProcedimento = get('idProcedimento', get_session('idProcedimento'));

    # Joga os parâmetros par as sessions
    set_session('idProcedimento', $idProcedimento);

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();
    br();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Define os sistemas
    $sistemas = ["Framework", "Grh", "Área do Servidor"];

    # Cria um menu
    $menu1 = new MenuBar("button-group");

    # Voltar
    $linkVoltar = new Link("Voltar", 'areaServidor.php?fase=menuAdmin');
    $linkVoltar->set_class('button');
    $linkVoltar->set_title('Voltar a página anterior');
    $menu1->add_link($linkVoltar, "left");

    # Sispemas
    foreach ($sistemas as $categorias) {
        $link = new Link($categorias, "?fase=sistema&sistema={$categorias}");

        if ($sistema == $categorias) {
            $link->set_class('button');
        } else {
            $link->set_class('hollow button');
        }
        $link->set_title('Gerencia as categorias');
        $menu1->add_link($link, "right");
    }

    $menu1->show();

    switch ($fase) {

        case "sistema" :

            tituloTable("Documentação", null, $sistema);

            $grid = new Grid();
            $grid->abreColuna(6);

            # Menu Principal
            $menu = new Menu("menuProcedimentos");
            $grupoarquivo = null;
            $sistemas = [
                ["Framework", PASTA_CLASSES_GERAIS, PASTA_FUNCOES_GERAIS . '/funcoes.gerais.php'],
                ["Grh", PASTA_CLASSES_GRH, PASTA_FUNCOES_GRH . '/funcoes.especificas.php'],
                ["Area do Servidor", PASTA_CLASSES, PASTA_FUNCOES . '/funcoes.especificas.php']
            ];

            # Percorre os sistemas
            foreach ($sistemas as $categorias) {

                if ($sistema == $categorias[0]) {

                    $menu->add_item('titulo', 'Classes', '#', 'Acessa a documentação das classes so sistema ' . $categorias[0]);

                    # Percorre o diretório das classes desse sistema
                    $dir = $categorias[1];

                    // Verificando a existência
                    if (is_dir($dir)) {

                        // Obtendo nome dos arquivos da(s) extensões especificadas
                        $Arquivos = glob("{$dir}/*.{php, html}", GLOB_BRACE);

                        // Verificando se houve resultado
                        if (is_array($Arquivos)) {

                            // Ordenando de forma ascendente (ASC)
                            sort($Arquivos);

                            echo '<dl>';
                            // Imprimindo o nome dos arquivos
                            foreach ($Arquivos as $Imagem) {

                                $Imagem = basename($Imagem);

                                # Divide o nome do arquivos
                                $partesArquivo = explode('.', $Imagem);

                                if ($grupoarquivo <> $partesArquivo[0]) {
                                    $menu->add_item('link', '📁 ' . ucfirst($partesArquivo[0]), '#');

                                    $grupoarquivo = $partesArquivo[0];

                                    $menu->add_item('sublink', "📄 " . $partesArquivo[1], 'documentaClasse.php?sistema=' . $categorias[0] . '&classe=' . $partesArquivo[0] . '.' . $partesArquivo[1]);
                                } else {
                                    $menu->add_item('sublink', "📄 " . $partesArquivo[1], 'documentaClasse.php?sistema=' . $categorias[0] . '&classe=' . $partesArquivo[0] . '.' . $partesArquivo[1]);
                                }
                            }
                        }
                    }
                }
            }

            $menu->show();

            $grid->fechaColuna();
            $grid->abreColuna(6);

            # Menu Principal
            $menu = new Menu("menuProcedimentos");
            $grupoarquivo = null;
            $sistemas = [
                ["Framework", PASTA_CLASSES_GERAIS, PASTA_FUNCOES_GERAIS . '/funcoes.gerais.php'],
                ["Grh", PASTA_CLASSES_GRH, PASTA_FUNCOES_GRH . '/funcoes.especificas.php'],
                ["Area do Servidor", PASTA_CLASSES, PASTA_FUNCOES . '/funcoes.especificas.php']
            ];

            # Percorre os sistemas
            foreach ($sistemas as $categorias) {

                if ($sistema == $categorias[0]) {

                    $menu->add_item('titulo', 'Funções', '#', 'Acessa a documentação das funções do sistema ' . $categorias[0]);

                    # Lê e guarda no array $lines o conteúdo do arquivo
                    $lines = file($categorias[2]);

                    # Percorre o array
                    foreach ($lines as $line_num => $line) {
                        $line = htmlspecialchars($line);

                        # Função
                        if (stristr($line, "function")) {
                            $posicao = stripos($line, 'function');
                            $posicaoParentesis = stripos($line, '(');
                            $tamanhoNome = $posicaoParentesis - ($posicao + 9);

                            $nomeFuncao[] = substr($line, $posicao + 9, $tamanhoNome);
                        }
                    }

                    # Ordena array
                    sort($nomeFuncao);

                    # Exibe o array
                    foreach ($nomeFuncao as $funcao) {

                        $menu->add_item('sublink', "📄 " . $funcao, 'documentaFuncao.php?sistema=' . $categorias[0] . '&funcao=' . $funcao);
                    }
                }
            }
            $menu->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

        ############################################################################

        case "funcao" :

            # Monta o painel
            $painel = new Callout();
            $painel->abre();

            # Menu Principal
            $menu = new Menu("menuProcedimentos");
            $grupoarquivo = null;
            $sistemas = [["Framework", PASTA_CLASSES_GERAIS, PASTA_FUNCOES_GERAIS . '/funcoes.gerais.php'],
                ["Grh", PASTA_CLASSES_GRH, PASTA_FUNCOES_GRH . '/funcoes.especificas.php'],
                ["Area do Servidor", PASTA_CLASSES, PASTA_FUNCOES . '/funcoes.especificas.php']];

            # Percorre os sistemas
            foreach ($sistemas as $categorias) {

                if ($sistema == $categorias[0]) {

                    $menu->add_item('titulo', '<b>Funções ' . $categorias[0] . '</b>', '#', 'Acessa a documentação das funções do sistema ' . $categorias[0]);

                    # Lê e guarda no array $lines o conteúdo do arquivo
                    $lines = file($categorias[2]);

                    # Percorre o array
                    foreach ($lines as $line_num => $line) {
                        $line = htmlspecialchars($line);

                        # Função
                        if (stristr($line, "function")) {
                            $posicao = stripos($line, 'function');
                            $posicaoParentesis = stripos($line, '(');
                            $tamanhoNome = $posicaoParentesis - ($posicao + 9);

                            $nomeFuncao[] = substr($line, $posicao + 9, $tamanhoNome);
                        }
                    }

                    # Ordena array
                    sort($nomeFuncao);

                    # Exibe o array
                    foreach ($nomeFuncao as $funcao) {

                        $menu->add_item('link', '- ' . $funcao, 'documentaFuncao.php?sistema=' . $categorias[0] . '&funcao=' . $funcao);
                    }
                }
            }
            $menu->show();

            # Fecha o painel
            $painel->fecha();
            break;

        ############################################################################    
    }

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}