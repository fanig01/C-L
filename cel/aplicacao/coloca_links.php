<?php

function load_ArrayLexicon($idProject , $idCurrentLexicon, $noCurrent) {
    
    $lexiconVector = array();
    
    if ($noCurrent) {
    
        $queryLexicos = "SELECT id_lexico, nome    
			 FROM lexico    
			 WHERE id_projeto = '$idProject ' AND id_lexico <> '$idCurrentLexicon' 
			 ORDER BY nome DESC";

        $querySinonimos = "SELECT id_lexico, nome 
			   FROM sinonimo
			   WHERE id_projeto = '$idProject ' AND id_lexico <> '$idCurrentLexicon' 
			   ORDER BY nome DESC";
    }
    else {

        $queryLexicos = "SELECT id_lexico, nome    
			FROM lexico    
			WHERE id_projeto = '$idProject ' 
			ORDER BY nome DESC";

        $querySinonimos = "SELECT id_lexico, nome    
			   FROM sinonimo
			   WHERE id_projeto = '$idProject ' ORDER BY nome DESC";
    }

    $resultadoQueryLexicos = mysql_query($queryLexicos) or die("Erro ao enviar a query de selecao na tabela lexicos !" . mysql_error());

    $index = 0;
    
    while ($linhaLexico = mysql_fetch_object($resultadoQueryLexicos)) {
        
        $lexiconVector[$index] = $linhaLexico;
        $index++;
    }

    $resultadoQuerySinonimos = mysql_query($querySinonimos) or die("Erro ao enviar a query de selecao na tabela sinonimos !" . mysql_error());
    
    while ($linhaSinonimo = mysql_fetch_object($resultadoQuerySinonimos)) {
    
        $lexiconVector[$index] = $linhaSinonimo;
        $index++;
    }
    
    return $lexiconVector;
}

function carrega_vetor_cenario($idProject , $idCurrentScenario, $semAtual) {
   
    if (!isset($scenariosVector)) {
        
        $scenariosVector = array();
    }
    
    if ($semAtual) {
    
        $queryCenarios = "SELECT id_cenario, titulo    
			  FROM cenario    
			  WHERE id_projeto = '$idProject ' AND id_cenario <> '$idCurrentScenario' 
			  ORDER BY titulo DESC";
    }
    else {
        
        $queryCenarios = "SELECT id_cenario, titulo    
			  FROM cenario    
			  WHERE id_projeto = '$idProject ' 
			  ORDER BY titulo DESC";
    }

    $resultadoQueryCenarios = mysql_query($queryCenarios) or die("Erro ao enviar a query de selecao !!" . mysql_error());

    $index = 0;
    
    while ($linhaCenario = mysql_fetch_object($resultadoQueryCenarios)) {
      
        $scenariosVector[$index] = $linhaCenario;
        $index++;
    }

    return $scenariosVector;
}

function divide_array(&$vet, $ini, $fim, $tipo) {
    
    $index = $ini;
    $j = $fim;
    $dir = 1;

    while ($index < $j) {
    
        if (strcasecmp($tipo, 'cenario') == 0) {
        
            if (strlen($vet[$index]->titulo) < strlen($vet[$j]->titulo)) {
            
                $str_temp = $vet[$index];
                $vet[$index] = $vet[$j];
                $vet[$j] = $str_temp;
                $dir--;
            }
        }
        else {
            
            if (strlen($vet[$index]->nome) < strlen($vet[$j]->nome)) {
                
                $str_temp = $vet[$index];
                $vet[$index] = $vet[$j];
                $vet[$j] = $str_temp;
                $dir--;
            }
        }
        
        if ($dir == 1) {
            
            $j--;
        }            
        else {
            
            $index++;
        }
    }

    return $index;
}

function quicksort(&$vet, $ini, $fim, $tipo) {
    
    if ($ini < $fim) {
    
        $k = divide_array($vet, $ini, $fim, $tipo);
        quicksort($vet, $ini, $k - 1, $tipo);
        quicksort($vet, $k + 1, $fim, $tipo);
    }
}

