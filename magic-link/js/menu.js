import { css, html, LitElement } from 'lit';

export class AppMenu extends LitElement {
  static get styles() {
    return css`
     .menu__toggle {
      cursor: pointer;
     }
    .menu__collapse {
      position: fixed;
      top: 87px;
      left: 0;
      right: 0;
      background-color: #2C5364;
      z-index: 99999;
    }
    
    .menu__list {
      margin: 50px 25px;
      padding: 0;
    }

    .menu__list .menu__item {
      /* border: 1px solid #fff;
      border-radius: 4px;
      padding: 10px 20px;
      text-align: center;
      margin: 10px; */
      list-style: none;
    }

    .menu__list a.menu__link {
      text-decoration: none;
      color: #fff;
      font-weight: 700;
      font-size: 14px;

      border: 1px solid #fff;
      border-radius: 4px;
      padding: 10px 20px;
      text-align: center;
      margin: 10px;
      display: block;
    } 
    .menu__list a.menu__link.menu__link--logout {
      background-color: #fff;
      color: #2C5364;

     
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
