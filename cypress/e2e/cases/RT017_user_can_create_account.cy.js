describe("RT017_user_can_create_account", () => {
  let shared_data = {
    username: `test`,
    email: "test@gmail.com",
    password: "password",
  };

  // before(() => {
  //   cy.npmAutoLinkInit();
  // });

  // create user account in autolink plugin
  it("Create user account in autolink plugin", () => {
    cy.session("dt_frontend_login_and_obtain_autolink_plugin_ml", () => {
      cy.visit("/autolink/login");

      cy.get('dt-button[context="link"]').contains("Create Account").click();

      cy.get('dt-text[name="username"]')
        .shadow()
        .find("input")
        .type(shared_data.username);

      //email
      cy.get('dt-text[name="email"]')
        .shadow()
        .find("input")
        .type(shared_data.email);
      //password
      cy.get('dt-text[name="password"]')
        .shadow()
        .find("input")
        .type(shared_data.password);

      //conform password
      cy.get('dt-text[name="confirm_password"]')
        .shadow()
        .find("input")
        .type(shared_data.password);

      cy.get('dt-button[context="success"]').shadow().find("button").click();

      cy.get("form").submit();

      cy.get("al-menu").click();

      cy.get("al-menu").shadow().find('a[title="Log Out"]').click();
    });
  });

  // login
});
