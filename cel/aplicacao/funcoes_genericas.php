<?php
include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");


/*
  if (!(class_exists("PGDB"))) {
  include("bd_class.php");
  }
 */

/* chkUser(): checa se o usu�rio acessando foi autenticado (presen�a da vari�vel de sess�o
  $id_usuario_corrente). Caso ele j� tenha sido autenticado, continua-se com a execu��o do
  script. Caso contr�rio, abre-se uma janela de logon. */
if (!(function_exists("chkUser"))) {

    function checkUserAuthentication($url) {
        //if (!(session_is_registered("id_usuario_corrente"))) {
        if( isset($_SESSION["id_usuario_correntegit"]))  {
            ?>

            <script language="javascript1.3">

                open('login.php?url=<?= $url ?>', 'login', 'dependent,height=430,width=490,resizable,scrollbars,titlebar');

            </script>

            <?php
            exit();
        }
    }

}


###################################################################
# Insere um cenario no banco de dados.
# Recebe o id_projeto, titulo, objetivo, contexto, atores, recursos, excecao e episodios. (1.1)
# Insere os valores do lexico na tabela CENARIO. (1.2)
# Devolve o id_cenario. (1.4)
#
###################################################################
if (!(function_exists("inclui_cenario"))) {

    function inclui_cenario($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes) {
        //global $r;      // Conexao com a base de dados
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $data = date("Y-m-d");

        $comandoSql = "INSERT INTO cenario (id_projeto,data, titulo, objetivo, contexto, atores, recursos, excecao, episodios) 
		VALUES ($idProject ,'$data', '" . prepara_dado(strtolower($title)) . "', '" . prepara_dado($objective) . "',
		'" . prepara_dado($context) . "', '" . prepara_dado($actors) . "', '" . prepara_dado($resources) . "',
		'" . prepara_dado($exception) . "', '" . prepara_dado($episodes) . "')";

        mysql_query($comandoSql) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $comandoSql = "SELECT max(id_cenario) FROM cenario";
        $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($requestResultSQL);
        return $result[0];
    }

}
###################################################################
# Insere um lexico no banco de dados.
# Recebe o id_projeto, nome, no��o, impacto e os sinonimos. (1.1)
# Insere os valores do lexico na tabela LEXICO. (1.2)
# Insere todos os sinonimos na tabela SINONIMO. (1.3)
# Devolve o id_lexico. (1.4)
#
###################################################################
if (!(function_exists("inclui_lexico"))) {

    function inclui_lexico($idProject , $nome, $notion, $impact, $sinonimos, $classificacao) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $data = date("Y-m-d");


        $comandoSql = "INSERT INTO lexico (id_projeto, data, nome, nocao, impacto, tipo)
              VALUES ($idProject , '$data', '" . prepara_dado(strtolower($nome)) . "',
			  '" . prepara_dado($notion) . "', '" . prepara_dado($impact) . "', '$classificacao')";

        mysql_query($comandoSql) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        //sinonimo
        $newLexId = mysql_insert_id($r);


        if (!is_array($sinonimos))
            $sinonimos = array();

        foreach ($sinonimos as $novoSin) {
            $comandoSql = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                VALUES ($newLexId, '" . prepara_dado(strtolower($novoSin)) . "', $idProject )";
            mysql_query($comandoSql, $r) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }

        $comandoSql = "SELECT max(id_lexico) FROM lexico";
        $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($requestResultSQL);
        return $result[0];
    }

}
###################################################################
# Insere um projeto no banco de dados.
# Recebe o nome e descricao. (1.1)
# Verifica se este usuario ja possui um projeto com esse nome. (1.2)
# Caso nao possua, insere os valores na tabela PROJETO. (1.3)
# Devolve o id_cprojeto. (1.4)
#
###################################################################
if (!(function_exists("inclui_projeto"))) {

    function inclui_projeto($nome, $descricao) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //verifica se usuario ja existe
        $qv = "SELECT * FROM projeto WHERE nome = '$nome'";
        $qvr = mysql_query($qv) or die("Erro ao enviar a query de select<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        //$result = mysql_fetch_row($qvr);
        $resultArray = mysql_fetch_array($qvr);


        if ($resultArray != false) {
            //verifica se o nome existente corresponde a um projeto que este usuario participa
            $id_projeto_repetido = $resultArray['id_projeto'];

            $id_usuario_corrente = $_SESSION['id_usuario_corrente'];

            $qvu = "SELECT * FROM participa WHERE id_projeto = '$id_projeto_repetido' AND id_usuario = '$id_usuario_corrente' ";

            $qvuv = mysql_query($qvu) or die("Erro ao enviar a query de SELECT no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

            $resultArray = mysql_fetch_row($qvuv);

            if ($resultArray[0] != null) {
                return -1;
            }
        }

        $comandoSql = "SELECT MAX(id_projeto) FROM projeto";
        $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query de MAX ID<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($requestResultSQL);

        if ($result[0] == false) {
            $result[0] = 1;
        } else {
            $result[0]++;
        }
        $data = date("Y-m-d");

        $qr = "INSERT INTO projeto (id_projeto, nome, data_criacao, descricao)
                  VALUES ($result[0],'" . prepara_dado($nome) . "','$data' , '" . prepara_dado($descricao) . "')";

        mysql_query($qr) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        return $result[0];
    }

}


if (!(function_exists("recarrega"))) {

    function recarrega($url) {
        ?>

        <script language="javascript1.3">

            location.replace('<?= $url ?>');

        </script>

        <?php
    }

}

if (!(function_exists("breakpoint"))) {

    function breakpoint($num) {
        ?>

        <script language="javascript1.3">

            alert('<?= $num ?>');

        </script>

        <?php
    }

}

if (!(function_exists("simple_query"))) {

    funcTion simple_query($field, $table, $where) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD");
        $comandoSql = "SELECT $field FROM $table WHERE $where";
        $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query");
        $result = mysql_fetch_row($requestResultSQL);
        return $result[0];
    }

}


// Para a correta inclusao de um cenario, uma serie de procedimentos
// precisam ser tomados (relativos ao requisito 'navegacao circular'):
//
// 1. Incluir o novo cenario na base de dados;
// 2. Para todos os cenarios daquele projeto, exceto o rec�m inserido:
//      2.1. Procurar em contexto e episodios
//           por ocorrencias do titulo do cenario incluido;
//      2.2. Para os campos em que forem encontradas ocorrencias:
//          2.2.1. Incluir entrada na tabela 'centocen';
//      2.3. Procurar em contexto e episodios do cenario incluido
//           por ocorrencias de titulos de outros cenarios do mesmo projeto;
//      2.4. Se achar alguma ocorrencia:
//          2.4.1. Incluir entrada na tabela 'centocen';
// 3. Para todos os nomes de termos do lexico daquele projeto:
//      3.1. Procurar ocorrencias desses nomes no titulo, objetivo, contexto,
//           recursos, atores, episodios, do cenario incluido;
//      3.2. Para os campos em que forem encontradas ocorrencias:
//          3.2.1. Incluir entrada na tabela 'centolex';

