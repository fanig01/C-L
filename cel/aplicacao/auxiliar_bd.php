<?php
session_start();

include_once 'estruturas.php';
include_once 'auxiliar_algoritmo.php';
include_once 'bd.inc';


function get_lista_de_sujeito() {
    
    $idProject  = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select * from lexico where tipo = 'sujeito' AND id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        
        $aux[] = obter_termo_do_lexico($line);
    }
    sort($aux);
    return $aux;
}

function get_lista_de_objeto() {
   
    $idProject  = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select * from lexico where tipo = 'objeto' AND id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = obter_termo_do_lexico($line);
    }
    sort($aux);
    return $aux;
}

function get_lista_de_verbo() {
    
    $idProject  = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select * from lexico where tipo = 'verbo' AND id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = obter_termo_do_lexico($line);
    }
    sort($aux);
    return $aux;
}

function get_lista_de_estado() {

    $idProject  = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select * from lexico where tipo = 'estado' AND id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = obter_termo_do_lexico($line);
    }
    sort($aux);
    return $aux;
}

function verifica_tipo() {
    
    $idProject  = $_SESSION['id_projeto'];
    /*
     This function checks whether all members of the table has a type defined lexicons. 
     If there are no records in the table define type, the function returns the records. 
     Otherwise, it returns true.
    */

    $query = "select * from lexico where tipo is null AND id_projeto='$idProject ' order by id_lexico;";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    $result2 = mysql_num_rows($result);

    $col_value = $result2;

    if ($col_value > 0) {
       
        // If there is no type defined lexicons, their id's are returned via an array
        $aux = array();

        while ($line2 = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $aux[] = $line2['id_lexico'];
        }
        mysql_free_result($result);
        return($aux);
    } 
    else {
        mysql_free_result($result);
        return(TRUE);
    }
}

function atualiza_tipo($id_lexico, $type) {
    
    $idProject  = $_SESSION['id_projeto'];
    /*
    This function updates the type of lexical $ id_lexico (integer) for $ type (string)
    and only accepts types: subject, object, verb, state and NULL.
    */

    if (!(($type != "sujeito") || ($type != "objeto") || ($type != "verbo") || ($type != "estado") || ($type != "null"))) {
        return (FALSE);
    }
    else {
        //Nothing should be done
    }
    if ($type == "null") {
        $query = "update lexico set tipo = $type where id_lexico = '$id_lexico';";
    }
    else {
        $query = "update lexico set tipo = '$type' where id_lexico = '$id_lexico';";
    }

    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    return(TRUE);
}

function obter_lexico($id_lexico) {
 
    /*
      Returns all fields of the lexicon. Each field is a position of
      array that can be indexed by field name or by an integer index.
    */   
    $idProject  = $_SESSION['id_projeto'];
    $query = "select * from lexico where id_lexico = '$id_lexico' AND id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    $line = mysql_fetch_array($result, MYSQL_BOTH);
    return($line);
}

function obter_termo_do_lexico($lexico) {
    $idProject  = $_SESSION['id_projeto'];
    $impactos = array();
    $id_lexico = $lexico['id_lexico'];
    $query = "select impacto from impacto where id_lexico = '$id_lexico'";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $impactos[] = strtolower($line['impacto']);
    }
    $termo_do_lexico = new lexiconTerm(strtolower($lexico['nome']), strtolower($lexico['nocao']), $impactos);
    return $termo_do_lexico;
}

function cadastra_impacto($id_lexico, $impact) {
    $idProject  = $_SESSION['id_projeto'];
    $query = "insert into impacto (id_lexico, impacto) values ('$id_lexico', '$impact');";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

    $query = "select * from impacto where impacto = '$impact' and id_lexico = $id_lexico;";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

    $line = mysql_fetch_array($result, MYSQL_ASSOC);
    $id_impacto = $line['id_impacto'];

    return $id_impacto;
}

