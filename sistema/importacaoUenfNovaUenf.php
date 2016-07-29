<?php
/**
 * Rotina de Importação
 *  
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

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Cria um menu
    $menu = new MenuBar();

    # Botão voltar
    $linkBotao1 = new Link("Voltar",'administracao.php');
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

    titulo("Importação do Banco de Dados Antigo da UENF para o Novo");

    # Verifica a fase do programa
    $fase = get('fase');

    switch ($fase)
    {
        case "" :
            br(4);
            mensagemAguarde();
            loadPage('?fase=importa');
            break;

        case"importa" :
            $painel = new Callout();
            $painel->abre();
            
            # Conecta ao banco
            $uenf = new Uenf();
                    
            # Pega a quantidade de registros com os bolsistas
            $select = "select nm from fen001";
            $result = $uenf->select($select);
            $totalRegistros = count($result);            
            echo $totalRegistros." Registros incluindo os bolsistas;";
            br();
            
            # Pega a quantidade de registros sem os bolsistas
            $select = "select nm from fen001 where vinc<>9 order by matr";
            $result = $uenf->select($select);
            $totalRegistros = count($result);
            echo $totalRegistros." Registros sem os bolsistas;";
            br(2);
            
            # Variáveis
            $numItens = 0;            // Número de itens importados
            $numItensDescartados = 0; // Número de itens descartados            
            
            # Inicia a Importação
            $select = "SELECT matr,nm,email,sit,"
                    . "tp_rua,rua,compl,bai,cep,cid,uf,"
                    . "sexo,eciv,"
                    . "id,orgao_id,emi_id,cp,ser_cp,uf_cp,cart_habil,titulo,zona,secao,reservista,"
                    . "fen001.natural,nacion FROM fen001 WHERE vinc <> 9";
            
            $conteudo = $uenf->select($select,true);
            
            # Percorre o Fen001 e monta o sql
            foreach ($conteudo as $campo){
                # Verifica se matrícula está vazia
                if((empty($campo[0])) OR (empty($campo[1]))){
                    $numItensDescartados++;
                    echo "1 item descartado...";
                    br();
                    echo "------------------------";
                    br();
                }else{
                    # Verifica se está vazio os campos para colocar o texto NULL
                    for ($i = 1; $i < 25; $i++) {
                        if(($i == 4) OR ($i == 5) OR ($i == 6)){
                            continue;
                        }elseif(empty($campo[$i])){
                            $campo[$i] = 'NULL';
                        }
                    }
                    
                    # Regra para o endereço (verifica se o campo 4 5 e 6 estão vazio juntos)
                    $endereco = NULL;
                    if((empty($campo[4])) AND  (empty($campo[5])) AND (empty($campo[6]))){
                        $endereco = 'NULL';
                    }else{
                        $endereco = $campo[4].' '.$campo[5].' '.$campo[6];
                    }
                    
                    # Regra para o campo sexo
                    $sexo = 'NULL';
                    if($campo[11] == 1){
                        $sexo = "Masculino";
                    }elseif($campo[11] == 2){
                        $sexo = "Feminino";
                    }
                    
                    # Regra para o campo eciv
                    $estadoCivil = NULL;
                    switch ($campo[12]){
                        case 3:
                            $estadoCivil = 5;
                            break;
                        case 4:
                            $estadoCivil = 3;
                            break;
                        case 5:
                        case 0:    
                            $estadoCivil = 9;
                            break;
                        case 6:
                            $estadoCivil = 8;
                            break;
                        default :
                            $estadoCivil = $campo[12];
                    }
                    
                    # Regra para o campo nacional (nacionalidade)
                    $nacionalidade = NULL;
                    switch ($campo[25]){
                        case 10:    // brasileiro
                            $nacionalidade = 1;
                            break;
                        case 20:    // naturalizado
                            $nacionalidade = 2;
                            break;                        
                        case 21:    // argentino
                            $nacionalidade = 4;
                            break;
                        case 22:    // boliviano
                            $nacionalidade = 6;
                            break;
                        case 23:    // chileno
                            $nacionalidade = 9;
                            break;
                        case 24:
                            $nacionalidade = 19;
                            break;
                        case 25:
                            $nacionalidade = 22;
                            break;
                        case 26:
                            $nacionalidade = 24;
                            break;
                        case 30:
                            $nacionalidade = 3;
                            break;
                        case 31:
                            $nacionalidade = 5;
                            break;
                        case 32:
                            $nacionalidade = 7;
                            break;
                        case 34:
                            $nacionalidade = 8;
                            break;
                        case 35:
                            $nacionalidade = 13;
                            break;
                        case 36:
                            $nacionalidade = 18;
                            break;
                        case 37:
                            $nacionalidade = 14;
                            break;
                        case 38:
                            $nacionalidade = 21;
                            break;
                        case 39:
                            $nacionalidade = 16;
                            break;
                        case 41:
                            $nacionalidade = 17;
                            break;
                        case 42:
                            $nacionalidade = 10;
                            break;
                        case 43:
                            $nacionalidade = 12;
                            break;
                        case 45:
                            $nacionalidade = 20;
                            break;
                        case 48:
                        case 49:
                        case 50:    
                            $nacionalidade = 25;
                            break;
                        case 53:
                            $nacionalidade = 11;
                            break;
                        default:
                            $nacionalidade = 25;
                            break;
                    }
                                        
                    # tbpessoa
                    $campos = array("nome","endereco","bairro","cep","cidade","uf","sexo","estCiv","naturalidade","nacionalidade");
                    $valor = array($campo[1],$endereco,$campo[7],$campo[8],$campo[9],$campo[10],$sexo,$estadoCivil,$campo[24],$nacionalidade);
                    
                    # Grava na tabela tbpessoa
                    $uenf->set_tabela('tbpessoa');
                    $uenf->set_idCampo('idPessoa');
                    $uenf->gravar($campos,$valor,$id);
                    
                    # regra para a situação
                    switch ($campo[3]){
                        case 9:         // Demitido
                        case 38:        // Aposentadoria Integral Voluntária
                        case 39:        // Aposentadoria Integral por Invalidez
                        case 40:        // Aposentadoria Integral Compulsória
                        case 41:        // Aposentadoria Proporcional Voluntária   
                        case 42:        // Aposentadoria Proporcional por Invalidez
                        case 43:        // Aposentadoria Proporcional Compulsória
                        case 54:        // Falecimento
                        case 55:        // Recisão de Contrato
                        case 57:        // Suspensão de Contrato
                            $campo[3] = 2;
                            break;
                            
                        default :
                            $campo[3] = 1;
                    }
                    
                    # tbservidor                    
                    $tbservidor = 'INSERT INTO tbservidor (matricula,idPessoa,situacao) values ('.$campo[0].',idPessoa,'.$campo[3].')';
                    
                    # tbcontatos                    
                    $tbcontatos = 'INSERT INTO tbcontatos (idPessoa,tipo,numero) values (idPessoa,E-mail,'.$campo[2].')';
                    
                    # tbdocumentacao                    
                    $tbcontatos = 'INSERT INTO tbdocumentacao (idPessoa,identidade,orgaoId,dtId,cp,serieCp,ufCp,motorista,titulo,zona,secao,reservista)'
                                                   . ' values (idPessoa,'.$campo[13].','.$campo[14].','.$campo[15].','.$campo[16].','.$campo[17].','.$campo[18].','.$campo[19].','.$campo[20].','.$campo[21].','.$campo[22].','.$campo[23].')';
                    
                    echo $tbpessoa;
                    br();
                    echo $tbservidor;
                    br();
                    echo $tbcontatos;
                    br();
                    echo "------------------------";
                    br();
                    $numItens ++;
                }
            }
            # Exibe o número de itens importado
            echo $numItens." registros importados";
            br();
            echo $numItensDescartados." registros descartados";
    
            $painel->fecha();
            break;
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}