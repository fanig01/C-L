<?php

include_once("coloca_links.php");

// Assemble the relations used in the side menu

function mountsRelations($idProject ) {

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
        $temporaryTitle = scenarioToLexicon($idCurrentScenario, $title, $vector_lexicons);
        addRelationships($idCurrentScenario, 'cenario', $temporaryTitle);

        $objective = $result['objetivo'];
        $temporaryObjective = scenarioToLexicon($idCurrentScenario, $objective, $vector_lexicons);
        addRelationships($idCurrentScenario, 'cenario', $temporaryObjective);

        $context = $result['contexto'];
        $temporaryContext = scenarioToLexicon_ScenarioToScenario($idCurrentScenario, $context, $vector_lexicons, $vector_scenarios);
        addRelationships($idCurrentScenario, 'cenario', $temporaryContext); 

        $actors = $result['atores'];
        $temporaryActors = scenarioToLexicon($idCurrentScenario, $actors, $vector_lexicons);
        addRelationships($idCurrentScenario, 'cenario', $temporaryActors);

        $resources = $result['recursos'];
        $temporaryResources = scenarioToLexicon($idCurrentScenario, $resources, $vector_lexicons);
        addRelationships($idCurrentScenario, 'cenario', $temporaryResources);

        $exception = $result['excecao'];
        $temporaryException = scenarioToLexicon($idCurrentScenario, $exception, $vector_lexicons);
        addRelationships($idCurrentScenario, 'cenario', $temporaryException);

        $episodes = $result['episodios'];
        $temporaryEpisodes = scenarioToLexicon_ScenarioToScenario($idCurrentScenario, $episodes, $vector_lexicons, $vector_scenarios);
        addRelationships($idCurrentScenario, 'cenario', $temporaryEpisodes);
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
        $temporaryNotion = lexiconToLexicon($id_lexico, $notion, $vector_lexicons);
        addRelationships($idCurrentLexicon, 'lexico', $temporaryNotion);	

        $impact = $result['impacto'];
        $temporaryImpact = lexiconToLexicon($id_lexico, $impact, $vector_lexicons);
        addRelationships($idCurrentLexicon, 'lexico', $temporaryImpact);
    }
}

function lexiconToLexicon($idLexicon, $text, $vector_lexicons) {
    
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

function scenarioToLexicon($id_cenario, $text, $vector_lexicons) {
    
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

function scenarioToScenario($id_cenario, $text, $vector_scenarios) {
    
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

function scenarioToLexicon_ScenarioToScenario($idScenario, $text, $vector_lexicons, $vector_scenarios) {
 
    $index1 = 0;
    $index2 = 0;
    $index3 = 0;
    
    $total = count($vector_lexicons) + count($vector_scenarios);
    
    while ($index3 < $total) {
        if (strlen($vector_scenarios[$index2]->titulo) < strlen($vector_lexicons[$index1]->nome)) {
            $regex = "/(\s|\b)(" . $vector_lexicons[$index1]->nome . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{l" . $vector_lexicons[$index1]->id_lexico . "**$2" . "}$3", $text);
            $index1++;

            /* Code to insert the relationship in the centolex's table
            
            $comandoSql = "INSERT 
               	  INTO centolex (id_cenario, id_lexico)
            	  VALUES ($id_cenario, " . $vector_lexicons[$i]->id_lexico . ")";
            mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
            */
        }
        else {
             
            $regex = "/(\s|\b)(" . $vector_scenarios[$index2]->titulo . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{c" . $vector_scenarios[$index2]->id_cenario . "**$2" . "}$3", $text);
            $index2++;
        }
        
        $index3++;
    }
    
    return $text;
}

// Function to add relationships in tables centocen, centolex and lextolex
function addRelationships($id_from, $typeFrom, $text) {
    
    $i = 0; 
    $parser = 0; // checks when should be added tags
    $newText = "";
    
    while ($i < strlen(&$text)) {
        
        if ($text[$i] == "{") {
            
            $parser++;
            
            //added link on the text 
            if ($parser == 1) { 
            
                $id_to = "";
                $i++;
                $type = $text[$i];
                $i++;
                
                while ($text[$i] != "*") {
                    
                    $id_to .= $text[$i];
                    $i++;               
                    
                }
                
                if ($type == "l") {
                    
                    if (strcasecmp($typeFrom, 'lexico') == 0) {
                        
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'l&eacute;xico para l&eacute;xico")</script>';
           	
                    }
                    else if (strcasecmp($typeFrom, 'cenario') == 0) {
                       
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen&aacute;rio para l&eacute;xico")</script>';
                      
                    }
                }
                else {
                    //Nothing should be done
                }
                
                if ($type == "c") {
                    
                    if (strcasecmp($typeFrom, 'cenario') == 0) {
                        
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen&aacute;rio para cen&aacute;rio")</script>';

                        /* Adds relation of scenario to  scenario in table centocen
                        
                        $comandoSql = "INSERT 
                     	      INTO centocen (id_cenario_from, id_cenario_to)
                              VALUES ($id_from, " . $vector_scenarios[$j]->id_cenario . ")";
                        mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT na centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                        */
                        
                    }
                    else{
                        //Nothing should be done
                    }
                }
                else {
                    //Nothing should be done
                }
                
                $i + 1;
            }
            else {
                //Nothing should be done
            }         
        }
        elseif ($text[$i] == "}") {
          
            $parser--;
        
        }
        
        $i++;
    }
}

?>