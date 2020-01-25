# Aktionen

Aktionen werden während der Transition von einem Workflow-Schritt zum nächsten ausgeführt. Aktionen sind dazu gedacht die Geschäftslogik des Prozesses auszulösen, oder je nach Datenmodel zu implementieren.

## Interface Action

Actions werden über das Interface `\Netzmacht\Workflow\Flow\Action` beschrieben:

```php
interface Action
{
    public function getRequiredPayloadProperties(Item $item): array;

    public function validate(Item $item, Context $context): bool;

    public function transit(Transition $transition, Item $item, Context $context): void;
}
``` 

Eine Action besitzt die Möglichkeit einen bestimmten Payload, sprich von Benutzer zur Verfügung gestellte Daten, vorauszusetzen. Dazu dient die Method `getRequiredPayloadProperties`, die die erforderlichen Attribute als Liste zurückgibt. Bevor eine Transition ausgeführt wird, erfolgt die Validierung der bereitgestellten User-Inputs. Dazu implementiert eine Action die Methode `validate`. Der Payload steht als `Context#getPayload` zur Verfügung.

Die eigentliche Action wird in der Methode `transit` ausgeführt.


## Action registrieren

### Manuell registrieren

Actions lassen sich manuell zu einer Transition hinzufügen. Dies kann in der `configure` Methode des implementierten Workflow-Typs erfolgen. Sinnvoll ist dieses Vorgehen für Aktionen, die in der Transition des Workflows erfolgen soll.

```php
    public function configure(Workflow $workflow, callable $next): void
    {
        parent::configure($workflow, $next);
        
        $action = new MyAction();
        $postAction = new MyPostAction();
        foreach ($workflow->getTransitions() as $transition) {
            $transition->addAction($action);
            $transition->addPostAction($postAction));
        }
    }

```

### Automatisch über Backend-Konfiguration

Darüber hinaus lassen sich Actions im Backend der Workflow-Konfiguration konfigurieren.

#### Interface ActionTypeFactory

Damit die nahtlose Integration in die Workflow-Erweiterung funktioniert, muss zusatzlich eine *Factory*-Klasse vom Typ `Netzmacht\ContaoWorkflowBundle\Workflow\Flow\Action\ActionTypeFactory` implementiert werden:


```php
interface ActionTypeFactory
{
    public function getCategory(): string;

    public function getName(): string;

    public function isPostAction(): bool;

    public function supports(Workflow $workflow): bool;

    public function create(array $config, Transition $transition): Action;
}
```

Die ActionTypeFactory wird nun im Symfony-Container registriert:

```yaml
services:
    Custom\Workflow\Action\CustomActionFactory:
      tags:
        - { name: 'netzmacht.contao_workflow.action' }
     

```

> **Hinweis**
> Benötigt die Action zusätzliche Konfiguration, kann eine DCA-Palette mit dem Namen der Action angelegt werden.
