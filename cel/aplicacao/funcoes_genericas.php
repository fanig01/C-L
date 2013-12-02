<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

if (!(function_exists("checkUserAuthentication"))) {

    function checkUserAuthentication($url) {

        if( !(isset($_SESSION["id_usuario_corrente"])))  {
           
            ?>
            <script language="javascript1.3">
                
                open('login.php?url=<?= $url ?>', 'login', 'dependent,height=430,width=490,resizable,scrollbars,titlebar');
  
            </script>

            <?php
            exit();
        }
        else {
            //Nothing should be done
        }
    }

}
else {
    //Nothing should be done
}

/*
  Inserts a scenario in the database.
  Receives id_projeto, title, objective, context, actors, resources, exception and episodes. 
  Insert values ​​in table SCENARIO lexicon. 
  Returns the id_cenario. 
*/

if (!(function_exists("scenarioIncludes"))) {

    function scenarioIncludes($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes) {
              
        $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $date = date("Y-m-d");
        $commandSQL = "INSERT INTO cenario (id_projeto,data, titulo, objetivo, contexto, atores, recursos, excecao, episodios) 
		       VALUES ($idProject ,'$date', '" . prepara_dado(strtolower($title)) . "', '" . prepara_dado($objective) . "',
		       '" . prepara_dado($context) . "', '" . prepara_dado($actors) . "', '" . prepara_dado($resources) . "',
		       '" . prepara_dado($exception) . "', '" . prepara_dado($episodes) . "')";

        mysql_query($commandSQL) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $commandSQL = "SELECT max(id_cenario) FROM cenario";
        
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($requestResultSQL);
        
        return $result[0];
    }

}
else {
    //Nothing should be done
}

/* 
  Inserts a lexicon in the database.
  Receives id_projeto, name, concept, impact and synonyms. (1.1)
  Inserts the values of lexicons ​​in the table lexicon. (1.2)
  Inserts all synonyms in the synonym table. (1.3)
  Returns the id_lexico. (1.4)
 */

if (!(function_exists("lexiconIncludes"))) {

    function lexiconIncludes($idProject , $name, $notion, $impact, $synonyms, $classification) {
        
        $$SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $date = date("Y-m-d");


        $commandSQL = "INSERT INTO lexico (id_projeto, data, nome, nocao, impacto, tipo)
                       VALUES ($idProject , '$date', '" . prepara_dado(strtolower($name)) . "',
	              '" . prepara_dado($notion) . "', '" . prepara_dado($impact) . "', '$classification')";

        mysql_query($commandSQL) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

 
        $newLexId = mysql_insert_id($$SgbdConnectStatus);


        if (!is_array($synonyms)) {
            $synonyms = array();
        }
        else {
            //Nothing should be done
        }

        foreach ($synonyms as $newSynonymous) {
            
            $commandSQL = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                           VALUES ($newLexId, '" . prepara_dado(strtolower($newSynonymous)) . "', $idProject )";
            mysql_query($commandSQL, $$SgbdConnectStatus) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }

        $commandSQL = "SELECT max(id_lexico) FROM lexico";
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($requestResultSQL);
        
        return $result[0];
    }

}

/*
  Inserts in a project database.
  Receives the name and description. (1.1)
  Checks if this user already has a project with that name. (1.2)
  If not possess, enter the values ​​in the PROJECT table. (1.3)
  Returns the id_cprojeto. (1.4)
 */

