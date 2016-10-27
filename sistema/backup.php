<?php
/**
 * Backup
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Verifica a fase do programa
    $fase = get('fase');        # Qual a fase
    $manual = get('manual');	# Usado na rotina de backup, verifica se o backup é automático ou manual
    $arquivoSql = get('arquivoSql');  # Usado para se exibir um arquivo sql
    
    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Cabeçalho
    AreaServidor::cabecalho();
    
    switch ($fase){
        case "" :
            #limita a tela
            $grid1 = new Grid();
            $grid1->abreColuna(12);
            
            # Botão voltar
            $linkBotaoVoltar = new Link("Voltar",'administracao.php');
            $linkBotaoVoltar->set_class('button float-left');
            $linkBotaoVoltar->set_title('Volta para a página anterior');
            $linkBotaoVoltar->set_accessKey('V');
            
            # Botão Backup Automático
            $linkBotaoAut = new Link("Configurar Backup Automático",'?fase=config');
            $linkBotaoAut->set_class('button');
            $linkBotaoAut->set_title('Configurar Backup Automático');
            $linkBotaoAut->set_accessKey('C');

            # Botão Fazer Backup Manual
            $linkBotaoEditar = new Link("Backup Manual",'?fase=grh&manual=TRUE');
            $linkBotaoEditar->set_class('button');
            $linkBotaoEditar->set_title('Executa um backup manual agora');
            $linkBotaoEditar->set_accessKey('B');

            # Cria um menu
            $menu = new MenuBar();
            $menu->add_link($linkBotaoVoltar,"left");
            $menu->add_link($linkBotaoEditar,"right");
            $menu->add_link($linkBotaoAut,"right");
            $menu->show();
            
            titulo("Gerenciamento de Backups");
            
            $grid1->fechaColuna();
            $grid1->fechaGrid();
            
            br();
            
            # Divide a tela
            $grid2 = new Grid();
            $grid2->abreColuna(5);
          
            # Abre o diretório
            $ponteiro  = opendir("../_backup/".date('Y.m.d'));
            
            $dadosArquivo = array();
            
            while ($arquivo = readdir($ponteiro)){ 
                
                if($arquivo == ".." || $arquivo == ".")continue; // Desconsidera os diretorios 
                if($arquivo == "Thumbs.db")continue; // Desconsidera o thumbs.db
                
                # Divide o nome do arquivo
                $partesArquivo = explode('.',$arquivo);
                
                # Organiza as partes
                $dia = $partesArquivo[2]."/".$partesArquivo[1]."/".$partesArquivo[0];
                $hora = $partesArquivo[3].":".$partesArquivo[4];
                $tipo = $partesArquivo[5];
                $banco = $partesArquivo[6];
                
                $dadosArquivo[] = array($dia,$hora,$tipo,$banco,$arquivo);                
            }
            
            # Monta a tabela
            $label = array("Dia","Hora","Tipo","Banco","Ver");
            $width = array(20,15,15,40,10);
            $align = array("center","center","center","left");
            $function = array (null);
            
            $tabela = new Tabela();
            $titulo = "Backups Efetuados no mês de ".get_nomeMes(date('m')). " de ".date('Y');

            $tabela->set_conteudo($dadosArquivo);
            $tabela->set_cabecalho($label,$width,$align);
            $tabela->set_titulo($titulo);
            
            # Botão de exibição dos servidores com permissão a essa regra
            $botao = new BotaoGrafico();
            $botao->set_label('');
            $botao->set_url('?arquivoSql=');
            $botao->set_title('Visualiza arquido de backup');
            $botao->set_image(PASTA_FIGURAS.'ver.png',20,20);
    
            # Coloca o objeto link na tabela			
            $tabela->set_link(array("","","","",$botao));
            $tabela->set_idCampo(4);
            
            if(count($dadosArquivo) == 0){
                $callout = new Callout();
                $callout->abre();
                    p('Nenhum item encontrado !!','center');
                $callout->fecha();
            }else{
                $tabela->show();
            }
            
            $grid2->fechaColuna();

            # Coluna da documentação detalhada
            $grid2->abreColuna(7);
            
                if(is_null($arquivoSql)){
                    $callout = new Callout();
                    $callout->abre();
                        p('Nenhum arquivo selecionado !!','center');
                    $callout->fecha();
                }else{
                    echo '<pre>';

                    # Define o arquivo da classe
                    $arquivoExemplo = "../_backup/".date('Y.m.d')."/".$arquivoSql;

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
                    }
                }
            
            
            $grid2->fechaColuna();
            $grid2->fechaGrid();
            break;
        
        case "grh":
            $db = new Backup(array(
                    'driver' => 'mysql',
                    'host' => '127.0.0.1',
                    'user' => 'root',
                    'password' => '',
                    'database' => 'grh'
            ));
            $backup = $db->backup($manual);
            
            if(!$backup['error']){
                // If there isn't errors, show the content
                // The backup will be at $var['msg']
                // You can do everything you want to. Like save in a file.
                // $fp = fopen('file.sql', 'a+');fwrite($fp, $backup['msg']);fclose($fp);
                echo nl2br($backup['msg']);
            } else {
                echo 'An error has ocurred.';
            }
            loadPage('?fase=areaservidor&manual='.$manual);
            break;
            
        case "areaservidor":
            $db = new Backup(array(
                    'driver' => 'mysql',
                    'host' => '127.0.0.1',
                    'user' => 'root',
                    'password' => '',
                    'database' => 'areaservidor'
            ));
            $backup = $db->backup($manual);
            
            if(!$backup['error']){
                // If there isn't errors, show the content
                // The backup will be at $var['msg']
                // You can do everything you want to. Like save in a file.
                // $fp = fopen('file.sql', 'a+');fwrite($fp, $backup['msg']);fclose($fp);
                echo nl2br($backup['msg']);
            } else {
                echo 'An error has ocurred.';
            }
            loadPage('?');
            break;
        case "ver":
            break;
    }
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}