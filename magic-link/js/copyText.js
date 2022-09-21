import {css, html, LitElement} from 'lit';
import {DtTextField} from 'dt-web-components';
import {styleMap} from 'lit-html/directives/style-map.js';

class DTCopyTextinput extends DtTextField {
  static get styles() {
    return [
      ...DtTextField.styles,
      css`
        .text-input {
           padding-right: 40px;
        }

         .text-input:disabled {
            cursor: text;
        }
    `];
  }
}

window.customElements.define('dt-copy-text-input', DTCopyTextinput);

export class DTCopyText extends LitElement {
  static get styles() {
    return css`
      :root {
        font-size: inherit;
      }

      .copy-text {
         display: flex;
         align-items: center;
         position: relative;
         color: var(--copy-text-color, var(--form-text-color));
      }

      .copy-text__input {
        flex: 1;
      }

      .copy_icon {
        position: absolute;
        cursor: copy;
        top: 50%;
        right: 10px;
        font-size: 16px;
        display: block;
        transform: translateY(calc(-50% - 5px));
        width: 20px;
      }
    `;
  }

  static get properties() {
    return {
      value: {type: String},
      success: {type: Boolean},
      error: {type: Boolean},
    };
  }

  get inputStyles() {
    if (this.success) {
      return {
        '--dt-text-border-color': 'var(--copy-text-success-color, var(--success-color))',
        '--dt-form-text-color': 'var( --copy-text-success-color, var(--success-color))',
        color:  'var( --copy-text-success-color, var(--success-color))',
      }
    } else if (this.error) {
      return {
        '---dt-text-border-color': 'var(--copy-text-alert-color, var(--alert-color))',
        '--dt-form-text-color': 'var(--copy-text-alert-color, var(--alert-color))',
      }
    }

    return {}
  }

  async copy() {
    try {
      this.success = false
      this.error = false
      await navigator.clipboard.writeText(this.value);
      console.log('here')
      this.success = true
      this.error = false
    } catch (err) {
      console.log(err)
      this.success = false
      this.error = true
    }
  }

  render() {
    return html`
      <div class="copy-text" style=${styleMap(this.inputStyles)}>
        <dt-copy-text-input class="copy-text__input"
                 value="${this.value}"
                 disabled></dt-copy-text-input>
        <div class="copy_icon"
             @click=${() => this.copy()}>
          ${this.success ? html`<dt-check/>` :  html`<dt-copy/>`}
        </div>
      </div>
    `
  }
}

window.customElements.define('dt-copy-text', DTCopyText);