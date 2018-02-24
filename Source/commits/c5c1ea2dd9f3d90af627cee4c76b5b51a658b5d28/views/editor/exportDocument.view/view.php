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
importer::import("UI", "Content");

// Import APP Packages
//#section_end#
//#section#[view]
use \API\Geoloc\locale;
use \UI\Content\MIMEContent;
use \BSS\WebDocs\wDoc;

// Get attributes
$documentDirectory = engine::getVar("d");
$documentName = engine::getVar("name");
$selectedLocale = engine::getVar("dlc");
$selectedLocale = (empty($selectedLocale) ? locale::get() : $selectedLocale);

// Initialize document
$wDoc = new wDoc($documentDirectory, $documentName);
$exportedDoc = $wDoc->export($selectedLocale);

// SEt mime file to download
$mime = new MIMEContent();
$mime->setFileContents($exportedDoc, $type = MIMEContent::CONTENT_TEXT_HTML);
	
// Return (to download)
return $mime->getReport($documentName.".".$selectedLocale.".html", $ignore_user_abort = FALSE, $removeFile = TRUE);
//#section_end#
?>