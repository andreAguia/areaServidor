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
        
        case "cadernoNovo" :
            set_session('idCaderno');
            break;
        
        case "notaNova" :
            set_session('idNota');
            break;
        
        case "menuCaderno" :
            set_session('idCaderno');
            set_session('idNota');
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
    
    # Cabeçalho da Página
    AreaServidor::cabecalho();
    
    # Cria um menu
    $menu1 = new MenuBar();

    # Voltar
    $botaoVoltar = new Link("Voltar","../../grh/grhSistema/grh.php");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Voltar a página anterior');
    $botaoVoltar->set_accessKey('V');
    $menu1->add_link($botaoVoltar,"left");
    
    # Ano Exercício
    $botaoVoltar = new Link("Ano Exercício");
    $botaoVoltar->set_class('button');
    $botaoVoltar->set_title('Férias por Ano Exercício');
    #$menu1->add_link($botaoVoltar,"right");
    
    # Ano por Fruíção
    $botaoVoltar = new Link("Ano de Fruição","areaFeriasFruicao.php");
    $botaoVoltar->set_class('hollow button');
    $botaoVoltar->set_title('Férias por Ano em que foi realmente fruído');
    #$menu1->add_link($botaoVoltar,"right");

    $menu1->show();  
    
    # Título
    titulo("Manual de Procedimentos");
    br();
    
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
            p("Manual de Procedimentos","f20","center");
            p("Versão 0.1","f16","center");
            p("Autor: André Águia","f14","center");
            br(5);

            $painel->fecha(); 
            
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            break;
                 
#############################################################################################################################
#   Caderno
#############################################################################################################################
        
        case "menuCaderno" :
                         
            # Area das notas
            $grid->abreColuna($col2P,$col2M,$col2L);
            
            $painel = new Callout();
            $painel->abre();
            
            # Menu
            $div = new Div('divEditaNota2');
            $div->abre();
            
            $menu1 = new MenuBar("small button-group");

            # Novo Caderno
            $link = new Link("Novo",'?fase=cadernoNovo');
            $link->set_class('button secondary');
            $link->set_title('Novo Caderno');
            $menu1->add_link($link,"right");

            $menu1->show();
            $div->fecha();
            
            # Pega os projetos cadastrados
            $select = 'SELECT idCaderno,
                              caderno,
                              descricao
                         FROM tbprojetocaderno
                      ORDER BY numOrdem, caderno';

            $dadosCaderno = $intra->select($select);
            $numCadernos = $intra->count($select);
            
            # Caderno
            p('Cadernos','descricaoProjetoTitulo');
            hr("projetosTarefas");
            br();
        
            # Inicia o menu
            $menu1 = new Menu();
            
            # Verifica se tem cadernos
            if($numCadernos > 0){
                
                # Percorre o array 
                foreach ($dadosCaderno as $valor){
                    $numNotas = $projeto->get_numeroNotas($valor[0]);
                    $texto = $valor[1]." <span id='numProjeto'>$numNotas</span>";                

                    $menu1->add_item('titulo2',$texto,'?fase=caderno&idCaderno='.$valor[0],"Caderno: ".$valor[1]);                    
                }           

            }
            $menu1->show();
            $painel->fecha();  
            
            $grid->fechaColuna();
            $grid->fechaGrid();   
            break;
            
    ###########################################################        
            
    case "dadosCaderno" :
                         
            # Area das notas
            $grid->abreColuna($col2P,$col2M,$col2L);
            
            $painel = new Callout();
            $painel->abre();
            
            # Menu
            $div = new Div('divEditaNota2');
            $div->abre();
            
            $menu1 = new MenuBar("small button-group");

            # Nova Nota
            $link = new Link("Editar",'?fase=cadernoEditar');
            $link->set_class('button secondary');
            $link->set_title('Edita Caderno');
            $menu1->add_link($link,"right");
            
            # Nova Nota
            $link = new Link("<i class='fi-plus'></i>",'?fase=notaNova');
            $link->set_class('button secondary');
            $link->set_title('Nova Nota');
            $menu1->add_link($link,"right");

            $menu1->show();
            $div->fecha();
            
            # Pega os projetos cadastrados
            $select = 'SELECT idCaderno,
                              caderno,
                              descricao
                         FROM tbprojetocaderno
                        WHERE idCaderno = '.$idCaderno;

            $dadosCaderno = $intra->select($select,false);
            $numCadernos = $intra->count($select);
            
            # Caderno
            p($dadosCaderno[1],'descricaoProjetoTitulo');
            p($dadosCaderno[2],'descricaoProjeto');
            hr("projetosTarefas");
            br();
                        
            # Pega as notas
            $select = 'SELECT idNota,
                              titulo,
                              descricao
                         FROM tbprojetonota
                        WHERE idcaderno = '.$idCaderno.' ORDER BY numOrdem,titulo';

            # Acessa o banco
            $notas = $intra->select($select);
            $numNotas = $intra->count($select);
            
            if($numNotas > 0){
                # Inicia o Manu de Notas
                $menu2 = new Menu();

                # Percorre as notas 
                foreach($notas as $tituloNotas){
                    $menu2->add_item('link',$tituloNotas[1],'?fase=caderno&idNota='.$tituloNotas[0],$tituloNotas[2]);
                }

                # Incluir nota
                #$menu2->add_item('sublink','+ Nova Nota','?fase=notaNova');

                $menu2->show();
                br();
            }else{
                br(2);
                p("Não há notas cadastradas !!","f14","center");
                br(3);
            }
            
            $painel->fecha();  
            
            $grid->fechaColuna();
            $grid->fechaGrid();   
            break;
            
    ###########################################################
            
        case "caderno" :
                         
            # Area das notas
            $grid->abreColuna($col2P,$col2M,$col2L);
            
            $painel = new Callout();
            $painel->abre();
            
            # Menu
            $div = new Div('divEditaNota2');
            $div->abre();
            
            $menu1 = new MenuBar("small button-group");

            # Nova Nota
            $link = new Link("Editar",'?fase=editaNota&idNota='.$idNota);
            $link->set_class('button secondary');
            $link->set_title('Editar Nota');
            $menu1->add_link($link,"right");

            $menu1->show();
            $div->fecha();
            
            # Pega os Dados
            $dados = $projeto->get_dadosNota($idNota);

            # Exibe a nota
            p($dados[2],'descricaoProjetoTitulo');
            p($dados[5],'descricaoProjeto');
            hr("projetosTarefas");
            
            $divNota = new Div("divNota");
            $divNota->abre();
            
            echo $dados[3];
            #echo "<pre id='preNota'>".$dados[3]."</pre>";
            
            $divNota->fecha();
            
            $painel->fecha();  
            
            $grid->fechaColuna();
            $grid->fechaGrid();   
            break;
            
    ###########################################################
            
        case "cadernoNovo" :
        case "cadernoEditar" :
            
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
            $controle->set_col(10);
            $controle->set_linha(1);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Nome do Caderno');
            $controle->set_title('O nome do Caderno a ser criado');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # numOrdem
            $controle = new Input('numOrdem','texto','Ordem:',1);
            $controle->set_size(5);
            $controle->set_linha(1);
            $controle->set_col(2);
            $controle->set_title('Ordem do caderno na lista');
            $controle->set_valor($dados[3]);
            $form->add_item($controle);
            
            # descrição
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,5));
            $controle->set_linha(2);
            $controle->set_title('A descrição detalhda do caderno');
            $controle->set_placeholder('Descrição');
            $controle->set_valor($dados[2]);
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
            $numOrdem = post('numOrdem');
            
            # Cria arrays para gravação
            $arrayNome = array("caderno","descricao","numOrdem");
            $arrayValores = array($caderno,$descricao,$numOrdem);
            
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
        