if (!(function_exists("projectIncludes"))) {

    function projectIncludes($name, $description) {
       
        $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        //verifies that user already exists
        $queryVerifies = "SELECT * FROM projeto WHERE nome = '$name'";
        $queryVerifiesResult = mysql_query($queryVerifies) or die("Erro ao enviar a query de select<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $resultArray = mysql_fetch_array($queryVerifiesResult);


        if ($resultArray != false) {
            
            //checks if the name corresponds to an existing project that this user participates
            $idProjectRepeated = $resultArray['id_projeto'];

            $idCurrentUser = $_SESSION['id_usuario_corrente'];

            $queryVerifyUser = "SELECT * FROM participa WHERE id_projeto = '$idProjectRepeated' AND id_usuario = '$idCurrentUser' ";

            $queryVerifyUserResult = mysql_query($queryVerifyUser) or die("Erro ao enviar a query de SELECT no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

            $resultArray = mysql_fetch_row($queryVerifyUserResult);

            if ($resultArray[0] != null) {
                return -1;
            }
            else {
                //Nothing should be done
            }
        }
        else {
            //Nothing should be done
        }

        $commandSQL = "SELECT MAX(id_projeto) FROM projeto";
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de MAX ID<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($requestResultSQL);

        if ($result[0] == false) {
            $result[0] = 1;
        }
        else {
            $result[0]++;
        }
        
        $date = date("Y-m-d");

        $query_result = "INSERT INTO projeto (id_projeto, nome, data_criacao, descricao)
                  VALUES ($result[0],'" . prepara_dado($name) . "','$date' , '" . prepara_dado($description) . "')";

        mysql_query($query_result) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        return $result[0];
    }

}
else {
    //Nothing should be done
}


if (!(function_exists("recharge"))) {

    function recharge($url) {
        ?>

        <script language="javascript1.3">

            location.replace('<?= $url ?>');

        </script>

        <?php
    }

}
else {
    //Nothing should be done
}

if (!(function_exists("breakpoint"))) {

    function breakpoint($num) {
        ?>

        <script language="javascript1.3">

            alert('<?= $num ?>');

        </script>

        <?php
    }

}
else {
    //Nothing should be done
}

if (!(function_exists("simple_query"))) {

    funcTion simple_query($field, $table, $where) {
        
        $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD");
        $commandSQL = "SELECT $field FROM $table WHERE $where";
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query");
        $result = mysql_fetch_row($requestResultSQL);
        
        return $result[0];
    }

}
else {
    //Nothing should be done
}


/*
 For correct inclusion of a scenario, a series of procedures
 need to be taken (relating to requirement 'circular navigation'):
 
  1. Include the new scenario in the database;
  2. For all scenarios that project, except the newly inserted:
    2.1. Search in context and episodes
         Occurrences for the title of the scenario included;
     2.2. For fields that are found occurrences:
         2.2.1. Include table entry 'centocen';
     2.3. Search in context and episodes of the scenario included
          For occurrences of securities of other scenarios of the same project;
     2.4. If you find any occurrence:
        2.4.1. Include table entry 'centocen';
  3. For all names under lexicon that project:
      3.1. Find occurrences of these names in the title, purpose, context,
            Resources, actors, episodes, the scenario included;
      3.2. For fields that are found occurrences:
       3.2.1. Include table entry 'centolex';
*/

if (!(function_exists("addScenario"))) {

    function addScenario($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes) {

        $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
      
        $idIncluded = scenarioIncludes($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes);

        $commandSQL = "SELECT id_cenario, titulo, contexto, episodios
                       FROM cenario
                       WHERE id_projeto = $idProject 
                       AND id_cenario != $idIncluded
                       ORDER BY CHAR_LENGTH(titulo) DESC";
        
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        //Filling out the tables and lextolex centocen for mounting the side menu

        while ($result = mysql_fetch_array($requestResultSQL)) {    
            
            $escapedTitle = escapa_metacaracteres($title);
            $regex = "/(\s|\b)(" . $escapedTitle . ")(\s|\b)/i";

            if ((preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) {   
                
                $commandSQL = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
		               VALUES (" . $result['id_cenario'] . ", $idIncluded)"; 
                
                mysql_query($commandSQL) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            else {
                //Nothing should be done
            }

            $escapedTitle = escapa_metacaracteres($result['titulo']);
            $regex = "/(\s|\b)(" . $escapedTitle . ")(\s|\b)/i";

            if ((preg_match($regex, $context) != 0) ||
                    (preg_match($regex, $episodes) != 0)) {           
                
                $commandSQL = "INSERT INTO centocen (id_cenario_from, id_cenario_to) 
                               VALUES ($idIncluded, " . $result['id_cenario'] . ")"; 

                mysql_query($commandSQL) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            else {
                //Nothing should be done
            }
        }   

        $commandSQL = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $idProject ";
        
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        while ($result2 = mysql_fetch_array($requestResultSQL)) { 
            
            $escapedName = escapa_metacaracteres($result2['nome']);
            $regex = "/(\s|\b)(" . $escapedName . ")(\s|\b)/i";

            if ((preg_match($regex, $title) != 0) ||
                    (preg_match($regex, $objective) != 0) ||
                    (preg_match($regex, $context) != 0) ||
                    (preg_match($regex, $actors) != 0) ||
                    (preg_match($regex, $resources) != 0) ||
                    (preg_match($regex, $episodes) != 0) ||
                    (preg_match($regex, $exception) != 0)) { 
                
                $queryScenario = "SELECT * FROM centolex 
                        WHERE id_cenario = $idIncluded AND id_lexico = " . $result2['id_lexico'];
                $queryScenarioResult = mysql_query($queryScenario)
                        or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                
                $resultArrayScenario = mysql_fetch_array($queryScenarioResult);

                if ($resultArrayScenario == false) {
                    $commandSQL = "INSERT INTO centolex (id_cenario, id_lexico) 
                                   VALUES ($idIncluded, " . $result2['id_lexico'] . ")";
                    mysql_query($commandSQL) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  
                }
                else {
                    //Nothing should be done
                }
            }
            else {
                //Nothing should be done
            }
        }   
        

        $query_synonymous = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $idProject  AND id_pedidolex = 0";

        $query_synonymous_result = mysql_query($query_synonymous) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $synonymousName = array();

        $id_lexiconSynonymous = array();

        while ($rowSinonimo = mysql_fetch_array($query_synonymous_result)) {

            $synonymousName[] = $rowSinonimo["nome"];
            $id_lexiconSynonymous[] = $rowSinonimo["id_lexico"];
        }

        $query_scenario = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $idProject 
              AND id_cenario = $idIncluded";
        $count = count($synonymousName);
        
        for ($i = 0; $i < $count; $i++) {

            $requestResultSQL = mysql_query($query_scenario) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            
           // checks synonyms of other lexicons included in the scene
            while ($result = mysql_fetch_array($requestResultSQL)) {

                $escapedSynonymousName = escapa_metacaracteres($synonymousName[$i]);
                $regex = "/(\s|\b)(" . $escapedSynonymousName . ")(\s|\b)/i";

                if ((preg_match($regex, $objective) != 0) ||
                        (preg_match($regex, $context) != 0) ||
                        (preg_match($regex, $actors) != 0) ||
                        (preg_match($regex, $resources) != 0) ||
                        (preg_match($regex, $episodes) != 0) ||
                        (preg_match($regex, $exception) != 0)) {

                    $queryScenario = "SELECT * FROM centolex WHERE id_cenario = $idIncluded 
                                      AND id_lexico = $id_lexiconSynonymous[$i] ";
                    $queryScenarioResult = mysql_query($queryScenario) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayScenario = mysql_fetch_array($queryScenarioResult);

                    if ($resultArrayScenario == false) {
                        $commandSQL = "INSERT INTO centolex (id_cenario, id_lexico) 
                                       VALUES ($idIncluded, $id_lexiconSynonymous[$i])";
                        mysql_query($commandSQL) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                    }
                    else {
                        //Nothing should be done
                    }
                }
                else {
                    //Nothing should be done
                }
            }  
        } 
    }

}
else {
    //Nothing should be done
}

/*
 For the correct inclusion of a term in the lexicon, a series of procedures
  need to be taken (relating to requirement 'circular navigation'):

  1. Add the new term in the database;
  2. For all scenarios that project:
       2.1. Search in title, objective, context, resources, actors, episodes
            by occurrences of the word or its synonyms included;
       2.2. For fields that are found occurrences:
               2.2.1. Include table entry 'centolex';
  3. For all the lexical terms that project (minus the newly inserted):
       3.1. Search in notion, impact by occurrences of the word or its synonyms inserted;
       3.2. For fields that are found occurrences:
               3.2.1. Include table entry 'lextolex';
       3.3. Search in notion, the impact of the term entered by
            occurrences of terms in the lexicon of the same project;
       3.4. If you find any occurrence:
               3.4.1. Include entry in table 'lextolex';
 */

if (!(function_exists("addLexicon"))) {

    function addLexicon($idProject , $name, $notion, $impact, $synonyms, $classification) {
        
        $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $idIncluded = lexiconIncludes($idProject , $name, $notion, $impact, $synonyms, $classification); 

        $query_result = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
              FROM cenario
              WHERE id_projeto = $idProject ";

        $requestResultSQL = mysql_query($query_result) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($requestResultSQL)) {    
            
            $escapedName = escapa_metacaracteres($name);
            $regex = "/(\s|\b)(" . $escapedName . ")(\s|\b)/i";

            if ((preg_match($regex, $result['objetivo']) != 0) ||
                    (preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['atores']) != 0) ||
                    (preg_match($regex, $result['recursos']) != 0) ||
                    (preg_match($regex, $result['excecao']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) { 
                $commandSQL = "INSERT INTO centolex (id_cenario, id_lexico)
                     VALUES (" . $result['id_cenario'] . ", $idIncluded)"; 

                mysql_query($commandSQL) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            else {
                //Nothing should be done
            }
        }


        //synonyms of the new lexicon
        $count = count($synonyms);
        
        for ($i = 0; $i < $count; $i++) {

            $requestResultSQL = mysql_query($query_result) or 
                    die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            
            while ($result2 = mysql_fetch_array($requestResultSQL)) {

                $escapedSynonymousName = escapa_metacaracteres($synonyms[$i]);
                $regex = "/(\s|\b)(" . $escapedSynonymousName . ")(\s|\b)/i";

                if ((preg_match($regex, $result2['objetivo']) != 0) ||
                        (preg_match($regex, $result2['contexto']) != 0) ||
                        (preg_match($regex, $result2['atores']) != 0) ||
                        (preg_match($regex, $result2['recursos']) != 0) ||
                        (preg_match($regex, $result2['excecao']) != 0) ||
                        (preg_match($regex, $result2['episodios']) != 0)) {

                    $query_lexicon = "SELECT * FROM centolex WHERE id_cenario = " . $result2['id_cenario'] . " AND id_lexico = $idIncluded ";
                    $query_lexiconResult = mysql_query($query_lexicon) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($query_lexiconResult);

                    if ($resultArraylex == false) {

                        $commandSQL = "INSERT INTO centolex (id_cenario, id_lexico)
                             VALUES (" . $result2['id_cenario'] . ", $idIncluded)";

                        mysql_query($commandSQL) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                    else {
                        //Nothing should be done
                    }
                }
                else {
                    //Nothing should be done
                }
            }            
        }


        $query_otherLexicon = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $idProject 
               AND id_lexico != $idIncluded";

 
        $requestResultSQL = mysql_query($query_otherLexicon) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($requestResultSQL)) {   
            
            $escapedName = escapa_metacaracteres($name);
            $regex = "/(\s|\b)(" . $escapedName . ")(\s|\b)/i";

            if ((preg_match($regex, $result['nocao']) != 0 ) ||
                    (preg_match($regex, $result['impacto']) != 0)) {

                $query_lexicon = "SELECT * FROM lextolex WHERE id_lexico_from = " . $result['id_lexico'] . " AND id_lexico_to = $idIncluded";
                $query_lexiconResult = mysql_query($query_lexicon) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArraylex = mysql_fetch_array($query_lexiconResult);

                if ($resultArraylex == false) {
                    
                    $commandSQL = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                          VALUES (" . $result['id_lexico'] . ", $idIncluded)";

                    mysql_query($commandSQL) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
                else {
                    //Nothing should be done
                }
            }
            else {
                //Nothing should be done
            }

            $escapedName = escapa_metacaracteres($result['nome']);
            $regex = "/(\s|\b)(" . $escapedName . ")(\s|\b)/i";

            if ((preg_match($regex, $notion) != 0) ||
                    (preg_match($regex, $impact) != 0)) {          
               
                $commandSQL = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) VALUES ($idIncluded, " . $result['id_lexico'] . ")";

                mysql_query($commandSQL) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            else {
                //Nothing should be done
            }
        }   
 

        $query_Lexicon = "SELECT id_lexico, nome, nocao, impacto
                          FROM lexico
                          WHERE id_projeto = $idProject 
                          AND id_lexico != $idIncluded";

        $requestResultSQL = mysql_query($query_Lexicon) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $count = count($synonyms);
        
        for ($i = 0; $i < $count; $i++) {
            
            while ($resultl = mysql_fetch_array($requestResultSQL)) {

                $escapedSynonymousName = escapa_metacaracteres($synonyms[$i]);
                $regex = "/(\s|\b)(" . $escapedSynonymousName . ")(\s|\b)/i";

                if ((preg_match($regex, $resultl['nocao']) != 0) ||
                        (preg_match($regex, $resultl['impacto']) != 0)) {

                    $query_lexicon = "SELECT * FROM lextolex WHERE id_lexico_from = " . $resultl['id_lexico'] . " AND id_lexico_to = $idIncluded";
                    $query_lexiconResult = mysql_query($query_lexicon) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($query_lexiconResult);

                    if ($resultArraylex == false) {

                        $commandSQL = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                         VALUES (" . $resultl['id_lexico'] . ", $idIncluded)";

                        mysql_query($commandSQL) or die("Erro ao enviar a query de insert no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                    else {
                        //Nothing should be done
                    }
                }
                else {
                    //Nothing should be done
                }
            }
        }

        $query_Synonymous = "SELECT nome, id_lexico FROM sinonimo 
                             WHERE id_projeto = $idProject  
                             AND id_lexico != $idIncluded AND id_pedidolex = 0";

        $query_SynonymousResult = mysql_query($query_Synonymous) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $synonymousName = array();

        $id_lexiconSynonyms = array();

        while ($rowSynonyms = mysql_fetch_array($query_SynonymousResult)) {

            $synonymousName[] = $rowSynonyms["nome"];
            $id_lexiconSynonyms[] = $rowSynonyms["id_lexico"];
        }
    }

}
else {
    //Nothing should be done
}

/*
   This function receives an id of scenery and removes all its
   links and relationships that exist.
*/

if (!(function_exists("removeScenario"))) {

    function removeScenario($idProject , $id_Scenario) {
        
        $DB = new PGDB ();
        $sql1 = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);

        // Removes the relationship between the scenario to be removed
        // and other scenarios that reference
        $sql1->execute("DELETE FROM centocen WHERE id_cenario_from = $id_Scenario");
        $sql2->execute("DELETE FROM centocen WHERE id_cenario_to = $id_Scenario");
        
        // Removes the relationship between the scenario to be removed
        // and its lexicon
        $sql3->execute("DELETE FROM centolex WHERE id_cenario = $id_Scenario");
        
        // Removes the selected scenario
        $sql4->execute("DELETE FROM cenario WHERE id_cenario = $id_Scenario");
    }

}
else {
   //Nothing should be done 
}

if (!(function_exists("changeScenario"))) {

    function changeScenario($idProject , $idScenario, $title, $objective, $context, $actors, $resources, $exception, $episodes) {
        
        $DB = new PGDB ();
        $sql1 = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);
        
        // Removes the relationship between the scenario to be change
        // and other scenarios that reference
        $sql1->execute("DELETE FROM centocen WHERE id_cenario_from = $idScenario");
        $sql2->execute("DELETE FROM centocen WHERE id_cenario_to = $idScenario");
        
        // Removes the relationship between the scenario to be change
        // and its lexicon
        $sql3->execute("DELETE FROM centolex WHERE id_cenario = $idScenario");

        // updated scenario
        $sql4->execute("update cenario set 
		objetivo = '" . prepara_dado($objective) . "', 
		contexto = '" . prepara_dado($context) . "', 
		atores = '" . prepara_dado($actors) . "', 
		recursos = '" . prepara_dado($resources) . "', 
		episodios = '" . prepara_dado($episodes) . "', 
		excecao = '" . prepara_dado($exception) . "' 
		where id_cenario = $idScenario ");

        $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $commandSQL = "SELECT id_cenario, titulo, contexto, episodios
                       FROM cenario
                       WHERE id_projeto = $idProject 
                       AND id_cenario != $idScenario
                       ORDER BY CHAR_LENGTH(titulo) DESC";
        
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($requestResultSQL)) {    
            
            $escaped_title = escapa_metacaracteres($title);
            $regex = "/(\s|\b)(" . $escaped_title . ")(\s|\b)/i";

            if ((preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) {  
                
                $commandSQL = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
	                      VALUES (" . $result['id_cenario'] . ", $idScenario)"; 
                mysql_query($commandSQL) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            else {
                //Nothing should be done
            }
            
            $escaped_title = escapa_metacaracteres($result['titulo']);
            $regex = "/(\s|\b)(" . $escaped_title . ")(\s|\b)/i";

            if ((preg_match($regex, $context) != 0) ||
                    (preg_match($regex, $episodes) != 0)) {      
                $commandSQL = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($idScenario, " . $result['id_cenario'] . ")"; 

                mysql_query($commandSQL) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            else {
                //Nothing should be done
            }
        }   


        $commandSQL = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $idProject ";
        
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        while ($result2 = mysql_fetch_array($requestResultSQL)) {    
            
            $escaped_name = escapa_metacaracteres($result2['nome']);
            $regex = "/(\s|\b)(" . $escaped_name . ")(\s|\b)/i";

            if ((preg_match($regex, $title) != 0) ||
                    (preg_match($regex, $objective) != 0) ||
                    (preg_match($regex, $context) != 0) ||
                    (preg_match($regex, $actors) != 0) ||
                    (preg_match($regex, $resources) != 0) ||
                    (preg_match($regex, $episodes) != 0) ||
                    (preg_match($regex, $exception) != 0)) {   
               
                $query_Scenario = "SELECT * FROM centolex WHERE id_cenario = $idScenario AND id_lexico = " . $result2['id_lexico'];
                $query_ScenarioResult = mysql_query($query_Scenario) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArrayScen = mysql_fetch_array($query_ScenarioResult);

                if ($resultArrayScen == false) {
                    
                    $commandSQL = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($idScenario, " . $result2['id_lexico'] . ")";
                    mysql_query($commandSQL) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  
                }
                else {
                    //Nothing should be done
                }
            }
            else {
                //Nothing should be done
            }
        }   
       

        $query_synonymous = "SELECT nome, id_lexico FROM sinonimo 
                             WHERE id_projeto = $idProject  AND id_pedidolex = 0";

        $query_synonymousResult = mysql_query($query_synonymous) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $synonymousName = array();

        $id_lexiconSynonymous = array();

        while ($rowSynonymous = mysql_fetch_array($query_synonymousResult)) {

            $synonymousName[] = $rowSynonymous["nome"];
            $id_lexiconSynonymous[] = $rowSynonymous["id_lexico"];
        }

        $query_LexiconScenario = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
                                  FROM cenario
                                  WHERE id_projeto = $idProject 
                                  AND id_cenario = $idScenario";
        
        $count = count($synonymousName);
        
        for ($i = 0; $i < $count; $i++) {

            $requestResultSQL = mysql_query($query_LexiconScenario) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            
            while ($result = mysql_fetch_array($requestResultSQL)) {    
               
                $escapedSynonymousName = escapa_metacaracteres($synonymousName[$i]);
                $regex = "/(\s|\b)(" . $escapedSynonymousName . ")(\s|\b)/i";

                if ((preg_match($regex, $objective) != 0) ||
                        (preg_match($regex, $context) != 0) ||
                        (preg_match($regex, $actors) != 0) ||
                        (preg_match($regex, $resources) != 0) ||
                        (preg_match($regex, $episodes) != 0) ||
                        (preg_match($regex, $exception) != 0)) {

                    $query_Scenario = "SELECT * FROM centolex WHERE id_cenario = $idScenario AND id_lexico = $id_lexiconSynonymous[$i] ";
                    $query_ScenarioResult = mysql_query($query_Scenario) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayScen = mysql_fetch_array($query_ScenarioResult);

                    if ($resultArrayScen == false) {
                        $commandSQL = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($idScenario, $id_lexiconSynonymous[$i])";
                        mysql_query($commandSQL) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                    }
                    else {
                        //Nothing should be done
                    }
                }
                else {
                    //Nothing should be done
                }
            }  
        } 
    }

}
else {
    //Nothing should be done
}

// This function receives an id of lexical and removes all its
// Links and relationships existing in all the database tables.

if (!(function_exists("removeLexicon"))) {

    function removeLexicon($idProject , $id_lexico) {
        $database = new PGDB ();
        $delete = new QUERY($database);
        
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico");
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico");
        $delete->execute("DELETE FROM centolex WHERE id_lexico = $id_lexico");
        $delete->execute("DELETE FROM sinonimo WHERE id_lexico = $id_lexico");
        $delete->execute("DELETE FROM lexico WHERE id_lexico = $id_lexico");
    }

}
else {
    //Nothing should be done   
}


// This function receives an id of lexical and removes all its
// links and relationships existing in all the database tables.


if (!(function_exists("changeLexicon"))) {

    function changeLexicon($idProject , $id_lexico, $name, $notion, $impact, $sinonimos, $classificacao) {
        
        $database = new PGDB ();
        $delete = new QUERY($database);

        // Removes the previously existing relationship
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico");
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico");
        $delete->execute("DELETE FROM centolex WHERE id_lexico = $id_lexico");

        // Removes all previously registered sinonimos
        $delete->execute("DELETE FROM sinonimo WHERE id_lexico = $id_lexico");

        // Changes the lexical choice
        $delete->execute("UPDATE lexico SET 
		nocao = '" . prepara_dado($notion) . "', 
		impacto = '" . prepara_dado($impact) . "', 
		tipo = '$classificacao' 
		where id_lexico = $id_lexico");

        $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        // Checks if there is any occurrence of the title in the lexicon scenarios existing in the database

        $query_result = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
                         FROM cenario
                         WHERE id_projeto = $idProject ";

        $requestResultSQL = mysql_query($query_result) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($requestResultSQL)) {    
            
            $escapedName = escapa_metacaracteres($name);
            $regex = "/(\s|\b)(" . $escapedName . ")(\s|\b)/i";

            if ((preg_match($regex, $result['objetivo']) != 0) ||
                    (preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['atores']) != 0) ||
                    (preg_match($regex, $result['recursos']) != 0) ||
                    (preg_match($regex, $result['excecao']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) {
               
                $commandSQL = "INSERT INTO centolex (id_cenario, id_lexico)
                               VALUES (" . $result['id_cenario'] . ", $id_lexico)"; 

                mysql_query($commandSQL) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            else {
                //Nothing should be done
            }
        }

        // Checks for any occurrence of any of the synonyms in the lexicon scenarios existing in the data base.
        //&sininonimos = sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {
            
            $requestResultSQL = mysql_query($query_result) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            
            while ($result2 = mysql_fetch_array($requestResultSQL)) {

                $nomeSinonimoEscapado = escapa_metacaracteres($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $result2['objetivo']) != 0) ||
                        (preg_match($regex, $result2['contexto']) != 0) ||
                        (preg_match($regex, $result2['atores']) != 0) ||
                        (preg_match($regex, $result2['recursos']) != 0) ||
                        (preg_match($regex, $result2['excecao']) != 0) ||
                        (preg_match($regex, $result2['episodios']) != 0)) {
                    // $comandoSql = "INSERT INTO centolex (id_cenario, id_lexico)
                    //      VALUES (" . $result2['id_cenario'] . ", $id_lexico)";                   
                    //  mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
                else {
                    //Nothing should be done
                }
            }            
        } 
        
        /*
        End of the verification
 
        VERIFICATION OF OCCURRENCE IN LEXICONS     
         Verifies the occurrence of the title text changed in the lexicon of other lexicons
         Verifies the occurrence of other lexicons title changed in lexical       
        */
        
        //select to catch all other lexical
        
        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $idProject 
               AND id_lexico <> $id_lexico";

        $requestResultSQL = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($requestResultSQL)) {
            // for each lexical except what is being changed    
            // verifies the occurrence of the title text changed in the lexicon of other lexicons
            $escapedName = escapa_metacaracteres($name);
            $regex = "/(\s|\b)(" . $escapedName . ")(\s|\b)/i";

            if ((preg_match($regex, $result['nocao']) != 0 ) ||
                    (preg_match($regex, $result['impacto']) != 0)) {
                $commandSQL = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                      	VALUES (" . $result['id_lexico'] . ", $id_lexico)";

                mysql_query($commandSQL) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            else {
                //Nothing should be done
            }

            // Verifies the occurrence of the title other lexicons in text lexicon changed

            $escapedName = escapa_metacaracteres($result['nome']);
            $regex = "/(\s|\b)(" . $escapedName . ")(\s|\b)/i";

            if ((preg_match($regex, $notion) != 0) ||
                    (preg_match($regex, $impact) != 0)) {   // (3.3)        
                $commandSQL = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) 
                		VALUES ($id_lexico, " . $result['id_lexico'] . ")";

                mysql_query($commandSQL) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            else {
                //Nothing should be done
            }
        }
        # End of verification by title

        $ql = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_projeto = $idProject 
              AND id_lexico <> $id_lexico";

        // Verifies the occurrence of synonyms of lexical change in other lexicons

        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {
        // for each synonym of lexical change

            $requestResultSQL = mysql_query($ql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($resultl = mysql_fetch_array($requestResultSQL)) {
            // for each lexical changed except
                $nomeSinonimoEscapado = escapa_metacaracteres($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                // checks synonym [i] changed the lexicon in the text of each lexical

                if ((preg_match($regex, $resultl['nocao']) != 0) ||
                        (preg_match($regex, $resultl['impacto']) != 0)) {

                    // Checks if the relationship is already found in the database. If you do not do anything else or registers a new relationship
                    $qverif = "SELECT * FROM lextolex where id_lexico_from=" . $resultl['id_lexico'] . " and id_lexico_to=$id_lexico";
                    echo("Query: " . $qverif . "<br>");
                    $result = mysql_query($qverif) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    if (!resultado) {
                        $commandSQL = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES (" . $resultl['id_lexico'] . ", $id_lexico)";
                        mysql_query($commandSQL) or die("Erro ao enviar a query de insert(sinonimo2) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                }
                else {
                    //Nothing should be done
                }
            }
        }
        // Verifies the occurrence of synonyms in the lexicons of other lexical changed

        $qSinonimos = "SELECT nome, id_lexico 
        		FROM sinonimo 
        		WHERE id_projeto = $idProject  
        		AND id_lexico <> $id_lexico 
        		AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();
        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {
            $nomeSinonimoEscapado = escapa_metacaracteres($rowSinonimo["nome"]);
            $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $notion) != 0) ||
                    (preg_match($regex, $impact) != 0)) {

                // Checks if the relationship is already found in the database. If you do not do anything else or registers a new relationship
                $qv = "SELECT * FROM lextolex where id_lexico_from=$id_lexico and id_lexico_to=" . $rowSinonimo['id_lexico'];
                $result = mysql_query($qv) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                if (!resultado) {
                    $commandSQL = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES ($id_lexico, " . $rowSinonimo['id_lexico'] . ")";

                    mysql_query($commandSQL) or die("Erro ao enviar a query de insert(sinonimo) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
                else {
                    //Nothing should be done
                }
            }
            else {
                //Nothing should be done
            }
        }

        // Register synonyms again

        if (!is_array($sinonimos)){
            $sinonimos = array();
        }
        else {
            //Nothing should be done
        }

        foreach ($sinonimos as $novoSin) {
            $commandSQL = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                VALUES ($id_lexico, '" . prepara_dado(strtolower($novoSin)) . "', $idProject )";

            mysql_query($commandSQL, $SgbdConnectStatus) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }

        // End - registration synonyms      
    }

}
else {
    //Nothing should be done
}

// This function receives an id of concept and remove all your links and existing relationships.

if (!(function_exists("removeConcept"))) {

    function removeConcept($idProject , $id_conceito) {
       
        $DB = new PGDB ();
        $sql = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);
        $sql5 = new QUERY($DB);
        $sql6 = new QUERY($DB);
        $sql7 = new QUERY($DB);
       
        # Este select procura o cenario a ser removido
        # dentro do projeto

        $sql2->execute("SELECT * FROM conceito WHERE id_projeto = $idProject  and id_conceito = $id_conceito");
        if ($sql2->getntuples() == 0) {
            //echo "<BR> Cenario nao existe para esse projeto." ;
        } else {
            $record = $sql2->gofirst();
            $nomeConceito = $record['nome'];
            # tituloCenario = Nome do cenario com id = $id_cenario
        }
        # [ATENCAO] Essa query pode ser melhorada com um join
        //print("<br>SELECT * FROM cenario WHERE id_projeto = $idProject ");
        /*  $sql->execute ("SELECT * FROM cenario WHERE id_projeto = $idProject  AND id_cenario != $scenarioName");
          if ($sql->getntuples() == 0){
          echo "<BR> Projeto n�o possui cenarios." ;
          }else{ */
        $qr = "SELECT * FROM conceito WHERE id_projeto = $idProject  AND id_conceito != $id_conceito";
        //echo($qr)."          ";
        $requestResultSQL = mysql_query($qr) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result = mysql_fetch_array($requestResultSQL)) {
            # Percorre todos os cenarios tirando as tag do conceito
            # a ser removido
            //$record = $sql->gofirst ();
            //while($record !='LAST_RECORD_REACHED'){
            $idConceitoRef = $result['id_conceito'];
            $nomeAnterior = $result['nome'];
            $descricaoAnterior = $result['descricao'];
            $namespaceAnterior = $result['namespace'];
            #echo        "/<a title=\"Cen�rio\" href=\"main.php?t='c'&id=$id_cenario>($scenarioName)<\/a>/mi"  ;
            #$episodiosAnterior = "<a title=\"Cen�rio\" href=\"main.php?t=c&id=38\">robin</a>" ;
            /* "'<a title=\"Cen�rio\" href=\"main.php?t=c&id=38\">robin<\/a>'si" ; */
            $tiratag = "'<[\/\!]*?[^<>]*?>'si";
            //$tiratagreplace = "";
            //$scenarioName = preg_replace($tiratag,$tiratagreplace,$scenarioName);
            $regexp = "/<a[^>]*?>($nomeConceito)<\/a>/mi"; //rever
            $replace = "$1";
            //echo($episodiosAnterior)."   ";
            //$tituloAtual = $tituloAnterior ;
            //*$tituloAtual = preg_replace($regexp,$replace,$tituloAnterior);*/
            $descricaoAtual = preg_replace($regexp, $replace, $descricaoAnterior);
            $namespaceAtual = preg_replace($regexp, $replace, $namespaceAnterior);
            /* echo "ant:".$episodiosAtual ;
              echo "<br>" ;
              echo "dep:".$episodiosAnterior ; */
            // echo($scenarioName)."   ";
            // echo($episodiosAtual)."  ";
            //print ("<br>update cenario set objetivo = '$objetivoAtual',contexto = '$contextoAtual',atores = '$atoresAtual',recursos = '$recursosAtual',episodios = '$episodiosAtual' where id_cenario = $idCenarioRef ");
            $sql7->execute("update conceito set descricao = '$descricaoAtual', namespace = '$namespaceAtual' where id_conceito = $idConceitoRef ");

            //$record = $sql->gonext() ;
            // }
        }

        # Remove o conceito escolhido
        $sql6->execute("DELETE FROM conceito WHERE id_conceito = $id_conceito");
        $sql6->execute("DELETE FROM relacao_conceito WHERE id_conceito = $id_conceito");
    }

}
else {
    //Nothing should be done
}
###################################################################
# Essa funcao recebe um id de relacao e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeRelationship"))) {

    function removeRelationship($idProject , $id_relacao) {
        $DB = new PGDB ();

        $sql6 = new QUERY($DB);

        # Remove o conceito escolhido
        $sql6->execute("DELETE FROM relacao WHERE id_relacao = $id_relacao");
        $sql6->execute("DELETE FROM relacao_conceito WHERE id_relacao = $id_relacao");
    }

}
else {
    //Nothing should be done
}

###################################################################
# Funcao faz um select na tabela lexico.
# Para inserir um novo lexico, deve ser verificado se ele ja existe,
# ou se existe um sinonimo com o mesmo nome.
# Recebe o id do projeto e o nome do lexico (1.0)
# Faz um SELECT na tabela lexico procurando por um nome semelhante
# no projeto (1.1)
# Faz um SELECT na tabela sinonimo procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################

function checarLexicoExistente($projeto, $name) {
    $naoexiste = false;

    $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $commandSQL = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$name' ";
    $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray == false) {
        $naoexiste = true;
    }
    else {
        //Nothing should be done
    }

    $commandSQL = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$name' ";
    $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);

    if ($resultArray != false) {
        $naoexiste = false;
    }
    else {
        //Nothing should be done
    }

    return $naoexiste;
}

###################################################################
# Recebe o id do projeto e a lista de sinonimos (1.0)
# Funcao faz um select na tabela sinonimo.
# Para verificar se ja existe um sinonimo igual no BD.
# Faz um SELECT na tabela lexico para verificar se ja existe
# um lexico com o mesmo nome do sinonimo.(1.1)
# retorna true caso nao exista ou false caso exista (1.2)
###################################################################

function checarSinonimo($projeto, $listSinonimo) {
    $naoexiste = true;

    $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    foreach ($listSinonimo as $sinonimo) {

        $commandSQL = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray != false) {
            $naoexiste = false;
            return $naoexiste;
        }
        else {
            //Nothing should be done
        }

        $commandSQL = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray != false) {
            $naoexiste = false;
            return $naoexiste;
        }
        else {
            //Nothing should be done
        }
    }

    return $naoexiste;
}

###################################################################
# Funcao faz um select na tabela cenario.
# Para inserir um novo cenario, deve ser verificado se ele ja existe.
# Recebe o id do projeto e o titulo do cenario (1.0)
# Faz um SELECT na tabela cenario procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################

function checarCenarioExistente($projeto, $title) {
    $naoexiste = false;

    $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $commandSQL = "SELECT * FROM cenario WHERE id_projeto = $projeto AND titulo = '$title' ";
    $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray == false) {
        $naoexiste = true;
    }
    else {
        //Nothing should be done
    }

    return $naoexiste;
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo cenario ela deve receber os campos do novo
# cenario.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este cenario caso o criador n�o seja o gerente.
# Arquivos que utilizam essa funcao:
# add_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAdicionarCenario"))) {

    function inserirPedidoAdicionarCenario($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $commandSQL = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente
            $insere->execute("INSERT INTO pedidocen (id_projeto, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado) VALUES ($idProject , '$title', '$objective', '$context', '$actors', '$resources', '$exception', '$episodes', $id_usuario, 'inserir', 0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $idProject ");
            $record = $select->gofirst();
            $name = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Inclus�o Cen�rio", "O usuario do sistema $name\nPede para inserir o cenario $title \nObrigado!", "From: $name\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } 
        else { //Eh gerente
            addScenario($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes);
        }
    }

}
else {
    //Nothing should be done
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um cenario ela deve receber os campos do cenario
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerentes do projeto
# referente a este cenario caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) {

    function inserirPedidoAlterarCenario($idProject , $id_cenario, $title, $objective, $context, $actors, $resources, $exception, $episodes, $justificativa, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $commandSQL = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($idProject , $id_cenario, '$title', '$objective', '$context', '$actors', '$resources', '$exception', '$episodes', $id_usuario, 'alterar', 0, '$justificativa')");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $idProject ");
            $record = $select->gofirst();
            $name = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Altera��o Cen�rio", "O usuario do sistema $name\nPede para alterar o cenario $title \nObrigado!", "From: $name\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
        else { //Eh gerente
            changeScenario($idProject , $id_cenario, $title, $objective, $context, $actors, $resources, $exception, $episodes);
        }
    }

}
else {
    //Nothing should be done
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um cenario ela deve receber
# o id do cenario e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_cenario.php
###################################################################
if (!(function_exists("inserirPedidoRemoverCenario"))) {

    function inserirPedidoRemoverCenario($idProject , $id_cenario, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $commandSQL = ("SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ");
        $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);

        if ($resultArray == false) { //Nao e gerente
            $select->execute("SELECT * FROM cenario WHERE id_cenario = $id_cenario");
            $cenario = $select->gofirst();
            $title = $cenario['titulo'];
            $insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, id_usuario, tipo_pedido, aprovado) VALUES ($idProject , $id_cenario, '$title', $id_usuario, 'remover', 0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $idProject ");
            $record = $select->gofirst();
            $name = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover Cen�rio", "O usuario do sistema $name\nPede para remover o cenario $id_cenario \nObrigado!", "From: $name\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
        else {
            removeScenario($idProject , $id_cenario);
        }
    }

}
else {
    //Nothing should be done
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo lexico ela deve receber os campos do novo
# lexicos.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador n�o seja o gerente.
# Arquivos que utilizam essa funcao:
# add_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAdicionarLexico"))) {

    function inserirPedidoAdicionarLexico($idProject , $name, $notion, $impact, $id_usuario, $sinonimos, $classificacao) {

        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $commandSQL = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidolex (id_projeto,nome,nocao,impacto,tipo,id_usuario,tipo_pedido,aprovado) VALUES ($idProject ,'$name','$notion','$impact','$classificacao',$id_usuario,'inserir',0)");

            $newId = $insere->getLastId();

            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'");

            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $idProject ");


            //insere sinonimos

            foreach ($sinonimos as $sin) {
                $insere->execute("INSERT INTO sinonimo (id_pedidolex, nome, id_projeto) 
				VALUES ($newId, '" . prepara_dado(strtolower($sin)) . "', $idProject )");
            }
            //fim da insercao dos sinonimos

            if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
            } 
            else {

                $record = $select->gofirst();
                $nome2 = $record['nome'];
                $email = $record['email'];
                $record2 = $select2->gofirst();
                while ($record2 != 'LAST_RECORD_REACHED') {
                    $id = $record2['id_usuario'];
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst();
                    $mailGerente = $record['email'];
                    mail("$mailGerente", "Pedido de Inclus�o de L�xico", "O usuario do sistema $nome2\nPede para inserir o lexico $name \nObrigado!", "From: $nome2\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        }
        else { //Eh gerente
            addLexicon($idProject , $name, $notion, $impact, $sinonimos, $classificacao);
        }
    }

}
else {
    //Nothing should be done
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um lexico ela deve receber os campos do lexicos
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAlterarLexico"))) {

    function inserirPedidoAlterarLexico($idProject , $id_lexico, $name, $notion, $impact, $justificativa, $id_usuario, $sinonimos, $classificacao) {

        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $commandSQL = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,nocao,impacto,id_usuario,tipo_pedido,aprovado,justificativa, tipo) VALUES ($idProject ,$id_lexico,'$name','$notion','$impact',$id_usuario,'alterar',0,'$justificativa', '$classificacao')");

            $newPedidoId = $insere->getLastId();

            //sinonimos
            foreach ($sinonimos as $sin) {
                $insere->execute("INSERT INTO sinonimo (id_pedidolex,nome,id_projeto) 
				VALUES ($newPedidoId,'" . prepara_dado(strtolower($sin)) . "', $idProject )");
            }


            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $idProject ");

            if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
            } 
            else {
                $record = $select->gofirst();
                $nome2 = $record['nome'];
                $email = $record['email'];
                $record2 = $select2->gofirst();
                while ($record2 != 'LAST_RECORD_REACHED') {
                    $id = $record2['id_usuario'];
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst();
                    $mailGerente = $record['email'];
                    mail("$mailGerente", "Pedido de Alterar L�xico", "O usuario do sistema $nome2\nPede para alterar o lexico $name \nObrigado!", "From: $nome2\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } 
        else { //Eh gerente
            changeLexicon($idProject , $id_lexico, $name, $notion, $impact, $sinonimos, $classificacao);
        }
    }

}
else {
    //Nothing should be done
}
###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um lexico ela deve receber
# o id do lexico e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_lexico.php
###################################################################
if (!(function_exists("inserirPedidoRemoverLexico"))) {

    function inserirPedidoRemoverLexico($idProject , $id_lexico, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $commandSQL = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);

        if ($resultArray == false) { //nao e gerente

            $select->execute("SELECT * FROM lexico WHERE id_lexico = $id_lexico");
            $lexico = $select->gofirst();
            $name = $lexico['nome'];

            $insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,id_usuario,tipo_pedido,aprovado) VALUES ($idProject ,$id_lexico,'$name',$id_usuario,'remover',0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $idProject ");

            if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
            } 
            else {
                $record = $select->gofirst();
                $name = $record['nome'];
                $email = $record['email'];
                $record2 = $select2->gofirst();
                while ($record2 != 'LAST_RECORD_REACHED') {
                    $id = $record2['id_usuario'];
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst();
                    $mailGerente = $record['email'];
                    mail("$mailGerente", "Pedido de Remover L�xico", "O usuario do sistema $nome2\nPede para remover o lexico $id_lexico \nObrigado!", "From: $name\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } 
        else { // e gerente
            removeLexicon($idProject , $id_lexico);
        }
    }

}
else {
    //Nothing should be done
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um conceito ela deve receber os campos do conceito
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerentes do projeto
# referente a este cenario caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) {

    function inserirPedidoAlterarConceito($idProject , $id_conceito, $name, $description, $namespace, $justificativa, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $commandSQL = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidocon (id_projeto, id_conceito, nome, descricao, namespace, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($idProject , $id_conceito, '$name', '$description', '$namespace', $id_usuario, 'alterar', 0, '$justificativa')");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $idProject ");
            $record = $select->gofirst();
            $nomeUsuario = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Altera��o Conceito", "O usuario do sistema $nomeUsuario\nPede para alterar o conceito $name \nObrigado!", "From: $nomeUsuario\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
        else { //Eh gerente
            removeConcept($idProject , $id_conceito);
            adicionar_conceito($idProject , $name, $description, $namespace);
        }
    }

}
else {
    //Nothing should be done
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um conceito ela deve receber
# o id do conceito e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este conceito.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_conceito.php
###################################################################
if (!(function_exists("inserirPedidoRemoverConceito"))) {

    function inserirPedidoRemoverConceito($idProject , $id_conceito, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM conceito WHERE id_conceito = $id_conceito");
        $conceito = $select->gofirst();
        $name = $conceito['nome'];

        $insere->execute("INSERT INTO pedidocon (id_projeto,id_conceito,nome,id_usuario,tipo_pedido,aprovado) VALUES ($idProject ,$id_conceito,'$name',$id_usuario,'remover',0)");
        $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
        $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $idProject ");

        if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
        } 
        else {
            $record = $select->gofirst();
            $name = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_conceito \nObrigado!", "From: $name\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
    }

}
else {
    //Nothing should be done
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover uma relacao ela deve receber
# o id da relacao e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este relacao.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_relacao.php
###################################################################
if (!(function_exists("inserirPedidoRemoverRelacao"))) {

    function inserirPedidoRemoverRelacao($idProject , $id_relacao, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM relacao WHERE id_relacao = $id_relacao");
        $relation = $select->gofirst();
        $name = $relation['nome'];

        $insere->execute("INSERT INTO pedidorel (id_projeto,id_relacao,nome,id_usuario,tipo_pedido,aprovado) VALUES ($idProject ,$id_relacao,'$name',$id_usuario,'remover',0)");
        $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
        $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $idProject ");

        if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
        } 
        else {
            $record = $select->gofirst();
            $name = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_relacao \nObrigado!", "From: $name\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
    }

}
else {
    //Nothing should be done
}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoCenario"))) {

    function tratarPedidoCenario($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        //print("<BR>SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
        $select->execute("SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } 
        else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_cenario = $record['id_cenario'];
                $idProject  = $record['id_projeto'];
                removeScenario($idProject , $id_cenario);
                //$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
            } 
            else {

                $idProject  = $record['id_projeto'];
                $title = $record['titulo'];
                $objective = $record['objetivo'];
                $context = $record['contexto'];
                $actors = $record['atores'];
                $resources = $record['recursos'];
                $exception = $record['excecao'];
                $episodes = $record['episodios'];
                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_cenario = $record['id_cenario'];
                    removeScenario($idProject , $id_cenario);
                    //$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
                }
                addScenario($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes);
            }
            //$delete->execute ("DELETE FROM pedidocen WHERE id_pedido = $id_pedido") ;
        }
    }

}
else {
    //Nothing should be done
}
###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o lexico e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoLexico"))) {

    function tratarPedidoLexico($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $selectSin = new QUERY($DB);
        $select->execute("SELECT * FROM pedidolex WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } 
        else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_lexico = $record['id_lexico'];
                $idProject  = $record['id_projeto'];
                removeLexicon($idProject , $id_lexico);
            } 
            else {
                $idProject  = $record['id_projeto'];
                $name = $record['nome'];
                $notion = $record['nocao'];
                $impact = $record['impacto'];
                $classificacao = $record['tipo'];

                //sinonimos

                $sinonimos = array();
                $selectSin->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_pedido");
                $sinonimo = $selectSin->gofirst();
                if ($selectSin->getntuples() != 0) {
                    while ($sinonimo != 'LAST_RECORD_REACHED') {
                        $sinonimos[] = $sinonimo["nome"];
                        $sinonimo = $selectSin->gonext();
                    }
                }
                else {
                    //Nothing should be done
                }

                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_lexico = $record['id_lexico'];
                    changeLexicon($idProject , $id_lexico, $name, $notion, $impact, $sinonimos, $classificacao);
                } 
                else if (($idLexicoConflitante = addLexicon($idProject , $name, $notion, $impact, $sinonimos, $classificacao)) <= 0) {
                    $idLexicoConflitante = -1 * $idLexicoConflitante;

                    $selectLexConflitante->execute("SELECT nome FROM lexico WHERE id_lexico = " . $idLexicoConflitante);

                    $row = $selectLexConflitante->gofirst();

                    return $row["nome"];
                }
                else {
                    //Nothing should be done
                }
                
            }
            return null;
        }
    }

}
else {
    //Nothing should be done
}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarrequestConcept"))) {

    function tratarrequestConcept($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidocon WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } 
        else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_conceito = $record['id_conceito'];
                $idProject  = $record['id_projeto'];
                removeConcept($idProject , $id_conceito);
            } 
            else {

                $idProject  = $record['id_projeto'];
                $name = $record['nome'];
                $description = $record['descricao'];
                $namespace = $record['namespace'];

                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_cenario = $record['id_conceito'];
                    removeConcept($idProject , $id_conceito);
                }
                else {
                    //Nothing should be done
                }
                adicionar_conceito($idProject , $name, $description, $namespace);
            }
        }
    }

}
else {
    //Nothing should be done
}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoRelacao"))) {

    function tratarPedidoRelacao($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidorel WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } 
        else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_relacao = $record['id_relacao'];
                $idProject  = $record['id_projeto'];
                removeRelationship($idProject , $id_relacao);
            } 
            else {

                $idProject  = $record['id_projeto'];
                $name = $record['nome'];

                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_relacao = $record['id_relacao'];
                    removeRelationship($idProject , $id_relacao);
                }
                else {
                    //Nothing should be done
                }
                adicionar_relacao($idProject , $name);
            }
        }
    }

}
else {
    //Nothing should be done
}

