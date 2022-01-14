<?php

declare(strict_types=1);

use Contao\System;
use Netzmacht\ContaoWorkflowBundle\Migration\TransitionActionsMigration;

/**
 * @psalm-suppress PossiblyNullFunctionCall
 * @psalm-suppress InvalidFunctionCall
 */
(System::getContainer()->get(TransitionActionsMigration::class))();
