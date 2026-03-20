export default function SelectComponent() {
    const CONFIG = {
        DEFAULTS: {
            VALUE: ''
        },
        TRANSITIONS: {
            leave: 'transition ease-in duration-100'
        }
    };

    return {
        state: {
            options: [],
            value: CONFIG.DEFAULTS.VALUE,
            name: '',
            open: false,
            focusedOptionIndex: null
        },

        init(config = {}) {
            // Transformer les langues en options si elles existent
            if (config.languages) {
                this.state.options = Object.entries(config.languages).map(([, lang]) => ({
                    value: lang.language_code,
                    label: lang.native_name,
                    flag_url: lang.country_flag_url
                }));
            } else {
                this.state.options = [];
            }

            this.state.name = config.group ? `${config.group}[${config.name}]` : '';
            this.state.value = config.value || CONFIG.DEFAULTS.VALUE;

            // Vérifier la valeur initiale
            if (!this.state.options.find(opt => opt.value === this.state.value)) {
                this.state.value = this.state.options[0]?.value || CONFIG.DEFAULTS.VALUE;
            }

            this.$nextTick(() => this.dispatchLanguageEvent());
        },

        // Getters
        get selectedOption() {
            return this.state.options.find(opt => opt.value === this.state.value)
                || this.state.options[0];
        },

        getSelectedLabel() {
            const option = this.selectedOption;
            if (!option) {return '';}

            return `<img src="${option.flag_url}" alt="${option.label}" class="inline-block w-5 h-4 rounded" />${option.label}`;
        },

        // Actions
        closeListbox() {
            this.state.open = false;
            this.state.focusedOptionIndex = null;
        },

        focusNextOption() {
            if (this.state.focusedOptionIndex === null) {
                this.state.focusedOptionIndex = 0;
                return;
            }

            if (this.state.focusedOptionIndex + 1 >= this.state.options.length) {return;}

            this.state.focusedOptionIndex++;
            this.scrollOptionIntoView();
        },

        focusPreviousOption() {
            if (this.state.focusedOptionIndex === null) {
                this.state.focusedOptionIndex = this.state.options.length - 1;
                return;
            }

            if (this.state.focusedOptionIndex <= 0) {return;}

            this.state.focusedOptionIndex--;
            this.scrollOptionIntoView();
        },

        selectOption(index) {
            if (index === undefined || index === null) {return;}
            const previousValue = this.state.value;
            this.state.value = this.state.options[index].value;

            if (previousValue !== this.state.value) {
                this.dispatchLanguageEvent();
            }

            this.closeListbox();
        },

        dispatchLanguageEvent() {
            if (!this.state.value) {return;}

            const selectedOption = this.selectedOption;

            window.dispatchEvent(new CustomEvent('language-changed', {
                detail: {
                    value: this.state.value,
                    language: this.state.value,
                    label: selectedOption?.label || ''
                }
            }));

            document.dispatchEvent(new CustomEvent('language-changed', {
                detail: {
                    value: this.state.value,
                    language: this.state.value,
                    label: selectedOption?.label || ''
                },
                bubbles: true
            }));

            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('lang', this.state.value);
            window.history.replaceState({}, '', currentUrl.toString());
        },

        toggleListbox() {
            if (this.state.open) {
                return this.closeListbox();
            }

            this.state.open = true;
            this.state.focusedOptionIndex = this.state.options.findIndex(
                opt => opt.value === this.state.value
            );

            if (this.state.focusedOptionIndex < 0) {
                this.state.focusedOptionIndex = 0;
            }

            this.$nextTick(() => this.scrollOptionIntoView());
        },

        // Helpers
        scrollOptionIntoView() {
            this.$refs.listbox?.children[this.state.focusedOptionIndex]?.scrollIntoView({
                block: 'nearest'
            });
        }
    };
}
