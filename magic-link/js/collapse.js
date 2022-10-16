import { css, html, LitElement } from 'lit';
import { classMap } from 'lit/directives/class-map.js';
import { DTBase } from 'dt-web-components';

export class AppCollapse extends DTBase {
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
        `
    }

    render() {
        return html`
        <div class=${classMap({
            collapse: true,
            'collapse--opened': this.opened
        })}>
            ${this.renderIcon()}
            <div class="collapse__content">
                ${this.renderContent()}
            </div>
        </div>`;
    }

    renderIcon() {
        return html`
            <div class="collapse__icon" @click=${() => this._toggle()}>
                ${
                    this.opened 
                    ? html`<dt-chevron-up></dt-chevron-up>` 
                    : html`<dt-chevron-down></dt-chevron-down>`
                }
            </div>
        `
    }

    renderContent() {
        if (this.opened) {
            return html`<slot></slot>`
        }
        return html``
    }

    _toggle() {
        if (this.opened) {
            this._close();
        } else {
            this._open();
        }
    }

    _open() {
        this.opened = true
    }

    _close() {
        this.opened = false
    }

}

window.customElements.define('app-collapse', AppCollapse);