if (!(function_exists("adicionar_cenario"))) {

    function adicionar_cenario($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes) {
        // Conecta ao SGBD
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        // Inclui o cenario na base de dados (sem transformar os campos, sem criar os relacionamentos)
        $id_incluido = inclui_cenario($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes);

        $comandoSql = "SELECT id_cenario, titulo, contexto, episodios
              FROM cenario
              WHERE id_projeto = $idProject 
              AND id_cenario != $id_incluido
              ORDER BY CHAR_LENGTH(titulo) DESC";
        $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        ### PREENCHIMENTO DAS TABELAS LEXTOLEX E CENTOCEN PARA MONTAGEM DO MENU LATERAL
        // Verifica ocorr�ncias do titulo do cenario incluido no contexto 
        // e nos episodios de todos os outros cenarios e adiciona os relacionamentos,
        // caso possua, na tabela centocen

        while ($result = mysql_fetch_array($requestResultSQL)) {    // Para todos os cenarios
            $tituloEscapado = escapa_metacaracteres($title);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) {   // (2.2)
                $comandoSql = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
		                      VALUES (" . $result['id_cenario'] . ", $id_incluido)"; // (2.2.1)
                mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }

            $tituloEscapado = escapa_metacaracteres($result['titulo']);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $context) != 0) ||
                    (preg_match($regex, $episodes) != 0)) {   // (2.3)        
                $comandoSql = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_incluido, " . $result['id_cenario'] . ")"; //(2.4.1)

                mysql_query($comandoSql) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }   // if
        }   // while
        // Verifica a ocorrencia do nome de todos os lexicos nos campos titulo, objetivo,
        // contexto, atores, recursos, episodios e excecao do cenario incluido 

        $comandoSql = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $idProject ";
        $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result2 = mysql_fetch_array($requestResultSQL)) {    // (3)
            $nomeEscapado = escapa_metacaracteres($result2['nome']);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $title) != 0) ||
                    (preg_match($regex, $objective) != 0) ||
                    (preg_match($regex, $context) != 0) ||
                    (preg_match($regex, $actors) != 0) ||
                    (preg_match($regex, $resources) != 0) ||
                    (preg_match($regex, $episodes) != 0) ||
                    (preg_match($regex, $exception) != 0)) {   // (3.2)
                $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = " . $result2['id_lexico'];
                $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArrayCen = mysql_fetch_array($qrCen);

                if ($resultArrayCen == false) {
                    $comandoSql = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, " . $result2['id_lexico'] . ")";
                    mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                }
            }   // if
        }   // while
        // Verifica a ocorrencia dos sinonimos de todos os lexicos nos campos titulo, objetivo,
        // contexto, atores, recursos, episodios e excecao do cenario incluido
        //Sinonimos

        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $idProject  AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();

        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {

            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }

        $qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $idProject 
              AND id_cenario = $id_incluido";
        $count = count($nomesSinonimos);
        for ($i = 0; $i < $count; $i++) {

            $requestResultSQL = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            // verifica sinonimos dos outros lexicos no cenario inclu�do
            while ($result = mysql_fetch_array($requestResultSQL)) {

                $nomeSinonimoEscapado = escapa_metacaracteres($nomesSinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $objective) != 0) ||
                        (preg_match($regex, $context) != 0) ||
                        (preg_match($regex, $actors) != 0) ||
                        (preg_match($regex, $resources) != 0) ||
                        (preg_match($regex, $episodes) != 0) ||
                        (preg_match($regex, $exception) != 0)) {

                    $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = $id_lexicoSinonimo[$i] ";
                    $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayCen = mysql_fetch_array($qrCen);

                    if ($resultArrayCen == false) {
                        $comandoSql = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, $id_lexicoSinonimo[$i])";
                        mysql_query($comandoSql) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                    }
                }   // if
            }   // while
        } //for
    }

}

//
// Para a correta inclusao de um termo no lexico, uma serie de procedimentos
// precisam ser tomados (relativos ao requisito 'navegacao circular'):
//
// 1. Incluir o novo termo na base de dados;
// 2. Para todos os cenarios daquele projeto:
//      2.1. Procurar em titulo, objetivo, contexto, recursos, atores, episodios
//           por ocorrencias do termo incluido ou de seus sinonimos;
//      2.2. Para os campos em que forem encontradas ocorrencias:
//              2.2.1. Incluir entrada na tabela 'centolex';
// 3. Para todos termos do lexico daquele projeto (menos o recem-inserido):
//      3.1. Procurar em nocao, impacto por ocorrencias do termo inserido ou de seus sinonimos;
//      3.2. Para os campos em que forem encontradas ocorrencias:
//              3.2.1. Incluir entrada na tabela 'lextolex';
//      3.3. Procurar em nocao, impacto do termo inserido por
//           ocorrencias de termos do lexico do mesmo projeto;
//      3.4. Se achar alguma ocorrencia:
//              3.4.1. Incluir entrada na table 'lextolex';

