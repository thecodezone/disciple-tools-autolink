describe('RT006_user_can_access_frontend_coaching_tree_view', () => {

  let shared_data = {};

  before(() => {
    //cy.npmAutoLinkInit();
  })

  // Login to AutoLink Frontend and confirm coaching-tree view.
  it('Login to AutoLink Frontend and confirm coaching-tree view.', () => {
    cy.session(
      'login_to_autoLink_frontend_confirm_coaching_tree_view',
      () => {

        /**
         * Ensure uncaught exceptions do not fail test run; however, any thrown
         * exceptions must not be ignored and a ticket must be raised, in order
         * to resolve identified exception.
         *
         * TODO:
         *  - Resolve any identified exceptions.
         */

        cy.on('uncaught:exception', (err, runnable) => {
          // Returning false here prevents Cypress from failing the test
          return false
        })

        // Capture admin credentials.
        const dt_config = cy.config('dt')
        const username = dt_config.credentials.admin.username
        const password = dt_config.credentials.admin.password

        // Login and navigate to frontend autolink view.
        cy.loginAutoLink(username, password)

        // Navigate to AutoLink frontend coaching-tree page.
        cy.visit('/autolink/coaching-tree')

        // Confirm expected dom elements exist.
        cy.get('al-coaching-tree')

      }
    );
  })

})
