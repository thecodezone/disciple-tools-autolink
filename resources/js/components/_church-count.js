import {css, html, LitElement} from "lit";
import {customElement, property} from "lit/decorators.js";

@customElement('al-church-counts')
class ChurchCounts extends LitElement {
  static styles = css`
    .count__value {
      transform: translateY(-3px);
      background-color: var(--alert-color);
      border-radius: 50%;
      aspect-ratio: 1 / 1;
      display: block;
      width: 1.5rem;
      color: white;
      font-family: var(--font-family);
    }

    .church__counts {
      display: flex;
      justify-content: center;
      font-family: var(--font-family);
    }
  `;

  @property({type: Object}) countFields = {};
  @property({type: Object}) group = {};

  render() {

    const {countFields, group} = this;
    if (!Object.values(countFields).length) {
      return null;
    }

    return html`
      <div class="church__counts">
        ${Object.entries(countFields).map(([key, field]) =>
      this.renderCount(key, field, group[key] ?? 0)
    )}
      </div>
    `;
  }

  renderCount(key, field, value) {
    return html`
      <div class="church__count"
           data-churchId="${this.group.ID}"
           data-field="${key}"
           key="church-${this.group.ID}-${key}"
      >
        <dt-modal context="default" hideHeader>
          <div slot="openButton">
            <img class="count__icon"
                 src="${field.icon}"
                 alt="${field.name}"
                 width="25"
                 height="25">
            <span class="count__value">${value}</span>
          </div>
          <div slot="content">
            <al-church-health-field
              id="groups_${this.group.ID}_${key}"
              name="${key}"
              icon="${field.icon}"
              label="${field.name}"
              @input="${this.handleInput}"
              @change="${this.handleFieldChange}"
              value="${value}"
              postType="groups"
              .postId="${this.group.ID}"
              min="0"
              placeholder="0"
            ></al-church-health-field>
          </div>
        </dt-modal>
      </div>`;
  }

  handleInput(event) {
    const {postId, name} = event.target;
    const updatedValue = event.target.value;
    const counter = this.shadowRoot.querySelector(
      `[data-churchId="${postId}"][data-field="${name}"]`
    );

    if (!counter) {
      return;
    }

    const numberBadge = counter.querySelector(".count__value");

    if (!numberBadge) {
      return;
    }

    numberBadge.textContent = updatedValue;
  }

  handleFieldChange(event) {
    const {postId, name} = event.target;
    const updatedValue = event.target.value;
    const counter = this.shadowRoot.querySelector(
      `[data-churchId="${postId}"][data-field="${name}"] .count__value`
    );

    if (counter) {
      counter.textContent = updatedValue;
    }
  }
}
