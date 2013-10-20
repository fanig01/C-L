<html>   
    <head>
        
        <title>Esqueci minha senha</title>
        
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <script language="JavaScript">
    <!--
        function testWhite(form)
        {
            login = form.login.value;

            if ((login == "")){
                alert("Por favor, digite o seu Login.");
                form.login.focus();
                return false;
            }
            else {
                //Nothing should be done
            }
        }

    </SCRIPT>
    
    <p style="color: red; font-weight: bold; text-align: center">
        <img src="Images/Logo_CEL.jpg" width="180" height="100"><br/><br/>
    </p>

    <body bgcolor="#FFFFFF">
        <form action="enviar_senha.php" method="post">
            <div align="center">

                <?php
                
/*
 Scenario: Remember password
 Objective: Allow registered user, that forgot his password, receive his
            password by email
 Context: System is open, user forgot his password on screen of reminder the
          password.
 Precondition: user have acessed to the system
 Actors: user, system
 Resources: database
 Episode: the user acess the screen of login of the system. 
            the user click the link FORGOT PASSWORD
            the system show a message on screen, asking to the user that
 type his login in text box.
            The user type his login and click send button.
*/
                
                ?>

                <p style="color: green; font-weight: bold; text-align: center">Entre com seu Login:</p>

                <table cellpadding="5">
                    <tr><td>Login:</td><td><input maxlength="12" name="login" size="24" type="text"></td></tr>

                    <tr><td height="10"></td></tr>
                    <tr><td align="center" colspan="2"><input name="submit"  onClick="return testWhite(this.form);" type="submit" value="Enviar"></td></tr>
                </table>
            </div>
            <br>
            <br>
            <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
        </form<i><a href="showSource.php?file=esqueciSenha.php">Veja o c&oacute;digo fonte!</a></i>
    </body>
</html>
