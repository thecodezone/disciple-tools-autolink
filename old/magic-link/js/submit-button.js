import {DtBase, DtButton} from "@disciple.tools/web-components";
import {property} from "lit/decorators.js";
import {html} from "lit";

/**
 * @class Churches
 */
export class SubmitButton extends DtButton {
    handleClick(e) {
        if (this.clicked) {
            return false;
        }
        this.clicked = true
        super.handleClick(e)
    }

}

window.customElements.define("submit-button", SubmitButton);