if (!(function_exists("adicionar_lexico"))) {

    function adicionar_lexico($idProject , $nome, $notion, $impact, $sinonimos, $classificacao) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $id_incluido = inclui_lexico($idProject , $nome, $notion, $impact, $sinonimos, $classificacao); // (1)

        $qr = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
              FROM cenario
              WHERE id_projeto = $idProject ";

        $requestResultSQL = mysql_query($qr) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($requestResultSQL)) {    // 2  - Para todos os cenarios
            $nomeEscapado = escapa_metacaracteres($nome);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['objetivo']) != 0) ||
                    (preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['atores']) != 0) ||
                    (preg_match($regex, $result['recursos']) != 0) ||
                    (preg_match($regex, $result['excecao']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) { //2.2
                $comandoSql = "INSERT INTO centolex (id_cenario, id_lexico)
                     VALUES (" . $result['id_cenario'] . ", $id_incluido)"; //2.2.1

                mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }


        //sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {

            $requestResultSQL = mysql_query($qr) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result2 = mysql_fetch_array($requestResultSQL)) {

                $nomeSinonimoEscapado = escapa_metacaracteres($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $result2['objetivo']) != 0) ||
                        (preg_match($regex, $result2['contexto']) != 0) ||
                        (preg_match($regex, $result2['atores']) != 0) ||
                        (preg_match($regex, $result2['recursos']) != 0) ||
                        (preg_match($regex, $result2['excecao']) != 0) ||
                        (preg_match($regex, $result2['episodios']) != 0)) {

                    $qLex = "SELECT * FROM centolex WHERE id_cenario = " . $result2['id_cenario'] . " AND id_lexico = $id_incluido ";
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($qrLex);

                    if ($resultArraylex == false) {

                        $comandoSql = "INSERT INTO centolex (id_cenario, id_lexico)
                             VALUES (" . $result2['id_cenario'] . ", $id_incluido)";

                        mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    } //if
                }//if                
            }//while            
        } //for


        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $idProject 
               AND id_lexico != $id_incluido";

        //pega todos os outros lexicos
        $requestResultSQL = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($requestResultSQL)) {    // (3)
            $nomeEscapado = escapa_metacaracteres($nome);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['nocao']) != 0 ) ||
                    (preg_match($regex, $result['impacto']) != 0)) {

                $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $result['id_lexico'] . " AND id_lexico_to = $id_incluido";
                $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArraylex = mysql_fetch_array($qrLex);

                if ($resultArraylex == false) {
                    $comandoSql = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                          VALUES (" . $result['id_lexico'] . ", $id_incluido)";

                    mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
            }

            $nomeEscapado = escapa_metacaracteres($result['nome']);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $notion) != 0) ||
                    (preg_match($regex, $impact) != 0)) {   // (3.3)        
                $comandoSql = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) VALUES ($id_incluido, " . $result['id_lexico'] . ")";

                mysql_query($comandoSql) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }   // while
        //lexico para lexico

        $ql = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_projeto = $idProject 
              AND id_lexico != $id_incluido";

        //sinonimos dos outros lexicos no texto do inserido

        $requestResultSQL = mysql_query($ql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {
            while ($resultl = mysql_fetch_array($requestResultSQL)) {

                $nomeSinonimoEscapado = escapa_metacaracteres($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $resultl['nocao']) != 0) ||
                        (preg_match($regex, $resultl['impacto']) != 0)) {

                    $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $resultl['id_lexico'] . " AND id_lexico_to = $id_incluido";
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($qrLex);

                    if ($resultArraylex == false) {

                        $comandoSql = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                         VALUES (" . $resultl['id_lexico'] . ", $id_incluido)";

                        mysql_query($comandoSql) or die("Erro ao enviar a query de insert no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }//if
                }    //if
            }//while
        }//for
        //sinonimos ja existentes

        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $idProject  AND id_lexico != $id_incluido AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();

        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {

            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }
    }

}


###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeCenario"))) {

    function removeCenario($idProject , $id_cenario) {
        $DB = new PGDB ();
        $sql1 = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);

        # Remove o relacionamento entre o cenario a ser removido
        # e outros cenarios que o referenciam
        $sql1->execute("DELETE FROM centocen WHERE id_cenario_from = $id_cenario");
        $sql2->execute("DELETE FROM centocen WHERE id_cenario_to = $id_cenario");
        # Remove o relacionamento entre o cenario a ser removido
        # e o seu lexico
        $sql3->execute("DELETE FROM centolex WHERE id_cenario = $id_cenario");
        # Remove o cenario escolhido
        $sql4->execute("DELETE FROM cenario WHERE id_cenario = $id_cenario");
    }

}


###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("alteraCenario"))) {

    function alteraCenario($idProject , $id_cenario, $title, $objective, $context, $actors, $resources, $exception, $episodes) {
        $DB = new PGDB ();
        $sql1 = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);

        # Remove o relacionamento entre o cenario a ser alterado
        # e outros cenarios que o referenciam
        $sql1->execute("DELETE FROM centocen WHERE id_cenario_from = $id_cenario");
        $sql2->execute("DELETE FROM centocen WHERE id_cenario_to = $id_cenario");
        # Remove o relacionamento entre o cenario a ser alterado
        # e o seu lexico
        $sql3->execute("DELETE FROM centolex WHERE id_cenario = $id_cenario");

        # atualiza o cenario

        $sql4->execute("update cenario set 
		objetivo = '" . prepara_dado($objective) . "', 
		contexto = '" . prepara_dado($context) . "', 
		atores = '" . prepara_dado($actors) . "', 
		recursos = '" . prepara_dado($resources) . "', 
		episodios = '" . prepara_dado($episodes) . "', 
		excecao = '" . prepara_dado($exception) . "' 
		where id_cenario = $id_cenario ");

        // monta_relacoes($idProject );
        // Conecta ao SGBD
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $comandoSql = "SELECT id_cenario, titulo, contexto, episodios
              FROM cenario
              WHERE id_projeto = $idProject 
              AND id_cenario != $id_cenario
              ORDER BY CHAR_LENGTH(titulo) DESC";
        $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($requestResultSQL)) {    // Para todos os cenarios
            $tituloEscapado = escapa_metacaracteres($title);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) {   // (2.2)
                $comandoSql = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
	                      VALUES (" . $result['id_cenario'] . ", $id_cenario)"; // (2.2.1)
                mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            $tituloEscapado = escapa_metacaracteres($result['titulo']);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $context) != 0) ||
                    (preg_match($regex, $episodes) != 0)) {   // (2.3)        
                $comandoSql = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_cenario, " . $result['id_cenario'] . ")"; //(2.4.1)

                mysql_query($comandoSql) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }   // if
        }   // while


        $comandoSql = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $idProject ";
        $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result2 = mysql_fetch_array($requestResultSQL)) {    // (3)
            $nomeEscapado = escapa_metacaracteres($result2['nome']);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $title) != 0) ||
                    (preg_match($regex, $objective) != 0) ||
                    (preg_match($regex, $context) != 0) ||
                    (preg_match($regex, $actors) != 0) ||
                    (preg_match($regex, $resources) != 0) ||
                    (preg_match($regex, $episodes) != 0) ||
                    (preg_match($regex, $exception) != 0)) {   // (3.2)
                $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_cenario AND id_lexico = " . $result2['id_lexico'];
                $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArrayCen = mysql_fetch_array($qrCen);

                if ($resultArrayCen == false) {
                    $comandoSql = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_cenario, " . $result2['id_lexico'] . ")";
                    mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                }
            }   // if
        }   // while
        //Sinonimos

        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $idProject  AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();

        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {

            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }

        $qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $idProject 
              AND id_cenario = $id_cenario";
        $count = count($nomesSinonimos);
        for ($i = 0; $i < $count; $i++) {

            $requestResultSQL = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result = mysql_fetch_array($requestResultSQL)) {    // verifica sinonimos dos lexicos no cenario inclu�do
                $nomeSinonimoEscapado = escapa_metacaracteres($nomesSinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $objective) != 0) ||
                        (preg_match($regex, $context) != 0) ||
                        (preg_match($regex, $actors) != 0) ||
                        (preg_match($regex, $resources) != 0) ||
                        (preg_match($regex, $episodes) != 0) ||
                        (preg_match($regex, $exception) != 0)) {

                    $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_cenario AND id_lexico = $id_lexicoSinonimo[$i] ";
                    $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayCen = mysql_fetch_array($qrCen);

                    if ($resultArrayCen == false) {
                        $comandoSql = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_cenario, $id_lexicoSinonimo[$i])";
                        mysql_query($comandoSql) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                    }
                }   // if
            }   // while
        } //for
    }

}


