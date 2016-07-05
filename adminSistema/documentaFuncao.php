<?php
/**
 * documentaFuncao
 * 
 * Gera documentação de uma função
 */

# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso)
{    

    # Cabeçalho
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Pega a função a ser documentada
    $funcao = trim(get('funcao'));
    $fase = get('fase');
    $sistema = get('sistema');

    switch ($sistema)
    {
      case "Framework" :
          $pasta = PASTA_CLASSES_GERAIS;
          break;

      case "Grh" :
          $pasta = PASTA_CLASSES_GRH;
          break;

      case "Administracao" :
          $pasta = PASTA_CLASSES;
          break;
    }

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Botão voltar
    $linkBotao1 = new Link("Voltar",'documentaCodigo.php?fase='.$sistema);
    $linkBotao1->set_class('button');
    $linkBotao1->set_title('Volta para a página anterior');
    $linkBotao1->set_accessKey('V');

    # Botão codigo
    $linkBotao2 = new Link("Código","?funcao=$funcao&fase=codigo");
    if($fase == "codigo"){
        $linkBotao2->set_class('disabled button');
    }
    else{
        $linkBotao2->set_class('button');
    }
    $linkBotao2->set_title('Exibe o código fonte');
    $linkBotao2->set_accessKey('C');

    # Cria um menu
    $menu = new MenuBar();
    $menu->add_link($linkBotao1,"left");
    $menu->add_link($linkBotao2,"right");
    $menu->show();

    # Exibe a função
    if(!is_null($funcao)){

        # Divide a tela
        $grid2 = new Grid();
        $grid2->abreColuna(4,3);

        # Coluna do nome da função
        $callout = new Callout();
        $callout->abre();

        # Inicia a documentação
        $doc = new Documenta(PASTA_FUNCOES_GERAIS."/funcoes.gerais.php","funcao");

        # Pega os dados da funcao
        $nomeFuncao = $doc->get_nomeMetodo();
        $descricaoFuncao = $doc->get_descricaoMetodo();
        $deprecatedFuncao = $doc->get_deprecatedMetodo();
        $syntaxFuncao = $doc->get_syntaxMetodo();
        $retornoFuncao = $doc->get_retornoMetodo();
        $notaFuncao = $doc->get_notaMetodo();
        $parametrosFuncao = $doc->get_parametrosMetodo();
        $exemploFuncao = $doc->get_exemploMetodo();

        # Busca a função dentro do array
        $keyFuncao = array_search($funcao, $nomeFuncao);

        # Função
        echo '<h4>'.$nomeFuncao[$keyFuncao].'</h4>';

        $callout->fecha();
        $grid2->fechaColuna();

        # Coluna da documentação detalhada
        $grid2->abreColuna(8,9);
        $callout = new Callout("success");
        $callout->abre();

        switch ($fase)
        {
            case "" :    
                # Nome
                echo '<h5>'.$nomeFuncao[$keyFuncao].'</h5>';

                # Decrição
                echo $descricaoFuncao[$keyFuncao];
                br(2);

                # Deprecated        
                if((isset($deprecatedFuncao[$keyFuncao])) AND ($deprecatedFuncao[$keyFuncao])) {
                    br(2);
                    echo '<div class="callout alert">';
                    echo '<h6>DEPRECATED</h6> Esta Função deverá ser descontiuado nas próximas versões.<br/>Seu uso é desaconselhado.';
                    echo '</div>';
                }

                hr();

                # Syntax
                if(isset($syntaxFuncao[$keyFuncao])){
                    echo 'Sintaxe:';
                    echo '<pre>'.$syntaxFuncao[$keyFuncao].'</pre>';
                    br();
                }

                # Return
                if(isset($retornoFuncao[$keyFuncao])){
                  echo 'Valor Retornado:';
                  echo '<div class="callout secondary">';
                  echo $retornoFuncao[$keyFuncao];
                  echo '</div>';
                }

                # Nota
                if(isset($notaFuncao[$keyFuncao])){
                    echo 'Nota:';
                    $callout = new Callout("warning");
                    $callout->abre();
                    echo $notaFuncao[$keyFuncao];
                    $callout->fecha();
                }

                # Parâmetros
                if(isset($parametrosFuncao[$keyFuncao])){
                    echo 'Parâmetros:';

                    $tabela = new Tabela();
                    #array_shift($lista);     
                    $tabela->set_conteudo($parametrosFuncao[$keyFuncao]);
                    $tabela->set_label(array('Nome','Tipo','Padrão','Descrição'));
                    $tabela->set_align(array("center","center","center","left"));
                    $tabela->set_width(array(10,10,10,60));
                    $tabela->show();
                }

                # Exemplo
                if(isset($exemploFuncao[$keyFuncao])){
                    echo 'Exemplo:';
                    echo '<pre>';
                    $linesExample = file(PASTA_CLASSES_GERAIS."exemplos/".rtrim($exemploFuncao[$keyFuncao]));

                    # Percorre o arquivo e guarda os dados em um array
                    foreach ($linesExample as $linha) {
                        $linha = htmlspecialchars($linha);
                        echo $linha;
                    }
                    echo '</pre>';
                    br();
                }
                break;

            case "codigo" :
                echo '<pre>';

                # Define o arquivo da classe
                $arquivoExemplo = PASTA_FUNCOES_GERAIS."/funcoes.gerais.php";

                # Exibe o nome do arquivo
                echo str_repeat("#", 80);
                br();
                echo '# Arquivo:'.$arquivoExemplo;
                br();       
                echo str_repeat("#", 80);
                br(2);

                # Marca se é a função desejada
                $funcaoDesejada = FALSE;

                # variável que conta o número da linha
                $numLinha = 1;

                # Verifica a existência do arquivo
                if(file_exists($arquivoExemplo)){
                    $linesCodigo = file($arquivoExemplo);

                    # Percorre o arquivo e guarda os dados em um array
                    foreach ($linesCodigo as $linha) {
                        $linha = htmlspecialchars($linha);

                            if (stristr($linha, "function")){            
                                $posicao = stripos($linha,'function');   // marca posição da palavra function
                                $posicaoFinal = stripos($linha,'(');     // marca posição final do nome do método
                                $tamanho = $posicaoFinal-$posicao-9;     // define o tamanho 

                                $nome = substr($linha, $posicao+9,$tamanho);   // extrai o nome
                                if($nome == $nomeFuncao[$keyFuncao]){
                                    $funcaoDesejada = TRUE;
                                }else{
                                    $funcaoDesejada = FALSE;
                                }
                            }

                            if($funcaoDesejada){
                                # Exibe o número da linha
                                echo "<span id='numLinhaCodigo'>".formataNumLinha($numLinha)."</span> ";

                                # Exibe o código
                                echo $linha;

                                # Incrementa o ~umero da linha
                                $numLinha++;
                            }
                    }
                }
                else{
                    echo "Arquivo de exemplo não encontrado";
                }

                echo '</pre>';
                break;
        }

        $callout->fecha();
        $grid2->fechaColuna();
        $grid2->fechaGrid();
    }   

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
}