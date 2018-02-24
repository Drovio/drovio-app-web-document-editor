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
importer::import("UI", "Apps");
importer::import("UI", "Developer");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Developer\devTabber;

// Create application content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Get attributes
$documentDirectory = engine::getVar("d");
$documentName = engine::getVar("name");

// Build content
$class = "webDocViewerTab webDocViewerTab_".$documentDirectory."_".$documentName;
$appContainer = $appContent->build("", $class)->get();

// Load document viewer
$documentViewer = $appContent->loadView("documentViewer");
$appContent->append($documentViewer);

// Send redWIDE Tab
$wide = new devTabber();
$docID = substr(hash("md5", $documentDirectory."/".$documentName), 0, 10);
$header = $documentName;
return $wide->getReportContent($docID, $header, $appContent->get(), "wdocWide");
//#section_end#
?>