###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################
if (!(function_exists("removeLexico"))) {

    function removeLexico($idProject , $id_lexico) {
        $DB = new PGDB ();
        $delete = new QUERY($DB);

        # Remove o relacionamento entre o lexico a ser removido
        # e outros lexicos que o referenciam
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico");
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico");
        $delete->execute("DELETE FROM centolex WHERE id_lexico = $id_lexico");

        # Remove o lexico escolhido
        $delete->execute("DELETE FROM sinonimo WHERE id_lexico = $id_lexico");
        $delete->execute("DELETE FROM lexico WHERE id_lexico = $id_lexico");
    }

}



###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################

if (!(function_exists("alteraLexico"))) {

    function alteraLexico($idProject , $id_lexico, $nome, $notion, $impact, $sinonimos, $classificacao) {
        $DB = new PGDB ();
        $delete = new QUERY($DB);

        # Remove os relacionamento existentes anteriormente

        $delete->execute("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico");
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico");
        $delete->execute("DELETE FROM centolex WHERE id_lexico = $id_lexico");

        # Remove todos os sinonimos cadastrados anteriormente

        $delete->execute("DELETE FROM sinonimo WHERE id_lexico = $id_lexico");

        # Altera o lexico escolhido

        $delete->execute("UPDATE lexico SET 
		nocao = '" . prepara_dado($notion) . "', 
		impacto = '" . prepara_dado($impact) . "', 
		tipo = '$classificacao' 
		where id_lexico = $id_lexico");

        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        # Fim altera lexico escolhido
        ### VERIFICACAO DE OCORRENCIA EM CENARIOS ###
        ########
        # Verifica se h� alguma ocorrencia do titulo do lexico nos cenarios existentes no banco

        $qr = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
              FROM cenario
              WHERE id_projeto = $idProject ";

        $requestResultSQL = mysql_query($qr) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($requestResultSQL)) {    // 2  - Para todos os cenarios
            $nomeEscapado = escapa_metacaracteres($nome);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['objetivo']) != 0) ||
                    (preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['atores']) != 0) ||
                    (preg_match($regex, $result['recursos']) != 0) ||
                    (preg_match($regex, $result['excecao']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) { //2.2
                $comandoSql = "INSERT INTO centolex (id_cenario, id_lexico)
                     VALUES (" . $result['id_cenario'] . ", $id_lexico)"; //2.2.1

                mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }//if
        }//while
        # Fim da verificacao
        ########
        # Verifica se h� alguma ocorrencia de algum dos sinonimos do lexico nos cenarios existentes no banco
        //&sininonimos = sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {//Para cada sinonimo
            $requestResultSQL = mysql_query($qr) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result2 = mysql_fetch_array($requestResultSQL)) {// para cada cenario

                $nomeSinonimoEscapado = escapa_metacaracteres($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $result2['objetivo']) != 0) ||
                        (preg_match($regex, $result2['contexto']) != 0) ||
                        (preg_match($regex, $result2['atores']) != 0) ||
                        (preg_match($regex, $result2['recursos']) != 0) ||
                        (preg_match($regex, $result2['excecao']) != 0) ||
                        (preg_match($regex, $result2['episodios']) != 0)) {
                    // $comandoSql = "INSERT INTO centolex (id_cenario, id_lexico)
                    //      VALUES (" . $result2['id_cenario'] . ", $id_lexico)";                   
                    //  mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }//if                
            }//while            
        } //for
        # Fim da verificacao
        ########
        ### VERIFICACAO DE OCORRENCIA EM LEXICOS
        ########
        # Verifica a ocorrencia do titulo do lexico alterado no texto dos outros lexicos
        # Verifica a ocorrencia do titulo dos outros lexicos no lexico alterado
        //select para pegar todos os outros lexicos
        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $idProject 
               AND id_lexico <> $id_lexico";

        $requestResultSQL = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($requestResultSQL)) { // para cada lexico exceto o que esta sendo alterado    // (3)
            # Verifica a ocorrencia do titulo do lexico alterado no texto dos outros lexicos
            $nomeEscapado = escapa_metacaracteres($nome);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['nocao']) != 0 ) ||
                    (preg_match($regex, $result['impacto']) != 0)) {
                $comandoSql = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                      	VALUES (" . $result['id_lexico'] . ", $id_lexico)";

                mysql_query($comandoSql) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }

            # Verifica a ocorrencia do titulo dos outros lexicos no texto do lexico alterado

            $nomeEscapado = escapa_metacaracteres($result['nome']);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $notion) != 0) ||
                    (preg_match($regex, $impact) != 0)) {   // (3.3)        
                $comandoSql = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) 
                		VALUES ($id_lexico, " . $result['id_lexico'] . ")";

                mysql_query($comandoSql) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }// while
        # Fim da verificao por titulo

        $ql = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_projeto = $idProject 
              AND id_lexico <> $id_lexico";

        # Verifica a ocorrencia dos sinonimos do lexico alterado nos outros lexicos

        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {// para cada sinonimo do lexico alterado

            $requestResultSQL = mysql_query($ql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($resultl = mysql_fetch_array($requestResultSQL)) {// para cada lexico exceto o alterado
                $nomeSinonimoEscapado = escapa_metacaracteres($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                // verifica sinonimo[i] do lexico alterado no texto de cada lexico

                if ((preg_match($regex, $resultl['nocao']) != 0) ||
                        (preg_match($regex, $resultl['impacto']) != 0)) {

                    // Verifica  se a relacao encontrada ja se encontra no banco de dados. Se tiver nao faz nada, senao cadastra uma nopva relacao
                    $qverif = "SELECT * FROM lextolex where id_lexico_from=" . $resultl['id_lexico'] . " and id_lexico_to=$id_lexico";
                    echo("Query: " . $qverif . "<br>");
                    $resultado = mysql_query($qverif) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    if (!resultado) {
                        $comandoSql = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES (" . $resultl['id_lexico'] . ", $id_lexico)";
                        mysql_query($comandoSql) or die("Erro ao enviar a query de insert(sinonimo2) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                }//if
            }//while
        }//for
        # Verifica a ocorrencia dos sinonimos dos outros lexicos no lexico alterado

        $qSinonimos = "SELECT nome, id_lexico 
        		FROM sinonimo 
        		WHERE id_projeto = $idProject  
        		AND id_lexico <> $id_lexico 
        		AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();
        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {
            $nomeSinonimoEscapado = escapa_metacaracteres($rowSinonimo["nome"]);
            $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $notion) != 0) ||
                    (preg_match($regex, $impact) != 0)) {

                // Verifica  se a relacao encontrada ja se encontra no banco de dados. Se tiver nao faz nada, senao cadastra uma nopva relacao
                $qv = "SELECT * FROM lextolex where id_lexico_from=$id_lexico and id_lexico_to=" . $rowSinonimo['id_lexico'];
                $resultado = mysql_query($qv) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                if (!resultado) {
                    $comandoSql = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES ($id_lexico, " . $rowSinonimo['id_lexico'] . ")";

                    mysql_query($comandoSql) or die("Erro ao enviar a query de insert(sinonimo) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
            }
        }

        # Cadastra os sinonimos novamente

        if (!is_array($sinonimos))
            $sinonimos = array();

        foreach ($sinonimos as $novoSin) {
            $comandoSql = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                VALUES ($id_lexico, '" . prepara_dado(strtolower($novoSin)) . "', $idProject )";

            mysql_query($comandoSql, $r) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }

        # Fim - cadastro de sinonimos        
    }

}



###################################################################
# Essa funcao recebe um id de conceito e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeConceito"))) {

    function removeConceito($idProject , $id_conceito) {
        $DB = new PGDB ();
        $sql = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);
        $sql5 = new QUERY($DB);
        $sql6 = new QUERY($DB);
        $sql7 = new QUERY($DB);
        # Este select procura o cenario a ser removido
        # dentro do projeto

        $sql2->execute("SELECT * FROM conceito WHERE id_projeto = $idProject  and id_conceito = $id_conceito");
        if ($sql2->getntuples() == 0) {
            //echo "<BR> Cenario nao existe para esse projeto." ;
        } else {
            $record = $sql2->gofirst();
            $nomeConceito = $record['nome'];
            # tituloCenario = Nome do cenario com id = $id_cenario
        }
        # [ATENCAO] Essa query pode ser melhorada com um join
        //print("<br>SELECT * FROM cenario WHERE id_projeto = $idProject ");
        /*  $sql->execute ("SELECT * FROM cenario WHERE id_projeto = $idProject  AND id_cenario != $tituloCenario");
          if ($sql->getntuples() == 0){
          echo "<BR> Projeto n�o possui cenarios." ;
          }else{ */
        $qr = "SELECT * FROM conceito WHERE id_projeto = $idProject  AND id_conceito != $id_conceito";
        //echo($qr)."          ";
        $requestResultSQL = mysql_query($qr) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result = mysql_fetch_array($requestResultSQL)) {
            # Percorre todos os cenarios tirando as tag do conceito
            # a ser removido
            //$record = $sql->gofirst ();
            //while($record !='LAST_RECORD_REACHED'){
            $idConceitoRef = $result['id_conceito'];
            $nomeAnterior = $result['nome'];
            $descricaoAnterior = $result['descricao'];
            $namespaceAnterior = $result['namespace'];
            #echo        "/<a title=\"Cen�rio\" href=\"main.php?t='c'&id=$id_cenario>($tituloCenario)<\/a>/mi"  ;
            #$episodiosAnterior = "<a title=\"Cen�rio\" href=\"main.php?t=c&id=38\">robin</a>" ;
            /* "'<a title=\"Cen�rio\" href=\"main.php?t=c&id=38\">robin<\/a>'si" ; */
            $tiratag = "'<[\/\!]*?[^<>]*?>'si";
            //$tiratagreplace = "";
            //$tituloCenario = preg_replace($tiratag,$tiratagreplace,$tituloCenario);
            $regexp = "/<a[^>]*?>($nomeConceito)<\/a>/mi"; //rever
            $replace = "$1";
            //echo($episodiosAnterior)."   ";
            //$tituloAtual = $tituloAnterior ;
            //*$tituloAtual = preg_replace($regexp,$replace,$tituloAnterior);*/
            $descricaoAtual = preg_replace($regexp, $replace, $descricaoAnterior);
            $namespaceAtual = preg_replace($regexp, $replace, $namespaceAnterior);
            /* echo "ant:".$episodiosAtual ;
              echo "<br>" ;
              echo "dep:".$episodiosAnterior ; */
            // echo($tituloCenario)."   ";
            // echo($episodiosAtual)."  ";
            //print ("<br>update cenario set objetivo = '$objetivoAtual',contexto = '$contextoAtual',atores = '$atoresAtual',recursos = '$recursosAtual',episodios = '$episodiosAtual' where id_cenario = $idCenarioRef ");
            $sql7->execute("update conceito set descricao = '$descricaoAtual', namespace = '$namespaceAtual' where id_conceito = $idConceitoRef ");

            //$record = $sql->gonext() ;
            // }
        }

        # Remove o conceito escolhido
        $sql6->execute("DELETE FROM conceito WHERE id_conceito = $id_conceito");
        $sql6->execute("DELETE FROM relacao_conceito WHERE id_conceito = $id_conceito");
    }

}
###################################################################
# Essa funcao recebe um id de relacao e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeRelacao"))) {

    function removeRelacao($idProject , $id_relacao) {
        $DB = new PGDB ();

        $sql6 = new QUERY($DB);

        # Remove o conceito escolhido
        $sql6->execute("DELETE FROM relacao WHERE id_relacao = $id_relacao");
        $sql6->execute("DELETE FROM relacao_conceito WHERE id_relacao = $id_relacao");
    }

}

