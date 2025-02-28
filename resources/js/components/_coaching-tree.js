import { css, html, LitElement } from "lit";
import { classMap } from "lit/directives/class-map.js";
import Sortable from "sortablejs";
import { customElement, property, query, queryAll } from "lit/decorators.js";
import { api_url } from "../_helpers.js";

/**
 * A component that renders a sortable tree of groups.
 */
@customElement("al-coaching-tree")
export class CoachingTree extends LitElement {
  sortableInstances = [];
  @queryAll(".groups--sortable")
  sortables;
  @queryAll(".groups")
  groupLists;
  @queryAll("#tree .group__generation")
  groupGenerationIcons;
  @query("#unassigned")
  unassignedSection;
  @property({
    type: String,
  })
  title = "Church Tree";
  @property({
    type: String,
  })
  unassignedTitle = "Unassigned Churches";
  @property({
    type: String,
  })
  unassignedTip =
    "Move these churches to the Church Tree to assign them to a group.";
  @property({
    type: String,
  })
  keyTitle = "Key";
  @property({
    type: String,
  })
  assignedLabel = "Churches you are assigned to";
  @property({
    type: String,
  })
  coachedLabel = "Churches you coach";
  @property({
    type: String,
  })
  leadingLabel = "Churches you lead";
  @property({
    type: String,
  })
  generationLabel = "Generation Number";
  @property({
    type: Object,
    reflect: true,
  })
  tree = [];
  @property({
    type: Object,
    reflect: true,
  })
  unassignedTree = [];
  @property({
    type: Object,
    reflect: true,
  })
  parents = {};
  @property({
    type: Object,
    reflect: true,
  })
  titles = {};
  @property({
    type: Boolean,
    reflect: true,
  })
  loading = true;
  @property({
    type: String,
  })
  endpoint = "";
  @property({
    type: String,
  })
  error = "";
  @property({
    type: String,
  })
  noGroupsMessage = "No churches found.";

  constructor() {
    super();
  }

