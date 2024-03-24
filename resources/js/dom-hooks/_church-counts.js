export default (el) => {
  el.addEventListener("change", (event) => {
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
}