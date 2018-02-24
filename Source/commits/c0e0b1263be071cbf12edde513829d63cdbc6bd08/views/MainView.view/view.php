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

// Create application content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build content
$appContent->build("", "WebDocsApplication", TRUE);

// Library Explorer
$libExplorerContainer = HTML::select(".webDocs .libExplorerContainer")->item(0);
$libExplorer = $appContent->getAppContainer($appID, "libraryExplorer");
DOM::append($libExplorerContainer, $libExplorer);

// Developer Tabber (red wide)
$docEditorContainer = HTML::select(".webDocs .docEditorContainer")->item(0);
$devTabber = new devTabber();
$wide = $devTabber->build()->get();
DOM::append($docEditorContainer, $wide);

return $appContent->getReport();
//#section_end#
?>