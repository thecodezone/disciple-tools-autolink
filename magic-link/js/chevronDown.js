import { css, html, LitElement } from 'lit';

export class DtChevronDown extends LitElement {
  static get styles() {
    return css`
      :root {
        font-size: inherit;
        color: inherit;
      }
      svg {
        width: 1em;
        height: auto;
      }
      svg use {
        fill: currentcolor;
      }
    `;
  }
  render() {
    return html`
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 429.3l22.6-22.6 192-192L493.3 192 448 146.7l-22.6 22.6L256 338.7 86.6 169.4 64 146.7 18.7 192l22.6 22.6 192 192L256 429.3z"/></svg>`;
  }
}

window.customElements.define('dt-chevron-down', DtChevronDown);