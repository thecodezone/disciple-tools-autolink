import {html, css} from "lit";
import {DtTile} from "@disciple.tools/web-components";
import { customElement } from "lit/decorators.js";

@customElement("al-church-tile")
export class ChurchTile extends DtTile {
    constructor() {
        super();
    }

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
                margin: 0px 6% 15px;
              }

              section {
                position: relative;
              }

              dt-toggle {
                margin: 0 auto;
              }
            `,
        ];
    }

    static get properties() {
        return {
            ...super.properties,
        };
    }

    render() {
        return html`
            <section>
                ${this.renderHeading()}
                <div class="section-body ${this.collapsed ? "collapsed" : null}">
                    <slot></slot>
                </div>
            </section>
        `;
    }
}