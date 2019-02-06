# Konzepte

## Workflow

 * umfasst verschiedene Zustände, in denen sich eine Entität während seiner Existenz befinden kann
 * Workflows werden für einen bespzifschen Typ einer Entität definiert
 * Pro Entität können mehrere Workflows definiert werden
 * Workflows besitzen einen Typ, der beeinflusst 
 * Über Bedingungen wird geregelt, welcher Workflow für eine Entität zuständig ist
 
## Workflowtyp

 * Ermöglicht die Erweiterbarkeit der Workflow-Erweiterung an spezifische Anforderungen
 
## Schritt

 * Ein Workflow hat eine beliebige Anzahl von Schritten, die eine Entität während ihrer Existenz erreichen kann
 * Ein Schritt hat verschiedene Transitions, die zu einem neuen Schritt führen
 * Ein Schritt kann final sein, sodass keine weitere Transition möglich ist
 
## Transition

 * Der Übergang von Schritt *A* zu Schritt *B* wird als Transition bezeichnet
 * Ein Workflow hat exakt eine Anfangstransition
 * Eine Transition hat einen Zielschritt
 * Eine Transition führt eine beliebige Anzahl von Aktionen aus, die zu einem neuen Schritt führen
 * Eine Transition hängt von *Pre Conditions* und *Conditions* ab, die erfüllt sein müssen
 * Eine Transition kann verfügbar sein *(potentiell ausführbar)* oder erlaubt *(ausführbar)*.
 
## Action

 * Eine Action wird während einer Transition ausgeführt
 * Eine Action beinhaltet jegliche Logik, die zu einer Statusänderung führen
 * Es wird zwischen Actions und Post Actions unterschieden
 * Post Actions werden nach der Transformation zu einem neuen Status ausgeführt
 * Actions können einen Payload erfordern (Benutzereingabe)
 * Scheitert eine Action, wird ein neuer Status (nicht erfolgreich) generiert
 
## Payload

 * Der Payload führt Actions aus, der aus verschiedenen Quellen kommen kann
 * In der Benutzeroberfläche erfolgt die Eingabe in der Regel über Formulare

## Transition Conditions

 * Es existieren zwei Arten von Bedingungen: Pre conditions uns conditions
 * Pre conditions müssen erfüllt sein, sodass diese führ einen Nutzer zugänglich sind
 * Conditions überprüfen auch den Payload und validieren somit den Payload

## Entität

 * Datensätze werden als Entität bezeichnet
 * Entitäten können beliebig implementiert sein
 * Standardmäßig werden die Models von Contao als auch Datenbank-Results unterstützt
 
## Status

 * Jeder Vesuch einer Transition wird als Status gespeichert
 * Es existieren somit erfolgreiche und fehlgeschlagene Transitions
 * Jeder Status kann zudem spezifische Daten speichern

## History

 * Der Log aller Statusänderungen wird als History beschrieben
 
## Item

 * Ein Item ist ein Wrapper für eine Entität und behinhaltet workflowspezifische Informationen
 * Es verfügt über die State-History
 * Es Verfügt über den Namen des aktuellen Workflows
