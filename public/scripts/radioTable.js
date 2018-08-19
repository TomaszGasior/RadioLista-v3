
// Popup for RDS informations.
function createRDSPopup() {

	// Get radiotable container and spans with RDS PS cells.
	var radioTableDiv = document.querySelector('div.radioTable');
	var rdsPsSpans    = radioTableDiv.querySelectorAll('span.rds.rdsPopup');

	// Create RDS popup element.
	var popupElement = document.createElement('div');
	var dlElement    = document.createElement('dl');
	popupElement.appendChild( document.createElement('h3') );
	popupElement.children[0].innerHTML = 'Informacje o RDS';
	popupElement.appendChild(dlElement);

	// Create <DL> elements.
	var labels = { 0 : 'PS', 1 : 'RT', 2 : 'PTY' };
	for( var i=0; i<3; i++ ) {
		var elDt = dlElement.appendChild( document.createElement('dt') );
		var elDd = dlElement.appendChild( document.createElement('dd') );
		elDt.innerHTML = labels[i];
		elDt.hidden = true;
		elDd.hidden = true;
	}
	elDt = null; elDd = null;

	// Function for showing RDS popup.
	var showRDSPopup = function( eventObject ) {

		if( !popupElement.hidden ) return; // Return, when mouse is over RDS cell.
		popupElement.hidden = false;

		// Put RDS data to popup.
		if( this.dataset.ps.trim() != '' ) {
			dlElement.children[0].hidden    = false;
			dlElement.children[1].hidden    = false;
			dlElement.children[1].innerHTML = '<span>' + this.dataset.ps.replace( /\|/g, '</span> <span>' ) + '</span>';
		}
		if( this.dataset.rt.trim() != '' ) {
			dlElement.children[2].hidden    = false;
			dlElement.children[3].hidden    = false;
			dlElement.children[3].innerHTML = '<div><span>' + this.dataset.rt.replace( /\|/g, '</span></div> <div><span>' ) + '</span></div>';
		}
		if( this.dataset.pty.trim() != '' ) {
			dlElement.children[4].hidden    = false;
			dlElement.children[5].hidden    = false;
			dlElement.children[5].innerHTML = '<span>' + this.dataset.pty + '</span>';
		}

		// Set left and top position when mouse is over RDS cell...
		if( eventObject.type == 'mousemove' ) {
			popupElement.style.left = (eventObject.clientX - popupElement.clientWidth - 40) + 'px';
			popupElement.style.top  = (eventObject.clientY - 20) + 'px';
			if( window.innerHeight-eventObject.clientY < popupElement.clientHeight )
				popupElement.style.top  = (eventObject.clientY - (popupElement.clientHeight - (window.innerHeight-eventObject.clientY)) - 10) + 'px'; // When popup is outside screen.
		}
		// ...or when user focus RDS cell.
		else {
			popupElement.style.left = (window.innerWidth/2  -  popupElement.clientWidth/2) + 'px'
			popupElement.style.top  = (window.innerHeight/2 - popupElement.clientHeight/2) + 'px';
		}
	}

	// Function for hiding RDS popup.
	var hideRDSPopup = function() {
		popupElement.hidden = true;
		for( key in dlElement.children )
			dlElement.children[key].hidden = true;
		this.blur();
	}

	// Attaching events to RDS PS cells.
	var spanLength = rdsPsSpans.length;
	for( var i=0; i<spanLength; i++ ) {
		rdsPsSpans[i].setAttribute( 'tabindex', '0' );  // User will be able to focus RDS cell.
		rdsPsSpans[i].addEventListener( 'mousemove',  showRDSPopup );
		rdsPsSpans[i].addEventListener( 'focus',      showRDSPopup );
		rdsPsSpans[i].addEventListener( 'mouseleave', hideRDSPopup );
		rdsPsSpans[i].addEventListener( 'blur',       hideRDSPopup );
	}

	// Prepare elements.
	popupElement.id     = 'rdsPopup_JSOn';
	popupElement.hidden = true;
	radioTableDiv.appendChild(popupElement);

}


