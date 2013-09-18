<?php

include_once("monta_relacoes.php");
include_once("coloca_links.php");

// Assemble the relations used in the side menu

function monta_relacoes($id_projeto) {

    $DB = new PGDB ();
    $sql1 = new QUERY($DB);
    $sql2 = new QUERY($DB);
    $sql3 = new QUERY($DB);

    /*$sql1->execute ("DELETE FROM centocen");
      $sql2->execute ("DELETE FROM centolex") ;
      $sql3->execute ("DELETE FROM lextolex") ;
      Remakes the table's relationships centocen, centolex and lextolex
    */
 

    $q = "SELECT *
	          FROM cenario
	          WHERE id_projeto = $id_projeto
	          ORDER BY CHAR_LENGTH(titulo) DESC";
    
    $qrr = mysql_query($q) or die("Erro ao enviar a query");

    while ($result = mysql_fetch_array($qrr)) { 
        
        $id_cenario_atual = $result['id_cenario'];

        // This function makes vector with scenarios's title

        $vetor_cenarios = carrega_vetor_cenario($id_projeto, $id_cenario_atual);

        // This function makes vector with name and synonyms of all lexical

        $vetor_lexicos = carrega_vetor_todos($id_projeto);

        // Sorts the levical's vector by the amount of words in name or synonym

        quicksort($vetor_lexicos, 0, count($vetor_lexicos) - 1, 'lexico');

        // Sorts the scenario's vector by the number of words of the title

        quicksort($vetor_cenarios, 0, count($vetor_cenarios) - 1, 'cenario');

 

        $titulo = $result['titulo'];
        $tempTitulo = cenario_para_lexico($id_cenario_atual, $titulo, $vetor_lexicos);
        adiciona_relacionamento($id_cenario_atual, 'cenario', $tempTitulo);

        $objetivo = $result['objetivo'];
        $tempObjetivo = cenario_para_lexico($id_cenario_atual, $objetivo, $vetor_lexicos);
        adiciona_relacionamento($id_cenario_atual, 'cenario', $tempObjetivo);

        $contexto = $result['contexto'];
        $tempContexto = cenario_para_lexico_cenario_para_cenario($id_cenario_atual, $contexto, $vetor_lexicos, $vetor_cenarios);
        adiciona_relacionamento($id_cenario_atual, 'cenario', $tempContexto); 

        $atores = $result['atores'];
        $tempAtores = cenario_para_lexico($id_cenario_atual, $atores, $vetor_lexicos);
        adiciona_relacionamento($id_cenario_atual, 'cenario', $tempAtores);

        $recursos = $result['recursos'];
        $tempRecursos = cenario_para_lexico($id_cenario_atual, $recursos, $vetor_lexicos);
        adiciona_relacionamento($id_cenario_atual, 'cenario', $tempRecursos);

        $excecao = $result['excecao'];
        $tempExcecao = cenario_para_lexico($id_cenario_atual, $excecao, $vetor_lexicos);
        adiciona_relacionamento($id_cenario_atual, 'cenario', $tempExcecao);

        $episodios = $result['episodios'];
        $tempEpisodios = cenario_para_lexico_cenario_para_cenario($id_cenario_atual, $episodios, $vetor_lexicos, $vetor_cenarios);
        adiciona_relacionamento($id_cenario_atual, 'cenario', $tempEpisodios);
    }

    $q = "SELECT *
	          FROM lexico
	          WHERE id_projeto = $id_projeto
	          ORDER BY CHAR_LENGTH(nome) DESC";
    
    $qrr = mysql_query($q) or die("Erro ao enviar a query");

    while ($result = mysql_fetch_array($qrr)) { 
        
        $id_lexico_atual = $result['id_lexico'];

        // This function makes vector with name and synonyms of all lexical less current vector

        $vetor_lexicos = carrega_vetor($id_projeto, $id_lexico_atual);

        //// Sorts the levical's vector by the amount of words in name or synonym
        quicksort($vetor_lexicos, 0, count($vetor_lexicos) - 1, 'lexico');

        $nocao = $result['nocao'];
        $tempNocao = lexico_para_lexico($id_lexico, $nocao, $vetor_lexicos);
        adiciona_relacionamento($id_lexico_atual, 'lexico', $tempNocao);	

        $impacto = $result['impacto'];
        $tempImpacto = lexico_para_lexico($id_lexico, $impacto, $vetor_lexicos);
        adiciona_relacionamento($id_lexico_atual, 'lexico', $tempImpacto);
    }
}

