<html> 
    <head>              
        <title></title>
    </head>    
    <body>

        <?php
        
        
        include_once( "bd.inc" );
        include 'auxiliar_bd.php';

        $link = bd_connect() or die("Erro na conex&atilde;o ao BD : " . mysql_error());

   
$query = "show tables";
        $resultArray = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);


        while ($line = mysql_fetch_array($resultArray, MYSQL_BOTH)) {
            
            $table = "show create table cel." . $line[0];
            $attributes = mysql_query($table) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
            
            while ($linha = mysql_fetch_array($attributes, MYSQL_BOTH)) {
                print ("\$query = \"$linha[1] ;\";<br>");
                print ("\$result = mysql_query(\$query) or die(\"A consulta ao BD falhou : \" . mysql_error() . __LINE__);<br>");
                print ("<br>");
            }
        }

      
echo "<br>FIM !!!";


        mysql_close($link);
        ?>

</body>

</html>