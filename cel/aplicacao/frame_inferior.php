<?php

checkUserAuthentication("index.php");      

/* 
Scenario: access control
Objective: Given the basis of the type "c" (scenario), "l" (lexicon), "oc" (concept) "or" (link) and 
"oa" (axiom) and the id of its shows data needed in the frame. 
 */

function bottom_frame($bd, $type, $id) {
    
    $search = "'<[\/\!]*?[^<>]*?>'si";
    $replace = "";


    if ($type == "c") {            
        
        $queryScenario = "SELECT id_cenario, titulo 
                        FROM   cenario, centocen 
                        WHERE  id_cenario = id_cenario_from 
                        AND    id_cenario_to = " . $id;

        $tbScenario = mysql_query($queryScenario) or
                die("Erro ao enviar a query de selecao.");
        ?> 

        <table> 
            <tr> 
                <th>Cen&aacute;rios</th> 
            </tr> 

        <?php
        
        // Removes the HTML tags from within the title of the scenario 
        while ($row = mysql_fetch_row($tbScenario)) {
            
            $row[1] = preg_replace($search, $replace, $row[1]);
            $link = "<a href=javascript:reLoad" .
                    "('main.php?id=$row[0]&t=c');><span style=\"font-variant: small-caps\">$row[1]</span></a>";
            ?> 

                <td><?= $link ?></td> 

                <?php
            } 
        } 
        else if ($type == "l") {

            $queryScenario = "SELECT c.id_cenario, c.titulo 
                              FROM   cenario c, centolex cl 
                              WHERE  c.id_cenario = cl.id_cenario 
                              AND    cl.id_lexico = " . $id;

            $tbScenario = mysql_query($queryScenario) or
                         die("Erro ao enviar a query de selecao.");

 
            $queryLexicon = "SELECT id_lexico, nome 
                            FROM   lexico, lextolex 
                            WHERE  id_lexico  = id_lexico_from 
                            AND    id_lexico_to = " . $id;

            $tbLexicon = mysql_query($queryLexicon) or
                    die("Erro ao enviar a query de selecao.");
            ?> 

            <table> 
                <tr> 
                    <th>Cen&aacute;rios</th> 
                    <th>L&eacute;xicos</th> 
                </tr> 

            <?php
            
            //infinite loop
            while (1) {
                ?>      
                <tr>             
                    <?php
           
                if ($rowc = mysql_fetch_row($tbScenario)) {
                 
                    $rowc[1] = preg_replace($search, $replace, $rowc[1]);
                    $link = "<a href=javascript:reLoad" .
                        "('main.php?id=$rowc[0]&t=c');><span style=\"font-variant: small-caps\">$rowc[1]</span></a>";
                }  
                else {
                     $link = "";
                }                 
                ?> 
                        
                    <td><?= $link ?></td> 
                        
                        <?php
                        
                if ($rowl = mysql_fetch_row($tbLexicon)) {
                            
                    $link = "<a href=javascript:reLoad" .                                    
                            "('main.php?id=$rowl[0]&t=l');>$rowl[1]</a>";
                } 
                else {
                    $link = "";
                }  
                        ?> 

                        <td><?= $link ?></td> 
                        </td>                </tr> 

                        <?php        
                if (!( $rowc ) && !( $rowl )) {
                            
                    break;
                }
                else {
                    //Nothing should be done
                }
            } 
         } 
         else if ($type == "oc") {
                    
             $commandSQL = "SELECT   r.id_relacao, r.nome, predicado 
                            FROM     conceito c, relacao_conceito rc, relacao r 
                            WHERE    c.id_conceito = $id 
                            AND      c.id_conceito = rc.id_conceito 
                            AND      r.id_relacao = rc.id_relacao 
                            ORDER BY r.nome  ";
                    
             $result = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao !!" . mysql_error());
                 
             print "<TABLE><tr><th align=left CLASS=\"Estilo\">Rela&ccedil;&atilde;o</th><th align=left CLASS=\"Estilo\">Conceito</Th></tr>";
                    
             while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
                        
                 print "<tr><td CLASS=\"Estilo\"><a href=\"main.php?id=$line[0]&t=or\">$line[1]</a></td><td>$line[2]</TD></tr>";
             }
         }                 
         else if ($type == "or") {
                    
             $commandSQL = "SELECT DISTINCT  c.id_conceito, c.nome 
                            FROM     conceito c, relacao_conceito rc, relacao r 
                            WHERE    r.id_relacao = $id 
                            AND      c.id_conceito = rc.id_conceito 
                            AND      r.id_relacao = rc.id_relacao 
                            ORDER BY r.nome  ";
                    
             $result = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao !!" . mysql_error());
             
             print "<TABLE><tr><th align=left CLASS=\"Estilo\">Conceitos</th></tr>";
                   
             while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
                        
                 print "<tr><td CLASS=\"Estilo\"><a href=\"main.php?id=$line[0]&t=oc\">$line[1]</a></td></tr>";
             }
         }                
         else if ($type == "oa")  {
                  
             $commandSQL = "SELECT   * 
                            FROM     axioma
                            WHERE    id_axioma = \"$id\";";

             $result = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao !!" . mysql_error());

             print "<TABLE><tr><th align=left CLASS=\"Estilo\">Conceito</th><th align=left CLASS=\"Estilo\">Conceito disjunto</th></tr>";
                   
             while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
                        
                 $axiom = explode(" disjoint ", $line[1]);                    
                 $query_1 = "SELECT * FROM conceito WHERE nome = \"$axiom[0]\";";                       
                 $result_query_1 = mysql_query($query_1) or die("Erro ao enviar a query de selecao !!" . mysql_error());                        
                 $line1 = mysql_fetch_array($result_query_1, MYSQL_BOTH);
                        
                 print "<tr><td CLASS=\"Estilo\"><a href=\"main.php?id=$line1[0]&t=oc\">$axiom[0]</a></td>";
                        
                 $query_2 = "SELECT * FROM conceito WHERE nome = \"$axiom[1]\";";                       
                 $query_result_2 = mysql_query($query_2) or die("Erro ao enviar a query de selecao !!" . mysql_error());                       
                 $line2 = mysql_fetch_array($query_result_2, MYSQL_BOTH);
                        
                 print "<td CLASS=\"Estilo\"><a href=\"main.php?id=$line2[0]&t=oc\">$axiom[1]</a></td></tr>";                                    
             }                             
          }
          else {
              //Nothing should be done
          }
                
          ?> 
        </table>             
            <?php
}        
?>