function lexico_para_lexico($id_lexico, $texto, $vetor_lexicos) {
    
    $i = 0;
    
    while ($i < count($vetor_lexicos)) {
    
        $regex = "/(\s|\b)(" . $vetor_lexicos[$i]->nome . ")(\s|\b)/i";
        $texto = preg_replace($regex, "$1{l" . $vetor_lexicos[$i]->id_lexico . "**$2" . "}$3", $texto);
        $i++;
        
        /*  Code to insert the relationship in the lextolex's table 
        
        $q = "INSERT 
              INTO lextolex (id_lexico_from, id_lexico_to)
              VALUES ($id_lexico, " . $vetor_lexicos[$i]->id_lexico . ")";
        mysql_query($q) or die("Erro ao enviar a query de INSERT na lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
        
        */
        
        }
        
    return $texto;

    
 }

function cenario_para_lexico($id_cenario, $texto, $vetor_lexicos) {
    
    $i = 0;
    
    while ($i < count($vetor_lexicos)) {
    
        $regex = "/(\s|\b)(" . $vetor_lexicos[$i]->nome . ")(\s|\b)/i";
        $texto = preg_replace($regex, "$1{l" . $vetor_lexicos[$j]->id_lexico . "**$2" . "}$3", $texto);
        $i++;
        
        /* Code to insert the relationship in the centolex's table  
         
        $q = "INSERT 
              INTO centolex (id_cenario, id_lexico)
              VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
        mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
         */
    }
    
    return $texto;
}

function cenario_para_cenario($id_cenario, $texto, $vetor_cenarios) {
    
    $i = 0;
    
    while ($i < count($vetor_cenarios)) {
    
        $regex = "/(\s|\b)(" . $vetor_cenarios[$i]->titulo . ")(\s|\b)/i";
        $texto = preg_replace($regex, "$1{c" . $vetor_cenarios[$j]->id_cenario . "**$2" . "}$3", $texto);
        $i++;
        
        /* Code to insert the relationship in the centolex's table
          
        $q = "INSERT 
              INTO centolex (id_cenario, id_lexico)
              VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
        mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
         */
    }
    
    return $texto;
}

function cenario_para_lexico_cenario_para_cenario($id_cenario, $texto, $vetor_lexicos, $vetor_cenarios) {
 
    $i = 0;
    $j = 0;
    $k = 0;
    
    $total = count($vetor_lexicos) + count($vetor_cenarios);
    
    while ($k < $total) {
        if (strlen($vetor_cenarios[$j]->titulo) < strlen($vetor_lexicos[$i]->nome)) {
            $regex = "/(\s|\b)(" . $vetor_lexicos[$i]->nome . ")(\s|\b)/i";
            $texto = preg_replace($regex, "$1{l" . $vetor_lexicos[$i]->id_lexico . "**$2" . "}$3", $texto);
            $i++;

            /* Code to insert the relationship in the centolex's table
            
            $q = "INSERT 
               	  INTO centolex (id_cenario, id_lexico)
            	  VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
            mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
            */
        }
        else {
             
            $regex = "/(\s|\b)(" . $vetor_cenarios[$j]->titulo . ")(\s|\b)/i";
            $texto = preg_replace($regex, "$1{c" . $vetor_cenarios[$j]->id_cenario . "**$2" . "}$3", $texto);
            $j++;
        }
        
        $k++;
    }
    
    return $texto;
}

// Function to add relationships in tables centocen, centolex and lextolex

function adiciona_relacionamento($id_from, $tipo_from, $texto) {
    
    $i = 0; // text's index with placeholder
    $parser = 0; // checks when should be added tags
    $novo_texto = "";
    
    while ($i < strlen(&$texto)) {
        
        if ($texto[$i] == "{") {
            
            $parser++;
            
            //added link on the text 
            if ($parser == 1) { 
            
                $id_to = "";
                $i++;
                $tipo = $texto[$i];
                $i++;
                
                while ($texto[$i] != "*") {
                    
                    $id_to .= $texto[$i];
                    $i++;               
                    
                }
                
                if ($tipo == "l") {
                    
                    if (strcasecmp($tipo_from, 'lexico') == 0) {
                        
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'léxico para léxico")</script>';
           	
                    }
                    else if (strcasecmp($tipo_from, 'cenario') == 0) {
                       
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen�rio para l�xico")</script>';
                      
                    }
                }
                
                if ($tipo == "c") {
                    
                    if (strcasecmp($tipo_from, 'cenario') == 0) {
                        
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen�rio para cen�rio")</script>';

                        /* Adds relation of scenario to  scenario in table centocen
                        
                        $q = "INSERT 
                     	      INTO centocen (id_cenario_from, id_cenario_to)
                              VALUES ($id_from, " . $vetor_cenarios[$j]->id_cenario . ")";
                        mysql_query($q) or die("Erro ao enviar a query de INSERT na centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                        */
                        
                    }
                }
                
                $i + 1;
            }
            
        }
        elseif ($texto[$i] == "}") {
          
            $parser--;
        
        }
        
        $i++;
    }
}

?>