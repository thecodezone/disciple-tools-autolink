import { loaded } from "./js/helpers";

loaded(() => {
  document.body.classList.add("dom-loaded");
  //Event Listener to update the church count number when the user updated the input in the modal.
  document.addEventListener("change", (event) => {
    let postID = event.srcElement.postID;
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

import "@disciple.tools/web-components";
import "./js/menu.js";
import "./js/button.js";
import "./js/copyText";
import "./js/churchHealthCircle";
import "./js/collapse";
import "./js/church";
import "./js/church-tile";
// import "./js/icon";
