<?php
/**
 * documentaClasse
 * 
 * Gera documentação de uma classe
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){    
    # Cabeçalho
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Pega a classe a ser documentada
    $arquivoClasse = get('classe'); // Classe a ser exibida
    $metodo = get('metodo');        // Método a ser exibido, se for "" exibe os dados da classe, se for "codigo" exibe o código
    $sistema = get('sistema');      // Informa a pasta a ser lido

    switch ($sistema){
      case "Framework" :
          $pasta = PASTA_CLASSES_GERAIS;
          break;

      case "Grh" :
          $pasta = PASTA_CLASSES_GRH;
          break;

      case "areaServidor" :
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
        $linkBotao2 = new Link("Código","?sistema=$sistema&classe=$arquivoClasse&metodo=codigo");
        $linkBotao2->set_class('button');
        $linkBotao2->set_title('Exibe o código fonte');
        $linkBotao2->set_accessKey('C');

        # Cria um menu
        $menu = new MenuBar();
        $menu->add_link($linkBotao1,"left");
        $menu->add_link($linkBotao2,"right");
        $menu->show();

    $grid->fechaColuna();
    $grid->fechaGrid();

    # Divide a tela
    $grid2 = new Grid();
    $grid2->abreColuna(4,3);

    # Coluna de atalhos para os métodos da classe
    $callout = new Callout();
    $callout->abre();

    # Inicia a documentação
    $doc = new Documenta($pasta.$arquivoClasse.".php","classe");

    # Pega os dados da classe
    $nomeClasse = $doc->get_nomeClasse();
    $descricaoClasse = $doc->get_descricaoClasse();
    $autorClasse = $doc->get_autorClasse();
    $notaClasse = $doc->get_notaClasse();
    $deprecatedClasse = $doc->get_deprecatedClasse();
    $numVariaveis = $doc->get_numVariaveis();
    $variaveisClasse = $doc->get_variaveisClasse();
    $exemploClasse = $doc->get_exemploClasse();

    # Pega os dados do método
    $nomeMetodo = $doc->get_nomeMetodo();
    $numMetodo = $doc->get_numMetodo();
    $visibilidadeMetodo = $doc->get_visibilidadeMetodo();
    $descricaoMetodo = $doc->get_descricaoMetodo();
    $deprecatedMetodo = $doc->get_deprecatedMetodo();
    $syntaxMetodo = $doc->get_syntaxMetodo();
    $retornoMetodo = $doc->get_retornoMetodo();
    $notaMetodo = $doc->get_notaMetodo();
    $parametrosMetodo = $doc->get_parametrosMetodo();
    $exemploMetodo = $doc->get_exemploMetodo();

    # Classe
    echo '<h4>';
    echo '<a href="?sistema='.$sistema.'&classe='.$arquivoClasse.'">';
    echo $nomeClasse;
    echo '</a>';
    echo '</h4>';
    
    hr();
    
    # Percorre as variáveis
    
    if($numVariaveis > 0){
        for ($i=1; $i < $numVariaveis;$i++){
            span($variaveisClasse[$i][1]." (".$variaveisClasse[$i][2].")",NULL,NULL,$variaveisClasse[$i][4]);
            br();
        }
        hr();
    }
    
    # Percorre os métodos
    for ($i=1; $i <= $numMetodo;$i++){
        # link
        echo '<a href="?sistema='.$sistema.'&classe='.$arquivoClasse.'&metodo='.$i.'" title="('.$visibilidadeMetodo[$i].') '.$descricaoMetodo[$i].'">';
        if((isset($deprecatedMetodo[$i])) AND ($deprecatedMetodo[$i])){
            span('<del>'.$nomeMetodo[$i].'</del>',$visibilidadeMetodo[$i]);
        }else{
            span($nomeMetodo[$i],$visibilidadeMetodo[$i]);
        }
        echo '</a>';
        br();
    }
    #echo "</table>";
    $callout->fecha();
    $grid2->fechaColuna();

    # Coluna da documentação detalhada
    $grid2->abreColuna(8,9);

    switch ($metodo){
        case "" :
            ### Classe
            $callout = new Callout("primary");
            $callout->abre();

            # Nome
            echo '<h4>'.$nomeClasse.'</h4>';

            # Decrição
            echo $descricaoClasse;
            br(2);

            # Autor
            if(!is_null($autorClasse))
                echo '<small>Autor: '.$autorClasse.'</small>';

            hr();

            # Nota
            if(!is_null($notaClasse)){
                # Vê quantas notas existem
                $qtdadeNotaClasse = count($notaClasse);

                # Percorre as notas
                for ($i = 0; $i < $qtdadeNotaClasse; $i++) {

                    echo 'Nota: ';

                    if($qtdadeNotaClasse > 1){
                        echo ($i+1);
                    }

                    # Exibe a nota
                    $callout = new Callout("warning");
                    $callout->abre();
                        echo $notaClasse[$i];
                    $callout->fecha();
                }
            }

            # Deprecated
            if($deprecatedClasse){
                $callout = new Callout("alert");
                $callout->abre();
                    echo '<h6>DEPRECATED</h6> Esta classe deverá ser descontiuada nas próximas versões.<br/>Seu uso é desaconselhado.';
                $callout->fecha();
            }

            # Variáveis da Classe
            if($numVariaveis > 0){
                echo 'Variáveis da Classe:';
                br();
                $novoArray = NULL;      // Armazena a tabela
                $grupoAnterior = NULL;  // Guarda o nome do grupo anterior
                $grupo = 0;             // Qual grupo será exibido

                foreach ($variaveisClasse as $vc){
                    if($vc[0] == "group"){
                        if($grupo == 0){
                            $grupoAnterior = $vc[1];
                            $grupo++;
                        }elseif($grupo > 0) {
                            echo $grupoAnterior;
                            br();

                            if($grupo == 1){
                                array_shift($novoArray);
                            }

                            $tabela = new Tabela();
                            $tabela->set_conteudo($novoArray);
                            $tabela->set_label(array('Visibilidade','Nome','Tipo','Padrão','Descrição'));
                            $tabela->set_align(array("center","center","center","center","left"));
                            $tabela->set_width(array(10,10,10,10,60));
                            $tabela->show(); 

                            $grupoAnterior = $vc[1];
                            $novoArray = NULL;
                            $grupo++;
                        }                 
                    }else{
                        $novoArray[] = $vc;
                    }   
                }
                
                # Exibe a lista de variáveis quando não se definiu grupos    
                if($grupo == 0){        
                    $tabela = new Tabela();
                    array_shift($variaveisClasse);     
                    $tabela->set_conteudo($variaveisClasse);
                    $tabela->set_label(array('Visibilidade','Nome','Tipo','Padrão','Descrição'));
                    $tabela->set_align(array("center","center","center","center","left"));
                    $tabela->set_width(array(10,10,10,10,60));
                    $tabela->show();
                }else{
                    # Exibe o último grupo de variáveis
                    echo $grupoAnterior;
                    br();
                    $tabela = new Tabela();
                    #array_shift($variaveisClasse);     
                    $tabela->set_conteudo($novoArray);
                    $tabela->set_label(array('Visibilidade','Nome','Tipo','Padrão','Descrição'));
                    $tabela->set_align(array("center","center","center","center","left"));
                    $tabela->set_width(array(10,10,10,10,60));
                    $tabela->show(); 
                }            
            }

            # Exemplo
            if(!is_null($exemploClasse)){
                # Define o arquivo de exemplo
                $arquivoExemplo = PASTA_CLASSES_GERAIS."exemplos/".rtrim($exemploClasse);

                # Verifica se o arquivo existe
                if(file_exists($arquivoExemplo)){

                    # Exibe o exemplo
                    echo 'Exemplo:';
                    echo '<pre>';

                    # Variável que conta o número da linha
                    $numLinhaExemplo = 1;

                    # Percorre o arquivo
                    $linesExample = file($arquivoExemplo);

                    # Percorre o arquivo e guarda os dados em um array
                    foreach ($linesExample as $linha) {
                        $linha = htmlspecialchars($linha);

                        # Exibe o número da linha
                        echo "<span id='numLinhaCodigo'>".formataNumLinha($numLinhaExemplo)."</span> ";

                        # Exibe o código
                        echo $linha;

                        # Incrementa o ~umero da linha
                        $numLinhaExemplo++;
                    }
                    echo '</pre>';
                    
                    br();

                    # Roda o exemplo
                    echo 'O exemplo acima exibirá o seguinte resultado:';
                    
                    # Cria borda para o exemplo
                    $calloutExemplo = new Callout();
                    $calloutExemplo->abre();

                    include PASTA_CLASSES_GERAIS."exemplos/".rtrim($exemploClasse);

                    $calloutExemplo->fecha();
                }else{
                    echo 'Exemplo:';
                    $callout1 = new Callout();
                    $callout1->abre();
                    echo "Arquivo de exemplo não encontrado";
                    $callout1->fecha();
                }
            }            
            break;

        case "codigo" :
            echo '<pre>';

            # Define o arquivo da classe
            $arquivoExemplo = PASTA_CLASSES_GERAIS.rtrim($arquivoClasse).".php";

            # Exibe o nome do arquivo
            echo str_repeat("#", 80);
            br();
            echo '# Arquivo:'.$arquivoExemplo;
            br();       
            echo str_repeat("#", 80);
            br(2);

            # variável que conta o número da linha
            $numLinha = 1;

            # Verifica a existência do arquivo
            if(file_exists($arquivoExemplo)){
                $linesCodigo = file($arquivoExemplo);

                # Percorre o arquivo e guarda os dados em um array
                foreach ($linesCodigo as $linha) {
                    $linha = htmlspecialchars($linha);

                        # Exibe o número da linha
                        echo "<span id='numLinhaCodigo'>".formataNumLinha($numLinha)."</span> ";

                        # Exibe o código
                        echo $linha;

                        # Incrementa o ~umero da linha
                        $numLinha++;
                }
            }else{
                echo "Arquivo de exemplo não encontrado";
            }

            echo '</pre>';
            break;

        default:
            ### Método
            $callout = new Callout();
            $callout->abre();

            # Nome
            echo '<h5> Método '.$nomeMetodo[$metodo].'</h5>';

            # Visibilidade
            echo '<small>('.$visibilidadeMetodo[$metodo].')</small>';

            # Descrição
            br(2);
            echo $descricaoMetodo[$metodo];

            # Deprecated        
            if((isset($deprecatedMetodo[$metodo])) AND ($deprecatedMetodo[$metodo])) {
                br(2);

                $callout1 = new Callout();
                $callout1->abre("alert");
                    echo '<h6>DEPRECATED</h6> Este método deverá ser descontiuado nas próximas versões.<br/>Seu uso é desaconselhado.';
                $callout1->fecha();
            }

            hr();

            # Syntax do método
            if(isset($syntaxMetodo[$metodo])){
                echo 'Sintaxe:';
                echo '<pre>'.$syntaxMetodo[$metodo].'</pre>';
                p('Parâmetros entre [ ] são opcionais.','right','f10');
            }

            # Return
            if(isset($retornoMetodo[$metodo])){
              echo 'Valor Retornado:';

              $callout = new Callout();
              $callout->abre();
                echo $retornoMetodo[$metodo];
              $callout->fecha();
            }

            # Nota
            if(isset($notaMetodo[$metodo])){
                # Vê quantas notas existem
                $qtdadeNotaMetodo = count($notaMetodo[$metodo]);

                # Percorre as notas
                for ($i = 0; $i < $qtdadeNotaMetodo; $i++) {

                    echo 'Nota: ';

                    if($qtdadeNotaMetodo > 1){
                        echo ($i+1);
                    }

                    # Exibe a nota
                    $callout = new Callout("warning");
                    $callout->abre();
                        echo $notaMetodo[$metodo][$i];
                    $callout->fecha();
                }
            }

            # Parâmetros de um método
            if(isset($parametrosMetodo[$metodo])){
                echo 'Parâmetros:';

                $tabela = new Tabela();
                #array_shift($lista);     
                $tabela->set_conteudo($parametrosMetodo[$metodo]);
                $tabela->set_label(array('Nome','Tipo','Padrão','Descrição'));
                $tabela->set_align(array("center","center","center","left"));
                $tabela->set_width(array(10,10,10,60));
                $tabela->show();
            }

            # Exemplo
            if(isset($exemploMetodo[$metodo])){
                echo 'Exemplo:';
                echo '<pre>';
                $linesExample = file(PASTA_CLASSES_GERAIS."exemplos/".rtrim($exemploMetodo[$metodo]));

                # Percorre o arquivo e guarda os dados em um array
                foreach ($linesExample as $linha) {
                    $linha = htmlspecialchars($linha);
                    echo $linha;
                }
                echo '</pre>';
                br();
            }
            break;     
    }

    $callout->fecha();

    $grid2->fechaColuna();
    $grid2->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}
