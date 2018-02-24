<?php
//#section#[header]
// Use Important Headers
use \API\Platform\importer;
use \API\Platform\engine;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import DOM, HTML
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

use \UI\Html\DOM;
use \UI\Html\HTML;

// Import application for initialization
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;

// Increase application's view loading depth
application::incLoadingDepth();

// Set Application ID
$appID = 38;

// Init Application and Application literal
application::init(38);
// Secure Importer
importer::secure(TRUE);

// Import SDK Packages
importer::import("API", "Geoloc");
importer::import("BSS", "WebDocs");
importer::import("UI", "Apps");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");

// Import APP Packages
//#section_end#
//#section#[view]
use \API\Geoloc\locale;
use \UI\Apps\APPContent;
use \UI\Developer\devTabber;
use \UI\Developer\editors\HTMLEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Navigation\navigationBar;
use \BSS\WebDocs\wDoc;

// Get attributes
$documentDirectory = engine::getVar("d");
$documentName = engine::getVar("name");
$selectedLocale = engine::getVar("dlc");
$selectedLocale = (empty($selectedLocale) ? locale::get() : $selectedLocale);

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	if (empty($documentDirectory) || empty($documentName))
	{
		$has_error = TRUE;
		
		// Header
		$err = $errFormNtf->addErrorHeader("qTitle_h", $err_header);
		$errFormNtf->addErrorDescription($err, "qTitle_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
	{
		$notification = $errFormNtf->get();
		return devTabber::getNotificationResult($notification, ($has_error === TRUE));
	}
	
	$wDoc = new wDoc($documentDirectory, $documentName);
	$result = $wDoc->update($_POST['doccontent'], $selectedLocale);
	
	$succFormNtf = new formNotification();
	if ($result)
	{
		$succFormNtf->build($type = formNotification::SUCCESS, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
		$succFormNtf->append($errorMessage);
	}
	else
	{
		$succFormNtf->build($type = formNotification::ERROR, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$errorMessage = $succFormNtf->getMessage("error", "err.save_error");
		
	}
	
	$succFormNtf->append($errorMessage);
	$notification = $succFormNtf->get();
	
	return devTabber::getNotificationResult($notification, ($result === TRUE), "wdocWide");
}

// Create application content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build content
$appContainer = $appContent->build("", "webDocEditor wDoc")->get();

// Get document object
$wDoc = new wDoc($documentDirectory, $documentName);

// Create form
$form = new simpleForm();
$docEditorForm = $form->build("", FALSE)->engageApp($appID, "editor/documentEditor")->get();
$appContent->append($docEditorForm);

// Hidden Values
$input = $form->getInput($type = "hidden", $name = "d", $documentDirectory, $class = "", $autofocus = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "name", $documentName, $class = "", $autofocus = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "dlc", $selectedLocale, $class = "", $autofocus = FALSE);
$form->append($input);

// Editor's form inner container
$editorContainer = DOM::create("div", "", "", "docEditorFormContainer");
$form->append($editorContainer);

// Toolbar Control
$tlb = new navigationBar();
$navToolbar = $tlb->build($dock = navigationBar::TOP, $editorContainer)->get();
DOM::append($editorContainer, $navToolbar);

// Save Button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);

// Delete document
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $tlb->insertToolbarItem($deleteTool);
$attr = array();
$attr['d'] = $documentDirectory;
$attr['name'] = $documentName;
$actionFactory->setAction($deleteTool, "editor/deleteDocument", "", $attr);

// Preview document
$prvTool = DOM::create("span", "", "", "objTool preview");
$tool = $tlb->insertToolbarItem($prvTool);
$attr = array();
$attr['d'] = $documentDirectory;
$attr['name'] = $documentName;
$attr['dlc'] = $selectedLocale;
$docHolder = ".webDocViewerTab_".$documentDirectory."_".$documentName;
$actionFactory->setAction($prvTool, "documentViewer", $docHolder, $attr);


// Load wDoc content
$docEditor = new HTMLEditor("doccss", "doccontent");
$docEditorObject = $docEditor->build($wDoc->get($selectedLocale))->get();
DOM::append($editorContainer, $docEditorObject);

return $appContent->getReport("", APPContent::REPLACE_METHOD);
//#section_end#
?>