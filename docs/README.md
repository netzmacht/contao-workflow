# Überblick

**netzmacht/contao-workflow** ist ein Bundle für das Open Source CMS Contao und ermöglicht die Konfiguration und Integration von Workflows. Flexibilität, Erweiterbarkeit und Anpassbarkeit an spezifische Anwendungsfälle zeichnen die Erweiterung aus. 

Auch wenn **netzmacht/contao-workflow** eine Standard-Integration von Workflows bietet, entfaltet die Erweiterung erst
durch die Entwicklung anwendungsfallspezifischer Logik ihre volle Stärke.

Als Workflow-Bibliothek wird [netzmacht/workflow](https://github.com/netzmacht/workflow) verwendet, dass auch als 
Standalone-Lösung für PHP-Projekte, die nicht auf Contao fußen, genutzt werden kann.

## Systemanforderungen

**netzmacht/contao-workflow** setzt folgende Anforderungen voraus:

 * min. Contao 4.4 (4.x)
 * min. Symfony Componenten in Version 3.4. Version 4 wird auch untersützt
 * PHP 7.1 mit Extensions `json` und und `pdo` 

## Features

 * Konfiguration beliebiger Workflows 
 * Flexibel und erweiterbar
 * Bereitstellung eines Workflowtyps Default
 * Aktivierung des Workflowtyps Default für beliebige DCA-basierte Tabellen mittels Konfiguration
 * Automatische Backend-Integration des Default Workflowtyps
 * Versand von Benachrichtungen über das [Notification Center](https://github.com/terminal42/contao-notification_center)
 * Erfassung beliebiger Formulareingaben

## Geplante Features

 * Frontend-Integration
 * Formulare anhand Teile der DCA-Konfiguration erstellen 

