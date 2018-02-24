<?php
//#section#[header]
// Use Important Headers
use \API\Platform\importer;
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
importer::import("UI", "Apps");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("BSS", "WebDocs");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Developer\devTabber;
use \UI\Developer\editors\HTMLEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Navigation\navigationBar;
use \BSS\WebDocs\wDoc;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	if (empty($_POST['d']) || empty($_POST['name']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_queryTitle");
		$err = $errFormNtf->addErrorHeader("qTitle_h", $err_header);
		$errFormNtf->addErrorDescription($err, "qTitle_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
	{
		$notification = $errFormNtf->get();
		return devTabber::getNotificationResult($notification, ($has_error === TRUE));
	}
	
	$wDoc = new wDoc($_POST['d'], $_POST['name']);
	$result = $wDoc->update($_POST['doccontent']);
	
	$succFormNtf = new formNotification();
	if ($result)
	{
		$succFormNtf->build($type = "success", $header = FALSE, $footer = FALSE);
		$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
		$succFormNtf->append($errorMessage);
	}
	else
	{
		$succFormNtf->build($type = "error", $header = TRUE, $footer = FALSE);
		$errorMessage = $succFormNtf->getMessage("error", "err.save_error");
		
	}
	
	$succFormNtf->append($errorMessage);
	$notification = $succFormNtf->get();
	
	return devTabber::getNotificationResult($notification, ($result === TRUE));
}

// Create application content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build content
$appContainer = $appContent->build("", "webDocEditor")->get();

// Get document object
$wDoc = new wDoc($_GET['d'], $_GET['name']);

// Create form
$form = new simpleForm();
$docEditorForm = $form->build("", FALSE)->engageApp($appID, "documentEditor")->get();
$appContent->append($docEditorForm);

// Hidden Values
$input = $form->getInput($type = "hidden", $name = "d", $_GET['d'], $class = "", $autofocus = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "name", $_GET['name'], $class = "", $autofocus = FALSE);
$form->append($input);


// Toolbar Control
$tlb = new navigationBar();
$navToolbar = $tlb->build($dock = navigationBar::TOP, $appContainer)->get();
DOM::append($appContainer, $navToolbar);

// Save Button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);

// Delete query
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $tlb->insertToolbarItem($deleteTool);
$attr = array();
$attr['qid'] = $qID;
$attr['domain'] = $qDomain;
$actionFactory->setAppAction($deleteTool, $appID, "deleteDocument", "", $attr);


// Load wDoc content
$docEditor = new HTMLEditor("doccss", "doccontent");
$docEditorObject = $docEditor->build($wDoc->get())->get();
$form->append($docEditorObject);



// Send redWIDE Tab
$wide = new devTabber();
$docID = substr(hash("md5", $_GET['d']."/".$_GET['name']), 0, 10);
$header = $_GET['name'];
return $wide->getReportContent($docID, $header, $appContent->get());
//#section_end#
?>