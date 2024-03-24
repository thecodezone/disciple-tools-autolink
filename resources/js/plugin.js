console.log('here')
import "../css/plugin.css";

import {loaded} from "./_helpers.js";

import "./components"

import "@shoelace-style/shoelace/dist/themes/light.css";
import "@shoelace-style/shoelace/dist/components/tab-group/tab-group.js";
import "@shoelace-style/shoelace/dist/components/tab/tab.js";
import "@shoelace-style/shoelace/dist/components/tab-panel/tab-panel.js";

import {churchCounts, locationField, handleDomLoaded} from "./dom-hooks";

loaded((document) => {
  document.querySelectorAll('body').forEach(handleDomLoaded);
  churchCounts(document);
  document.querySelectorAll(".location-field").forEach(locationField);
});

