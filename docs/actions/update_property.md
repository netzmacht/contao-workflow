# Action Update Property

Mithilfe der Action kann die Eigenschaft einer Entität überschrieben werden.

Optional kann als neuer Wert eine Expression auf Basis der 
[Symfony Expression Language](https://symfony.com/doc/current/components/expression_language/syntax.html)
angegeben werden. Derzeit stehen folgende Daten zur Verfügung:

 * `now`: Instanz von `\DateTimeImmutable`
 * `entity`: Instanz von `\Netzmacht\ContaoWorkflowBundle\PropertyAccess\ReadonlyPropertyAccessor`
 
Letzeres erlaubt den Zugriff auf die Werte einer Entität über folgende Syntax:

```
## Get raw value
entity.raw('author')

## Get deserialized value
entity.get('headline')
```

## Contao Models

Für Contao Models besteht der Zugriff auch auf abhängige Models, die in einer 1:1 oder n:1 Beziehung stehen. Der Zugriff
gilt sowohl für den Schreib- als auch Lesezugriff.

Die Werte der abhängigen Entität sind über den Pfad der Eigenschaft zugänglich, z.b. `author.name`.

```
## Get raw value
entity.raw('author.name')

## Get deserialized value
5 in entity.get('author.groups')
```
