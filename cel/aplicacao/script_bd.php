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

        $query = "create table conceito (id_conceito int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
                                        descricao varchar(250) not null,
                                        pai int(11),
                                        unique key(nome),
                                        primary key(id_conceito)
                                        );";
        $resultArray = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

        $query = "create table relacao_conceito (id_conceito int(11) not null,
                                        id_relacao int(11) not null,
                                        predicado varchar(250) not null
                                        );";
        $resultArray = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

        $query = "create table relacao (id_relacao int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
                                        unique key(nome),
                                        primary key(id_relacao)
                                        );";
        $resultArray = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

        $query = "create table axioma (id_axioma int(11) not null AUTO_INCREMENT,
                                        axioma varchar(250) not null ,
                                        unique key(axioma),
                                        primary key(id_axioma)
                                        );";
        $resultArray = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

        $query = "create table algoritmo (id_variavel int(11) not null AUTO_INCREMENT,
                                        nome varchar(250) not null ,
					valor varchar(250) not null ,
                                        unique key(nome),
                                        primary key(id_variavel)
                                        );";
        $resultArray = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

        mysql_close($link);
        ?>

    </body>

</html>
