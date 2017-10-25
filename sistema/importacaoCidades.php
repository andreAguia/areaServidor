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

    switch ($fase)
    {
        case "" :
            br(4);
            aguarde();
            br();    
           
            loadPage('?fase=importa');
            break;

        case "importa" :            
            # Cria um menu
            $menu = new MenuBar();

            # Botão voltar
            $linkBotao1 = new Link("Voltar",'administracao.php?fase=importacao');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1,"left");

            # Refazer
            $linkBotao2 = new Link("Refazer","?");
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('R');
            $menu->add_link($linkBotao2,"right");
            $menu->show();

            # Conecta ao banco
            $pessoal = new Pessoal();

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
                              cidade,
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
            
        case "final" :
            # Conecta ao banco
            $pessoal = new Pessoal();
            
            titulo('Registros não importador (com idCidade NULL)');
            
            # select
            $select = "SELECT tbpessoa.nome,
                              cidade,
                              tbcidade.nome,
                              uf,
                              idServidor
                        FROM tbpessoa LEFT JOIN tbcidade USING (idCidade)
                                      JOIN tbservidor USING (idPessoa)
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
            $tabela->set_editar($linkEditar);
            $tabela->set_idCampo('idServidor');
            $tabela->show();
            break;
        
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}