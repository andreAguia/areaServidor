<?php
/**
 * Backup
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

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
    $arquivoSql = get('arquivoSql');  # Usado para se exibir um arquivo sql
      
    $dataLista = retiraAspas(post('dataLista',get('dataLista',date("Y-m-d"))));
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Pega a pasta de backup
    $pastaBackup = $intra->get_variavel('pastaBackup');
    
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
            $linkBotaoEditar = new Link("Backup Manual",'?fase=aguarde');
            $linkBotaoEditar->set_class('button');
            $linkBotaoEditar->set_title('Executa um backup manual agora');
            $linkBotaoEditar->set_accessKey('B');

            # Cria um menu
            $menu = new MenuBar();
            $menu->add_link($linkBotaoVoltar,"left");
            $menu->add_link($linkBotaoEditar,"right");
            #$menu->add_link($linkBotaoAut,"right");
            $menu->show();
            
            titulo("Gerenciamento de Backups");
            
            $grid1->fechaColuna();
            $grid1->fechaGrid();
            
            # Data
            $form = new Form('?');

            $controle = new Input('dataLista','data','Entre com a data',1);
            $controle->set_size(30);
            $controle->set_title('Insira a data');
            $controle->set_valor($dataLista);
            $controle->set_autofocus(TRUE);
            $controle->set_onChange('formPadrao.submit();');
            $controle->set_linha(1);
            $controle->set_col(3);
            $form->add_item($controle);
            
            br();
            $form->show();
            
            # Divide a tela
            $grid2 = new Grid();
            $grid2->abreColuna();
          
            # Troca o - por .
            $partesData = explode('-',$dataLista);
            
            # Abre o diretório
            $pasta = "../$pastaBackup/".$partesData[0].'.'.$partesData[1].'.'.$partesData[2];
            
            # Array que guarda s arquivos
            $dadosArquivo = array();
            
            $dia = NULL;
            
            if (file_exists($pasta)){
                $ponteiro  = opendir($pasta);
                
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
                    
                    $extensao = $partesArquivo[7];

                    if($extensao == 'zip'){
                        $dadosArquivo[] = array($dia,$hora,$tipo,$banco);
                    }
                }
            }
            
            # Monta a tabela
            $label = array("Dia","Hora","Tipo","Banco");
            #$width = array(20,15,15,40,10);
            $align = array("center");
            $function = array (NULL);
            
            $tabela = new Tabela();
            $titulo = "Backups Efetuados em ".$dia;

            $tabela->set_conteudo($dadosArquivo);
            $tabela->set_label($label);
            $tabela->set_align($align);
            $tabela->set_titulo($titulo);
            
            if(count($dadosArquivo) == 0){
                $callout = new Callout();
                $callout->abre();
                    p('Nenhum item encontrado !!','center');
                $callout->fecha();
            }else{
                $tabela->show();
            }
                        
            $grid2->fechaColuna();
            $grid2->fechaGrid();
            break;
            
        case "aguarde" :
            br(10);
            aguarde();
            br();
            
            # Limita a tela
            $grid1 = new Grid("center");
            $grid1->abreColuna(5);
                p("Efetuando backup ...","center");
            $grid1->fechaColuna();
            $grid1->fechaGrid();
            
            loadPage('?fase=backup');
            break;    
        
        case "backup":
            # Senha root
            $senha = "DSvuEtwz6h9HfLCF";
            
            # Cria a pasta do backup se não existir
            $pastaBackup = $intra->get_variavel('pastaBackup');
            $pasta = $pastaBackup."/".date('Y.m.d');
            if(!file_exists("../$pasta")){
                mkdir("../$pasta");
            }
                       
            # Define o nome do arquivo
            $nomeArquivo = $pasta."/".date('Y.m.d.H.i').".M";
            
            # Executa o backup acessando rotina externa
            exec("backup.bat $nomeArquivo $senha $pastaMysqlDump");
            
            # troca as / por \ pois rotina de zipar usa barra invertida
            $nomeArquivo = str_replace("/","\\",$nomeArquivo);
            
            # Acrescenta ..\ para o endereço relativo
            $nomeArquivo = '..\\'.$nomeArquivo;
            
            # Faz o zip para o banco grh
            $arquivo = array($nomeArquivo.".grh.sql");
            $arquivoZipado = $nomeArquivo.".grh.zip";
            createZip($arquivoZipado,$arquivo);
            
            # Faz o zip para o banco areaServidor
            $arquivo = array($nomeArquivo.".areaServidor.sql");
            $arquivoZipado = $nomeArquivo.".areaServidor.zip";
            createZip($arquivoZipado,$arquivo);
            
            # Apaga os arquivos sql criados deixando somente os zipados
            unlink($nomeArquivo.".areaServidor.sql");
            unlink($nomeArquivo.".grh.sql");
                                    
            # Escreve o log
            $data = date("Y-m-d H:i:s");
            $atividade = "Efetuou o Backup Manual";
            $intra->registraLog($idUsuario,$data,$atividade,NULL,NULL,6,NULL);
                                   
            loadPage('?');
            break;
    }
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}