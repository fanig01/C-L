<html>

    <head>
        <title></title>
    </head>

    <body>

        <?php
        include_once("bd.inc");
        include_once("CELConfig/CELConfig.inc");

        $link = bd_connect() or die("Erro na conex&atilde;o ao BD : " . mysql_error() . __LINE__);
        if ($link && mysql_select_db(CELConfig_ReadVar("BD_database"))){
           
            echo "SUCESSO NA CONEX&Auml;O do BD <br>";
        }
        else{ 
                echo "ERRO NA CONEX&Auml;O do BD <br>";
        }

        $query_concept = "create table conceito (id_conceito int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
                                        descricao varchar(250) not null,
                                        pai int(11),
                                        unique key(nome),
                                        primary key(id_conceito)
                                        );";
        $resultArray_concept = mysql_query($query_concept) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

        $query_rel_concept = "create table relacao_conceito (id_conceito int(11) not null,
                                        id_relacao int(11) not null,
                                        predicado varchar(250) not null
                                        );";
        $resultArray_rel_concept = mysql_query($query_rel_concept) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

        $query_relationship = "create table relacao (id_relacao int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
                                        unique key(nome),
                                        primary key(id_relacao)
                                        );";
        $resultArray_relationship = mysql_query($query_relationship) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

        $query_axiom = "create table axioma (id_axioma int(11) not null AUTO_INCREMENT,
                                        axioma varchar(250) not null ,
                                        unique key(axioma),
                                        primary key(id_axioma)
                                        );";
        $resultArray_axiom = mysql_query($query_axiom) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

        $query_algorithm = "create table algoritmo (id_variavel int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
					valor varchar(250) not null ,
                                        unique key(nome),
                                        primary key(id_variavel)
                                        );";
        $resultArray_algorithm = mysql_query($query_algorithm) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

        mysql_close($link);
        ?>

    </body>

</html>
