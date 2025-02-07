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

                          if (button.getAttribute('context') === 'alert') {
                            return; // Skip navigation for alert context
                          }
                          event.preventDefault();
                          const link = button.shadowRoot ? button.shadowRoot.querySelector( 'a' ) : null;
                          if (link) {
                            window.location.href = link.href;
                          }
                      });
                  });

                document.getElementById('copyTextElement').addEventListener('click', function() {
                  const link = this.getAttribute('value');
                  showCopyMessage(link);
                });

                function showCopyMessage(link) {
                  let copyMessage = document.getElementById("copyMessage");
                  let helpMessage = document.getElementById("help-text");
                  let toast = document.getElementById("copyToast");

                  copyMessage.style.display = "block";
                  helpMessage.style.display = "none";

                  navigator.clipboard.writeText(link).then(() => {
                    // Show toast
                    toast.classList.add("show");
                    // Auto-hide after 3 seconds
                    setTimeout(() => hideCopyToast(), 3000);
                  });
                }
                document.getElementById('hide-copy-toast').addEventListener('click', function() {
                  hideCopyToast();
                });

                function hideCopyToast() {
                  let toast = document.getElementById("copyToast");
                  toast.classList.remove("show");
                }
              });
