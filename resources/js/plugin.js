import "../css/plugin.css";
import "foundation-sites/dist/js/foundation";

import {loaded} from "./_helpers.js";

import "./components"

import {churchCounts, locationField, handleDomLoaded} from "./dom-hooks";

loaded((document) => {
  document.querySelectorAll('body').forEach(handleDomLoaded);
  churchCounts(document);
  document.querySelectorAll(".location-field").forEach(locationField);
});
