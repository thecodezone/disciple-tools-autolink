import {css, html, LitElement,} from "lit";
import {unsafeHTML} from 'lit/directives/unsafe-html.js';
import {DtBase} from "@disciple.tools/web-components";
import {property, query} from "lit/decorators.js";
import {ref, createRef} from 'lit/directives/ref.js';
import httpBuildQuery from 'http-build-query'
import {keyed} from 'lit/directives/keyed.js';

/**
 * @class Churches
 */
export class Churches extends DtBase {
    loadTriggerRef = createRef();

    @property({type: Object})
    translations = {};

    @property({type: Object})
    links = {};

    @property({type: String})
    content = "";

    @property({type: Boolean})
    loading = false;

    @property({type: Array})
    posts = [];

    @property({type: Number})
    total = 0;

    @property({type: Number})
    limit = 10;

    @property({type: Object})
    fields = {}

    @property({type: Object})
    countFields = {}

    @property({type: String})
    error = "";

    loadTriggerObserver = null;

    get hasMorePages() {
        return this.posts.length < this.total
    }

    createRenderRoot() {
        return this; // will render the template without shadow DOM
    }

    loadMore() {
        const {loading} = this

        if (loading) {
            return
        }

        this.fetch()
    }

    /**
     * @returns {string}
     */
    fetch() {
        const {limit, posts} = this
        let url = window.app.rest_base + window.magic.rest_namespace;
        const method = "get"
        const params = httpBuildQuery({
            '_wpnonce': window.app.nonce,
            'parts': window.magic.parts,
            'action': 'groups',
            limit,
            offset: posts.length
        })

        const headers = new Headers();
        const endpoint = url + "?" + params;
        this.loading = true;
        this.error = ""
        fetch(endpoint, {method, headers})
            .then((response) => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                return response;
            })
            .then(response => response.json())
            .then(this.handleSuccess.bind(this))
            .catch(this.handleError.bind(this))
            .finally(() => {
                this.loading = false;
            });
    }

    connectedCallback() {
        super.connectedCallback();
        setTimeout(this.listenForScrolled.bind(this), 1)
    }

    listenForScrolled() {
        if (!this.loadTriggerRef.value) {
            return
        }
        let checked = true

        const listener = () => {
            if (!this.hasMorePages) {
                window.removeEventListener('scroll', listener)
                return
            }

            if (!this.loadTriggerRef.value) {
                return
            }

            if (this.loadTriggerRef.value.getBoundingClientRect().top < window.innerHeight
                && this.loadTriggerRef.value.getBoundingClientRect().bottom > 0) {
                checked = true
                this.loadMore()
            } else {
                checked = false
            }
        }

        window.addEventListener('scroll', listener);
    }

    /**
     * @param {Error} error
     * @param error
     */
    handleError(error) {
        this.error = error
    }

    handleSuccess(data) {
        this.posts.push(...data.posts)
        this.total = data.total
    }

    /**
     * @returns {TemplateResult<1>}
     */
    render() {
        const {loading, error} = this;

        return html`
            ${error && !loading ? html`
                <dt-alert context="alert" dismissible>${error}</dt-alert>
            ` : null}
            ${this.renderGroups()}
            ${this.renderPagination()}
        `
    }

    renderGroups() {
        const {posts} = this;

        return html`
            <div class="churches__groups">
                ${posts.map((group, index) => {
                    return this.renderGroup(group, index === 0)
                })}
            </div>
        `
    }

    renderGroup(group, opened) {
        const {translations, fields, links} = this;

        return html`
            <church-tile class="church"
                         title="${group.post_title}"
                         key="church-${group.ID}"
            >
                ${this.renderCounts(group)}
                <app-church
                        .translations="${translations.start_date_label}"
                        .group="${group}"
                        .fields="${fields}"
                        ?opened="${opened}"></app-church>
                <app-church-menu>
                    <dt-button context="primary"
                               href="${links.view_group + "&post=" + group.ID}">
                        ${translations.view_group}
                    </dt-button>
                    <dt-button context="primary" W
                               href="${links.edit_group + "&post=" + group.ID}"
                    >
                        ${translations.edit_group}
                    </dt-button>
                    <dt-button context="alert"
                               href="${links.delete_group + "&post=" + group.ID}"
                               confirm="${translations.delete_group_confirm}">
                        ${translations.delete_group}
                    </dt-button>
                </app-church-menu>
            </church-tile>
        `
    }

    renderCounts(group) {
        const {translations, countFields} = this;

        if (!Object.values(countFields).length) {
            return null
        }

        return html`
            <div class="church__counts">
                ${Object.entries(countFields).map(([key, field]) => this.renderCount(group, key, field, group[key] ?? 0))}
            </div>
        `
    }

    renderCount(group, key, field, value) {
        return html`
            <div class="church__count"
                 data-churchId="${group.ID}"
                 data-field="${key}"
                 key="church-${group.ID}-${key}"
            >
                <dt-modal context="default"
                          hideHeader>
                    <div slot="openButton">
                        <img class="count__icon"
                             src="${field.icon}"
                             alt="${field.name}"
                             width="25"
                             height="25">
                        <span class="count__value">${value}</span>
                    </div>

                    <div slot="content">
                        <app-church-health-field
                                id="groups_${group.ID}_${key}"
                                name="${key}"
                                icon="${field.icon}"
                                label="${field.name}"
                                onChange=""
                                value="${value}"
                                postType="groups"
                                postId="${group.ID}"
                                apiRoot="${window.app.apiRoot}"
                                min="0"
                                placeholder="0"
                                nonce="${window.app.nonce}"/>
                    </div>
                </dt-modal>
            </div>`
    }

    renderLoading() {
        return html`
            <div class="churches__loading">
                <dt-spinner></dt-spinner>
            </div>
        `
    }

    renderPagination() {
        const {posts, total, translations, loading} = this;

        if (posts.length >= total) {
            return
        }

        return html`
            ${keyed('load-trigger', html`
                <div ${ref(this.loadTriggerRef)} class="churches__pagination">
                    ${loading ? this.renderLoading()
                            : html`
                                <dt-button context="primary">
                                    ${translations.more}
                                </dt-button>
                            `
                    }
                </div>`)}
        `
    }
}

window.customElements.define("app-churches", Churches);
