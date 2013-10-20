<?php
session_start();

include("funcoes_genericas.php");
include_once("coloca_links.php");
include("httprequest.inc");
include_once("bd.inc");
include_once("seguranca.php");

checkUserAuthentication("index.php");

if (isset($_POST['flag'])) {
    $flag = "ON";
} else {
    $flag = "OFF";
}
?>

<?php

/* gerador_xml.php
Given the base and the id of the project , it generates the xml scenarios and lexicons .
Scenario - Generate XML Reports
Objective : Allow the administrator to generate reports in XML format to a project , identified by date .
Context : Manager to generate a report for a project which is administrator.
Precondition : Login and registered design .
Actors : Administrator
Features : System , report data and data registered the project database.
Episodes : The system provides the administrator must provide a screen where data
          the report for later identificção such as date and version .
          To effect the generation of the report , just click Generate .
          Restriction : The system executes two validations :
                      - If the date is valid .
                      - If there are scenarios and lexicons on dates equal to or earlier .
          Successfully generating the report from the data registered design ,
          the system gives the administrator the viewing screen of the report created XML .
          Restriction: Retrieve XML data from the database and by an XSL transform to display .*/

if (!(function_exists("gerar_xml"))) {

    function gerar_xml($bd, $idProject , $data_pesquisa, $flag_formatado) {
        $xml_resultante = "";
        $emptyVector = array();

        if ($flag_formatado == "ON") {
            $xml_resultante = "";
            $xml_resultante = $xml_resultante . "<?xml-stylesheet type='text/xsl' href='projeto.xsl'?>\n";
        }
        $xml_resultante = $xml_resultante . "<projeto>\n";

        $qry_nome = "SELECT nome
                     FROM projeto
                     WHERE id_projeto = " . $idProject ;
        $tb_nome = mysql_query($qry_nome) or die("Erro ao enviar a query de selecao.");
		
        $xml_resultante = $xml_resultante . "<nome>" . mysql_result($tb_nome, 0) . "</nome>\n";

        $qry_cenario = "SELECT id_cenario, titulo, objetivo, contexto, atores,
                               recursos, episodios, excecao
                        FROM cenario
                        WHERE (id_projeto = " . $idProject . ") 
                        AND (data <=" . " '" . $data_pesquisa . "'" . ")
                        ORDER BY id_cenario,data DESC";

        $tb_cenario = mysql_query($qry_cenario) or die("Erro ao enviar a query de selecao.");

        $primeiro = true;

        $id_temp = "";

        $vetor_todos_lexicos = load_ArrayLexicon($idProject , 0, false);

        while ($row = mysql_fetch_row($tb_cenario)) {
            $id_cenario = "<ID>" . $row[0] . "</ID>";
            $idCurrentScenario = $row[0]; 
            $vector_scenarios = loadScenariosVector($idProject , $idCurrentScenario, true);

            if (($id_temp != $id_cenario) or (primeiro)) {
                $title = '<titulo id="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . ucwords(strip_tags($row[1])) . '</titulo>';

                $objective = "<objetivo>" . "<sentenca>" . gera_xml_links(mountLinks($row[2], $vetor_todos_lexicos, $emptyVector)) . "</sentenca>" . "<PT/>" . "</objetivo>";

                $context = "<contexto>" . "<sentenca>" . gera_xml_links(mountLinks($row[3], $vetor_todos_lexicos, $vector_scenarios)) . "</sentenca>" . "<PT/>" . "</contexto>";

                $actors = "<atores>" . "<sentenca>" . gera_xml_links(mountLinks($row[4], $vetor_todos_lexicos, $emptyVector)) . "</sentenca>" . "<PT/>" . "</atores>";

                $resources = "<recursos>" . "<sentenca>" . gera_xml_links(mountLinks($row[5], $vetor_todos_lexicos, $emptyVector)) . "</sentenca>" . "<PT/>" . "</recursos>";

                $exception = "<excecao>" . "<sentenca>" . gera_xml_links(mountLinks($row[7], $vetor_todos_lexicos, $emptyVector)) . "</sentenca>" . "<PT/>" . "</excecao>";

                $episodes = "<episodios>" . "<sentenca>" . gera_xml_links(mountLinks($row[6], $vetor_todos_lexicos, $vector_scenarios)) . "</sentenca>" . "<PT/>" . "</episodios>";

                $xml_resultante = $xml_resultante . "<cenario>\n";

                $xml_resultante = $xml_resultante . "$title\n";

                $xml_resultante = $xml_resultante . "$objective\n";

                $xml_resultante = $xml_resultante . "$context\n";

                $xml_resultante = $xml_resultante . "$actors\n";

                $xml_resultante = $xml_resultante . "$resources\n";

                $xml_resultante = $xml_resultante . "$episodes\n";

                $xml_resultante = $xml_resultante . "$exception\n";

                $xml_resultante = $xml_resultante . "</cenario>\n";

                $primeiro = false;

                //??$id_temp = id_cenario;
            }
            else {
                // Nothing should be done
            }
        }

        $qry_lexico = "SELECT id_lexico, nome, nocao, impacto
                       FROM lexico
                       WHERE (id_projeto = " . $idProject  .")
                       AND (data <=" . " '" . $data_pesquisa . "'" . ")
                       ORDER BY id_lexico,data DESC";

        $tb_lexico = mysql_query($qry_lexico) or die("Erro ao enviar a query de selecao.");

        $primeiro = true;

        $id_temp = "";

        while ($row = mysql_fetch_row($tb_lexico)) {
            $vector_lexicons = load_ArrayLexicon($idProject , $row[0], true);
            quicksort($vector_lexicons, 0, count($vector_lexicons) - 1, 'lexico');
            $id_lexico = "<ID>" . $row[0] . "</ID>";
            
            if (($id_temp != $id_lexico) or (primeiro)) {

                $name = '<nome_simbolo id="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . '<texto>' . ucwords(strip_tags($row[1])) . '</texto>' . '</nome_simbolo>';

                $querySinonimo = "SELECT nome 
				  FROM sinonimo
				  WHERE (id_projeto = " . $idProject  . ") 
				  AND (id_lexico = " . $row[0] . " )";

                $resultSinonimos = mysql_query($querySinonimo) or die("Erro ao enviar a query de selecao de sinonimos.");

                $sinonimo = "<sinonimos>";

                while ($rowSin = mysql_fetch_row($resultSinonimos)) {
                    $sinonimo .= "<sinonimo>" . $rowSin[0] . "</sinonimo>";
                }
                $sinonimo .= "</sinonimos>";

                $notion = "<nocao>" . "<sentenca>" . gera_xml_links(mountLinks($row[2], $vector_lexicons, $emptyVector)) . "<PT/>" . "</sentenca>" . "</nocao>";

                $impact = "<impacto>" . "<sentenca>" . gera_xml_links(mountLinks($row[3], $vector_lexicons, $emptyVector)) . "<PT/>" . "</sentenca>" . "</impacto>";

                $xml_resultante = $xml_resultante . "<lexico>\n";

                $xml_resultante = $xml_resultante . "$name\n";

                $xml_resultante = $xml_resultante . "$sinonimo\n";

                $xml_resultante = $xml_resultante . "$notion\n";

                $xml_resultante = $xml_resultante . "$impact\n";

                $xml_resultante = $xml_resultante . "</lexico>\n";

                $primeiro = false;
            }
            else{
                // Nothing should be done
            }
        }

        $xml_resultante = $xml_resultante . "</projeto>\n";

        return $xml_resultante;
    }
}

