    <?php
session_start();


if (isset($_GET['id_projeto'])) {
    $idProject  = $_GET['id_projeto'];
}


include("funcoes_genericas.php");
include_once("bd.inc");

checkUserAuthentication("index.php");
//$idProject  = 2; 
?>  

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 

<?php

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($idProject )) {
     permissionCheckToProject($_SESSION['id_usuario_corrente'], $idProject ) or die("Permissao negada");
    
    $commandSQL = "SELECT nome FROM projeto WHERE id_projeto = $idProject ";
    $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query");
    
    $resultArray = mysql_fetch_array($requestResultSQL);
    $nome_projeto = $resultArray['nome'];
    
} else {
    ?>  

    <script language="javascript1.3">

        top.frames['menu'].document.writeln('<font color="red">Nenhum projeto selecionado</font>');

    </script> 

    <?php
    exit();
}
?>  

<html> 
    <head> 

        <script type="text/javascript">
        // Framebuster script to relocate browser when MSIE bookmarks this 
        // page instead of the parent frameset.  Set variable relocateURL to 
        // the index document of your website (relative URLs are ok): 
            /*var relocateURL = "/"; 
     
             if (parent.frames.length == 0) { 
             if(document.images) { 
             location.replace(relocateURL); 
             } else { 
             location = relocateURL; 
             } 
             }*/
        </script> 

        <script type="text/javascript" src="mtmcode.js">
        </script> 

        <script type="text/javascript">
        // Morten's JavaScript Tree Menu 
        // version 2.3.2, dated 2002-02-24 
        // http://www.treemenu.com/ 

        // Copyright (c) 2001-2002, Morten Wang & contributors 
        // All rights reserved. 

        // This software is released under the BSD License which should accompany 
        // it in the file "COPYING".  If you do not have this file you can access 
        // the license through the WWW at http://www.treemenu.com/license.txt 

        // Nearly all user-configurable options are set to their default values. 
        // Have a look at the section "Setting options" in the installation guide 
        // for description of each option and their possible values. 

            MTMDefaultTarget = "text";
            MTMenuText = "<?= $nome_projeto ?>";

            /****************************************************************************** 
             * User-configurable list of icons.                                            * 
             ******************************************************************************/

            var MTMIconList = null;
            MTMIconList = new IconList();
            MTMIconList.addIcon(new MTMIcon("menu_link_external.gif", "http://", "pre"));
            MTMIconList.addIcon(new MTMIcon("menu_link_pdf.gif", ".pdf", "post"));

            /****************************************************************************** 
             * User-configurable menu.                                                     * 
             ******************************************************************************/

            var menu = null;
            menu = new MTMenu();
            menu.addItem("Cen�rios");
        // + submenu 
            var mc = null;
            mc = new MTMenu();

<?php
$commandSQL = "SELECT id_cenario, titulo  
                  FROM cenario  
                  WHERE id_projeto = $idProject   
                  ORDER BY titulo";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");

// We must remove all the tags HTML of the scenario's title. Possibly
// there will be tags of links (<a> </a>). If we won't remove it, there will be
// error when we show it in the menu.
$search = "'<[\/\!]*?[^<>]*?>'si";
$replace = "";

// For each scenario of project
while ($row = mysql_fetch_row($requestResultSQL)) {
    $row[1] = preg_replace($search, $replace, $row[1]);
    ?>

                mc.addItem("<?= $row[1] ?>", "main.php?id=<?= $row[0] ?>&t=c");

            // + submenu 
                var mcs_<?= $row[0] ?> = null;
                mcs_<?= $row[0] ?> = new MTMenu();
                mcs_<?= $row[0] ?>.addItem("Sub-cen�rios", "", null, "Cen�rios que este cen�rio referencia");
            // + submenu 
                var mcsrc_<?= $row[0] ?> = null;
                mcsrc_<?= $row[0] ?> = new MTMenu();

    <?php
    $commandSQL = "SELECT c.id_cenario_to, cen.titulo FROM centocen c, cenario cen WHERE c.id_cenario_from = " . $row[0];
    $commandSQL = $commandSQL . " AND c.id_cenario_to = cen.id_cenario";
    $qrr_2 = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");
    while ($row_2 = mysql_fetch_row($qrr_2)) {
        $row_2[1] = preg_replace($search, $replace, $row_2[1]);
        ?>

                    mcsrc_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=c&cc=<?= $row[0] ?>");

        <?php
    }
    ?>

            // - submenu 
                mcs_<?= $row[0] ?>.makeLastSubmenu(mcsrc_<?= $row[0] ?>);

            // - submenu 
                mc.makeLastSubmenu(mcs_<?= $row[0] ?>);

    <?php
}
?>

        // - submenu 
            menu.makeLastSubmenu(mc);




            menu.addItem("L�xico");
        // + submenu 
            var ml = null;
            ml = new MTMenu();

