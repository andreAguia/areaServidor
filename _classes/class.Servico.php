<?php

class Servico {

    /**
     * Abriga as várias rotina do Controle de Serviços da GRH
     * 
     * @author André Águia (Alat) - alataguia@gmail.com
     * 
     * @var private $projeto        integer null O id do projeto a ser acessado
     * 
     */
    private $projeto = null;

    ###########################################################

    /**
     * Método Construtor
     */
    public function __construct() {
        
    }

    ###########################################################

    public function get_dados($id = null) {
        /**
         * Retorna um array com todas as informações
         * 
         * @param $id integer null o id
         * 
         * @syntax $servico->get_Dados([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT *
                     FROM tbservico
                     WHERE idServico = {$id}";

        $intra = new Intra();
        $row = $intra->select($select, false);
        return $row;
    }

    ###########################################################

    public function get_anexos($id = null) {
        /**
         * Retorna um array com os anexos de um servico
         * 
         * @param $id integer null o id
         * 
         * @syntax $servico->get_Dados([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT *
                     FROM tbservicoanexos
                    WHERE idServico = {$id}
                 ORDER BY categoria";

        $intra = new Intra();
        $row = $intra->select($select);
        return $row;
    }

    ###########################################################

    public function exibeServicos($id = null) {

        # Pega os dados desse servico
        $dados = $this->get_dados($id);

        # Título
        titulotable($dados["nome"]);
        br();

        $div = new Div("divNota");
        $div->abre();

        # Define os Campos
        $campos = [
            ["O que é", "oque"],
            ["Quem Pode Requerer", "quem"],
            ["Como Requerer", "como"],
            ["Observações", "obs"],
        ];

        # Percorre os campos
        foreach ($campos as $item) {
            if (!empty($dados[$item[1]])) {

                $menu = new Menu("menuProcedimentos");
                $menu->add_item('titulo', $item[0], '#', $item[0]);
                $menu->show();

                # Exibe o texto
                echo $dados[$item[1]];
            }
        }

        # Carrega os anexos
        $dados2 = $this->get_anexos($id);

        # Defina a categoria para o agrupamento
        $categoriaAtual = null;

        # Verifica se tem algum anexo
        if (count($dados2) > 0) {

            # Menu
            $menu = new Menu("menuProcedimentos");

            # Percorre o array 
            foreach ($dados2 as $valor) {
                # Verifica se mudou a categoria
                if ($categoriaAtual <> $valor["categoria"]) {
                    $categoriaAtual = $valor["categoria"];
                    $menu->add_item('titulo', $valor["categoria"], '#', "Categoria " . $valor["categoria"]);
                    $menu->add_item('br');
                }

                if (empty($valor["title"])) {
                    $title = $valor["texto"];
                } else {
                    $title = $valor["title"];
                }

                # Verifica qual o tipo: 1-Documento e 2-Link
                if ($valor["tipo"] == 1) {
                    # É do tipo Documento
                    $arquivoDocumento = PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . ".pdf";
                    if (file_exists($arquivoDocumento)) {
                        # Caso seja PDF abre uma janela com o pdf
                        $menu->add_item('linkWindow', $valor["titulo"], PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . '.pdf', $valor["descricao"]);
                    } else {
                        # Caso seja um .doc, somente faz o download
                        $menu->add_item('link', $valor["titulo"], PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . '.doc', $valor["descricao"]);
                    }
                }

                # Tipo Link
                if ($valor["tipo"] == 2) {
                    $menu->add_item('linkWindow', " - " . $valor["texto"], "?fase=exibeDocumento&idServicoAnexos={$valor['idServicoAnexos']}", $title);
                }

                # Tipo pdf
                if ($valor["tipo"] == 3) {
                    $arquivoDocumento = PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . ".pdf";

                    $menu->add_item('linkWindow', " - " . $valor["titulo"], PASTA_SERVICOANEXOS . $valor["idServicoAnexos"] . '.pdf', $valor["descricao"]);
                }
            }


            $menu->show();
        }

        $div->fecha();
    }

    ###########################################################

    public function get_anexo($id = null) {
        /**
         * Retorna um array com os anexos de um servico
         * 
         * @param $id integer null o id
         * 
         * @syntax $servico->get_Dados([$id]);  
         */
        # Pega os projetos cadastrados
        $select = "SELECT *
                     FROM tbservicoanexos
                    WHERE idServicoAnexo = {$id}";

        $intra = new Intra();
        $row = $intra->select($select);
        return $row;
    }

    ###########################################################

