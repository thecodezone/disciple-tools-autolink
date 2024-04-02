import { DtBase } from "@disciple.tools/web-components";
import {customElement, query, property, state} from "lit/decorators.js";
import {html, nothing, css} from "lit";
import {classMap} from "lit/directives/class-map.js";
import {unsafeHTML} from 'lit/directives/unsafe-html.js';
import {form_data_to_object} from "../_helpers.js";

@customElement( "al-ajax-form" )
export class AjaxForm extends DtBase {
  @property( { type: String } ) callback = "";
  @property( { type: String } ) content = "";

  @query( "form" ) form;

  @state() loading = false;
  @state() success = false;
  @state() submitting = false;
  @state() error = false;

  submitted = () => {};

  get action() {
    return this.form.action;
  }

  get formData() {
    return new FormData(this.form);
  }

  get data() {
    return form_data_to_object(this.formData)
  }

  constructor() {
    super();

    this.submitted = (e) => {
      e.preventDefault();
      e.stopPropagation();
      this.submit();
    }
  }

  createRenderRoot() {
    return this; // will render the template without shadow DOM
  }


  updated(changedProperties) {
    if (changedProperties.has('callback')) {
      this.callbackUpdated(this.callback, changedProperties.get('callback'));
    }

    if (changedProperties.has('content')) {
      this.contentUpdated(this.content, changedProperties.get('content'));
    }
  }

  firstUpdated() {
    this.addListeners();
  }

  callbackUpdated(newUrl, oldUrl) {
    this.fetch()
  }

  contentUpdated(newContent, oldContent) {
    this.addListeners();
  }

  disconnectedCallback() {
    this.removeListeners();
  }

  addListeners() {
    if (!this.form) return;
    this.form.addEventListener('submit', this.submitted);
  }

  async fetch() {
    this.loading = true;
    this.error = false;

    try {
      const response = await fetch(this.callback, {
        method: "GET",
        headers: new Headers({
          "Content-Type": "application/json",
          "accept": "application/json",
          "X-WP-Nonce": $autolink.nonce
        }),
      });
      const data = await response.json();
      const { success = false, content =  "" } = data;
      if (content) {
        this.injectContent(content)
        setTimeout(() => this.dispatchLoaded(data), 1)
      }
      this.error = !success
    } catch (error) {
      console.log(error)
    }

    this.loading = false
  }

  async submit() {
    if (this.submitting) return;

    this.submitting = true;
    this.success = false;
    this.error = false;
    try {
      const response = await fetch(this.action, {
        method: this.form.method,
        body: JSON.stringify(this.data),
        headers: new Headers({
          "Content-Type": "application/json",
          "accept": "application/json",
          "X-WP-Nonce": $autolink.nonce
        }),
      });
      const { success = false, content =  "" } = await response.json();
      this.success = success
      this.error = !success
      this.dispatchSuccess(this.data)
      if (content) {
        this.injectContent(content)
      }
    } catch (error) {
      console.log(error)
    }

    this.submitting = false
  }

  cancel() {
    this.dispatchCancel();
  }


  injectContent(content) {
    this.removeListeners();
    this.content = content;
  }

  dispatchLoaded( data ) {
    this.dispatchEvent(new CustomEvent('loaded', {detail: data}));
  }

  dispatchSuccess( data ) {
    this.dispatchEvent(new CustomEvent('success', {detail: data}));
  }

  dispatchCancel() {
    this.dispatchEvent(new CustomEvent('cancel'));
  }

  render() {
    return html`
      <div class="${classMap({
          "ajax-for": true,
          "ajax-form--error": this.error, 
          "ajax-form--success": this.success,
          "ajax-form--loading": this.loading,
      })}">
        ${this.loading ? html`<div class="ajax-form__loader">Loading</div>` : nothing}
        <slot></slot>
        ${!this.loading && this.content ? html`<div class="ajax-form__content">
          ${unsafeHTML(this.content)}
        </div>` : nothing}
        <footer class="ajax-form__footer">
          ${this.content ? this.renderButton() : nothing}
        </footer>
      </div>
    `;
  }

  renderButton() {
    return html`
      <dt-button @click="${this.submit}" ?disabled="${this.submitting}" context="success">
        ${$autolink.translations.save}
      </dt-button>
      <dt-button @click="${this.cancel}" context="alert">
        ${$autolink.translations.close}
      </dt-button>
    `;
  }

  removeListeners() {
    if (this.form) {
      this.form.removeEventListener('submit', this.submitted);
    }
  }
}