describe("RT015_user_can_update_survey_answers_from_menu", () => {
  let shared_data = {
    dt_autolink_number_of_leaders_coached: "1",
    dt_autolink_number_of_churches_led: "1",
  };

  before(() => {
    cy.npmAutoLinkInit();
  });

  // XYZ.....
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

      // Click the "Menu" button
      cy.get("al-menu").click();

      cy.get("al-menu")
        .shadow()
        .find('a[title="Update Survey Answers"]')
        .click();

      cy.get('dt-text[name="dt_autolink_number_of_leaders_coached"]')
        .shadow()
        .find("input")
        .type(shared_data.dt_autolink_number_of_leaders_coached);

      cy.get("dt-button.pagination__next").click();

      cy.get('dt-text[name="dt_autolink_number_of_churches_led"]')
        .shadow()
        .find("input")
        .type(shared_data.dt_autolink_number_of_churches_led);

      cy.get("dt-button.pagination__next").click();

      cy.get("al-menu").click();

      cy.get("al-menu").shadow().find('a[title="Log Out"]').click();
    });
  });
});
