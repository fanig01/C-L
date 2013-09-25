<?php
session_start();
include("coloca_tags_xml.php");
include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

checkUserAuthentication("index.php");        // Checa se o usuario foi autenticado
// Testa se o usuario quer uma visualiza��o formatada ou n�o

if (isset($_POST['flag'])) {
    $flag_formatado = "ON";
} else {
    $flag_formatado = "OFF";
}
?>

<?php

// gerador_xml.php
// Dada a base e o id do projeto, gera-se o xml dos cen�rios e l�xicos.
// Cen�rio - Gerar Relat�rios XML 
// Objetivo:    Permitir ao administrador gerar relat�rios em formato XML de um projeto, identificados por data.     
// Contexto:    Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
//          Pr�-Condi��o: Login, projeto cadastrado.
// Atores:    Administrador     
// Recursos:    Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.     
// Epis�dios:O sistema fornece para o administrador uma tela onde dever� fornecer os dados
//          do relat�rio para sua posterior identifica��o, como data e vers�o. 
//          Para efetivar a gera��o do relat�rio, basta clicar em Gerar. 
//          Restri��o: O sistema executar� duas valida��es: 
//                      - Se a data � v�lida.
//                      - Se existem cen�rios e l�xicos em datas iguais ou anteriores.
//          Gerando com sucesso o relat�rio a partir dos dados cadastrados do projeto,
//          o sistema fornece ao administrador a tela de visualiza��o do relat�rio XML criado, 
//          incluindo os tags de links internos entre lexicos e cenarios.
//          Restri��o: Recuperar os dados em XML do Banco de dados e os transformar por uma XSL para a exibi��o.      

