<?php

include_once("bd.inc");

$link = bd_connect();
$query = "show tables";
$resultArray = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

print "<font color=#7c75b2 face=arial><h3>TABELAS e seus ATRIBUTOS<h3></font>";

while ($line = mysql_fetch_array($resultArray, MYSQL_BOTH)) {
    
    print "<table border=1><tr><td bgcolor=#7c75b2 width=120><font color=white>" . $line[0] . "</font></td>";

    $table = "describe " . $line[0];
    $attributes = mysql_query($table) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
    
    while ($linha = mysql_fetch_array($attributes, MYSQL_BOTH)) {
        print "<td>" . $linha[0] . " </td>";
    }
    
    print "</tr></table><br>";
}

$projects = "SELECT * FROM projeto WHERE nome='Adm_Imoveis' order by id_projeto";
$result = mysql_query($projects) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>Projeto que est&aacute; sendo traduzido pelo Jer&ocirc;nimo<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td> 
                  <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>data da cria��o</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>descri&ccedil;&atilde;o</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>id_status</font></td>
           </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
    
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>" . $line[4] . "</td><td>" . $line[5] . "</td><td>";
}
print "</table>";


$results = "select * from pedidorel order by nome";
$result = mysql_query($results) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>PedidoRel<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_pedido</font></td> 
                  <td bgcolor=#7c75b2 width=120><font color=white>id_usuario</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>tipo_pedido</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>aprovado</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>id_relacao</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>justificativa</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>id_status</font></td>
  
           </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
    
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>" . $line[4] . "</td><td>" . $line[5] . "</td><td>" . $line[6] . "</td><td>" . $line[7] . "</td><td>" . $line[8] . "</td><td>";
}

print "</table>";


$results = "select * from lexico order by nome";
$result = mysql_query($results) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>Lexico<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_lexico</font></td> 
                  <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>data</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>tipo</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>nocao</font></td>
  				  <td bgcolor=#7c75b2 width=120><font color=white>impacto</font></td>
  
           </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
    
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>" . $line[4] . "</td><td>" . $line[5] . "</td><td>" . $line[6] . "</td><td>" . $line[7] . "</td><td>";
}

print "</table>";


$results = "select * from algoritmo";
$result = mysql_query($results) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>Algoritmo<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_variavel</font></td> 
                  <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>valor</font></td>                  
           </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
    
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>";
}

print "</table>";


$concepts = "select * from conceito order by nome asc";
$result = mysql_query($concepts) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>Conceitos<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_conceito</font></td> 
                  <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>descricao</font></td>
                  <td bgcolor=#7c75b2 width=120><font color=white>pai</font></td>
           </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
    
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>" . $line[4] . "</td><td>" . $line[5] . "</td><td>";
}

print "</table>";


$relations = "select * from relacao order by id_relacao";
$result = mysql_query($relations) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>Rela��es<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_relacao</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
         </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
    
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>";
}

print "</table>";


$hierarchy = "select * from hierarquia";
$result = mysql_query($hierarchy) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>Hierarquia<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_hierarquia</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>id_conceito</font></td>
			 <td bgcolor=#7c75b2 width=120><font color=white>id_subconceito</font></td>
         </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>";
}

print "</table>";


$relationBetweenConcepts = "select c.nome, r.nome, rc.predicado, rc.id_projeto from relacao_conceito rc, relacao r, conceito c WHERE c.id_conceito = rc.id_conceito AND rc.id_relacao = r.id_relacao ORDER BY c.nome, r.nome ASC;";
$result = mysql_query($relationBetweenConcepts) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>Rela&ccedil;&atilde;o entre conceitos<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>conceito</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>relacao</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>predicado</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
         </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>";
}

print "</table>";


$axioms = "select * from axioma order by id_axioma";
$result = mysql_query($axioms) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>Axiomas<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_axioma</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>axioma</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
         </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
    
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>";
}

print "</table>";

$users = "select * from usuario order by id_usuario";
$result = mysql_query($users) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>Usu�rios<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_usuario</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>nome</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>email</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>login</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>senha</font></td>
         </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>" . $line[3] . "</td><td>" . $line[4] . "</td><td>";
}

print "</table>";

$participants = "select * from participa order by id_projeto";
$result = mysql_query($participants) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

print "<br><br><font color=#7c75b2 face=arial><h3>Participa<h3></font>";
print "<table border=1>";

print "<tr><td bgcolor=#7c75b2 width=120><font color=white>id_usuario</font></td> 
             <td bgcolor=#7c75b2 width=120><font color=white>id_projeto</font></td>
             <td bgcolor=#7c75b2 width=120><font color=white>gerente</font></td>
         </tr>";

while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
   
    print "<tr><td>" . $line[0] . "</td><td>" . $line[1] . "</td><td>" . $line[2] . "</td><td>";
}

print "</table>";

mysql_close($link);
?>