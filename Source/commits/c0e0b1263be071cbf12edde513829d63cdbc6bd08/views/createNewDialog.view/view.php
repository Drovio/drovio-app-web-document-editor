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
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("AEL", "Literals");
importer::import("BSS", "WebDocs");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Navigation\sideMenu;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\frames\windowFrame;
use \AEL\Literals\appLiteral;
use \BSS\WebDocs\wDocLibrary;

// Create Application Content
$appContent = new appContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build content
$appContent->build("", "createNewDialog", TRUE);
$sidebar = HTML::select(".createNewDialog .sidebar")->item(0);

// Create a sideMenu
$sMenu = new sideMenu();
$header = appLiteral::get("dialog", "lbl_menuHeader");
$sideMenu = $sMenu->build("", $header)->get();
DOM::append($sidebar, $sideMenu);

$targetcontainer = "mainDialog";
$targetgroup = "menuGroup";
$navgroup = "navGroup";
$display = "none";

$title = appLiteral::get("dialog", "lbl_folders");
$item = $sMenu->insertListItem("wd_folders", $title, TRUE);
$sMenu->addNavigation($item, $ref = "wDoc_folders", $targetcontainer, $targetgroup, $navgroup, $display);

$title = appLiteral::get("dialog", "lbl_docs");
$item = $sMenu->insertListItem("wd_docs", $title);
$sMenu->addNavigation($item, $ref = "wDoc_docs", $targetcontainer, $targetgroup, $navgroup, $display);

// Set navigator selectors
$ref_element = HTML::select("#wDoc_folders")->item(0);
$sMenu->addNavigationSelector($ref_element, $targetgroup);

$ref_element = HTML::select("#wDoc_docs")->item(0);
$sMenu->addNavigationSelector($ref_element, $targetgroup);


// Create form
$folderFormContainer = HTML::select(".dlgContainer.folders .formContainer")->item(0);
$form = new simpleForm();
$folderForm = $form->build()->engageApp($appID, "createFolder")->get();
DOM::append($folderFormContainer, $folderForm);

// Folder Parent
$wDocLib = new wDocLibrary();
$folderResources = array();
$folderResources[""] = "Root";
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


// Create form
$folderFormContainer = HTML::select(".dlgContainer.docs .formContainer")->item(0);
$form = new simpleForm();
$folderForm = $form->build()->engageApp($appID, "createDocument")->get();
DOM::append($folderFormContainer, $folderForm);

$title = appLiteral::get("dialog", "lbl_folderParent");
$label = $form->getLabel($title);
$input = $form->getResourceSelect($name = "parent", $multiple = FALSE, $class = "", $folderResources, $selectedValue = "");
$form->insertRow($title, $input, $required = TRUE, $notes = "");

$title = appLiteral::get("dialog", "lbl_docName");
$label = $form->getLabel($title);
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Build window frame
$wFrame = new windowFrame();
$title = appLiteral::get("dialog", "lbl_createNew");
$wFrame->build($title);

$wFrame->append($appContent->get());
return $wFrame->getFrame();
//#section_end#
?>