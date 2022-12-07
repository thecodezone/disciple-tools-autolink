import { css, html } from "lit";
import { DtBase } from "@disciple.tools/web-components";
import "iconify-icon";

export class DtIcon extends DtBase {
  static get styles() {
    return css`
      :root {
        font-size: inherit;
        color: inherit;
      }
    `;
  }

  static properties = {
    icon: { type: String },
  };

  render() {
    return html` <iconify-icon icon=${this.icon}></iconify-icon> `;
  }
}

window.customElements.define("dt-icon", DtIcon);
