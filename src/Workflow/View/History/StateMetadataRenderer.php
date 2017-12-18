<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\Workflow\View\History;


use Contao\StringUtil;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\Entity;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;

class StateMetadataRenderer implements HistoryRenderer
{
    /**
     * Entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;

    public function supports(Item $item, Workflow $workflow): bool
    {
        return true;
    }

    public function render(Item $item, Workflow $workflow, array $data): array
    {
        $stateColumns = StringUtil::deserialize($workflow->getConfigValue('stepHistoryColumns'), true);

        foreach ($item->getStateHistory() as $index => $state) {
            if (!isset($state->getData()['metadata'])) {
                continue;
            }

            $metadata = $state->getData()['metadata'];

            foreach ($stateColumns as $column) {
                switch ($column) {
                    case 'user':
                        $value = $this->renderUserName($metadata);
                        break;

                    default:
                        continue;
                }

                $data[$index][$column] = $value;
            }
        }

        return $data;
    }

    /**
     * Render the username.
     *
     * @param array $metadata State metadata.
     *
     * @return string
     */
    private function renderUserName(array $metadata): string
    {
        $userId     = EntityId::fromString($metadata['userId']);
        $repository = $this->entityManager->getRepository($userId->getProviderName());
        $user       = $repository->find($userId->getIdentifier());

        if ($user instanceof Entity) {
            $userName = '';

            if ($user->getProviderName() === 'tl_user') {
                $userName = $user->getProperty('name');
            } elseif ($user->getProviderName() === 'tl_member') {
                $userName = $user->getProperty('firstname') . ' ' . $user->getProperty('lastname');
            }

            $userName .= sprintf(
                ' <span class="tl_gray">[%s]</span>',
                $user->getProperty('username') ?: $user->getId()
            );

            return $userName;
        }

        return $metadata['userId'];
    }
}
