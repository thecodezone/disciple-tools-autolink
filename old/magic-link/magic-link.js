import {loaded} from "./js/helpers";

import "../../resources/js/components/menu.js";
import "../../resources/js/components/collapse";
import "../../resources/js/components/church";
import "../../resources/js/components/_church-tile";
import "../../resources/js/components/_lazy-reveal";
import "../../resources/js/components/churchMenu";
import "../../resources/js/components/_church-health-field";
import "../../resources/js/components/ajax-field";
import "../../resources/js/components/groups-tree";
import "../../resources/js/components/churches"
import "../../resources/js/components/_submit-button"

import "@shoelace-style/shoelace/dist/themes/light.css";
import "@shoelace-style/shoelace/dist/components/tab-group/tab-group.js";
import "@shoelace-style/shoelace/dist/components/tab/tab.js";
import "@shoelace-style/shoelace/dist/components/tab-panel/tab-panel.js";


import _locationField from "../../resources/js/_locationField";

loaded(() => {
    document.body.classList.add("dom-loaded");

    document.querySelectorAll(".location-field").forEach(_locationField);

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

        if (!counter) {
            return;
        }

        let numberBadge = counter.querySelector("dt-modal > div > span");

        if (!numberBadge) {
            return;
        }
        numberBadge.innerHTML = updatedValue;
    });
});
