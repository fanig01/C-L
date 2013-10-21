<?php

require_once '../../cel/aplicacao/funcoes_genericas.php';


class funcoes_genericasTest extends PHPUnit_Framework_TestCase {
    
    public $test;
    
    protected function setUp() {
        $this->object = new funcoes_genericas();
    }
    
    protected function tearDown() {
        
    }
    
    public function testCheckUserNotAuthentication() {
        $actual = $this->object->checkUserAuthentication($url);
        $expected = NULL;
        $this->assertEquals($expected, $actual);
    }
    
    public function testCheckUserValidAuthentication()  {
        $actual = $this->object->checkUserAuthentication($url);
        $expected = exit;
        $this->assertEquals($expected, $actual);
    }
}
?>