###################################################################
# Funcao faz um select na tabela lexico.
# Para inserir um novo lexico, deve ser verificado se ele ja existe,
# ou se existe um sinonimo com o mesmo nome.
# Recebe o id do projeto e o nome do lexico (1.0)
# Faz um SELECT na tabela lexico procurando por um nome semelhante
# no projeto (1.1)
# Faz um SELECT na tabela sinonimo procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################

function checarLexicoExistente($projeto, $nome) {
    $naoexiste = false;

    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $comandoSql = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$nome' ";
    $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray == false) {
        $naoexiste = true;
    }

    $comandoSql = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$nome' ";
    $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);

    if ($resultArray != false) {
        $naoexiste = false;
    }

    return $naoexiste;
}

###################################################################
# Recebe o id do projeto e a lista de sinonimos (1.0)
# Funcao faz um select na tabela sinonimo.
# Para verificar se ja existe um sinonimo igual no BD.
# Faz um SELECT na tabela lexico para verificar se ja existe
# um lexico com o mesmo nome do sinonimo.(1.1)
# retorna true caso nao exista ou false caso exista (1.2)
###################################################################

function checarSinonimo($projeto, $listSinonimo) {
    $naoexiste = true;

    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    foreach ($listSinonimo as $sinonimo) {

        $comandoSql = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray != false) {
            $naoexiste = false;
            return $naoexiste;
        }

        $comandoSql = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray != false) {
            $naoexiste = false;
            return $naoexiste;
        }
    }

    return $naoexiste;
}

