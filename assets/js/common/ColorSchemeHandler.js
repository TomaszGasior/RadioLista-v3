export class ColorSchemeHandler
{
    constructor(container)
    {
        this.settingName = 'rl-color-scheme-override';
        this.mediaRulesByColorScheme = [];

        this.collectMediaRules();
        this.restoreSavedOverridenColorScheme();

        container.querySelector('.color-scheme-switches').hidden = false;

        container.querySelectorAll('.color-scheme-switch').forEach(node => {
            node.addEventListener('click', this.onChangeButtonClick.bind(this));
        });
    }

    restoreSavedOverridenColorScheme()
    {
        let targetColorScheme = window.localStorage.getItem(this.settingName);

        if (null !== targetColorScheme) {
            this.disableAllTransitionsForMoment();
            this.overrideColorSchemeOnMediaRules(targetColorScheme);
        }
    }

    onChangeButtonClick(event)
    {
        let button = event.target;

        let targetColorScheme = button.dataset.colorScheme;
        let browserColorScheme = (window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';

        this.disableAllTransitionsForMoment();

        if (targetColorScheme === browserColorScheme) {
            this.unsetOverridenColorSchemeOnMediaRules();
            window.localStorage.removeItem(this.settingName);
        }
        else {
            this.overrideColorSchemeOnMediaRules(targetColorScheme);
            window.localStorage.setItem(this.settingName, targetColorScheme);
        }

        window.scrollTo(0, 0);
    }

    collectMediaRules()
    {
        let mediaRules = Array.from(document.styleSheets)
            .map(styleSheet => Array.from(styleSheet.cssRules))
            .flat()
            .filter(rule => rule instanceof CSSMediaRule)
        ;

        ['light', 'dark'].forEach(colorScheme => {
            this.mediaRulesByColorScheme[colorScheme] = mediaRules.filter(
                rule => rule.media.mediaText.includes('(prefers-color-scheme: ' + colorScheme + ')')
            );
        });
    }

    overrideColorSchemeOnMediaRules(targetColorScheme)
    {
        ['light', 'dark'].forEach(colorScheme => {
            this.mediaRulesByColorScheme[colorScheme].forEach(rule => {
                try {
                    rule.media.deleteMedium('(prefers-color-scheme: ' + colorScheme + ')');
                }
                catch (e) {}

                if (colorScheme !== targetColorScheme) {
                    rule.media.appendMedium('(width: 1px)');
                }
            });
        });
    }

    unsetOverridenColorSchemeOnMediaRules()
    {
        ['light', 'dark'].forEach(colorScheme => {
            this.mediaRulesByColorScheme[colorScheme].forEach(rule => {
                rule.media.appendMedium('(prefers-color-scheme: ' + colorScheme + ')');

                try {
                    rule.media.deleteMedium('(width: 1px)');
                }
                catch (e) {}
            });
        });
    }

    disableAllTransitionsForMoment()
    {
        let style = document.createElement('style');
        style.innerText = '* { transition: none !important; }';

        document.head.appendChild(style);
        window.setTimeout(() => { document.head.removeChild(style); }, 100);
    }
}
