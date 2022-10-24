import { css, html, LitElement } from 'lit';
import { DTBase } from 'dt-web-components';

export class AppMenu extends DTBase {
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
    }
    .menu__list a.menu__link {
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
      color:  var(--surface-1);
    }

    `;
  }
  static get properties() {
    return {
      show: { type: Boolean, attribute: false }
    };
  }

  get icon() {
    return this.show ? 'ic:sharp-close' : 'ic:sharp-menu';
  }

  render() {
    return html`
      <nav class="menu">
        <a @click=${() => this.toggle()} title="${app.translations.toggle_menu}">
          <dt-icon class="menu__toggle" icon="${this.icon}"></dt-icon>
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
