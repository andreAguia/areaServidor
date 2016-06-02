<?php
/**
 * documentaFuncao
 * 
 * Gera documentação de uma função
 */

# Configuração
include ("_config.php");

# Pega a função a ser documentada
$funcao = trim(get('funcao'));

# Começa uma nova página
$page = new Page();
$page->iniciaPagina();

# Botão voltar
$linkBotaoVoltar = new Link("Voltar",'documentacao.php');
$linkBotaoVoltar->set_class('hollow button float-left');
$linkBotaoVoltar->set_title('Volta para a página anterior');
$linkBotaoVoltar->set_accessKey('V');

# Cria um menu
$menu = new MenuBar();
$menu->add_link($linkBotaoVoltar,"left");
$menu->show();

# Variáveis
$nomeFuncao = null;			// O nome da função que está o ponteiro
$linhaFuncao = null;		// A linha do arquivo onde está o nome da função	
$funcaoDesejada = false;	// Se true informa que essa é a função solicittada
$primeiroParametros = true;	// Flag que informa se é o primeiro parâmetro de uma função 
$temTabelaAberta = false;	// Flag que informa que existe uma tabela da lista de parâmetros aberta se a tag não for @param terá que fechá-la
$temExemploAberto = false;	// Flag que informa se tem um exemplo aberto

# Define o arquivo e caminho das funções gerais
$arquivoFuncaoGeral = PASTA_FUNCOES_GERAIS.'/funcoes.gerais.php';

# Lê e guarda no array $lines o conteúdo do arquivo
$lines = file ($arquivoFuncaoGeral);

# Divide a tela
echo '<div class="row">';

# Coluna de atalhos para os métodos da classe
echo '<div class="small-3 columns">';
echo '<div class="callout secondary">';

# Define o arquivo e caminho das funções gerais
$arquivoFuncaoGeral = PASTA_FUNCOES_GERAIS.'/funcoes.gerais.php';

# Lê e guarda no array $lines o conteúdo do arquivo
$lines = file ($arquivoFuncaoGeral);

# Inicia o menu
#echo '<ul class="menu vertical">';

echo '<h6>Funções Gerais</h6>';

# Percorre o array
foreach ($lines as $line_num => $line){
  $line = htmlspecialchars($line);

  # Função
  if (stristr($line, "@function")){
    $posicao = stripos($line,'@');

    echo '<a href="documentaFuncao.php?funcao='.substr($line, $posicao+10).'">';
    echo substr($line, $posicao+10);
    echo '</a>';
    br();
  }
}

echo '</div>';
echo '</div>';

echo '<div class="small-9 columns">';
echo '<div class="callout primary">';

# Percorre o array
foreach ($lines as $line_num => $line){
  	$line = htmlspecialchars($line);

	# Nome da Função
	if (stristr($line, "@function")){
		$posicao = stripos($line,'@');

		# Pega o nome da função
		$nomeFuncao = trim(substr($line, $posicao+10));

		# Verifica se é a função desejada
		if($funcao == $nomeFuncao){

			# Informa ao ponteiro
			$funcaoDesejada = true;

			# Exibe o nome da função
			echo '<h4>'.$nomeFuncao.'</h4>';

			# Guarda o número da linha da função
			if (is_null($linhaFuncao))
				$linhaFuncao = $line_num;
		}
		else{
			$funcaoDesejada = false;    	
		}
	}

	# Descrição
	if (($funcaoDesejada) AND ($line_num == ($linhaFuncao+2))){
		$posicao = stripos($line,'*');
		echo substr($line, $posicao+2);
		hr();
	}

	# Sintaxe
	if (($funcaoDesejada) AND (stristr($line, "@syntax"))){
		$posicao = stripos($line,'@');
		echo 'Sintaxe:';
		echo '<div class="callout secondary">';
		echo substr($line, $posicao+8);
		echo '</div>';
	}

	# Nota
	if (($funcaoDesejada) AND (stristr($line, "@note"))){
		$posicao = stripos($line,'@');

		# Verifica se tem tabela a berta e a fecha
        if ($temTabelaAberta){
          echo '</table>';

          # informa que fechou a tabela
          $temTabelaAberta = false;
        }

		echo 'Nota:';
		echo '<div class="callout warning">';
		echo substr($line, $posicao+5);
		echo '</div>';
	}

	# Deprecated
	if (($funcaoDesejada) AND (stristr($line, "@deprecated"))){
		echo '<div class="callout alert">';
		echo '<h6>DEPRECATED</h6> Esta função deverá ser descontiuada nas próximas versões.<br/>Seu uso é desaconselhado.';
		echo '</div>';
	}

	# Parâmetros de um método
	if (($funcaoDesejada) AND (stristr($line, "@param"))){
		$piecesParam = str_word_count($line,1,'/:ãõáéíúóâê1234567890');

		if($primeiroParametros){
		  echo 'Parâmetros:';

		  echo '<table>';
		  echo '<col style="width:10%">';        
		  echo '<col style="width:10%">';
		  echo '<col style="width:10%">';
		  echo '<col style="width:70%">';
		 
		  echo '<tr>';
		  echo '<th>Nome</th>';
		  echo '<th>Tipo</th>';
		  echo '<th>Padrão</th>';
		  echo '<th>Descrição</th>';
		  echo '</tr>';

		  # Volta a opçào de não ser o primeiro parâmetro
		  $primeiroParametros = false;

		  # informa que existe uma tabela de parâmetros aberta
		  $temTabelaAberta = true;
		}

		echo '<tr>';
		echo '<td>';
		echo $piecesParam[1];
		echo '</td>';

		echo '<td>';
		echo $piecesParam[2];
		echo '</td>';

		echo '<td>';
		echo $piecesParam[3];
		echo '</td>';

		echo '<td>';
		for($i=4; $i<count($piecesParam); $i++){
		  echo $piecesParam[$i];
		  echo ' ';
		}
		echo '</td>';
		echo '</tr>';       
	}

	# Exemplo
	if (($funcaoDesejada) AND (stristr($line, "@codeInicio"))){

		# Informa que abriu uma div de exemplo
		$temExemploAberto = true;

		# Verifica se tem tabela a berta e a fecha
        if ($temTabelaAberta){
          echo '</table>';

          # informa que fechou a tabela
          $temTabelaAberta = false;
        }

		echo 'Exemplo:';
		echo '<div class="callout secondary" id="codigo">';
		echo '<pre>';

		continue;      
	}

	if (($funcaoDesejada) AND (stristr($line, "@codeFim"))){

		# Informa que fechou uma div de exemplo
		$temExemploAberto = false;

		echo '</pre>';
		echo '</div>';		
	}


	if ($temExemploAberto){
		$posicao = stripos($line,'*');
		echo substr($line, $posicao+2);
		br();
	}

	
}

echo '</div>';
echo '</div>';
echo '</div>';

$page->terminaPagina();