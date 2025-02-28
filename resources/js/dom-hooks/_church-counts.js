export default (el) => {
  const validateInputField = (inputField) => {
    if (inputField) {
      inputField.addEventListener("keypress", function (evt) {
        if (
          (evt.which != 8 && evt.which != 0 && evt.which < 48) ||
          evt.which > 57
        ) {
          evt.preventDefault();
        }
      });

      inputField.addEventListener("input", function (evt) {
        const value = evt.target.value;

        if (/e/i.test(value)) {
          evt.target.value = value.replace(/e/gi, "");
        }
      });
    }
  };

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
        '"]',
    );

    if (!counter) {
      return;
    }

    let numberBadge = counter.querySelector("dt-modal > div > span");

    if (!numberBadge) {
      return;
    }

    numberBadge.innerHTML = updatedValue;

    // Validate the input field
    const inputField = event.srcElement;
    validateInputField(inputField);
  });
};
