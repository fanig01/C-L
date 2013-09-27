<?php

session_start();

include("funcoes_genericas.php");
include_once("bd.inc");

checkUserAuthentication("index.php");

if (isset($_GET['id_projeto'])) {
    $idProject  = $_GET['id_projeto'];
}
else {
    //Nothing should be done
}

?>  

<?php

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($idProject )) {

    permissionCheckToProject($_SESSION['id_usuario_corrente'], $idProject ) or die("Permissao negada");
    
    $commandSQL = "SELECT nome FROM projeto WHERE id_projeto = $idProject ";
    $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query");
    
    $resultArray = mysql_fetch_array($requestResultSQL);
    $nameProject = $resultArray['nome'];
    
}
else {
   
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
                
/* Framebuster script to relocate browser when MSIE bookmarks this 
   page instead of the parent frameset.  Set variable relocateURL to 
   the index document of your website (relative URLs are ok): 
   var relocateURL = "/"; 
 */    
        </script> 

        <script type="text/javascript" src="mtmcode.js">
        </script> 

        <script type="text/javascript">

            MTMDefaultTarget = "text";
            MTMenuText = "<?= $nameProject ?>";
            
            var MTMIconList = null;            
            MTMIconList = new IconList();
            MTMIconList.addIcon(new MTMIcon("menu_link_external.gif", "http://", "pre"));
            MTMIconList.addIcon(new MTMIcon("menu_link_pdf.gif", ".pdf", "post"));
            
            var menu = null;
            menu = new MTMenu();
            menu.addItem("Cen�rios");
            
            var mc = null;
            mc = new MTMenu();

<?php
$commandSQL = "SELECT id_cenario, titulo  
               FROM cenario  
               WHERE id_projeto = $idProject   
               ORDER BY titulo";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");

/* We must remove all the tags HTML of the scenario's title. Possibly
   there will be tags of links (<a> </a>). If we won't remove it, there will be
   error when we show it in the menu.
*/

$search = "'<[\/\!]*?[^<>]*?>'si";
$replace = "";

// For each scenario of project
while ($row = mysql_fetch_row($requestResultSQL)) {

    $row[1] = preg_replace($search, $replace, $row[1]);
    ?>
        
    mc.addItem("<?= $row[1] ?>", "main.php?id=<?= $row[0] ?>&t=c");            
    var mcs_<?= $row[0] ?> = null;
    mcs_<?= $row[0] ?> = new MTMenu();
    
    mcs_<?= $row[0] ?>.addItem("Sub-cen&aacute;rios", "", null, "Cen&aacute;rios que este cen&aacute;rio referencia");
    var mcsrc_<?= $row[0] ?> = null;
    mcsrc_<?= $row[0] ?> = new MTMenu();

    <?php
    
    $commandSQL = "SELECT c.id_cenario_to, cen.titulo 
                   FROM centocen c, cenario cen 
                   WHERE c.id_cenario_from = " . $row[0];
    
    $commandSQL = $commandSQL . " AND c.id_cenario_to = cen.id_cenario";
    
    $requisitionResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");
    
    while ($row_2 = mysql_fetch_row($requisitionResultSQL)) {
    
        $row_2[1] = preg_replace($search, $replace, $row_2[1]);
        
        ?>                  
        mcsrc_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=c&cc=<?= $row[0] ?>");
        <?php
    }
    
    ?>            
    mcs_<?= $row[0] ?>.makeLastSubmenu(mcsrc_<?= $row[0] ?>);
    mc.makeLastSubmenu(mcs_<?= $row[0] ?>);
    <?php
}

?>
            
    menu.makeLastSubmenu(mc);
    menu.addItem("L&eacute;xico");
            
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
    var mls_<?= $row[0] ?> = null;
    mls_<?= $row[0] ?> = new MTMenu();
    
    /* mls_<?= $row[0] ?>.addItem("L�xico", "", null, "Termos do l�xico que este termo referencia");  
     var mlsrl_<?= $row[0] ?> = null; 
     mlsrl_<?= $row[0] ?> = new MTMenu(); 
    */
   
    <?php
   
    $commandSQL = "SELECT l.id_lexico_to, lex.nome FROM lextolex l, lexico lex WHERE l.id_lexico_from = " . $row[0];
    
    $commandSQL = $commandSQL . " AND l.id_lexico_to = lex.id_lexico";
    
    $requisitionResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");
    
    while ($row_2 = mysql_fetch_row($requisitionResultSQL)) {
    
        ?>            
        mls_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=l&ll=<?= $row[0] ?>");
        <?php
    }
    
    ?>
    // mls_<?= $row[0] ?>.makeLastSubmenu(mlsrl_<?= $row[0] ?>); 
    ml.makeLastSubmenu(mls_<?= $row[0] ?>);

    <?php
}
?>
            
    menu.makeLastSubmenu(ml);         
    menu.addItem("Ontologia");
            
    var mo = null;
    mo = new MTMenu();
            
    menu.makeLastSubmenu(mo);
        
    mo.addItem("Conceitos");
    var moc = null;
    moc = new MTMenu();

<?php

$commandSQL = "SELECT id_conceito, nome  
               FROM conceito 
               WHERE id_projeto = $idProject   
               ORDER BY nome";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");

while ($row = mysql_fetch_row($requestResultSQL)) {  
    
    print "moc.addItem(\"$row[1]\", \"main.php?id=$row[0]&t=oc\");";
}
?>
     
    mo.makeLastSubmenu(moc);       
    mo.addItem("Rela&ccedil;&otilde;es");
            
    var mor = null;        
    mor = new MTMenu();
<?php

$commandSQL = "SELECT   id_relacao, nome 
               FROM     relacao r 
               WHERE    id_projeto = $idProject   
               ORDER BY nome";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");

while ($row = mysql_fetch_row($requestResultSQL)) {  
    
    print "mor.addItem(\"$row[1]\", \"main.php?id=$row[0]&t=or\");";
}
?>    
    mo.makeLastSubmenu(mor);
    mo.addItem("Axiomas");
            
    var moa = null;
    moa = new MTMenu();

<?php

$commandSQL = "SELECT   id_axioma, axioma 
               FROM     axioma 
               WHERE    id_projeto = $idProject   
               ORDER BY axioma";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao");

while ($row = mysql_fetch_row($requestResultSQL)) {  
 
    $axi = explode(" disjoint ", $row[1]);
   
    print "moa.addItem(\"$axi[0]\", \"main.php?id=$row[0]&t=oa\");";
}
?>        
    mo.makeLastSubmenu(moa);
    
        </script> 
        </head> 
        <body onload="MTMStartMenu(true)" bgcolor="#000033" text="#ffffcc" link="yellow" vlink="lime" alink="red"> 
        </body> 
    </html> 
