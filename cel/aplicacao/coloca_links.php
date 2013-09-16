<?php

function load_ArrayLexicon($id_projeto, $idCurrentLexicon, $noCurrent) {
    $vetorDeLexicos = array();
    if ($noCurrent) {
        $queryLexicos = "SELECT id_lexico, nome    
							FROM lexico    
							WHERE id_projeto = '$id_projeto' AND id_lexico <> '$idCurrentLexicon' 
							ORDER BY nome DESC";

        $querySinonimos = "SELECT id_lexico, nome 
							FROM sinonimo
							WHERE id_projeto = '$id_projeto' AND id_lexico <> '$idCurrentLexicon' 
							ORDER BY nome DESC";
    } else {

        $queryLexicos = "SELECT id_lexico, nome    
							FROM lexico    
							WHERE id_projeto = '$id_projeto' 
							ORDER BY nome DESC";

        $querySinonimos = "SELECT id_lexico, nome    
							FROM sinonimo
							WHERE id_projeto = '$id_projeto' ORDER BY nome DESC";
    }

    $resultadoQueryLexicos = mysql_query($queryLexicos) or die("Erro ao enviar a query de selecao na tabela lexicos !" . mysql_error());

    $index = 0;
    while ($linhaLexico = mysql_fetch_object($resultadoQueryLexicos)) {
        $vetorDeLexicos[$index] = $linhaLexico;
        $index++;
    }

    $resultadoQuerySinonimos = mysql_query($querySinonimos) or die("Erro ao enviar a query de selecao na tabela sinonimos !" . mysql_error());
    while ($linhaSinonimo = mysql_fetch_object($resultadoQuerySinonimos)) {
        $vetorDeLexicos[$index] = $linhaSinonimo;
        $index++;
    }
    return $vetorDeLexicos;
}

function carrega_vetor_cenario($id_projeto, $id_cenario_atual, $semAtual) {
    if (!isset($vetorDeCenarios)) {
        $vetorDeCenarios = array();
    }
    if ($semAtual) {
        $queryCenarios = "SELECT id_cenario, titulo    
							FROM cenario    
							WHERE id_projeto = '$id_projeto' AND id_cenario <> '$id_cenario_atual' 
							ORDER BY titulo DESC";
    } else {
        $queryCenarios = "SELECT id_cenario, titulo    
							FROM cenario    
							WHERE id_projeto = '$id_projeto' 
							ORDER BY titulo DESC";
    }

    $resultadoQueryCenarios = mysql_query($queryCenarios) or die("Erro ao enviar a query de selecao !!" . mysql_error());

    $index = 0;
    while ($linhaCenario = mysql_fetch_object($resultadoQueryCenarios)) {
        $vetorDeCenarios[$index] = $linhaCenario;
        $index++;
    }

    return $vetorDeCenarios;
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
        } else {
            if (strlen($vet[$index]->nome) < strlen($vet[$j]->nome)) {
                $str_temp = $vet[$index];
                $vet[$index] = $vet[$j];
                $vet[$j] = $str_temp;
                $dir--;
            }
        }
        if ($dir == 1)
            $j--;
        else
            $index++;
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