#############################################################################################################################
#   Nota
#############################################################################################################################
            
        case "notaNova" :
        case "editaNota" :    
             
            $grid->abreColuna($col2P,$col2M,$col2L);
             
            # Verifica se é incluir ou editar
            if(!is_null($idNota)){
                # Pega os dados dessa nota
                $dados = $projeto->get_dadosNota($idNota);
                $titulo = "Editar Nota";
            }else{
                $dados = array(NULL,NULL,NULL,NULL,NULL,NULL);
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
            
            # numOrdem
            $controle = new Input('numOrdem','texto','Ordem:',1);
            $controle->set_size(5);
            $controle->set_linha(1);
            $controle->set_col(2);
            $controle->set_title('Ordem da nota na lista');
            $controle->set_valor($dados[4]);
            $form->add_item($controle);
            
            # descricao            
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,2));
            $controle->set_linha(2);
            $controle->set_col(12);
            $controle->set_title('Breve Descrição da nota');
            $controle->set_valor($dados[5]);
            $form->add_item($controle);
                                    
            # nota            
            $controle = new Input('nota','editor','Nota:',1);
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
            $descricao = post('descricao');
                      
            # Cria arrays para gravação
            $arrayNome = array("titulo","idCaderno","nota","numOrdem","descricao");
            $arrayValores = array($titulo,$caderno,$nota,$numOrdem,$descricao);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idNota,"tbprojetonota","idNota");
            
            # Pega o id quando for inclusão
            if(is_null($idNota)){
                $idnota = $intra->get_lastId();
                set_session('idNota',$idnota);
            }
            
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