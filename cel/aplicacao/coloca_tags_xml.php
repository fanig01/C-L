<?php

  // Module that put the tags of links into the archive XML

include ("coloca_links.php");

function putsTagXML($str) {

    $reference = "<link ref=\"$str\">$str </link>";
    return $reference;
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

function exchangeKeysXML($string) {
    
    $accountOpened = 0;
    $accountClosed = 0;
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
            
            $accountOpened = $accountOpened + 1;
        }
        else {
            //Nothing should be done
        }
       
        $index++;
    }
    
    $index = 0;
    
    while ($index <= $sizeOfString) {
        
        if ($string[$index] == '}') {
           
            $accountClosed = $accountClosed + 1;
        }
        else {
            //Nothing should be done
        }
        
        $index++;    
    }
    
    $index = 0;
    
    if ($accountOpened == 0) {
        return $string;
    }
    else {
        //Nothing should be done
    }
    
    $index = 0;
    
    while ($index <= $sizeOfString) {
        
        if ($string[$index] == '{') {
            $buffer = $buffer + 1;
            
            if ($buffer == 1) {
                $begin[$realLinks] = $index;
                
                $realLinks++;
            }
            else {
                //Nothing should be done
            }
        }
        else {
            //Nothing should be done
        }
        
        if ($string[$index] == '}') {    
            $buffer = $buffer - 1;
           
            if ($buffer == 0) {
                $end[$y] = $index + 1;
                
                $y++;
            }
            else {
                //Nothing should be done
            }
        }
        else {
            //Nothing should be done
        }
        
        $index++;
    }
    
    $index = 0;

    while ($index < $realLinks) { 
       
        $link = substr($string, $begin[$index], $end[$index] - $begin[$index]);
        $originalLink[$index] = $link;        
        $link = str_replace('{', '', $link);
        $link = str_replace('}', '', $link);
        $buffer2 = 0;
        $account = 0;
        $index2 = 0;
        $arrayId[$index] = get_IdXml($link);
        $link = '**' . $link;
        $marker = 0;

        while ($index2 < $end[$index] - $begin[$index]) {
           
            if ($link[$index2] == '*' && $link[$index2 + 1] == '*' && $marker == 1) {
                $marker = 0;
                $link[$index2] = '{';
                $link[$index2 + 1] = '{';
                $index2++;
                $index2++;
                continue;
            }
            else {
                //Nothing should be done
            }

            if ($link[$index2] == '*' && $link[$index2 + 1] == '*') {
                $marker = 1;
                $link[$index2] = '{';
                $index2++;
                continue;
            }
            else {
                //Nothing should be done
            }

            if ($marker == 1) {
                $link[$index2] = '{';
            }
            else {
                //Nothing should be done
            }
            
            $index2++;
        }
        
        $link = str_replace('{', '', $link);
        $link = putsTagXML($link, $arrayId[$index]);
        $newLink[$index] = $link;
        $index++;
    }
    
    $index = 0;
    
    while ($index < $realLinks) {
        
        $string = str_replace($originalLink[$index], $newLink[$index], $string);
       
        $index++;
    }

    return $string;
}

function makeLinksXML($text, $vector_lexicon, $vector_scenario) {

    marca_texto($text, $vector_scenario, "cenario");
    marca_texto_cenario($text, $vector_lexicon, $vector_scenario);

    $str = exchangeKeysXML($text);
    return $str;
}
?> 
