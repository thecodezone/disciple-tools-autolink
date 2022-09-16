import { css, html, LitElement } from 'lit';

export class DtChevronLeft extends LitElement {
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
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M18.7 256l22.6 22.6 192 192L256 493.3 301.3 448l-22.6-22.6L109.3 256 278.6 86.6 301.3 64 256 18.7 233.4 41.4l-192 192L18.7 256z"/></svg>`;
  }
}

window.customElements.define('dt-chevron-left', DtChevronLeft);