#############################################
#Deprecated by the author:
#Essa funcao deveria receber um id_projeto
#de forma a verificar se o gerente pertence
#a esse projeto.Ela so verifica atualmente
#se a pessoa e um gerente.
#############################################
if (!(function_exists("verificaGerente"))) {

    function verificaGerente($id_usuario) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $select->execute("SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario");
        if ($select->getntuples() == 0) {
            return 0;
        }
        else {
            return 1;
        }
    }

}
else {
    //Nothing should be done
}

#############################################
# Formata Data
# Recebe YYY-DD-MM
# Retorna DD-MM-YYYY
#############################################
if (!(function_exists("formataData"))) {

    function formataData($date) {

        $novaData = substr($date, 8, 9) .
                substr($date, 4, 4) .
                substr($date, 0, 4);
        return $novaData;
    }

}
else {
    //Nothing should be done
}





// Retorna TRUE ssse $id_usuario eh admin de $idProject 
if (!(function_exists("is_admin"))) {

    function is_admin($id_usuario, $idProject ) {
        $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $commandSQL = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $idProject 
              AND gerente = 1";
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($requestResultSQL));
    }

}
else {
    //Nothing should be done
}

// Retorna TRUE ssse $id_usuario tem permissao sobre $idProject 
if (!(function_exists("permissionCheckToProject"))) {

    function  permissionCheckToProject($id_usuario, $idProject ) {
        $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $commandSQL = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $idProject ";
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($requestResultSQL));
    }

}
else {
    //Nothing should be done
}

