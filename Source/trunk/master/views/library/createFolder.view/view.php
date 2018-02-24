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
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\dialogFrame;
use \AEL\Literals\appLiteral;
use \BSS\WebDocs\wDocLibrary;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Domain
	$empty = is_null($_POST['name']) || empty($_POST['name']);
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = appLiteral::get("dialog", "lbl_folderName");
		$err_header = $errFormNtf->addErrorHeader('domainErrorHeader', $header);
		
		// Description
		$errFormNtf->addErrorDescription($err_header, 'domainErrorDescription', "err.required");
	}
	
	// If error, show notification
	if ($has_error)	
		return $errFormNtf->getReport();
		
	// Create a new Query
	$docLib = new wDocLibrary();
	$parent = ($_POST['parent'] < 0 ? "" : $_POST['parent']);
	$status = $docLib->createFolder($parent, $_POST['name']);
	if (!$status)
		return $errFormNtf->getReport();
		
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build(formNotification::SUCCESS);
	
	// Reload library
	$successNotification->addReportAction($type = "documents.library.reload", $value = "");
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport();
}


// Build dialog frame
$wFrame = new dialogFrame();
$title = appLiteral::get("dialog", "lbl_createNewFolder");
$wFrame->build($title)->engageApp("library/createFolder");
$form = $wFrame->getFormFactory();

// Folder Parent
$wDocLib = new wDocLibrary();
$folderResources = array();
$folderResources["-1"] = "Root";
$libFolders = $wDocLib->getLibFolders("", TRUE);
foreach ($libFolders as $fl)
	$folderResources[$fl] = $fl;
ksort($folderResources);

$title = appLiteral::get("dialog", "lbl_folderParent");
$label = $form->getLabel($title);
$input = $form->getResourceSelect($name = "parent", $multiple = FALSE, $class = "", $folderResources, $selectedValue = "");
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = appLiteral::get("dialog", "lbl_folderName");
$label = $form->getLabel($title);
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");


return $wFrame->getFrame();
//#section_end#
?>