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
importer::import("UI", "Forms");
importer::import("AEL", "Literals");
importer::import("BSS", "WebDocs");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \AEL\Literals\appLiteral;
use \BSS\WebDocs\wDocLibrary;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check foldern name
	if (empty($_POST['parent']))
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
	$status = $docLib->removeFolder($_POST['parent']);
	if (!$status)
		return $errFormNtf->getReport();
		
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build(formNotification::SUCCESS);
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport();
}

return FALSE;
//#section_end#
?>