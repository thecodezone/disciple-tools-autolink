import { css, html, LitElement } from "lit";
import { classMap } from "lit/directives/class-map.js";
import { DtBase } from "@disciple.tools/web-components";

export class AppCollapse extends DtBase {
  static get properties() {
    return {
      opened: { type: Boolean, reflect: true },
    };
  }

  static get styles() {
    return css`
      :host {
        color: currentcolor;
        display: block;
      }
    `;
  }

  get icon() {
    return this.opened ? "mdi:chevron-up" : "mdi:chevron-down";
  }

  render() {
    return html` <div
      class=${classMap({
        collapse: true,
        "collapse--opened": this.opened,
      })}
    >
      ${this.renderIcon()}
      <div class="collapse__content">${this.renderContent()}</div>
    </div>`;
  }

  renderIcon() {
    return html`
      <div class="collapse__icon" @click=${() => this._toggle()}>
        <dt-icon icon="${this.icon}"></dt-icon>
      </div>
    `;
  }

  renderContent() {
    if (this.opened) {
      return html`<slot></slot>`;
    }
    return html``;
  }

  _toggle() {
    if (this.opened) {
      this._close();
    } else {
      this._open();
    }
  }

  _open() {
    this.opened = true;
  }

  _close() {
    this.opened = false;
  }
}

window.customElements.define("app-collapse", AppCollapse);
