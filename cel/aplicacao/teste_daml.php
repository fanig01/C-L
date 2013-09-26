<?php

include 'daml.php';
include 'auxiliar_bd.php';
include_once("bd.inc");
include_once("CELConfig/CELConfig.inc");

$link = bd_connect();

$site = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
$dir = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
$file = nome_arquivo_daml();

$input = array("title" => "Ontologia de teste",
    "creator" => "Pedro",
    "description" => "teste de tradução de léxico para ontologia",
    "subject" => "",
    "versionInfo" => "1.1");

$list_concept = get_lista_de_conceitos();
$list_relationship = get_lista_de_relacoes();
$list_axiom = get_lista_de_axiomas();


$daml = salva_daml($site, $dir, $file, $input, $list_concept, $list_relationship, $list_axiom);

if (!$daml) {
    print 'Erro ao exportar ontologia para DAML!';
} else {
    print 'Ontologia exportada para DAML com sucesso! <br>';
    print 'Arquivo criado: ';
    print "<a href=\"$site$daml\">$daml</a>";
}

mysql_close($link);
?>