<?php
/**
 * Rotina de Importação da CArteira de identidade
 * 
 * Rotina que terá a tarefa de consertar erro na primeira importação que 
 * truncou o campo de identidade em 10 caractreres. 
 * Agora faremos uma nova importação com limite de 30 caracteres.
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
           
            loadPage('?fase=exibe');
            break;
        
        case"exibe" :
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
            $linkBotao2->set_title('Refazer a Simulação');
            $linkBotao2->set_accessKey('R');
            $menu->add_link($linkBotao2,"right");
            
            # Importar
            $linkBotao2 = new Link("Importar","?fase=importa");
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Faz a Importação');
            $linkBotao2->set_accessKey('I');
            $menu->add_link($linkBotao2,"right");
            $menu->show();
            
            # Conecta ao banco
            $uenf = new Uenf();
            $pessoal = new Pessoal();
            
            # select
            $select = "SELECT uenf2.fen001.matr,"
                    . "       uenf2.fen001.id,"
                    . "       grh.tbservidor.matricula,"
                    . "       grh.tbpessoa.nome,"
                    . "       grh.tbdocumentacao.identidade"
                    . "  FROM uenf2.fen001 LEFT JOIN grh.tbservidor ON (uenf2.fen001.matr = grh.tbservidor.matricula)"
                    . "                    LEFT JOIN grh.tbpessoa USING (idPessoa)"
                    . "                    LEFT JOIN grh.tbdocumentacao USING (idPessoa)"
                    . "ORDER BY nome,dt_adm";
            $conteudo = $uenf->select($select);

            titulo('Importação das identidades');

            # Cria um painel
            $painel = new Callout();
            $painel->abre();
            
            # Exibe a tabela
            echo "<table class='tabelaPadrao'>";
            echo "<tr>";
            echo "<th>#</th>";
            echo "<th>Matrícula</th>";
            echo "<th>Identidade</th>";
            echo "<th>Identidade Cadastrada</th>";
            echo "<th>Matrícula</th>";
            echo "<th>Nome</th>";
            echo "<th>Importar ?</th>";
            echo "<th>Motivo</th>";
            echo "</tr>";
            
            # Contadores
            $contTotal = 0;
            $contSim = 0;
            $contNao = 0;
            $contValidos = 0;
            
            # Percorre a tabela
            foreach ($conteudo as $campo){
                # Passa para as variáveis
                $matricula = $campo[0];
                $identidade = $campo[1];
                $matricula2 = $campo[2];
                $nome = $campo[3];
                $identidade2 = $campo[4];
                
                # Faz a análise
                $tamanho1 = strlen($identidade);
                $tamanho2 = strlen($identidade2);
                $importar = "";
                $motivo = "";
                
                
                
                if(!is_null($nome)){
                    $contValidos++;
                
                    # Exibe os dados
                    echo "<tr>";
                    echo "<td>".$contValidos."</td>";
                    echo "<td>".$matricula."</td>";
                    echo "<td>".$identidade."</td>";
                    echo "<td>".$identidade2."</td>";
                    echo "<td>".$matricula2."</td>";
                    echo "<td>".$nome."</td>";
                
                    if($tamanho1 == 0){
                        $importar = "NÃO";
                        $motivo = "Identidade em branco";
                        echo "<td><label class='alert label'>NÃO</span></td>";
                        echo "<td>".$motivo."</td>";
                        $contNao++;
                    }elseif($identidade <> $identidade2){
                        $importar = "SIM";
                        $motivo = "Números Diferentes"; 
                        echo "<td><label class='success label'>SIM</span></td>";
                        echo "<td>".$motivo."</td>";
                        $contSim++;
                    }elseif($identidade == $identidade2){
                        $motivo = "Números Iguais";
                        echo "<td><label class='alert label'>NÃO</span></td>";
                        echo "<td>".$motivo."</td>";
                        $contNao++;
                    }
                    echo "</tr>";
                }else{
                    $importar = "NÃO";
                    $motivo = "Servidor não encontrado";
                    $contNao++;
                }
                $contTotal++;
            }
            echo "</table>";
            
            br();
            echo $contNao." Identidades rejeitadas.";br();
            echo $contSim." Identidades Aprovadas.";br();
            echo "-------------------------------------";br();
            echo $contTotal." Identidades analisadas no total.";
            
            $painel->fecha();
            
            break;
            
        case"importa" :            
       
        break;
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}