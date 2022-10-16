import { css, html, LitElement } from 'lit';
import { classMap } from 'lit/directives/class-map.js';
import { DTBase } from 'dt-web-components';

export class DtChurchHealthCircle extends DTBase {
  static get styles() {
    return css`
      .health-circle {
          display: block;
          margin: auto;
          height: 280px;
          width: 280px;
          border-radius: 100%;
          border: 3px darkgray dashed;
      }
      .health-circle__grid {
          display: inline-block;
          position: relative;
          height: 100%;
          width: 100%;
          margin-left: auto;
          margin-right: auto;
      }
      .health-circle--committed {
          border: 3px #4caf50 solid !important;
      }
      dt-church-health-icon {
        margin: auto;
          position: absolute;
          height: 50px;
          width: 50px;
          border-radius: 100%;
          font-size: 16px;
          color: black;
          text-align: center;
          font-style: italic;
          cursor: pointer;
      }
    `;
  }

  static get properties() {
    return {
      groupId: {type: Number},
      group: {type: Object, reflect: false},
      settings: {type: Object, reflect: false},
      errorMessage: {type: String, attribute: false},
      missingIcon: {type: String},
    };
  }

  /**
   * Map fields settings as an array and filter out church commitment
   */
  get metrics() {
    const settings = this.settings || [];

    if (!Object.values(settings).length) {
      return [];
    }

    const entries = Object.entries(settings);

    //We don't want to show church commitment in the circle
    return entries.filter(([key, value]) => key !== 'church_commitment');
  }

  /**
   * Fetch group data on component load if it's not provided as a property
   */
  connectedCallback() {
    super.connectedCallback();
    this.fetch();
  }

  adoptedCallback() {
    this.distributeItems();
  }

  /**
   * Position the items after the component is rendered
   */
  updated() {
    this.distributeItems();
  }

  /**
   * Fetch the group and settings data if not provided by the server
   */
  async fetch() {
    try {
      const promises = [
        this.fetchSettings(),
        this.fetchGroup()
      ];
      let [settings, group] = await Promise.all(promises);
      this.settings = settings
      this.post = group
      if (!settings) {
        this.errorMessage = 'Error loading settings'
      }
      if (!group) {
        this.errorMessage = 'Error loading group'
      }
    } catch (e) {
      console.error(e)
    }
  }

  /**
   * Fetch the group data if it's not already set
   * @returns 
   */
  fetchGroup() {
    if (this.group) {
      return Promise.resolve(this.group);
    }
    fetch(`/wp-json/dt-posts/v2/groups/${this.groupId}`).then((response) => response.json());
  }

  /**
   * Fetch the settings data if not already set
   * @returns 
   */
  fetchSettings() {
    if (this.settings) {
      return Promise.resolve(this.settings);
    }
    return fetch('/wp-json/dt-posts/v2/groups/settings').then((response) => response.json());
  }

  /**
   * Find a metric by key
   * @param {*} key 
   * @returns 
   */
  findMetric( key ) {
    const metric = this.metrics.find( (item) => item.key === key );
    return metric ? metric.value : null;
  }

  /**
   * Render the component
   * @returns 
   */
  render() {
    //Show the spinner if we don't have data
    if (!this.group || !this.metrics.length) {
      return html`<dt-spinner></dt-spinner>`;
    }

    //Setup data
    const practicedItems = this.group.health_metrics || [];
    const missingIcon = this.missingIcon ? this.missingIcon : '/dt-assets/images/groups/missing.svg';

    //Show the error message if we have one
    if (this.errorMessage) {
      html`<dt-alert type="error">${this.errorMessage}</dt-alert>`
    }

    //Render the group circle
    return html`
      <div >
        <div class=${classMap({
          'health-circle': true,
          'health-circle--committed': practicedItems.indexOf( 'church_commitment' ) !== -1
        })}>
          <div class="health-circle__grid">
            ${this.metrics.map(([key, metric]) =>
              html`<dt-church-health-icon 
                key="${key}"
                .group="${this.group}"
                .metric=${metric} 
                .active=${practicedItems.indexOf(key) !== -1}>
                </dt-church-health-icon>`
            )}
          </div>
        </div>
      </div>
    `;
  }

  /**
   * Dynamically distribute items in Church Health Circle
   * according to amount of health metric elements
   */
  distributeItems() {
    const container = this.renderRoot.querySelector('.health-circle');
    const items = container.querySelectorAll('dt-church-health-icon')

    let radius = 75;
    let item_count = items.length,
      width = container.offsetWidth,
      height = container.offsetHeight + 66,
      angle = 0,
      step = (2*Math.PI) / items.length,
      y_offset = -35;

    if ( item_count >= 5 && item_count < 7 ) {
      radius = 90;
    }

    if ( item_count >= 7 & item_count < 11 ) {
      radius = 100;
    }

    if ( item_count >= 11 ) {
      radius = 110;
    }

    if ( item_count == 3 ) {
      angle = 22.5;
    }

    Array.from(items).forEach(function(item) {
      let x = Math.round( width / 2 + radius * Math.cos(angle) - item.offsetWidth / 2 );
      let y = Math.round( height / 2 + radius * Math.sin(angle) - item.offsetHeight / 2 ) + y_offset;

      if ( item_count == 1 ) {
        x = 112.5;
        y = 68;
      }

      item.style.left = x + 'px';
      item.style.top = y + 'px';

      angle += step;
    });
  }
}


class DtChurchHealthIcon extends LitElement {
  static get styles() {
    return css`
      root {
        display: block;
      }
      .health-item img {
          height: 50px;
          width: 50px;
          filter: grayscale(1) opacity(0.75);
      }
      .health-item--active img {
          filter: none !important;
      }
    `;
  }


  static get properties() {
    return {
      key: {type: String},
      metric: { type: Object },
      group: { type: Object },
      active: { type: Boolean, reflect: true },
    }
  }

  render() {
    const { key, metric, active } = this;

    return html`<div class=${classMap({
      'health-item': true,
      'health-item--active': active
    })} title="${metric.description}" @click="${this._handleClick}">
    <img src="${metric.icon ? metric.icon : missingIcon}" >
  </div>`
  }

  async _handleClick() {
    const active = !this.active;
    this.active = active
    const payload = { 
      'health_metrics': { 
        values: [ { 
          value : this.key,
          delete: !active
        } ] 
      } 
    };
    try {
      API.update_post( 'groups', this.group.ID, payload)
    } catch (err) {
      console.log(err)
    }
  }
}


window.customElements.define('dt-church-health-icon', DtChurchHealthIcon);
window.customElements.define('dt-church-health-circle', DtChurchHealthCircle);
