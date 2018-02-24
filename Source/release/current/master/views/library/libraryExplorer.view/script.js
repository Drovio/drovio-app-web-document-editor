var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Search notes
	jq(document).on("keyup", ".libraryExplorer .searchContainer .searchInput", function(ev) {
		// Get input and search notes
		var search = jq(this).val();
		searchDocuments(search);
	});
	
	// Enable search
	jq(document).on("focusin", ".libraryExplorer .searchContainer .searchInput", function(ev) {
		// Get input and search notes
		var search = jq(this).val();
		searchDocuments(search);
	});
	
	// Search all projects
	function searchDocuments(search) {
		// If search is empty, show all notes
		if (search == "")
			return jq(".libraryExplorer .docList .treeItem").removeClass("open").show();
		
		// Create the regular expression
		var regEx = new RegExp(jq.map(search.trim().split(' '), function(v) {
			return '(?=.*?' + v + ')';
		}).join(''), 'i');
		
		// Select all note rows, hide and filter by the regex then show
		jq(".libraryExplorer .docList .treeItem").hide().find(".wdc").filter(function() {
			return regEx.exec(jq(this).text());
		}).each(function() {
			jq(this).parents(".treeItem").addClass("open").show();
		});
	}
	
	
	// Reload note list
	jq(document).on("documents.library.reload", function() {
		jq("#wDocLib").trigger("saveState");
		jq(".libraryExplorerContainer").trigger("reload");
	});
	
	// Set interval to reload the notes every 60 seconds
	setInterval(function() {
		jq("#wDocLib").trigger("saveState");
		jq(".libraryExplorerContainer").trigger("reload");
	}, 60000);
	
	
	// Clean note container
	jq(document).on("documents.remove", function(ev, documentID) {
		// Remove document
		/*
		var ntrow = jq(".libraryExplorer .ntrow#"+documentID);
		if (ntrow.hasClass("selected"))
			jq(".noteContainer").html("");
		
		// Remove row
		ntrow.remove();
		
		// Check if there are note rows remaining
		if (jq(".ntrow").length == 0)
			jq(".libraryExplorerContainer").trigger("reload");
		*/
	});
	/*
	// Trigger events when the content is modified
	jq(document).on("content.modified", function() {
		// Get note editor note id and select row (if any)
		var noteID = jq(".noteEditorContainer").attr("id");
		jq(".ntrow#"+noteID).addClass("selected");
		
		// Set listeners for all remote note forms
		jq(".ntrow .removeNoteForm").off("submit");
		jq(".ntrow .removeNoteForm").on("submit", function(ev) {
			// Confirm to delete the note
			return confirmRemoveNote(ev);
		});
	});
	
	jq(".ntrow .removeNoteForm").on("submit", function(ev) {
		// Confirm to delete the note
		return confirmRemoveNote(ev);
	});
	
	function confirmRemoveNote(ev) {
		// Confirm to delete the note
		var status = confirm("Are you sure you want to delete this note?");
		if (!status) {
			ev.preventDefault();
			return false;
		}
	}
	*/
});