  static get styles() {
    return css`
      :host {
        justify-content: center;
        display: block;
        width: 100%;
        --tree-spacing: 10px;
      }

      #tree,
      #unassigned-tree {
        margin-top: 0;
        margin-bottom: calc(var(--tree-spacing) * 2);
        overflow-y: hidden;
        overflow-x: auto;
      }

      p {
        font-weight: normal;
      }

      ul {
        list-style: none;
        position: relative;
        padding-left: calc(var(--tree-spacing) * 2);
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

      .group__title {
        display: block;
        color: #2c5364;
        max-width: 100%;
        white-space: nowrap;
      }

      .group__body {
        display: flex;
        align-items: center;
        padding-top: calc(var(--tree-spacing) * 2);
      }

      #tree > .group:first-child > .group__body,
      #unassigned-tree > .group:first-child > .group__body {
        padding-top: 0;
      }

      #tree > .group > .group__body > .group__tag:before,
      #tree > .group > .group__body > .group__tag:after,
      #unassigned-tree > .group > .group__body > .group__tag:before,
      #unassigned-tree > .group > .group__body > .group__tag:after {
        display: none;
      }

      .tree__spinner {
        display: flex;
        align-items: center;
        justify-content: center;
        grid-column: 1 / -1;
      }

      #tree,
      #unassigned-tree {
        padding-left: 0;
      }

      #unassigned.unassigned--empty {
        display: none;
      }

      .group {
        width: fit-content;
      }

      .group__tag {
        background-color: #e2e2e2;
        display: flex;
        align-items: center;
        padding: calc(var(--tree-spacing) * 0.5) calc(var(--tree-spacing) * 1.5)
          calc(var(--tree-spacing) * 0.5) calc(var(--tree-spacing) * 1.5);
        border-radius: 0 5px 5px 0;
        position: relative;
        border: solid 1px #afc1cc;
        border-left-color: #2c5364;
        min-height: calc(var(--tree-spacing) * 3);
        gap: var(--tree-spacing);
      }

      .group__tag:before {
        content: "";
        display: block;
        width: calc(var(--tree-spacing) * 2);
        height: 1px;
        background-color: #2c5364;
        position: absolute;
        top: 50%;
        left: calc(var(--tree-spacing) * -2);
        transform: translateY(1px);
      }

      .group__tag:after {
        content: "";
        display: block;
        width: 1px;
        border-left: 1px inset #2c5364;
        left: calc(var(--tree-spacing) * -2 - 1px);
        top: calc(var(--tree-spacing) * -2 - 2px);
        bottom: 0;
        position: absolute;
      }

      .group:not(:last-child) > .group__children:before {
        content: "";
        display: block;
        width: 1px;
        border-left: 1px inset #2c5364;
        left: calc(var(--tree-spacing) * -2);
        top: -1px;
        bottom: -2px;
        position: absolute;
      }

      .group:last-child > .group__body > .group__tag:after {
        bottom: calc(100% - calc(var(--tree-spacing) * 2 + 2px));
      }

      .group--assigned > .group__body > .group__tag {
        background-color: #b3e3ae;
      }

      .group__label,
      .group__handle {
        display: flex;
        align-items: center;
        pointer-events: none;
      }

      .group__handle {
        padding-top: calc(var(--tree-spacing) * 0.5);
        margin-left: calc(var(--tree-spacing) * -1.5);
        width: var(--tree-spacing);
      }

      .group__icons {
        display: flex;
        gap: calc(var(--tree-spacing) * 0.5);
        color: #2c5364;
        font-weight: lighter;
        align-items: center;
        justify-content: center;
      }

      .group__icons dt-icon {
        transform: translateY(3px);
      }

      .key__generation,
      .group__generation:not(:empty) {
        height: var(--tree-spacing);
        width: var(--tree-spacing);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: solid 1px #2c5364;
        font-size: calc(var(--tree-spacing) * 0.9);
        line-height: calc(var(--tree-spacing));
        font-weight: lighter;
      }

      dt-icon {
        color: #2c5364;
      }

      .unassigned__tip {
        margin-bottom: calc(var(--tree-spacing) * 2.5);
      }

      .group__icon {
        margin-left: var(--tree-spacing);
        display: block;
        transform: translateY(2px);
      }

      .tree__key td {
        padding: 0 var(--tree-spacing);
      }

      .key {
        border: solid 1px #afc1cc;
        border-left-color: #2c5364;
        border-radius: 0 5px 5px 0;
        background-color: #e2e2e2;
      }

      .key--assigned {
        background-color: #b3e3ae;
      }

      @media print {
        .group__body {
          -webkit-print-color-adjust: exact;
        }
      }
    `;
  }

  /**
   * When the component is first connected to the DOM, load the tree.
   */
  connectedCallback() {
    super.connectedCallback();
    this.load();
  }

  /**
   * When the component is disconnected from the DOM, destroy the sortable instances.
   */
  disconnectedCallback() {
    super.disconnectedCallback();
    this.sortableInstances.forEach((instance) => {
      instance.destroy();
    });
  }

  /**
   * Load the tree from the server.
   * @returns {Promise<void>}
   */
  async load() {
    this.loading = true;
    this.error = "";
    let result;
    try {
      result = await fetch(api_url("coaching-tree"), {
        method: "GET",
        headers: {
          "Content-Type": "application/json; charset=utf-8",
          "X-WP-Nonce": $autolink.nonce,
        },
      });
    } catch (e) {
      this.loading = false;
      this.error = e.message;
      return;
    }

    let { tree, parent_list, title_list } = await result.json();
    let unassignedChildTree = tree.filter((group) => group.id === "u");
    if (unassignedChildTree.length) {
      unassignedChildTree = unassignedChildTree[0].children;
    }
    tree = tree.filter((group) => group.id !== "u");

    this.tree = tree;
    this.parents = parent_list;
    this.titles = title_list;
    this.loading = false;
    this.unassignedTree = unassignedChildTree;
    setTimeout(this.initSortable.bind(this), 1);
    setTimeout(this.applyDomTweaks.bind(this), 1);
  }

