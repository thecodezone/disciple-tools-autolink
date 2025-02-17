import {css, html, LitElement, nothing} from "lit";
import {DtBase} from "@disciple.tools/web-components";
import { customElement, property } from "lit/decorators.js";
import {api_url} from "../_helpers.js";

@customElement("al-menu")
export class AppMenu extends DtBase {
    @property({type: Boolean, attribute: false}) show = false;
    @property({type: Array}) languages = [];
    static get styles() {
        return css`
          .menu__toggle {
            cursor: pointer;
            font-size: 2.5rem;
          }

          .menu__collapse {
            position: absolute;
            top: 100px;
            left: 0;
            right: 0;
            background-color: var(--primary-color);
            z-index: 99999;
          }

          .menu__list {
            margin: 50px 25px;
            padding: 0;
          }

          .menu__list .menu__item {
            list-style: none;
            margin: 10px 0;
          }

          .menu__list a.menu__link,
          .menu__item select {
            text-decoration: none;
            color: var(--surface-1);
            font-weight: 700;
            font-size: 14px;
            border: 1px solid var(--surface-1);
            border-radius: 4px;
            padding: 10px 20px;
            text-align: center;
            margin: 10px auto;
            display: block;
            max-width: 342px;
            width: 100%; /* Ensure full width */
            box-sizing: border-box; /* Include padding and border in width calculation */
            background-color: var(--primary-color);
          }

          .menu__item select option {
            background-color: var(--primary-color);
            color: var(--surface-1);
          }


          .menu__list a.menu__link:hover {
            background-color: var(--surface-1);
            color: var(--primary-color);
          }

          .menu__list a.menu__link.menu__link--logout {
            background-color: var(--surface-1);
            color: var(--primary-color);
          }

          .menu__list a.menu__link.menu__link--logout:hover {
            background-color: var(--primary-color);
            color: var(--surface-1);
          }

          .menu__backdrop {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
          }

          .menu__item select option {
            background-color: var(--primary-color);
            color: var(--surface-1);
          }
        `;
    }
    connectedCallback() {
      super.connectedCallback();
      const dataLang = this.getAttribute('data-lang');
      if (dataLang) {
         this.languages = JSON.parse(dataLang);
      }
    }
    /**
     * The icon code.
     * @see https://iconify.design/ for more icons
     */
    get icon() {
        return this.show ? "ic:sharp-close" : "ic:sharp-menu";
    }

    /**
     * Render the component
     */
    render() {
        return html`
            <nav class="menu">
                <a
                        @click=${() => this.toggle()}
                        title="${$autolink.translations.toggle_menu}"
                >
                    <dt-icon class="menu__toggle" icon="${this.icon}"></dt-icon>
                </a>
                ${this.renderCollapse()}
            </nav>
            ${this.renderBackdrop()}
        `;
    }

    /**
     * Render the backdrop. If clicked, it closes the menu.
     */
    renderBackdrop() {
        if (!this.show) {
            return nothing;
        }

        return html`
            <div class="menu__backdrop" @click=${this.handleBackdropClick}></div>
        `;
    }

    handleLocaleChange(event) {
      const selectedLocale = event.target.value;
      fetch( api_url("language"), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': $autolink.nonce // Assuming you have a nonce for security
        },
        body: JSON.stringify({ dt_autolink_locale: selectedLocale })
      })
      .then(response => response.json())
      .then(data => {
        console.log('Locale switched:', data);
      })
      .catch(error => {
        console.error('Error switching locale:', error);
      });
    }

    /**
     * Make sure the backdrop is the specific element clicked
     */
    handleBackdropClick(e) {
        if (e.target === e.currentTarget) {
            this.close();
        }
    }

    /**
     * Render the collapsable part of the menu
     */
    renderCollapse() {
        if (!this.show) {
            return "";
        }

        return html`
            <div class="menu__collapse">
                <ul class="menu__list">
                     <li class="menu__item">
                       <select class="menu__link" name="dt-autolink-locale" @change=${this.handleLocaleChange}>
                         ${this.languages.map(language => html`
                           <option value="${language.name.language}" ?selected=${language.selected}>
                             ${language.name.flag ? language.name.flag + ' ' : ''}${language.name.native_name}
                           </option>
                         `)}
                       </select>
                     </li>
                    <li class="menu__item">
                        <a
                                href="${$autolink.urls.home}"
                                class="menu__link"
                                title="${$autolink.translations.dt_nav_label}"
                        >${$autolink.translations.dt_nav_label}</a
                        >
                    </li>
                    <li class="menu__item">
                        <a
                                href="${$autolink.urls.survey}"
                                class="menu__link"
                                title="${$autolink.translations.survey_nav_label}"
                        >${$autolink.translations.survey_nav_label}</a
                        >
                    </li>
                    <li class="menu__item">
                        <a
                                href="${$autolink.urls.training}"
                                class="menu__link"
                                title="${$autolink.translations.training_nav_label}"
                        >${$autolink.translations.training_nav_label}</a
                        >
                    <li class="menu__item">
                        <a
                                href="${$autolink.urls.logout}"
                                class="menu__link menu__link--logout"
                                title="${$autolink.translations.logout_nav_label}"
                        >${$autolink.translations.logout_nav_label}</a
                        >
                    </li>
                </ul>
            </div>
        `;
    }

    /**
     * Toggle the menu open or closed
     */
    toggle() {
        if (this.show) {
            this.close();
        } else {
            this.open();
        }
    }

    /**
     * Close the menu
     */
    close() {
        this.show = false;
    }

    /**
     * Open the menu
     */
    open() {
        this.show = true;
    }
}