###################################################################
# Funcao faz um select na tabela cenario.
# Para inserir um novo cenario, deve ser verificado se ele ja existe.
# Recebe o id do projeto e o titulo do cenario (1.0)
# Faz um SELECT na tabela cenario procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################

function checarCenarioExistente($projeto, $title) {
    $naoexiste = false;

    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $comandoSql = "SELECT * FROM cenario WHERE id_projeto = $projeto AND titulo = '$title' ";
    $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray == false) {
        $naoexiste = true;
    }

    return $naoexiste;
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo cenario ela deve receber os campos do novo
# cenario.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este cenario caso o criador n�o seja o gerente.
# Arquivos que utilizam essa funcao:
# add_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAdicionarCenario"))) {

    function inserirPedidoAdicionarCenario($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $comandoSql = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente
            $insere->execute("INSERT INTO pedidocen (id_projeto, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado) VALUES ($idProject , '$title', '$objective', '$context', '$actors', '$resources', '$exception', '$episodes', $id_usuario, 'inserir', 0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $idProject ");
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Inclus�o Cen�rio", "O usuario do sistema $nome\nPede para inserir o cenario $title \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else { //Eh gerente
            adicionar_cenario($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um cenario ela deve receber os campos do cenario
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerentes do projeto
# referente a este cenario caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) {

    function inserirPedidoAlterarCenario($idProject , $id_cenario, $title, $objective, $context, $actors, $resources, $exception, $episodes, $justificativa, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $comandoSql = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($idProject , $id_cenario, '$title', '$objective', '$context', '$actors', '$resources', '$exception', '$episodes', $id_usuario, 'alterar', 0, '$justificativa')");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $idProject ");
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Altera��o Cen�rio", "O usuario do sistema $nome\nPede para alterar o cenario $title \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else { //Eh gerente
            alteraCenario($idProject , $id_cenario, $title, $objective, $context, $actors, $resources, $exception, $episodes);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um cenario ela deve receber
# o id do cenario e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_cenario.php
###################################################################
if (!(function_exists("inserirPedidoRemoverCenario"))) {

    function inserirPedidoRemoverCenario($idProject , $id_cenario, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $comandoSql = ("SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ");
        $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);

        if ($resultArray == false) { //Nao e gerente
            $select->execute("SELECT * FROM cenario WHERE id_cenario = $id_cenario");
            $cenario = $select->gofirst();
            $title = $cenario['titulo'];
            $insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, id_usuario, tipo_pedido, aprovado) VALUES ($idProject , $id_cenario, '$title', $id_usuario, 'remover', 0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $idProject ");
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover Cen�rio", "O usuario do sistema $nome\nPede para remover o cenario $id_cenario \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else {
            removeCenario($idProject , $id_cenario);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo lexico ela deve receber os campos do novo
# lexicos.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador n�o seja o gerente.
# Arquivos que utilizam essa funcao:
# add_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAdicionarLexico"))) {

    function inserirPedidoAdicionarLexico($idProject , $nome, $notion, $impact, $id_usuario, $sinonimos, $classificacao) {

        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $comandoSql = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidolex (id_projeto,nome,nocao,impacto,tipo,id_usuario,tipo_pedido,aprovado) VALUES ($idProject ,'$nome','$notion','$impact','$classificacao',$id_usuario,'inserir',0)");

            $newId = $insere->getLastId();

            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'");

            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $idProject ");


            //insere sinonimos

            foreach ($sinonimos as $sin) {
                $insere->execute("INSERT INTO sinonimo (id_pedidolex, nome, id_projeto) 
				VALUES ($newId, '" . prepara_dado(strtolower($sin)) . "', $idProject )");
            }
            //fim da insercao dos sinonimos

            if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
            } else {

                $record = $select->gofirst();
                $nome2 = $record['nome'];
                $email = $record['email'];
                $record2 = $select2->gofirst();
                while ($record2 != 'LAST_RECORD_REACHED') {
                    $id = $record2['id_usuario'];
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst();
                    $mailGerente = $record['email'];
                    mail("$mailGerente", "Pedido de Inclus�o de L�xico", "O usuario do sistema $nome2\nPede para inserir o lexico $nome \nObrigado!", "From: $nome2\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } else { //Eh gerente
            adicionar_lexico($idProject , $nome, $notion, $impact, $sinonimos, $classificacao);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um lexico ela deve receber os campos do lexicos
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAlterarLexico"))) {

    function inserirPedidoAlterarLexico($idProject , $id_lexico, $nome, $notion, $impact, $justificativa, $id_usuario, $sinonimos, $classificacao) {

        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $comandoSql = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,nocao,impacto,id_usuario,tipo_pedido,aprovado,justificativa, tipo) VALUES ($idProject ,$id_lexico,'$nome','$notion','$impact',$id_usuario,'alterar',0,'$justificativa', '$classificacao')");

            $newPedidoId = $insere->getLastId();

            //sinonimos
            foreach ($sinonimos as $sin) {
                $insere->execute("INSERT INTO sinonimo (id_pedidolex,nome,id_projeto) 
				VALUES ($newPedidoId,'" . prepara_dado(strtolower($sin)) . "', $idProject )");
            }


            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $idProject ");

            if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
            } else {
                $record = $select->gofirst();
                $nome2 = $record['nome'];
                $email = $record['email'];
                $record2 = $select2->gofirst();
                while ($record2 != 'LAST_RECORD_REACHED') {
                    $id = $record2['id_usuario'];
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst();
                    $mailGerente = $record['email'];
                    mail("$mailGerente", "Pedido de Alterar L�xico", "O usuario do sistema $nome2\nPede para alterar o lexico $nome \nObrigado!", "From: $nome2\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } else { //Eh gerente
            alteraLexico($idProject , $id_lexico, $nome, $notion, $impact, $sinonimos, $classificacao);
        }
    }

}
###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um lexico ela deve receber
# o id do lexico e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_lexico.php
###################################################################
if (!(function_exists("inserirPedidoRemoverLexico"))) {

    function inserirPedidoRemoverLexico($idProject , $id_lexico, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $comandoSql = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);

        if ($resultArray == false) { //nao e gerente

            $select->execute("SELECT * FROM lexico WHERE id_lexico = $id_lexico");
            $lexico = $select->gofirst();
            $nome = $lexico['nome'];

            $insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,id_usuario,tipo_pedido,aprovado) VALUES ($idProject ,$id_lexico,'$nome',$id_usuario,'remover',0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $idProject ");

            if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
            } else {
                $record = $select->gofirst();
                $nome = $record['nome'];
                $email = $record['email'];
                $record2 = $select2->gofirst();
                while ($record2 != 'LAST_RECORD_REACHED') {
                    $id = $record2['id_usuario'];
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst();
                    $mailGerente = $record['email'];
                    mail("$mailGerente", "Pedido de Remover L�xico", "O usuario do sistema $nome2\nPede para remover o lexico $id_lexico \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } else { // e gerente
            removeLexico($idProject , $id_lexico);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um conceito ela deve receber os campos do conceito
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerentes do projeto
# referente a este cenario caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) {

    function inserirPedidoAlterarConceito($idProject , $id_conceito, $nome, $descricao, $namespace, $justificativa, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $comandoSql = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
        $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidocon (id_projeto, id_conceito, nome, descricao, namespace, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($idProject , $id_conceito, '$nome', '$descricao', '$namespace', $id_usuario, 'alterar', 0, '$justificativa')");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $idProject ");
            $record = $select->gofirst();
            $nomeUsuario = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Altera��o Conceito", "O usuario do sistema $nomeUsuario\nPede para alterar o conceito $nome \nObrigado!", "From: $nomeUsuario\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else { //Eh gerente
            removeConceito($idProject , $id_conceito);
            adicionar_conceito($idProject , $nome, $descricao, $namespace);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um conceito ela deve receber
# o id do conceito e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este conceito.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_conceito.php
###################################################################
if (!(function_exists("inserirPedidoRemoverConceito"))) {

    function inserirPedidoRemoverConceito($idProject , $id_conceito, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM conceito WHERE id_conceito = $id_conceito");
        $conceito = $select->gofirst();
        $nome = $conceito['nome'];

        $insere->execute("INSERT INTO pedidocon (id_projeto,id_conceito,nome,id_usuario,tipo_pedido,aprovado) VALUES ($idProject ,$id_conceito,'$nome',$id_usuario,'remover',0)");
        $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
        $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $idProject ");

        if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
        } else {
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_conceito \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover uma relacao ela deve receber
# o id da relacao e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este relacao.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_relacao.php
###################################################################
if (!(function_exists("inserirPedidoRemoverRelacao"))) {

    function inserirPedidoRemoverRelacao($idProject , $id_relacao, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM relacao WHERE id_relacao = $id_relacao");
        $relacao = $select->gofirst();
        $nome = $relacao['nome'];

        $insere->execute("INSERT INTO pedidorel (id_projeto,id_relacao,nome,id_usuario,tipo_pedido,aprovado) VALUES ($idProject ,$id_relacao,'$nome',$id_usuario,'remover',0)");
        $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
        $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $idProject ");

        if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
        } else {
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_relacao \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
    }

}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoCenario"))) {

    function tratarPedidoCenario($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        //print("<BR>SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
        $select->execute("SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_cenario = $record['id_cenario'];
                $idProject  = $record['id_projeto'];
                removeCenario($idProject , $id_cenario);
                //$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
            } else {

                $idProject  = $record['id_projeto'];
                $title = $record['titulo'];
                $objective = $record['objetivo'];
                $context = $record['contexto'];
                $actors = $record['atores'];
                $resources = $record['recursos'];
                $exception = $record['excecao'];
                $episodes = $record['episodios'];
                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_cenario = $record['id_cenario'];
                    removeCenario($idProject , $id_cenario);
                    //$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
                }
                adicionar_cenario($idProject , $title, $objective, $context, $actors, $resources, $exception, $episodes);
            }
            //$delete->execute ("DELETE FROM pedidocen WHERE id_pedido = $id_pedido") ;
        }
    }

}
###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o lexico e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoLexico"))) {

    function tratarPedidoLexico($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $selectSin = new QUERY($DB);
        $select->execute("SELECT * FROM pedidolex WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_lexico = $record['id_lexico'];
                $idProject  = $record['id_projeto'];
                removeLexico($idProject , $id_lexico);
            } else {
                $idProject  = $record['id_projeto'];
                $nome = $record['nome'];
                $notion = $record['nocao'];
                $impact = $record['impacto'];
                $classificacao = $record['tipo'];

                //sinonimos

                $sinonimos = array();
                $selectSin->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_pedido");
                $sinonimo = $selectSin->gofirst();
                if ($selectSin->getntuples() != 0) {
                    while ($sinonimo != 'LAST_RECORD_REACHED') {
                        $sinonimos[] = $sinonimo["nome"];
                        $sinonimo = $selectSin->gonext();
                    }
                }

                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_lexico = $record['id_lexico'];
                    alteraLexico($idProject , $id_lexico, $nome, $notion, $impact, $sinonimos, $classificacao);
                } else if (($idLexicoConflitante = adicionar_lexico($idProject , $nome, $notion, $impact, $sinonimos, $classificacao)) <= 0) {
                    $idLexicoConflitante = -1 * $idLexicoConflitante;

                    $selectLexConflitante->execute("SELECT nome FROM lexico WHERE id_lexico = " . $idLexicoConflitante);

                    $row = $selectLexConflitante->gofirst();

                    return $row["nome"];
                }
            }
            return null;
        }
    }

}
###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarrequestConcept"))) {

    function tratarrequestConcept($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidocon WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_conceito = $record['id_conceito'];
                $idProject  = $record['id_projeto'];
                removeConceito($idProject , $id_conceito);
            } else {

                $idProject  = $record['id_projeto'];
                $nome = $record['nome'];
                $descricao = $record['descricao'];
                $namespace = $record['namespace'];

                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_cenario = $record['id_conceito'];
                    removeConceito($idProject , $id_conceito);
                }
                adicionar_conceito($idProject , $nome, $descricao, $namespace);
            }
        }
    }

}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoRelacao"))) {

    function tratarPedidoRelacao($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidorel WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_relacao = $record['id_relacao'];
                $idProject  = $record['id_projeto'];
                removeRelacao($idProject , $id_relacao);
            } else {

                $idProject  = $record['id_projeto'];
                $nome = $record['nome'];

                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_relacao = $record['id_relacao'];
                    removeRelacao($idProject , $id_relacao);
                }
                adicionar_relacao($idProject , $nome);
            }
        }
    }

}
#############################################
#Deprecated by the author:
#Essa funcao deveria receber um id_projeto
#de forma a verificar se o gerente pertence
#a esse projeto.Ela so verifica atualmente
#se a pessoa e um gerente.
#############################################
if (!(function_exists("verificaGerente"))) {

    function verificaGerente($id_usuario) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $select->execute("SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario");
        if ($select->getntuples() == 0) {
            return 0;
        } else {
            return 1;
        }
    }

}

#############################################
# Formata Data
# Recebe YYY-DD-MM
# Retorna DD-MM-YYYY
#############################################
if (!(function_exists("formataData"))) {

    function formataData($data) {

        $novaData = substr($data, 8, 9) .
                substr($data, 4, 4) .
                substr($data, 0, 4);
        return $novaData;
    }

}





// Retorna TRUE ssse $id_usuario eh admin de $idProject 
if (!(function_exists("is_admin"))) {

    function is_admin($id_usuario, $idProject ) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $comandoSql = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $idProject 
              AND gerente = 1";
        $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($requestResultSQL));
    }

}

// Retorna TRUE ssse $id_usuario tem permissao sobre $idProject 
if (!(function_exists("check_proj_perm"))) {

    function  permissionCheckToProject($id_usuario, $idProject ) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $comandoSql = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $idProject ";
        $requestResultSQL = mysql_query($comandoSql) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($requestResultSQL));
    }

}
###################################################################
# Verifica se um determinado usuario e gerente de um determinado
# projeto
# Recebe o id do projeto. (1.1)
# Faz um select para pegar o resultArray da tabela Participa.(1.2)
# Se o resultArray for nao nulo: devolvemos TRUE(1);(1.3)
# Se o resultArray for nulo: devolvemos False(0);(1.4)
###################################################################