###################################################################
# Verifica se um determinado usuario e gerente de um determinado
# projeto
# Recebe o id do projeto. (1.1)
# Faz um select para pegar o resultArray da tabela Participa.(1.2)
# Se o resultArray for nao nulo: devolvemos TRUE(1);(1.3)
# Se o resultArray for nulo: devolvemos False(0);(1.4)
###################################################################

function verificaGerente($id_usuario, $idProject ) {
    $ret = 0;
    $commandSQL = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
    $qr = mysql_query($commandSQL) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);

    if ($resultArray != false) {

        $ret = 1;
    }
    else {
        //Nothing should be done
    }
    return $ret;
}

###################################################################
# Remove um determinado projeto da base de dados
# Recebe o id do projeto. (1.1)
# Apaga os valores da tabela pedidocen que possuam o id do projeto enviado (1.2)
# Apaga os valores da tabela pedidolex que possuam o id do projeto enviado (1.3)
# Faz um SELECT para saber quais l�xico pertencem ao projeto de id_projeto (1.4)
# Apaga os valores da tabela lextolex que possuam possuam lexico do projeto (1.5)
# Apaga os valores da tabela centolex que possuam possuam lexico do projeto (1.6)
# Apaga os valores da tabela sinonimo que possuam possuam o id do projeto (1.7)
# Apaga os valores da tabela lexico que possuam o id do projeto enviado (1.8)
# Faz um SELECT para saber quais cenario pertencem ao projeto de id_projeto (1.9)
# Apaga os valores da tabela centocen que possuam possuam cenarios do projeto (2.0)
# Apaga os valores da tabela centolex que possuam possuam cenarios do projeto (2.1)
# Apaga os valores da tabela cenario que possuam o id do projeto enviado (2.2)
# Apaga os valores da tabela participa que possuam o id do projeto enviado (2.3)
# Apaga os valores da tabela publicacao que possuam o id do projeto enviado (2.4)
# Apaga os valores da tabela projeto que possuam o id do projeto enviado (2.5)
#
###################################################################

