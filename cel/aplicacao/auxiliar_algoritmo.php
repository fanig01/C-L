<?php

function existe_relacao($rel, $list) {
    foreach ($list as $key => $relation) {
        if (@$relation->verbo == $rel) {
            return $key;
        }
        else {
            //Nothing should be done
        }
    }
    return -1;
}

function existe_conceito($conc, $list) {
    foreach ($list as $key => $conc1) {
        if ($conc1->nome == $conc) {
            return $key;
        }
        else {
            //Nothing should be done
        }
    }
    return -1;
}

?>