
// Tabbed interface for forms.
function useTabbedUI () {

    // Don't load in IE10 and older.
    if( typeof(document.documentMode) !== 'undefined' && document.documentMode < 11 ) return;

    // Get tabs container and each tab box.
    var tabsContainer = document.getElementById('useTabbedUI');
    var tabsBoxes     = tabsContainer.querySelectorAll('#useTabbedUI > div');
    var count         = tabsBoxes.length;

    // Get tabs titles and check, which tab is selected as default by CSS class.
    var currentTab = null;
    var tabsTitles = [];
    for( var i=0; i<count; i++ ) {
        tabsTitles[i] = tabsBoxes[i].getElementsByTagName('h3')[0].innerHTML;
        tabsBoxes[i].setAttribute( 'role', 'tabpanel' ); // Semantic role.

        if( tabsBoxes[i].className == 'currentTab' )
            currentTab = i;
    }

    // Change ID of tabs container to mark, that JS is enabled.
    tabsContainer.id = 'useTabbedUI_JSOn';

    // Function, that changes current tab.
    var changeCurrentTabNow = function () {
        tabsBoxes[currentTab].className = '';
        tabNavigatorButtons[currentTab].className = '';

        currentTab = this.dataset.tabNumber;
        this.blur();  // Unfocus tab navigator button.

        tabsBoxes[currentTab].className = 'currentTab';
        tabNavigatorButtons[currentTab].className = 'currentTabNavigator';

        sessionStorage.setItem( 'defaultTab_'+location.href, currentTab ); // Save tab number in sessionStorage.
    };

    // Create tabs navigator and add to document before first tab box.
    var tabsNavigator = document.createElement('ul');
    tabsNavigator.className = 'tabsNavigator';
    tabsNavigator.setAttribute( 'role', 'tablist' ); // Semantic role.
    tabsContainer.insertBefore( tabsNavigator, tabsBoxes[0] );

    // Create tabs navigation elements.
    var tabNavigatorButtons = [];
    for( var i=0; i<count; i++ ) {
        newTabNavigatorElement  = document.createElement('li');
        tabNavigatorButtons[i]  = document.createElement('button');

        tabNavigatorButtons[i].type              = 'button';
        tabNavigatorButtons[i].dataset.tabNumber = i;
        tabNavigatorButtons[i].innerHTML         = tabsTitles[i];
        tabNavigatorButtons[i].addEventListener( 'click', changeCurrentTabNow );
        tabNavigatorButtons[i].setAttribute( 'role', 'tab' );  // Semantic role.

        tabsNavigator.appendChild( newTabNavigatorElement );
        newTabNavigatorElement.appendChild( tabNavigatorButtons[i] );
    }

    // Get selected tab id from URL address or from sessionStorage, if it is not specified by CSS class.
    if( currentTab == null ) {

        if( location.search.indexOf('tab') > 0 )
            currentTab = location.search.charAt( location.search.indexOf('tab')+4 );
        else if( sessionStorage.getItem('defaultTab_'+location.href) != null )
            currentTab = sessionStorage.getItem( 'defaultTab_' + location.href );
        else
            currentTab = 0;

        if( isNaN(currentTab) || currentTab >= count )
            currentTab = 0;

    }

    // Apply needed classes for elements.
    tabsBoxes[currentTab].className = 'currentTab';
    tabNavigatorButtons[currentTab].className = 'currentTabNavigator';

}


// Fake checkboxes for free styling by CSS.
function createFakeCheckboxes () {

    // Get all inputs.
    var inputsCheckboxes = document.querySelectorAll('input[type="checkbox"], input[type="radio"]');

    // Add fake elements to document and hide real checkboxes.
    var fakeCheckboxes = [];
    var fakeMarkers    = [];
    for( var i=0; i<inputsCheckboxes.length; i++ ) {
        inputsCheckboxes[i].className = 'hiddenCheckbox_JSOn';

        fakeCheckboxes[i] = document.createElement('div');
        fakeCheckboxes[i].className = 'fakeCheckbox';
        inputsCheckboxes[i].parentNode.appendChild( fakeCheckboxes[i] );

        fakeMarkers[i] = document.createElement('span');
        fakeMarkers[i].innerHTML = ( inputsCheckboxes[i].getAttribute('type')=='radio' ) ? 'Pole opcji' : 'Pole wyboru';   // Hidden content for speech readers.
        fakeCheckboxes[i].appendChild( fakeMarkers[i] );
    }

}


// Start these functions.
useTabbedUI();
createFakeCheckboxes();
