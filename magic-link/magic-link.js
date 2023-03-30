import { loaded } from "./js/helpers";

import "./js/menu.js";
import "./js/collapse";
import "./js/church";
import "./js/church-tile";
import "./js/lazyReveal";
import "./js/churchMenu";

import "@shoelace-style/shoelace/dist/themes/light.css";
import "@shoelace-style/shoelace/dist/components/tab-group/tab-group.js";
import "@shoelace-style/shoelace/dist/components/tab/tab.js";
import "@shoelace-style/shoelace/dist/components/tab-panel/tab-panel.js";

import locationField from "./js/locationField";

loaded(() => {
  document.body.classList.add("dom-loaded");

  document.querySelectorAll(".location-field").forEach(locationField);

  //Event Listener to update the church count number when the user updated the input in the modal.
  document.addEventListener("change", (event) => {
    let postID = event.srcElement.postID;
    if (!event.detail) {
      return;
    }
    let updatedValue = event.detail.newValue;
    let counter = document.querySelector(
      '[data-churchid="' +
        postID +
        '"][data-field="' +
        event.srcElement.name +
        '"]'
    );

    let numberBadge = counter.querySelector("dt-modal > div > span");
    numberBadge.innerHTML = updatedValue;
  });
});