// Radiotable sorting function.
function tableSorting() {

	// Get radiotable column headers and rows.
	var tableHeaders  = document.querySelectorAll('div.radioTable table thead th');
	var tableBody     = document.querySelector('div.radioTable table tbody');
	var tableRows     = Array.prototype.slice.call( tableBody.querySelectorAll('div.radioTable table tbody tr'), 0 );  // Convert NodeList to standard array to get possibility to sorting.

	// Variables with sorting order and last index of cell. It is needed for reverse sorting.
	var sortingOrder  = 1;
	var lastCellIndex = -1;

	// Special function for removing unwatnted chars from sorted strings.
	var prepareString = function( string ) {
		var map = {
			'Č': 'C',
			'Ö': 'O',
			'Ć': 'C',
			'Ł': 'L',
			'Ś': 'S',
			'Ż': 'Z',
			'Ź': 'Z'
		}
		return string.replace( RegExp(Object.keys(map).join('|')), function(el){ return map[el]; } );
	}

	// Sorting function.
	var sortColumn = function( eventObject ) {

		// Accessibility. Sort, when user press enter key only.
		if( eventObject.keyCode && eventObject.keyCode != 13 ) return;

		// Reverse sorting order when user clicked column header second time and save current column index.
		sortingOrder  = ( this.cellIndex == lastCellIndex ) ? sortingOrder*-1 : 1;
		lastCellIndex = this.cellIndex;

		// Sort elements of table rows array.
		tableRows.sort( function(a,b) {
			var valueA = a.children[this.cellIndex].textContent;
			var valueB = b.children[this.cellIndex].textContent;

			// Prepare cell value according to type (string or number).
			if( this.dataset.sort == 'N' ) {
				if( valueA != '' ) valueA = parseFloat( valueA.replace(',','.') );
				if( valueB != '' ) valueB = parseFloat( valueB.replace(',','.') );
			}
			else if( this.dataset.sort == 'T' ) {
				valueA = prepareString(valueA);
				valueB = prepareString(valueB);
			}

			// Use default row position given from browser, when values are equal.
			if( valueA === valueB ) return (a.rowIndexDefault - b.rowIndexDefault);

			// Always move rows with empty value to the end.
			if( valueA == '' ) return 1; if( valueB == '' ) return -1;

			return ( (valueA > valueB) ? 1 : -1 ) * sortingOrder;
		}.bind(this) );

		// Refresh table rows.
		tableRows.forEach( function(row){ tableBody.appendChild(row); } );

		// Refresh other table headers - remove CSS class and reset sorting order.
		for( var i=0; i<tableHeaders.length; i++ )
			tableHeaders[i].className = '';

		// Set CSS class to mark, that column is sorted.
		this.className = 'sorted' + ((sortingOrder==1) ? 'A' : 'Z');

	}

	// Set default index in rows. It will be needed, when sorting function encounter repeated values.
	tableRows.forEach( function(row){ row.rowIndexDefault = row.rowIndex; } );

	// Attach sorting action to columns headers; set order and index property.
	for( var i=0; i<tableHeaders.length; i++ ) {
		if( typeof(tableHeaders[i].dataset.sort) != 'undefined' ) {
			tableHeaders[i].addEventListener( 'click', sortColumn );
			if( navigator.appName != 'Opera' )
				tableHeaders[i].addEventListener( 'keydown', sortColumn ); // Version for keyboard users. Opera 12 handle key down by onclick event.
			tableHeaders[i].setAttribute( 'role', 'button' );  // Semantic role.
			tableHeaders[i].setAttribute( 'aria-label', 'Posortuj tabelę wg kolumny' );  // Semantic label for screen readers.
			tableHeaders[i].setAttribute( 'tabindex', '0' );  // User will be able to focus header.
		}
	}

}


// Automatic generator of indents in radiotable columns with numeric values.
function numberIndent() {

	var numericElements = document.querySelectorAll('div.radioTable span.ni');

	for( key in numericElements ) {

		if( isNaN(key) ) break;
		var value = parseInt(numericElements[key].innerHTML);

		if( numericElements[key].classList[1] == 'ni2' ) {
			if( value < 10 )
				numericElements[key].classList.add('niApply1');
		}
		else {
			if( value < 10 )
				numericElements[key].classList.add('niApply2');
			else if( value < 100 )
				numericElements[key].classList.add('niApply1');
		}

	}

}


// Radiotable scrolling CSS style.
function overflowStyle() {

	// Get radiotable container.
	var radioTableDiv     = document.querySelector('div.radioTable');
	var radioTableClasses = radioTableDiv.classList;

	// Function for refreshing CSS classes of radiotable container.
	var refreshClasses = function() {

		// Set CSS classes to initial.
		radioTableDiv.classList.remove('overflowLeft');
		radioTableDiv.classList.remove('overflowRight');

		// When horizontal scrollbar is visible.
		if( radioTableDiv.scrollWidth != radioTableDiv.clientWidth ) {
			if( radioTableDiv.scrollLeft > 3 )
				radioTableDiv.classList.add('overflowLeft');
			if( radioTableDiv.scrollWidth-radioTableDiv.clientWidth-radioTableDiv.scrollLeft > 3 )
				radioTableDiv.classList.add('overflowRight');
		}

	};

	// Attach function to events.
	window.addEventListener( 'load', refreshClasses );
	window.addEventListener( 'resize', refreshClasses );
	radioTableDiv.addEventListener( 'scroll', refreshClasses );

}


// Start these functions.
createRDSPopup();
tableSorting();
numberIndent();
overflowStyle();