function gerar_xml($bd, $id_projeto, $data_pesquisa, $flag_formatado) {
    if ($flag_formatado == "ON") {
        $xml_resultante = $xml_resultante . "<?xml-stylesheet type=''text/xsl'' href=''projeto.xsl''?>\n";
    }

    $xml_resultante = $xml_resultante . "<projeto>\n";

    // Seleciona o nome do projeto

    $qry_nome = "SELECT nome
	                 FROM projeto
                     WHERE id_projeto = " . $id_projeto;
    $tb_nome = mysql_query($qry_nome) or die("Erro ao enviar a query de selecao.");

    $xml_resultante = $xml_resultante . "<nome>" . mysql_result($tb_nome, 0) . "</nome>\n";

    // Seleciona os cen�rios de um projeto.

    $qry_cenario = "SELECT id_cenario ,
                               titulo ,
                               objetivo ,
                               contexto ,
                               atores ,
                               recursos ,
                               episodios ,
                               excecao
                        FROM cenario
                        WHERE  (id_projeto = " . $id_projeto . ")
                        AND (data <=" . " '" . $data_pesquisa . "'" . ")
                        ORDER BY id_cenario,data DESC";

    $tb_cenario = mysql_query($qry_cenario) or die("Erro ao enviar a query de selecao.");
    $primeiro = true;

    $id_temp = "";
    $vetor_lex = carrega_vetor_todos($id_projeto);
    $vetor_cen = carrega_vetor_cenario_todos($id_projeto);

    while ($row = mysql_fetch_row($tb_cenario)) {
        $id_cenario = "<ID>" . $row[0] . "</ID>";
        if (($id_temp != $id_cenario) or (primeiro)) {
            $titulo = '<titulo name="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . ucwords(strip_tags($row[1])) . '</titulo>';

            $objetivo = "<objetivo>" . "<sentenca>" . faz_links_XML(strip_tags($row[2]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</objetivo>";

            $contexto = "<contexto>" . "<sentenca>" . faz_links_XML(strip_tags($row[3]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</contexto>";

            $atores = "<atores>" . "<sentenca>" . faz_links_XML(strip_tags($row[4]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</atores>";

            $recursos = "<recursos>" . "<sentenca>" . faz_links_XML(strip_tags($row[5]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</recursos>";

            $episodios = "<episodios>" . "<sentenca>" . faz_links_XML(strip_tags($row[6]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</episodios>";

            $excecao = "<excecao>" . "<sentenca>" . faz_links_XML(strip_tags($row[7]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</excecao>";

            $xml_resultante = $xml_resultante . "<cenario>\n";

            // $xml_resultante = $xml_resultante . "$id_cenario\n" ;

            $xml_resultante = $xml_resultante . "$titulo\n";

            $xml_resultante = $xml_resultante . "$objetivo\n";

            $xml_resultante = $xml_resultante . "$contexto\n";

            $xml_resultante = $xml_resultante . "$atores\n";

            $xml_resultante = $xml_resultante . "$recursos\n";

            $xml_resultante = $xml_resultante . "$episodios\n";

            $xml_resultante = $xml_resultante . "$excecao\n";

            $xml_resultante = $xml_resultante . "</cenario>\n";

            $primeiro = false;

            //??$id_temp = id_cenario;
        }
    } // while
    // Seleciona os lexicos de um projeto.

    $qry_lexico = "SELECT id_lexico ,
		                        nome ,
                                nocao ,
                                impacto
                        FROM   lexico
                        WHERE  (id_projeto = " . $id_projeto . ")
                        AND (data <=" . " '" . $data_pesquisa . "'" . ")
                        ORDER BY id_lexico,data DESC";
    $tb_lexico = mysql_query($qry_lexico) or die("Erro ao enviar a query de selecao.");

    $primeiro = true;

    $id_temp = "";

    while ($row = mysql_fetch_row($tb_lexico)) {
        $id_lexico = "<ID>" . $row[0] . "</ID>";
        if (($id_temp != $id_lexico) or (primeiro)) {
            $nome = '<nome_simbolo name="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . '<texto>' . ucwords(strip_tags($row[1])) . '</texto>' . '</nome_simbolo>';

            $nocao = "<nocao>" . "<sentenca>" . faz_links_XML(strip_tags($row[2]), $vetor_lex, $vetor_cen) . "<PT/>" . "</sentenca>" . "</nocao>";

            $impacto = "<impacto>" . "<sentenca>" . faz_links_XML(strip_tags($row[3]), $vetor_lex, $vetor_cen) . "<PT/>" . "</sentenca>" . "</impacto>";

            $xml_resultante = $xml_resultante . "<lexico>\n";

            // $xml_resultante = $xml_resultante . "$id_lexico\n" ;

            $xml_resultante = $xml_resultante . "$nome\n";

            $xml_resultante = $xml_resultante . "$nocao\n";

            $xml_resultante = $xml_resultante . "$impacto\n";

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
$id_projeto = $_SESSION['id_projeto_corrente'];
$data_pesquisa = $data_ano . "-" . $data_mes . "-" . $data_dia;
$flag_formatado = $flag;

// Abre base de dados.
$bd_trabalho = bd_connect() or die("Erro ao conectar ao SGBD");

$qVerifica = "SELECT * FROM publicacao WHERE id_projeto = '$id_projeto' AND versao = '$versao' ";
$qrrVerifica = mysql_query($qVerifica);

if (!mysql_num_rows($qrrVerifica)) {
    $str_xml = gerar_xml($bd_trabalho, $id_projeto, $data_pesquisa, $flag_formatado);

    $xml_resultante = "<?xml version=''1.0'' encoding=''ISO-8859-1'' ?>\n" . $str_xml;
    $str_xml = "<?xml version='1.0' encoding='ISO-8859-1' ?>\n" . $str_xml;

    $commandSQL = "INSERT INTO publicacao ( id_projeto, data_publicacao, versao, XML)
                 VALUES ( '$id_projeto', '$data_pesquisa', '$versao', '$xml_resultante')";

    //echo $q;

    mysql_query($commandSQL) or die("Erro ao enviar a query INSERT!");

    $qq = "select * from publicacao where id_projeto = $id_projeto ";
    $requestResultSQL = mysql_query($qq) or die("Erro ao enviar a query");
    $row = mysql_fetch_row($requestResultSQL);
    $xml_banco = $row[3];

    // echo $xml_banco;

    $bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");
    $qRecupera = "SELECT * FROM publicacao WHERE id_projeto = '$id_projeto' AND versao = '$versao'";
    $qrrRecupera = mysql_query($qRecupera) or die("Erro ao enviar a query de busca!");
    $row = mysql_fetch_row($qrrRecupera);

    if ($flag_formatado == "ON") {

        $xh = xslt_create();

        $args = array('/_xml' => $str_xml);

        $html = @xslt_process($xh, 'arg:/_xml', 'projeto.xsl', NULL, $args); //retirado o endere�o f�sico para o arquivo .xsl

        if (!( $html ))
            die("Erro ao processar o arquivo XML: " . xslt_error($xh));

        xslt_free($xh);

        $xml_banco = $row[3];

        echo $xml_banco;

        //echo $html ;
    }
    else {
        /* $str_xml = str_replace ( "<", "<font color=\"red\">&lt;", $str_xml ) ;
          $str_xml = str_replace ( ">", "&gt;</font>", $str_xml ) ;
          $str_xml = str_replace ( "\n", "<br>", $str_xml ) ; */

        //<html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
        ?>
        <?
        echo $xml_banco;
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
