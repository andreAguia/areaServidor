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
$acesso = Verifica::acesso($idUsuario);

if ($acesso) {

    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();

    # Define a senha padrão de acordo com o que está nas variáveis
    define("SENHA_PADRAO", $intra->get_variavel('senhaPadrao'));

    # Verifica a fase do programa
    $fase = get('fase');

    # Começa uma nova página
    $page = new Page();
    #$page->set_jscript($senhaForte);
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita a tela
    $grid = new Grid();
    $grid->abreColuna(12);

    ##################################################

    switch ($fase) {
        case "":
            # Botão voltar
            if (Verifica::acesso($idUsuario, 2)) {
                $pagina = '../../grh/grhSistema/grh.php';
            } else {
                $pagina = 'areaServidor.php';
            }

            $linkBotao1 = new Link("Voltar", $pagina);
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');

            # Banco de Dados
            $linkBotao2 = new Link("Dicas para uma Senha Segura", "?fase=dicas");
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Dicas para uma senha Segura');
            $linkBotao2->set_accessKey('D');

            # Cria um menu
            $menu = new MenuBar();
            $menu->add_link($linkBotao1, "left");
            $menu->add_link($linkBotao2, "right");
            #$menu->add_link($linkBotao3,"right");    
            $menu->show();

            # Exibe os dados do Servidor
            Grh::listaDadosServidor($intra->get_idServidor($idUsuario));

            # Limita a tela
            $grid = new Grid("center");
            $grid->abreColuna(5);
            br();

            titulo('Alteração da Senha');

            # Formulário
            $callout = new Callout();
            $callout->abre();
            $form = new Form('?fase=gravar', 'login');

            # usuário
            $controle = new Input('senha1', 'password', 'Digite a Nova Senha:', 1);
            $controle->set_size(20);
            $controle->set_linha(1);
            $controle->set_required(true);
            $controle->set_autofocus(true);
            $controle->set_tabIndex(1);
            $controle->set_placeholder('Digite a Senha');
            $controle->set_title('A nova senha');
            $form->add_item($controle);

            # senha
            $controle = new Input('senha2', 'password', 'Redigite a Nova Senha:', 1);
            $controle->set_size(20);
            $controle->set_linha(2);
            $controle->set_required(true);
            $controle->set_tabIndex(2);
            $controle->set_title('Redigite a nova senha para confirmar e evitar erros de digitação');
            $controle->set_placeholder('Redigite a senha');
            $form->add_item($controle);

            # submit
            $controle = new Input('submit', 'submit');
            $controle->set_valor('Entrar');
            $controle->set_linha(3);
            $controle->set_tabIndex(3);
            $controle->set_accessKey('E');
            $form->add_item($controle);

            $form->show();
            $callout->fecha();

            $grid->fechaColuna();
            $grid->fechaGrid();

            br(2);
            Grh::rodape($idUsuario);
            break;

        #########################################################################################

        case "gravar" :
            $erro = 0;    // flag de erro: 1 - tem erro; 0 - não tem	
            $msgErro = null; // repositório de mensagens de erro
            # pega as senhas digitadas
            $senha1 = post('senha1');
            $senha2 = post('senha2');

            # Verifica se estão nulas
            if ((is_null($senha1)) or (is_null($senha2))) {
                $msgErro .= 'A senha não pode estar vazia !!';
                $erro = 1;
            }

            # Verifica se estão em branco
            if (($senha1 == '') or ($senha2 == '')) {
                $msgErro .= 'A senha não pode estar vazia !!';
                $erro = 1;
            }

            # Verifica se não é a senha padrao
            if (($senha1 == SENHA_PADRAO) AND ($senha2 == SENHA_PADRAO)) {
                $msgErro .= 'Fala Sério !! A senha não pode ser essa !! A senha padrão é insegura !! Todos conhecem ela !!';
                $erro = 1;
            }

            # Verifica se são iguais
            if ($senha1 <> $senha2) {
                $msgErro .= 'As senhas não são iguais !!';
                $erro = 1;
            }

            if ($erro == 0) {
                # Altera a senha
                $intra->set_senha($idUsuario, $senha1);

                # Pega o idServidor
                $idServidor = $intra->get_idServidor($idUsuario);

                # Grava no log a atividade
                $data = date("Y-m-d H:i:s");
                $atividade = 'Alterou a própria senha';
                $intra->registraLog($idUsuario, $data, $atividade, 'tbusuario', $idUsuario, 2, $idServidor);

                if (Verifica::acesso($idUsuario, 2)) {
                    loadPage("../../grh/grhSistema/grh.php");
                } else {
                    loadPage('areaServidor.php');
                }
            } else {
                alert($msgErro);
                back(1);
            }
            break;

        #########################################################################################

        case "dicas":
            botaoVoltar("?");
            titulo('Dicas para um Senha Segura');
            br();

            # Define o tipo de callout
            $tipo = array(null, "secondary", "primary", "success", "warning", "alert", null);

            # Mensagens
            $mensagem[1] = '1. Use mais de uma palavra<br/>Em vez de usar apenas o nome de uma pessoa conhecida, como "Amador", escolha um detalhe dessa pessoa que mais ninguém saiba, por exemplo, "UrsoAmador" ou "UrsoDodo".';
            $mensagem[2] = '2. Use símbolos em vez de caracteres<br/>Muitas pessoas tendem a colocar os símbolos e números exigidos no fim de uma palavra, por exemplo, "Amador1234".'
                    . 'Infelizmente, isso é relativamente fácil de ser quebrado.'
                    . 'A palavra "Amador" encontra-se em diversos dicionários que têm nomes comuns; depois de ter descoberto o nome, o atacante tem apenas mais quatro caracteres relativamente fáceis para adivinhar.'
                    . 'Em vez disso, substitua uma ou mais letras da palavra por símbolos que você lembre facilmente.'
                    . 'Muitas pessoas têm suas próprias interpretações criativas da letra com que alguns símbolos e números se parecem.'
                    . 'Por exemplo, tente substituir "A" por "@", "l" por "!", "O" por zero (0), "S" por "$" e "E" por "3".'
                    . 'Com substituições como essas, você reconheceria "Ur$o@mador", "Ur$oAm@dor" e "Urs0Am@d0r", mas elas seriam muito difíceis de serem adivinhadas ou quebradas.'
                    . 'Examine os símbolos do seu teclado e pense nos primeiros caracteres que lhe vêm à cabeça; pode não ser o que outra pessoa imaginaria, mas você lembrará.'
                    . 'Use alguns desses símbolos como substituições nas suas senhas de hoje em diante.';
            $mensagem[3] = '3. Escolha acontecimentos ou pessoas em que você pensa<br/>'
                    . 'Para lembrar uma senha de alta segurança que deverá ser alterada em alguns meses, tente escolher um acontecimento futuro pessoal ou público.'
                    . 'Use como uma oportunidade para lembrar de algo agradável que está acontecendo na sua vida, ou de uma pessoa que você admira ou ama.'
                    . 'Provavelmente você não esquecerá a senha se ela for engraçada ou carinhosa. Torne-a exclusividade sua.'
                    . 'Certifique-se de torná-la uma frase com duas ou mais palavras e continue a usar os símbolos. Por exemplo: "F0rm@turaJ0na$".';
            $mensagem[4] = '4. Use fonética nas palavras<br/>'
                    . 'Geralmente, dicionários de senhas usados por atacantes buscam palavras contidas na sua senha.'
                    . 'Como já foi mencionado, não hesite em usar palavras, mas certifique-se de salpicar generosamente símbolos no meio dessas palavras.'
                    . 'Outra forma de vencer o atacante é evitar soletrar as palavras corretamente, ou usar fonéticas engraçadas que você possa lembrar.'
                    . 'Por exemplo, "Cade a galinha" poderia se transformar "KDHlinh@!".';
            $mensagem[5] = '5. Não tenha medo de criar uma senha longa<br/>'
                    . 'Se for mais fácil para você lembrar uma frase completa, digite-a.'
                    . 'Senhas mais longas são muito mais difíceis de serem quebradas.'
                    . ' E mesmo se for longa, se for fácil de lembrar, provavelmente você terá muito menos dificuldade para entrar no sistema, mesmo que não seja o melhor digitador do mundo.';
            $mensagem[6] = '6. Use as primeiras letras de uma frase<br/>'
                    . 'Para criar uma senha fácil de lembrar e de alto nível, comece com uma frase que tenha as maiúsculas e a pontuação corretas, e que seja fácil de lembrar.'
                    . 'Por exemplo: "Minha filha Laura estuda na Escola Internacional". Depois, pegue a primeira letra de cada palavra da frase, preservando as maiúsculas usadas.'
                    . 'No exemplo acima, "MfLenEI" seria o resultado. Finalmente, substitua algumas letras da senha por caracteres não-alfanuméricos.'
                    . 'Você pode usar um "@" para substituir um "a" ou um "!" para substituir um "L". Depois dessa substituição, a senha do exemplo acima poderia ser "Mf!enEI"; uma senha muito difícil de ser quebrada, e mesmo assim, fácil de lembrar, desde que você possa lembrar a frase na qual ela se baseou.<br/>';

            # coluna 1
            $grid = new Grid("center");
            $grid->abreColuna(6);

            # Exibe as mensagens
            for ($i = 1; $i <= 3; $i++) {
                callout($mensagem[$i], $tipo[$i]);
            }

            $grid->fechaColuna();

            # Coluna 2
            $grid->abreColuna(6);

            # Exibe as mensagens
            for ($i = 4; $i <= 6; $i++) {
                callout($mensagem[$i], $tipo[$i]);
            }

            $grid->fechaColuna();
            $grid->fechaGrid();

            hr();

            $mensagem = 'Faça<br/><br/>'
                    . '- Combine letras, símbolos e números de que você se lembre facilmente e que sejam difíceis para os outros adivinharem.<br/>'
                    . '- Crie senhas que possam ser pronunciadas (mesmo que não sejam palavras) fáceis de lembrar, o que diminui a tentação de anotá-las.<br/>'
                    . '- Tente usar as letras inicias de uma frase que você goste, especialmente se ela incluir um número ou caractere especial.<br/>'
                    . '- Use duas coisas familiares e combine-as com um número ou caractere especial. Como alternativa, altere a ortografia incluindo um caractere especial. Dessa forma, você obtém algo desconhecido, o que gera uma boa senha, pois é fácil para você, e somente para você, lembrar, mas difícil para qualquer outra pessoa descobrir.'
                    . '<br/>Aqui estão alguns exemplos:<br/>'
                    . '- "Estou + 100 + dinheiro" = "Estou100dinheiro" ou "E$t0u100dinheir0"<br/>'
                    . '- "gato + * + Rato" = "gato*Rato" ou "gato*R@to"<br/>'
                    . '- "ataque + 3 + livro" = "ataque3livro" ou "@taque3livrO"';
            callout($mensagem, "primary");

            $mensagem = 'Não Faça<br/><br/>'
                    . '- Não use informações pessoais, como derivados da sua identidade de usuário, nomes de membros da família, nomes de solteira, carros, placas de automóveis, números de telefone, animais de estimação, aniversários, números de CPF, endereços ou passatempos.<br/>'
                    . '- Não use palavras em qualquer idioma, soletradas de trás para frente ou de frente para trás.<br/>'
                    . '- Não junte senhas ao mês, por exemplo, não use "Maioral" em maio.<br/>'
                    . '- Não crie senhas novas substancialmente parecidas com as senhas usadas anteriormente.';
            callout($mensagem, "secondary");

            p('Retirado do artigo de Diogo Henrique Silva - site:http://technet.microsoft.com/pt-br/', 'right');
            break;
    }

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}