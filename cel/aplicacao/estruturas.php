<?php

$_SESSION["estruturas"] = 1;

class concept {

    var $name = NULL;
    var $description = NULL;
    var $relations = NULL;
    var $subconcepts = NULL;
    var $namespace = NULL;

    function concept($name, $description) {
        $this->name = $name;
        $this->description = $description;
        $this->relations = array();
        $this->subconcepts = array();
        $this->namespace = "";
    }

}

class relationshipBetweenConcepts {

    var $predicates = NULL;
    var $verb = NULL;

    function relationshipBetweenConcepts($predicates, $verb) {
        $this->predicates[] = $predicates;
        $this->verb = $verb;
    }
}

class lexiconTerm {

    var $name = NULL;
    var $notion = NULL;
    var $impact = NULL;

    function lexiconTerm($name, $notion, $impact) {
        $this->name = $name;
        $this->notion = $notion;
        $this->impact = $impact;
    }
}

?>