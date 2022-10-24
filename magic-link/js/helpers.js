export const loaded = function (callback) {
    if (document.readyState === 'complete') {
        callback();
    } else {
        document.onreadystatechange = function () {
            if (document.readyState === "complete") {
                callback();
            }
        }
    }
}