    function exibeProcedimento($id, $editar = false) {

        /**
         * Fornece todos os dados da categoria
         */
        # Pega os dados
        $dados = $this->get_anexo($id);

        $grid = new Grid();
        $grid->abreColuna(12);

        # Div onde vai exibir o procedimento
        $div = new Div("divNota");
        $div->abre();

        if (!empty($dados)) {

            # Exibe de acordo com o tipo do arquivo
            switch ($dados["tipo"]) {
                case 1 : // documento
                    br();
                    # Botão de Editar
                    if ($editar) {
                        $divBtn = new Div("editarProcedimento");
                        $divBtn->abre();

                        $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
                        $btnEditar->set_class('button secondary');
                        $btnEditar->set_title('Editar o Procedimento');
                        $btnEditar->show();

                        $divBtn->fecha();
                    }

                    # Exibe o titulo
                    p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                    br();

                    p($dados['titulo'], "procedimentoTitulo");
                    p($dados['descricao'], "procedimentoDescricao");
                    hr("procedimento");

                    if (empty($dados['textoProcedimento'])) {
                        br(4);
                        p("Não há conteúdo", "center");
                        br(10);
                    } else {
                        echo $dados['textoProcedimento'];
                    }
                    break;
                case 2: // arquivo jpg
                    br();
                    # Botão de Editar
                    if ($editar) {
                        $divBtn = new Div("editarProcedimento");
                        $divBtn->abre();

                        $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
                        $btnEditar->set_class('button secondary');
                        $btnEditar->set_title('Editar o Procedimento');
                        $btnEditar->show();

                        $divBtn->fecha();
                    }

                    # Exibe o titulo
                    p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                    br();

                    p($dados['titulo'], "procedimentoTitulo");
                    p($dados['descricao'], "procedimentoDescricao");
                    br();

                    # Define o arquivo
                    $arquivo = PASTA_PROCEDIMENTOS . $id . '.jpg';

                    if (file_exists($arquivo)) {
                        $figura = new Imagem($arquivo, $dados['descricao'], '100%', '100%');
                        $figura->show();
                    } else {
                        br(4);
                        p("Não há conteúdo", "center");
                        br(10);
                    }
                    break;
                case 3: // arquivo pdf
                    br();
                    # Botão de Editar
                    if ($editar) {
                        $divBtn = new Div("editarProcedimento");
                        $divBtn->abre();

                        $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
                        $btnEditar->set_class('button secondary');
                        $btnEditar->set_title('Editar o Procedimento');
                        $btnEditar->show();

                        $divBtn->fecha();
                    }

                    # Exibe o titulo
                    p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                    br();

                    p($dados['titulo'], "procedimentoTitulo");
                    p($dados['descricao'], "procedimentoDescricao");
                    br();

                    # Define o arquivo
                    $arquivo = PASTA_PROCEDIMENTOS . $idProcedimento . '.pdf';

                    if (file_exists($arquivo)) {
                        echo '<iframe src="' . PASTA_PROCEDIMENTOS . $idProcedimento . '.pdf" height="1000px" width="100%" marginwidth ="0" marginheight ="0" style="border:1px solid #d7d7d7;"></iframe>';
                    } else {
                        # Monta o painel
                        $painel = new Callout();
                        $painel->abre();

                        # Exibe o titulo
                        p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                        br();

                        p($dados['titulo'], "procedimentoTitulo");
                        p($dados['descricao'], "procedimentoDescricao");
                        hr("procedimento");

                        br(4);
                        p("Não há conteúdo", "center");
                        br(10);
                        $painel->fecha();
                    }
                    break;
                case 4: // link
                    br();
                    # Botão de Editar
                    if ($editar) {
                        $divBtn = new Div("editarProcedimento");
                        $divBtn->abre();

                        $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
                        $btnEditar->set_class('button secondary');
                        $btnEditar->set_title('Editar o Procedimento');
                        $btnEditar->show();

                        $divBtn->fecha();
                    }

                    # Exibe o titulo
                    p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                    br();

                    p($dados['titulo'], "procedimentoTitulo");
                    p($dados['descricao'], "procedimentoDescricao");
                    br();

                    echo "<iframe src='{$dados['link']}' height='1000px' width='100%' marginwidth ='0' marginheight ='0' style='border:1px solid #d7d7d7;'></iframe>";
                    break;
                case 5: // rotina
                    br();
                    # Botão de Editar
                    if ($editar) {
                        $divBtn = new Div("editarProcedimento");
                        $divBtn->abre();

                        $btnEditar = new Link("<i class='fi-pencil'></i>", "procedimentoNota.php?fase=editar&id=$idProcedimento");
                        $btnEditar->set_class('button secondary');
                        $btnEditar->set_title('Editar o Procedimento');
                        $btnEditar->show();

                        $divBtn->fecha();
                    }

                    # Exibe o titulo
                    p("{$dados['categoria']} / {$dados['subCategoria']} / {$dados['titulo']}", "procedimentoPai");
                    br();

                    $rotina = new Rotina();
                    $rotina->exibeRotina($dados['idRotina']);
                    break;
            }
            $div->fecha();
        }
        $grid->fechaColuna();
        $grid->fechaGrid();
    }

    ###########################################################
}