//Create table to concepts (concept class)
function get_lista_de_conceitos() {
    
    $idProject  = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select * from conceito where id_projeto='$idProject ';";
    $result1 = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result1, MYSQL_BOTH)) {
        
        $conc = new concept($line['nome'], $line['descricao']);
        $conc->namespace = $line['namespace'];
        $id = $line['id_conceito'];
        $query = "select * from relacao_conceito where id_conceito = '$id' AND id_projeto='$idProject ';";
        $result2 = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
        
        while ($line2 = mysql_fetch_array($result2, MYSQL_BOTH)) {
            $idrel = $line2['id_relacao'];
            $query = "select * from relacao where id_relacao = '$idrel' AND id_projeto='$idProject ';";
            $result3 = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
            $line3 = mysql_fetch_array($result3, MYSQL_BOTH);
            $rel = $line3['nome'];
            $pred = $line2['predicado'];
            $indice = existe_relacao($rel, $conc->relacoes);
            
            if ($indice != -1) {
                $conc->relacoes[$indice]->predicados[] = $pred;
            } else {
                $conc->relacoes[] = new relationshipBetweenConcepts($pred, $rel);
            }
        }
        $aux[] = $conc;
    }
    sort($aux);
    $query = "select * from hierarquia where id_projeto='$idProject ';";
    $result1 = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    
    while ($line = mysql_fetch_array($result1, MYSQL_BOTH)) {

        $id_conceito = $line['id_conceito'];
        $query = "select * from conceito where id_conceito = '$id_conceito' AND id_projeto='$idProject ';";
        $result2 = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
        $line2 = mysql_fetch_array($result2, MYSQL_BOTH);
        $conceito_nome = $line2['nome'];

        $id_subconceito = $line['id_subconceito'];
        $query = "select * from conceito where id_conceito = '$id_subconceito' AND id_projeto='$idProject ';";
        $result2 = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
        $line2 = mysql_fetch_array($result2, MYSQL_BOTH);
        $subconceito_nome = $line2['nome'];

        foreach ($aux as $key => $conc1) {
            
            if ($conc1->nome == $conceito_nome) {
                $aux[$key]->subconceitos[] = $subconceito_nome;
            }
            else {
               //Nothing should be done 
            }
        }
    }
    return $aux;
}

//criar tabela para conceitos (class relacao_entre_conceitos)
function get_lista_de_relacoes() {
    
    $idProject  = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select nome from relacao where id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = $line['nome'];
    }
    sort($aux);
    return $aux;
}

//Create table to axioms (string)
function get_lista_de_axiomas() {
   
    $idProject  = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select axioma from axioma where id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = $line['axioma'];
    }
    sort($aux);
    return $aux;
}

//Variable function (string)
function get_funcao() {
   
    $idProject  = $_SESSION['id_projeto'];
    $query = "select valor from algoritmo where nome = 'funcao' AND id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    $line = mysql_fetch_array($result, MYSQL_BOTH);
    
    return $line['valor'];
}

//variables for index (int)
function get_indices() {
    
    $idProject  = $_SESSION['id_projeto'];
    $query = "select * from algoritmo where id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    $indice = array();

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $indice[$line['nome']] = $line['valor'];
    }
    return $indice;
}

