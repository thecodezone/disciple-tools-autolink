import { css, html, nothing } from "lit";
import { Collapse } from "./_collapse.js";
import { customElement } from "lit/decorators.js";

@customElement("al-church-menu")
export class ChurchMenu extends Collapse {
  constructor() {
    super();
    this.openIcon = "mdi:cog-outline";
    this.closeIcon = "mdi:cog";
  }

  static get properties() {
    return {
      opened: { type: Boolean, reflect: true },
      openIcon: { type: String },
      closeIcon: { type: String },
    };
  }

  static get styles() {
    return css`
      :host {
        color: currentcolor;
        display: block;
      }

      .collapse__icon {
        font-size: 1.2rem;
        display: flex;
        justify-content: center;
        position: absolute;
        top: 6px;
        right: 8px;
        z-index: 3;
        color: var(--primary-color);
      }

      .church__menu {
        position: absolute;
        top: 34px;
        right: 8px;
        z-index: 1;
        min-width: 200px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        align-items: end;
      }

      .menu__overlay {
        position: absolute;
        background-color: white;
        opacity: 0.7;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
      }

      @media print {
        .collapse__icon {
          display: none !important; /* Hide the icon when printing */
        }
      }
    `;
  }

  connectedCallback() {
    super.connectedCallback();
    this._printMediaQuery.removeListener(this._handlePrintChange);
  }

  _handlePrintChange(e) {
    // Override parent logic: ChurchMenu should NOT expand when printing
  }

  render() {
    return html`
      <div class="menu__wrapper">
        <div class="church__menu">${this.renderContent()}</div>
        ${this.renderIcon()} ${this.renderOverlay()}
      </div>
    `;
  }

  renderOverlay() {
    if (this.opened) {
      return html`
        <div class="menu__overlay" @click=${() => this._close()}></div>
      `;
    }
  }

  renderContent() {
    if (this.opened) {
      return html` <slot></slot> `;
    }
    return nothing;
  }
}
