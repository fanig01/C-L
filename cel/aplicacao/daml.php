<?php

/* This module have functions to save a ontology on DAML file */

include 'estruturas.php';
session_start();

// Template to date
$dia = date("Y-m-d");
$hora = date("H:i:s");
$data = $dia . "T" . $hora . "Z";


// This function Save a ontology on DAML
function salva_daml($url_ontologia, $diretorio, $arquivo, $array_info, 
                   $lista_de_conceitos, $lista_de_relacoes, $lista_de_axiomas) {
    $url = $url_ontologia . $arquivo;

    $caminho = $diretorio . $arquivo;

    // Cria um novo arquivo DAML 
    if (!$fp = fopen($caminho, "w"))
        return FALSE;

    $cabecalho = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
    $cabecalho = $cabecalho . '<rdf:RDF xmlns:daml="http://www.daml.org/2001/03/daml+oil#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:xsd="http://www.w3.org/2000/10/XMLSchema#" xmlns:';
    $cabecalho = $cabecalho . $array_info['title'] . '="' . $url . '#">';
    if (!fwrite($fp, $cabecalho))
        return FALSE;

    $info = '<daml:Ontology rdf:about="">';
    if ($array_info ["title"] == "") {
        $info = $info . '<dc:title />';
    }
    else {
        $info = $info . '<dc:title>' . $array_info ["title"] . '</dc:title>';
    }
    
    $info = $info . '<dc:date>' . date("j-m-Y  H:i:s") . '</dc:date>';
    
    if ($array_info ["creator"] == "") {
        $info = $info . '<dc:creator />';
    }
    else {
        $info = $info . '<dc:creator>' . $array_info ["creator"] . '</dc:creator>';
    }
    
    if ($array_info ["description"] == "") {
        $info = $info . '<dc:description />';
    }
    else {
        $info = $info . '<dc:description>' . $array_info ["description"] . '</dc:description>';
    }
    
    if ($array_info ["subject"] == "") {
        $info = $info . '<dc:subject />';
    }
    else {
        $info = $info . '<dc:subject>' . $array_info ["subject"] . '</dc:subject>';
    }
    
    if ($array_info ["versionInfo"] == "") {
        $info = $info . '<daml:versionInfo />';
    }
    else {
        $info = $info . '<daml:versionInfo>' . $array_info ["versionInfo"] . '</daml:versionInfo>';
    }
    
    $info = $info . '</daml:Ontology>';
    
    if (!fwrite($fp, $info)) {
        return FALSE;
    }
    else {
    }

    if (!grava_conceitos($fp, $url, $lista_de_conceitos, 
            $array_info ["creator"])) {
        return FALSE;
    }
    else {
    }
    if (!grava_relacoes($fp, $url, $lista_de_relacoes, 
            $array_info ["creator"])) {
        return FALSE;
    }
    else {
    }
    
    if (!grava_axiomas($fp, $url, $lista_de_axiomas, $array_info ["creator"])) {
        return FALSE;
    }
    else {
    }
    
    if (!fwrite($fp, '</rdf:RDF>')) {
        return FALSE;
    }
    else {
    }

    fclose($fp);

    return $arquivo;
}

