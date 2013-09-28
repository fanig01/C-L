<?php

function load_ArrayLexicon($idProject , $idCurrentLexicon, $noCurrent) {
    
    $lexiconVector = array();
    
    if ($noCurrent) {
    
        $queryLexicons = "SELECT id_lexico, nome    
			  FROM lexico    
			  WHERE id_projeto = '$idProject ' AND id_lexico <> '$idCurrentLexicon' 
			  ORDER BY nome DESC";

        $querySynonyms = "SELECT id_lexico, nome 
			  FROM sinonimo
		          WHERE id_projeto = '$idProject ' AND id_lexico <> '$idCurrentLexicon' 
		         ORDER BY nome DESC";
    }
    else {

        $queryLexicons = "SELECT id_lexico, nome    
			  FROM lexico    
			  WHERE id_projeto = '$idProject ' 
		          ORDER BY nome DESC";

        $querySynonyms = "SELECT id_lexico, nome    
			  FROM sinonimo
			  WHERE id_projeto = '$idProject ' ORDER BY nome DESC";
    }

    $queryLexiconsResult = mysql_query($queryLexicons) or die("Erro ao enviar a query de selecao na tabela lexicos !" . mysql_error());

    $index = 0;
    
    while ($lexicalLine = mysql_fetch_object($queryLexiconsResult)) {
        
        $lexiconVector[$index] = $lexicalLine;
        $index++;
    }

    $querySynonymsResult = mysql_query($querySynonyms) or die("Erro ao enviar a query de selecao na tabela sinonimos !" . mysql_error());
    
    while ($synonymousLine = mysql_fetch_object($querySynonymsResult)) {
    
        $lexiconVector[$index] = $synonymousLine;
        $index++;
    }
    
    return $lexiconVector;
}

function loadScenariosVector($idProject , $idCurrentScenario, $noCurrent) {
   
    if (!isset($scenariosVector)) {
        
        $scenariosVector = array();
    }
    
    if ($noCurrent) {
    
        $queryScenarios = "SELECT id_cenario, titulo    
			   FROM cenario    
			   WHERE id_projeto = '$idProject ' AND id_cenario <> '$idCurrentScenario' 
			   ORDER BY titulo DESC";
    }
    else {
        
        $queryScenarios = "SELECT id_cenario, titulo    
			   FROM cenario    
			   WHERE id_projeto = '$idProject ' 
			   ORDER BY titulo DESC";
    }

    $queryScenariosResult = mysql_query($queryScenarios) or die("Erro ao enviar a query de selecao !!" . mysql_error());

    $index = 0;
    
    while ($scenarioLine = mysql_fetch_object($queryScenariosResult)) {
      
        $scenariosVector[$index] = $scenarioLine;
        $index++;
    }

    return $scenariosVector;
}

function dividesArray(&$vector, $initiation, $end, $type) {
    
    $index = $initiation;
    $index2 = $end;
    $dir = 1;

    while ($index < $index2) {
    
        if (strcasecmp($type, 'cenario') == 0) {
        
            if (strlen($vector[$index]->titulo) < strlen($vector[$index2]->titulo)) {
            
                $temporaryString = $vector[$index];
                $vector[$index] = $vector[$index2];
                $vector[$index2] = $temporaryString;
                $dir--;
            }
            else {
                //Nothing should be done
            }
        }
        else {
            
            if (strlen($vector[$index]->nome) < strlen($vector[$index2]->nome)) {
                
                $temporaryString = $vector[$index];
                $vector[$index] = $vector[$index2];
                $vector[$index2] = $temporaryString;
                $dir--;
            }
            else {
                //Nothing should be done
            }
        }
        
        if ($dir == 1) {
            
            $index2--;
        }            
        else {
            
            $index++;
        }
    }

    return $index;
}

function quicksort(&$vector, $initiation, $end, $type) {
    
    if ($initiation < $end) {
    
        $k = dividesArray($vector, $initiation, $end, $type);
        quicksort($vector, $initiation, $k - 1, $type);
        quicksort($vector, $k + 1, $end, $type);
    }
}