function monta_links($text, $lexiconVector, $scenariosVector) {
    
    $copiaTexto = $text;
    
    if (!isset($vetorAuxLexicos)) {
        $vetorAuxLexicos = array();
    }
    
    if (!isset($vetorAuxCenarios)) {
        $vetorAuxCenarios = array();
    }
    
    if (!isset($scenariosVector)) {
        $scenariosVector = array();
    }
    
    if (!isset($lexiconVector)) {
        $lexiconVector = array();
    }

    if (count($scenariosVector) == 0) {

        $index = 0;
        $indexAux_2 = 0;
    
        while ($index < count($lexiconVector)) {
        
            $nomeLexico = escapa_metacaracteres($lexiconVector[$index]->nome);
            $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
            
            if (preg_match($regex, $copiaTexto) != 0) {
                $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                $vetorAuxLexicos[$indexAux_2] = $lexiconVector[$index];
                $indexAux_2++;
            }
            
            $index++;
        }
    }
    else {

        $tamLexicos = count($lexiconVector);
        $tamCenarios = count($scenariosVector);
        $tamanhoTotal = $tamLexicos + $tamCenarios;
        $index = 0;
        $indexAux_1 = 0;
        $indexAux_2 = 0;
        $indexAux_3 = 0;
        $contador = 0;
    
        while ($contador < $tamanhoTotal) {
        
            if (($index < $tamLexicos ) && ($indexAux_1 < $tamCenarios)) {
            
                if (strlen($scenariosVector[$indexAux_1]->titulo) < strlen($lexiconVector[$index]->nome)) {
                
                    $nomeLexico = escapa_metacaracteres($lexiconVector[$index]->nome);
                    $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
                    
                    if (preg_match($regex, $copiaTexto) != 0) {
                    
                        $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                        $vetorAuxLexicos[$indexAux_2] = $lexiconVector[$index];
                        $indexAux_2++;
                    }
                    
                    $index++;
                }
                else {

                    $tituloCenario = escapa_metacaracteres($scenariosVector[$indexAux_1]->titulo);
                    $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
                    
                    if (preg_match($regex, $copiaTexto) != 0) {
                    
                        $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                        $vetorAuxCenarios[$indexAux_3] = $scenariosVector[$indexAux_1];
                        $indexAux_3++;
                    }
                    
                    $indexAux_1++;
                }
            }
            else if ($tamLexicos == $index) {

                $tituloCenario = escapa_metacaracteres($scenariosVector[$indexAux_1]->titulo);
                $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
                
                if (preg_match($regex, $copiaTexto) != 0) {
                    
                    $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                    $vetorAuxCenarios[$indexAux_3] = $scenariosVector[$indexAux_1];
                    $indexAux_3++;
                }
                
                $indexAux_1++;
            }
            else if ($tamCenarios == $indexAux_1) {

                $nomeLexico = escapa_metacaracteres($lexiconVector[$index]->nome);
                $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
                
                if (preg_match($regex, $copiaTexto) != 0) {
                    
                    $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                    $vetorAuxLexicos[$indexAux_2] = $lexiconVector[$index];
                    $indexAux_2++;
                }
                
                $index++;
            }
            
            $contador++;
        }
    }

    $index = 0;
    $vetorAux = array();
    
    while ($index < count($vetorAuxLexicos)) {
    
        $nomeLexico = escapa_metacaracteres($vetorAuxLexicos[$index]->nome);
        $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
        $link = "<a title=\"Léxico\" href=\"main.php?t=l&id=" . $vetorAuxLexicos[$index]->id_lexico . "\">" . $vetorAuxLexicos[$indice]->nome . "</a>";
        $vetorAux[$index] = $link;
        $text = preg_replace($regex, "$1wzzxkkxy" . $index . "$3", $text);
        $index++;
    }
    
    $indice2 = 0;

    while ($indice2 < count($vetorAux)) {
    
        $linkLexico = ( $vetorAux[$indice2] );
        $regex = "/(\s|\b)(wzzxkkxy" . $indice2 . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1" . $linkLexico . "$3", $text);
        $indice2++;
    }

    $index = 0;
    $vetorAuxCen = array();
    
    while ($index < count($vetorAuxCenarios)) {
        
        $tituloCenario = escapa_metacaracteres($vetorAuxCenarios[$index]->titulo);
        $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
        $link = "$1<a title=\"Cen�rio\" href=\"main.php?t=c&id=" . $vetorAuxCenarios[$index]->id_cenario . "\"><span style=\"font-variant: small-caps\">" . $vetorAuxCenarios[$index]->titulo . "</span></a>$3";
        $vetorAuxCen[$index] = $link;
        $text = preg_replace($regex, "$1wzzxkkxyy" . $index . "$3", $text);
        $index++;
    }

    $indice2 = 0;
    
    while ($indice2 < count($vetorAuxCen)) {
        $linkCenario = ( $vetorAuxCen[$indice2] );
        $regex = "/(\s|\b)(wzzxkkxyy" . $indice2 . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1" . $linkCenario . "$3", $text);
        $indice2++;
    }

    return $text;
}
?>

