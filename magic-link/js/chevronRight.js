import { css, html, LitElement } from 'lit';

export class DtChevronRight extends LitElement {
  static get styles() {
    return css`
     :root {
        font-size: inherit;
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
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M365.3 256l-22.6 22.6-192 192L128 493.3 82.7 448l22.6-22.6L274.7 256 105.4 86.6 82.7 64 128 18.7l22.6 22.6 192 192L365.3 256z"/></svg>
`;
  }
}

window.customElements.define('dt-chevron-right', DtChevronRight);
