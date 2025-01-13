import "../css/plugin.css";
import "foundation-sites/dist/js/foundation";

import {loaded} from "./_helpers.js";

import "./components"

import {churchCounts, locationField, handleDomLoaded} from "./dom-hooks";
import submitFormOnEnter from './dom-hooks/submit-form-on-enter.js';

loaded((document) => {
  document.querySelectorAll( 'form' ).forEach( submitFormOnEnter );
  document.querySelectorAll( 'body' ).forEach( handleDomLoaded );
  churchCounts( document );
  document.querySelectorAll( ".location-field" ).forEach( locationField );
  // Force all dt-button elements with an href attribute to navigate to the href when clicked
  document.querySelectorAll( 'dt-button[href]' ).forEach(button => {
    button.addEventListener('click', (event) => {
      event.preventDefault();
      const link = button.shadowRoot ? button.shadowRoot.querySelector( 'a' ) : null;
      if (link) {
        window.location.href = link.href;
      }
    });
  });
});

