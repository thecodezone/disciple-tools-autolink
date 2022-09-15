import { css, html, LitElement } from 'lit';

export class AppMenu extends LitElement {
  static get styles() {
    return css`
     .menu__toggle {
      cursor: pointer;
     }
    .menu__collapse {
      position: fixed;
      top: 96px;
      left: 0;
      right: 0;
      background-color: var(--primary-color);
      z-index: 99999;
    }
    `;
  }
  static get properties() {
    return {
      show: { type: Boolean, attribute: false }
    };
  }
  render() {
    return html`
      <nav class="menu">
        <a @click=${() => this.toggle()} title="${app.translations.toggle_menu}">
          <dt-hamburger class="menu__toggle"></dt-hamburger>
          ${this.renderCollapse()}
        </a>
      </nav>
`;
  }

  renderCollapse() {
    if (!this.show) {
      return '';
    }

    return html`
      <div class="menu__collapse">
          <ul class="menu__list">
            <li class="menu__item">
                <a href="${app.urls.home}" class="menu__link" title="${app.translations.dt_nav_label}">${app.translations.dt_nav_label}</a>
            </li>
            <li class="menu__item">
              <a href="${app.urls.survey}" class="menu__link" title="${app.translations.survey_nav_label}">${app.translations.survey_nav_label}</a>
            </li>
            <li class="menu__item">
              <a href="${app.urls.logout}" class="menu__link menu__link--logout" title="${app.translations.logout_nav_label}">${app.translations.logout_nav_label}</a>
            </li>
          </ul>
        </div>
    `;
  }

  toggle() {
    this.show = !this.show;
  }
}

window.customElements.define('app-menu', AppMenu);
