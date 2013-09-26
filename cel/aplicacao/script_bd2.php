<html>

    <head>
        <title></title>
    </head>

    <body>

        <?php
        include_once("bd.inc");
        include_once('auxiliar_bd.php');
        session_start();

        function converte_impactos() {
            $link = bd_connect() or die("Erro na conex&atilde;o ao BD : " . mysql_error() . __LINE__);

            $filename = "teste.txt";

            $query_lexicon = "select * from lexico;";
            $result_lexicon = mysql_query($query_lexicon) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

            if (!$handle = fopen($filename, 'w')) {
                print "Nao foi poss&iacute;vel abrir o arquivo !!!($filename)";
                exit;
            }

            while ($line = mysql_fetch_array($result_lexicon, MYSQL_ASSOC)) {
                $id_lexicon = $line['id_lexico'];
                $impact = $line['impacto'];

                if (!fwrite($handle, "@\r\n$id_lexicon\r\n")) {
                    print "Cannot write to file ($filename)";
                    exit;
                }

                if (!fwrite($handle, "$impact\r\n")) {
                    print "Cannot write to file ($filename)";
                    exit;
                }
            }

            fclose($handle);

            mysql_query("delete from impacto;");

            $lines = file($filename);

            $catch_id = "FALSE";
            $id_lexicon = 0;

            foreach ($lines as $line_num => $line) {
                if ($line[0] == '@') {
                    $catch_id = 1;
                    continue;
                }
                if ($catch_id) {
                    $id = sscanf($line, "%d");
                    $id_lexicon = $id[0];
                    $catch_id = 0;
                    continue;
                }

                print ($line . "<br>\n");
                if (strcmp(trim($line), "") != 0) {
                    $query_impact = "insert into impacto (id_lexico, impacto) values ('$id_lexicon', '$line');";
                    $result_impact = mysql_query($query_impact) or die("A consulta ao BD falhou : " . mysql_error() . " " . $line . " " . $id_lexicon . " " . __LINE__);
                }
            }

            $query_impact = "select * from impacto order by id_lexico;";
            $result_impact = mysql_query($query_impact) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
            $result_impact_rows = mysql_num_rows($result_impact);

            mysql_close($link);
        }
        ?>

    </body>

</html>