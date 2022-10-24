import { css, html } from 'lit';
import { DTBase } from 'dt-web-components';
import 'iconify-icon';

export class DtIcon extends DTBase {
    static get styles() {
        return css`
            :root {
                font-size: inherit;
                color: inherit;
            }
        `;
    }

    static properties = {
        icon: { type: String }
    }

    render() {
        return html`
         <iconify-icon icon=${this.icon}></iconify-icon>
    `;
    }
}

window.customElements.define('dt-icon', DtIcon);