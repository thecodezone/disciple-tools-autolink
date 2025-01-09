class GenMapper {
  constructor() {
    this.plugin_uri = window.wpApiGenmapper.plugin_uri;
    this.showMetrics = window.genApiTemplate.show_metrics === "1";
    this.showIcons = window.genApiTemplate.show_icons === "1";
    this.translations = window.wpApiGenmapper.translation;

    this.language = "en";

    this.margin = {top: 50, right: 30, bottom: 50, left: 30};

    this.updateDOMafterLangSwitch();

    this.zoom = d3
    .zoom()
    .scaleExtent([0.15, 2])
    .on("zoom", function zoomed() {
      d3.select("g").attr("transform", d3.event.transform);
    });

    this.setSvgHeight();
    this.svg = d3
    .select("#genmapper-graph-svg")
    .call(this.zoom)
    .on("dblclick.zoom", null);
    this.g = this.svg.append("g").attr("id", "maingroup");
    this.gLinks = this.g.append("g").attr("class", "group-links");
    this.gLinksText = this.g.append("g").attr("class", "group-links-text");
    this.gNodes = this.g.append("g").attr("class", "group-nodes");

    this.csvHeader =
      template.fields.map((field) => field.header).join(",") + "\n";
    this.initialCsv =
      this.csvHeader +
      template.fields.map((field) => this.getInitialValue(field)).join(",");
    this.data = this.parseCsvData(this.initialCsv);
    this.nodes;

    this.origPosition();
    this.redraw(template);

    this.alertElement = document.getElementById("alert-message");
    this.editGroupElement = document.getElementById("edit-group");

    this.setKeyboardShorcuts();

    document.getElementsByTagName("body")[0].onresize = this.setSvgHeight;
  }

  static getFieldValueForRadioType(field, d) {
    let fieldValue = window.lodash.filter(field.values, {
      header: d.data[field.header],
    })[0];
    if (typeof fieldValue === "undefined") {
      fieldValue = window.lodash.filter(field.values, {
        header: field.initial,
      })[0];
    }
    return fieldValue;
  }

  // Beginning of function definitions
  setKeyboardShorcuts() {
    document.addEventListener("keyup", (e) => {
      if (e.keyCode === 27) {
        if (this.alertElement.classList.contains("alert-message--active")) {
          this.alertElement.classList.remove("alert-message--active");
        } else {
          this.editGroupElement.classList.remove("edit-group--active");
        }
      } else if (e.keyCode === 13) {
        // hitting enter is like submitting changes in the edit window
        if (this.editGroupElement.classList.contains("edit-group--active")) {
          document.getElementById("edit-submit").click();
        }
      }
    });
  }

  setSvgHeight() {
    const windowHeight = document.documentElement.clientHeight;
    const leftMenuHeight = document.getElementById("left-menu").clientHeight;
    const height = Math.max(windowHeight, leftMenuHeight + 10);
    d3.select("#genmapper-graph-svg").attr("height", height);
  }

  loadHTMLContent() {

    document.getElementById("left-menu").innerHTML = `<div id="template-logo">
    <!--<button onclick="genmapper.introSwitchVisibility()" class="hint--rounded hint--right" aria-label="Help / About">
      <img src="${this.plugin_uri}/charts/icons/266-question.svg">
      </button> -->
    <button onclick="genmapper.origData();" class="hint--rounded hint--right" aria-label="${this.translations.reset_label}"><img src="${this.plugin_uri}/charts/icons/refresh.svg"></i></button>
    <button onclick="genmapper.zoomIn();" class="hint--rounded hint--right" aria-label="${this.translations.zoom_in_label}"><img src="${this.plugin_uri}/charts/icons/136-zoom-in.svg"></i></button>
    <button onclick="genmapper.zoomOut();" class="hint--rounded hint--right" aria-label="${this.translations.zoom_out_label}"><img src="${this.plugin_uri}/charts/icons/137-zoom-out.svg"></i></button>
  `;

    document.getElementById(
      "edit-group"
    ).innerHTML = `<div id="edit-group-content"></div>`;
    document.getElementById(
      "alert-message"
    ).innerHTML = `<div id="alert-message-content">
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
  }

  zoomOut() {
    this.zoom.scaleBy(this.svg, 1 / 1.2);
  }

  origData() {
    this.data = this.masterData;
    this.redraw(template);
    this.origPosition(true);
  }

  origPosition(atRoot = false) {
    this.zoom.scaleTo(this.svg, 1);
    const origX =
      this.margin.left +
      document.getElementById("genmapper-graph").clientWidth / 2;
    const origY = this.margin.top - (atRoot ? 150 : 0);
    const parsedTransform = this.parseTransform(this.g.attr("transform"));
    this.zoom.translateBy(
      this.svg,
      origX - parsedTransform.translate[0],
      origY - parsedTransform.translate[1]
    );
  }

  onLoad(fileInputElementId) {
    const fileInput = document.getElementById(fileInputElementId);
    fileInput.value = "";
    fileInput.click();
  }

  displayAlert(message) {
    this.alertElement.classList.add("alert-message--active");
    document.getElementById("alert-message-text").innerHTML = message;
  }

  closeAlert() {
    this.alertElement.classList.remove("alert-message--active");
    document.getElementById("alert-message-text").innerHTML = null;
  }

  introSwitchVisibility() {
    document.getElementById("intro").classList.toggle("intro--active");
  }

  async popupEditGroupModal(group) {
    this.editGroupElement.classList.add("edit-group--active");
    const groupData = group.data;
    const action = `${$autolink.urls.route}groups/${groupData.id}/modal`;
    this.injectForm(action)
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
        addButton.addEventListener("click", () => this.addGroup(groupData));
      }

      const closeButton = form.querySelector(".group__close")
      if (closeButton) {
        closeButton.addEventListener("click", this.closeEditGroupModal.bind(this));
      }
    });

    form.addEventListener("success", (e) => {
      groupData.name = e.detail.name;
      this.editGroup(groupData);
    });
  }

  injectForm(action, group) {
    const container = document.getElementById("edit-group-content")
    container.innerHTML = `
              <dt-tile>
                  <div class="section__inner">
                    <al-ajax-form callback="${action}"></al-ajax-form>
                  </div>
              </dt-tile>
        `
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
      const newNodeData = {};
      newNodeData["id"] = e.detail.group.ID;
      newNodeData["parentId"] = parentGroup.id;
      newNodeData["title"] = e.detail.name;
      newNodeData["name"] = e.detail.name;
      newNodeData["line_1"] = e.detail.name;
      this.createNode(newNodeData);
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
    this.redraw(template);
  }

  openRecord(d) {
    let id = d.data.id;
    let url = genApiTemplate.app_url;
    let query = `?action=group&post=${id}&return=${window.location.href}`;

    var win = window.open(url + query, "_blank");
    win.focus();
  }

  redraw(template) {
    // declares a tree layout and assigns the size
    const tree = d3
    .tree()
    .nodeSize([
      template.settings.nodeSize.width,
      template.settings.nodeSize.height,
    ])
    .separation(function separation(a, b) {
      return a.parent === b.parent ? 1 : 1.2;
    });

    const stratifiedData = d3.stratify()(this.data);
    this.nodes = tree(stratifiedData);
    // update the links between the nodes
    const link = this.gLinks
    .selectAll(".link")
    .data(this.nodes.descendants().slice(1));

    link.exit().remove();

    link
    .enter()
    .append("path")
    .merge(link)
    .attr("class", function (d) {
      return d.parent.id == 0 ? "link-dummy" : "link"; // #customtft dummy root node
    })
    .attr("d", function (d) {
      return (
        "M" +
        d.x +
        "," +
        d.y +
        "C" +
        d.x +
        "," +
        (d.y + (d.parent.y + boxHeight)) / 2 +
        " " +
        d.parent.x +
        "," +
        (d.y + (d.parent.y + boxHeight)) / 2 +
        " " +
        d.parent.x +
        "," +
        (d.parent.y + boxHeight)
      );
    });

    const node = this.gNodes.selectAll(".node").data(this.nodes.descendants());

    node.enter().text(function (d) {
    });

    node.exit().remove();

    // NEW ELEMENTS
    const newGroup = node.enter().append("g");

    // append SVG elements without fields
    Object.keys(template.svg).forEach((svgElement) => {
      const svgElementValue = template.svg[svgElement];
      const element = newGroup.append(svgElementValue["type"]);
      element.attr("class", "node-" + svgElement);
    });

    // append SVG elements related to fields
    template.fields.forEach((field) => {
      if (field.svg) {
        const element = newGroup.append(field.svg["type"]);
        element.attr("class", "node-" + field.header);
        Object.keys(field.svg.attributes).forEach((attribute) => {
          element.attr(attribute, field.svg.attributes[attribute]);
        });
        if (field.svg.style) {
          Object.keys(field.svg.style).forEach((styleKey) => {
            element.style(styleKey, field.svg.style[styleKey]);
          });
        }
      }
    });

    //this.appendRebaseButton(newGroup);
    //this.appendAddButton(newGroup);

    // UPDATE including NEW
    const nodeWithNew = node.merge(newGroup);
    nodeWithNew
    .attr("class", function (d) {
      return (
        "node" +
        (d.data.id === 0
          ? " node--dummyroot"
          : d.data.active
            ? " node--active"
            : " node--inactive")
      );
    })
    .attr("class", (d) => {
      const classes = ["node"];
      if (d.data.id === 0) {
        classes.push("node--dummyroot");
      }
      classes.push(d.data.active ? "node--active" : "node--inactive");
      if (this.showIcons) {
        if (d.data.health_metrics_baptism) {
          classes.push("health--baptism");
        }
        if (d.data.health_metrics_baptism) {
          classes.push("health--baptism");
        }
        if (d.data.health_metrics_bible) {
          classes.push("health--bible");
        }
        if (d.data.health_metrics_commitment) {
          classes.push("health--commitment");
        }
        if (d.data.health_metrics_communion) {
          classes.push("health--communion");
        }
        if (d.data.health_metrics_giving) {
          classes.push("health--giving");
        }
        if (d.data.health_metrics_leaders) {
          classes.push("health--leaders");
        }
        if (d.data.health_metrics_fellowship) {
          classes.push("health--fellowship");
        }
        if (d.data.health_metrics_praise) {
          classes.push("health--praise");
        }
        if (d.data.health_metrics_prayer) {
          classes.push("health--prayer");
        }
        if (d.data.health_metrics_sharing) {
          classes.push("health--sharing");
        }
        if (d.data.health_metrics_sharing) {
          classes.push("health--sharing");
        }
      }
      return classes.join(" ");
    })
    .attr("transform", function (d) {
      return "translate(" + d.x + "," + d.y + ")";
    })
    .on("click", (d) => {
      this.popupEditGroupModal(d);
    });

    nodeWithNew.select(".removeNode svg").on("click", (d) => {
      console.log("removeNode");
      this.removeNode(d);
      d3.event.stopPropagation();
    });
    nodeWithNew.select(".rebaseNode svg").on("click", (d) => {
      console.log("rebaseNode");
      this.rebaseOnNode(d);
      d3.event.stopPropagation();
    });

    nodeWithNew.select(".addNode svg").on("click", (d) => {
      this.addNode(d);
      d3.event.stopPropagation();
    });

    // refresh class and attributes in SVG elements without fields
    // in order to remove any additional classes or settings from inherited fields
    Object.keys(template.svg).forEach((svgElement) => {
      const svgElementValue = template.svg[svgElement];
      const element = nodeWithNew
      .select(".node-" + svgElement)
      .attr("class", "node-" + svgElement);
      Object.keys(svgElementValue.attributes).forEach((attribute) => {
        element.attr(attribute, svgElementValue.attributes[attribute]);
      });
    });

    // update node elements which have SVG in template
    template.fields.forEach((field) => {
      if (field.svg) {
        const element = nodeWithNew.select(".node-" + field.header);
        this.updateSvgForFields(field, element);
      }
      if (field.inheritsFrom) {
        const element = nodeWithNew.select(".node-" + field.inheritsFrom);
        this.updateFieldWithInherit(field, element);
      }
    });
  }

  updateFieldWithInherit(field, element) {
    if (!element.empty()) {
      if (field.type === "checkbox") this.updateCheckboxField(field, element);
      if (field.type === "radio") this.updateRadioField(field, element);
    }
  }

  updateCheckboxField(field, element) {
    // add class to the element which the field inherits from
    if (field.class) {
      element.attr("class", function (d) {
        const checked = d.data[field.header];
        const class_ = checked
          ? field.class.checkedTrue
          : field.class.checkedFalse;
        return this.classList.value + " " + class_;
      });
    }
    if (
      typeof field.attributes !== "undefined" &&
      typeof field.attributes.rx !== "undefined"
    ) {
      element.attr("rx", function (d) {
        const checked = d.data[field.header];
        const rxObj = field.attributes.rx;
        const rx = checked ? rxObj.checkedTrue : rxObj.checkedFalse;
        return String(rx);
      });
    }
  }

  updateRadioField(field, element) {
    // add class to the element which the field inherits from
    element.attr("class", function (d) {
      const fieldValue = GenMapper.getFieldValueForRadioType(field, d);
      if (fieldValue.class) {
        return this.classList.value + " " + fieldValue.class;
      } else {
        return this.classList.value;
      }
    });
    element.attr("rx", function (d) {
      const fieldValue = GenMapper.getFieldValueForRadioType(field, d);
      if (
        typeof fieldValue.attributes !== "undefined" &&
        typeof fieldValue.attributes.rx !== "undefined"
      ) {
        return String(fieldValue.attributes.rx);
      } else {
        return this.rx.baseVal.valueAsString;
      }
    });
  }

  updateSvgForFields(field, element) {
    element.text(function (d) {
      return d.data[field.header];
    });
    if (field.svg.type === "image") {
      element.style("display", function (d) {
        return d.data[field.header] ? "block" : "none";
      });
    }
  }

  appendRemoveButton(group) {
    group
    .append("g")
    .attr("class", "removeNode")
    .append("svg")
    .html(
      '<rect x="40" y="0" rx="7" width="25" height="40">' +
      "<title>" +
      __("Delete group &amp; subtree", "disciple_tools") +
      "</title>" +
      "</rect>" +
      '<line x1="46" y1="13.5" x2="59" y2="26.5" stroke="white" stroke-width="3"></line>' +
      '<line x1="59" y1="13.5" x2="46" y2="26.5" stroke="white" stroke-width="3"></line>'
    );
  }

  appendRebaseButton(group) {
    group.append("g").attr("class", "rebaseNode").append("svg").html(`
        <rect x="40" y="0" rx="7" width="25" height="40">
          <title>Rebase</title>
        </rect>
        <line x1="46" y1="13.5" x2="46" y2="26.5" stroke="white" stroke-width="3"></line>
        <line x1="59" y1="13.5" x2="46" y2="13.5" stroke="white" stroke-width="3"></line>
        <line x1="59" y1="26.5" x2="59" y2="13.5" stroke="white" stroke-width="3"></line>
        <line x1="59" y1="26.5" x2="46" y2="26.5" stroke="white" stroke-width="3"></line>
      `);
  }

  appendAddButton(group) {
    group.append("g").attr("class", "addNode").append("svg").html(`
        <rect x="40" y="40" rx="7" width="25" height="40">
          <title> Add child</title>
        </rect>
        <line x1="45" y1="60" x2="60" y2="60" stroke="white" stroke-width="3"></line>
        <line x1="52.5" y1="52.5" x2="52.5" y2="67.5" stroke="white" stroke-width="3"></line>
      `);
  }

  addNode(d) {
    let tmp = window.lodash.cloneDeep(this.masterData);
    tmp.push({parentId: d.data.id});
    try {
      this.validTree(tmp);
    } catch (err) {
      this.displayAlert(
        ` Cannot add a child to this node. Check if node has 2 parents.`
      );
      return;
    }
    // this.validTree(tmp)
    jQuery("#chart").trigger("add-node-requested", [d]);
  }

  /* required: id, parentId, name
   *
   */
  createNode(newNode) {
    template.fields.forEach((field) => {
      if (!newNode[field.header]) {
        newNode[field.header] = this.getInitialValue(field);
      }
    });
    this.data.push(newNode);
    this.redraw(template);
  }

  findNodeById(id) {
    let nodes = this.nodes.descendants().filter((x) => x.data.id === id);
    if (nodes.length === 1) {
      return nodes[0];
    } else {
      return false;
    }
  }

  findNewId() {
    const ids = window.lodash.map(this.data, function (row) {
      return row.id;
    });
    return this.findNewIdFromArray(ids);
  }

  /*
   * Find smallest int >= 0 not in array
   */
  findNewIdFromArray(arr) {
    // copy and sort
    arr = arr.slice().sort(function (a, b) {
      return a - b;
    });
    let tmp = 0;
    for (let i = 0; i < arr.length; i++) {
      if (arr[i] >= 0) {
        // ids must be >= 0
        if (arr[i] === tmp) {
          tmp += 1;
        } else {
          break;
        }
      }
    }
    return tmp;
  }

  rebaseOnNode(d) {
    //fetch from server because the local tree might not be complete
    jQuery("#chart").trigger("rebase-node-requested", [d]);
    // const tmpData = window.lodash.cloneDeep(d.descendants().map(x => x.data))
    // tmpData[0].parentId = ''
    // this.data = tmpData
    // this.redraw(template)
    // this.origPosition()
  }

  rebaseOnNodeID(id) {
    this.data = this.masterData;
    this.redraw(template);
    let node = this.findNodeById(id);
    if (node) {
      this.rebaseOnNode(node);
    }
  }

  reset() {
    jQuery("#chart").trigger("reset-requested");
  }

  removeNode(d) {
    console.log("remove");
    // if (!d.parent) {
    //   this.displayAlert(i18next.t('messages.errDeleteRoot'))
    // } else {
    //   let confirmMessage
    //   if (!d.children) {
    //     confirmMessage = i18next.t('messages.confirmDeleteGroup', {groupName: d.data.name})
    //   } else {
    //     confirmMessage = i18next.t('messages.confirmDeleteGroupWithChildren', {groupName: d.data.name})
    //   }
    //   if (window.confirm(confirmMessage)) {
    //     this.deleteAllDescendants(d)
    //     const nodeToDelete = window.lodash.filter(this.data, {id: d.data.id})
    //     if (nodeToDelete) {
    //       this.data = window.lodash.without(this.data, nodeToDelete[0])
    //     }
    //   }
    // }
    // this.editGroupElement.classList.remove('edit-group--active')
    // this.redraw(template)
  }

  parseCsvData(csvData) {
    return d3.csvParse(csvData, function (d) {
      const parsedId = parseInt(d.id);
      if (parsedId < 0 || isNaN(parsedId)) {
        throw new Error("Group id must be integer >= 0.");
      }
      const parsedLine = {};
      parsedLine["id"] = parsedId;
      parsedLine["parentId"] = d.parentId !== "" ? parseInt(d.parentId) : "";
      template.fields.forEach((field) => {
        if (field.type === "checkbox") {
          const fieldValue = d[field.header].toUpperCase();
          parsedLine[field.header] = !!["TRUE", "1"].includes(fieldValue);
        } else if (field.type) {
          parsedLine[field.header] = d[field.header];
        }
      });
      return parsedLine;
    });
  }

  parseTransform(a) {
    const b = {};
    for (let i in (a = a.match(/(\w+\((-?\d+.?\d*e?-?\d*,?)+\))+/g))) {
      const c = a[i].match(/[\w.-]+/g);
      b[c.shift()] = c;
    }
    return b;
  }

  importJSON(jsonString, initial = false) {
    let tree = {};
    if (typeof jsonString === "string") {
      tree = JSON.parse(jsonString);
    } else {
      tree = jsonString;
    }
    try {
      this.validTree(tree);
    } catch (err) {
      this.displayImportError(err);
      console.log(tree);
      console.log(err);
      return;
    }
    if (initial) {
      this.masterData = tree;
    }
    this.data = tree;
    this.redraw(template);
  }

  /**
   * Checks if parsedCsv creates a valid tree.
   * If not, raises error
   */
  validTree(parsedCsv) {
    const treeTest = d3.tree();
    const stratifiedDataTest = d3.stratify()(parsedCsv);
    treeTest(stratifiedDataTest);
  }

  displayImportError(err, msg) {
    if (
      err.toString().includes(">= 0.") ||
      err.toString().includes("Wrong type")
    ) {
      this.displayAlert(` Error setting up the graph.  <br>${err.toString()}`);
    } else {
      this.displayAlert(` Error setting up the graph
        <br><br>
        This can be caused by circular connection data.
        Check that no child has two parents that are related. A group and a grand child group can't both be the parent of another group.
      `);
    }
  }

  deleteAllDescendants(d) {
    let idsToDelete = window.lodash.map(d.children, function (row) {
      return parseInt(row.id);
    });
    while (idsToDelete.length > 0) {
      const currentId = idsToDelete.pop();
      const childrenIdsToDelete = window.lodash.map(
        window.lodash.filter(this.data, {parentId: currentId}),
        function (row) {
          return row.id;
        }
      );
      idsToDelete = idsToDelete.concat(childrenIdsToDelete);
      const nodeToDelete = window.lodash.filter(this.data, {
        id: currentId,
      });
      if (nodeToDelete) {
        this.data = window.lodash.without(this.data, nodeToDelete[0]);
      }
    }
  }

  addFieldsToEditWindow(template) {
    template.fields.forEach((field) => {
      if (field.type) {
        // add table row
        const tr = d3
        .select("#edit-group-content")
        .select("form")
        .select("table")
        .append("tr");
        // add left column
        const fieldDesciption = field.label + ":";
        tr.append("td").text(fieldDesciption).attr("class", "left-field");
        // add right column
        const td = tr.append("td").attr("class", "right-field");
        if (field.type === "radio") {
          for (let value of field.values) {
            const valueDescription = value.label;
            td.append("input")
            .attr("type", field.type)
            .attr("name", field.header)
            .attr("value", value.header)
            .attr("id", "edit-" + field.header + "-" + value.header);
            td.append("span").html(valueDescription);
            td.append("br");
          }
        } else {
          td.append("input")
          .attr("type", field.type)
          .attr("name", field.header)
          .attr("id", "edit-" + field.header);
        }
      }
    });
  }

  updateDOMafterLangSwitch() {
    this.loadHTMLContent();
    this.addFieldsToEditWindow(template);
    // document.getElementById('lang-' + this.language).className = 'current-lang'
    // d3.select('#project-name')
    //   .attr('aria-label', i18next.t('messages.editProjectName') + ': ' + this.projectName)
    //   .on('click', () => {
    //     let userInput = window.prompt(i18next.t('messages.editProjectName'), this.projectName)
    //     if (userInput === null) { return }
    //     userInput = userInput.trim()
    //     if (userInput === '') { this.displayAlert(i18next.t('messages.errProjectNameEmpty')) } else {
    //       this.projectName = userInput
    //       d3.select('#project-name')
    //         .attr('aria-label', i18next.t('messages.editProjectName') + ': ' + this.projectName)
    //     }
    //   })
    this.editFieldElements = {};
    template.fields.forEach((field) => {
      if (field.type === "radio") {
        field.values.forEach((value) => {
          this.editFieldElements[field.header + "-" + value.header] =
            document.getElementById(
              "edit-" + field.header + "-" + value.header
            );
        });
      } else if (field.type) {
        this.editFieldElements[field.header] = document.getElementById(
          "edit-" + field.header
        );
      }
    });
    this.editParentElement = document.getElementById("edit-parent");
  }
}


window.genMapperClass = GenMapper;
