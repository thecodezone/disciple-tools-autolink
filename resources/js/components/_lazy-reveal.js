import { css, html, LitElement, nothing } from "lit";
import { DtBase } from "@disciple.tools/web-components";
import { styleMap } from "lit/directives/style-map.js";
import { customElement, property } from "lit/decorators.js";

@customElement("al-lazy-reveal")
export class LazyReveal extends DtBase {
  @property({ type: Number }) perPage = 7;
  @property({ type: Boolean }) visible = true;
  @property({ type: Number }) visibleCount = this.perPage;
  @property({ type: Boolean }) showButtons = false;
  @property({ type: Number }) count = 0;
  @property({ type: Array }) items = [];

  static get styles() {
    return [
      css`
        :host {
          display: block;
        }
        .lazy-reveal--visible {
          display: block;
        }
        .lazy-reveal__controls {
          display: flex;
          justify-content: center;
          gap: 0.5rem;
        }
      `,
    ];
  }

  get children() {
    const slot = this.shadowRoot.querySelector("slot");
    if (!slot) return [];
    return slot.assignedElements({ flatten: true });
  }

  get visibleItems() {
    return Array.from(this.children).slice(0, this.visibleCount);
  }

  constructor() {
    super();
    this.visible = true;
    this.perPage = 7;
    this.visibleCount = this.perPage;
    this.type = 0;
    this.items = [];
    this.showButtons = false;
  }

  firstUpdated() {
    this.count = this.children.length;
  }

  updated() {
    this.count = this.children.length;

    let i = 0;
    this.children.forEach((child) => {
      i++;
      child.style.display = i <= this.visibleCount ? "block" : "none";
    });
  }

  render() {
    return html`
      <div class="lazy-reveal">
        <div class="lazy-reveal__items">
          <slot></slot>
        </div>
        ${this.renderControls()}
      </div>
    `;
  }

  renderControls() {
    if (this.visibleCount >= this.count) return nothing;

    return html`
      <div class="lazy-reveal__controls">
        <dt-button @click=${this.addPage}> More </dt-button>
        <dt-button @click=${this.addAll}> All </dt-button>
      </div>
    `;
  }

  addPage() {
    this.visibleCount += this.perPage;
  }

  addAll() {
    this.visibleCount = this.count;
  }
}