<html> 

    <head> 
        <title></title> 
    </head> 

    <body> 

        <?php
        include 'auxiliar_bd.php';
        include_once("bd.inc");
        include_once("CELConfig/CELConfig.inc");

        $link = bd_connect() or die("Erro na conexão ao BD : " . mysql_error() . __LINE__);

        if ($link && mysql_select_db(CELConfig_ReadVar("BD_database"))){
            echo "SUCESSO NA CONEXÃO ao BD <br>";
        } else{ 
                echo "ERRO NA CONEXÃO ao BD <br>";
              }

        $query = "alter table conceito add namespace varchar(250) NULL after descricao;";
        $result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

        echo "FIM !!!";
        
        mysql_close($link);
        ?> 

    </body> 

</html> 
