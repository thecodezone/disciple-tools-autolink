import { DtNumberField } from "@disciple.tools/web-components";

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
    return window.app.rest_base + window.magic.rest_namespace;
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
          "X-WP-Nonce": this.nonce,
        },
        body: JSON.stringify({
          id: this.id,
          value: e.target.value,
          action: "update_field",
          parts: window.magic.parts,
        }),
      };

      try {
        const response = await fetch(this.action, params);
        const body = await response.json();

        if (body.data && body.data.status && body.data.status !== 200) {
          this.handleError(body.message);
        } else if (body.success == false) {
          this.handleError(body.data.message);
        }
      } catch (error) {
        this.handleError(error);
      }
    } else {
      e.currentTarget.value = "";
    }
  }
}

window.customElements.define("app-church-health-field", ChurchHealthField);