function verificaGerente($id_usuario, $idProject ) {
    $ret = 0;
    $comandoSql = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $idProject ";
    $qr = mysql_query($comandoSql) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);

    if ($resultArray != false) {

        $ret = 1;
    }
    return $ret;
}

###################################################################
# Remove um determinado projeto da base de dados
# Recebe o id do projeto. (1.1)
# Apaga os valores da tabela pedidocen que possuam o id do projeto enviado (1.2)
# Apaga os valores da tabela pedidolex que possuam o id do projeto enviado (1.3)
# Faz um SELECT para saber quais l�xico pertencem ao projeto de id_projeto (1.4)
# Apaga os valores da tabela lextolex que possuam possuam lexico do projeto (1.5)
# Apaga os valores da tabela centolex que possuam possuam lexico do projeto (1.6)
# Apaga os valores da tabela sinonimo que possuam possuam o id do projeto (1.7)
# Apaga os valores da tabela lexico que possuam o id do projeto enviado (1.8)
# Faz um SELECT para saber quais cenario pertencem ao projeto de id_projeto (1.9)
# Apaga os valores da tabela centocen que possuam possuam cenarios do projeto (2.0)
# Apaga os valores da tabela centolex que possuam possuam cenarios do projeto (2.1)
# Apaga os valores da tabela cenario que possuam o id do projeto enviado (2.2)
# Apaga os valores da tabela participa que possuam o id do projeto enviado (2.3)
# Apaga os valores da tabela publicacao que possuam o id do projeto enviado (2.4)
# Apaga os valores da tabela projeto que possuam o id do projeto enviado (2.5)
#
###################################################################

