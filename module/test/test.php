<?php

use Netzmacht\Contao\Workflow\Factory;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Form\Form;
use Netzmacht\Contao\Workflow\Item;

define('TL_MODE', 'BE');
error_reporting('E_ALL');
ini_set('display_errors', '1');

require_once '/var/www/html/dev/workflow/system/initialize.php';

/** @var Factory $factory */
$factory = $GLOBALS['container']['workflow.factory'];

$manager  = $factory->createManager('default');
$workflow = $manager->getWorkflowByName('test');
$entity   = $factory->createEntity(\ContentModel::findByPk(1));
$item     = $manager->createItem($entity);
$handler  = $manager->handle($item, 'publish');
$stepName = $handler->getItem()->getCurrentStepName();
$step     = $workflow->getStep($stepName);

$GLOBALS['container']['event-dispatcher']->addListener(
    \Netzmacht\Contao\Workflow\Event\Action\ExecuteTransitionEvent::NAME,
    function ($event) {
        var_dump($event->getTransition()->getRoles());
    }
);

class MyAction extends \Netzmacht\Contao\Workflow\Action\AbstractAction
{
    public function requiresInputData()
    {
        return true;
    }

    public function buildForm(Form $form)
    {
        $form->addField(
            'test',
            'text',
            'arschloch',
            array('mandatory' => true)
        );
//        $form->addField('test', array(
//                'inputType' => 'text',
//                'default'   => 'aschl',
//                'eval'      => array(
//                    'mandatory' => true,
//                )
//            ),
//            'test'
//        );
    }

    /**
     * Transit will execute the action.
     *
     * @param Transition $transition Current transition.
     * @param Item            $item       The passed item.
     * @param Context    $context    Transition context.
     *
     * @return void
     */
    public function transit(Transition $transition, Item $item, Context $context)
    {
        $context->setProperty('test', 'test_value');
        echo 'Transition is executed';
    }
}

\Input::setPost('FORM_SUBMIT', 'workflow_transition');

$action = new MyAction();
$workflow->getTransition('publish')->addAction($action);

if ($handler->validate()) {
    echo 'validated';

    $state = $handler->transit();

    var_dump($state);
}
else {
    echo 'not validated';

    if ($handler->requiresInputData()) {
        $form = $handler->getForm();
        echo $form->render();
    }

    var_dump($handler->getContext()->getErrorCollection()->getErrors());
}




//if ($handler->validate()) {
//    if ($handler->isWorkflowStarted()) {
//        $state = $handler->transit('publish');
//    }
//    else {
//        $handler->start();
//    }
//}
//else {
//    if ($handler->requiresInputData()) {
//        $view = new View();
//        echo $handler->getForm()->render($view);
//    }
//    else {
//        echo 'Something went wrong';
//    }
//}
