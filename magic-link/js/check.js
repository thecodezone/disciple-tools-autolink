import { css, html, LitElement } from 'lit';

export class DtCheckMark extends LitElement {
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
      svg path {
        fill: currentcolor;
      }
    `;
  }
  render() {
    return html`
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>`
  }
}

window.customElements.define('dt-check', DtCheckMark);