  /**
   * Initialize the sortable instances.
   * @see https://github.com/SortableJS/Sortable#event-object-demo
   */
  initSortable() {
    this.sortables.forEach((sortable) => {
      this.sortableInstances.push(
        new Sortable(sortable, {
          group: {
            name: "tree",
            pull: true,
            put: true,
          },
          handle: ".group__tag",
          animation: 500,
          fallbackOnBody: true,
          swapThreshold: 0.11,
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
          sort: false,
          delay: 10,
        }),
      );
    });
  }

  /**
   * Render the component.
   * @returns {TemplateResult<1>}
   */
  render() {
    const { tree, loading } = this;
    if (!tree.length && !loading) {
      return html`
        <dt-alert context="info"> ${this.noGroupsMessage} </dt-alert>
      `;
    }
    return html`
      <div class="tree">
        ${this.renderTree()} ${this.renderUnassignedTree()} ${this.renderKey()}
      </div>
    `;
  }

  /**
   * Render the tree.
   * @returns {TemplateResult<1>}
   */
  renderTree() {
    const { loading, tree, error, syncing, unassignedTree, title } = this;

    if (error) {
      return html` <dt-alert context="error" dismissable>${error}</dt-alert> `;
    }

    return html`
      <dt-tile title="${title}">
        ${loading
          ? html` <div class="tree__spinner">
              <dt-spinner></dt-spinner>
            </div>`
          : ""}
        <div class="section__inner">
          <ul class="groups groups--sortable" id="tree">
            ${tree.map((group) => this.renderGroup(group))}
          </ul>
        </div>
      </dt-tile>
    `;
  }

  /**
   * Render the unassigned tree. Unassigned churches are churches that are not assigned to a parent group.
   * @returns {TemplateResult<1>}
   */
  renderUnassignedTree() {
    const { unassignedTree, unassignedTitle, unassignedTip, loading } = this;

    return html`
      <dt-tile title="${unassignedTitle}" id="unassigned">
        <div class="section__inner">
          <dt-alert
            class="unassigned__tip"
            context="success"
            outline
            icon=""
            dismissable
          >
            ${unassignedTip}
          </dt-alert>
          <ul class="groups groups--sortable" id="unassigned-tree">
            ${unassignedTree.map((group) => this.renderGroup(group))}
          </ul>
        </div>
      </dt-tile>
    `;
  }

  renderKey() {
    const {
      keyTitle,
      assignedLabel,
      coachedLabel,
      leadingLabel,
      generationLabel,
      loading,
    } = this;

    if (loading) {
      return null;
    }

    return html`
            <dt-tile title="${keyTitle}">
                <div class="section__inner">
                    <table class="tree__key">
                        <tr>
                            <td class="key key--assigned">
                            </td>
                            <td>
                                <p>
                                    ${assignedLabel}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="key key--coached">
                            </td>
                            <td>
                                <p>
                                    ${coachedLabel}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class="key key--leading">
                                <dt-icon icon="ph:user-bold" size="15px"></dt-icon>
                            </td>
                            <td>
                                <p>
                                    ${leadingLabel}
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
            </div>`;
  }

  /**
   * Render a group.
   * @param id
   * @param children
   * @param name
   * @param assigned
   * @param leading
   * @param isSortable
   * @returns {TemplateResult<1>}
   */
  renderGroup(
    { id, children, name, assigned, leading, coaching },
    isSortable = true,
  ) {
    return html`
      <li
        data-id="${id}"
        data-assigned="${assigned}"
        class="${classMap({
          group: true,
          "group--assigned": assigned,
          "group--leading": leading,
          "group--coaching": coaching,
        })}"
      >
        <div class="group__body">
          <div class="group__tag">
            ${isSortable
              ? html` <div class="group__handle">
                  <dt-icon
                    icon="clarity:drag-handle-line"
                    size="20px"
                    class="group__icon--handle"
                  ></dt-icon>
                </div>`
              : null}
            <span class="group__generation"></span>
            <label class="group__title">${name}</label>
            <div class="group__icons">
              ${assigned
                ? html`
                    <dt-icon
                      icon="ph:user-bold"
                      size="15px"
                      class="group__icon--assigned"
                    ></dt-icon>
                  `
                : null}
            </div>
          </div>
          <div style="flex-grow: 1"></div>
        </div>
        ${assigned ? this.renderChildren(children) : null}
      </li>
    `;
  }

