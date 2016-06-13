<?php
/**
 * documentaFuncao
 * 
 * Gera documentação de uma função
 */

# Configuração
include ("_config.php");

# Cabeçalho
AreaServidor::cabecalho();

# Limita o tamanho da tela
$grid = new Grid();
$grid->abreColuna(12);

# Pega a função a ser documentada
$funcao = trim(get('funcao'));
$fase = get('fase');

# Começa uma nova página
$page = new Page();
$page->iniciaPagina();

# Botão voltar
$linkBotao1 = new Link("Voltar",'documentacao.php');
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

# Variáveis
$nomeFuncao = null;		// Nome da função
$funcaoEncontrada = FALSE;      // Verifica se encontrou a função
$funcãoEscolhida = NULL;        // Nome da função escolhida
$descricaoFuncao = NULL;        // Descrição da função
$linhaComentario = NULL;        // Linha do comentário
$notaFuncao = NULL;             // Nota da Função

$linhaFuncao = NULL;		// A linha do arquivo onde está o nome da função	
$funcaoDesejada = FALSE;	// Se true informa que essa é a função solicittada
$primeiroParametros = TRUE;	// Flag que informa se é o primeiro parâmetro de uma função 
$temTabelaAberta = FALSE;	// Flag que informa que existe uma tabela da lista de parâmetros aberta se a tag não for @param terá que fechá-la
$temExemploAberto = FALSE;	// Flag que informa se tem um exemplo aberto

# Define o arquivo e caminho das funções gerais
$lines = file(PASTA_FUNCOES_GERAIS."/funcoes.gerais.php",FILE_TEXT);

# Percorre o arquivo e guarda os dados em um array
foreach ($lines as $line_num => $line) {
    $line = htmlspecialchars($line);
    
    # Função
    if(stristr($line, "function")){
        $posicao = stripos($line,'function');
        $posicaoParentesis = stripos($line,'(');
        $tamanhoNome = $posicaoParentesis - ($posicao+9);
        $nomeFuncao = substr($line, $posicao+9,$tamanhoNome);
        
        # Verifica se é a função desejada
        if($nomeFuncao == $funcao){
            $funcaoEncontrada = TRUE;
            $funcãoEscolhida = $nomeFuncao;
        }else{
            $funcaoEncontrada = FALSE;
        }
    }
        
    # Enqualto for a função desejada
    if($funcaoEncontrada){
        # Verifica se é o começo de um comentário da função
        if(stristr($line, "/**")){
            $linhaComentario = $line_num;
            continue;
        }

        # Descrição da Função
        if ($line_num == ($linhaComentario+1)){
            $posicao = stripos($line,'*');
            $descricaoFuncao = substr($line, $posicao+2);
        }
        
        # Nota
        if (stristr($line, "@note")){
            $posicao = stripos($line,'@');
            $notaFuncao = substr($line, $posicao+5);
        }
    }
}

# Exibe a função
if(!is_null($funcãoEscolhida)){
    
    # Divide a tela
    $grid2 = new Grid();
    $grid2->abreColuna(4,3);

    # Coluna de atalhos para os métodos da classe
    $callout = new Callout();
    $callout->abre();

    # Função
    echo '<h4>'.$funcãoEscolhida.'</h4>';

    $callout->fecha();
    $grid2->fechaColuna();

    # Coluna da documentação detalhada
    $grid2->abreColuna(8,9);
    $callout = new Callout("success");
    $callout->abre();
    
    # Nome
    echo '<h5>'.$funcãoEscolhida.'</h5>';
    
    # Decrição
    echo $descricaoFuncao;
    br(2);
    
    # Nota
    if(!is_null($notaFuncao)){
        echo 'Nota:';
        $callout = new Callout("warning");
        $callout->abre();
        echo $notaFuncao;
        $callout->fecha();
    }

    $callout->fecha();
    $grid2->fechaColuna();
    $grid2->fechaGrid();    
}



$grid->fechaColuna();
$grid->fechaGrid();

$page->terminaPagina();