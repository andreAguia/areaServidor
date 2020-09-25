<?php

class Log {

    function getAtividades($idLog) {

        # Pega os dados
        $select = "SELECT atividade
                     FROM tblog
                    WHERE idLog = {$idLog}";
        
        $intra = new Intra();
        $dados = $intra->select($select, false);

        #$retorno = $dados["atividade"];
        $retorno = str_replace(["["], ["<br>["], $dados[0]);

        return $retorno;
    }
}
