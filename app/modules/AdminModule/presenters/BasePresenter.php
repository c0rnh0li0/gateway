<?php

namespace AdminModule;

use Gateway,
    Nette\Diagnostics\Debugger;

class BasePresenter extends \BasePresenter {
    
    public function startup() {
        parent::startup();

        $this->logger = $this->getService('FileLogger');

        // performs authentication
        $this->authenticate();
   
    }
    
function json_format($json) 
{ 
    $tab = "  "; 
    $new_json = ""; 
    $indent_level = 0; 
    $in_string = false; 

    $json_obj = json_decode($json); 

    if($json_obj === false) 
        return false; 

    $json = json_encode($json_obj); 
    $len = strlen($json); 

    for($c = 0; $c < $len; $c++) 
    { 
        $char = $json[$c]; 
        switch($char) 
        { 
            case '{': 
            case '[': 
                if(!$in_string) 
                { 
                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1); 
                    $indent_level++; 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case '}': 
            case ']': 
                if(!$in_string) 
                { 
                    $indent_level--; 
                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char; 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case ',': 
                if(!$in_string) 
                { 
                    $new_json .= ",\n" . str_repeat($tab, $indent_level); 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case ':': 
                if(!$in_string) 
                { 
                    $new_json .= ": "; 
                } 
                else 
                { 
                    $new_json .= $char; 
                } 
                break; 
            case '"': 
                if($c > 0 && $json[$c-1] != '\\') 
                { 
                    $in_string = !$in_string; 
                } 
            default: 
                $new_json .= $char; 
                break;                    
        } 
    } 

    return $new_json; 
}     
    
    /**
     * Automatic invalidation when ajax.
     * 
     */
    public function beforeRender() {
        
        if ($this->isAjax()) {
            $this->invalidateControl('flashMessages');
        }
    }
    
    //////////////
    // SECURITY //
    //////////////
    protected function authenticate() {
        $user = $this->getUser();

        if (!$user->isLoggedIn()) {
            if ($user->getLogoutReason() === \Nette\Security\User::INACTIVITY) {
                $this->flashMessage('For security reasons, system has logged you out. Please, log in again.', 'warning');
            }

            $backlink = $this->getApplication()->storeRequest();
            $this->redirect(':Admin:Auth:default', array('backlink' => $backlink));
        /*} elseif (!$user->isAllowed($this->name, $this->action)) {
            $this->flashMessage('Not enough permissions to enter this page.', 'warning');
            $this->redirect('Default:');*/
        } 
    }

    public function handleLogout() {
        $this->getUser()->logout(true);

        $this->flashMessage('You have been signed out.', 'success');
        $this->redirect('Default:default');
    }    

    ////////////////
    // COMPONENTS //
    ////////////////
    /**
     * Navigation.
     *
     * @param string $name
     */
    protected function createComponentNavigation($name) {
        $nav = new \Yourface\Application\UI\Navigation($this, $name);

        // TODO read from database
        $home = $nav->setupHomepage("Overview", $this->link("Default:"), true);
        $connections = $nav->add("Connections", $this->link("Connection:"));
        $connectionEdit = $connections->add("Connection edit", $this->link("Connection:edit"));
        $connectionSetup = $connections->add("Connection base settings", $this->link("Setup:edit"));
        $connectionSetup = $connections->add("Connection handlers setup", $this->link("Setup:"));
        $connectionSetup = $connections->add("Connection attributes setup", $this->link("Setup:attributes"));
        $connectionSetup = $connections->add("Connection localization setup", $this->link("Setup:localization"));
        $connectionSetup = $connections->add("Connection property setup", $this->link("Setup:enumeration"));

        $schedule = $nav->add("Schedule", $this->link("Schedule:"));
        $scheduleDetail = $schedule->add("Schedule report", $this->link("Schedule:detail"));
        $scheduleSource = $schedule->add("Schedule source", $this->link("Schedule:source"));
        
        $nav->add("Logout", $this->link("logout!"));

    }

}
