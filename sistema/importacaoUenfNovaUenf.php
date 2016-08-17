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

if($acesso){
    
    # cria um cronômetro a ser exibido na tela
    $script = '<script>
        function formatatempo(segs) {
        min = 0;
        hr = 0;
        /*
        if hr < 10 then hr = "0"&hr
        if min < 10 then min = "0"&min
        if segs < 10 then segs = "0"&segs
        */
        while(segs>=60) {
        if (segs >=60) {
        segs = segs-60;
        min = min+1;
        }
        }

        while(min>=60) {
        if (min >=60) {
        min = min-60;
        hr = hr+1;
        }
        }

        if (hr < 10) {hr = "0"+hr}
        if (min < 10) {min = "0"+min}
        if (segs < 10) {segs = "0"+segs}
        fin = hr+":"+min+":"+segs
        return fin;
        }
        var segundos = 0; //inicio do cronometro
        function conta() {
        segundos++;
        document.getElementById("counter").innerHTML = formatatempo(segundos);
        }

        function inicia(){
        interval = setInterval("conta();",1000);
        }

        function para(){
        clearInterval(interval);
        }

        function zera(){
        clearInterval(interval);
        segundos = 0;
        document.getElementById("counter").innerHTML = formatatempo(segundos);
        }
        </script>';

    # Começa uma nova página
    $page = new Page();
    $page->set_jscript($script);
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

    titulo('Importação do Banco de Dados Antigo da UENF para o Novo');
    titulo('<span id=\'counter\'>00:00:00</span>');
    
    # Verifica a fase do programa
    $fase = get('fase');

    switch ($fase)
    {
        case "" :
            br(4);
            mensagemAguarde();
            br();
            echo '<script>inicia();</script>';

            loadPage('?fase=importa');
            break;

        case"importa" :
            # Cria um painel
            $painel = new Callout();
            $painel->abre();
            
            # Conecta ao banco
            $uenf = new Uenf();
            $pessoal = new Pessoal();
                    
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
            $numItens = 0;              // Número de itens importados
            $numItensDescartados = 0;   // Número de itens descartados            
            
            # Inicia a Importação
            $select = "SELECT matr,nm,email,sit,"
                    . "tp_rua,rua,compl,bai,cep,cid,uf,"
                    . "sexo,eciv,"
                    . "id,orgao_id,emi_id,cp,ser_cp,uf_cp,cart_habil,titulo,zona,secao,reservista,"
                    . "fen001.natural,nacion,dir,ger,"
                    . "categ_res,pais,ano_cheg,"
                    . "perfil,cpf,pis,dt_adm,dt_nasc,obs,dt_dem,"
                    . "matr_est,causa_dem FROM fen001 where (vinc <> 9 or (vinc = 9 and perfil = 21)) and cpf is not null";

            $conteudo = $uenf->select($select,true);
            
            echo "<table class='tabelaPadrao'>";
            echo "<tr>";
            echo "<th>Matrícula</th>";
            echo "<th>Cpf</th>";
            echo "<th>IdPessoa Existente</th>";
            echo "<th>idPessoa Novo</th>";
            echo "<tr>";
            
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
                    # Variáveis
                    $jaExistePessoa = FALSE;    // Informa se já existe alguma pessoa com esse cpf
                    
                    # Regra para o endereço (verifica se o campo 4 5 e 6 estão vazio juntos)
                    $endereco = NULL;
                    if((empty($campo[4])) AND (empty($campo[5])) AND (empty($campo[6]))){
                        $endereco = NULL;
                    }else{
                        $endereco = $campo[4].' '.$campo[5].' '.$campo[6];
                    }
                    
                    # Regra para o campo sexo
                    $sexo = NULL;
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
                    
                    # Regra para Lotação
                    $dir = strtolower($campo[26]);
                    $ger = strtolower($campo[27]);
                    
                    switch ($dir){
                        case "cbb":
                            switch ($ger){
                                case "secr":
                                    $idLotacao = 69;
                                    break;                                
                                case "bibli":
                                    $idLotacao = 70;
                                    break;                                
                                case "biot":
                                    $idLotacao = 71;
                                    break;
                                case "lbct":
                                    $idLotacao = 72;
                                    break;
                                case "lbr":
                                    $idLotacao = 73;
                                    break;
                                case "lca":
                                    $idLotacao = 75;
                                    break;
                                case "lbt":
                                    $idLotacao = 74;
                                    break;
                                case "lfbm":
                                    $idLotacao = 76;
                                    break;
                                case "lqfpp":
                                    $idLotacao = 77;
                                    break;
                                case "lca":
                                    $idLotacao = 75;
                                    break;
                                default:
                                    $idLotacao = 69;
                                    break;
                            }
                            break;
                        case "cch":
                            switch ($ger){
                                case "secr":
                                    $idLotacao = 78;
                                    break;                                
                                case "bibli":
                                    $idLotacao = 79;
                                    break;                                
                                case "lcl":
                                    $idLotacao = 80;
                                    break;
                                case "leea":
                                    $idLotacao = 82;
                                    break;
                                case "leel":
                                    $idLotacao = 81;
                                    break;
                                case "lesce":
                                    $idLotacao = 83;
                                    break;
                                case "lgpp":
                                    $idLotacao = 84;
                                    break;
                                default:
                                    $idLotacao = 78;
                                    break;
                            }
                            break;
                        case "cct":
                            switch ($ger){   
                                case "secr":
                                    $idLotacao = 85;
                                    break;                                
                                case "bibli":
                                    $idLotacao = 86;
                                    break;                                
                                case "lamav":
                                    $idLotacao = 93;
                                    break;
                                case "lamet":
                                    $idLotacao = 94;
                                    break;
                                case "lcfis":
                                    $idLotacao = 87;
                                    break;
                                case "lcmat":
                                    $idLotacao = 88;
                                    break;
                                case "lcqui":
                                    $idLotacao = 89;
                                    break;
                                case "leciv":
                                    $idLotacao = 91;
                                    break;
                                case "lenep":
                                    $idLotacao = 90;
                                    break;
                                case "leprod":
                                    $idLotacao = 92;
                                    break;
                                default:
                                    $idLotacao = 85;
                                    break;
                            }
                            break;
                        case "ccta":
                            switch ($ger){
                                case "secr":
                                    $idLotacao = 95;
                                    break;                                
                                case "bibli":
                                    $idLotacao = 96;
                                    break;                                
                                case "lcca":
                                    $idLotacao = 107;
                                    break;
                                case "leag":
                                    $idLotacao = 97;
                                    break;
                                case "lef":
                                    $idLotacao = 101;
                                    break;
                                case "lfit":
                                    $idLotacao = 98;
                                    break;
                                case "lmgv":
                                    $idLotacao = 100;
                                    break;
                                case "lmpa":
                                    $idLotacao = 106;
                                    break;
                                case "lrmga":
                                    $idLotacao = 99;
                                    break;
                                case "lsa":
                                    $idLotacao = 102;
                                    break;
                                case "lsol":
                                    $idLotacao = 103;
                                    break;
                                case "lta":
                                    $idLotacao = 104;
                                    break;
                                case "lzna":
                                    $idLotacao = 105;
                                    break;
                                default:
                                    $idLotacao = 95;
                                    break;
                            }
                            break;
                        case "dga":
                            switch ($ger){
                                case "secr":
                                    $idLotacao = 62;
                                    break;
                                case "gcom":
                                    $idLotacao = 63;
                                    break;
                                case "gpaf":
                                    $idLotacao = 65;
                                    break;
                                case "gpat":
                                    $idLotacao = 64;
                                    break;
                                case "grh":
                                    $idLotacao = 66;
                                    break;
                                default:
                                    $idLotacao = 62;
                                    break;
                            }
                            break;
                        case "dic":
                            switch ($ger){
                                case "ascom":
                                    $idLotacao = 109;
                                    break;
                            }
                            break;
                        case "dispos":
                            $idLotacao = 113;
                            break;
                        case "e.cienc":
                            $idLotacao = 52;
                            break;
                        case "prefeit":
                            switch ($ger){
                                case "secr":
                                    $idLotacao = 112;
                                    break;
                                case "asman":
                                    $idLotacao = 59;
                                    break;
                                case "astran":
                                    $idLotacao = 61;
                                    break;
                                case "gpeng":
                                    $idLotacao = 60;
                                    break;
                                default:
                                    $idLotacao = 112;
                                    break;
                            }
                            break;
                        case "proex":
                            $idLotacao = 56;
                            break;
                        case "prograd":
                            $idLotacao = 57;
                            break;
                        case "proppg":
                        case "propos":    
                            $idLotacao = 58;
                            break;
                        case "reit":
                            switch ($ger){
                                case "cgab":
                                    $idLotacao = 48;
                                    break;
                                case "aginova":
                                    $idLotacao = 114;
                                    break;
                                case "ascom":
                                    $idLotacao = 109;
                                    break;
                                case "asjur":
                                    $idLotacao = 49;
                                    break;
                                case "audit":
                                    $idLotacao = 50;
                                    break;
                                case "ca":
                                case "coord":
                                case "secacad":
                                    $idLotacao = 54;
                                    break;
                                case "E.CIEN.":
                                    $idLotacao = 52;
                                    break;
                                case "grc":
                                    $idLotacao = 108;
                                    break;
                                case "hospvet":
                                    $idLotacao = 55;
                                    break;
                                case "prot":
                                    $idLotacao = 53;
                                    break;
                                case "villa":
                                case "vmaria":
                                    $idLotacao = 51;
                                    break;
                                default:
                                    $idLotacao = 48;
                                    break;
                            }
                            break;
                        default:
                            $idLotacao = 110;
                            break;  
                    }
                    
                    # Perfil
                    $perfil = $campo[31];
                    $idPerfil = NULL;
                    
                    switch ($perfil){
                        case 17:
                        case 18:
                            $idPerfil = 1;
                            break;
                        case 10:
                        case 20:
                            $idPerfil = 2;
                            break;
                        case 16:
                            $idPerfil = 3;
                            break;
                        case 1:
                        case 2:
                            $idPerfil = 4;
                            break;
                        case 23:
                        case 22:
                            $idPerfil = 5;
                            break;
                        case 25:
                        case 24:
                            $idPerfil = 6;
                            break;
                        case 21:
                            $idPerfil = 7;
                            break;
                        default :
                            $idPerfil = 8;
                            break;
                    }
                    
                    # Campo obs (o odiado campo vermelho !!!)
                    $obs = htmlentities($campo[36],ENT_QUOTES,'UTF-8');
                    
                    # CPF
                    $cpf = soNumeros($campo[32]);
                    $cpf = trim(substr($cpf,0,3).'.'.substr($cpf,3,3).'.'.substr($cpf,6,3).'-'.substr($cpf,9,2));
                    
                    # Verifica se esse cpf já existe na tbpessoa
                    $idPessoaExistente = $pessoal->get_idPessoaCPF($cpf);
                    if(empty($idPessoaExistente)){
                        $jaExistePessoa = FALSE;
                    }else{
                        $jaExistePessoa = TRUE;
                    }
                    
                    echo "<tr>";
                    echo "<td>".$campo[0]."</td>";
                    echo "<td>".$cpf."</td>";
                    echo "<td>".$idPessoaExistente."</td>";
                    
                    # tbpessoa
                    $nome = ucwords(strtolower($campo[1]));
                    $tabela = 'tbpessoa';
                    $idCampo = 'idPessoa';
                    $campos = array("nome","endereco","bairro","cep","cidade","uf","sexo","estCiv","naturalidade","nacionalidade","paisOrigem","anoChegada","dtNasc");
                    $valor = array($nome,$endereco,$campo[7],$campo[8],$campo[9],$campo[10],$sexo,$estadoCivil,$campo[24],$nacionalidade,$campo[29],$campo[30],$campo[35]);
                    
                    # Se não existe alguem com esse CPF 
                    if($jaExistePessoa){
                        $idPessoa = $idPessoaExistente;
                        echo "<td></td>";
                    }else{
                        $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                        $idPessoa = $pessoal->get_lastId();
                        echo "<td>".$idPessoa."</td>";
                    }
                    
                    # regra para a situação e a causa de demissão (uma coisa depende da outra)
                    $motivoDemissao = $campo[39];   // Motivo da demissão
                    $situacao = $campo[3];          // Situação  
                    
                    if($situacao == 1){
                        $motivoDemissao = null;
                    }else{
                        if($perfil == 2){   // Verifica se é cedido
                            switch ($motivoDemissao){
                                case 60:
                                    $situacao = 5;          // -- Falecido
                                    $motivoDemissao = 2;    // -- Falecimento
                                    break;
                                default:
                                    $situacao = 6;          // -- Fim de Cessão
                                    $motivoDemissao = 14;   // -- Fim de Cessão
                                    break;
                            }
                        }else{
                        switch ($motivoDemissao){
                            case 0:                 // Valor 0 ?! tentar identificar pela situação   
                                switch ($situacao){
                                    case 3:
                                    case 7:
                                    case 9:
                                    case 34:
                                    case 36:
                                        $situacao = 4;          // -- Demitido
                                        $motivoDemissao = 11;   // -- Demissão sem justa causa
                                        break;
                                    
                                    case 53:                    // Pedido de Exoneração
                                        $situacao = 3;          // -- Exonerado
                                        $motivoDemissao = 1;    // -- Exonerado a pedido
                                        break;                                
                                }
                                break;
                            
                            case 1:                     // Duplicidade de Bolsa (não se aplica)
                                $situacao = 4;          // -- Demitido
                                $motivoDemissao = 11;   // -- Demissão sem justa causa
                                break;
                            
                            case 2:                     // Não comparecimento ao setor (abandono de emprego)
                                $situacao = 4;          // -- Demitido
                                $motivoDemissao = 12;   // -- Abandono de Emprego
                                break;
                            
                            case 4:                     // Término de Bolsa (aplca-se somente ao prof visitante)
                                $situacao = 4;          // -- Demitido
                                $motivoDemissao = 9;    // -- Término do Contrato
                                break;
                            
                            case 10:                 // Demissão com justa causa
                            case 20:    
                                $situacao = 4;       // -- Demitido
                                $motivoDemissao = 10;// -- Demissão com justa causa
                                break;
                            
                            case 11:                    // Demissão sem justa causa
                            case 21:    
                                $situacao = 4;          // -- Demitido
                                $motivoDemissao = 11;   // -- Demissão sem justa causa
                                break;
                            
                            case 13:                    // Término do contrato de trabalho
                                $situacao = 4;          // -- Demitido
                                $motivoDemissao = 9;    // -- Término do contrato
                                break;
                            
                            case 30:                    // Transferência com ônus (??)
                            case 31:                    // Transferência sem ônus (??)
                                $situacao = 4;          // -- Demitido
                                $motivoDemissao = 13;   // -- Outros
                                break;
                            
                            case 40:                    // Mudança de regime (??) São 2 prof. visitantes
                                $situacao = 4;          // -- Demitido
                                $motivoDemissao = 13;   // -- Outros
                                break;
                            
                            case 60:                    // Falecimento
                                $situacao = 5;          // -- Falecido
                                $motivoDemissao = 2;    // -- Falecimento
                                break;
                            
                            case 69:                    // Não existe esse tipo de demissão mas existe 15 pessoas com essa causa.Todas sit 38 (APOSENTADORIA INTEGRAL VOLUNTARIA) 
                                $situacao = 2;          // -- Aposentado (inativo) 
                                $motivoDemissao = 3;    // -- Aposentadoria Voluntária Integral
                                break;
                            
                            case 70:                    // Aposentadoria Voluntária Integral
                            case 71:    
                                $situacao = 2;          // -- Aposentado (inativo) 
                                $motivoDemissao = 3;    // -- Aposentadoria Voluntária Integral
                                break;
                            
                            case 72:                    // Aposentadoria Voluntária Proporcional
                                $situacao = 2;          // -- Aposentado (inativo) 
                                $motivoDemissao = 4;    // -- Aposentadoria Voluntária Proporcional
                                break;
                            
                            case 73:                    // Aposentadoria por Invalidez - Acidente de Trabalho
                                $situacao = 2;          // -- Aposentado (inativo) 
                                
                                break;
                            
                            case 74:                    // Aposentadoria por Invalidez - Doença Profissional
                                $situacao = 2;          // -- Aposentado (inativo) 
                                
                                break;
                            
                            case 74:                   // Não existe esse tipo de demissão (8 pessoas (3 - sit 38 e 5 sit 41)
                                if($situacao == 38){
                                    $situacao = 2;      // -- Aposentado (inativo)
                                    $motivoDemissao = 3;// -- Aposentadoria Voluntária Integral
                                }
                                
                                if($situacao == 41){
                                    $situacao = 2;      // -- Aposentado (inativo)
                                    $motivoDemissao = 4;// -- Aposentadoria Voluntária Proporcional
                                }
                                break;
                                
                            case 75:                    // Exonerado a pedido
                                $situacao = 3;          // -- exonerado
                                $motivoDemissao = 1;    // -- exonerado a pedido
                                break;
                            
                            case 88:                    // Rescisão do contrato de trabalho
                                $situacao = 4;          // -- Demitido
                                $motivoDemissao = 8;    // -- Rescisão de contrato à pedido
                                break;
                            
                            case 90:                    // Outros
                                $situacao = 4;          // -- Demitido
                                $motivoDemissao = 13;   // -- Outros
                                break;
                            
                        }
                        }
                    }
                    
                    # tbservidor
                    $tabela = 'tbservidor';
                    $idCampo = 'idServidor';
                    $campos = array("matricula","idPessoa","situacao","idPerfil","dtAdmissao","obs","dtDemissao","idFuncional");
                    $valor = array($campo[0],$idPessoa,$campo[3],$idPerfil,$campo[34],$obs,$campo[37],$campo[38]);                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    $idServidor = $pessoal->get_lastId();
                    
                    # tbcontatos
                    $email = $campo[2];
                        if(!empty($email)){
                        $tabela = 'tbcontatos';
                        $idCampo = 'idContatos';
                        $campos = array("idPessoa","tipo","numero");
                        $valor = array($idPessoa,"E-mail",$email);                    
                        $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    }
                    
                    # tbdocumentacao
                    $tabela = 'tbdocumentacao';
                    $idCampo = 'idDocumentacao';
                    $campos = array("idPessoa","identidade","orgaoId","dtId","cp","serieCp","ufCp","motorista","titulo","zona","secao","reservista","reservistaCateg","cpf","pisPasep");
                    $valor = array($idPessoa,$campo[13],$campo[14],$campo[15],$campo[16],$campo[17],$campo[18],$campo[19],$campo[20],$campo[21],$campo[22],$campo[23],$campo[28],$cpf,$campo[33]);                    
                    if(!$jaExistePessoa){
                        $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    }
                                                           
                    # tbhistlot
                    $data = date('Y-m-d');
                    $tabela = 'tbhistlot';
                    $idCampo = 'idHistLot';
                    $campos = array("idServidor","data","lotacao","motivo");
                    $valor = array($idServidor,$data,$idLotacao,"Importado do sistema antigo");                    
                    $pessoal->gravar($campos,$valor,NULL,$tabela,$idCampo,FALSE);
                    
                    $numItens ++;
                }
            }
            
            echo "</table>";
            
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