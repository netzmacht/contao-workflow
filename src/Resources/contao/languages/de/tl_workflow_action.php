<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link https://www.transifex.com/signup/
 * @link https://www.transifex.com/projects/p/contao-workflow/language/de/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2020-10-13T07:00:59+00:00
 */

$GLOBALS['TL_LANG']['tl_workflow_action']['active']['0']                           = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_workflow_action']['active']['1']                           = 'Aktive Aktionen werden während der Transition ausgeführt.';
$GLOBALS['TL_LANG']['tl_workflow_action']['assign_user_permission']['0']           = 'Benötigte Berechtigung';
$GLOBALS['TL_LANG']['tl_workflow_action']['assign_user_property']['0']             = 'Benutzer-Eigenschaft';
$GLOBALS['TL_LANG']['tl_workflow_action']['conditions_legend']                     = 'Bedingungen';
$GLOBALS['TL_LANG']['tl_workflow_action']['config_legend']                         = 'Einstellungen';
$GLOBALS['TL_LANG']['tl_workflow_action']['delete']['0']                           = 'Löschen';
$GLOBALS['TL_LANG']['tl_workflow_action']['delete']['1']                           = 'Aktion %s löschen';
$GLOBALS['TL_LANG']['tl_workflow_action']['description']['0']                      = 'Beschreibung';
$GLOBALS['TL_LANG']['tl_workflow_action']['description']['1']                      = 'Beschreibung der Aktion';
$GLOBALS['TL_LANG']['tl_workflow_action']['description_legend']                    = 'Beschreibung';
$GLOBALS['TL_LANG']['tl_workflow_action']['edit']['0']                             = 'Bearbeiten';
$GLOBALS['TL_LANG']['tl_workflow_action']['edit']['1']                             = 'Aktion %s bearbeiten';
$GLOBALS['TL_LANG']['tl_workflow_action']['final']['0']                            = 'Abschliessender Schritt';
$GLOBALS['TL_LANG']['tl_workflow_action']['final']['1']                            = 'Diesen Schritt als abschlißend festlegen. Danach sind keine Transitionen mehr erlaubt.';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_fieldset']['0']                    = 'Fieldset-Wrapper hinzufügen';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_fieldset']['1']                    = 'Fügt ein umgebendes Fieldset-Element um die Formularinhalte hinzu (nicht benötigt, falls ein Fieldset bereits im Formular definiert wurde).';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_formId']['0']                      = 'Formular';
$GLOBALS['TL_LANG']['tl_workflow_action']['form_formId']['1']                      = 'Ein Formular zur Integration auswählen.';
$GLOBALS['TL_LANG']['tl_workflow_action']['label']['0']                            = 'Bezeichnung';
$GLOBALS['TL_LANG']['tl_workflow_action']['label']['1']                            = 'Bezeichnung der Aktion';
$GLOBALS['TL_LANG']['tl_workflow_action']['name']['0']                             = 'Name';
$GLOBALS['TL_LANG']['tl_workflow_action']['name']['1']                             = 'Name der Workflow-Aktion';
$GLOBALS['TL_LANG']['tl_workflow_action']['name_legend']                           = 'Workflow-Aktion';
$GLOBALS['TL_LANG']['tl_workflow_action']['new']['0']                              = 'Neue Aktion';
$GLOBALS['TL_LANG']['tl_workflow_action']['new']['1']                              = 'Eine neue Aktion erstellen';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_minlength']['0']                   = 'Minimale Länge';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_minlength']['1']                   = 'Falls festgelegt ist die Mindestlänge an Zeichen erforderlich.';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_required']['0']                    = 'Pflichtfeld';
$GLOBALS['TL_LANG']['tl_workflow_action']['note_required']['1']                    = 'Bitte vordefinieren, falls Ausfüllen nicht nötig sein soll.';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_id']['0']                  = 'Benachrichtigung';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_id']['1']                  = 'Eine Benachrichtigung zum Senden auswählen.';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_state_options']['failed']  = 'fehlgeschlagen';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_state_options']['success'] = 'erfolgreich';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_states']['0']              = 'Erfolgs-Zustände';
$GLOBALS['TL_LANG']['tl_workflow_action']['notification_states']['1']              = 'Benachrichtigung senden, wenn die Transition erfolgreich war oder fehlgeschlagen ist.';
$GLOBALS['TL_LANG']['tl_workflow_action']['property']['0']                         = 'Eigenschaft';
$GLOBALS['TL_LANG']['tl_workflow_action']['property']['1']                         = 'Eine Eigenschaft auswählen, die verändert werden soll';
$GLOBALS['TL_LANG']['tl_workflow_action']['propertyChanged']['0']                  = 'Änderungen protokollieren';
$GLOBALS['TL_LANG']['tl_workflow_action']['propertyChanged']['1']                  = 'Änderungen der Aktion werden protokolliert.';
$GLOBALS['TL_LANG']['tl_workflow_action']['property_expression']['0']              = 'Ausdruck auswerten';
$GLOBALS['TL_LANG']['tl_workflow_action']['property_expression']['1']              = 'Der Eigenschafts-Wert ist eine Symfony-Expression.';
$GLOBALS['TL_LANG']['tl_workflow_action']['property_value']['0']                   = 'Eigenschafts-Wert oder -Ausdruck';
$GLOBALS['TL_LANG']['tl_workflow_action']['property_value']['1']                   = 'Neuer Eigenschaftswert. Falls eine Expression benutzt wird, wird gleichzeitig der Zugriff auf  <em>entity</em> and <em>now</em> gewährt. Beispiel: <em>entity.get(\'author.admin\') ? \'\' : (now.getTimestamp() + 86400)</em>';
$GLOBALS['TL_LANG']['tl_workflow_action']['reference']['0']                        = 'Referenz';
$GLOBALS['TL_LANG']['tl_workflow_action']['reference']['1']                        = 'Referenz-Aktion, die auf Workflow-Ebene definiert wurde.';
$GLOBALS['TL_LANG']['tl_workflow_action']['show']['0']                             = 'Details';
$GLOBALS['TL_LANG']['tl_workflow_action']['show']['1']                             = 'Details der Aktion %s anzeigen';
$GLOBALS['TL_LANG']['tl_workflow_action']['toggle']['0']                           = 'Aktion aktivieren / deaktivieren';
$GLOBALS['TL_LANG']['tl_workflow_action']['toggle']['1']                           = 'Die Aktion ID "%s" aktivieren / deaktivieren';
$GLOBALS['TL_LANG']['tl_workflow_action']['type']['0']                             = 'Typ';
$GLOBALS['TL_LANG']['tl_workflow_action']['type']['1']                             = 'Typ der Aktion';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['default']                      = 'Standard';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['form']['0']                    = 'Formular';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['form']['1']                    = 'Daten aus einem Formular des Form-Generators während der Transition hinzufügen.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['note']['0']                    = 'Notiz';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['note']['1']                    = 'Eine Notiz während der Transition hinzufügen.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['notification']['0']            = 'Benachrichtigung';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['notification']['1']            = 'Eine Benachrichtigung mit dem Notification Center senden.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['reference']['0']               = 'Referenz';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['reference']['1']               = 'Referenz auf eine Aktion, die im Workflow definiert wurde.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['transitions']                  = 'Transitionen';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['update_entity']['0']           = 'Entität aktualisieren';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['update_entity']['1']           = 'Entität mit einem Formular mit ausgewählten Eigenschaften aktualisieren.';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['update_property']['0']         = 'Eigenschaft aktualisieren';
$GLOBALS['TL_LANG']['tl_workflow_action']['types']['update_property']['1']         = 'Den Eigenschaftswert einer Entität aktualisieren.';
$GLOBALS['TL_LANG']['tl_workflow_action']['update_entity_properties']['0']         = 'Eigenschaften';
$GLOBALS['TL_LANG']['tl_workflow_action']['update_entity_properties']['1']         = 'Bitte die Eigenschaften auswählen, die innerhalb dieser Aktion aktualisiert werden sollen.';