/*
Scenario - Generate links created in XML Reports
Objective: Allow the reports generated in XML format have terms with links to their respective lexicons
Context: Manager to generate an XML report for one of the projects which is administrator.
Precondition: Login, registered design and access to the database.
Actors: System
Features: System, sentences to be linked, registered data design, database.
Episodes: The system receives a sentence with the tags themselves the C & L and returns the 
 HTML link code equivalent to the lexicons cadatrados system.*/

/*    

Lexicons :

     Function: gera_xml_links
     Description : Analyzes received a sentence in order to identify the tags used in the C & L link to the lexicons and transform XML into links .
     Synonyms: -
     example :
        INPUT : < ! - CL : tam :2 - > <a title="Lexico" href="main.php?t=l&id=228"> free software < / a>
                 < ! --/CL-- >
        OUTPUT : <a title="Lexico" href="main.php?t=l&id=228"> < = text referencia_lexico software
                free > free software < / text > < / a>

     Variable: $ sentence
     Description : Stores the last expressed by argument to be shifted toward link.
     Synonyms: -
     Example : < ! - CL : tam :2 - > <a title="Lexico" href="main.php?t=l&id=228"> free software
               < / a> < ! --/CL-- >

     Variable: $ regex
     Description : Stores the pattern to be used to separate the sentence .
     Synonyms: -
     Example : " / ( < ! - CL : tam : \ d + - > ( <a[^> ] * ? \ > ) 
                   ( [ ^ < ] * ? ) < \ / A> < ! - \ / CL -> ) / mi "

     Variable: $ vetor_texto
     Description : Array that stores word for word senteaa be linked without the tag .
     Synonyms: -
     Example : $ vetor_texto [ 0 ] = > software
                 $ vetor_texto [ 1 ] = > free

     Variable: $ inside_tag
     Description : Determines if the analysis is being done inside or outside of the tag
     Synonyms: -
     Example : false

     Variable: $ tamanho_vetor_texto
     Description : Stores anmero of words that are in the array $ vetor_texto .
     Synonyms: -
     Example : 2

     Variable : $ i
     Description : The variable used as a counter for general use .
     Synonyms: -
     Example : -

     Variable : $ match
     Description : Stores the value 1 if the string " / href = " main.php \ ' t = ( . ) & Id = ( \ d + ? ) " / Mi"
                is found in the array $ vetor_texto . Otherwise , stores 0 .
     Synonyms: -
     Example : 0

     Variable: $ idProject
     Description : Stores the number identifier of the current project .
     Synonyms: -
     Example : 1

     Variable: $ attribute
     Description : Stores a tag that indicates the reference for lexicon
     Synonyms: -
     Example : referencia_lexico

     Variable : $ query
     Description : Stores the query to be made ​​in the database
     Synonyms: -
     Example : SELECT name FROM WHERE lexicon id_projeto = $ idProject

     Variable : $ result
    Description : Stores the result of the query against the database
     Synonyms: -
     Example : -

     Variable: $ row
     Description : Array that stores the tuple tuple the result of the consultation
     Synonyms: -
     Example : -

     Variable: $ value
     Description : Stores a tuple , replacing the accented characters by their equivalents without acentuao .
     Synonyms: -
     Example : Accent
*/


