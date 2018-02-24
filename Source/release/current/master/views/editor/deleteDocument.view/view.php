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
importer::import("AEL", "Literals");
importer::import("BSS", "WebDocs");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \BSS\WebDocs\wDoc;

$docFolder = engine::getVar("d");
$docName = engine::getVar("name");
if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	if (!simpleForm::validate())
	{
		$hasError = TRUE;
		
		// Header
		$err_header = DOM::create("div", "ERROR");
		$err = $errFormNtf->addErrorHeader("lblDesc_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblDesc_desc", $errFormNtf->getErrorMessage("err.invalid"));
	}
	if ($hasError)
		return $errFormNtf->getReport();
	
	// Commit SDK Object
	$wdoc = new wDoc($docFolder, $docName);
	$status = $wdoc->remove();

	// If there is an error in creating the folder, show it
	if ($status !== TRUE)
	{
		$err_header = appLiteral::get("editor", "lbl_deleteDocument");
		$err = $errFormNtf->addErrorHeader("lblFolder_h", $err_header);
		$errFormNtf->addErrorDescription($err, "lblFolder_desc", "Error removing document.");
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Reload library
	$succFormNtf->addReportAction($type = "documents.library.reload", $value = "");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Build the frame
$frame = new dialogFrame();
$title = appLiteral::get("editor", "hd_deleteDocument", array(), FALSE);
$frame->build($title, "", FALSE)->engageApp($appID, "editor/deleteDocument", FALSE);
$sForm = new simpleForm();

// Header
$title = appLiteral::get("editor", "lbl_deleteDocument");
$hd = DOM::create("h2", $title);
$frame->append($hd);

$path = $docFolder."/".$docName.".wDoc";
$p = DOM::create("h4", $path);
$frame->append($p);

// Document folder
$input = $sForm->getInput($type = "hidden", $name = "d", $value = $docFolder);
$frame->append($input);

// Document name
$input = $sForm->getInput($type = "hidden", $name = "name", $value = $docName);
$frame->append($input);

// Return the report
return $frame->getFrame();
//#section_end#
?>