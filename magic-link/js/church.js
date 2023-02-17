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
      isChurch: { type: Boolean },
    };
  }

  constructor() {
    super();
  }

  connectedCallback() {
    super.connectedCallback();
    if (Object.hasOwn(this.group, "health_metrics")) {
      if (this.group.health_metrics.includes("church_commitment")) {
        return (this.isChurch = true);
      }
      return (this.isChurch = false);
    } else {
      return (this.isChurch = false);
    }
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

  toggleClick(e) {
    let toggle = this.renderRoot.querySelector("dt-toggle");
    let church_commitment = toggle.toggleAttribute("checked");
    const payload = {
      health_metrics: {
        values: [
          {
            value: "church_commitment",
            delete: !church_commitment,
          },
        ],
      },
    };
    try {
      API.update_post("groups", this.group.ID, payload);
      if (church_commitment) {
        this.group.health_metrics.push("church_commitment");
      } else {
        this.group.health_metrics.pop("church_commitment");
      }
    } catch (err) {
      console.log(err);
    }
  }

  _isChecked() {
    if (Object.hasOwn(this.group, "health_metrics")) {
      if (this.group.health_metrics.includes("church_commitment")) {
        return (this.isChurch = true);
      }
      return (this.isChurch = false);
    }
    return (this.isChurch = false);
  }

  renderChurchHealth() {
    return html`
      <div class="church_health">
        <dt-church-health-circle
          .group=${this.group}
          .settings=${this.fields.health_metrics}
        ></dt-church-health-circle>

        <dt-toggle
          name="church-commitment"
          label="Church Commitment"
          requiredmessage=""
          icon="https://cdn-icons-png.flaticon.com/512/1077/1077114.png"
          iconalttext="Icon Alt Text"
          privatelabel=""
          @click="${this.toggleClick}"
          ?checked=${this.isChurch}
        >
        </dt-toggle>
      </div>
    `;
  }
}

window.customElements.define("app-church", AppChurch);
