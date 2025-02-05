class GenMapperAutolink extends GenMapper {

  constructor() {
    super();

    // Chart v2 (Tree) Global Variables.
    this.orgchart_container = null;
    this.orgchart_container_chart = null;
  }
    appendRebaseButton(group) {}
    appendAddButton(group) {}

    loadHTMLContent() {
    this.translations = window.wpApiGenmapper.translation;

    document.getElementById("left-menu").innerHTML = `<div id="template-logo">
    <button onclick="genmapper.origData();" class="hint--rounded hint--right" aria-label="${this.translations.reset_label}"><img src="${this.plugin_uri}/charts/icons/refresh.svg"></i></button>
    <button onclick="genmapper.zoomIn();" class="hint--rounded hint--right" aria-label="${this.translations.zoom_in_label}"><img src="${this.plugin_uri}/charts/icons/136-zoom-in.svg"></i></button>
    <button onclick="genmapper.zoomOut();" class="hint--rounded hint--right" aria-label="${this.translations.zoom_out_label}"><img src="${this.plugin_uri}/charts/icons/137-zoom-out.svg"></i></button>`;

    document.getElementById("edit-group").innerHTML = `<div id="edit-group-content"></div>`;

    document.getElementById("alert-message").innerHTML = `<div id="alert-message-content">
      <p id="alert-message-text"></p>
      <button onclick="genmapper.closeAlert()">${this.translations.ok_label}</button>
    </div>`;
  }

  getInitialValue(field) {
    switch (field.header) {
      case "line_1":
        return 'New';
      case "attenders":
        return 1;
      case "believers":
      case "baptized":
      case "newlyBaptized":
        return 0;

    }

    return field.initial;
  }

  zoomIn() {
    this.zoom.scaleBy(this.svg, 1.2);

    // Support Chart v2 (Tree OrgChart) scaling.
    if (this.orgchart_container && this.orgchart_container_chart) {
      this.orgchart_container.setChartScale(this.orgchart_container_chart, 1.2);
    }
  }

  zoomOut() {
    this.zoom.scaleBy(this.svg, 1 / 1.2);

    // Support Chart v2 (Tree OrgChart) scaling.
    if (this.orgchart_container && this.orgchart_container_chart) {
      this.orgchart_container.setChartScale(this.orgchart_container_chart, 1 / 1.2);
    }
  }

  origData() {
    window.location.reload();
  }

  async popupEditGroupModal(group, global_scope = this) {
    global_scope.editGroupElement.classList.add("edit-group--active");

    const groupData = group.data;
    const action = `${$autolink.urls.route}groups/${groupData.id}/modal`;

    global_scope.injectForm(action)

    const container = document.getElementById("edit-group-content")
    const form = container.querySelector("al-ajax-form")

    form.addEventListener('loaded', (e) => {
      const parentField = container.querySelector('input[name="parent_group"]')
      if (group?.parent?.id) {
        parentField.value = group.parent.id
      }

      const script = document.querySelector('#mapbox-search-widget-js')

      if (script) {
        dtMapbox.post_id = e.detail.post.ID
        dtMapbox.post = e.detail.post
        init_mapbox()
      }

      const addButton = form.querySelector(".group__add");
      if (addButton) {
        addButton.addEventListener("click", () => global_scope.addGroup(groupData));
      }

      const closeButton = form.querySelector(".group__close")
      if (closeButton) {
        closeButton.addEventListener("click", global_scope.closeEditGroupModal.bind(global_scope));
      }
    });

    form.addEventListener("success", (e) => {
      groupData.name = e.detail.name;
      global_scope.editGroup(groupData);
    });
  }

  injectForm(action, group) {
    const container = document.getElementById("edit-group-content")
    container.innerHTML = `
              <dt-tile>
                  <div class="section__inner">
                    <al-ajax-form callback="${action}"></al-ajax-form>
                  </div>
              </dt-tile>`;
  }

  addGroup(parentGroup) {
    const action = `${$autolink.urls.route}groups/modal/create/`

    this.injectForm(action)

    const container = document.getElementById("edit-group-content")
    const form = container.querySelector("al-ajax-form")

    form.addEventListener('loaded', (e) => {
      const parentField = container.querySelector('input[name="parent_group"]')
      parentField.value = parentGroup.id

      const script = document.querySelector('#mapbox-search-widget-js')

      if (script) {
        dtMapbox.post_id = null
        dtMapbox.post = false
        init_mapbox()
      }

      const closeButton = form.querySelector(".group__close")
      if (closeButton) {
        closeButton.addEventListener("click", this.closeEditGroupModal.bind(this));
      }
    });

    form.addEventListener("success", (e) => {
      this.createNode({
        "id": e.detail.group.ID,
        "parentId": parentGroup.id,
        "title": e.detail.name,
        "name": e.detail.name,
        "line_1": e.detail.name
      });

      this.closeEditGroupModal();
    });
  }

  closeEditGroupModal() {
    this.editGroupElement.classList.remove("edit-group--active");
    this.reset();
  }

  editGroup(groupData) {
    let groupFields = {};

    template.fields.forEach((field) => {
      switch (field.type) {
        case "text":
        case "radio":
        case "checkbox":
          groupFields[field.header] = groupData[field.header];
          break;
      }

      switch (field.header) {
        case "name":
          groupFields["line_1"] = groupData.name;
          break;
      }
    });

    const node = this.findNodeById(groupData.id);
    Object.entries(groupFields).forEach(([key, value]) => {
      node.data[key] = value;
    })

    jQuery("#chart").trigger("node-updated", [
      groupData.id,
      groupData,
      groupFields,
    ]);

    this.editGroupElement.classList.remove("edit-group--active");

    window.location.reload();
  }

  reset() {
    jQuery("#chart").trigger("reset-requested");
  }

  chart_tree(tree_connections) {
    const groups = tree_connections['groups'];
    const lookup_idx = tree_connections['lookup_idx'];

    // Refresh Genmap Chart Tree.
    let container = jQuery('#genmap-v2');
    container.empty();

    const nodeTemplate = function (data) {
      return `<div class="title" data-item-id="${window.lodash.escape(data.id)}">${window.lodash.escape(data.name)}</div>
            <div class="content" style="padding-left: 5px; padding-right: 5px;">${window.lodash.escape((data['line_2']) ? data['line_2'] : '' )}</div>`;
    };

    // Capture global scope locally, required for node click flows below.
    const global_scope = this;

    // Initialise groups orgchart.
    global_scope.orgchart_container = container.orgchart({
      data: groups,
      nodeContent: 'content',
      direction: 'l2r',
      pan: true,
      zoom: true,
      zoomoutLimit: 0.25,
      nodeTemplate: nodeTemplate,
      initCompleted: function (chart) {
        global_scope.orgchart_container_chart = chart;
      }
    });

    // Initialise groups orgchart event listeners.
    container.off('click', '.node');
    container.on('click', '.node', function () {
      const node = jQuery(this);
      const node_id = node.attr('id');

      // Ensure valid node data exists.
      if (lookup_idx[node_id]) {

        // Proceed with popup edit modal display.
        global_scope.popupEditGroupModal({
          'data': lookup_idx[node_id],
          'parent': {
            'id': lookup_idx[node_id]['parentId']
          }
        }, global_scope).then(r => console.log(r));
      }
    });
  }

}

window.genMapperAutolinkClass = GenMapperAutolink;
