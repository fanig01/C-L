<HTML> 
    <HEAD> 
        <LINK rel="stylesheet" type="text/css" href="style.css"> 

        <TITLE>Recupera&ccedil;&atilde;o de Arquivos DAML</TITLE> 

    </HEAD> 

    <BODY> 
       
        <H2>Hist&oacute;rico de Arquivos DAML</H2> 
        <?PHP

        
 include_once( "CELConfig/CELConfig.inc" );        
       
 function extractDate($fileName) {
            
            
     list($projeto, $resto) = split("__", $fileName);           
     list($day, $month, $year, $hour, $minute, $second, $extension) = split('[_-.]', $resto);
          
     if (!is_numeric($day) || !is_numeric($month) || !is_numeric($year) || !is_numeric($hour) || !is_numeric($minute) || !is_numeric($second)){                       
                
         return "-";  
     }
          
     $monthSpelled = "-";
            
     switch ($month) {
                              
         case 1: $monthSpelled = "janeiro";                   
             break;
                               
         case 2: $monthSpelled = "fevereiro";                    
             break;
                
         case 3: $monthSpelled = "março";                  
             break;
                
         case 4: $monthSpelled = "abril";                    
             break;
                
         case 5: $monthSpelled = "maio";                   
             break;
                              
         case 6: $monthSpelled = "junho";                   
             break;
                               
         case 7: $monthSpelled = "julho";                    
             break;
                                
         case 8: $monthSpelled = "agosto";                   
             break;
                                
         case 9: $monthSpelled = "setembro";                    
             break;
                                
         case 10: $monthSpelled = "outubro";                   
             break;
                               
         case 11: $monthSpelled = "novembro";                  
             break;
                              
         case 12: $monthSpelled = "dezembro";                   
             break;        
     }

            
     return $day . " de " . $monthSpelled . " de " . $year . " às " . $hour . ":" . $minute . "." . $second . "\n";
        
     
}
       
function extractProject($nome_arquivo) {
                       
    list($projeto) = split("__", $nome_arquivo);
            
    return $projeto;
}

        
$directory = $_SESSION['diretorio'];
$site = $_SESSION['site'];

        
if ($directory == "") {
                       
    $directory = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
}
else {
    //Nothing should be done
}
        
if ($site == "") {
                              
    $site = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
            
            
    if ($site == "http:///") {
                
        print( "Aten&ccedil;&atilde;o: O arquivo de configura&ccedil;&atilde;o do CELConfig (padr&atilde;o: config2.conf) precisa ser configurado corratamente.<BR>\n ");
        print("* N&atilde;o foram preenchidas as vari&aacute;veis 'HTTPD_ip','CEL_dir_relativo' e 'DAML_dir_relativo_ao_CEL'.<BR>\n");
        print("Por favor, verifique o arquivo e tente novamente.<BR>\n");
    }
    else {
        //Nothing should be done
    }
}
else {
    //Nothing should be done
}
       
// Mounts the file table DAML 
                
print( "<CENTER><TABLE WIDTH=\"80%\">\n");
print( "<TR>\n\t<Th><STRONG>Projeto</STRONG></Th>\n\t<Th><STRONG>Gerado em</STRONG></Th>\n</TR>\n");
               
if ($dir_handle = @opendir($directory)) {
            
            
    while (( $archive = readdir($dir_handle) ) !== false) {
                                
        if (is_file($directory . "/" . $archive) && $archive != "." && $archive != "..") {
                                        
            print( "<TR>\n");
            print( "\t<TD WIDTH=\"25%\" CLASS=\"Estilo\"><B>" . extractProject($archive) . "</B></TD>\n");
            print( "\t<TD WIDTH=\"55%\" CLASS=\"Estilo\">" . extractDate($archive) . "</TD>\n");
            print( "\t<TD WIDTH=\"10%\" >[<A HREF=\"" . $site . $archive . "\">Abrir</A>]</TD>\n");
            print( "</TR>\n");
         }
         else {
             //Nothing should be done
         }
     }
            
     closedir($dir_handle);
}
else {
    //Nothing should be done
}
        
        
print("</TABLE></CENTER>\n");
        
?> 
       
    </BODY> 
</HTML> 