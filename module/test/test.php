<?php

use Netzmacht\Workflow\Factory;

define('TL_MODE', 'BE');
error_reporting('E_ALL');
ini_set('display_errors', '1');

require_once '/var/www/html/dev/workflow/system/initialize.php';

/** @var Factory $factory */
$factory = $GLOBALS['container']['workflow.factory'];

//$manager  = $factory->createManager('default');
//$workflow = $manager->getWorkflowByName('test');
//$entity   = $factory->createEntity(\ContentModel::findByPk(1));
//$item     = $manager->createItem($entity);
//$handler  = $manager->handle($item, 'publish');
//$stepName = $handler->getItem()->getCurrentStepName();
//$step     = $workflow->getStep($stepName);

\BackendUser::getInstance()->authenticate();
$user    = $factory->createUser();
$manager = new \Netzmacht\Workflow\Contao\Manager($user, $factory, $GLOBALS['container']['workflow.factory.entity']);

$entity = $manager->createEntity(array('id' => 5, 'name' => 'David'), 'tl_content');
var_dump($entity);

$entity->setProviderName('tl_content');
$entity->setID(4);
$entity->setPropertyRaw('name', 'David');

$handler = $manager->handle($entity);

if ($handler) {
    var_dump($handler->getWorkflow());
}


//$GLOBALS['container']['event-dispatcher']->addListener(
//    \Netzmacht\Contao\Workflow\Event\Action\ExecuteTransitionEvent::NAME,
//    function ($event) {
//        var_dump($event->getTransition()->getRoles());
//    }
//);
//
//class MyAction extends \Netzmacht\Contao\Workflow\Action\AbstractAction
//{
//    public function requiresInputData()
//    {
//        return true;
//    }
//
//    public function buildForm(Form $form)
//    {
//        $form->setFieldsetDetails('default', 'Standard', 'Here we go', 'tl_special');
//
//        $test = $form->createField('test', 'text');
//        $test
//            ->setLabel('Test')
//            ->setDescription('Test it baby.')
//            ->setDefaultValue('arschloch')
//            ->setExtra(array('mandatory' => true))
//            ->addToForm($form);
//
//        $email = $form->createField('email', 'text');
//        $email->setExtra(array('rgxp' => 'email'));
//        $email->addToForm($form);
//
////        $form->addField('email', array('inputType' => 'text'));
////        $form->addField('test', array(
////                'inputType' => 'text',
////                'default'   => 'aschl',
////                'eval'      => array(
////                    'mandatory' => true,
////                )
////            ),
////            'test'
////        );
//    }
//
//    /**
//     * Transit will execute the action.
//     *
//     * @param \Netzmacht\Workflow\Flow\Transition $transition Current transition.
//     * @param Item            $item       The passed item.
//     * @param \Netzmacht\Workflow\Flow\Context    $context    Transition context.
//     *
//     * @return void
//     */
//    public function transit(Transition $transition, Item $item, Context $context)
//    {
//        $context->setProperty('test', 'test_value');
//        echo 'Transition is executed';
//    }
//}
//
//\Input::setPost('FORM_SUBMIT', 'workflow_transition');
//
//$action = new MyAction();
//$workflow->getTransition('publish')->addAction($action);
//
//if ($handler->validate($factory->createForm('backend'))) {
//    echo 'validated';
//
//    $state = $handler->transit();
//
//    var_dump($state);
//}
//else {
//    echo 'not validated';
//
//    if ($handler->requiresInputData()) {
//        echo $handler->getForm()->render();
//    }
//
//    var_dump($handler->getContext()->getErrorCollection()->getErrors());
//}
//
//
//
//
////if ($handler->validate()) {
////    if ($handler->isWorkflowStarted()) {
////        $state = $handler->transit('publish');
////    }
////    else {
////        $handler->start();
////    }
////}
////else {
////    if ($handler->requiresInputData()) {
////        $view = new View();
////        echo $handler->getForm()->render($view);
////    }
////    else {
////        echo 'Something went wrong';
////    }
////}
