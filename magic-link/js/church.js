import { DtBase } from "@disciple.tools/web-components";
import { css, html, nothing } from "lit";
import { classMap } from "lit/directives/class-map.js";
import { AppCollapse } from "./collapse";

export class AppChurch extends AppCollapse {
  static get properties() {
    return {
      group: { type: Object },
      fields: { type: Object },
      opened: { type: Boolean, reflect: true },
      startDateLabel: { type: String },
    };
  }

  constructor() {
    super();
  }

  static get styles() {
    return css`
      :host {
        color: currentcolor;
        display: block;
      }

      .church_health {
        text-align: center;
      }

      .collapse__icon {
        font-size: 2rem;
        display: flex;
        justify-content: center;
        padding-top: 1rem;
      }

      .group__content {
        font-weight: normal;
        text-align: center;
        text-transform: uppercase;
      }
    `;
  }

  render() {
    return html`
      <div class="group">${this.renderContent()} ${this.renderIcon()}</div>
    `;
  }

  renderContent() {
    const { startDateLabel } = this;
    const startDate = this.group.start_date?.formatted;

    if (this.opened) {
      return html`
        ${this.renderChurchHealth()}
        ${startDate
          ? html`<div class="group__content">
              ${startDateLabel ? startDateLabel : "Church start date"} :
              ${this.group.start_date?.formatted}
            </div>`
          : nothing}
      `;
    }
    return nothing;
  }

  renderChurchHealth() {
    return html`
      <div class="church_health">
        <dt-church-health-circle
          .group=${this.group}
          .settings=${this.fields.health_metrics}
        ></dt-church-health-circle>
      </div>
    `;
  }
}

window.customElements.define("app-church", AppChurch);