function removeProjeto($idProject ) {
    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os pedidos de cenario
    $qv = "Delete FROM pedidocen WHERE id_projeto = '$idProject ' ";
    $deletaPedidoCenario = mysql_query($qv) or die("Erro ao apagar pedidos de cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os pedidos de lexico
    $qv = "Delete FROM pedidolex WHERE id_projeto = '$idProject ' ";
    $deletaPedidoLexico = mysql_query($qv) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os lexicos //verificar lextolex!!!
    $qv = "SELECT * FROM lexico WHERE id_projeto = '$idProject ' ";
    $qvr = mysql_query($qv) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    while ($result = mysql_fetch_array($qvr)) {
        $id_lexico = $result['id_lexico']; //seleciona um lexico

        $qv = "Delete FROM lextolex WHERE id_lexico_from = '$id_lexico' OR id_lexico_to = '$id_lexico' ";
        $deletaLextoLe = mysql_query($qv) or die("Erro ao apagar pedidos do lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM centolex WHERE id_lexico = '$id_lexico'";
        $deletacentolex = mysql_query($qv) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        //$qv = "Delete FROM sinonimo WHERE id_lexico = '$id_lexico'";
        //$deletacentolex = mysql_query($qv) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM sinonimo WHERE id_projeto = '$idProject '";
        $deletacentolex = mysql_query($qv) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }

    $qv = "Delete FROM lexico WHERE id_projeto = '$idProject ' ";
    $deletaLexico = mysql_query($qv) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remove os cenarios
    $qv = "SELECT * FROM cenario WHERE id_projeto = '$idProject ' ";
    $qvr = mysql_query($qv) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArrayCenario = mysql_fetch_array($qvr);

    while ($result = mysql_fetch_array($qvr)) {
        $id_lexico = $result['id_cenario']; //seleciona um lexico

        $qv = "Delete FROM centocen WHERE id_cenario_from = '$id_cenario' OR id_cenario_to = '$id_cenario' ";
        $deletaCentoCen = mysql_query($qv) or die("Erro ao apagar pedidos do centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM centolex WHERE id_cenario = '$id_cenario'";
        $deletaLextoLe = mysql_query($qv) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }

    $qv = "Delete FROM cenario WHERE id_projeto = '$idProject ' ";
    $deletaLexico = mysql_query($qv) or die("Erro ao apagar pedidos do cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remover participantes
    $qv = "Delete FROM participa WHERE id_projeto = '$idProject ' ";
    $deletaParticipantes = mysql_query($qv) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remover publicacao
    $qv = "Delete FROM publicacao WHERE id_projeto = '$idProject ' ";
    $deletaPublicacao = mysql_query($qv) or die("Erro ao apagar no publicacao<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remover projeto
    $qv = "Delete FROM projeto WHERE id_projeto = '$idProject ' ";
    $deletaProjeto = mysql_query($qv) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
}
?>
