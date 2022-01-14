<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Workflow;

use Doctrine\DBAL\Connection;
use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Event\CreateTransitionEvent;
use Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ConditionalTransition\ConditionalTransitionAction;

use function array_map;
use function sprintf;

/**
 * Class creates the conditional transition action for transition of type conditional
 */
class CreateConditionalTransitionsListener
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection Database connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Handle the event.
     *
     * @param CreateTransitionEvent $event The event.
     */
    public function onCreateTransition(CreateTransitionEvent $event): void
    {
        $transition = $event->getTransition();
        if ($transition->getConfigValue('type') !== 'conditional') {
            return;
        }

        $workflow    = $event->getTransition()->getWorkflow();
        $transitions = $this->getConditionalTransitionNames((int) $transition->getConfigValue('id'));

        $action = new ConditionalTransitionAction(
            sprintf('%s_%s_conditional_action', $workflow->getName(), $transition->getName()),
            $workflow,
            $transitions
        );

        $transition->addPostAction($action);
    }

    /**
     * Get conditional transition actions by transition id.
     *
     * @param int $transitionId The id of the parent transition.
     *
     * @return string[]
     */
    private function getConditionalTransitionNames(int $transitionId): array
    {
        $sql       = 'SELECT tid FROM tl_workflow_transition_conditional_transition WHERE pid=:pid ORDER BY sorting';
        $statement = $this->connection->prepare($sql);
        $result    = $statement->executeQuery(['pid' => $transitionId]);

        return array_map(
            // @codingStandardsIgnoreStart
            static function ($transitionId) : string {
                return 'transition_' . $transitionId;
            },
            // @codingStandardsIgnoreEnd
            $result->fetchFirstColumn()
        );
    }
}