function mountLinks($text, $lexiconVector, $scenariosVector) {
    
    $copyText = $text;
    
    if (!isset($lexiconsAuxiliaryVector)) {
        $lexiconsAuxiliaryVector = array();
    }
    else {
        //Nothing should be done
    }
    
    if (!isset($scenariosAuxiliaryVector)) {
        $scenariosAuxiliaryVector = array();
    }
    else {
        //Nothing should be done
    }
    
    if (!isset($scenariosVector)) {
        $scenariosVector = array();
    }
    else {
        //Nothing should be done
    }
    
    if (!isset($lexiconVector)) {
        $lexiconVector = array();
    }
    else {
        //Nothing should be done
    }

    if (count($scenariosVector) == 0) {

        $index = 0;
        $indexAux_2 = 0;
    
        while ($index < count($lexiconVector)) {
        
            $lexiconName = escapa_metacaracteres($lexiconVector[$index]->nome);
            $regex = "/(\s|\b)(" . $lexiconName . ")(\s|\b)/i";
            
            if (preg_match($regex, $copyText) != 0) {
               
                $copyText = preg_replace($regex, " ", $copyText);
                $lexiconsAuxiliaryVector[$indexAux_2] = $lexiconVector[$index];
                $indexAux_2++;
            }
            else {
                //Nothing should be done
            }
            
            $index++;
        }
    }
    else {

        $lexiconsSize = count($lexiconVector);
        $scenariosSize  = count($scenariosVector);
        $totalSize = $lexiconsSize + $scenariosSize ;
        $index = 0;
        $indexAux_1 = 0;
        $indexAux_2 = 0;
        $indexAux_3 = 0;
        $counter = 0;
    
        while ($counter < $totalSize) {
        
            if (($index < $lexiconsSize ) && ($indexAux_1 < $scenariosSize )) {
            
                if (strlen($scenariosVector[$indexAux_1]->titulo) < strlen($lexiconVector[$index]->nome)) {
                
                    $lexiconName = escapa_metacaracteres($lexiconVector[$index]->nome);
                    $regex = "/(\s|\b)(" . $lexiconName . ")(\s|\b)/i";
                    
                    if (preg_match($regex, $copyText) != 0) {
                    
                        $copyText = preg_replace($regex, " ", $copyText);
                        $lexiconsAuxiliaryVector[$indexAux_2] = $lexiconVector[$index];
                        $indexAux_2++;
                    }
                    else {
                        //Nothing should be done
                    }
                    
                    $index++;
                }
                else {

                    $scenarioName = escapa_metacaracteres($scenariosVector[$indexAux_1]->titulo);
                    $regex = "/(\s|\b)(" . $scenarioName . ")(\s|\b)/i";
                    
                    if (preg_match($regex, $copyText) != 0) {
                    
                        $copyText = preg_replace($regex, " ", $copyText);
                        $scenariosAuxiliaryVector[$indexAux_3] = $scenariosVector[$indexAux_1];
                        $indexAux_3++;
                    }
                    else {
                        //Nothing should be done
                    }
                    
                    $indexAux_1++;
                }
            }
            else if ($lexiconsSize == $index) {

                $scenarioName = escapa_metacaracteres($scenariosVector[$indexAux_1]->titulo);
                $regex = "/(\s|\b)(" . $scenarioName . ")(\s|\b)/i";
                
                if (preg_match($regex, $copyText) != 0) {
                    
                    $copyText = preg_replace($regex, " ", $copyText);
                    $scenariosAuxiliaryVector[$indexAux_3] = $scenariosVector[$indexAux_1];
                    $indexAux_3++;
                }
                else {
                    //Nothing should be done
                }
                
                $indexAux_1++;
            }
            else if ($scenariosSize  == $indexAux_1) {

                $lexiconName = escapa_metacaracteres($lexiconVector[$index]->nome);
                $regex = "/(\s|\b)(" . $lexiconName . ")(\s|\b)/i";
                
                if (preg_match($regex, $copyText) != 0) {
                    
                    $copyText = preg_replace($regex, " ", $copyText);
                    $lexiconsAuxiliaryVector[$indexAux_2] = $lexiconVector[$index];
                    $indexAux_2++;
                }
                else {
                    //Nothing should be done
                }
                
                $index++;
            }
            
            $counter++;
        }
    }

    $index = 0;
    $auxiliaryVector = array();
    
    while ($index < count($lexiconsAuxiliaryVector)) {
    
        $lexiconName = escapa_metacaracteres($lexiconsAuxiliaryVector[$index]->nome);
        $regex = "/(\s|\b)(" . $lexiconName . ")(\s|\b)/i";
        $link = "<a title=\"L&eacute;xico\" href=\"main.php?t=l&id=" . $lexiconsAuxiliaryVector[$index]->id_lexico . "\">" . $lexiconsAuxiliaryVector[$indice]->nome . "</a>";
        $auxiliaryVector[$index] = $link;
        $text = preg_replace($regex, "$1wzzxkkxy" . $index . "$3", $text);
        $index++;
    }
    
    $indexAux_4 = 0;

    while ($indexAux_4 < count($auxiliaryVector)) {
    
        $linkLexicon = ( $auxiliaryVector[$indexAux_4] );
        $regex = "/(\s|\b)(wzzxkkxy" . $indexAux_4 . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1" . $linkLexicon . "$3", $text);
        $indexAux_4++;
    }

    $index = 0;
    $scenariosAuxVector = array();
    
    while ($index < count($scenariosAuxiliaryVector)) {
        
        $scenarioName = escapa_metacaracteres($scenariosAuxiliaryVector[$index]->titulo);
        $regex = "/(\s|\b)(" . $scenarioName . ")(\s|\b)/i";
        $link = "$1<a title=\"Cen&aacute;rio\" href=\"main.php?t=c&id=" . $scenariosAuxiliaryVector[$index]->id_cenario . "\"><span style=\"font-variant: small-caps\">" . $scenariosAuxiliaryVector[$index]->titulo . "</span></a>$3";
        $scenariosAuxVector[$index] = $link;
        $text = preg_replace($regex, "$1wzzxkkxyy" . $index . "$3", $text);
        $index++;
    }

    $indexAux_4 = 0;
    
    while ($indexAux_4 < count($scenariosAuxVector)) {
        
        $linkScenario = ( $scenariosAuxVector[$indexAux_4] );
        $regex = "/(\s|\b)(wzzxkkxyy" . $indexAux_4 . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1" . $linkScenario . "$3", $text);
        $indexAux_4++;
    }

    return $text;
}
?>

