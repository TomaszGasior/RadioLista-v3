
// Easy to use UI for columns order edition in radiotable settings.
function createColumnsOrderUI() {

	// Don't load in IE10.
	if( typeof(document.documentMode) !== 'undefined' && document.documentMode < 11 ) return;

	// Get from definition list (<DL> tag) and <DD> tags within.
	var interfaceDLTag  = document.querySelector('dl.radioTableColumnsForm'),
		interfaceDDTags = interfaceDLTag.querySelectorAll('dl.radioTableColumnsForm > dd'),
		interfaceCount  = interfaceDDTags.length;

	// Create array width keys from absolute value of <INPUT> values with <DT>, <DD> and <INPUT> tags.
	var interfaceElements = [],
		temporaryArray    = [];
	for( var i=0; i<interfaceCount; i++ ) {
		temporaryArray['dd']    = interfaceDDTags[i];
		temporaryArray['dt']    = interfaceDDTags[i].previousElementSibling;
		temporaryArray['input'] = interfaceDDTags[i].firstElementChild;

		// Validate interface elements array content. Exit when is not valid.
		if( typeof(interfaceElements[ Math.abs(temporaryArray['input'].value) ]) != 'undefined' ) {
			// Array may contain positive and negative values, but it must not be repeated.
			console.log('Script encountered fatal error. Interface elemenets array don\'t contain unique values.');
			return;
		}

		interfaceElements[ Math.abs(temporaryArray['input'].value) ] = temporaryArray;
		temporaryArray = [];
	}
	interfaceDDTags = null; temporaryArray = null;

	// Validate interface elements array content. Exit when is not valid.
	if( interfaceCount != interfaceElements.length-1 ) {
		// Array.length contain incremented index of last array element, not count of array elements.
		console.log('Script encountered fatal error. Interface elemenets array count is not valid.');
		return;
	}

	// Change ID of <DL> tag to mark, that JS is enabled.
	interfaceDLTag.className = 'radioTableColumnsForm_JSOn';

	// Function, that refresh interface elements for proper sorting.
	var refreshInterface = function() {
		for( var i=1; i<=interfaceCount; i++ ) {
			var thisElement  = interfaceElements[i];

			// Temporary hide <DT> and <DD> tags. They are sorted and we need to append them again.
			interfaceDLTag.removeChild(thisElement['dt']);
			interfaceDLTag.removeChild(thisElement['dd']);

			// After sorting update IDs saved in buttons attributes.
			thisElement['buttonMoveUp'].dataset.forId   = i;
			thisElement['buttonMoveDown'].dataset.forId = i;
			thisElement['buttonHideShow'].dataset.forId = i;

			// Update also state of "move up" and "move down" buttons.
			thisElement['buttonMoveUp'].disabled   = (Math.abs(thisElement['input'].value)==1) ? true : false;
			thisElement['buttonMoveDown'].disabled = (Math.abs(thisElement['input'].value)==interfaceCount) ? true : false;

			// Append <DT> and <DD> tags again.
			interfaceDLTag.appendChild(thisElement['dt']);
			interfaceDLTag.appendChild(thisElement['dd']);
		}
	}

	// Button command: hide and show column.
	var hideShowColumn = function() {
		var thisElement = interfaceElements[ this.dataset.forId ];

		if( thisElement['input'].value < 0 ) {
			thisElement['input'].value  = Math.abs(thisElement['input'].value);
			thisElement['dt'].className = '';
			thisElement['dd'].className = '';
			this.innerHTML              = 'Ukryj';
		}
		else {
			thisElement['input'].value  = -(thisElement['input'].value);
			thisElement['dt'].className = 'hiddenColumn';
			thisElement['dd'].className = 'hiddenColumn';
			this.innerHTML              = 'Pokaż';
		}

		// refreshInterface() is not needed here.
		// this.focus() is unneeded too.
	}

	// Button command: move up column.
	var moveUpColumn = function() {
		var id          = parseInt(this.dataset.forId);
		var thisElement = interfaceElements[id];

		if( id > 1 ) {
			var temporaryArray = interfaceElements[id-1];

			( thisElement['input'].value > 0 ) ? --thisElement['input'].value : ++thisElement['input'].value;
			interfaceElements[id-1] = thisElement;

			( temporaryArray['input'].value > 0 ) ? ++temporaryArray['input'].value : --temporaryArray['input'].value;
			interfaceElements[id] = temporaryArray;

			refreshInterface();
			this.focus();
		}
	}

	// Button command: move down column.
	var moveDownColumn = function() {
		var id           = parseInt(this.dataset.forId);
		var thisElement  = interfaceElements[id];

		if( id < interfaceCount ) {   // IDs are started from 1, not from 0.
			var temporaryArray = interfaceElements[id+1];

			( thisElement['input'].value > 0 ) ? ++thisElement['input'].value : --thisElement['input'].value;
			interfaceElements[id+1] = thisElement;

			( temporaryArray['input'].value > 0 ) ? --temporaryArray['input'].value : ++temporaryArray['input'].value;
			interfaceElements[id] = temporaryArray;

			refreshInterface();
			this.focus();
		}
	}

	// Prepare user interface for columns order edition.
	for( var i=1; i<=interfaceCount; i++ ) {
		var thisElement = interfaceElements[i];

		// Hide inputs - this is fallback for non-JS browsers.
		thisElement['input'].type = 'hidden';
		thisElement['dt'].firstElementChild.removeAttribute('for');  // Remove "for" attribute from <LABEL>.

		// Set CSS class, when radiotable column is hidden.
		thisElement['dt'].className = ( thisElement['input'].value > 0 ) ? '' : 'hiddenColumn';
		thisElement['dd'].className = ( thisElement['input'].value > 0 ) ? '' : 'hiddenColumn';

		// Set up "move up" button.
		thisElement['buttonMoveUp'] = document.createElement('button');
		thisElement['buttonMoveUp'].type      = 'button';
		thisElement['buttonMoveUp'].innerHTML = 'Do góry';
		thisElement['buttonMoveUp'].addEventListener( 'click', moveUpColumn );

		// Set up "move down" button.
		thisElement['buttonMoveDown'] = document.createElement('button');
		thisElement['buttonMoveDown'].type      = 'button';
		thisElement['buttonMoveDown'].innerHTML = 'W dół';
		thisElement['buttonMoveDown'].addEventListener( 'click', moveDownColumn );

		// Set up "hide" button.
		thisElement['buttonHideShow'] = document.createElement('button');
		thisElement['buttonHideShow'].type      = 'button';
		thisElement['buttonHideShow'].innerHTML = ( thisElement['input'].value > 0 ) ? 'Ukryj' : 'Pokaż';
		thisElement['buttonHideShow'].addEventListener( 'click', hideShowColumn );

		// Disable "hide" button when it is needed.
		if( thisElement['input'].name == 'columns[frequency]' || thisElement['input'].name == 'columns[name]' )
			thisElement['buttonHideShow'].disabled = true;

		// Append buttons to <DD> tags.
		thisElement['dd'].appendChild(thisElement['buttonHideShow']);
		thisElement['dd'].appendChild(thisElement['buttonMoveUp']);
		thisElement['dd'].appendChild(thisElement['buttonMoveDown']);
	}

	// Refresh interface elements first time.
	refreshInterface();

}


// Start function.
createColumnsOrderUI();
