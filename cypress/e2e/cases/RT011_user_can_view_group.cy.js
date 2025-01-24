describe("RT011_user_can_view_group", () => {
  let shared_data = {
    name: `Cypress Test Group`,
    start_date: "2023-10-01",
  };

  // before(() => {
  //   cy.npmAutoLinkInit();
  // });

  // Login to D.T frontend and obtain autolink plugin magic link.
  it("Login to D.T frontend and obtain autolink plugin magic link.", () => {
    cy.session("dt_frontend_login_and_obtain_autolink_plugin_ml", () => {
      /**
       * Ensure uncaught exceptions do not fail test run; however, any thrown
       * exceptions must not be ignored and a ticket must be raised, in order
       * to resolve identified exception.
       *
       * TODO:
       *  - Resolve any identified exceptions.
       */

      cy.on("uncaught:exception", () => {
        // Returning false here prevents Cypress from failing the test
        return false;
      });

      // Capture admin credentials.
      const dt_config = cy.config("dt");
      const username = dt_config.credentials.admin.username;
      const password = dt_config.credentials.admin.password;

      // Fetch the home screen plugin magic link associated with admin user.
      cy.loginAutoLink(username, password);

      // Click the "My Groups" button
      cy.get('dt-button[context="inactive"]').contains("My Groups").click();

      // Click the "churches__add" button
      cy.get(".churches__add").click();

      // Fill the form fields with shared data
      cy.get('dt-text[name="name"]')
        .shadow()
        .find("input")
        .type(shared_data.name);

      cy.get('dt-date[name="start_date"]')
        .shadow()
        .find("input")
        .type(shared_data.start_date);

      cy.get("body").click();
      // Submit the form
      cy.get("form.create-group").submit();

      cy.get(
        `.churches__groups al-church-tile[title="${shared_data.name}"]`,
      ).within(() => {
        // Open the menu and click "Edit"
        cy.get("al-church-menu").click({ force: true });

        cy.get('dt-button[context="primary"]')
          .contains("View")
          .click({ force: true });
      });

      // Click the "Back to AutoLink" button
      cy.get('dt-button[title="Back to AutoLink"]').click();

      cy.get('dt-button[context="inactive"]').contains("My Groups").click();

      cy.get(
        `.churches__groups al-church-tile[title="${shared_data.name}"]`,
      ).within(() => {
        // Open the menu and click "Edit"
        cy.get("al-church-menu").click({ force: true });

        cy.get('dt-button[context="alert"]')
          .contains("Delete Group")
          .click({ force: true });
      });
    });
  });
});
