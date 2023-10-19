import {css, html, LitElement} from "lit";
import {unsafeHTML} from 'lit/directives/unsafe-html.js';
import {DtBase} from "@disciple.tools/web-components";
import {queryAll, property} from "lit/decorators.js";

/**
 * @class AjaxField
 */
export class AjaxField extends DtBase {
    @queryAll("input, select, textarea, dt-text dt-select dt-textarea [value] [name]")
    fields;

    @property({type: String})
    callback = "";

    @property({type: String})
    method = "GET";

    @property({type: String})
    nonce = "";

    @property({type: String | Array})
    watch = "*";

    @property({type: Function})
    onSuccess = null;

    @property({type: String})
    content = "";

    @property({type: Array})
    events = ['input', 'change'];

    @property({type: Boolean})
    prefetch = true;

    @property({type: Boolean})
    loading = false;

    /**
     * @returns {string}
     * @returns {CSSResult}
     */
    static get styles() {
        return css`
          :host {
            width: fit-content;
            display: block;
          }
        `;
    }

    /**
     * @returns {HTMLFormElement}
     * @returns {*}
     */
    get form() {
        return this.closest("form");
    }

    /**
     * @returns {NodeListOf<Element>}
     * @returns {*}
     */
    get watched() {
        if (this.watch === "*") {
            return [this.form.querySelectorAll("input, select, textarea")];
        } else if (Array.isArray(this.watch)) {
            return this.form.querySelectorAll(this.watch.map(field => `input[name="${field}"], select[name="${field}"], textarea[name="${field}"]`).join(", "));
        } else {
            return [this.form];
        }
    }

    createRenderRoot() {
        return this; // will render the template without shadow DOM
    }

    /**
     * @returns {string}
     */
    connectedCallback() {
        super.connectedCallback();
        this.listen();
        if (this.prefetch) {
            window.setTimeout(this.fetch.bind(this), 1);
        }
    }

    /**
     * @returns {string}
     */
    listen() {
        this.watched.forEach(field => {
            this.events.forEach(event => {
                field.addEventListener(event, this.handleEvent.bind(this));
            });
        });
    }

    /**
     * @returns {string}
     */
    handleEvent() {
        window.setTimeout(this.fetch.bind(this), 1);
    }

    /**
     * @returns {string}
     */
    fetch() {
        const formData = new FormData(this.form);
        let url = this.callback;
        const method = this.method;
        const params = new URLSearchParams(formData);

        //combine params with any params in the callback url
        if (url.includes("?")) {
            const urlParams = new URLSearchParams(url.split("?")[1]);
            for (const [key, value] of urlParams.entries()) {
                params.has(key) ? params.set(key, value) : params.append(key, value);
            }
            url = url.split("?")[0];
        }

        params.set("_wpnonce", window.app.nonce);

        const headers = new Headers();
        const endpoint = url + "?" + params;
        this.loading = true;
        fetch(endpoint, {method, headers})
            .then((response) => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                return response;
            })
            .then(response => response.text())
            .then(content => {
                if (
                    content === "0"
                    || content === false
                    || content === ""
                    || content === "false"
                ) {
                    return ""
                }
                return content;
            })
            .then(this.handleSuccess.bind(this))
            .catch(this.handleError.bind(this))
            .finally(() => {
                this.loading = false;
            });
    }

    /**
     * @param {Error} error
     * @param error
     */
    handleError(error) {
        console.log(error)
        console.error(error);
    }

    /**
     * @param {string} content
     * @param content
     */
    handleSuccess(content) {
        this.dispatchEvent(new CustomEvent("ajax-field-response", {detail: content}));
        if (this.onSuccess) {
            this.onSuccess(content);
        } else {
            this.content = content;
        }
    }

    /**
     * @returns {TemplateResult<1>}
     */
    render() {
        const {content, loading} = this;

        if (loading) {
            return html`
                <dt-spinner></dt-spinner>
            `;
        }

        if (content) {
            return html`
                ${unsafeHTML(content)}`;
        }

        return html`
            <slot></slot>`;
    }

    /**
     * @returns {string}
     */
    disconnectedCallback() {
        super.disconnectedCallback();
        this.watched.forEach(field => {
            this.events.forEach(event => {
                field.removeEventListener(event, this.handleEvent.bind(this));
            });
        });
    }
}

window.customElements.define("ajax-field", AjaxField);
