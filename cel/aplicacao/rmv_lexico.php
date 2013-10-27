<?php
/*
This script makes a request to remove a lexicon project. Removes the current lexicon.


Scenario - Delete Lexicon
Objective: Allow User to Delete a word from the lexicon that is active.
Context: User wants to delete a word from the lexicon.
Pro-Condition: Login and word lexicon registered in the system.
Actors: User, System.
Resources: Data informed
Episodes: The system provide a screen to the user the need to justify that 
exclusion so that the administrator can read and approve or not.
This screen also contains a button to confirm the deletion.
Restriction: After clicking the button the system verifies that all fields have been filled.
Exception: If all fields are empty, returns to the user a message that all fields must be completed
and a button to return to the previous page.
 */

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
checkUserAuthentication("index.php");

inserirPedidoRemoverLexico($id_project, $id_lexicon, $_SESSION['id_usuario_corrente']);
?>  

<script language="javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>

<script language="javascript1.3">

    self.close();

</script>