  /**
   * Render the children of a group.
   * @param children
   * @returns {TemplateResult<1>}
   */
  renderChildren(children) {
    return html`
      <ul
        class="${classMap({
          "groups groups--sortable group__children": true,
        })}"
      >
        ${children ? children.map((group) => this.renderGroup(group)) : null}
      </ul>
    `;
  }

  /**
   * Handle a SortableJS drop event.
   * @param event
   * @see
   */
  handleDrop(event) {
    const { assignedError, tree } = this;
    const { oldIndex, newIndex, item, to, from } = event;
    const id = item.dataset.id;
    const newParent = to.closest("li");
    const newParentId = newParent ? newParent.dataset.id : "root";
    const oldParent = from.closest("li");
    const oldParentId = oldParent ? oldParent.dataset.id : "root";

    if (!this.validateDrop(event)) {
      const nextElement = from.children[oldIndex + 1];
      if (!nextElement) {
        console.log(from);
        from.appendChild(item);
      } else {
        from.insertBefore(item, nextElement);
      }
      this.applyDomTweaks();
      return;
    }

    this.saveParentConnection(id, oldParentId, newParentId);
    setTimeout(this.applyDomTweaks.bind(this), 1);
  }

  /**
   * Find a group in the tree.
   * @param id
   * @param tree
   * @returns {*}
   */
  findGroup(id, tree = false) {
    if (!tree) {
      tree = this.tree;
    }
    return tree.reduce((match, group) => {
      if (match) {
        return match;
      }

      let isMatch = group.id === parseInt(id);

      if (isMatch) {
        return group;
      }

      if (group.children) {
        return this.findGroup(id, group.children);
      }

      return false;
    }, false);
  }

  /**
   * Validate a drop event.
   * @param oldIndex
   * @param newIndex
   * @param item
   * @param to
   * @param from
   * @returns {boolean}
   */
  validateDrop({ oldIndex, newIndex, item, to, from }) {
    const id = item.dataset.id;

    if (from.id === "unassigned-tree") {
      if (to.id === "tree") {
        return false;
      }
      return true;
    }

    const group = this.findGroup(id);

    if (!group) {
      return false;
    }

    if (!group.assigned && to.id === "tree") {
      return false;
    }

    return true;
  }

  /**
   * Apply DOM tweaks to the tree.
   */
  applyDomTweaks() {
    this.groupLists.forEach((list) => {
      if (list.querySelector("li")) {
        list.classList.remove("groups--empty");
      } else {
        list.classList.add("groups--empty");
      }
    });
    if (this.unassignedSection) {
      if (!this.unassignedSection.querySelector("li")) {
        this.unassignedSection.classList.add("unassigned--empty");
      } else {
        this.unassignedSection.classList.remove("unassigned--empty");
      }
    }
    this.groupGenerationIcons.forEach((generationIcon) => {
      let list = generationIcon.closest("ul");
      let generation = 0;
      while (list) {
        generation++;
        list = list.parentNode.closest("ul");
      }
      generationIcon.innerText = generation;
    });
  }

  /**
   * Save the parent connection to the server.
   * @param id
   * @param oldParentId
   * @param newParentId
   * @returns {Promise<void>}
   */
  async saveParentConnection(id, oldParentId, newParentId) {
    try {
      const result = await fetch(api_url("coaching-tree"), {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": $autolink.nonce,
        },
        body: JSON.stringify({
          self: id,
          new_parent: newParentId,
          previous_parent: oldParentId,
        }),
      });
      console.log(await result.json());
    } catch (e) {
      this.error = e.message;
      console.error(e);
    }
  }
}
