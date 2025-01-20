describe('RT009_user_can_copy_shared_autolink', () => {
  let shared_data = {};

  before(() => {
    cy.npmAutoLinkInit();
  })
  

  // Use Cypress session to persist the login state
  it('Login to D.T frontend and obtain Autolink plugin.', () => {
    cy.session('dt_frontend_login_and_obtain_autolink_plugin', () => {
      // Handle uncaught exceptions gracefully
      cy.on('uncaught:exception', (err, runnable) => {
        return false;
      });

      // Retrieve credentials from Cypress configuration
      const dt_config = cy.config('dt');
      const username = dt_config.credentials.admin.username;
      const password = dt_config.credentials.admin.password;

      // Log in to the application
      cy.loginAutoLink(username, password);

      // Ensure the `dt-copy-text` component is present and contains a value
      cy.get('dt-copy-text').invoke('val').should('not.be.empty');

      // Interact with the `dt-copy-text` component to trigger the copy functionality
      cy.get('dt-copy-text')
        .shadow()
        .find('dt-icon')
        .shadow()
        .find('iconify-icon')
        .shadow()
        .find('svg')
        .should('be.visible')
        .click({ force: true });

      // Validate the copied value
      cy.get('dt-copy-text').invoke('val').then((copyurl) => {
        cy.log('Copied URL:', copyurl); // Log the copied URL for debugging purposes
        expect(copyurl).to.not.be.empty; // Assert that the URL is not empty

        // Simulate incognito mode by clearing cookies and local storage
        cy.clearCookies();
        cy.clearLocalStorage();

        // Visit the copied URL and validate the resulting page
        cy.visit(copyurl);
        cy.url().should('include', 'login'); // Update with the actual expected URL fragment
      });
    });
  });
});
