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
importer::import("API", "Geoloc");
importer::import("BSS", "WebDocs");
importer::import("UI", "Apps");
importer::import("UI", "Developer");
importer::import("UI", "Forms");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \API\Geoloc\locale;
use \UI\Apps\APPContent;
use \UI\Developer\devTabber;
use \UI\Forms\templates\simpleForm;
use \BSS\WebDocs\wDoc;

// Create application content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build content
$appContainer = $appContent->build("", "webDocViewerContainer", TRUE)->get();

// Get attributes
$documentDirectory = engine::getVar("d");
$documentName = engine::getVar("name");
$selectedLocale = engine::getVar("dlc");
$selectedLocale = (empty($selectedLocale) ? locale::get() : $selectedLocale);


// Create form for changing the preview
$formContainer = HTML::select(".webDocViewer .navbar .pvfContainer")->item(0);
$form = new simpleForm();
$previewForm = $form->build("", FALSE)->engageApp("documentViewer")->get();
DOM::append($formContainer, $previewForm);

$input = $form->getInput($type = "hidden", $name = "d", $value = $documentDirectory, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "name", $value = $documentName, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

$docHolder = ".webDocViewerTab_".$documentDirectory."_".$documentName;
$docHolder = str_replace("/", "_", $docHolder);
$input = $form->getInput($type = "hidden", $name = "holder", $value = $docHolder, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);


// Get all locale
$alocale = locale::active();
$lcResource = array();
foreach ($alocale as $locale)
	$lcResource[$locale['locale']] = $locale['friendlyName'];
	
$selectInput = $form->getResourceSelect($name = "dlc", $multiple = FALSE, $class = "pvfInput", $resource = $lcResource, $selectedValue = $selectedLocale);
$inputID = DOM::attr($selectInput, "id");

// Label
$title = appLiteral::get("preview", "lbl_previewDoc");
$label = $form->getLabel($title, $for = $inputID, $class = "pvfLabel");
$form->append($label);

// Append select
$form->append($selectInput);

// Submit button
$title = appLiteral::get("preview", "lbl_preview");
$button = $form->getSubmitButton($title, $id = "", $name = "");
HTML::addClass($button, "pvfButton");
$form->append($button);


// Edit Document
$editAction = HTML::select(".webDocViewer .edfContainer .action.edit")->item(0);
$attr = array();
$attr['d'] = $documentDirectory;
$attr['name'] = $documentName;
$attr['dlc'] = $selectedLocale;
$actionFactory->setAction($editAction, $viewName = "editor/documentEditor", $docHolder, $attr, $loading = TRUE);


// Export Document
$exportAction = HTML::select(".webDocViewer .edfContainer .action.export")->item(0);
$attr = array();
$attr['d'] = $documentDirectory;
$attr['name'] = $documentName;
$attr['dlc'] = $selectedLocale;
$actionFactory->setDownloadAction($exportAction, $viewName = "editor/exportDocument", $attr);


// Get document object
$wDoc = new wDoc($documentDirectory, $documentName);

// Preview document full html
$docContainer = HTML::select(".webDocViewer .docContainer")->item(0);
HTML::innerHTML($docContainer, $wDoc->load($selectedLocale));

return $appContent->getReport($docHolder, APPContent::REPLACE_METHOD);
//#section_end#
?>