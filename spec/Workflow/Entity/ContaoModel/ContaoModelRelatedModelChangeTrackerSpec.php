<?php

declare(strict_types=1);

namespace spec\Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel;

use Contao\Database\Result;
use Contao\Model;
use Netzmacht\ContaoWorkflowBundle\Workflow\Entity\ContaoModel\ContaoModelRelatedModelChangeTracker;
use PhpSpec\ObjectBehavior;

final class ContaoModelRelatedModelChangeTrackerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ContaoModelRelatedModelChangeTracker::class);
    }

    public function it_tracks_changes(): void
    {
        $baseModel    = $this->modelInstance();
        $changedModel = $this->modelInstance();

        $this->track($baseModel, $changedModel);

        $this->release($baseModel)->shouldReturn([$changedModel]);
    }

    public function it_releases_changes(): void
    {
        $baseModel    = $this->modelInstance();
        $changedModel = $this->modelInstance();

        $this->track($baseModel, $changedModel);

        $this->release($baseModel)->shouldReturn([$changedModel]);
        $this->release($baseModel)->shouldReturn([]);
    }

    /** @param array<string,mixed> $data */
    private function modelInstance(array $data = []): Model
    {
        $instance = new class extends Model {
            /** @var string */
            protected static $strTable = 'tl_example';

            public function __construct(?Result $objResult = null)
            {
                // Do not call parent constructor as it requires contao framework being initialized
            }
        };

        $instance->setRow($data);

        return $instance;
    }
}
