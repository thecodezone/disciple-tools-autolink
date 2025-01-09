export default (form) => {
  form.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault()
      form.submit()
      return false;
    }
  });
};
