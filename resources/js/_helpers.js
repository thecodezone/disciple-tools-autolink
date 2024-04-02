/**
 * Executes the callback function when the document has finished loading.
 *
 * @param {function} callback - The function to be executed.
 */
export const loaded = function (callback) {
  if (document.readyState === 'complete') {
    callback(document);
  } else {
    document.onreadystatechange = function () {
      if (document.readyState === "complete") {
        callback(document);
      }
    }
  }
}

export const route_url = ( path ) => {
  return `${$autolink.urls.route.replace(/\/$/, "").trim()}/${path.replace(/^\/|\/$/g, '').trim()}`
}

export const api_url = ( path ) => {
  return route_url("api/" + path)
}

export const form_data_to_object = (formData) => {
  let data = {};

  for (let [key, value] of formData.entries()) {
    // Check if the key contains brackets (indicating an array)
    if (key.includes('[') && key.includes(']')) {
      // Extract the field name (for example, extract 'leaders' from 'leaders[0]')
      let field = key.substring(0, key.indexOf('['));

      // Initialize the field with an array if it doesn't exist yet
      if (!(field in data)) {
        data[field] = [];
      }

      // Add the value to the field array
      data[field].push(value);
    } else {
      data[key] = value;
    }
  }

  return data;
}