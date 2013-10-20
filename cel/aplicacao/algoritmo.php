<?php
session_start();

include 'estruturas.php';
include_once 'auxiliar_algoritmo.php';

?>
<html>
    <head>
        <title>Algoritmo de Gera&ccedil;&atilde;o de Ontologias</title>
        <style>

        </style>
    </head>
    <body>
        <?php

        
        function verifica_consistencia() {
            return TRUE;
        }

        function compara_arrays($array1, $array2) {

            if (count($array1) != count($array2)) {
                return FALSE;
            }
            else {
                //Nothing should be done
            }
           
            foreach ($array1 as $key => $elem) {
                if ($elem->verbo != $array2[$key]->verbo) {
                    return FALSE;
                }
                else {
                    //Nothing should be done
                }
            }
            return TRUE;
        }

        /*
        Scenario: Assemble hierarchy.
        Objective: Assemble concept hierarchy.
        Context: Organization ontology in progress.
        Resources: System, concept, subconcepts list and list of concepts.
        Episodes:
            - For each subconceito
        Search the list of your key concepts.
        Add subconceito as a key concept.
         */
        
        function montar_hierarquia($conceito, $nova_lista, $list) {
            
            foreach ($nova_lista as $subConceito) {
                $key = existe_conceito($subConceito, $list);
                $conceito->subconceitos[] = $subConceito;
            }
        }

        /*
        Scenario: Translate the terms of lexical classified as subject and object. Objective: To translate the terms of lexical classified as subject and object.
        Context: Algorithm translation started.
        Actors: User.
        Resources: System, list of subjects and objects, concepts list, list of relations.
        Episodes:
           - For each element of the list of subjects and objects
          * Create new concept with the same name and description like the notion of the element.
          * For each element of the impact
           . Check with the User the existence of the impact on the list of relations.
           . If not, this impact include the list of relations.
           . Include this relation to list in the concept relations.
           . Discover
          * Include the concept in the list of concepts.
          * Check consistency.
         */

        function traduz_sujeito_objeto($lista_de_sujeito_e_objeto, $concepts, $relations, $axioms) {

            for (; $_SESSION["index1"] < count($lista_de_sujeito_e_objeto); ++$_SESSION["index1"]) {

                $sujeito = $lista_de_sujeito_e_objeto[$_SESSION["index1"]];

                if (!isset($_SESSION["conceito"])) {
                    $_SESSION["salvar"] = "TRUE";
                    $_SESSION["conceito"] = new concept($sujeito->nome, $sujeito->nocao);
                    $_SESSION["conceito"]->namespace = "proprio";
                }
                else {
                    $_SESSION["salvar"] = "FALSE";
                }

                for (; $_SESSION["index2"] < count($sujeito->impacto); ++$_SESSION["index2"]) {

                    $imp = $sujeito->impacto[$_SESSION["index2"]];

                    if (trim($imp) == ""){
                        continue;
                    }
                    else {
                        //Nothing should be done
                    }
                    if (!isset($_SESSION["verbos_selecionados"])){
                        $_SESSION["verbos_selecionados"] = array();
                    }
                    else {
                        //Nothing should be done
                    }

                    if (!isset($_SESSION["impact"])) {
                        $_SESSION["impact"] = array();
                        $_SESSION["finish_insert"] = FALSE;
                    }
                    else {
                        //Nothing should be done
                    }
                    while (!$_SESSION["finish_insert"]) {
                        
                        if (!isset($_SESSION["exist"])) {
                            asort($relations);
                            $_SESSION["lista"] = $relations;
                            $_SESSION["nome1"] = $imp;
                            $_SESSION["nome2"] = $sujeito;
                            $_SESSION["job"] = "exist";
                            ?>
                            <SCRIPT language='javascript'>
                                document.location = "auxiliar_interface.php";
                            </SCRIPT>



                            <?php
                            exit();
                        }
                        else {
                            //Nothing should be done
                        }



                        if ($_POST["existe"] == "FALSE") {

                            $name = strtolower($_POST["nome"]);
                            session_unregister("exist");
                            if ((count($_SESSION["verbos_selecionados"]) != 0) && (array_search($name, $_SESSION["verbos_selecionados"]) !== null)) {
                                continue;
                            }
                            else {
                                //Nothing should be done
                            }
                            $_SESSION["verbos_selecionados"][] = $name;
                            $i = array_search($name, $relations);
                            if ($i === false) {
                                $_SESSION["impact"][] = (array_push($relations, $name) - 1);
                            } 
                            else {
                                $_SESSION["impact"][] = $i;
                            }
                        } 
                        else if ($_POST["indice"] != -1) {
                            session_unregister("exist");
                            if ((count($_SESSION["verbos_selecionados"]) != 0) && array_search($relations[$_POST["indice"]], $_SESSION["verbos_selecionados"]) !== false) {
                                continue;
                            }
                            else {
                                //Nothing should be done
                            }
                            $_SESSION["verbos_selecionados"][] = $relations[$_POST["indice"]];
                            $_SESSION["impact"][] = $_POST["indice"];
                        } 
                        else {
                            $_SESSION["finish_insert"] = TRUE;
                        }
                    }

                    if (!isset($_SESSION["ind"])) {
                        $_SESSION["ind"] = 0;
                    }
                    else {
                        //Nothing should be done
                    }

                    $_SESSION["verbos_selecionados"] = array();

                    for (; $_SESSION["ind"] < count($_SESSION["impact"]); ++$_SESSION["ind"]) {

                        if (!isset($_SESSION["predicados_selecionados"])){
                            $_SESSION["predicados_selecionados"] = array();
                        }
                        else {
                            //Nothing should be done
                        }

                        $indice = $_SESSION["impact"][$_SESSION["ind"]];
                        $_SESSION["finish_relation"] = FALSE;
                        
                        while (!$_SESSION["finish_relation"]) {
                            
                            if (!isset($_SESSION["insert_relation"])) {
                                
                                asort($concepts);
                                $_SESSION["lista"] = $concepts;
                                $_SESSION["nome1"] = $relations[$indice];
                                $_SESSION["nome2"] = $sujeito->nome;
                                $_SESSION["nome3"] = $imp;
                                $_SESSION["job"] = "insert_relation";
                                
                                ?>
                                <SCRIPT language='javascript'>
                                    document.location = "auxiliar_interface.php";
                                </SCRIPT>
                                <?php
                                
                                exit();
                            } 
                            else if (isset($_SESSION["nome2"])) {

                                session_unregister("nome2");
                                session_unregister("nome3");
                                session_unregister("insert_relation");


                                if ($_POST["existe"] == "FALSE") {
                                   
                                    $conceito = strtolower($_POST["nome"]);

                                    if ((count($_SESSION["predicados_selecionados"]) != 0) && (array_search($conceito, $_SESSION["predicados_selecionados"]) !== null)) {
                                        continue;
                                    }
                                    else {
                                        //Nothing should be done
                                    }
                                    $_SESSION["predicados_selecionados"][] = $conceito;

                                    if (existe_conceito($conceito, $_SESSION['lista_de_conceitos']) == -1) {
                                        
                                        if (existe_conceito($conceito, $lista_de_sujeito_e_objeto) == -1) {
                                            $nconc = new concept($conceito, "");
                                            $nconc->namespace = $_POST['namespace'];
                                            $_SESSION['lista_de_conceitos'][] = $nconc;
                                        }
                                        else {
                                            //Nothing should be done
                                        }
                                    }
                                    else {
                                        //Nothing should be done
                                    }

                                    $indiceRelacao = existe_relacao($_SESSION['nome1'], $_SESSION['conceito']->relacoes);
                                   
                                    if ($indiceRelacao != -1) {
                                        if (array_search($conceito, $_SESSION["conceito"]->relacoes[$indiceRelacao]->predicados) === false){
                                            $_SESSION["conceito"]->relacoes[$indiceRelacao]->predicados[] = $conceito;
                                        }
                                        else {
                                            //Nothing should be done
                                        }
                                    }
                                    else {
                                        $_SESSION["conceito"]->relacoes[] = new relationshipBetweenConcepts($conceito, $_SESSION["nome1"]);
                                    }
                                } 
                                else if ($_POST["indice"] != "-1") {
                                    
                                    $conceito = $concepts[$_POST["indice"]]->nome;
                                    if ((count($_SESSION["predicados_selecionados"]) != 0) && (array_search($conceito, $_SESSION["predicados_selecionados"]) !== null)) {
                                        continue;
                                    }
                                    else {
                                        //Nothing should be done
                                    }

                                    $_SESSION["predicados_selecionados"][] = $conceito;

                                    $indiceRelacao = existe_relacao($_SESSION['nome1'], $_SESSION['conceito']->relacoes);
                                    
                                    if ($indiceRelacao != -1) {
                                        
                                        if (array_search($conceito, $_SESSION["conceito"]->relacoes[$indiceRelacao]->predicados) === false){
                                            $_SESSION["conceito"]->relacoes[$indiceRelacao]->predicados[] = $conceito;
                                        }
                                        else {
                                            //Nothing should be done
                                        }
                                    }
                                    else {
                                        $_SESSION["conceito"]->relacoes[] = new relationshipBetweenConcepts($conceito, $_SESSION["nome1"]);
                                    }
                                    
                                } 
                                else {
                                    $_SESSION["finish_relation"] = TRUE;
                                }
                            }
                            else {
                                //Nothing should be done
                            }
                        }
                        $_SESSION["predicados_selecionados"] = array();
                    }


                    /* Unregister a global variable from the current session */
                    session_unregister("exist");
                    session_unregister("impact");
                    session_unregister("ind");
                    session_unregister("insert_relation");
                    session_unregister("insert");
                    session_unregister("verbos_selecionados");
                    session_unregister("predicados_selecionados");
                }

                $finish_disjoint = FALSE;
                
                while (!$finish_disjoint) {
                    
                    if (!isset($_SESSION["axiomas_selecionados"])){
                        $_SESSION["axiomas_selecionados"] = array();
                    }
                    else {
                        //Nothing should be done
                    }

                    if (!isset($_SESSION["disjoint"])) {
                        $_SESSION["lista"] = $concepts;
                        $_SESSION["nome1"] = $_SESSION["conceito"]->nome;
                        $_SESSION["job"] = "disjoint";
                        ?>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                        <?php
                        exit();
                    }
                    else {
                        //Nothing should be done
                    }
                    
                    if ($_POST["existe"] == "TRUE") {
                        $axiom = $_SESSION["conceito"]->nome . " disjoint " . strtolower($_POST["nome"]);
                        
                        if (array_search($axiom, $axioms) === false) {
                            $axioms[] = $axiom;
                            $_SESSION["axiomas_selecionados"][] = $axiom;
                        }
                        else {
                            //Nothing should be done
                        }
                        
                        session_unregister("disjoint");
                    } 
                    else {
                        $finish_disjoint = TRUE;
                    }
                }
                
                $_SESSION["axiomas_selecionados"] = array();                          
                $concepts[] = $_SESSION["conceito"];
                asort($concepts);

                if (!verifica_consistencia()) {
                    exit();
                }
                else {
                    //Nothing should be done
                }

                session_unregister("insert");
                session_unregister("disjoint");
                session_unregister("exist");
                session_unregister("insert_relation");
                session_unregister("conceito");
                $_SESSION["index2"] = 0;
            }
            
            $_SESSION["index1"] = 0;
            session_unregister("finish_insert");
            session_unregister("finish_relation");
        }

        /*
        Scenario: Translate the terms of lexical classified as a verb.
        Objective: To translate the terms of lexical classified as verb.
        Context: Algorithm translation started.
        Actors: User.
        Resources: System, verb list and list of relations.
        Episodes:
           - For each element of the list of verb
          * Check with the user if there is existence of the verb in the list of relations.
          * If not, include this in the list of verb relations.
          * Check consistency.
        */

        function traduz_verbos($verbos, $relations) {
            for (; $_SESSION["index3"] < count($verbos); ++$_SESSION["index3"]) {

                $verb = $verbos[$_SESSION["index3"]];


                if (!isset($_SESSION["exist"])) {
                    $_SESSION["salvar"] = "TRUE";
                    asort($relations);
                    $_SESSION["lista"] = $relations;
                    $_SESSION["nome1"] = $verb->nome;
                    $_SESSION["nome2"] = $verb;
                    $_SESSION["job"] = "exist";
                    ?>
                    <SCRIPT language='javascript'>
                        document.location = "auxiliar_interface.php";
                    </SCRIPT>
                    <?php
                    exit();
                }
                else {
                    //Nothing should be done
                }

                if ($_POST["existe"] == "FALSE") {
                    $name = strtolower($_POST["nome"]);
                    if (array_search($name, $relations) === false)
                        array_push($relations, $name);
                }
                else {
                    //Nothing should be done
                }

                if (!verifica_consistencia()) {
                    exit();
                }
                else {
                    //Nothing should be done
                }

                session_unregister("exist");
                session_unregister("insert");
            }
            $_SESSION["index3"] = 0;
        }

        /*
        Scenario: Translate the terms of lexical classified as a state.
        Objective: To translate the terms of lexical classified as a state.
        Context: translation algorithm started.
        Actors: User.
        Resources: System status list, list of concepts, list of relations, list of axioms.
        Episodes:
           - For each element of the list of state
           * For each element of the impact
            . Discover
          * Check if the element has central importance in the ontology.
          * If yes, translate as if it were a subject / object.
          * Otherwise, translate as if it were a verb.
          * Check consistency.
        */

        function traduz_estados($estados, $concepts, $relations, $axioms) {
            for (; $_SESSION["index4"] < count($estados); ++$_SESSION["index4"]) {

                $estado = $estados[$_SESSION["index4"]];


                $aux = array($estado);

                if (!isset($_SESSION["main_subject"])) {

                    $_SESSION["nome1"] = $estado->nome;
                    $_SESSION["nome2"] = $estado;
                    $_SESSION["job"] = "main_subject";
                    ?>
                    <p>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                    <?php
                    exit();

                }
                else {
                    //Nothing should be done
                }


                if (!isset($_SESSION["translate"])) {
                    if ($_POST["main_subject"] == "TRUE") {
                        $_SESSION["translate"] = 1;
                        traduz_sujeito_objeto($aux, &$concepts, &$relations, &$axioms);
                    }
                    else {
                        $_SESSION["translate"] = 2;
                        traduz_verbos($aux, &$relations);
                    }
                } 
                else if ($_SESSION["translate"] == 1) {
                    traduz_sujeito_objeto($aux, &$concepts, &$relations);
                } 
                else if ($_SESSION["translate"] == 2) {
                    traduz_verbos($aux, &$relations);
                }
                else {
                    //Nothing should be done
                }



                if (!verifica_consistencia()) {
                    exit();
                }
                else {
                    //Nothing should be done
                }

                session_unregister("main_subject");
                session_unregister("translate");
            }
            $_SESSION["index4"] = 0;
        }

        /*
          Cenario:	Organizar ontologia.
          Objetivo:	Organizar ontologia.
          Contexto:	Listas de conceitos, relacoes e axiomas prontas.
          Atores:		Usuario.
          Recursos:	Sistema, lista de conceitos, lista de relacoes, lista de axiomas.
          Episodios:
          - Faz-se uma copia da lista de conceitos.
          - Para cada elemento x da lista de conceitos
         * Cria-se uma nova lista contendo o elemento x.
         * Para cada elemento subsequente y
          . Compara as relacoes dos elementos x e y.
          . Caso possuam as mesmas relacoes, adiciona-se o elemento y a nova lista que ja contem x.
          . Retira-se y da lista de conceitos.
         * Retira-se x da lista de conceitos.
         * Caso a nova lista tenha mais de dois elementos, ou seja, caso x compartilhe as mesmas
          relacoes com outro termo
          . Procura por um elemento na lista de conceitos que faca referencia a todos os elementos
          da nova lista.
          . Caso exista tal elemento, montar hierarquia.
          . Caso nao exista, descobrir.
         * Verificar consistencia.
          - Restaurar lista de conceitos.
         */

        function organizar_ontologia($concepts, $relations, $axioms) {
            $_SESSION["salvar"] = "TRUE";
            
            /* for( ; $_SESSION["index5"] < count($concepts); ++$_SESSION["index5"] )
              {
              $_SESSION["salvar"] = "TRUE";

              $conc = $concepts[$_SESSION["index5"]];

              if( count($conc->subconceitos) > 0 )
              {
              if( $conc->subconceitos[0] == -1 )
              {
              array_splice($conc->subconceitos, 0, 1);
              continue;
              }
              }

              $conc->subconceitos[0] = -1;
              $key = $_SESSION["index5"];

              $nova_lista_de_conceitos = array($conc);

              for( $i = $key+1; $i < count($concepts); ++$i )
              {
              if (compara_arrays($conc->relacoes, $concepts[$i]->relacoes))
              {
              $concepts[$i]->subconceitos[0] = -1;
              $nova_lista_de_conceitos[] = $concepts[$i];
              }
              }
             */
            
            //if( count($nova_lista_de_conceitos) >= 2 )

            $finish_relation = FALSE;
            while (!$finish_relation) {
                $indice = 0;

                if (!isset($_SESSION["reference"])) {

                    $_SESSION["lista"] = $concepts; 
                    $_SESSION["job"] = "reference";
                    ?>
                        <a href="auxiliar_interface.php">auxiliar_interface</a>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                        <?php
                        exit();
                    }
                    else {
                        //Nothing should be done
                    }

                    session_unregister("reference");

                    $achou = FALSE;

                    if (isset($_POST['pai'])) {
                        $pai_nome = $_POST['pai'];
                        $key2 = existe_conceito($pai_nome, $concepts);
                        $filhos = array();
                        foreach ($concepts as $key3 => $filho) {
                            $filho_nome = trim($filho->nome);
                            if (isset($_POST[$key3])) {
                                $filhos[] = $filho_nome;
                            }
                            else {
                                //Nothing should be done
                            }
                        }
                        if (count($filhos) > 0) {
                            montar_hierarquia(&$concepts[$key2], $filhos, $concepts);
                            $achou = true;
                        }
                        else {
                            //Nothing should be done
                        }
                    } 
                    else {
                        $finish_relation = true;
                    }


                    if (!$achou) {
                        //Trying to mount hierarchy using minimal vocabulary.
                    }
                    else {
                        //Nothing should be done
                    }
                }

                if (!verifica_consistencia()) {
                    exit();
                }
                else {
                    //Nothing should be done
                }
 
            }

            /*
            Scenario: Translate lexicon to ontology.
            Objective: Translate Lexicon to Ontology.
            Context: There are lists of elements of the lexicon organized by type, and these elements are consistent.
            Actors: User.
            Resources: System elements of the lexicon lists organized by type and lists of ontology elements.
            Episodes:
               - Create list of empty concepts.
               - Create empty list of relations.
               - Create empty list of axioms.
               - Translate the terms of lexical classified as subject and object.
               - Translate the terms of lexical classified as a verb.
               - Translate the terms of lexical classified as a state.
               - Organize the ontology.

             */

            function traduz() {
                //Checks if the lists were initiated.
                if (isset($_SESSION["lista_de_sujeito"]) && isset($_SESSION["lista_de_objeto"]) &&
                        isset($_SESSION["lista_de_verbo"]) && isset($_SESSION["lista_de_estado"]) &&
                        isset($_SESSION["lista_de_conceitos"]) && isset($_SESSION["lista_de_relacoes"]) &&
                        isset($_SESSION["lista_de_axiomas"])) {
                    $sujeitos = $_SESSION["lista_de_sujeito"];
                    $objetos = $_SESSION["lista_de_objeto"];
                    $verbos = $_SESSION["lista_de_verbo"];
                    $estados = $_SESSION["lista_de_estado"];
                }
                else {
                    echo "ERRO! <br>";
                    exit();
                }

                $lista_de_sujeito_e_objeto = array_merge($sujeitos, $objetos);
                sort($lista_de_sujeito_e_objeto);
                $_SESSION['lista_de_sujeito_e_objeto'] = $lista_de_sujeito_e_objeto;


                if ($_SESSION["funcao"] == "sujeito_objeto") {
                    traduz_sujeito_objeto($lista_de_sujeito_e_objeto, &$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "verbo";
                }
                else {
                    //Nothing should be done
                }

                if ($_SESSION["funcao"] == "verbo") {
                    traduz_verbos($verbos, &$_SESSION["lista_de_relacoes"]);
                    $_SESSION["funcao"] = "estado";
                }
                else {
                    //Nothing should be done
                }

                if ($_SESSION["funcao"] == "estado") {
                    traduz_estados($estados, &$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "organiza";
                }
                else {
                    //Nothing should be done
                }

                if ($_SESSION["funcao"] == "organiza") {
                    organizar_ontologia(&$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "fim";
                }
                else {
                    //Nothing should be done
                }


                //Imprime Resultados
                /*
                  print("CONCEITOS: <br>");
                  foreach( $_SESSION["lista_de_conceitos"] as $con)
                  {
                  echo "$con->nome --> $con->descricao ";
                  foreach($con->relacoes as $rel)
                  {

                  }
                  echo "<br>";
                  }

                  print("RELACOES: <br>");
                  print_r($_SESSION["lista_de_relacoes"]);
                  echo "<br>";

                  print("AXIOMAS: <br>");
                  print_r($_SESSION["lista_de_axiomas"]);
                  echo "<br>";
                 */
                echo 'O processo de gera&ccedil;&atilde;o de Ontologias foi conclu&iacute;do com sucesso!<br>
	N&atilde;o esque&ccedil;a de clicar em Salvar.';
                ?>
            <p>
            <form method="POST" action="auxiliar_bd.php">
                <input type="hidden" value="TRUE" name="save" size="20" >
                <input type="submit" value="SALVAR">
            </form>
        </p>
                <?php
            }

            traduz();
            ?>


</body>
</html>