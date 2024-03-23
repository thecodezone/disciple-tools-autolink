import {html, css, LitElement} from 'lit';
import {customElement, property, query} from 'lit/decorators.js';
import {unsafeHTML} from 'lit/directives/unsafe-html.js';

@customElement('admin-training-videos-field')
export class TrainingVideosField extends LitElement {
    static styles = css`
      .button,
      button {
        display: inline-block;
        text-decoration: none;
        cursor: pointer;
        border-width: 1px;
        border-style: solid;
        -webkit-appearance: none;
        border-radius: 3px;
        white-space: nowrap;
        box-sizing: border-box;
        padding: 0 14px;
        line-height: 2.71428571;
        font-size: 14px;
        vertical-align: middle;
        min-height: 40px;
        margin-bottom: 4px;
        color: #2271b1;
        border-color: #2271b1;
      }

      input[type="text"], textarea {
        -webkit-appearance: none;
        padding: 3px 10px;
        min-height: 40px;
        word-wrap: break-word;
        font-size: 16px;
        box-shadow: 0 0 0 transparent;
        border-radius: 4px;
        border: 1px solid #aeb0b6;
        background-color: #fff;
        color: #2c3338;
        width: 100%;
        box-sizing: border-box;
      }

      textarea {
        height: 60px;
      }

      .embed iframe {
        aspect-ratio: 16 / 9;
        height: 100%;
        width: 100%;
      }

      .video {
        padding: 5px;
        border: solid #C3C4C7 1px;
      }

      .button--success {
        color: #35b122;
        border-color: #35b122;
      }

      .button--danger {
        color: #b12222;
        border-color: #b12222;
      }
    `;

    internals;
    @property({
        type: Array,
        reflect: true
    })
    value = [];
    @property({
        type: Array
    })
    default = []
    @property({
        type: Object
    })
    translations = {
        title: 'Title',
        embed: 'Embed',
        reset: 'Reset',
        add: 'Add',
        remove: 'Remove',
        resetConfirm: 'Are you sure you want to revert to default content?',
        removeConfirm: 'Are you sure you want to remove this video?',
        up: 'Up',
        down: 'Down'
    }

    constructor() {
        super();
        this.internals = this.attachInternals();
    }

    static get formAssociated() {
        return true;
    }

    updated(changedProperties) {
        if (changedProperties.has('value')) {
            const value = JSON.stringify(this.value);
            this.internals.setFormValue(value);
            this.dispatchEvent(new CustomEvent('change', {
                detail: {
                    value
                }
            }));
        }
        super.updated(changedProperties);
    }


    render() {
        const {translations} = this;

        return html`
            <table style="width: 100%;">
                <thead>
                <tr>
                    <th style="width: 99%;"></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                ${this.value.map((video, index) => this.renderVideoFields(video, index))}
                <tbody>
            </table>

            <button @click="${this.add}" class="button button--success" aria-label="${translations.add}">+</button>
            <button @click="${this.reset}" class="button button--danger" aria-label="${translations.reset}">
                ${translations.reset}
            </button>
        `;
    }

    renderVideoFields(video, index) {
        const {translations} = this;

        return html`
            <tr>
                <td>
                    <table style="width: 100%;" class="video">
                        <tr>
                            <td>
                                <input type="text"
                                       name="videos[${index}][title]"
                                       value="${video.title}"
                                       placeholder="${translations.title}"
                                       @input="${this.handleInput}"
                                >
                            </td>
                        <tr>
                        <tr>
                            <td>
                                <textarea name="videos[${index}][embed]"
                                          @input="${this.handleInput}"
                                          placeholder="${translations.embed}"
                                >${video.embed}</textarea>
                                <div class="embed">
                                    ${unsafeHTML(video.embed)}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top">
                    ${index > 0 ? html`
                        <button @click="${() => this.up(index)}" class="button"
                                aria-label="${translations.up}">↑
                        </button>
                    ` : ''}
                    <button @click="${() => this.remove(index)}" class="button button--danger"
                            aria-label="${translations.remove}">x
                    </button>
                    ${index < this.value.length - 1 ? html`
                        <button @click="${() => this.down(index)}" class="button"
                                aria-label="${translations.down}">↓
                        </button>
                    ` : ''}
                </td>
            </tr>
        `;
    }

    add() {
        this.value = [...this.value, {title: '', link: ''}];
    }

    remove(index) {
        const {translations} = this;
        const confirmed = window.confirm(translations.removeConfirm);

        if (!confirmed) {
            return;
        }
        this.value = this.value.filter((video, i) => i !== index);
    }

    reset() {
        const {translations} = this;
        const confirmed = window.confirm(translations.resetConfirm);

        if (!confirmed) {
            return;
        }

        this.value = this.default;
    }

    move(index, direction) {
        const value = [...this.value];
        const [item] = value.splice(index, 1);
        value.splice(index + direction, 0, item);
        this.value = value;
    }

    up(index) {
        this.move(index, -1);
    }

    down(index) {
        this.move(index, 1);
    }

    handleInput(event) {
        const {target} = event;
        const {name, value} = target;
        const [key, index, field] = name.match(/videos\[(\d+)\]\[(\w+)\]/);

        console.log(value)

        this.value[index][field] = value;
        this.value = [...this.value];
    }
}