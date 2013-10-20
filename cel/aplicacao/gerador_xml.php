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
 Dada a base e o id do projeto, gera-se o xml
 dos cen�rios e l�xicos.
Cenário - Gerar Relat�rios XML 
Objetivo:    Permitir ao administrador gerar relat�rios em formato XML de um projeto, identificados por data.     
Contexto:    Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
          Pr�-Condi��o: Login, projeto cadastrado.
Atores:    Administrador     
Recursos:    Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.     
Epis�dios:O sistema fornece para o administrador uma tela onde dever� fornecer os dados
          do relat�rio para sua posterior identifica��o, como data e vers�o. 
          Para efetivar a gera��o do relat�rio, basta clicar em Gerar. 
          Restri��o: O sistema executar� duas valida��es: 
                      - Se a data � v�lida.
                      - Se existem cen�rios e l�xicos em datas iguais ou anteriores.
          Gerando com sucesso o relat�rio a partir dos dados cadastrados do projeto,
          o sistema fornece ao administrador a tela de visualiza��o do relat�rio XML criado. 
          Restri��o: Recuperar os dados em XML do Banco de dados e os transformar por uma XSL para a exibi��o.      
*/

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

///////////////////////////////////////////////////////////////////////////////////////////////////
//
//Cen�rio - Gerar links nos Relat�rios XML criados
//
//Objetivo:    Permitir que os relat�rios gerados em formato XML possuam termos com links 
//          para os seus respectivos l�xicos
//
//Contexto:    Gerente deseja gerar um relat�rio em XML para um dos projetos da qual � administrador.
//          Pr�-Condi��o: Login, projeto cadastrado, acesso ao banco de dados.
//
//Atores:    Sistema    
//
//Recursos:    Sistema, senten�as a serem linkadas, dados cadastrados do projeto, banco de dados. 
//    
//Epis�dios:O sistema recebe a senten�a com os tags pr�prios do C&L e retorna o c�digo do link HTML
//            equivalente para os l�xicos cadatrados no sistema. 
//     
///////////////////////////////////////////////////////////////////////////////////////////////////
//
//L�xicos:
//
//     Fun��o:            gera_xml_links
//     Descri��o:         Analisa uma senten�a recebida afim de identificar as tags utilizadas no C&L
//                        para linkar os l�xicos e transformar em links XML.
//     Sin�nimos:         -
//     Exemplo: 
//        ENTRADA: <!--CL:tam:2--><a title="Lexico" href="main.php?t=l&id=228">software livre</a>
//                 <!--/CL-->
//        SA�DA:  <a title="Lexico" href="main.php?t=l&id=228"><texto referencia_lexico=software 
//                livre>software livre</texto></a>
//
//     Vari�vel:            $sentenca
//     Descri��o:         Armazena a express�o passada por argumento a ser tranformada em link.
//     Sin�nimos:         -
//     Exemplo:             <!--CL:tam:2--><a title="Lexico" href="main.php?t=l&id=228">software livre
//                        </a><!--/CL-->
//
//     Vari�vel:            $regex
//     Descri��o:            Armazena o pattern a ser utilizado ao se separar a senten�a.
//     Sin�nimos:            -
//     Exemplo:            "/(<!--CL:tam:\d+-->(<a[^>]*?\>)([^<]*?)<\/a><!--\/CL-->)/mi"
//
//     Vari�vel:            $vetor_texto
//     Descri��o:         Array que armazena palavra por palavra a sente�a a ser linkada, sem o tag.
//     Sin�nimos:         -
//     Exemplo:             $vetor_texto[0] => software
//                        $vetor_texto[1] => livre
//
//     Vari�vel:            $inside_tag
//     Descri��o:         Determina se a an�lise est� sendo feita dentro ou fora do tag
//     Sin�nimos:         -
//     Exemplo:             false
//
//     Vari�vel:            $tamanho_vetor_texto
//     Descri��o:         Armazena a n�mero de palavras que se encontram no array $vetor_texto. 
//     Sin�nimos:         -
//     Exemplo:             2
//
//     Vari�vel:            $i
//     Descri��o:         Vari�vel utilizada como um contador para uso gen�rico.
//     Sin�nimos:         -
//     Exemplo:             -
//
//     Vari�vel:            $match
//     Descri��o:         Armazena o valor 1 caso a string "/href="main.php\?t=(.)&id=(\d+?)"/mi"
//                        seja encontrada na no array $vetor_texto. Caso contr�rio, armazena 0.
//     Sin�nimos:         -
//     Exemplo:             0
//
//     Vari�vel:            $idProject 
//     Descri��o:         Armazena o n�mero identificador do projeto corrente.
//     Sin�nimos:         -
//     Exemplo:             1
//
//     Vari�vel:            $atributo
//     Descri��o:         Armazena um tag que indica a refer�ncia para um l�xico
//     Sin�nimos:         -
//     Exemplo:             referencia_lexico
//
//     Vari�vel:            $query
//     Descri��o:         Armazena a consulta a ser feita no banco de dados
//     Sin�nimos:         -
//     Exemplo:             SELECT nome FROM lexico WHERE id_projeto = $idProject 
//
//     Vari�vel:            $result
//     Descri��o:         Armazena o resultado da consulta feita ao banco de dados
//     Sin�nimos:         -
//     Exemplo:             -
//
//     Vari�vel:            $row
//     Descri��o:         Array que armazena tupla a tupla o resultado da consulta realizada
//     Sin�nimos:         -
//     Exemplo:             -
//
//     Vari�vel:            $valor
//     Descri��o:         Armazena uma tupla, substituindo os caracteres acentuados pelos seus 
//                        equivalentes sem acentua��o.
//     Sin�nimos:         -
//     Exemplo:             acentuacao
//
///////////////////////////////////////////////////////////////////////////////////////////////////


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
