<?php
/**
 * Manual de Procedimentos
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario);

if($acesso){
    # Conecta ao Banco de Dados
    $procedimento = new Procedimento();
    $intra = new Intra();
    
    # Verifica a fase do programa
    $fase = get('fase');
    $sistema = get('sistema');
    
    # Pega od Ids
    $idProcedimento = get('idProcedimento',get_session('idProcedimento'));
    
    # Joga os parâmetros par as sessions
    set_session('idProcedimento',$idProcedimento);
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho da Página
    AreaServidor::cabecalho();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);  
    
    # Cria um menu
    #$menu1 = new MenuBar("button-group");

    # Sair da Área do Servidor
    $linkVoltar = new Link("Voltar","administracao.php");
    $linkVoltar->set_class('button');
    $linkVoltar->set_title('Voltar a página anterior');    
    #$menu1->add_link($linkVoltar,"left");

    # Procedimentos
    $linkProcedimento = new Link("Procedimentos","procedimentoNota.php");
    $linkProcedimento->set_class('button');
    $linkProcedimento->set_title('Gerencia as categorias');
    #$menu1->add_link($linkProcedimento,"right");

    #$menu1->show();
    
    # Título
    titulo("Documentação");
    
    # Define o grid
    $col1P = 0;
    $col1M = 4;
    $col1L = 3;

    $col2P = 12 - $col1P;
    $col2M = 12 - $col1M;
    $col2L = 12 - $col1L;
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna($col1P,$col1M,$col1L);
    br();
    
    # Menu Principal
    $menu = new Menu("menuProcedimentos");
    $grupoarquivo = NULL;
    $sistemas = ["Framework","Grh","Área do Servidor"];
    
    $menu->add_item('titulo','Sistemas','#','Acessa a documentação do sistema.');
    
    # Percorre os sistemas
    foreach($sistemas as $categorias){        
        $menu->add_item('link','📁 '.$categorias,'?fase=sistema&sistema='.$categorias,'Acessa a documentação do sistema '.$categorias);
    }
    
    $menu->show();
    $grid->fechaColuna();
    
    # Define a coluna de Conteúdo
    $grid->abreColuna($col2P,$col2M,$col2L);
    
    switch ($fase){        
        
    #############################################################################################################################
    #   Inicial
    ############################################################################################################################# 
        
        case "" :
            
            break;
   
    ############################################################################
        
        case "sistema" :
            
            $callout = new Callout();
            $callout->abre();
            
            tituloTable($sistema);
            
            $grid = new Grid();
            $grid->abreColuna(6);
            
            # Menu Principal
            $menu = new Menu("menuProcedimentos");
            $grupoarquivo = NULL;
            $sistemas = [["Framework",PASTA_CLASSES_GERAIS,PASTA_FUNCOES_GERAIS.'/funcoes.gerais.php'],
                         ["Grh",PASTA_CLASSES_GRH,PASTA_FUNCOES_GRH.'/funcoes.especificas.php'],
                         ["Area do Servidor",PASTA_CLASSES,PASTA_FUNCOES.'/funcoes.especificas.php']];

            # Percorre os sistemas
            foreach($sistemas as $categorias){

                if($sistema == $categorias[0]){

                    $menu->add_item('titulo','Classes','#','Acessa a documentação das classes so sistema '.$categorias[0]);

                    # Percorre o diretório das classes desse sistema
                    $dir = $categorias[1];

                    // Verificando a existência
                    if (is_dir($dir)){
                        
                        // Obtendo nome dos arquivos da(s) extensões especificadas
                        $Arquivos = glob("{$dir}/*.{php, html}", GLOB_BRACE);

                        // Verificando se houve resultado
                        if (is_array($Arquivos)){
                            
                            // Ordenando de forma ascendente (ASC)
                            sort($Arquivos);

                            echo '<dl>';
                            // Imprimindo o nome dos arquivos
                            foreach ($Arquivos as $Imagem){

                                $Imagem = basename($Imagem);

                                # Divide o nome do arquivos
                                $partesArquivo = explode('.',$Imagem);

                                if($grupoarquivo <> $partesArquivo[0]){
                                    $menu->add_item('link','📁 '.ucfirst($partesArquivo[0]),'#');

                                    $grupoarquivo = $partesArquivo[0];

                                    $menu->add_item('sublink',"📄 ".$partesArquivo[1],'documentaClasse.php?sistema='.$categorias[0].'&classe='.$partesArquivo[0].'.'.$partesArquivo[1]);
                                }else{
                                    $menu->add_item('sublink',"📄 ".$partesArquivo[1],'documentaClasse.php?sistema='.$categorias[0].'&classe='.$partesArquivo[0].'.'.$partesArquivo[1]);
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
            $grupoarquivo = NULL;
            $sistemas = [["Framework",PASTA_CLASSES_GERAIS,PASTA_FUNCOES_GERAIS.'/funcoes.gerais.php'],
                         ["Grh",PASTA_CLASSES_GRH,PASTA_FUNCOES_GRH.'/funcoes.especificas.php'],
                         ["Area do Servidor",PASTA_CLASSES,PASTA_FUNCOES.'/funcoes.especificas.php']];

            # Percorre os sistemas
            foreach($sistemas as $categorias){

                if($sistema == $categorias[0]){
                    
                    $menu->add_item('titulo','Funções','#','Acessa a documentação das funções do sistema '.$categorias[0]);
                    
                    # Lê e guarda no array $lines o conteúdo do arquivo
                    $lines = file ($categorias[2]);

                    # Percorre o array
                    foreach ($lines as $line_num => $line){
                      $line = htmlspecialchars($line);

                      # Função
                      if (stristr($line, "function")){
                        $posicao = stripos($line,'function');
                        $posicaoParentesis = stripos($line,'(');
                        $tamanhoNome = $posicaoParentesis - ($posicao+9);

                        $nomeFuncao[] = substr($line, $posicao+9,$tamanhoNome);
                      }
                    }

                    # Ordena array
                    sort($nomeFuncao);

                    # Exibe o array
                    foreach($nomeFuncao as $funcao){
                        
                        $menu->add_item('sublink',"📄 ".$funcao,'documentaFuncao.php?sistema='.$categorias[0].'&funcao='.$funcao);
                    }
                }
            }
            $menu->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();
           
            
            $callout->fecha();
            break;
        
    ############################################################################
        
        case "funcao" :
            
            # Monta o painel
            $painel = new Callout();
            $painel->abre();

           # Menu Principal
            $menu = new Menu("menuProcedimentos");
            $grupoarquivo = NULL;
            $sistemas = [["Framework",PASTA_CLASSES_GERAIS,PASTA_FUNCOES_GERAIS.'/funcoes.gerais.php'],
                         ["Grh",PASTA_CLASSES_GRH,PASTA_FUNCOES_GRH.'/funcoes.especificas.php'],
                         ["Area do Servidor",PASTA_CLASSES,PASTA_FUNCOES.'/funcoes.especificas.php']];

            # Percorre os sistemas
            foreach($sistemas as $categorias){

                if($sistema == $categorias[0]){
                    
                    $menu->add_item('titulo','<b>Funções '.$categorias[0].'</b>','#','Acessa a documentação das funções do sistema '.$categorias[0]);
                    
                    # Lê e guarda no array $lines o conteúdo do arquivo
                    $lines = file ($categorias[2]);

                    # Percorre o array
                    foreach ($lines as $line_num => $line){
                      $line = htmlspecialchars($line);

                      # Função
                      if (stristr($line, "function")){
                        $posicao = stripos($line,'function');
                        $posicaoParentesis = stripos($line,'(');
                        $tamanhoNome = $posicaoParentesis - ($posicao+9);

                        $nomeFuncao[] = substr($line, $posicao+9,$tamanhoNome);
                      }
                    }

                    # Ordena array
                    sort($nomeFuncao);

                    # Exibe o array
                    foreach($nomeFuncao as $funcao){
                        
                        $menu->add_item('link','- '.$funcao,'documentaFuncao.php?sistema='.$categorias[0].'&funcao='.$funcao);
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
}else{
    loadPage("login.php");
}