if (!(function_exists("gera_xml_links"))) {

    function gera_xml_links($sentenca) {

        if (trim($sentenca) != "") {

            $regex = "/(<a[^>]*?>)(.*?)<\/a>/";

            $vetor_texto = preg_split($regex, $sentenca, -1, PREG_SPLIT_DELIM_CAPTURE);
            $tamanho_vetor_texto = count($vetor_texto);
            $i = 0;

            while ($i < $tamanho_vetor_texto) {
                preg_match('/href="main.php\?t=(.)&id=(\d+?)"/mi', $vetor_texto[$i], $match);
                if ($match) {
                    $idProject  = $_SESSION['id_projeto_corrente'];
 
                    if ($match[1] == 'l') {

                        $vetor_texto[$i] = "";

                        $atributo = "referencia_lexico";

                        $query = "SELECT nome FROM lexico WHERE id_projeto = $idProject  AND id_lexico = $match[2] ";
                        $result = mysql_query($query) or die("Erro ao enviar a query lexico");
                        
                        $row = mysql_fetch_row($result);

                        $valor = strtr($row[0], "����������", "aaaaoooeec");

                        $vetor_texto[$i + 1] = '<texto ' . $atributo . '="' . $valor . '">' . $vetor_texto[$i + 1] . '</texto>';
                    } else if ($match[1] == 'c') {

                        $vetor_texto[$i] = "";


                        $atributo = "referencia_cenario";

                        $query = "SELECT titulo FROM cenario WHERE id_projeto = $idProject  AND id_cenario = $match[2] ";
                        $result = mysql_query($query) or die("Erro ao enviar a query cenario");
                        $row = mysql_fetch_row($result);

                        $valor = strtr($row[0], "����������", "aaaaoooeec");

                        $vetor_texto[$i + 1] = '<texto ' . $atributo . '="' . $valor . '">' . strip_tags($vetor_texto[$i + 1]) . '</texto>';
                    }

                    $i = $i + 2;
                } else {
                    if (trim($vetor_texto[$i]) != "") {
                        $vetor_texto[$i] = "<texto>" . $vetor_texto[$i] . "</texto>";
                    }

                    $i = $i + 1;
                }
            }

            return implode("", $vetor_texto);
        }
        
        return $sentenca;
    }
}
else {
    // Nothing should be done
}

?>

<?php
$idProject  = $_SESSION['id_projeto_corrente'];
$data_pesquisa = $data_ano . "-" . $data_mes . "-" . $data_dia;
$flag_formatado = $flag;

$bd_trabalho = bd_connect() or die("Erro ao conectar ao SGBD");

$qVerifica = "SELECT * FROM publicacao WHERE id_projeto = '$idProject ' AND versao = '$version' ";
$qrrVerifica = mysql_query($qVerifica);


if (!mysql_num_rows($qrrVerifica)) {

    $str_xml = gerar_xml($bd_trabalho, $idProject , $data_pesquisa, $flag_formatado);

    $xml_resultante = "<?xml version='1.0' encoding='ISO-8859-1' ?>\n" . $str_xml;

    $commandSQL = "INSERT INTO publicacao ( id_projeto, data_publicacao, versao, XML)
                 VALUES ( '$idProject ', '$data_pesquisa', '$version', '" . mysql_real_escape_string($xml_resultante) . "')";

    mysql_query($commandSQL) or die("Erro ao enviar a query INSERT do XML no banco de dados! ");
    recharge("http://pes.inf.puc-rio.br/cel/aplicacao/mostraXML.php?id_projeto=" . $idProject  . "&versao=" . $version);
} 
else {
    
    ?>
    <html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
            <p style="color: red; font-weight: bold; text-align: center">Essa vers�o j� existe!</p>
            <br>
            <br>
        <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
    </body></html>

    <?php
}

?> 