function removeProjeto($idProject ) {
    $SgbdConnectStatus = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os pedidos de cenario
    $qv = "Delete FROM pedidocen WHERE id_projeto = '$idProject ' ";
    $deletaPedidoCenario = mysql_query($qv) or die("Erro ao apagar pedidos de cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os pedidos de lexico
    $qv = "Delete FROM pedidolex WHERE id_projeto = '$idProject ' ";
    $deletaPedidoLexico = mysql_query($qv) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os lexicos //verificar lextolex!!!
    $qv = "SELECT * FROM lexico WHERE id_projeto = '$idProject ' ";
    $qvr = mysql_query($qv) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    while ($result = mysql_fetch_array($qvr)) {
        $id_lexico = $result['id_lexico']; //seleciona um lexico

        $qv = "Delete FROM lextolex WHERE id_lexico_from = '$id_lexico' OR id_lexico_to = '$id_lexico' ";
        $deletaLextoLe = mysql_query($qv) or die("Erro ao apagar pedidos do lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM centolex WHERE id_lexico = '$id_lexico'";
        $deletacentolex = mysql_query($qv) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        //$qv = "Delete FROM sinonimo WHERE id_lexico = '$id_lexico'";
        //$deletacentolex = mysql_query($qv) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM sinonimo WHERE id_projeto = '$idProject '";
        $deletacentolex = mysql_query($qv) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }

    $qv = "Delete FROM lexico WHERE id_projeto = '$idProject ' ";
    $deletaLexico = mysql_query($qv) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remove os cenarios
    $qv = "SELECT * FROM cenario WHERE id_projeto = '$idProject ' ";
    $qvr = mysql_query($qv) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArrayCenario = mysql_fetch_array($qvr);

    while ($result = mysql_fetch_array($qvr)) {
        $id_lexico = $result['id_cenario']; //seleciona um lexico

        $qv = "Delete FROM centocen WHERE id_cenario_from = '$id_cenario' OR id_cenario_to = '$id_cenario' ";
        $deletaCentoCen = mysql_query($qv) or die("Erro ao apagar pedidos do centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM centolex WHERE id_cenario = '$id_cenario'";
        $deletaLextoLe = mysql_query($qv) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }

    $qv = "Delete FROM cenario WHERE id_projeto = '$idProject ' ";
    $deletaLexico = mysql_query($qv) or die("Erro ao apagar pedidos do cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remove participants
    $qv = "Delete FROM participa WHERE id_projeto = '$idProject ' ";
    $deletaParticipantes = mysql_query($qv) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remove publication
    $qv = "Delete FROM publicacao WHERE id_projeto = '$idProject ' ";
    $deletaPublicacao = mysql_query($qv) or die("Erro ao apagar no publicacao<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remove project
    $qv = "Delete FROM projeto WHERE id_projeto = '$idProject ' ";
    $deletaProjeto = mysql_query($qv) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
}
?>
