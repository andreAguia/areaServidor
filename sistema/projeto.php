<?php

/**
 * Gestão de Projetos
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
    $intra = new Intra();
    $servidor = new Pessoal();
    $projeto = new Projeto();

    # Verifica a fase do programa
    $fase = get('fase', 'fazendo');

    # Determina as sessions e o botão voltar conforme a fase
    switch ($fase) {
        case "ínicial" :
            $voltar = 'administracao.php';
            break;

        case "cartaoProjeto":
            set_session('idProjeto');
            set_session('etiqueta');
            set_session('solicitante');
            set_session('fazendo', false);
            $voltar = 'administracao.php';
            break;

        case "fazendo":
            set_session('idProjeto');
            set_session('etiqueta');
            set_session('solicitante');
            set_session('fazendo', true);
            $voltar = 'administracao.php';
            break;

        case "projeto":
            set_session('etiqueta');
            set_session('solicitante');
            set_session('fazendo', false);
            $voltar = '?';
            break;

        case "projetoNovo":
            set_session('idProjeto');
            set_session('etiqueta');
            set_session('solicitante');
            set_session('fazendo', false);
            $voltar = '?';
            break;

        case "projetoEditar":
            $voltar = '?fase=projeto';
            break;

        case "etiqueta":
            set_session('idProjeto');
            set_session('solicitante');
            set_session('fazendo', false);
            $voltar = '?';
            break;

        case "solicitante":
            set_session('idProjeto');
            set_session('etiqueta');
            set_session('fazendo', false);
            $voltar = '?';
            break;

        case "exibeTarefa":
            $voltar = '?fase=verificaVolta';
            break;

        case "tarefaNova":
            $voltar = '?fase=verificaVolta';
            break;

        default :
            $voltar = 'administracao.php';
            break;
    }

    # Pega os ids quando se é necessário de acordo com a fase
    $idProjeto = get('idProjeto', get_session('idProjeto'));
    $etiqueta = get('etiqueta', get_session('etiqueta'));
    $solicitante = get('solicitante', get_session('solicitante'));
    $fazendo = get_session('fazendo');
    $idTarefa = get('idTarefa');
    $grupo = get('grupo');

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    echo '<div class="title-bar">
            <button class="menu-icon show-for-small-only" type="button" onclick="abreFechaDivId(\'menuSuspenso\');"></button>
            <div class="title-bar-title">Sistema de Gestão de Projetos</div>
          </div>';

    br();

    # Cria um menu
    $menu1 = new MenuBar("button-group");

    # Sair da Área do Servidor
    $linkVoltar = new Link("Voltar", $voltar);
    $linkVoltar->set_class('button');
    $linkVoltar->set_title('Voltar a página anterior');
    $menu1->add_link($linkVoltar, "left");

    # Novo Projeto
    $linkSenha = new Link("Novo Projeto", "?fase=projetoNovo");
    $linkSenha->set_class('button');
    $linkSenha->set_title('Cria novo projeto');
    $menu1->add_link($linkSenha, "right");

    # Fazendo
    $linkSenha = new Link("Fazendo", "?fase=fazendo");
    $linkSenha->set_class('button success');
    $linkSenha->set_title('Exibe as Tarefas que estão sendo feitas');
    $menu1->add_link($linkSenha, "right");

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
    $grid->abreColuna($col1P, $col1M, $col1L);

    $div = new Div("menuNormal", "hide-for-small-only");
    $div->abre();
    br();

    $form = new Form('?fase=pesquisa');

    $controle = new Input('parametro', 'texto');
    $controle->set_size(30);
    $controle->set_linha(1);
    $controle->set_col(12);
    $controle->set_placeholder('Pesquisar');
    $controle->set_title('Pesquisar');
    $controle->set_onChange('formPadrao.submit();');
    $form->add_item($controle);

    $form->show();

    # Menu Cronológico
    Gprojetos::menuFazendo($fase);

    # Menu de Projetos
    Gprojetos::menuProjetosAtivos($idProjeto);

    # Menu de Etiquetas
    Gprojetos::menuEtiquetas($etiqueta);

    # Menu de Solicitantes
    Gprojetos::menuSolicitante($solicitante);

    $div->fecha();

    $grid->fechaColuna();

    switch ($fase) {

#############################################################################################################################
#   Fazendo
############################################################################################################################# 

        case "fazendo" :
            # Exibe a lista de tarefas com status fazendo

            $grid->abreColuna($col2P, $col2M, $col2L);

            # Exibe as tarefas fazendo
            $lista = new ListaTarefas("Fazendo");
            $lista->set_status("fazendo");
            $lista->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

#############################################################################################################################
#   Pesquisa
############################################################################################################################# 

        case "pesquisa" :
            # Exibe a lista de tarefas com status fazendo

            $grid->abreColuna($col2P, $col2M, $col2L);

            $parametro = post("parametro");

            # Exibe as tarefas fazendo
            $lista = new ListaTarefas("Pesquisa: " . $parametro);
            $lista->set_pesquisa($parametro);
            $lista->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

#############################################################################################################################
#   Projeto
#############################################################################################################################

        case "projeto" :
            # Exibe as tarefas de um determinado projeto
            # joga para session o idProjeto
            set_session('idProjeto', $idProjeto);

            $grid->abreColuna($col2P, $col2M, $col2L);

            # Pega os dados do projeto pesquisado
            $projetoPesquisado = $projeto->get_dadosProjeto($idProjeto);

            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(8);

            # Exibe o nome e a descrição
            p("Projeto:", "pDescricaoTag");
            p($projetoPesquisado[1], 'descricaoProjetoTitulo');
            p($projetoPesquisado[2], 'descricaoProjeto');

            $grid->fechaColuna();
            $grid->abreColuna(4);

            # Menu
            $menu1 = new MenuBar("small button-group");

            # Nova Tarefa
            $link4 = new Link("Editar", '?fase=projetoEditar&idProjeto=' . $idProjeto);
            $link4->set_class('button secondary');
            $link4->set_title('Editar Projeto');
            $menu1->add_link($link4, "right");

            $link5 = new Link("<i class='fi-plus'></i>", '?fase=tarefaNova');
            $link5->set_class('button secondary');
            $link5->set_title('Nova Tarefa');
            $menu1->add_link($link5, "right");

            $menu1->show();

            $grid->fechaColuna();
            $grid->fechaGrid();

            hr("projetosTarefas");
            br();

            # Exibe as tarefas pendentes fazendo
            $lista = new ListaTarefas("Fazendo");
            $lista->set_status("fazendo");
            $lista->set_projeto($idProjeto);
            $lista->set_datado(false);
            $lista->show();

            # Exibe as tarefas a fazer
            $lista = new ListaTarefas("a Fazer");
            $lista->set_status("a fazer");
            $lista->set_projeto($idProjeto);
            $lista->show();

            # Exibe as tarefas completatadas
            $lista = new ListaTarefas("Feito");
            $lista->set_projeto($idProjeto);
            $lista->set_pendente(false);
            $lista->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

        ###########################################################      

        case "projetoNovo" :
        case "projetoEditar" :
            # Inclui um projeto novo ou edita um já existente

            $grid->abreColuna($col2P, $col2M, $col2L);

            # Verifica se é incluir ou editar
            if (!is_null($idProjeto)) {
                # Pega os dados desse projeto
                $dados = $projeto->get_dadosProjeto($idProjeto);
                $titulo = "Editar Projeto";
            } else {
                $dados = array(null, null, null, null);
                $titulo = "Novo Projeto";
            }

            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
            p($titulo, 'descricaoProjetoTitulo');
            $grid->fechaColuna();
            $grid->fechaGrid();
            hr("projetosTarefas");

            # Formulário
            $form = new Form('?fase=validaProjeto&idProjeto=' . $idProjeto);

            # projeto
            $controle = new Input('projeto', 'texto', 'Nome do Projeto:', 1);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_required(true);
            $controle->set_autofocus(true);
            $controle->set_placeholder('Nome do Projeto');
            $controle->set_title('O nome do Projeto a ser criado');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);

            # descrição
            $controle = new Input('descricao', 'textarea', 'Descrição:', 1);
            $controle->set_size(array(80, 5));
            $controle->set_linha(2);
            $controle->set_title('A descrição detalhda do projeto');
            $controle->set_placeholder('Descrição');
            $controle->set_valor($dados[2]);
            $form->add_item($controle);

            # grupo
            $controle = new Input('grupo', 'texto', 'Nome do agrupamento:', 1);
            $controle->set_size(50);
            $controle->set_linha(3);
            $controle->set_col(5);
            $controle->set_placeholder('Grupo');
            $controle->set_title('O nome agrupamento do Projeto');
            $controle->set_plm(true);
            $controle->set_valor($dados[3]);
            $form->add_item($controle);

            # cor
            $controle = new Input('cor', 'combo', 'Cor:', 1);
            $controle->set_size(10);
            $controle->set_col(5);
            $controle->set_linha(3);
            $controle->set_title('A cor da etiqueta');
            $controle->set_placeholder('Cor');
            $controle->set_array(array("secondary", "primary", "success", "warning", "alert"));
            $controle->set_valor($dados[2]);
            $form->add_item($controle);

            # numOrdem
            $controle = new Input('numOrdem', 'texto', 'Ordem:', 1);
            $controle->set_size(5);
            $controle->set_linha(3);
            $controle->set_col(2);
            $controle->set_title('O numero de ordem');
            $controle->set_valor($dados[5]);
            $form->add_item($controle);

            # submit
            $controle = new Input('submit', 'submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(4);
            $form->add_item($controle);

            $form->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

        ###########################################################

        case "validaProjeto" :
            # Valida um projeto digitado 
            # Recuperando os valores
            $projeto = post('projeto');
            $descricao = post('descricao');
            $grupo = plm(post('grupo'));
            $cor = post('cor');
            $numOrdem = post('numOrdem');

            # Cria arrays para gravação
            $arrayNome = array("projeto", "descricao", "ativo", "grupo", "cor", "numOrdem");
            $arrayValores = array($projeto, $descricao, 1, $grupo, $cor, $numOrdem);

            # Grava	
            $intra->gravar($arrayNome, $arrayValores, $idProjeto, "tbprojeto", "idProjeto");

            if (is_null($idProjeto)) {
                set_session('idProjeto', $intra->get_lastId());
            }
            loadPage("?fase=cartaoProjeto&grupo=" . $grupo);
            break;

        ###########################################################

        case "cartaoProjeto" :
            # Exibe a tela inicial dos cartões de projeto

            $grid->abreColuna($col2P, $col2M, $col2L);

            # Menu de Projetos
            Gprojetos::cartoesProjetosAtivos($grupo);

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

#############################################################################################################################
#   Tarefa
#############################################################################################################################

        case "exibeTarefa" :
            # Exibe as tarefas e subtarefas

            $grid->abreColuna($col2P, $col2M, $col2L);

            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(8);

            # Pega os dados dessa tarefa
            $dados = $projeto->get_dadosTarefa($idTarefa);

            # Preenche as variáveis
            $exibeTarefa = $dados[1];
            $exibeNomeProjeto = $projeto->get_nomeProjeto($dados[8]);

            p("Tarefa:", "pDescricaoTag");
            p($exibeTarefa, "pExibeTarefa");

            # Projeto
            span($exibeNomeProjeto, "projeto", null, "Projeto");

            # Etiqueta
            if (!is_null($dados[7])) {
                echo "&nbsp&nbsp&nbsp";
                span($dados[7], "etiqueta", null, "Etiqueta");
            }

            # Solicitante
            if (!is_null($dados[11])) {
                echo "&nbsp&nbsp&nbsp";
                span($dados[11], "solicitante", null, "Solicitante");
            }

            $grid->fechaColuna();
            $grid->abreColuna(4);

            # Menu
            $menu1 = new MenuBar("small button-group");

            $link1 = new Link("Voltar", $voltar);
            $link1->set_class('button');
            $link1->set_title('Voltar Sem Salvar');
            $menu1->add_link($link1, "right");

            # Editar
            $link4 = new Link("<i class='fi-pencil'></i>", '?fase=tarefaNova&idTarefa=' . $idTarefa);
            $link4->set_class('button secondary');
            $link4->set_title('Editar Tarefa');
            $menu1->add_link($link4, "right");

            # Nova Sub Tarefa
            $link5 = new Link("<i class='fi-plus'></i>", '?fase=subtarefaNova');
            $link5->set_class('button secondary');
            $link5->set_title('Nova Sub-Tarefa');
            $menu1->add_link($link5, "right");

            $menu1->show();

            $grid->fechaColuna();
            $grid->fechaGrid();

            hr("hrCard");

            if (!vazio($dados[2])) {
                p("Descrição:", "pDescricaoTag");
                p($dados[2], "pDescricao");
                hr("hrCard");
            }


            p("SubTarefas:", "pDescricaoTag");

            br(2);

            hr("hrCard");

            if (!vazio($dados[9])) {
                p("Conclusão:", "pDescricaoTag");
                p($dados[9], "pDescricao");
                hr("hrCard");
            }

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

###########################################################        

        case "tarefaNova" :
            # Inclui uma tarefa nova ou edita uma já existente

            $grid->abreColuna($col2P, $col2M, $col2L);

            # Verifica se é incluir ou editar
            if (!is_null($idTarefa)) {
                # Pega os dados dessa tarefa
                $dados = $projeto->get_dadosTarefa($idTarefa);
                $titulo = "Editar Tarefa";
                $etiqueta = $dados[7];
                $idProjeto = $dados[8];
            } else {
                $dados = array(null, null, null, null, null, null, null, null, null, null, null, null);
                $titulo = "Nova Tarefa";
            }

            # Menu
            $menu1 = new MenuBar("small button-group");

            if (!is_null($idTarefa)) {

                # Voltar
                $link1 = new Link("Voltar", '?fase=exibeTarefa&idTarefa=' . $idTarefa);
                $link1->set_class('button');
                $link1->set_title('Voltar Sem Salvar');
                $menu1->add_link($link1, "left");

                # Excluir
                $link1 = new Link("Excluir", '?fase=TarefaExcluir&idTarefa=' . $idTarefa);
                $link1->set_class('alert button');
                $link1->set_title('Excluir Tarefa');
                $link1->set_confirma('Deseja mesmo excluir ?');
                $menu1->add_link($link1, "right");
            } else {
                # Voltar
                $link1 = new Link("Voltar", $voltar);
                $link1->set_class('button');
                $link1->set_title('Voltar Sem Salvar');
                $menu1->add_link($link1, "left");
            }

            $menu1->show();

            # Pega os dados da combo projeto
            $select = 'SELECT idProjeto,
                              projeto
                         FROM tbprojeto
                     ORDER BY projeto';

            $comboProjeto = $intra->select($select);
            array_unshift($comboProjeto, array(null, null)); # Adiciona o valor de nulo
            # Formuário
            $form = new Form('?fase=validaTarefa&idTarefa=' . $idTarefa);

            # tarefa
            $controle = new Input('tarefa', 'textarea', 'Tarefa:', 1);
            $controle->set_size(array(80, 3));
            $controle->set_linha(1);
            $controle->set_col(12);
            $controle->set_required(true);
            $controle->set_autofocus(true);
            $controle->set_placeholder('Tarefa');
            $controle->set_title('A tarefa a ser executada');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);

            # idProjeto
            $controle = new Input('idProjeto', 'combo', 'Projeto:', 1);
            $controle->set_size(20);
            $controle->set_linha(2);
            $controle->set_col(12);
            $controle->set_array($comboProjeto);
            if (is_null($idTarefa)) {
                $controle->set_valor($idProjeto);
            } else {
                $controle->set_valor($dados[8]);
            }
            $form->add_item($controle);

            # Pega as etiquetas cadastradas
            $select = 'SELECT distinct etiqueta
                         FROM tbprojetotarefa
                        WHERE etiqueta is not null
                         ORDER BY etiqueta';

            $dadosEtiquetas = $intra->select($select);

            # etiqueta
            $controle = new Input('etiqueta', 'texto', 'Etiqueta:', 1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(6);
            $controle->set_placeholder('Etiqueta');
            $controle->set_datalist($dadosEtiquetas);
            $controle->set_title('Uma etiqueta para ajudar na busca');
            if (is_null($idTarefa)) {
                $controle->set_valor($etiqueta);
            } else {
                $controle->set_valor($dados[7]);
            }
            $form->add_item($controle);

            # Pega as etiquetas cadastradas
            $select = 'SELECT distinct solicitante
                         FROM tbprojetotarefa
                        WHERE solicitante is not null
                         ORDER BY solicitante';

            $dadosSolicitantes = $intra->select($select);

            # solicitante
            $controle = new Input('solicitante', 'texto', 'Solicitante:', 1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(6);
            $controle->set_placeholder('Solicitante');
            $controle->set_title('O Solicitante');
            $controle->set_datalist($dadosSolicitantes);
            if (is_null($idTarefa)) {
                $controle->set_valor($solicitante);
            } else {
                $controle->set_valor($dados[11]);
            }
            $form->add_item($controle);

            # descrição
            $controle = new Input('descricao', 'textarea', 'Descrição:', 1);
            $controle->set_size(array(80, 3));
            $controle->set_linha(4);
            $controle->set_title('A descrição detalhda do tarefa');
            $controle->set_placeholder('Descrição da tarefa');
            $controle->set_valor($dados[2]);
            $form->add_item($controle);

            # status
            $controle = new Input('status', 'combo', 'Status:', 1);
            $controle->set_size(20);
            $controle->set_linha(5);
            $controle->set_col(6);
            $controle->set_placeholder('Status');
            $controle->set_title('O status da tarefa');
            $controle->set_array(array("a fazer", "fazendo"));
            $controle->set_valor($dados[10]);
            $form->add_item($controle);

            # prioridade
            $controle = new Input('noOrdem', 'combo', 'Prioridade:', 1);
            $controle->set_size(20);
            $controle->set_linha(5);
            $controle->set_col(6);
            $controle->set_placeholder('Prioridade');
            $controle->set_title('A prioridade da tarefa');
            $controle->set_array(array(array(0, "Nenhuma"), array(1, "Média"), array(2, "Alta"), array(3, "Urgente")));
            $controle->set_valor($dados[3]);
            $form->add_item($controle);

            # conclusao
            $controle = new Input('conclusao', 'textarea', 'Conclusão:', 1);
            $controle->set_size(array(80, 3));
            $controle->set_linha(6);
            $controle->set_placeholder('O que foi feito para colcluir');
            $controle->set_title('O que foi feito para colcluir');
            $controle->set_valor($dados[9]);
            $form->add_item($controle);

            # pendente
            $controle = new Input('pendente', 'hidden', '', 1);
            $controle->set_size(20);
            $controle->set_linha(7);
            $controle->set_valor($dados[6]);
            $form->add_item($controle);

            # submit
            $controle = new Input('submit', 'submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(8);
            $form->add_item($controle);

            $form->show();

            $grid->fechaColuna();
            $grid->fechaGrid();
            break;

        ###########################################################

        case "validaTarefa" :
            # Valida uma terefa já digitada
            # Recuperando os valores
            $tarefa = post('tarefa');
            $descricao = post('descricao');
            $dataInicial = vazioPraNulo(post('dataInicial'));
            $dataFinal = vazioPraNulo(post('dataFinal'));
            $idProjeto = post('idProjeto');
            $etiqueta = vazioPraNulo(post('etiqueta'));
            $pendente = post('pendente');
            $conclusao = post('conclusao');
            $noOrdem = post('noOrdem');
            $solicitante = vazioPraNulo(post('solicitante'));
            $status = post('status');

            # Força a tarefa pendente quando é inclusão
            if (is_null($idTarefa)) {
                $pendente = 1;
            }

            # Cria arrays para gravação
            $arrayNome = array("tarefa", "descricao", "dataInicial", "dataFinal", "idProjeto", "pendente", "etiqueta", "conclusao", "noOrdem", "solicitante", "status");
            $arrayValores = array($tarefa, $descricao, $dataInicial, $dataFinal, $idProjeto, $pendente, $etiqueta, $conclusao, $noOrdem, $solicitante, $status);

            # Grava	
            $intra->gravar($arrayNome, $arrayValores, $idTarefa, "tbprojetotarefa", "idTarefa");

            # Verifica para onde volta
            loadPage("?fase=verificaVolta");
            break;

        ###########################################################  

        case "mudaTarefa" :
            # Muda somente o campo pendente true or false
            # Pega os dados da tarefa
            $valor = $projeto->get_dadosTarefa($idTarefa);

            # Verifica o valor de pendente
            if ($valor[6] == 1) {
                $pendente = 0;
            } else {
                $pendente = 1;
            }

            # Cria arrays para gravação
            $arrayNome = array("pendente");
            $arrayValores = array($pendente);

            # Grava	
            $intra->gravar($arrayNome, $arrayValores, $idTarefa, "tbprojetotarefa", "idTarefa");

            # Verifica para onde volta
            loadPage("?fase=verificaVolta");
            break;

        ###########################################################  

        case "TarefaExcluir" :
            # Exclui tarefa
            # Conecta com o banco de dados
            $intra->set_tabela("tbprojetotarefa"); # a tabela
            $intra->set_idCampo("idtarefa");     # o nome do campo id
            $intra->excluir($idTarefa);

            # Verifica para onde volta
            loadPage("?fase=verificaVolta");
            break;

#############################################################################################################################
#   Etiqueta
#############################################################################################################################

        case "etiqueta" :

            # joga para session a etiqueta
            set_session('etiqueta', $etiqueta);

            $grid->abreColuna($col2P, $col2M, $col2L);

            $grid = new Grid();
            $grid->abreColuna(8);

            # Exibe o nome e a descrição
            p("Etiqueta: " . $etiqueta, 'descricaoProjetoTitulo');
            hr("projetosTarefas");

            $grid->fechaColuna();
            $grid->abreColuna(4);

            # Menu
            $menu1 = new MenuBar("small button-group");

            $link5 = new Link("<i class='fi-plus'></i>", '?fase=tarefaNova');
            $link5->set_class('button secondary');
            $link5->set_title('Nova Tarefa');
            $menu1->add_link($link5, "right");

            $menu1->show();

            $grid->fechaColuna();
            $grid->fechaGrid();

            # Exibe as tarefas Fazendo
            $lista = new ListaTarefas("Fazendo");
            $lista->set_status("fazendo");
            $lista->set_etiqueta($etiqueta);
            $lista->show();

            # Exibe as tarefas a fazer
            $lista = new ListaTarefas("a Fazer");
            $lista->set_status("a fazer");
            $lista->set_etiqueta($etiqueta);
            $lista->show();

            # Exibe as tarefas completatadas
            $lista = new ListaTarefas("Feito");
            $lista->set_etiqueta($etiqueta);
            $lista->set_pendente(false);
            $lista->show();
            break;

#############################################################################################################################
#   Solicitante
#############################################################################################################################

        case "solicitante" :

            # joga para session o solicitante
            set_session('solicitante', $solicitante);

            $grid->abreColuna($col2P, $col2M, $col2L);

            $grid = new Grid();
            $grid->abreColuna(8);

            # Exibe o nome e a descrição
            p("Solicitante: " . $solicitante, 'descricaoProjetoTitulo');
            hr("projetosTarefas");

            $grid->fechaColuna();
            $grid->abreColuna(4);

            # Menu
            $menu1 = new MenuBar("small button-group");

            $link5 = new Link("<i class='fi-plus'></i>", '?fase=tarefaNova');
            $link5->set_class('button secondary');
            $link5->set_title('Nova Tarefa');
            $menu1->add_link($link5, "right");

            $menu1->show();

            $grid->fechaColuna();
            $grid->fechaGrid();

            # Exibe as tarefas Fazendo
            $lista = new ListaTarefas("Fazendo");
            $lista->set_status("fazendo");
            $lista->set_solicitante($solicitante);
            $lista->show();

            # Exibe as tarefas a fazer
            $lista = new ListaTarefas("a Fazer");
            $lista->set_status("a fazer");
            $lista->set_solicitante($solicitante);
            $lista->show();

            # Exibe as tarefas completatadas
            $lista = new ListaTarefas("Feito");
            $lista->set_solicitante($solicitante);
            $lista->set_pendente(false);
            $lista->show();
            break;

        ###########################################################

        case "verificaVolta" :

            # Se é projeto
            if (!is_null($idProjeto)) {
                loadPage("?fase=projeto");
            }

            # Se é etiqueta
            if (!is_null($etiqueta)) {
                loadPage("?fase=etiqueta");
            }

            # Se é solicitante
            if (!is_null($solicitante)) {
                loadPage("?fase=solicitante");
            }

            # Se é fazendo
            if ($fazendo) {
                loadPage("?fase=fazendo");
            }
            break;
    }

    $div = new Div("menuSuspenso", "show-for-small-only");
    $div->abre();
    br();

    $div2 = new Div("campoPesquisa");
    $div2->abre();

    $form = new Form('?fase=pesquisa');

    $controle = new Input('parametro', 'texto');
    $controle->set_size(30);
    $controle->set_linha(1);
    $controle->set_col(12);
    $controle->set_placeholder('Pesquisar');
    $controle->set_title('Pesquisar');
    $controle->set_onChange('formPadrao.submit();');
    $form->add_item($controle);

    $form->show();
    $div2->fecha();

    # Menu Cronológico
    Gprojetos::menuFazendo($fase);

    # Menu de Projetos
    Gprojetos::menuProjetosAtivos($idProjeto);

    # Menu de Etiquetas
    Gprojetos::menuEtiquetas($etiqueta);

    # Menu de Solicitantes
    Gprojetos::menuSolicitante($solicitante);

    $div->fecha();

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}