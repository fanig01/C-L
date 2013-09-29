<?php
session_start();
include("coloca_tags_xml.php");
include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

checkUserAuthentication("index.php");

if (isset($_POST['flag'])) {
    $flag_formatado = "ON";
} 
else {
    $flag_formatado = "OFF";
}
?>

<?php

// Scenario - To generate XML reports
// Objective: Allow the administrator generate reports in XML format of a project,
//            identified for date.
// Context:  Manager wants generate a report to one projects which is administrator.
// Pre-Condition: Login, registered project.
// Actors: Administrator
// Resource: System, report's data, report's registered data, database.
// Episodes: System provides to a administrator a view where should provide the data
//           of the report for later identification, as data and version.
//           To actualize the generate, enough click in Generate.
// Restriction: System execute two validations
//              - If the date is validates.
//              - If there are scenarios and lexicons in dates equals or previous.
//              Generating with sucess the report from the registered data of the project
//              the system provide to the administrator a preview of report generatd XML
//              including the tags of internal links between lexicons and scenarios
/// Restriction: Recover the data in XML database and transform him in XSL to display.

function gerar_xml($bd, $idProject , $data_pesquisa, $flag_formatado) {
    if ($flag_formatado == "ON") {
        $xml_resultante = $xml_resultante . "<?xml-stylesheet type=''text/xsl'' href=''projeto.xsl''?>\n";
    }

    $xml_resultante = $xml_resultante . "<projeto>\n";

    $qry_nome = "SELECT nome
	         FROM projeto
                 WHERE id_projeto = " . $idProject ;
    
    $tb_nome = mysql_query($qry_nome) or die("Erro ao enviar a query de selecao.");

    $xml_resultante = $xml_resultante . "<nome>" . mysql_result($tb_nome, 0) . "</nome>\n";

    $qry_cenario = "SELECT id_cenario, titulo, objetivo, contexto , atores ,
                           recursos, episodios, excecao
                    FROM cenario
                    WHERE (id_projeto = " . $idProject  . ")
                    AND (data <=" . " '" . $data_pesquisa . "'" . ")
                    ORDER BY id_cenario,data DESC";

    $tb_cenario = mysql_query($qry_cenario) or die("Erro ao enviar a query de selecao.");
    $primeiro = true;

    $id_temp = "";
    $vetor_lex = carrega_vetor_todos($idProject );
    $vetor_cen = carrega_vetor_cenario_todos($idProject );

    while ($row = mysql_fetch_row($tb_cenario)) {
        $id_cenario = "<ID>" . $row[0] . "</ID>";
     
        if (($id_temp != $id_cenario) or (primeiro)) {
            $title = '<titulo name="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . ucwords(strip_tags($row[1])) . '</titulo>';

            $objective = "<objetivo>" . "<sentenca>" . makeLinksXML(strip_tags($row[2]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</objetivo>";

            $context = "<contexto>" . "<sentenca>" . makeLinksXML(strip_tags($row[3]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</contexto>";

            $actors = "<atores>" . "<sentenca>" . makeLinksXML(strip_tags($row[4]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</atores>";

            $resources = "<recursos>" . "<sentenca>" . makeLinksXML(strip_tags($row[5]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</recursos>";

            $episodes = "<episodios>" . "<sentenca>" . makeLinksXML(strip_tags($row[6]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</episodios>";

            $exception = "<excecao>" . "<sentenca>" . makeLinksXML(strip_tags($row[7]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</excecao>";

            $xml_resultante = $xml_resultante . "<cenario>\n";

            // $xml_resultante = $xml_resultante . "$id_cenario\n" ;

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
    } // while

    $qry_lexico = "SELECT id_lexico, nome, nocao, impacto
                   FROM lexico
                   WHERE (id_projeto = " . $idProject  . ")
                   AND (data <=" . " '" . $data_pesquisa . "'" . ")
                   ORDER BY id_lexico,data DESC";
    
    $tb_lexico = mysql_query($qry_lexico) or die("Erro ao enviar a query de selecao.");

    $primeiro = true;

    $id_temp = "";

    while ($row = mysql_fetch_row($tb_lexico)) {
        $id_lexico = "<ID>" . $row[0] . "</ID>";
       
        if (($id_temp != $id_lexico) or (primeiro)) {
            $name = '<nome_simbolo name="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . '<texto>' . ucwords(strip_tags($row[1])) . '</texto>' . '</nome_simbolo>';

            $notion = "<nocao>" . "<sentenca>" . makeLinksXML(strip_tags($row[2]), $vetor_lex, $vetor_cen) . "<PT/>" . "</sentenca>" . "</nocao>";

            $impact = "<impacto>" . "<sentenca>" . makeLinksXML(strip_tags($row[3]), $vetor_lex, $vetor_cen) . "<PT/>" . "</sentenca>" . "</impacto>";

            $xml_resultante = $xml_resultante . "<lexico>\n";

            // $xml_resultante = $xml_resultante . "$id_lexico\n" ;

            $xml_resultante = $xml_resultante . "$name\n";

            $xml_resultante = $xml_resultante . "$notion\n";

            $xml_resultante = $xml_resultante . "$impact\n";

            $xml_resultante = $xml_resultante . "</lexico>\n";

            $primeiro = false;

            //$id_temp = id_lexico;
        }
    } // while

    $xml_resultante = $xml_resultante . "</projeto>\n";

    return $xml_resultante;
}

// gerar_xml
?>

<?php

$idProject  = $_SESSION['id_projeto_corrente'];
$data_pesquisa = $data_ano . "-" . $data_mes . "-" . $data_dia;
$flag_formatado = $flag;

$bd_trabalho = bd_connect() or die("Erro ao conectar ao SGBD");

$qVerifica = "SELECT * FROM publicacao WHERE id_projeto = '$idProject ' AND versao = '$versao' ";
$qrrVerifica = mysql_query($qVerifica);

if (!mysql_num_rows($qrrVerifica)) {
    $str_xml = gerar_xml($bd_trabalho, $idProject , $data_pesquisa, $flag_formatado);

    $xml_resultante = "<?xml version=''1.0'' encoding=''ISO-8859-1'' ?>\n" . $str_xml;
    $str_xml = "<?xml version='1.0' encoding='ISO-8859-1' ?>\n" . $str_xml;

    $commandSQL = "INSERT INTO publicacao ( id_projeto, data_publicacao, versao, XML)
                 VALUES ( '$idProject ', '$data_pesquisa', '$versao', '$xml_resultante')";

    //echo $comandoSql;

    mysql_query($commandSQL) or die("Erro ao enviar a query INSERT!");

    $queryResult = "select * from publicacao where id_projeto = $idProject  ";
    $requestResultSQL = mysql_query($queryResult) or die("Erro ao enviar a query");
    $row = mysql_fetch_row($requestResultSQL);
    $databaseXML = $row[3];

    // echo $xml_banco;

    $recoverDatabase = bd_connect() or die("Erro ao conectar ao SGBD");
    $qRecupera = "SELECT * FROM publicacao WHERE id_projeto = '$idProject ' AND versao = '$versao'";
    $qrrRecupera = mysql_query($qRecupera) or die("Erro ao enviar a query de busca!");
    
    $row = mysql_fetch_row($qrrRecupera);

    if ($flag_formatado == "ON") {

        $xh = xslt_create();

        $args = array('/_xml' => $str_xml);

        $html = @xslt_process($xh, 'arg:/_xml', 'projeto.xsl', NULL, $args);

        if (!( $html ))
        {
            die("Erro ao processar o arquivo XML: " . xslt_error($xh));
        }
        else {
            // Nothing should be done
        }

        xslt_free($xh);

        $databaseXML = $row[3];

        echo $databaseXML;

        //echo $html ;
    }
    else {
        /* $str_xml = str_replace ( "<", "<font color=\"red\">&lt;", $str_xml ) ;
          $str_xml = str_replace ( ">", "&gt;</font>", $str_xml ) ;
          $str_xml = str_replace ( "\n", "<br>", $str_xml ) ; */

        //<html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
        ?>

        <?
        
        echo $databaseXML;
        //</body></html>
        
        ?>

        <?php
    }
} else {
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
