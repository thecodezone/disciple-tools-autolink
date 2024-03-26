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