describe('RT013_user_can_delete_group', () => {
  const sharedData = {
    groupName: 'Cypress_Group',
    startDate: '2021-01-01',
  };

  before(() => {
   cy.npmAutoLinkInit();
  });

  it('Login to D.T frontend and create a group', () => {
    cy.session('dt_frontend_login_and_create_group', () => {
      // Handle uncaught exceptions gracefully
      cy.on('uncaught:exception', () => false);

      // Retrieve credentials from Cypress configuration
      const dtConfig = cy.config('dt');
      const username = dtConfig.credentials.admin.username;
      const password = dtConfig.credentials.admin.password;

      // Log in to the application
      cy.loginAutoLink(username, password);

      // Navigate to "My Groups"
      cy.get('dt-button[context="inactive"]').contains('My Groups').click();

      // Click the create group button
      cy.get('.churches__add').click();

      // Fill in the group creation form
      cy.get('dt-text.create-group__input[name="name"]')
        .shadow()
        .find('input')
        .type(sharedData.groupName);

      cy.get('dt-date[name="start_date"]')
        .shadow()
        .find('input')
        .type(sharedData.startDate);

      cy.get('body').click();

      // Submit the form
      cy.get('form.create-group').submit();
    });
  });

  it('Edit the created group', () => {
    cy.session('dt_frontend_edit_group', () => {
      // Handle uncaught exceptions gracefully
      cy.on('uncaught:exception', () => false);

      // Retrieve credentials from Cypress configuration
      const dtConfig = cy.config('dt');
      const username = dtConfig.credentials.admin.username;
      const password = dtConfig.credentials.admin.password;

      // Log in to the application
      cy.loginAutoLink(username, password);

      // Navigate to "My Groups"
      cy.get('dt-button[context="inactive"]').contains('My Groups').click();

      cy.get(`.churches__groups al-church-tile[title="${sharedData.groupName}"]`).within(() => {
        // Open the menu and click "Edit"
        cy.get('al-church-menu').click({ force: true });
        cy.get('dt-button[context="primary"]').contains('Edit').click({ force: true });
      });

      // Edit the group name
      cy.get('dt-text.create-group__input[name="name"]')
        .shadow()
        .find('input')
        .type('_Edit');

      cy.get('dt-date[name="start_date"]')
        .shadow()
        .find('input')
        .type(sharedData.startDate);

      cy.get('body').click();

      // Submit the form
      cy.get('form.create-group').submit({ force: true });

    });
  });

  it('Delete the created group', () => {
    cy.session('dt_frontend_delete_group', () => {
      // Handle uncaught exceptions gracefully
      cy.on('uncaught:exception', () => false);

      // Retrieve credentials from Cypress configuration
      const dtConfig = cy.config('dt');
      const username = dtConfig.credentials.admin.username;
      const password = dtConfig.credentials.admin.password;

      // Log in to the application
      cy.loginAutoLink(username, password);

      // Navigate to "My Groups"
      cy.get('dt-button[context="inactive"]').contains('My Groups').click();

      // Locate the created group
      cy.get(`.churches__groups al-church-tile[title="${sharedData.groupName}_Edit"]`).within(() => {
        // Open the menu and click "Edit"
        cy.get('al-church-menu').click({ force: true });
        cy.get('dt-button[context="alert"]').contains('Delete Group').click({ force: true });
      });

    });
  });

})
