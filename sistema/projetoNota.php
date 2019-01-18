<?php
/**
 * Gestão de Projetos
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){
    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
    $projeto = new Projeto();

    # Verifica a fase do programa
    $fase = get('fase','inicial');
    
    # Determina as sessions e o botão voltar conforme a fase
    switch ($fase){
        
        case "cadernoNovo":
            set_session('idCaderno');
            $voltar = '?';
            break;
        
        default :
            $voltar = 'administracao.php';
            break;
    }
    
    # Pega os ids quando se é necessário de acordo com a fase
    $idCaderno = get('idCaderno',get_session('idCaderno'));
    $idNota = get('idNota',get_session('idNota'));
    
    # Passa para Session o que veio do get
    set_session('idCaderno',$idCaderno);
    
    # Verifica se a nota é do caderno editado
    if(!is_null($idNota)){
        $dadosNota = $projeto->get_dadosNota($idNota);
        
        # Se não for apaga o idNota
        if($dadosNota[1] <> $idCaderno){
            set_session('idNota');
            $idNota = NULL;
        }else{
            set_session('idNota',$idNota);
        }
        
    }
    
    # Pega a estante (grupo)
    $grupo = get('grupo');
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    echo '<div class="title-bar">
            <button class="menu-icon show-for-small-only" type="button" onclick="abreFechaDivId(\'menuSuspenso\');"></button>
            <div class="title-bar-title">Sistema de Notas</div>
          </div>';
    
    br();
    
    # Cria um menu
    $menu1 = new MenuBar("button-group");

    # Sair da Área do Servidor
    $linkVoltar = new Link("Voltar",$voltar);
    $linkVoltar->set_class('button');
    $linkVoltar->set_title('Voltar a página anterior');    
    $menu1->add_link($linkVoltar,"left");
    
    # Novo Caderno
    $linkSenha = new Link("Novo Caderno","?fase=cadernoNovo");
    $linkSenha->set_class('button');
    $linkSenha->set_title('Cria novo Caderno');
    $menu1->add_link($linkSenha,"right");

    #$menu1->show();
    
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
    
    $div = new Div(NULL,"hide-for-small-only");
    $div->abre();
    
    # Menu de Cadernos
    Gprojetos::menuCadernos($idCaderno,$idNota);
    
    $div->fecha();
    $grid->fechaColuna();
    
    switch ($fase){ 
        
#############################################################################################################################
#   Inicial
#############################################################################################################################
        
        case "inicial" :
            $grid->abreColuna($col2P,$col2M,$col2L);
            
            $painel = new Callout();
            $painel->abre();
                            
            br(5);
            p("Sistema de Gestão Hierárquica de Notas","f20","center");
            p("Versão 0.1","f16","center");
            br(5);

            $painel->fecha(); 
            
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            break;
                 
#############################################################################################################################
#   Caderno
#############################################################################################################################
            
        case "caderno" :
                         
            # Area das notas
            $grid->abreColuna($col2P,$col2M,$col2L);
            
            $painel = new Callout();
            $painel->abre(); 
            
            # Pega os dados dessa nota
            if(!is_null($idNota)){
                # Pega os Dados
                $dados = $projeto->get_dadosNota($idNota);
                
                # Exibe a nota
                $grid = new Grid();
                $grid->abreColuna(10);
                    p($dados[2],'descricaoProjetoTitulo');
                $grid->fechaColuna();
                $grid->abreColuna(2);

                    # Menu
                    $menu1 = new MenuBar("small button-group");

                    # Nova Nota
                    $link = new Link("Editar",'?fase=notaNova&idNota='.$idNota);
                    $link->set_class('button secondary');
                    $link->set_title('Editar Nota');
                    $menu1->add_link($link,"right");

                    $menu1->show();

                $grid->fechaColuna();
                $grid->fechaGrid();

                hr("projetosTarefas");
                echo "<pre id='preNota'>".$dados[3]."</pre>";
                  
            }else{               
                br(3);
                p("Nenhuma Nota Selecionada","f14","center");  
                br(3);
            }
            $painel->fecha();  
            
            $grid->fechaColuna();
            $grid->fechaGrid();   
            break;
            
    ###########################################################
            
        case "cadernoNovo" :
             
            $grid->abreColuna($col2P,$col2M,$col2L);
             
            # Verifica se é incluir ou editar
            if(!is_null($idCaderno)){
                # Pega os dados 
                $dados = $projeto->get_dadosCaderno($idCaderno);
                $titulo = "Editar";
            }else{
                $dados = array(NULL,NULL,NULL,NULL,NULL);
                $titulo = "Novo Caderno";
            }
             
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
                p($titulo,'descricaoProjetoTitulo');
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            
            # Formulário
            $form = new Form('?fase=validaCaderno');        
                    
            # caderno
            $controle = new Input('caderno','texto','Nome do Caderno:',1);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Nome do Caderno');
            $controle->set_title('O nome do Caderno a ser criado');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # descrição
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,5));
            $controle->set_linha(2);
            $controle->set_title('A descrição detalhda do caderno');
            $controle->set_placeholder('Descrição');
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # grupo
            $controle = new Input('grupo','texto','Estante:',1);
            $controle->set_size(50);
            $controle->set_linha(3);
            $controle->set_col(6);
            $controle->set_placeholder('Grupo');
            $controle->set_title('O nome da estante');
            $controle->set_plm(TRUE);
            $controle->set_valor($dados[3]);
            $form->add_item($controle);
            
            # cor
            $controle = new Input('cor','combo','Cor:',1);
            $controle->set_size(10);
            $controle->set_col(6);
            $controle->set_linha(3);
            $controle->set_title('A cor do cartão');
            $controle->set_placeholder('Cor');
            $controle->set_array(array("secondary","primary","success","warning","alert"));
            $controle->set_valor($dados[4]);
            $form->add_item($controle);
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(4);
            $form->add_item($controle);
            
            $form->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                        
    ###########################################################
            
        case "validaCaderno" :
            
            # Recuperando os valores
            $caderno = post('caderno');
            $descricao = post('descricao');
            $grupo = post('grupo');
            $cor = post('cor');
            
            # Cria arrays para gravação
            $arrayNome = array("caderno","descricao","grupo","cor");
            $arrayValores = array($caderno,$descricao,$grupo,$cor);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idCaderno,"tbprojetocaderno","idCaderno");
            
            loadPage("?");
            break;
        
    ###########################################################
            
        case "cartaoCaderno" :
            # Exibe a tela inicial dos cartões de Cadernos
            
            $grid->abreColuna($col2P,$col2M,$col2L);
            
            # Menu de Projetos
            Gprojetos::cartoesCadernos($grupo);  
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
            
            break;
        
#############################################################################################################################
#   Nota
#############################################################################################################################
            
        case "notaNova" :
             
            $grid->abreColuna($col2P,$col2M,$col2L);
             
            # Verifica se é incluir ou editar
            if(!is_null($idNota)){
                # Pega os dados dessa nota
                $dados = $projeto->get_dadosNota($idNota);
                $titulo = "Editar Nota";
            }else{
                $dados = array(NULL,NULL,NULL,NULL,NULL);
                $titulo = "Nova Nota";
            } 
             
            # Titulo
            $grid = new Grid();
            $grid->abreColuna(12);
                p($titulo,"f18");
            $grid->fechaColuna(); 
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            
            # Pega os dados da combo caderno
            $select = 'SELECT idCaderno,
                              caderno
                         FROM tbprojetocaderno
                     ORDER BY caderno';
            
            $comboCaderno = $intra->select($select);
            
            # Formuário
            $form = new Form('?fase=validaNota');        
                    
            # Título
            $controle = new Input('titulo','texto','Título:',1);
            $controle->set_size(100);
            $controle->set_linha(1);
            $controle->set_col(5);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_title('Título da nota');
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # idProjeto
            $controle = new Input('idCaderno','combo','Caderno:',1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_col(5);
            $controle->set_array($comboCaderno);
            if(is_null($idNota)){
                $controle->set_valor($idCaderno);
            }else{
                $controle->set_valor($dados[1]);
            }
            $form->add_item($controle);
            
            # Título
            $controle = new Input('numOrdem','texto','Ordem:',1);
            $controle->set_size(5);
            $controle->set_linha(1);
            $controle->set_col(2);
            $controle->set_title('Ordem da nota na lista');
            $controle->set_valor($dados[4]);
            $form->add_item($controle);
                                    
            # nota            
            $controle = new Input('nota','textarea','Descrição:',1);
            $controle->set_size(array(80,15));
            $controle->set_linha(2);
            $controle->set_col(12);
            $controle->set_title('Corpo da nota');
            $controle->set_valor($dados[3]);
            $form->add_item($controle);
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(3);
            $form->add_item($controle);
            
            $form->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                        
        ###########################################################
            
        case "validaNota" :
            
            # Recuperando os valores
            $titulo = post('titulo');
            $caderno = post('idCaderno');
            $nota = post('nota');
            $numOrdem = post('numOrdem');
                      
            # Cria arrays para gravação
            $arrayNome = array("titulo","idCaderno","nota","numOrdem");
            $arrayValores = array($titulo,$caderno,$nota,$numOrdem);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idNota,"tbprojetonota","idNota");
            
            loadPage("?fase=caderno");
            break;
                        
        ###########################################################
    }
    
    $div = new Div("menuSuspenso","show-for-small-only");
    $div->abre();
    
    # Menu de Cadernos
    Gprojetos::menuCadernos($idCaderno,$idNota);
    
    $div->fecha();
    
    $grid->fechaColuna();
    $grid->fechaGrid();  
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}