<?php

/* * ******************************************************
 * Module created in 05/07/07
 * Group PES_07_1_1
 * Authors:
 *   BVF
 *   DFS
 *   TVD
 *   EMC
 * ****************************************************** */

/* * ******************************************************* 
  /* Module that put the tags of links into the archive XML
  /******************************************************** */


include ("coloca_links.php");

function poe_tag_xml($str) {

    $r = "<link ref=\"$str\">$str </link>";
    return $r;
}

function get_IdXml($string) {

    $index2 = 0;
    $index1 = 0;
    
    while ($string[$index1] != '*') {
        $buffer[$index2] = $string[$index1];
        
        $index1++;
        $index2++;
    }

    return implode('', $buffer);
}

function troca_chaves_xml($string) {
    $conta_abertos = 0;
    $conta_fehados = 0;
    $begin;
    $end;
    
    $realLinks = 0;
    $y = 0;
    
    $arrayId;
    $originalLink;
    $newLink;
    
    $buffer3 = '';
    $buffer = 0;
    
    $index = 0;
    
    $sizeOfString = strlen($string);

    while ($index <= $sizeOfString) {
        if ($string[$index] == '}') {
            $conta_abertos = $conta_abertos + 1;
        }
       
        $index++;
    }
    
    $index = 0;
    
    while ($index <= $sizeOfString) {
        if ($string[$index] == '}') {
            $conta_fechados = $conta_fechados + 1;
        }
        
        $index++;    
    }
    
    $index = 0;
    
    if ($conta_abertos == 0) {
        return $string;
    }
    
    $index = 0;
    
    while ($index <= $sizeOfString) {
        
        if ($string[$index] == '{') {
            $buffer = $buffer + 1;
            
            if ($buffer == 1) {
                $begin[$realLinks] = $index;
                
                $realLinks++;
            }
        }
        
        if ($string[$index] == '}') {    
            $buffer = $buffer - 1;
           
            if ($buffer == 0) {
                $end[$y] = $index + 1;
                
                $y++;
            }
        }
        
        $index++;
    };
    
    $index = 0;

    while ($index < $realLinks) { 
        $link = substr($string, $begin[$index], $end[$index] - $begin[$index]);
        $originalLink[$index] = $link;
        
        $link = str_replace('{', '', $link);
        $link = str_replace('}', '', $link);
        $buffer2 = 0;
        $conta = 0;
        $n = 0;
       
        //echo('aki - >'."$link".'<br>');
        $arrayId[$index] = get_IdXml($link);
        $link = '**' . $link;
        $marcador = 0;

        while ($n < $end[$index] - $begin[$index]) {
            if ($link[$n] == '*' && $link[$n + 1] == '*' && $marcador == 1) {
                $marcador = 0;
                $link[$n] = '{';
                $link[$n + 1] = '{';
                $n++;
                $n++;
                continue;
            }

            if ($link[$n] == '*' && $link[$n + 1] == '*') {
                $marcador = 1;
                $link[$n] = '{';
                $n++;
                continue;
            }

            if ($marcador == 1) {
                $link[$n] = '{';
            }
            
            $n++;
        }
        
        $link = str_replace('{', '', $link);
        $link = poe_tag_xml($link, $arrayId[$index]);
        $newLink[$index] = $link;
        $index++;
    }
    
    $index = 0;
    
    //echo("STRING INICAL -> $str<br/>");
    while ($index < $realLinks) {
        $string = str_replace($originalLink[$index], $newLink[$index], $string);
       
        $index++;
    }
    //echo("STRING FINAL -> $str<br/>");
    return $string;
}

function faz_links_XML($text, $vetor_lex, $vetor_cen) {

    marca_texto($text, $vetor_cen, "cenario");
    marca_texto_cenario($text, $vetor_lex, $vetor_cen);

    $str = troca_chaves_xml($text);
    return $str;
}
?> 