function monta_links($texto, $vetorDeLexicos, $vetorDeCenarios) {
    $copiaTexto = $texto;
    if (!isset($vetorAuxLexicos)) {
        $vetorAuxLexicos = array();
    }
    if (!isset($vetorAuxCenarios)) {
        $vetorAuxCenarios = array();
    }
    if (!isset($vetorDeCenarios)) {
        $vetorDeCenarios = array();
    }
    if (!isset($vetorDeLexicos)) {
        $vetorDeLexicos = array();
    }

    // Se o vetor de cen�rios estiver vazio ele s� ira procurar por refer�ncias a lexicos


    if (count($vetorDeCenarios) == 0) {

        $index = 0;
        $indexAux_2 = 0;
        while ($index < count($vetorDeLexicos)) {
            $nomeLexico = escapa_metacaracteres($vetorDeLexicos[$index]->nome);
            $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
            if (preg_match($regex, $copiaTexto) != 0) {
                $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                $vetorAuxLexicos[$indexAux_2] = $vetorDeLexicos[$index];
                $indexAux_2++;
            }
            $index++;
        }
    } else {

        // Se o vetor de cen�rios n�o estiver vazio ele ir� procurar por l�xicos e cen�rios

        $tamLexicos = count($vetorDeLexicos);
        $tamCenarios = count($vetorDeCenarios);
        $tamanhoTotal = $tamLexicos + $tamCenarios;
        $index = 0;
        $indexAux_1 = 0;
        $indexAux_2 = 0;
        $indexAux_3 = 0;
        $contador = 0;
        while ($contador < $tamanhoTotal) {
            if (($index < $tamLexicos ) && ($indexAux_1 < $tamCenarios)) {
                if (strlen($vetorDeCenarios[$indexAux_1]->titulo) < strlen($vetorDeLexicos[$index]->nome)) {
                    $nomeLexico = escapa_metacaracteres($vetorDeLexicos[$index]->nome);
                    $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
                    if (preg_match($regex, $copiaTexto) != 0) {
                        $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                        $vetorAuxLexicos[$indexAux_2] = $vetorDeLexicos[$index];
                        $indexAux_2++;
                    }
                    $index++;
                } else {

                    $tituloCenario = escapa_metacaracteres($vetorDeCenarios[$indexAux_1]->titulo);
                    $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
                    if (preg_match($regex, $copiaTexto) != 0) {
                        $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                        $vetorAuxCenarios[$indexAux_3] = $vetorDeCenarios[$indexAux_1];
                        $indexAux_3++;
                    }
                    $indexAux_1++;
                }
            } else if ($tamLexicos == $index) {

                $tituloCenario = escapa_metacaracteres($vetorDeCenarios[$indexAux_1]->titulo);
                $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
                if (preg_match($regex, $copiaTexto) != 0) {
                    $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                    $vetorAuxCenarios[$indexAux_3] = $vetorDeCenarios[$indexAux_1];
                    $indexAux_3++;
                }
                $indexAux_1++;
            } else if ($tamCenarios == $indexAux_1) {

                $nomeLexico = escapa_metacaracteres($vetorDeLexicos[$index]->nome);
                $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
                if (preg_match($regex, $copiaTexto) != 0) {
                    $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                    $vetorAuxLexicos[$indexAux_2] = $vetorDeLexicos[$index];
                    $indexAux_2++;
                }
                $index++;
            }
            $contador++;
        }
    }
    //print_r( $vetorAuxLexicos );
    // Adiciona os links para lexicos no texto 

    $index = 0;
    $vetorAux = array();
    while ($index < count($vetorAuxLexicos)) {
        $nomeLexico = escapa_metacaracteres($vetorAuxLexicos[$index]->nome);
        $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
        $link = "<a title=\"L�xico\" href=\"main.php?t=l&id=" . $vetorAuxLexicos[$index]->id_lexico . "\">" . $vetorAuxLexicos[$indice]->nome . "</a>";
        $vetorAux[$index] = $link;
        $texto = preg_replace($regex, "$1wzzxkkxy" . $index . "$3", $texto);
        $index++;
    }
    $indice2 = 0;

    while ($indice2 < count($vetorAux)) {
        $linkLexico = ( $vetorAux[$indice2] );
        $regex = "/(\s|\b)(wzzxkkxy" . $indice2 . ")(\s|\b)/i";
        $texto = preg_replace($regex, "$1" . $linkLexico . "$3", $texto);
        $indice2++;
    }


    // Adiciona os links para cen�rios no texto 

    $index = 0;
    $vetorAuxCen = array();
    while ($index < count($vetorAuxCenarios)) {
        $tituloCenario = escapa_metacaracteres($vetorAuxCenarios[$index]->titulo);
        $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
        $link = "$1<a title=\"Cen�rio\" href=\"main.php?t=c&id=" . $vetorAuxCenarios[$index]->id_cenario . "\"><span style=\"font-variant: small-caps\">" . $vetorAuxCenarios[$index]->titulo . "</span></a>$3";
        $vetorAuxCen[$index] = $link;
        $texto = preg_replace($regex, "$1wzzxkkxyy" . $index . "$3", $texto);
        $index++;
    }


    $indice2 = 0;
    while ($indice2 < count($vetorAuxCen)) {
        $linkCenario = ( $vetorAuxCen[$indice2] );
        $regex = "/(\s|\b)(wzzxkkxyy" . $indice2 . ")(\s|\b)/i";
        $texto = preg_replace($regex, "$1" . $linkCenario . "$3", $texto);
        $indice2++;
    }

    return $texto;
}
?>

