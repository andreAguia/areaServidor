<?php

/**
* Trocar Senha
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

    # Define a senha padrão de acordo com o que está nas variáveis
    define("SENHA_PADRAO",$intra->get_variavel('senha_padrao'));

    # Verifica a fase do programa
    $fase = get('fase','editar');

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();	

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Exibe os dados do Servidor    
    Grh::listaDadosServidor($intra->get_idServidor($idUsuario));

    ##################################################

    # abre um novo objeto 
    $objeto = new Modelo();

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Alterar Senha');
    $objeto->set_id('trocaSenha');

    # Link do botão de voltar
    $objeto->set_voltarForm('areaServidor.php');

    # Exibe o botão voltar se não for redirecionado pelo login
    if($fase == "trocaSenhaSV"){
        $objeto->set_botaoVoltarForm(false);
        $fase = 'editar';
    }

    # Caminhos
    $objeto->set_linkGravar('?fase=gravar');

    # Classe do banco de dados
    $objeto->set_classBd('servidor');

    # Nome do campo id
    $objeto->set_idCampo('idServidor');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # botão salvar
    $objeto->set_botaoSalvarGrafico(false);

    # Campos para o formulario
    $objeto->set_campos(array( 
            array ( 'nome' => 'senhaIntro1',
                    'label' => 'Entre com a nova senha:',
                    'tipo' => 'password',
                    'autofocus' => true,
                    'size' => 60,
                    'required' => true,
                    'title' => 'A nova senha.',
                    'linha' => 1),
            array ( 'nome' => 'senhaIntro2',
                    'label' => 'Redigite e senha:',
                    'tipo' => 'password',
                    'title' => 'Redigite a nova senha para eliminar erros de digitação.',
                    'size' => 60,
                    'required' => true,
                    'linha' => 2)));

    switch ($fase)
    {
        case "editar" :
            # gera o formulário
            $objeto->set_botaoHistorico(false);
            $objeto->editar($idUsuario);

            # Post it
            $mensagem = 'Lembre-se: você é o único responsável pela guarda e sigilo
                         de sua senha. Não divulgue sua senha a terceiros, ainda que
                         estes terceiros sejam de sua plena confiança. Memorize sua
                         senha e não a anote.';
            $postit = new Postit($mensagem,'postitTrocaSenha');            
            $postit->show();

            # botão abre dicas
            $botao = new BotaoGrafico('botaoDicasSenha');
            $botao->set_title('Dicas para uma Senha Segura');
            $botao->set_image(PASTA_FIGURAS.'botaoDicasSenha.png',150,50);
            $botao->set_onClick("abreDivId('divDicas');");
            #$botao->show();
            
            $botao = new Button('botaoDicasSenha');
            $botao->set_label('Dicas para uma Senha Segura');
            $botao->set_onClick("abreDivId('divDicas');");
            $botao->show();

            # Dicas de Senha Segura
            $divDicas = new Box('divDicas');
            $divDicas->set_titulo('Dicas para uma Senha Segura');
            $divDicas->abre();           

                $p = new P('<br/>1. Use mais de uma palavra<br/>','pDica');
                $p->show();

                    $p = new P('Em vez de usar apenas o nome de uma pessoa conhecida, como "Amador", escolha um detalhe dessa pessoa que mais ninguém saiba, por exemplo, "UrsoAmador" ou "UrsoDodo".','pDicaTexto');
                    $p->show();

                $p = new P('<br/>2. Use símbolos em vez de caracteres<br/>','pDica');
                $p->show();

                    $p = new P('Muitas pessoas tendem a colocar os símbolos e números exigidos no fim de uma palavra, por exemplo, "Amador1234". Infelizmente, isso é relativamente fácil de ser quebrado. A palavra "Amador" encontra-se em diversos dicionários que têm nomes comuns; depois de ter descoberto o nome, o atacante tem apenas mais quatro caracteres relativamente fáceis para adivinhar. Em vez disso, substitua uma ou mais letras da palavra por símbolos que você lembre facilmente. Muitas pessoas têm suas próprias interpretações criativas da letra com que alguns símbolos e números se parecem. Por exemplo, tente substituir "A" por "@", "l" por "!", "O" por zero (0), "S" por "$" e "E" por "3". Com substituições como essas, você reconheceria "Ur$o@mador", "Ur$oAm@dor" e "Urs0Am@d0r", mas elas seriam muito difíceis de serem adivinhadas ou quebradas. Examine os símbolos do seu teclado e pense nos primeiros caracteres que lhe vêm à cabeça; pode não ser o que outra pessoa imaginaria, mas você lembrará. Use alguns desses símbolos como substituições nas suas senhas de hoje em diante.','pDicaTexto');
                    $p->show();

                $p = new P('<br/>3. Escolha acontecimentos ou pessoas em que você pensa<br/>','pDica');
                $p->show();   

                    $p = new P('Para lembrar uma senha de alta segurança que deverá ser alterada em alguns meses, tente escolher um acontecimento futuro pessoal ou público. Use como uma oportunidade para lembrar de algo agradável que está acontecendo na sua vida, ou de uma pessoa que você admira ou ama. Provavelmente você não esquecerá a senha se ela for engraçada ou carinhosa. Torne-a exclusividade sua. Certifique-se de torná-la uma frase com duas ou mais palavras e continue a usar os símbolos. Por exemplo: "F0rm@turaJ0na$".','pDicaTexto');
                    $p->show();

                $p = new P('<br/>4. Use fonética nas palavras<br/>','pDica');
                $p->show();

                    $p = new P('Geralmente, dicionários de senhas usados por atacantes buscam palavras contidas na sua senha. Como já foi mencionado, não hesite em usar palavras, mas certifique-se de salpicar generosamente símbolos no meio dessas palavras. Outra forma de vencer o atacante é evitar soletrar as palavras corretamente, ou usar fonéticas engraçadas que você possa lembrar. Por exemplo, "Cade a galinha" poderia se transformar "KDHlinh@!".','pDicaTexto');
                    $p->show();

                $p = new P('<br/>5. Não tenha medo de criar uma senha longa<br/>','pDica');
                $p->show();

                    $p = new P('Se for mais fácil para você lembrar uma frase completa, digite-a. Senhas mais longas são muito mais difíceis de serem quebradas. E mesmo se for longa, se for fácil de lembrar, provavelmente você terá muito menos dificuldade para entrar no sistema, mesmo que não seja o melhor digitador do mundo.','pDicaTexto');
                    $p->show();

                # botão próxima página
                $botao = new BotaoGrafico();
                #$botao->set_label('Dicas para uma Senha Segura');
                $botao->set_title('Próxima página');
                $botao->set_image(PASTA_FIGURAS.'bt_proxima.jpg',38,26);
                $botao->set_onClick("fechaDivId('divDicas'); abreDivId('divDicas2');");
                $botao->show();    

            $divDicas->fecha(); 

            # Dicas de Senha Segura
            $divDicas = new Box('divDicas2');
            $divDicas->set_titulo('Dicas para uma Senha Segura');
            $divDicas->abre();

            $p = new P('<br/>6. Use as primeiras letras de uma frase<br/>','pDica');
            $p->show();

                $p = new P('Para criar uma senha fácil de lembrar e de alto nível, comece com uma frase que tenha as maiúsculas e a pontuação corretas, e que seja fácil de lembrar. Por exemplo: "Minha filha Laura estuda na Escola Internacional". Depois, pegue a primeira letra de cada palavra da frase, preservando as maiúsculas usadas. No exemplo acima, "MfLenEI" seria o resultado. Finalmente, substitua algumas letras da senha por caracteres não-alfanuméricos. Você pode usar um "@" para substituir um "a" ou um "!" para substituir um "L". Depois dessa substituição, a senha do exemplo acima poderia ser "Mf!enEI"; uma senha muito difícil de ser quebrada, e mesmo assim, fácil de lembrar, desde que você possa lembrar a frase na qual ela se baseou.<br/>','pDicaTexto');
                $p->show();

            # Faça
            $divFaca = new Div('divFaca');          
            $divFaca->abre(); 

            $p = new P('Faça<br/><br/>','pDicaTitulo');
            $p->show();

                $p = new P('- Combine letras, símbolos e números de que você se lembre facilmente e que sejam difíceis para os outros adivinharem.<br/>
                            - Crie senhas que possam ser pronunciadas (mesmo que não sejam palavras) fáceis de lembrar, o que diminui a tentação de anotá-las.<br/>
                            - Tente usar as letras inicias de uma frase que você goste, especialmente se ela incluir um número ou caractere especial.<br/>
                            - Use duas coisas familiares e combine-as com um número ou caractere especial. Como alternativa, altere a ortografia incluindo um caractere especial. Dessa forma, você obtém algo desconhecido, o que gera uma boa senha, pois é fácil para você, e somente para você, lembrar, mas difícil para qualquer outra pessoa descobrir.
                                Aqui estão alguns exemplos:<br/>','pDicaTexto');
                $p->show();

                $p = new P('- "Estou + 100 + dinheiro" = "Estou100dinheiro" ou "E$t0u100dinheir0"<br/>
                            - "gato + * + Rato" = "gato*Rato" ou "gato*R@to"<br/>
                            - "ataque + 3 + livro" = "ataque3livro" ou "@taque3livrO"','pDicaTexto');
                $p->show();

            $divFaca->fecha(); 

            # Não Faça
            $divFaca = new Div('divNFaca');
            $divFaca->abre(); 

            $p = new P('Não Faça<br/><br/>','pDicaTitulo');
            $p->show();

                $p = new P('- Não use informações pessoais, como derivados da sua identidade de usuário, nomes de membros da família, nomes de solteira, carros, placas de automóveis, números de telefone, animais de estimação, aniversários, números de CPF, endereços ou passatempos.<br/>
                            - Não use palavras em qualquer idioma, soletradas de trás para frente ou de frente para trás.<br/>
                            - Não junte senhas ao mês, por exemplo, não use "Maioral" em maio.<br/>
                            - Não crie senhas novas substancialmente parecidas com as senhas usadas anteriormente.','pDicaTexto');
                $p->show();

            $divFaca->fecha();

            $p = new P('<br/><br/><br/>Retirado do artigo de Diogo Henrique Silva - site:http://technet.microsoft.com/pt-br/','center');
            $p->show();

            $divDicas->fecha(); 
            break;

    case "gravar" :
        $erro = 0;		  // flag de erro: 1 - tem erro; 0 - não tem	
        $msgErro = null; // repositório de mensagens de erro

        # pega as senhas digitadas
        $senha1 = post('senhaIntro1');
        $senha2 = post('senhaIntro2');

        # Verifica se estão nulas
        if((is_null($senha1)) or (is_null($senha2)))
        {
            $msgErro.='A senha não pode estar vazia !!';
            $erro = 1;
        }

        # Verifica se estão em branco
        if(($senha1 == '') or ($senha2 == ''))
        {
            $msgErro.='A senha não pode estar vazia !!';
            $erro = 1;
        }

        # Verifica se não é a senha padrao
        if(($senha1 == SENHA_PADRAO) AND ($senha2 == SENHA_PADRAO))
        {
            $msgErro.='Fala Sério !! A senha não pode ser essa !! A senha padrão é insegura !! Todos conhecem ela !!';
            $erro = 1;
        }

        # Verifica se são iguais
        if($senha1 <> $senha2)
        {
            $msgErro.='As senhas não são iguais !!';
            $erro = 1;
        }

        if ($erro == 0){

                # Conecta com o banco de dados
                $objeto = new Pessoal();
                $objeto->set_senha($matricula,$senha1);

                # Grava no log a atividade
                $log = new Intra();
                $data = date("Y-m-d H:i:s");
                $atividade = 'Alterou a própria senha';
                $log->registraLog($matricula,$data,$atividade,null,null);
                loadPage('areaServidor.php');
        }else{
                $alert = new Alert($msgErro) ;
                $alert->show();
                back(1);
        }		   	
        break;
     }

    $page->terminaPagina();
}