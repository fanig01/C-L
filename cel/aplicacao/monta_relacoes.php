<?php

include_once("monta_relacoes.php");
include_once("coloca_links.php");

// Assemble the relations used in the side menu

function monta_relacoes($idProject ) {

    $DB = new PGDB ();
    $sql1 = new QUERY($DB);
    $sql2 = new QUERY($DB);
    $sql3 = new QUERY($DB);

    /*$sql1->execute ("DELETE FROM centocen");
      $sql2->execute ("DELETE FROM centolex") ;
      $sql3->execute ("DELETE FROM lextolex") ;
      Remakes the table's relationships centocen, centolex and lextolex
    */
 

    $comandoSql = "SELECT *
	          FROM cenario
	          WHERE id_projeto = $idProject 
	          ORDER BY CHAR_LENGTH(titulo) DESC";
    
    $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query");

    while ($result = mysql_fetch_array($requestResultSQL)) { 
        
        $idCurrentScenario = $result['id_cenario'];

        // This function makes vector with scenarios's title
        $vector_scenarios = loadScenariosVector($idProject , $idCurrentScenario);

        // This function makes vector with name and synonyms of all lexical
        $vector_lexicons = carrega_vetor_todos($idProject );

        // Sorts the levical's vector by the amount of words in name or synonym
        quicksort($vector_lexicons, 0, count($vector_lexicons) - 1, 'lexico');

        // Sorts the scenario's vector by the number of words of the title
        quicksort($vector_scenarios, 0, count($vector_scenarios) - 1, 'cenario');

        $title = $result['titulo'];
        $temporaryTitle = cenario_para_lexico($idCurrentScenario, $title, $vector_lexicons);
        adiciona_relacionamento($idCurrentScenario, 'cenario', $temporaryTitle);

        $objective = $result['objetivo'];
        $temporaryObjective = cenario_para_lexico($idCurrentScenario, $objective, $vector_lexicons);
        adiciona_relacionamento($idCurrentScenario, 'cenario', $temporaryObjective);

        $context = $result['contexto'];
        $temporaryContext = cenario_para_lexico_cenario_para_cenario($idCurrentScenario, $context, $vector_lexicons, $vector_scenarios);
        adiciona_relacionamento($idCurrentScenario, 'cenario', $temporaryContext); 

        $actors = $result['atores'];
        $temporaryActors = cenario_para_lexico($idCurrentScenario, $actors, $vector_lexicons);
        adiciona_relacionamento($idCurrentScenario, 'cenario', $temporaryActors);

        $resources = $result['recursos'];
        $temporaryResources = cenario_para_lexico($idCurrentScenario, $resources, $vector_lexicons);
        adiciona_relacionamento($idCurrentScenario, 'cenario', $temporaryResources);

        $exception = $result['excecao'];
        $temporaryException = cenario_para_lexico($idCurrentScenario, $exception, $vector_lexicons);
        adiciona_relacionamento($idCurrentScenario, 'cenario', $temporaryException);

        $episodes = $result['episodios'];
        $temporaryEpisodes = cenario_para_lexico_cenario_para_cenario($idCurrentScenario, $episodes, $vector_lexicons, $vector_scenarios);
        adiciona_relacionamento($idCurrentScenario, 'cenario', $temporaryEpisodes);
    }

    $comandoSql = "SELECT *
	          FROM lexico
	          WHERE id_projeto = $idProject 
	          ORDER BY CHAR_LENGTH(nome) DESC";
    
    $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query");

    while ($result = mysql_fetch_array($requestResultSQL)) { 
        
        $idCurrentLexicon = $result['id_lexico'];

        // This function makes vector with name and synonyms of all lexical less current vector

        $vector_lexicons = carrega_vetor($idProject , $idCurrentLexicon);

        //// Sorts the levical's vector by the amount of words in name or synonym
        quicksort($vector_lexicons, 0, count($vector_lexicons) - 1, 'lexico');

        $notion = $result['nocao'];
        $temporaryNotion = lexico_para_lexico($id_lexico, $notion, $vector_lexicons);
        adiciona_relacionamento($idCurrentLexicon, 'lexico', $temporaryNotion);	

        $impact = $result['impacto'];
        $temporaryImpact = lexico_para_lexico($id_lexico, $impact, $vector_lexicons);
        adiciona_relacionamento($idCurrentLexicon, 'lexico', $temporaryImpact);
    }
}

