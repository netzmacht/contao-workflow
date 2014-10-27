<?php

namespace Netzmacht\Contao\Workflow\Action;

use Netzmacht\Contao\Workflow\Acl\AclManager;
use Netzmacht\Contao\Workflow\Action\Notify\NotificationFactory;
use Netzmacht\Contao\Workflow\Contao\Model\RoleModel;
use Netzmacht\Contao\Workflow\Data\Data;
use Netzmacht\Contao\Workflow\Entity\Entity;
use Netzmacht\Contao\Workflow\Flow\Context;
use Netzmacht\Contao\Workflow\Flow\Transition;
use Netzmacht\Contao\Workflow\Flow\Transition\TransactionActionFailed;
use Netzmacht\Contao\Workflow\Form\FormBuilder;

/**
 * Class NotifyAction sends a notification using the notificiation center of contao
 *
 * @package Netzmacht\Contao\Workflow\Action
 */
class NotifyAction extends AbstractAction
{
    /**
     * @var int
     */
    private $notificationId;

    /**
     * @var string
     */
    private $notificationIdMapping;

    /**
     * @var string
     */
    private $language;

    /**
     * @var NotificationFactory
     */
    private $notificationFactory;

    /**
     * @var AclManager
     */
    private $aclManager;

    /**
     * @var string
     */
    private $email;

    /**
     * @var int
     */
    private $role;

    /**
     * @param NotificationFactory $notificationFactory
     * @param AclManager          $aclManager
     * @param FormBuilder         $formBuilder
     */
    public function __construct(NotificationFactory $notificationFactory, AclManager $aclManager, FormBuilder $formBuilder = null)
    {
        parent::__construct($formBuilder);

        $this->notificationFactory = $notificationFactory;
        $this->aclManager          = $aclManager;
    }

    /**
     * @param $idOrName
     * @param bool $mapToData
     * @return $this
     */
    public function setNotificationId($idOrName, $mapToData = false)
    {
        if ($mapToData) {
            $this->notificationIdMapping = $idOrName;
        } else {
            $this->notificationId = $idOrName;
        }

        return $this;
    }

    /**
     * @param Context $context
     * @return int|mixed
     */
    public function getNotificationId(Context $context)
    {
        if ($this->notificationIdMapping) {
            return $context->getProperty($this->notificationIdMapping);
        }

        return $this->notificationId;
    }

    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param Transition $transition
     * @param Entity     $entity
     * @param Context    $context
     *
     * @throws TransactionActionFailed
     *
     * @return void
     */
    public function transit(Transition $transition, Entity $entity, Context $context)
    {
        $notificationId = $this->getNotificationId($context);
        $notification   = $this->notificationFactory->create($notificationId);
        $tokens         = $this->buildTokens($transition, $entity, $context);

        try {
            $notification->send($tokens, $this->language);
        } catch (\Exception $e) {
            throw new TransactionActionFailed('Notify users failed', 0, $e);
        }
    }

    /**
     * @param Transition $transition
     * @param Entity $entity
     * @param Context $context
     *
     * @return array
     */
    private function buildTokens(Transition $transition, Entity $entity, Context $context)
    {
        $step   = $transition->getStepTo();
        $tokens = array();

        $tokens['entity']     = $entity->getPropertiesAsArray();
        $tokens['properties'] = $context->getProperties();

        $tokens['transition'] = array(
            'name'  => $transition->getName(),
            'label' => $transition->getLabel(),
        );

        $tokens['step'] = array(
            'name'  => $step->getName(),
            'label' => $step->getLabel()
        );

        $tokens['recipient_email'] = $this->email;
        $tokens['recipient_role']  = $this->buildRoleEmails();

        return $tokens;
    }

    /**
     * @return string
     */
    private function buildRoleEmails()
    {
        if (!$this->role) {
            return '';
        }

        $roleModel = RoleModel::findByPk($this->role);

        if (!$roleModel) {
            return '';
        }

        $memberGroups = deserialize($roleModel->memberGroups, true);
        $userGroups   = deserialize($roleModel->userGroups, true);
    }
}