// This function record the concepts on DAML file
function grava_conceitos($fp, $url, $lista_de_conceitos, $criador) {
    // Não podemos usar a variável $conceito por causa do algoritmo do Jerônimo...
    foreach ($lista_de_conceitos as $oConceito) {

        if ($oConceito->namespace == "proprio") {
            $namespace = "";
        }
        else {
            $namespace = $oConceito->namespace;
        }
        
        $s_conc = '<daml:Class rdf:about="' . $namespace . '#' . $oConceito->nome . '">';
        $s_conc = $s_conc . '<rdfs:label>' . strip_tags($oConceito->nome) . '</rdfs:label>';
        $s_conc = $s_conc . '<rdfs:comment><![CDATA[' . strip_tags($oConceito->descricao) . ']]> ' . '</rdfs:comment>';
        $s_conc = $s_conc . '<creationDate><![CDATA[' . $GLOBALS["data"] . ']]> ' . '</creationDate>';
        $s_conc = $s_conc . '<creator><![CDATA[' . $criador . ']]> ' . '</creator>';

        if (!fwrite($fp, $s_conc)) {
            return FALSE;
        }
        else {
        }

        $lista_subconceitos = $oConceito->subconceitos;
        
        foreach ($lista_subconceitos as $subconceito) {
            $s_subconc = '<rdfs:subClassOf>';
            $s_subconc = $s_subconc . '<daml:Class rdf:about="' . $url . '#' . strip_tags($subconceito) . '" />';
            $s_subconc = $s_subconc . '</rdfs:subClassOf>';
        
            if (!fwrite($fp, $s_subconc)) {
                return FALSE;
            }
            else {
            }
        }

        $lista_relacoes = $oConceito->relacoes;
        foreach ($lista_relacoes as $relacao) {
            $s_relac = '<rdfs:subClassOf>';
            $s_relac = $s_relac . '<daml:Restriction>';
            $lista_predicados = $relacao->predicados;

            foreach ($lista_predicados as $predicado) {
                $s_relac = $s_relac . '<daml:onProperty rdf:resource="' . '#' . strip_tags($relacao->verbo) . '" />';
                $s_relac = $s_relac . '<daml:hasClass>';
                $s_relac = $s_relac . '<daml:Class rdf:about="' . '#' . strip_tags($predicado) . '" />';
                $s_relac = $s_relac . '</daml:hasClass>';
            }
            
            $s_relac = $s_relac . '</daml:Restriction>';
            $s_relac = $s_relac . '</rdfs:subClassOf>';
            
            if (!fwrite($fp, $s_relac)) {
                return FALSE;
            }
            else {
            }
        }

        $s_conc = '</daml:Class>';

        if (!fwrite($fp, $s_conc)) {
            return FALSE;
        }
        else {
        }
    }

    return TRUE;
}

// This function record the relationships on DAML file
function grava_relacoes($fp, $url, $lista_de_relacoes, $criador) {
    foreach ($lista_de_relacoes as $relacao) {
        $s_rel = '<daml:ObjectProperty rdf:about="' . "#" . strip_tags($relacao) . '">';
        $s_rel = $s_rel . '<rdfs:label>' . $relacao . '</rdfs:label>';
        
        // $s_rel = $s_rel . '<rdfs:comment><![CDATA[' . "" . ']]> ' . '</rdfs:comment>' ;   não h� vari�vel coment�rio na estrutura utilizada 
        
        $s_rel = $s_rel . '<creationDate><![CDATA[' . $GLOBALS["data"] . ']]> ' . '</creationDate>';
        $s_rel = $s_rel . '<creator><![CDATA[' . $criador . ']]> ' . '</creator>';
        $s_rel = $s_rel . '</daml:ObjectProperty>';
        
        if (!fwrite($fp, $s_rel)) {
            return FALSE;
        }
        else {
        }
    }
    
    return TRUE;
}

// This function record the axioms on DAML file
function grava_axiomas($fp, $url, $lista_de_axiomas) {
    foreach ($lista_de_axiomas as $axioma) {

        $axi = explode(" disjoint ", $axioma);
        $s_axi = '<daml:Class rdf:about="' . $url . '#' . strip_tags($axi[0]) . '">';
        $s_axi = $s_axi . '<daml:disjointWith>';
        $s_axi = $s_axi . '<daml:Class rdf:about="' . $url . '#' . strip_tags($axi[1]) . '" />';
        $s_axi = $s_axi . '</daml:disjointWith>';
        $s_axi = $s_axi . '</daml:Class>';

        if (!fwrite($fp, $s_axi)) {
            return FALSE;
        }
        else {
        }
    }

    return TRUE;
}

?>