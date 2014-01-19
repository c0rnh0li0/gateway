<?php

namespace AdminModule;

use Nette\Diagnostics\Debugger,
    Yourface\Application\UI\Form,
    Nette\Utils\Html;

/**
 * Connections administration.
 * 
 * @author Lukas Bruha
 */
class ConnectionPresenter extends BasePresenter {

    /**
     * List of connections loaded from DB.
     * 
     * @var \Nette\Database\Statement\Selection
     */
    protected $connections;

    /**
     * Loads available connections.
     * 
     */
    public function startup() {
        parent::startup();

        $this->connections = $this->getService('database')->table('gw_connection');
    }

    /**
     * Edit connection.
     * 
     * @param int $id
     */
    public function actionEdit($id) {
        $form = $this->getComponent('connectionForm');
        $form->addSubmit('cancel', 'Cancel')->setValidationScope(NULL);

        if (!$form->isSubmitted()) {
            $row = $this->connections->get($id);

            if (!$row) {
                $this->flashMessage('No such record.', 'error');
                $this->redirect('default');
            }

            $form->setDefaults($row);
        }
    }
    
    /**
     * Clones the whole connection incl. all its settings.
     * 
     * @param int $id
     */
    public function actionClone($id) {
        $oldConn = $this->connections->get($id);

        if (!$oldConn) {
            $this->flashMessage('No such record.', 'error');
            $this->redirect('default');
        }
        
        // CONNECTION
        $db = $this->getService('database');
        $db->beginTransaction();
        
        try {
            $oldConn = $oldConn->toArray();
            $oldConn['name'] = $oldConn['name'] . "[CLONE]";
            $oldConn['created_at'] = new \Nette\Database\SqlLiteral('NOW()');
            
            unset($oldConn['id']); // remove from insert

            $newConn = $db->table('gw_connection')->insert($oldConn);

            // HANDLERS
            $newAdapters = array();

            foreach ($db->table('gw_adapter')->where(array('gw_connection_id' => $id)) as $oldAdapter) {            
                $newAdapter = $oldAdapter->toArray();
                $newAdapter['gw_connection_id'] = (int) $newConn->id;
                
                unset($newAdapter['id']); // remove from insert

                $newAdapters[] = $newAdapter;
            }

            // adapters multi insert
            if (count($newAdapters)) {
                $db->table('gw_adapter')->insert($newAdapters);
            }

            // MAPPING RULES
            $newRules = array();
            
            foreach ($db->table('gw_mapping_rule')->where(array('gw_connection_id' => $id)) as $oldRule) {            
                $newRule = $oldRule->toArray();
                $newRule['gw_connection_id'] = (int) $newConn->id;
                
                unset($newRule['id']); // remove from insert

                $newRules[] = $newRule;
            }

            // mapping rules multi insert
            if (count($newRules)) {
                $db->table('gw_mapping_rule')->insert($newRules);
            }
            
            $db->commit();
            
            $this->flashMessage(sprintf("Connection has been successfully cloned as '%s'.", $newConn['name']), 'success');
        } catch(\PDOException $e) {
            $db->rollback();
            
            $this->flashMessage("Error raised during connection clone process.", 'error');
        }
        
        $this->redirect('default');
    }

    /**
     * Connections overview datagrid.
     * 
     * @return \AdminModule\Component\ConnectionsGrid
     */
    public function createComponentConnectionsGrid() {
        return new \AdminModule\Component\ConnectionsGrid($this->connections->order('name ASC'));
    }

    /**
     * Connection form.
     * 
     * @return \Yourface\Application\UI\Form
     */
    public function createComponentConnectionForm() {

        $form = new Form;
        $form->addHidden('id');

        $form->addText('name', 'Name')
                ->setRequired('Please enter name.')
                ->setOption('description', 'Min. three signs, use A..Z, a..z, 0-9 only.')
                ->addRule(function ($control) {
                            return \Yourface\Utils\Validators::hasLettersAndNumbersOnly($control->getValue() );
                    }, 'Name can include letters and numbers only.')
                ->addRule($form::MIN_LENGTH, 'Minimum name length is %d signs.', 3);

        $form->addText('shop_url', 'Shop URL')
                ->setRequired('Please enter shop URL.')
                ->addRule(Form::URL, 'Shop URL must be valid absolute URL')
                ->addRule(function ($control) {
                            return \Nette\Utils\Strings::startsWith($control->getValue(), 'http://');
                    }, 'Shop URL must start with http://');
        
        $form->addText('description', 'Description')
                ->setRequired('Please enter description.');
        
        $form->addRadioList('is_enabled', 'Is enabled?', array(0 => "no", 1 => "yes"))
                ->setRequired('Please, set enabled.')
                ->getSeparatorPrototype()->setName(null);

        //$form->addSelect('handler_products', 'Handler products', $productHandlers);

        $form->addSubmit('save', 'Save')->setAttribute('class', 'default');
        $form->addSubmit('continue', 'Save and setup')->setAttribute('class', 'default');
        $form->onSuccess[] = callback($this, 'handleConnectionFormSubmitted');

        $form->addProtection('Please submit this form again (security token has expired).');

        return $form;
    }

    /**
     * Connection form submit.
     * 
     * @param \Yourface\Application\UI\Form $form
     */
    public function handleConnectionFormSubmitted(Form $form) {
        if (isset($form['cancel']) && $form['cancel']->isSubmittedBy()) {
            $this->redirect('default');
        }

        $values = $form->getValues();
        $data = array(
            'name' => $values['name'],
            'shop_url' => $values['shop_url'],
            'description' => $values['description'],
            'created_at' => new \Nette\Database\SqlLiteral('NOW()'),
            'is_enabled' => (int) $values['is_enabled'],
        );

        try {
            if ($values['id']) {
                $this->connections->find($values['id'])->update($data);
                $this->flashMessage(sprintf("Connection '%s' has been updated.", $data['name']), 'success');
            } else {
                $conn = $this->connections->insert($data);
                $this->flashMessage("New connection has been stored.", 'success');

                if ($form['continue']->isSubmittedBy()) {
                    $this->redirect('Setup:', array('id' => $conn->id));
                }
            }
        } catch (\PDOException $e) {
            $this->flashMessage("There was an error during connection saving: " . $e->getMessage(), 'error');
        }

        $this->redirect('default');
    }

}
