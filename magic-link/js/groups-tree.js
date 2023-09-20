import {html, css, LitElement} from "lit";
import {ref, createRef} from 'lit/directives/ref.js';
import Sortable from 'sortablejs';
import {customElement, property, query, queryAll} from "lit/decorators.js";

@customElement('app-groups-tree')
export class ChurchTile extends LitElement {

    treeRef = createRef()
    unassignedTreeRef = createRef()
    sortableInstances = []
    @queryAll('ul')
    sortables
    @property({
        type: String,
    })
    title = 'Church Tree'
    @property({
        type: String,
    })
    unassignedTitle = 'Unassigned Churches'
    @property({
        type: String,
    })
    unassignedTip = 'Move these churches to the Church Tree to assign them to a group.'
    @property({
        type: String,
    })
    keyTitle = 'Key'
    @property({
        type: String,
    })
    assignedLabel = 'Churches you lead'
    @property({
        type: Object,
        reflect: true
    })
    tree = []
    @property({
        type: Object,
        reflect: true
    })
    unassignedTree = []
    @property({
        type: Object,
        reflect: true
    })
    parents = {}
    @property({
        type: Object,
        reflect: true
    })
    titles = {}
    @property({
        type: Boolean,
        reflect: true
    })
    loading = true
    @property({
        type: String,
    })
    endpoint = ''
    @property({
        type: String,

    })Html
    error = ''

    constructor() {
        super();
    }

    static get styles() {
        return css`
          :host {
            display: flex;
            justify-content: center;
          }

          p {
            font-weight: normal;
          }

          ul {
            list-style: none;
            border-left: 1px solid #2C5364;
            padding: 0 10px 0 10px;
          }

          .section__inner {
            grid-column: 1 / -1;
          }

          li {
            margin-bottom: 10px;
            padding: 0 10px 0 10px;
          }

          li:first-child {
            border-top: none;
          }

          li:last-child {
            border-bottom: none;
          }

          .groups-tree {
            width: 100%;
          }

          .nested-sortable {
            padding-left: 30px;
          }

          .group__title {
            display: block;
            color: #2C5364;
          }

          .group__body {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
          }

          .group__tag {
            background-color: #E2E2E2;
            display: flex;
            align-items: center;
            padding: 5px 15px 5px 0px;
            border-radius: 5px;
            position: relative;
          }

          .group__tag:before {
            content: '';
            display: block;
            width: 15px;
            height: 1px;
            background-color: #2C5364;
            position: absolute;
            top: 50%;
            left: -20px;
            transform: translateY(-50%);
          }

          .group__label,
          .group__handle {
            display: flex;
            align-items: center;
          }

          .group__handle {
            padding-top: 5px;
          }

          dt-icon {
            color: #2C5364;
          }

          .unassigned__tip {
            margin-bottom: 25px;
          }

          .group__icon--assigned {
            margin-left: 10px;
          }

          .tree__key td {
            padding: 7px;
          }
        `;
    }

    connectedCallback() {
        super.connectedCallback();
        this.load();
    }

    disconnectedCallback() {
        super.disconnectedCallback();
        this.sortableInstances.forEach((instance) => {
            instance.destroy();
        });
    }

    async load() {
        this.loading = true
        this.error = ''
        let result
        try {
            result = await this.fetch('tree')
        } catch (e) {
            this.error = e.message
        }

        let {tree, parent_list, title_list} = await result.json()
        let unassignedChildTree = tree.filter((group) => group.id === 'u')
        if (unassignedChildTree.length) {
            unassignedChildTree = unassignedChildTree[0].children
        }
        tree = tree.filter((group) => group.id !== 'u')

        this.tree = tree
        this.parents = parent_list
        this.titles = title_list
        this.loading = false
        this.unassignedTree = unassignedChildTree
        setTimeout(this.initSortable.bind(this), 1)
    }

