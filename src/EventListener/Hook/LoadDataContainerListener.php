<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Hook;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Input;

/** @Hook("loadDataContainer") */
final class LoadDataContainerListener
{
    /** @var Adapter<Input> */
    private $input;

    public function __construct(Adapter $adapter)
    {
        $this->input = $adapter;
    }

    public function __invoke(string $name): void
    {
        if ($name !== 'tl_workflow_action') {
            return;
        }

        if ($this->input->get('ptable') !== 'tl_workflow') {
            $GLOBALS['TL_DCA']['tl_workflow_action']['config']['ptable'] = 'tl_workflow_transition';
        }
    }
}
