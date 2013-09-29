<?php
session_start();

include("funcoes_genericas.php");

checkUserAuthentication("index.php");        // Checa se o usuario foi autenticado
?>

<html>
    <head>
        <script language="javascript1.3">

            // Funcoes que serao usadas quando o script
            // for chamado atraves dele proprio ou da arvore
            
    function reLoad(URL) {
                
        document.location.replace(URL);
    }
        
    function changeScenario(cenario) {
                
        var url = 'alt_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
          
        open(url, where, window_spec);
    }

            
    function removeScenario(cenario) {
                
        var url = 'rmv_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
        
        open(url, where, window_spec);
     }

     function changeLexicon(lexico) {
                
        var url = 'alt_lexico.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function removeLexicon(lexico) {
                var url = 'rmv_lexico.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            // Funcoes que serao usadas quando o script
            // for chamado atraves da heading.php
            function requestScenario() {
                var url = 'ver_pedido_cenario.php?id_projeto=' + '<?= $idProject  ?>';
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function requestLexicon() {
                var url = 'ver_pedido_lexico.php?id_projeto=' + '<?= $idProject  ?>';
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function addUser() {
                var url = 'add_usuario.php';
                var where = '_blank';
                var window_spec = 'dependent,height=270,width=490,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function relateUsers() {
                var url = 'rel_usuario.php';
                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function generateXML() {
                var url = 'xml_gerador.php?id_projeto=' + '<?= $idProject  ?>';
                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }
        </script>
        <script type="text/javascript" src="mtmtrack.js">
        </script>
    </head>
    <body>

<?php
include("frame_inferior.php");

if (isset($id) && isset($term)) {      // SCRIPT CHAMADO PELO PROPRIO MAIN.PHP (OU PELA ARVORE)
    if ($term == "c") {
        ?>

                <h3>Informa��es sobre o cen�rio</h3>

                <?php
            } else {
                ?>

                <h3>Informa��es sobre o l�xico</h3>

                <?php
            }
            ?>

            <table>

            <?php
            $SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

            if ($term == "c") {        // se for cenario
                $commandSQL = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, episodios
              FROM cenario
              WHERE id_cenario = $id";
                $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");
                $resultArray = mysql_fetch_array($requestResultSQL);
                ?>

                    <tr>
                        <td>Titulo:</td><td><?= $resultArray['titulo'] ?></td>
                    </tr>
                    <tr>
                        <td>Objetivo:</td><td><?= $resultArray['objetivo'] ?></td>
                    </tr>
                    <tr>
                        <td>Contexto:</td><td><?= $resultArray['contexto'] ?></td>
                    </tr>
                    <tr>
                        <td>Atores:</td><td><?= $resultArray['atores'] ?></td>
                    </tr>
                    <tr>
                        <td>Recursos:</td><td><?= $resultArray['recursos'] ?></td>
                    </tr>
                    <tr>
                        <td>Epis�dios:</td><td><?= $resultArray['episodios'] ?></td>
                    </tr>
                    <tr>
                        <td height="40" valign="bottom">
                            <a href="#" onClick="changeScenario(<?= $resultArray['id_cenario'] ?>);">Alterar Cen�rio</a>
                        </td>
                        <td valign="bottom">
                            <a href="#" onClick="removeScenario(<?= $resultArray['id_cenario'] ?>);">Remover Cen�rio</a>
                        </td>
                    </tr>

        <?php
    } else {
        $commandSQL = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_lexico = $id";
        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");
        $resultArray = mysql_fetch_array($requestResultSQL);
        ?>

                    <tr>
                        <td>Nome:</td><td><?= $resultArray['nome'] ?></td>
                    </tr>
                    <tr>
                        <td>No��o:</td><td><?= $resultArray['nocao'] ?></td>
                    </tr>
                    <tr>
                        <td>Impacto:</td><td><?= $resultArray['impacto'] ?></td>
                    </tr>
                    <tr>
                        <td height="40" valign="bottom">
                            <a href="#" onClick="changeLexicon(<?= $resultArray['id_lexico'] ?>);">Alterar L�xico</a>
                        </td>
                        <td valign="bottom">
                            <a href="#" onClick="removeLexicon(<?= $resultArray['id_lexico'] ?>);">Remover L�xico</a>
                        </td>
                    </tr>

        <?php
    }
    ?>

            </table>
            <br>
            <br>
            <br>

    <?php
    if ($term == "c") {
        ?>

                <h3>Cen�rios que referenciam este cen�rio</h3>

                <?php
            } else {
                ?>

                <h3>Cen�rios e termos do l�xico que referenciam este termo</h3>

                <?php
            }

            bottom_frame($SgbdConnect, $term, $id);
        } elseif (isset($idProject )) {         // SCRIPT CHAMADO PELO HEADING.PHP
            // Foi passada uma variavel $idProject . Esta variavel deve conter o id de um
            // projeto que o usuario esteja cadastrado. Entretanto, como a passagem eh
            // feita usando JavaScript (no heading.php), devemos checar se este id realmente
            // corresponde a um projeto que o usuario tenha acesso (seguranca).
             permissionCheckToProject($_SESSION['id_usuario_corrente'], $idProject ) or die("Permissao negada");

            // Seta uma variavel de sessao correspondente ao projeto atual
            $_SESSION['id_projeto_corrente'] = $idProject ;
            ?>

            <table>
                <tr>
                    <td>Projeto:</td>
                    <td><?= simple_query("nome", "projeto", "id_projeto = $idProject ") ?></td>
                </tr>
                <tr>
                    <td>Data de cria��o:</td>
                    <td><?= simple_query("TO_CHAR(data_criacao, 'DD/MM/YY')", "projeto", "id_projeto = $idProject ") ?></td>
                </tr>
                <tr>
                    <td>Descri��o:</td>
                    <td><?= simple_query("descricao", "projeto", "id_projeto = $idProject ") ?></td>
                </tr>
            </table>

    <?php
    // Verifica se o usuario eh administrador deste projeto
    if (is_admin($_SESSION['id_usuario_corrente'], $idProject )) {
        ?>

                <br>
                <p><b>Voc� � um administrador deste projeto</b></p>
                <p><a href="#" onClick="requestScenario();">Verificar pedidos de altera��o de Cen�rios</a></p>
                <p><a href="#" onClick="requestLexicon();">Verificar pedidos de altera��o de termos do L�xico</a></p>
                <p><a href="#" onClick="addUser();">Adicionar usu�rio (n�o existente) neste projeto</a></p>
                <p><a href="#" onClick="relateUsers();">Relacionar usu�rios j� existentes com este projeto</a></p>
                <p><a href="#" onClick="generateXML();">Gerar XML deste projeto</a></p>

        <?php
    }
} else {        // SCRIPT CHAMADO PELO INDEX.PHP
    ?>

            <p>Selecione um projeto acima, ou crie um novo projeto.</p>

            <?php
        }
        ?>

    </body>
</html>

