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
importer::import("UI", "Navigation");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Navigation\treeView;
use \UI\Navigation\navigationBar;
use \UI\Navigation\toolbarComponents\toolbarMenu;
use \BSS\WebDocs\wDocLibrary;

// Create application content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build content
$appContent->build("", "libraryExplorerContainer", TRUE);

// Create tree view explorer
$navTree = new treeView();
$treeViewElement = $navTree->build("wDocLib")->get();

$docList = HTML::select(".libraryExplorer .docList")->item(0);
DOM::append($docList, $treeViewElement);

// Read library
$wDocLib = new wDocLibrary();
buildLibTree($actionFactory, $navTree, $wDocLib);

return $appContent->getReport();


function buildLibTree($actionFactory, $navTree, $docLib, $parent = "")
{
	// Get folder docs
	$docs = $docLib->getFolderDocs($parent);
	foreach ($docs as $docName)
	{
		// Create group item container
		$item = DOM::create("div", "", "", "wdc");
		$itemIco = DOM::create("span", "", "", "contentIcon flIco");
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", $docName);
		DOM::append($item, $itemName);
		
		$itemPath = (empty($parent) ? "" : $parent."/").$docName;
		$itemID = substr(hash("md5", $itemPath), 0, 10);
		if (!empty($parent))
			$parentID = substr(hash("md5", $parent), 0, 10);
		$treeItem = $navTree->insertTreeItem($itemID, $item, $parentID);
		$navTree->assignSortValue($treeItem, $docName);
		
		// Set document loader
		$attr = array();
		$attr['d'] = $parent;
		$attr['name'] = $docName;
		$actionFactory->setAction($treeItem, "docViewer_Tab", "", $attr, TRUE);
	}
	
	// Get sub folders
	$folders = $docLib->getLibFolders($parent);
	foreach ($folders as $folderName => $children)
	{
		// Check for public folder
		$public = ($folderName."/" == wDocLibrary::PUBLIC_FOLDER);
		// Create group item container
		$item = DOM::create("div", "", "", "wdf");
		$itemIco = DOM::create("span", "", "", "contentIcon ".($public ? "libIco" : "fldIco"));
		DOM::append($item, $itemIco);
		$itemName = DOM::create("span", $folderName);
		DOM::append($item, $itemName);
		
		$itemPath = (empty($parent) ? "" : $parent."/").$folderName;
		$itemID = substr(hash("md5", $itemPath), 0, 10);
		if (!empty($parent))
			$parentID = substr(hash("md5", $parent), 0, 10);
		$treeItem = $navTree->insertExpandableTreeItem($itemID, $item, $parentID);
		$navTree->assignSortValue($treeItem, ($public ? ".." : ".").$folderName);
		
		// Get children folders
		buildLibTree($actionFactory, $navTree, $docLib, (empty($parent) ? "" : $parent."/").$folderName);
	}
}
//#section_end#
?>