function lexico_para_lexico($id_lexico, $text, $vector_lexicons) {
    
    $i = 0;
    
    while ($i < count($vector_lexicons)) {
    
        $regex = "/(\s|\b)(" . $vector_lexicons[$i]->nome . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{l" . $vector_lexicons[$i]->id_lexico . "**$2" . "}$3", $text);
        $i++;
        
        /*  Code to insert the relationship in the lextolex's table 
        
        $comandoSql = "INSERT 
              INTO lextolex (id_lexico_from, id_lexico_to)
              VALUES ($id_lexico, " . $vector_lexicons[$i]->id_lexico . ")";
        mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT na lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
        
        */
        
        }
        
    return $text;

    
 }

function cenario_para_lexico($id_cenario, $text, $vector_lexicons) {
    
    $i = 0;
    
    while ($i < count($vector_lexicons)) {
    
        $regex = "/(\s|\b)(" . $vector_lexicons[$i]->nome . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{l" . $vector_lexicons[$j]->id_lexico . "**$2" . "}$3", $text);
        $i++;
        
        /* Code to insert the relationship in the centolex's table  
         
        $comandoSql = "INSERT 
              INTO centolex (id_cenario, id_lexico)
              VALUES ($id_cenario, " . $vector_lexicons[$i]->id_lexico . ")";
        mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
         */
    }
    
    return $text;
}

function cenario_para_cenario($id_cenario, $text, $vector_scenarios) {
    
    $i = 0;
    
    while ($i < count($vector_scenarios)) {
    
        $regex = "/(\s|\b)(" . $vector_scenarios[$i]->titulo . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{c" . $vector_scenarios[$j]->id_cenario . "**$2" . "}$3", $text);
        $i++;
        
        /* Code to insert the relationship in the centolex's table
          
        $comandoSql = "INSERT 
              INTO centolex (id_cenario, id_lexico)
              VALUES ($id_cenario, " . $vector_lexicons[$i]->id_lexico . ")";
        mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
         */
    }
    
    return $text;
}

function cenario_para_lexico_cenario_para_cenario($id_cenario, $text, $vector_lexicons, $vector_scenarios) {
 
    $i = 0;
    $j = 0;
    $k = 0;
    
    $total = count($vector_lexicons) + count($vector_scenarios);
    
    while ($k < $total) {
        if (strlen($vector_scenarios[$j]->titulo) < strlen($vector_lexicons[$i]->nome)) {
            $regex = "/(\s|\b)(" . $vector_lexicons[$i]->nome . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{l" . $vector_lexicons[$i]->id_lexico . "**$2" . "}$3", $text);
            $i++;

            /* Code to insert the relationship in the centolex's table
            
            $comandoSql = "INSERT 
               	  INTO centolex (id_cenario, id_lexico)
            	  VALUES ($id_cenario, " . $vector_lexicons[$i]->id_lexico . ")";
            mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
            */
        }
        else {
             
            $regex = "/(\s|\b)(" . $vector_scenarios[$j]->titulo . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{c" . $vector_scenarios[$j]->id_cenario . "**$2" . "}$3", $text);
            $j++;
        }
        
        $k++;
    }
    
    return $text;
}

// Function to add relationships in tables centocen, centolex and lextolex

function adiciona_relacionamento($id_from, $tipo_from, $text) {
    
    $i = 0; // text's index with placeholder
    $parser = 0; // checks when should be added tags
    $novo_texto = "";
    
    while ($i < strlen(&$text)) {
        
        if ($text[$i] == "{") {
            
            $parser++;
            
            //added link on the text 
            if ($parser == 1) { 
            
                $id_to = "";
                $i++;
                $tipo = $text[$i];
                $i++;
                
                while ($text[$i] != "*") {
                    
                    $id_to .= $text[$i];
                    $i++;               
                    
                }
                
                if ($tipo == "l") {
                    
                    if (strcasecmp($tipo_from, 'lexico') == 0) {
                        
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'l&eacute;xico para l&eacute;xico")</script>';
           	
                    }
                    else if (strcasecmp($tipo_from, 'cenario') == 0) {
                       
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen&aacute;rio para l&eacute;xico")</script>';
                      
                    }
                }
                
                if ($tipo == "c") {
                    
                    if (strcasecmp($tipo_from, 'cenario') == 0) {
                        
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen&aacute;rio para cen&aacute;rio")</script>';

                        /* Adds relation of scenario to  scenario in table centocen
                        
                        $comandoSql = "INSERT 
                     	      INTO centocen (id_cenario_from, id_cenario_to)
                              VALUES ($id_from, " . $vector_scenarios[$j]->id_cenario . ")";
                        mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT na centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                        */
                        
                    }
                }
                
                $i + 1;
            }
            
        }
        elseif ($text[$i] == "}") {
          
            $parser--;
        
        }
        
        $i++;
    }
}

?>