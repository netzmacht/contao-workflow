<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Hook;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Input;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;

/** @Hook("loadDataContainer") */
final class LoadDataContainerListener
{
    /** @var Adapter<Input> */
    private Adapter $input;

    public function __construct(private DcaManager $dcaManager, Adapter $adapter)
    {
        $this->input = $adapter;
    }

    public function __invoke(string $name): void
    {
        if ($name !== 'tl_workflow_action') {
            return;
        }

        if ($this->input->get('ptable') === 'tl_workflow') {
            return;
        }

        $this->dcaManager->getDefinition('tl_workflow_action')->set(['config', 'ptable'], 'tl_workflow_transition');
    }
}
