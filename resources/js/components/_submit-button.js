import { DtButton } from "@disciple.tools/web-components";
import { customElement } from "lit/decorators.js";

@customElement("al-submit-button")
export class SubmitButton extends DtButton {
    handleClick(e) {
        if (this.clicked) {
            return false;
        }
        this.clicked = true
        super.handleClick(e)
    }
}
