import { css, html } from "lit";
import { classMap } from "lit/directives/class-map.js";
import { DtBase } from "@disciple.tools/web-components";
import { customElement, property } from "lit/decorators.js";

@customElement("al-collapse")
export class Collapse extends DtBase {
  @property({ type: Boolean, reflect: true }) opened = false;
  @property({ type: String }) openIcon = "mdi:chevron-up";
  @property({ type: String }) closeIcon = "mdi:chevron-down";

  constructor() {
    super();
    this._handlePrintChange = this._handlePrintChange.bind(this);
  }

  static get styles() {
    return css`
      :host {
        color: currentcolor;
        display: block;
      }

      .collapse__content {
        display: none;
      }

      .collapse--opened .collapse__content {
        display: block;
      }

      @media print {
        .collapse__icon {
          display: none !important; /* Hide the toggle button when printing */
        }
      }
    `;
  }

  get icon() {
    return this.opened ? this.openIcon : this.closeIcon;
  }

  connectedCallback() {
    super.connectedCallback();
    this._printMediaQuery = window.matchMedia("print");
    this._printMediaQuery.addListener(this._handlePrintChange);
  }

  disconnectedCallback() {
    super.disconnectedCallback();
    this._printMediaQuery.removeListener(this._handlePrintChange);
  }

  updated(changedProperties) {
    if (changedProperties.has("opened")) {
      this.requestUpdate();
    }
  }

  _handlePrintChange(e) {
    if (e.matches) {
      this.opened = true; // Expand the content when printing
    }
  }

  render() {
    return html`
      <div
        class=${classMap({
          collapse: true,
          "collapse--opened": this.opened,
        })}
      >
        ${this.renderIcon()}
        <div class="collapse__content">
          <slot></slot>
        </div>
      </div>
    `;
  }

  renderIcon() {
    return html`
      <div class="collapse__icon" @click=${() => this._toggle()}>
        <dt-icon icon="${this.icon}"></dt-icon>
      </div>
    `;
  }

  _toggle() {
    this.opened = !this.opened;
  }
}
