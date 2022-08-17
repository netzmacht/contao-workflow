<?php

declare(strict_types=1);

namespace Netzmacht\ContaoWorkflowBundle\EventListener\Dca;

use function sprintf;

/**
 * Class Common provides common helper functionalities.
 */
final class CommonListener
{
    /**
     * Generate a row view.
     *
     * @param array<string,mixed> $row Current data row.
     */
    public function generateRow(array $row): string
    {
        return sprintf(
            '<strong>%s</strong><br>%s',
            $row['label'],
            $row['description']
        );
    }
}
