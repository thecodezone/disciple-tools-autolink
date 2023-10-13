import {html, css, LitElement} from "lit";
import {ref, createRef} from 'lit/directives/ref.js';
import {classMap} from "lit/directives/class-map.js";
import Sortable from 'sortablejs';
import {customElement, property, query, queryAll} from "lit/decorators.js";

@customElement('app-groups-tree')
export class ChurchTile extends LitElement {
    sortableInstances = []
    @queryAll('.groups--sortable')
    sortables
    @queryAll('.groups')
    groupLists
    @queryAll('.group__generation')
    groupGenerationIcons
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
        type: String,
    })
    generationLabel = 'Generation Number'
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

    })
    error = ''
    @property({
        type: String,
    })
    noGroupsMessage = 'No churches found.'

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
            margin-top: 0;
            margin-bottom: 25px;
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

          .groups {
            position: relative;
          }

          .section__inner {
            grid-column: 1 / -1;
          }

          li {
            position: relative;
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
            padding-top: 20px;
          }

          #tree > .group:first-child > .group__body {
            padding-top: 0;
          }

          #tree > .group > .group__body > .group__tag:before,
          #tree > .group > .group__body > .group__tag:after {
            display: none;
          }

          #tree {
            padding-left: 0;
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
            width: 10px;
            height: 1px;
            background-color: #2C5364;
            position: absolute;
            top: 50%;
            left: -10px;
            transform: translateY(1px);
          }

          .group__tag:after {
            content: '';
            display: block;
            width: 1px;
            background-color: #2C5364;
            left: -11px;
            top: -22px;
            bottom: 0;
            position: absolute;
          }

          .group:not(:last-child) > .group__children:before {
            content: '';
            display: block;
            width: 1px;
            background-color: #2C5364;
            left: -10px;
            top: -1px;
            bottom: -1px;
            position: absolute;
          }

          .group:last-child > .group__body > .group__tag:after {
            bottom: calc(100% - 22px);
          }


          .group--leading > .group__body > .group__tag {
            background-color: #b3e3ae;
          }

          .group--leading .group .group > .group__body > .group__tag {
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

          .group__icons {
            display: flex;
            gap: 5px;
            color: #2C5364;
            font-weight: lighter;
            align-items: center;
            justify-content: center;
          }

          .key__generation,
          .group__generation {
            height: 10px;
            width: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: solid 1px #2C5364;
            font-size: 9px;
            line-height: 10px;
            font-weight: lighter;
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
                    group: {
                        name: 'tree',
                        pull: true,
                        put: true
                    },
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
        const {keyTitle, assignedLabel, coachedLabel, generationLabel, tree} = this;
        if (!tree.length) {
            return html`
                <dt-alert context="info">
                    ${this.noGroupsMessage}
                </dt-alert>
            `
        }
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
                            <tr>
                                <td class="key key--generation">
                                    <div class="key__generation">
                                        1
                                    </div>
                                </td>
                                <td>
                                    <p>
                                        ${generationLabel}
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
                    <ul class="groups groups--sortable" id="tree">
                        ${tree.map(group => this.renderGroup(group))}
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

    renderGroup({id, children, name, assigned, leading}, isSortable = true) {
        return html`
            <li class="${
            classMap({
                'group': true,
                'group--assigned': assigned,
                'group--leading': leading
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
                            <span class="group__generation">
                            </span>
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
            "groups groups--sortable group__children": true
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

        if (!this.validateDrop(event)) {
            const nextElement = from.children[oldIndex + 1]
            if (!nextElement) {
                from.appendChild(item)
            } else {
                from.insertBefore(item, nextElement)
            }
            return;
        }

        this.saveParentConnection(id, oldParentId, newParentId)
        setTimeout(this.applyDomTweaks.bind(this), 1)
    }

    findGroup(id, tree = false) {
        if (!tree) {
            tree = this.tree
        }
        return tree.reduce((match, group) => {
            if (match) {
                return match;
            }

            let isMatch = group.id === parseInt(id)

            if (isMatch) {
                return group
            }

            if (group.children) {
                return this.findGroup(id, group.children)
            }

            return false;
        }, false)
    }

    validateDrop({oldIndex, newIndex, item, to, from}) {
        const id = item.dataset.id

        const group = this.findGroup(id)

        if (!group) {
            return false;
        }

        if (!group.leading && to.id === 'tree') {
            return false;
        }

        return true;
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
        this.groupGenerationIcons.forEach((generationIcon) => {
            let list = generationIcon.closest('ul')
            let generation = 0
            while (list) {
                generation++
                list = list.parentNode.closest('ul')
            }
            generationIcon.innerText = generation
        })
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