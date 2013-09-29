<?php

session_start();

include 'estruturas.php';

/* This module have functions to save a ontology on DAML file */

$day = date("Y-m-d");
$hour = date("H:i:s");
$date = $day . "T" . $hour . "Z";


// This function Save a ontology on DAML
function saveDAML($urlOntology, $directory, $file, $arrayInformation, 
                   $conceptsList, $relationsList,$axiomsList) {
 
    $url = $urlOntology . $file;
    $address = $directory . $file;

    // Cria um novo arquivo DAML 
    if (!$fp = fopen($address, "w")){
       
        return FALSE;
    }
    else {
        //Nothing should be done
    }

    $header = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
    $header = $header . '<rdf:RDF xmlns:daml="http://www.daml.org/2001/03/daml+oil#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:xsd="http://www.w3.org/2000/10/XMLSchema#" xmlns:';
    $header = $header . $arrayInformation['title'] . '="' . $url . '#">';
    
    if (!fwrite($fp, $header)){
        
        return FALSE;
    }
    else {
        //Nothing should be done
    }
    
    $information = '<daml:Ontology rdf:about="">';
    
    if ($arrayInformation ["title"] == "") {
        
        $information = $information . '<dc:title />';
    }
    else {
       
        $information = $information . '<dc:title>' . $arrayInformation ["title"] . '</dc:title>';
    }
    
    $information = $information . '<dc:date>' . date("j-m-Y  H:i:s") . '</dc:date>';
    
    if ($arrayInformation ["creator"] == "") {
        
        $information = $information . '<dc:creator />';
    }
    else {
       
        $information = $information . '<dc:creator>' . $arrayInformation ["creator"] . '</dc:creator>';
    }
    
    if ($arrayInformation ["description"] == "") {
        
        $information = $information . '<dc:description />';
    }
    else {
        
        $information = $information . '<dc:description>' . $arrayInformation ["description"] . '</dc:description>';
    }
    
    if ($arrayInformation ["subject"] == "") {
        
        $information = $information . '<dc:subject />';
    }
    else {
        
        $information = $information . '<dc:subject>' . $arrayInformation ["subject"] . '</dc:subject>';
    }
    
    if ($arrayInformation ["versionInfo"] == "") {
        
        $information = $information . '<daml:versionInfo />';
    }
    else {
        
        $information = $information . '<daml:versionInfo>' . $arrayInformation ["versionInfo"] . '</daml:versionInfo>';
    }
    
    $information = $information . '</daml:Ontology>';
    
    if (!fwrite($fp, $information)) {
        
        return FALSE;
    }
    else {
        //Nothing should be done
    }

    if (!recordsConcepts($fp, $url, $conceptsList, 
            $arrayInformation ["creator"])) {
        
        return FALSE;
    }
    else {
        //Nothing should be done
    }
    
    if (!recordsRelations($fp, $url, $relationsList, 
            $arrayInformation ["creator"])) {
        
        return FALSE;
    }
    else {
        //Nothing should be done
    }
    
    if (!recordsAxioms($fp, $url,$axiomsList, $arrayInformation ["creator"])) {
        
        return FALSE;
    }
    else {
        //Nothing should be done
    }
    
    if (!fwrite($fp, '</rdf:RDF>')) {
        
        return FALSE;
    }
    else {
        //Nothing should be done
    }

    fclose($fp);

    return $file;
}

// This function record the concepts on DAML file
function recordsConcepts($fp, $url, $conceptsList, $creator) {

    foreach ($conceptsList as $theConcept) {

        if ($theConcept->namespace == "proprio") {
            
            $namespace = "";
        }
        else {
            
            $namespace = $theConcept->namespace;
        }
        
        $s_conc = '<daml:Class rdf:about="' . $namespace . '#' . $theConcept->nome . '">';
        $s_conc = $s_conc . '<rdfs:label>' . strip_tags($theConcept->nome) . '</rdfs:label>';
        $s_conc = $s_conc . '<rdfs:comment><![CDATA[' . strip_tags($theConcept->descricao) . ']]> ' . '</rdfs:comment>';
        $s_conc = $s_conc . '<creationDate><![CDATA[' . $GLOBALS["data"] . ']]> ' . '</creationDate>';
        $s_conc = $s_conc . '<creator><![CDATA[' . $creator . ']]> ' . '</creator>';

        if (!fwrite($fp, $s_conc)) {
            
            return FALSE;
        }
        else {
            //Nothing should be done
        }

        $subconceptsList = $theConcept->subconceitos;
        
        foreach ($subconceptsList as $subconcept) {
            
            $s_subconc = '<rdfs:subClassOf>';
            $s_subconc = $s_subconc . '<daml:Class rdf:about="' . $url . '#' . strip_tags($subconcept) . '" />';
            $s_subconc = $s_subconc . '</rdfs:subClassOf>';
        
            if (!fwrite($fp, $s_subconc)) {
                
                return FALSE;
            }
            else {
                //Nothing should be done
            }
        }

        $relationsList = $theConcept->relacoes;
        
        
        foreach ($relationsList as $relation) {
            
            $s_relac = '<rdfs:subClassOf>';
            $s_relac = $s_relac . '<daml:Restriction>';
            $predicatesList = $relation->predicados;

            foreach ($predicatesList as $predicado) {
                
                $s_relac = $s_relac . '<daml:onProperty rdf:resource="' . '#' . strip_tags($relation->verbo) . '" />';
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
                //Nothing should be done   
            }
        }

        $s_conc = '</daml:Class>';

        if (!fwrite($fp, $s_conc)) {
            
            return FALSE;
        }
        else {
            //Nothing should be done
        }
    }

    return TRUE;
}

// This function record the relationships on DAML file
function recordsRelations($fp, $url, $relationsList, $creator) {
   
    foreach ($relationsList as $relation) {
        
        $s_rel = '<daml:ObjectProperty rdf:about="' . "#" . strip_tags($relation) . '">';
        $s_rel = $s_rel . '<rdfs:label>' . $relation . '</rdfs:label>';
        $s_rel = $s_rel . '<creationDate><![CDATA[' . $GLOBALS["data"] . ']]> ' . '</creationDate>';
        $s_rel = $s_rel . '<creator><![CDATA[' . $creator . ']]> ' . '</creator>';
        $s_rel = $s_rel . '</daml:ObjectProperty>';
        
        if (!fwrite($fp, $s_rel)) {
            
            return FALSE;
        }
        else {
            //Nothing should be done
        }
    }
    
    return TRUE;
}

// This function record the axioms on DAML file
function recordsAxioms($fp, $url,$axiomsList) {
   
    foreach ($axiomsList as $axiom) {

        $axi = explode(" disjoint ", $axiom);
        $s_axi = '<daml:Class rdf:about="' . $url . '#' . strip_tags($axi[0]) . '">';
        $s_axi = $s_axi . '<daml:disjointWith>';
        $s_axi = $s_axi . '<daml:Class rdf:about="' . $url . '#' . strip_tags($axi[1]) . '" />';
        $s_axi = $s_axi . '</daml:disjointWith>';
        $s_axi = $s_axi . '</daml:Class>';

        if (!fwrite($fp, $s_axi)) {
            return FALSE;
        }
        else {
            //Nothing should be done
        }
    }

    return TRUE;
}

?>