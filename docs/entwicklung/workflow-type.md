# Workflow-Typ

Ein Workflow-Typ dient in erster Linie dem Workflow-System als Unterscheidungsmerkmal, welche Aktionen zur Verfügung gestellt werden und welche Datenquellen unterstützt werden.


## Interface WorkflowType

Workflow-Typen werden über das Interface [`Netzmacht\Workflow\Flow\Workflow\Type\WorkflowType`](https://github.com/netzmacht/contao-workflow/blob/develop/src/Workflow/Type/WorkflowType.php) beschrieben:

```php
interface WorkflowType
{
    public function getName(): string;

    public function match(string $typeName): bool;

    public function configure(Workflow $workflow, callable $next): void;

    public function getProviderNames(): array;
}
```

## Workflow-Typ registrieren

Der eigene Workflow-Typ wird über den Symfony-Container definiert, indem dieser mit dem Tag `netzmacht.contao_workflow.type` getagged wird:

```yaml
services:
    Custom\Workflow\CustomType:
      tags:
        - { name: 'netzmacht.contao_workflow.type' }
     

```

## Backendkonfiguration

Der Workflowtyp triggerd die Palettenauswahl im Backend. Damit im eigenen Workflow-Typ die Standardkonfigurationen möglich bleiben, muss hier das DCA erweitert werden. Dank MetaPalettes erfolgt dies durch folgende Zeile:

```php
$GLOBALS['TL_DCA']['tl_workflow']['metapalettes']['custom extends __base__'] = [];
``` 

## AbstractWorkflowType

Für gängige Implementation wird eine abstrakte Basisklasse zur Verfügung gestellt, die verwendet werden kann:

```php
namespace Custom\Workflow;

use Netzmacht\ContaoWorkflowBundle\Workflow\Type\AbstractWorkflowType;

final class CustomType extends AbstractWorkflowType
{
    public function __construct()
    {
        parent::__construct('custom', ['tl_custom']);
    }
} 
```

## Workflow vorkonfigurieren

Ein Workflow-Typ kann außerdem dazu genutzt werden diesen vorzukonfigurieren. 

```php
namespace Custom\Workflow;

use Netzmacht\ContaoWorkflowBundle\Workflow\Type\AbstractWorkflowType;
use Netzmacht\Workflow\Flow\Workflow;

final class CustomType extends AbstractWorkflowType
{
    // ...
    
    public function configure(Workflow $workflow, callable $next): void
    {
        // Vor der DB gestützten Konfiguration
        
        parent::configure($workflow, $next);
        
        // Nach der DB gestützten Konfiguration
    }
} 
```
