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

    # Pega a classe a ser documentada
    $arquivoClasse = get('classe'); // Classe a ser exibida    
    $sistema = get('sistema');      // Informa a pasta a ser lido
    $metodo = get('metodo');        // Método a ser exibido, se for "" exibe os dados da classe, se for "codigo" exibe o código

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
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

    # Botão voltar
    if($metodo == "codigo"){
        # Se estiver exibindo o código o voltar volta para a classe
        $linkBotao1 = new Link("Voltar",'?sistema='.$sistema.'&classe='.$arquivoClasse);
        $linkBotao1->set_class('button');
        $linkBotao1->set_title('Volta para a página anterior');
        $linkBotao1->set_accessKey('V');
        $menu->add_link($linkBotao1,"left");
    }else{
        # Se estiver exibindo a classe o voltar volta para o menu de classes
        $linkBotao1 = new Link("Voltar",'documentacao.php?fase=sistema&sistema='.$sistema);
        $linkBotao1->set_class('button');
        $linkBotao1->set_title('Volta para a página anterior');
        $linkBotao1->set_accessKey('V');
        $menu->add_link($linkBotao1,"left");
    }        

    # Botão codigo
    if($metodo <> "codigo"){
        $linkBotao2 = new Link("Exibe o Código","?sistema=$sistema&classe=$arquivoClasse&metodo=codigo");
        $linkBotao2->set_class('button');
        $linkBotao2->set_title('Exibe o código fonte');
        $linkBotao2->set_accessKey('C');
        $menu->add_link($linkBotao2,"right");
    }

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
    $doc = new DocumentaClasse($pasta.$arquivoClasse.".php");

    # Pega os dados da classe
    $nomeClasse = $doc->get_nomeClasse();
    $abstracaoClasse = $doc->get_abstracaoClasse();
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
    
    # Define array de visibilidade
    $tipoVisibilidade = [
        "private" => "-",
        "public" => "+",
        "protected" => "#"
        ];

    # Classe    
    echo '<a href="?sistema='.$sistema.'&classe='.$arquivoClasse.'">';
    p($nomeClasse,"documentacaoNomeClasse");
    echo '</a>';
    
    # Abstração
    if(!is_null($abstracaoClasse)){
        p($abstracaoClasse,"documentacaoAutor");
    }
    
    hr("documentacao");
    
    # Percorre as variáveis
    if($numVariaveis > 0){
        for ($i=0; $i < $numVariaveis;$i++){            
            p($tipoVisibilidade[$variaveisClasse[$i][0]]." <b>".$variaveisClasse[$i][1].":</b> ".$variaveisClasse[$i][2],"documentaAtributos",NULL,"(".$variaveisClasse[$i][0].") ".$variaveisClasse[$i][4]);
        }
        hr("documentacao");
    }
    
    # Percorre os métodos    
    for ($i=1; $i <= $numMetodo;$i++){
        
        # Verifica se é deprecated
        if((isset($deprecatedMetodo[$i])) AND ($deprecatedMetodo[$i])){
            echo "<del>";
        }
        
        # link
        $link = new Link($tipoVisibilidade["$visibilidadeMetodo[$i]"]." ".$nomeMetodo[$i]."()","?sistema=$sistema&classe=$arquivoClasse&metodo=$i");
        $link->set_title("(".$visibilidadeMetodo[$i].") ".$descricaoMetodo[$i]);
        $link->set_id("documentaMetodo");
        $link->show();
        br();
        
        # fecha o depricated
        if((isset($deprecatedMetodo[$i])) AND ($deprecatedMetodo[$i])){
            echo "</del>";
        }
    }
    
    $callout->fecha();
    $grid2->fechaColuna();
    
