import {html, css, LitElement} from "lit";
import {ref, createRef} from 'lit/directives/ref.js';
import {classMap} from "lit/directives/class-map.js";
import Sortable from 'sortablejs';
import {customElement, property, query, queryAll} from "lit/decorators.js";

@customElement('app-groups-tree')
export class ChurchTile extends LitElement {
    sortableInstances = []
    @queryAll('.group__children')
    sortables
    @queryAll('.groups')
    groupLists
    @query('#unassigned')
    unassignedSection
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
        type: String,
    })
    coachedLabel = 'Churches you coach'
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
            justify-content: center;
            display: block;
            width: 100%;
          }

          #tree {
            overflow: hidden;
          }

          p {
            font-weight: normal;
          }

          ul {
            list-style: none;
            position: relative;
            padding-left: 10px;
          }

          ul:after {
            content: '';
            display: block;
            height: calc(100% - 40px);
            top: 20px;
            bottom: 20px;
            width: 1px;
            background-color: #2C5364;
            position: absolute;
            left: 0px;
          }

          .groups {
            position: relative;
          }

          .groups:not(.groups--empty):before {
            content: '';
            display: block;
            height: 40px;
            width: 1px;
            background-color: #2C5364;
            position: absolute;
            bottom: calc(100% - 20px);
            left: 0;
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
            -50
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
            padding: 5px 15px 5px 15px;
            border-radius: 0 5px 5px 0;
            position: relative;
            border: solid 1px #afc1cc;
            border-left-color: #2C5364;
            min-height: 31px;
          }

          .group__tag:before {
            content: '';
            display: block;
            width: 21px;
            height: 1px;
            background-color: #2C5364;
            position: absolute;
            top: 50%;
            left: -21px;
            transform: translateY(1px);
          }

          .group--assigned > .group__body > .group__tag {
            background-color: #b3e3ae;
          }

          .group--assigned .group .group > .group__body > .group__tag {
            background-color: white;
          }

          .group__label,
          .group__handle {
            display: flex;
            align-items: center;
          }

          .group__handle {
            padding-top: 5px;
            margin-left: -14px;
          }

          dt-icon {
            color: #2C5364;
          }

          .unassigned__tip {
            margin-bottom: 25px;
          }

          .group__icon--assigned,
          .group__icon--coached {
            margin-left: 10px;
            display: block;
            transform: translateY(2px);
          }

          .tree__key td {
            padding: 0 7px;
          }

          .key {
            border: solid 1px #afc1cc;
            border-left-color: #2C5364;
            border-radius: 0 5px 5px 0;
          }

          .key--assigned {
            background-color: #b3e3ae;
          }

          .key--coached {
            background-color: #E2E2E2;
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
        setTimeout(this.applyDomTweaks.bind(this), 1)
    }

    initSortable() {
        this.sortables.forEach((sortable) => {
            this.sortableInstances.push(
                new Sortable(sortable, {
                    group: 'tree',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onEnd: this.handleDrop.bind(this),
                    onChoose: this.applyDomTweaks.bind(this),
                    onUnchoose: this.applyDomTweaks.bind(this),
                    onStart: this.applyDomTweaks.bind(this),
                    onAdd: this.applyDomTweaks.bind(this),
                    onUpdate: this.applyDomTweaks.bind(this),
                    onRemove: this.applyDomTweaks.bind(this),
                    onFilter: this.applyDomTweaks.bind(this),
                    onClone: this.applyDomTweaks.bind(this),
                    onCharge: this.applyDomTweaks.bind(this),
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
        const {keyTitle, assignedLabel, coachedLabel} = this;
        return html`
            <div class="tree">
                ${this.renderTree()}
                ${this.renderUnassignedTree()}
                <dt-tile title="${keyTitle}">
                    <div class="section__inner">
                        <table class="tree__key">
                            <tr>
                                <td class="key key--assigned">
                                    <dt-icon icon="ph:user-bold" size="15px"></dt-icon>
                                </td>
                                <td>
                                    <p>
                                        ${assignedLabel}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="key key--coached">
                                    <dt-icon icon="mdi:help-outline" size="15px"></dt-icon>
                                </td>
                                <td>
                                    <p>
                                        ${coachedLabel}
                                    </p>
                                </td>
                            </tr>
                        </table>
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
                    <ul class="groups" id="tree">
                        ${tree.map(group => this.renderGroup(group, false))}
                    </ul>
                </div>
            </dt-tile>
        `
    }

    renderUnassignedTree() {
        const {unassignedTree, unassignedTitle, unassignedTip} = this;

        return html`
            <dt-tile title="${unassignedTitle}" id="unassigned">
                <div class="section__inner">
                    <dt-alert class="unassigned__tip" context="success" outline icon="" dismissable>
                        ${unassignedTip}
                    </dt-alert>
                    <ul class="groups">
                        ${unassignedTree.map(group => {
                            this.renderGroup(group)
                        })}
                    </ul>
                </div>
            </dt-tile>
        `
    }

    renderGroup({id, children, name, assigned}, isSortable = true) {
        return html`
            <li class="${
                    classMap({
                        'group': true,
                        'group--assigned': assigned,
                    })
            }" data-id="${id}" data-assigned="${assigned}">
                <div class="group__body">
                    <div class="group__tag">
                        ${isSortable ? html`
                            <div class="group__handle">
                                <dt-icon icon="clarity:drag-handle-line" size="20px"
                                         class="group__icon--handle"></dt-icon>
                            </div>` : null}
                        <label class="group__title">${name}</label>
                        <div class="group__icons">
                            ${assigned ? html`
                                <dt-icon icon="ph:user-bold" size="15px" class="group__icon--assigned"></dt-icon>
                            ` : html`
                                <dt-icon icon="mdi:help-outline" size="15px" class="group__icon--coached"></dt-icon>
                            `}
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
            <ul class="${classMap({
                "groups group__children": true
            })}">
                ${children ? children.map(group => this.renderGroup(group)) : null}
            </ul>
        `
    }

    handleDrop(event) {
        const {assignedError, tree} = this
        const {oldIndex, newIndex, item, to, from} = event
        const id = item.dataset.id
        const newParent = to.closest('li')
        const newParentId = newParent ? newParent.dataset.id : 'root'
        const oldParent = from.closest('li')
        const oldParentId = oldParent ? oldParent.dataset.id : 'root'
        this.saveParentConnection(id, oldParentId, newParentId)
        setTimeout(this.applyDomTweaks.bind(this), 1)
    }

    applyDomTweaks() {
        this.groupLists.forEach((list) => {
            if (list.querySelector('li')) {
                list.classList.remove("groups--empty")
            } else {
                list.classList.add("groups--empty")
            }
        })
        if (this.unassignedSection) {
            if (!this.unassignedSection.querySelector('li')) {
                this.unassignedSection.remove()
            }
        }
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