    initSortable() {
        console.log(this.sortables)
        this.sortables.forEach((sortable) => {
            this.sortableInstances.push(
                new Sortable(sortable, {
                    group: 'tree',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onEnd: this.handleDrop.bind(this),
                    sort: false
                })
            )
        });
    }

    fetch(action, data) {
        return fetch(this.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json; charset=utf-8',
                'X-WP-Nonce': window.app.nonce
            },
            body: JSON.stringify({
                action,
                parts: window.magic.parts,
                data
            })
        })
    }

    render() {
        const {keyTitle, assignedLabel} = this;
        return html`
            <div class="tree" ${ref(this.treeRef)}}>
                ${this.renderTree()}
                ${this.renderUnassignedTree()}
                <dt-tile title="${keyTitle}">
                    <div class="section__inner">
                        <table class="tree__key">
                            <tr>
                                <td>
                                    <dt-icon icon="ph:user-bold" size="20px"></dt-icon>
                                </td>
                                <td>
                                    <p>
                                        ${assignedLabel}
                                    </p>
                                </td>
                            </tr>
                            </p>
                    </div>
                </dt-tile>
            </div>
        `
    }

    renderTree() {
        const {loading, tree, error, syncing, unassignedTree, title} = this;

        if (loading) {
            return html`
                <dt-spinner></dt-spinner>
            `
        }

        if (error) {
            return html`
                <dt-alert context="error" dismissable>${error}</dt-alert>
            `
        }

        return html`
            <dt-tile title="${title}">
                <div class="section__inner">
                    <ul id="tree" ${ref(this.treeRef)}>
                        ${tree.map(this.renderGroup.bind(this))}
                    </ul>
                </div>
            </dt-tile>
        `
    }

    renderUnassignedTree() {
        const {unassignedTree, unassignedTitle, unassignedTip} = this;

        if (!unassignedTree.length) {
            return null
        }

        return html`
            <dt-tile title="${unassignedTitle}">
                <div class="section__inner">
                    <dt-alert class="unassigned__tip" context="success" outline icon="" dismissable>
                        ${unassignedTip}
                    </dt-alert>
                    <ul id="unassigned-tree" ${ref(this.unassignedTreeRef)}>
                        ${unassignedTree.map(this.renderGroup.bind(this))}
                    </ul>
                </div>
            </dt-tile>
        `
    }

    renderGroup({id, children, name, assigned}) {
        return html`
            <li class="group" data-id="${id}" data-assigned="${assigned}">
                <div class="group__body">
                    <div class="group__tag">
                        <div class="group__handle">
                            <dt-icon icon="clarity:drag-handle-line" size="20px" class="group__icon--handle"></dt-icon>
                        </div>
                        <label class="group__title">${name}</label>
                        <div class="group__icons">
                            ${assigned ? html`
                                <dt-icon icon="ph:user-bold" size="10px" class="group__icon--assigned"></dt-icon>
                            ` : null}
                        </div>
                    </div>
                    <div style="flex-grow: 1"></div>
                </div>
                ${assigned ? this.renderChildren(children) : null}
            </li>
        `
    }

    renderChildren(children) {
        return html`
            <ul class=group__children">
                ${children ? children.map(this.renderGroup.bind(this)) : null}
            </ul>
        `
    }

    handleDrop(event) {
        const {assignedError} = this
        const {oldIndex, newIndex, item, to, from} = event
        const id = item.dataset.id
        const newParent = to.closest('li')
        const newParentId = newParent ? newParent.dataset.id : 'root'
        const oldParent = from.closest('li')
        const oldParentId = oldParent ? oldParent.dataset.id : 'root'
        this.saveParentConnection(id, oldParentId, newParentId)
    }

    async saveParentConnection(id, oldParentId, newParentId) {
        try {
            await this.fetch('onItemDrop', {
                self: id,
                new_parent: newParentId,
                previous_parent: oldParentId
            })
        } catch (e) {
            this.error = e.message
            console.error(e)
        }
    }
}