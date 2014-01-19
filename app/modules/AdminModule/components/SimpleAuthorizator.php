<?php
namespace SecurityModule\Component;

class SimpleAuthorizator extends \Nette\Object implements \Nette\Security\IAuthorizator {

    public function isAllowed($role, $resource, $privilege) {
        return true; // vrací TRUE nebo FALSE
    }

}