function salvar_algoritmo() {
   
    $idProject  = $_SESSION['id_projeto'];
    $link = bd_connect();

    foreach ($_SESSION["lista_de_conceitos"] as $conceit) {
        print($conceit->nome);
        foreach ($conceit->relacoes as $rel) {
            print("<br>----$rel->verbo");
            foreach ($rel->predicados as $pred) {
                print("<br>--------$pred");
            }
        }
    }


    $query = "delete from relacao where id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from conceito where id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from relacao_conceito where id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from axioma where id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from algoritmo where id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from hierarquia where id_projeto='$idProject ';";
    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

    if (isset($_SESSION["lista_de_relacoes"])) {
        foreach ($_SESSION["lista_de_relacoes"] as $relation) {
            $query = "insert into relacao (nome, id_projeto) values ('$relation', '$idProject ');";
            $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
        }
    }
    else {
        //Nothing should be done
    }
    
    if (isset($_SESSION["lista_de_conceitos"])) {
       
        foreach ($_SESSION["lista_de_conceitos"] as $conc) {
            $query = "select id_conceito from conceito where nome = '$conc->nome' and id_projeto='$idProject ';";
            $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

            $id_conceito = 0;
            if (mysql_num_rows($result) > 0) {
                $line = mysql_fetch_array($result, MYSQL_BOTH);
                $id_conceito = $line['id_conceito'];
            }
            else {
                $query = "insert into conceito (nome,descricao,namespace, id_projeto) values ('$conc->nome', '$conc->descricao','$conc->namespace' ,'$idProject ');";
                $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

                $query = "select id_conceito from conceito where nome = '$conc->nome' and id_projeto='$idProject ';";
                $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
                $line = mysql_fetch_array($result, MYSQL_BOTH);
                $id_conceito = $line['id_conceito'];
            }


            foreach ($conc->relacoes as $relation) {
                $verb = $relation->verbo;
                $query = "select id_relacao from relacao where nome = '$verb' and id_projeto='$idProject ';";
                $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
                $line = mysql_fetch_array($result, MYSQL_BOTH);
                $id_relacao = $line['id_relacao'];
                $predicates = $relation->predicados;
                foreach ($predicates as $pred) {
                    $query = "insert into relacao_conceito (id_conceito,id_relacao,predicado,id_projeto) values ('$id_conceito', '$id_relacao', '$pred', '$idProject ');";
                    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
                }
            }
        }
        foreach ($_SESSION["lista_de_conceitos"] as $conc) {
            foreach ($conc->subconceitos as $subconceito) {
                if ($subconceito != -1) {
                    $query = "select id_conceito from conceito where nome = '$subconceito' and id_projeto='$idProject ';";
                    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
                    $line = mysql_fetch_array($result, MYSQL_BOTH);
                    $id_subconceito = $line['id_conceito'];

                    $name = $conc->nome;
                    $query = "select id_conceito from conceito where nome = '$name' and id_projeto='$idProject ';";
                    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
                    $line = mysql_fetch_array($result, MYSQL_BOTH);
                    $id_conceito = $line['id_conceito'];

                    $query = "insert into hierarquia (id_conceito,id_subconceito,id_projeto) values ('$id_conceito', '$id_subconceito','$idProject ');";
                    $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
                }
                else {
                    //Nothing should be done
                }
            }
        }
    }
    else {
        //Nothing should be done
    }
    if (isset($_SESSION["lista_de_axiomas"])) {
        foreach ($_SESSION["lista_de_axiomas"] as $axiom) {
            $query = "insert into axioma (axioma,id_projeto) values ( '$axiom','$idProject ' );";
            $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
        }
    }
    else {
        //Nothing should be done
    }
    if (isset($_SESSION["funcao"])) {
        $func = $_SESSION['funcao'];
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('funcao',";
        $query = $query . "'" . $func . "', '$idProject ' );";
        $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    }
    else {
        //Nothing should be done
    }
    if (isset($_SESSION["index1"])) {
        $query = "insert into algoritmo (nome, valor,id_projeto) values ('index1',";
        $query = $query . "'" . $_SESSION['index1'] . "', '$idProject ');";
        $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    }
    else {
        //Nothing should be done
    }
    if (isset($_SESSION["index3"])) {
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('index3',";
        $query = $query . "'" . $_SESSION['index3'] . "', '$idProject ');";
        $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    } 
    else {
        //Nothing should be done
    }
    if (isset($_SESSION["index4"])) {
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('index4',";
        $query = $query . "'" . $_SESSION['index4'] . "', '$idProject ');";
        $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    }
    else {
        //Nothing should be done
    }
    if (isset($_SESSION["index5"])) {
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('index5',";
        $query = $query . "'" . $_SESSION['index5'] . "', '$idProject ');";
        $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    }
    else {
        //Nothing should be done
    }
    mysql_close($link);

    if ($_SESSION["funcao"] != 'fim') {
        ?>
        <script>
            document.location = "auxiliar_interface.php";
        </script>
        <?php
    } 
    
    else {
        ?>
        <script>
            document.location = "algoritmo.php";
        </script>
        <?php
    }
}

if (isset($_SESSION["tipos"])) {
    session_unregister("tipos");

    include_once 'bd.inc';

    $link = bd_connect();

    $list = verifica_tipo();

    foreach ($list as $key => $term) {
        $aux = $_POST["type" . $key];
        echo ("$term, $aux <br>");
        if (!atualiza_tipo($term, $aux)) {
            echo "ERRO <br>";
        }
        else {
            //Nothing should be done
        }
    }

    mysql_close($link);
    ?>
    <script>
        document.location = "algoritmo_inicio.php";
    </script>
    <?php
}
else {
    //Nothing should be done
}

if (array_key_exists("save", $_POST)) {
    salvar_algoritmo();
}
else {
    //Nothing should be done
}
?>

<html>
    <head>
        <title>Auxiliar BD</title>
        <style>

        </style>
    </head>
    <body>
    </body>
</html>