export default (el) => {
  const mapboxInput = el.querySelector("#mapbox-search"); // Callback function to execute when mutations are observed
  const field = el.querySelector("input[name='location']");

  const proxyMapbox = () => {
    if (window.location_data === undefined) {
      field.value = "";
    } else {
      field.value = JSON.stringify(window.location_data);
    }
    setTimeout(proxyMapbox, 500);
  };

  setTimeout(proxyMapbox, 500);
};