<?php
$commandSQL = "SELECT id_lexico, nome  
                  FROM lexico  
                  WHERE id_projeto = $idProject   
                  ORDER BY nome";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");

// For each Lexicon of project
while ($row = mysql_fetch_row($requestResultSQL)) {
    ?>

                ml.addItem("<?= $row[1] ?>", "main.php?id=<?= $row[0] ?>&t=l");
            // + submenu 
                var mls_<?= $row[0] ?> = null;
                mls_<?= $row[0] ?> = new MTMenu();
            // mls_<?= $row[0] ?>.addItem("L�xico", "", null, "Termos do l�xico que este termo referencia"); 
            // + submenu 
            // var mlsrl_<?= $row[0] ?> = null; 
            // mlsrl_<?= $row[0] ?> = new MTMenu(); 

    <?php
    $commandSQL = "SELECT l.id_lexico_to, lex.nome FROM lextolex l, lexico lex WHERE l.id_lexico_from = " . $row[0];
    $commandSQL = $commandSQL . " AND l.id_lexico_to = lex.id_lexico";
    $qrr_2 = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");
    while ($row_2 = mysql_fetch_row($qrr_2)) {
        ?>

                // mlsrl_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=l&ll=<?= $row[0] ?>"); 
                    mls_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=l&ll=<?= $row[0] ?>");

        <?php
    }
    ?>

            // - submenu 
            // mls_<?= $row[0] ?>.makeLastSubmenu(mlsrl_<?= $row[0] ?>); 
            // - submenu 
                ml.makeLastSubmenu(mls_<?= $row[0] ?>);

    <?php
}
?>

        // -submenu 
            menu.makeLastSubmenu(ml);










        // ONTOLGIA 
        // + submenu 
            menu.addItem("Ontologia");
            var mo = null;
            mo = new MTMenu();

        // -submenu 
            menu.makeLastSubmenu(mo);


        // CONCEITO 
        // ++ submenu 
            mo.addItem("Conceitos");
            var moc = null;
            moc = new MTMenu();

<?php
$commandSQL = "SELECT id_conceito, nome  
                  FROM conceito 
                  WHERE id_projeto = $idProject   
                  ORDER BY nome";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");
while ($row = mysql_fetch_row($requestResultSQL)) {  // para cada conceito do projeto 
    print "moc.addItem(\"$row[1]\", \"main.php?id=$row[0]&t=oc\");";
}
?>

        // --submenu 
            mo.makeLastSubmenu(moc);




        // RELA��ES 
        // ++ submenu 
            mo.addItem("Rela��es");
            var mor = null;
            mor = new MTMenu();

<?php
$commandSQL = "SELECT   id_relacao, nome 
                  FROM     relacao r 
                  WHERE    id_projeto = $idProject   
                  ORDER BY nome";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");
while ($row = mysql_fetch_row($requestResultSQL)) {   // para cada rela��o do projeto 
    print "mor.addItem(\"$row[1]\", \"main.php?id=$row[0]&t=or\");";
}
?>

        // --submenu    
            mo.makeLastSubmenu(mor);




        // AXIOMAS 
        // ++ submenu 
            mo.addItem("Axiomas");
            var moa = null;
            moa = new MTMenu();

<?php
$commandSQL = "SELECT   id_axioma, axioma 
                 FROM     axioma 
                 WHERE    id_projeto = $idProject   
                 ORDER BY axioma";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");

while ($row = mysql_fetch_row($requestResultSQL)) {  // para cada axioma do projeto 
    $axi = explode(" disjoint ", $row[1]);
    print "moa.addItem(\"$axi[0]\", \"main.php?id=$row[0]&t=oa\");";
}
?>

        // --submenu    
            mo.makeLastSubmenu(moa);



        </script> 
    </head> 
    <body onload="MTMStartMenu(true)" bgcolor="#000033" text="#ffffcc" link="yellow" vlink="lime" alink="red"> 
    </body> 
</html> 
