<?php
/**
 * Rotina de Importação
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

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();
    
    # Verifica a fase do programa
    $fase = get('fase');
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    br();

    switch ($fase){
        case "" :
            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar",'administracao.php?fase=importacao');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1,"left");

            # Importar
            $linkBotao2 = new Link("Importar","?fase=inicia");
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('I');
            $menu->add_link($linkBotao2,"right");
            
            # Analisar
            $linkBotao3 = new Link("Analisar","?fase=analisa");
            $linkBotao3->set_class('button');
            $linkBotao3->set_title('Verifica os registros que não foram importados');
            $linkBotao3->set_accessKey('A');
            $menu->add_link($linkBotao3,"right");
            $menu->show();
            break;
        
        case "inicia":
            br(4);
            aguarde();
            br();    
           
            loadPage('?fase=importa');
            break;

        case "importa" :
            # Conecta ao banco
            $pessoal = new Pessoal();
            botaoVoltar("?");
            titulo('Implantação de arquivo externo para cadastro das cidades');

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Variáveis
            $numItensImportados = 0;    // Número de itens importados
            $numIdInvalido = 0;         // Número de ids inválidos
            $item = 1;

            # select
            $select = "SELECT idPessoa,
                              endereco,
                              complemento,
                              bairro,
                              LCASE(cidade),
                              tbcidade.nome,
                              uf 
                        FROM tbpessoa LEFT JOIN tbcidade USING (idCidade)
                    ORDER BY cidade";
            $conteudo = $pessoal->select($select,TRUE);

            echo "<table class='tabelaPadrao'>";
            echo "<tr>";
            echo "<th>#</th>";
            echo "<th>IdPessoa</th>";
            echo "<th>Endereço</th>";
            #echo "<th>Complemento</th>";
            echo "<th>Bairro</th>";
            echo "<th>Cidade</th>";
            echo "<th>IdCidade</th>";
            echo "<th>UF</th>";
            echo "</tr>";

            # Contador
            $item = 1;

            # Percorre a tabela
            foreach ($conteudo as $campo){

                # Ve qual é a cidade
                $cidade = $campo[4];

                # Percorre a tabela de cidades e verifica cida 

                # Inicia a tabela que exibe o processo de impotação
                echo "<tr>";

                echo "<td>".$item."</td>";
                echo "<td>".$campo[0]."</td>";
                if(is_null($campo[2])){
                    $novoEndereco = $campo[1];
                }else{
                    $novoEndereco = $campo[1]." ".$campo[2];
                }
                echo "<td>".$novoEndereco."</td>";
                #echo "<td>".$campo[2]."</td>";
                echo "<td>".$campo[3]."</td>";
                echo "<td>".$campo[4]."</td>";
                #echo "<td>".$campo[5]."</td>";
                $novaCidade = encontraCidade($campo[4]);
                $classe = NULL;
                if(is_null($novaCidade)){
                    # Trata para encontrar a cidade
                    switch ($campo[4]){
                     case "atafona - são joão da barra":
                         $novaCidade = 3664;
                         break;
                     
                     case "b.j. itabapoana":
                     case "bom jesus de itabapoana":  
                     case "bom jesus do itababoana":    
                         $novaCidade = 3601;
                         break;
                     
                     case "buzios":
                         $novaCidade = 3595;
                         break;
                     
                     case "cachoeiro de macacu":
                         $novaCidade = 3603;
                         break;
                     
                     case "campos":
                     case "campos dos goitacazes":
                     case "campos dos goytacaazes": 
                     case "campos dos goytacaze":   
                     case "campos dos goytacazes-rj":
                     case "campos dos goytavazes":
                     case "campos dos goytazes":
                     case "campos dos goytazes":
                     case "camposd dos goytacazes":    
                         $novaCidade = 3605;
                         break;
                     
                     case "macae":
                         $novaCidade = 3627;
                         break;
                     
                     case "ilha do governador":
                         $novaCidade = 3658;
                         break;
                     
                     case "barra de sao joao":
                         $novaCidade = 3610;
                         break;
                     
                     case "sao francisco do itabapoana":
                         $novaCidade = 3662;
                         break;
                     
                     case "itaperuna - rj":
                         $novaCidade = 3623;
                         break;
                     
                     case "macaé-rj":
                         $novaCidade = 3627;
                         break;
                     
                     case "saop joao da barra":
                         $novaCidade = 3664;
                         break;
                     
                      case "sto antonio de padua":
                      case "sto. antônio de pádua":    
                         $novaCidade = 3660;
                         break;
                     
                     default :
                         $novaCidade = NULL;
                    }
                }
                
                echo "<td>".$novaCidade."</td>";                
                echo "<td>".$campo[6]."</td>";
               
                # Grava na tabela
                $campos = array("idCidade","endereco");
                $valor = array($novaCidade,ltrim($novoEndereco));
                $tabela = "tbpessoa";
                $idCampo = "idPessoa";
                
                $pessoal->gravar($campos,$valor,$campo[0],$tabela,$idCampo,FALSE);
                              
                echo "</tr>";
                $item++;
            }

            echo "</table>";
            
            

            $painel->fecha();
            break;
            
        case "analisa" :
            # Conecta ao banco
            $pessoal = new Pessoal();
            botaoVoltar("?");
            titulo('Registros não importador (com idCidade NULL)');
            
            # select
            $select = "SELECT tbpessoa.nome,
                              cidade,
                              tbcidade.nome,
                              uf
                        FROM tbpessoa LEFT JOIN tbcidade USING (idCidade)
                        WHERE tbpessoa.idCidade IS NULL
                    ORDER BY cidade";
            $conteudo = $pessoal->select($select,TRUE);
            
            # Cabeçalho da tabela
            #$titulo = 'Servidores com licença terminando em '.date('Y');
            $label = array('Nome','Cidade Antiga','Cidade Importada','UF');
            $align = array('left','left');
            $linkEditar = 'servidor.php?fase=editar&id=';

            # Exibe a tabela
            $tabela = new Tabela();
            $tabela->set_conteudo($conteudo);
            $tabela->set_label($label);
            $tabela->set_align($align);
            #$tabela->set_titulo($titulo);
            #$tabela->set_editar($linkEditar);
            #$tabela->set_idCampo('idServidor');
            $tabela->show();
            break;
        
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}