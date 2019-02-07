# Features


## Entwickertool

 * Integration eigener Workflowtypen
 * Integration workflowspezifischer Geschäftslogik
 * Unabhängigkeit der Entitäten. Nutzen von Doctrine Entities, Contao Models, MetaModels, usw. 


## Konfiguration

 * Konfiguration von beliebigen Workflows direkt im Backend
 * Bereitstellung eines *Default* Workflowtyps
 * Aktivierung des Workflowtyps Default für beliebige DCA-basierte Tabellen mittels Applikationskonfiguration
 * Automatische Backend-Integration des Default Workflowtyps

## Berechtigungen

 * Berechtigungen je Workflow definieren
 * Anwendbar der Berechtigungen für Contao Benutzer und Mitglieder
 * Beschränkung von Transitions auf Berechtigungen

## Bedingungen

 * Beliebige Bedingungen für Transitions definierbar
 * Konfiguration im Backend von Property Conditions* oder *Expression Conditions* konfigurierbar
 
## Backend-Integration

 * Konfigurierbare Backend-Integration für den Workflowtyp *Default*
 * Backend-Ansicht für den Status
 * Backend-Ansicht für die Transition
 * Bereitstellung von Hilfsklassen für eigene Workflow-Integrationen

## Bereitgestellte Actions

### Action Formular

 * Einbindung eines Formulars des Formulargenerators zur Erfassung beliebiger Daten je Transition
 * Speicherung der Daten im aktuellen Status


### Action Notiz 
 
 * Erfassung einer einfachen Notiz je Transition
 * Speicherung der Notiz im aktuellen Status

### Action Notification 

 * Versand einer Notification über das [Notification Center](https://github.com/terminal42/contao-notification_center)
 * Versand abhängig vom Erfolg einer Transition konfigurierbar

## Geplante Features

 * Formulare anhand Teile der DCA-Konfiguration erstellen 
 * Zugriffsbeschränkung je Workflow-Schritt anwenden
 * Backend-Ansicht für einen vergangenen Workflow-Status
 * Frontend-Integration
