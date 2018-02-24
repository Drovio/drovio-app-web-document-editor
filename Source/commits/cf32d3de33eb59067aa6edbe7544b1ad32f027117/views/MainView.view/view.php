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
importer::import("BSS", "WebDocs");
importer::import("UI", "Apps");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Developer\devTabber;
use \UI\Presentation\gridSplitter;

// Create application content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build content
$appContent->build("", "WebDocsApplication", TRUE);

// Create grid splitter
$splitter = new gridSplitter();
$gSplitter = $splitter->build($orientation = gridSplitter::ORIENT_HOZ, $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, $sideTitle = "Docs Library")->get();
$appContent->append($gSplitter);

// Library Explorer
$libExplorer = $appContent->getAppViewContainer($viewName = "library/libraryExplorer", $attr = array(), $startup = FALSE, $containerID = "", $loading = FALSE, $preload = TRUE);
$splitter->appendToSide($libExplorer);


// Get docOuterContainer to append to main
$docOuterContainer = HTML::select(".docOuterContainer")->item(0);
$splitter->appendToMain($docOuterContainer);


// Developer Tabber
$devTabber = new devTabber();
$wide = $devTabber->build($id = "wdocWide", $withBorder = FALSE)->get();

$docContainer = HTML::select(".docOuterContainer .docContainer")->item(0);
DOM::append($docContainer, $wide);



// Set actions
$button = HTML::select(".docOuterContainer .toolbar .new_folder")->item(0);
$actionFactory->setAction($button, "library/createFolder");

// New document
$button = HTML::select(".docOuterContainer .toolbar .new_document")->item(0);
$actionFactory->setAction($button, "library/createDocument");

// Remove folder
$button = HTML::select(".docOuterContainer .toolbar .delete_folder")->item(0);
$actionFactory->setAction($button, "library/removeFolder");

return $appContent->getReport();
//#section_end#
?>