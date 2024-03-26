import { DtNumberField } from "@disciple.tools/web-components";
import { customElement } from "lit/decorators.js";
import { api_url } from "../_helpers.js";

@customElement("al-church-health-field")
export class ChurchHealthField extends DtNumberField {
  static get properties() {
    return {
      ...super.properties,
    };
  }

  get value() {
    return this._value === "0" ? "" : this._value;
  }

  set value(value) {
    this._value = value ? value : "0";
  }

  get action() {
    return api_url("field");
  }

  async onChange(e) {
    if (this._checkValue(e.target.value)) {
      const event = new CustomEvent("change", {
        detail: {
          field: this.name,
          oldValue: this.value,
          newValue: e.target.value,
        },
        bubbles: true,
        composed: true,
      });

      this.value = e.target.value;
      this._field.setCustomValidity("");
      this.dispatchEvent(event);

      const params = {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": $autolink.nonce,
        },
        body: JSON.stringify({
          id: this.id,
          value: e.target.value,
        }),
      };

      try {
        const response = await fetch(this.action, params);
        const body = await response.json();

        if (response.status !== 200) {
          alert(body.message);
          this.handleError(body.message);
        }

      } catch (error) {
        this.handleError(error);
      }
    } else {
      e.currentTarget.value = "";
    }
  }
}