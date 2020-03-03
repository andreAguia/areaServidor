<?php
class ManualProcedimento{
 /**
  * Abriga as várias rotina do Sistema de Manual de Procedimentos
  *
  * @author André Águia (Alat) - alataguia@gmail.com
  *
  */

   ###########################################################

    function get_dados($idManualProcedimento){

    /**
     * Informe o número do processo de solicitação de redução de carga horária de um servidor
     */
        # Conecta ao Banco de Dados
        $intra = new Intra();
        
        # Verifica se o id foi informado
        if(vazio($idManualProcedimento)){
            alert("É necessário informar o id.");
            return;
        }

        # Pega os dados
        $select = 'SELECT * 
                     FROM tbmanualprocedimento
                    WHERE idManualProcedimento = '.$idManualProcedimento;

       $row = $intra->select($select,FALSE);

        # Retorno
        return $row;
    }

    ###########################################################

    /**
     * Método exibeDadosVaga
     * fornece os dados de uma vaga em forma de tabela
     * 
     * @param	string $idVaga O id da vaga
     */

    function exibeDadosProcedimento($idManualProcedimento){ 
        
        # Conecta com o banco de dados
        $intra = new Intra();
        
        $conteudo = $this->get_dados($idManualProcedimento);
        
        $painel = new Callout("primary");
        $painel->abre();
        
        $btnEditar = new Link("Editar","manualProcedimento.php?fase=editar&id=$idManualProcedimento");
        $btnEditar->set_class('button tiny secondary');
        $btnEditar->set_id('editarVaga');
        $btnEditar->set_title('Editar o Procedimento');
        $btnEditar->show();
        
        $categoria = $conteudo["categoria"];
        $titulo = $conteudo["titulo"];
        $descricao = $conteudo["descricao"];;        
        
        p($idManualProcedimento,"vagaId");
        p($categoria,"vagaCentro");
        p($titulo,"vagaCargo");
        p($descricao,"vagaCargo");
        
        $painel->fecha();        
    }

    ###########################################################
    
}
