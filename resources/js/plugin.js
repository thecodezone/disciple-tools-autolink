import "../css/plugin.css";
import "foundation-sites/dist/js/foundation";

import {loaded} from "./_helpers.js";

import "./components"

import {churchCounts, locationField, handleDomLoaded} from "./dom-hooks";
import submitFormOnEnter from './dom-hooks/submit-form-on-enter.js';

loaded((document) => {
  document.querySelectorAll('form').forEach(submitFormOnEnter);
  document.querySelectorAll('body').forEach(handleDomLoaded);
  churchCounts(document);
  document.querySelectorAll(".location-field").forEach(locationField);

});