################################################################################    

    # Coluna da documentação detalhada
    $grid2->abreColuna(8,9);

    switch ($metodo){
        case "" :
            ### Classe
            $callout = new Callout("primary");
            $callout->abre();
            
            # Abstração
            if(!is_null($abstracaoClasse)){
                $div = new Div("divAbstracaoClasse");
                $div->abre();
                if($abstracaoClasse == "abstract"){
                    label("Classe Abstrata","secondary");
                }

                if($abstracaoClasse == "final"){
                    label("Classe Final","secondary");
                }
                $div->fecha();
            }

            # Nome
            p($nomeClasse,"documentacaoNomeClassePrincipal");
            
            # Decrição
            p($descricaoClasse,"documentacaoDescricaoClasse");
            
            # Autor
            if(!is_null($autorClasse)){
                p('Autor: '.$autorClasse,"documentacaoAutor");
            }

            hr("documentacao");
            br();
            
            # Abstração
            if(!is_null($abstracaoClasse)){
                if($abstracaoClasse == "abstract"){
                    callout("Classes Abstratas são super classes que não podem ser instanciadas diretamente, somente por herança.");
                }
                
                if($abstracaoClasse == "final"){
                    callout("Classes Final são classes que não podem ser herdadas.");
                }                
            }

            # Nota
            if(!is_null($notaClasse)){
                # Vê quantas notas existem
                $qtdadeNotaClasse = count($notaClasse);

                # Percorre as notas
                for ($i = 0; $i < $qtdadeNotaClasse; $i++) {

                    # Exibe a nota
                    $callout = new Callout("warning");
                    $callout->abre();
                        p($notaClasse[$i],"documentacaoNota");
                    $callout->fecha();
                }
            }

            # Deprecated
            if($deprecatedClasse){
                $callout = new Callout("alert");
                $callout->abre();
                    p('<h6>DEPRECATED</h6> Esta classe deverá ser descontiuada nas próximas versões.<br/>Seu uso é desaconselhado.',"p#documentacaoDeprecated");
                $callout->fecha();
            }

            # Variáveis da Classe
            if($numVariaveis > 0){
                p('Variáveis da Classe:',"documentacaoDescricaoClasse");
                
                # Gera a tabela
                $tabela = new Tabela("tableVariaveisClasse");
                $tabela->set_conteudo($variaveisClasse);
                $tabela->set_label(array('Visibilidade','Nome','Tipo','Padrão','Descrição'));
                $tabela->set_align(array("center","left","center","center","left"));
                $tabela->set_totalRegistro(FALSE);
                $tabela->show();
                br();
            }

            # Exemplo
            if(!is_null($exemploClasse)){
                # Define o arquivo de exemplo
                $arquivoExemplo = PASTA_CLASSES_GERAIS."exemplos/".rtrim($exemploClasse);

                # Verifica se o arquivo existe
                if(file_exists($arquivoExemplo)){

                    # Exibe o exemplo
                    p('Exemplo:',"documentacaoDescricaoClasse");
                    echo '<pre>';

                    # Variável que conta o número da linha
                    $numLinhaExemplo = 1;

                    # Percorre o arquivo
                    $linesExample = file($arquivoExemplo);

                    # Percorre o arquivo e guarda os dados em um array
                    foreach ($linesExample as $linha) {
                        $linha = htmlspecialchars($linha);

                        # Exibe o número da linha
                        #echo "<span id='numLinhaCodigo'>".formataNumLinha($numLinhaExemplo)."</span> ";

                        # Exibe o código
                        echo $linha;

                        # Incrementa o ~umero da linha
                        $numLinhaExemplo++;
                    }
                    echo '</pre>';
                    
                    br();

                    # Roda o exemplo
                    p('O exemplo acima exibirá o seguinte resultado:',"documentacaoDescricaoClasse");
                    
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
            
    #######################################################################
        
        default:
            ### Método
            $callout = new Callout("primary");
            $callout->abre();
            
            # Visibilidade
            p($visibilidadeMetodo[$metodo],"documentacaoVisibilidadeMetodo");

            # Nome
            echo '<h4> Método '.$nomeMetodo[$metodo].'</h4>';
            
            # Descrição
            p($descricaoMetodo[$metodo],"documentacaoDescricaoClasse");

            hr("documentacao");
            br();

            # Deprecated        
            if((isset($deprecatedMetodo[$metodo])) AND ($deprecatedMetodo[$metodo])){
                $callout = new Callout("alert");
                $callout->abre();
                    p('<h6>DEPRECATED</h6> Este método deverá ser descontiuado nas próximas versões.<br/>Seu uso é desaconselhado.',"p#documentacaoDeprecated");
                $callout->fecha();
            }

            # Syntax do método
            if(isset($syntaxMetodo[$metodo])){
                p('Sintaxe:',"documentacaoDescricaoClasse");
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

                    # Exibe a nota
                    $callout = new Callout("warning");
                    $callout->abre();
                        p($notaMetodo[$metodo][$i],"documentacaoNota");
                    $callout->fecha();
                }
            }

            # Parâmetros de um método
            if(isset($parametrosMetodo[$metodo])){
                p('Parâmetros:',"documentacaoDescricaoClasse");

                $tabela = new Tabela("tableVariaveisClasse");
                #array_shift($lista);     
                $tabela->set_conteudo($parametrosMetodo[$metodo]);
                $tabela->set_label(array('Nome','Tipo','Padrão','Descrição'));
                $tabela->set_align(array("center","center","center","left"));
                $tabela->set_width(array(10,10,10,60));
                $tabela->set_totalRegistro(FALSE);
                $tabela->show();
                br();
            }

            # Exemplo
            if(isset($exemploMetodo[$metodo])){
                p('Exemplo:',"documentacaoDescricaoClasse");
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
            
    #######################################################################
            
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
            
    #######################################################################
            
    }

    $callout->fecha();

    $grid2->fechaColuna();
    $grid2->fechaGrid();

    $page->terminaPagina();
}else{
    loadPage("login.php");
}
