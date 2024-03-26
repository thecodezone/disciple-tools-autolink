import { css, html, nothing } from "lit";
import { Collapse } from "./_collapse.js";
import { customElement } from "lit/decorators.js";
import { api_url } from "../_helpers.js";

@customElement("al-church")
export class Church extends Collapse {
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

  async handleSave(group_id, { health_metrics }) {
    const params = {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": $autolink.nonce,
      },
      body: JSON.stringify({
        id: `groups_${group_id}_health_metrics`,
        value: health_metrics
      }),
    };

    const response = await fetch(
      api_url("field"),
      params
    );
    const body = await response.json();

    if (response.status !== 200) {
      throw new Error(body.message);
    }

    return body;
  }

  renderChurchHealth() {
    return html`
      <div class="church_health">
        <dt-church-health-circle
          .group=${this.group}
          .settings=${this.fields.health_metrics}
          .handleSave=${this.handleSave.bind(this)}
        ></dt-church-health-circle>
      </div>
    `;
  }
}