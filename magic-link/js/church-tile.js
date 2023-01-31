import { html, css } from 'lit';
import { DtTile } from "@disciple.tools/web-components";

export class ChurchTile extends DtTile {
  static get styles() {
    return [
      super.styles,
      css`
        .section-header {
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          text-align: center;
          display: block;
          margin: 0px 6%;
        }
      `,
    ]
  }

  static get properties() {
    return {
      ...super.properties,
    }
  }

  constructor() {
    super();
  }

  render() {
    return html`
    <section>
      ${this.renderHeading()}
      <div class="section-body ${this.collapsed ? 'collapsed' : null}">
        <slot></slot>
      </div>
    </section>
  `;
  }

}

window.customElements.define('church-tile', ChurchTile);
