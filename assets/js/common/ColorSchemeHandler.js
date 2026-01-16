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
        let findMediaRules = cssRules => {
            let result = [];

            Array.from(cssRules).forEach(rule => {
                if (rule instanceof CSSMediaRule) {
                    result.push(rule);
                }

                if (rule instanceof CSSImportRule) {
                    result = result.concat(findMediaRules(rule.styleSheet.cssRules));
                }
            });

            return result;
        };

        let mediaRules = findMediaRules(
            Array.from(document.styleSheets).flatMap(styleSheet => Array.from(styleSheet.cssRules))
        );

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
        let nodes = document.querySelectorAll('*');

        nodes.forEach(node => node.style.transition = 'none');
        window.setTimeout(() => { nodes.forEach(node => node.style.transition = ''); }, 100);
    }
}
