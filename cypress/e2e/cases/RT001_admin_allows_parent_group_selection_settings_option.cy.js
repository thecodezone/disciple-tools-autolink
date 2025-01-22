describe('RT001_admin_allows_parent_group_selection_settings_option', () => {

  let shared_data = {};

  before(() => {
    cy.npmAutoLinkInit();
  })

  // Ensure Allow Parent group selection admin option is enabled.
  it('Ensure Allow Parent Group Selection admin option is not enabled.', () => {
    cy.session(
      'ensure_allow_parent_group_selection_admin_option_is_not_enabled',
      () => {

        // Login and navigate to option settings admin view.
        cy.adminOptionsSettingsInit()

        // Force option to a checked state and save.
        cy.get('input[name="allow_parent_group_selection"]').uncheck()
        cy.get('#post-body-content').find('form').submit()


      }
    );
  })

  // Confirm Parent Group is not shown.
  it('Confirm Parent Group Selection is not shown.', () => {
    cy.session(
      'confirm_parent_group_selection_is_not_shown.',
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

        // Confirm required svg genmap element exists and is visible.
        cy.get( 'dt-button[context="inactive"]' ).contains( 'My Groups' ).click();

        cy.get( '.churches__add' ).click();

        // Confirm parent group input is visible.
        cy.get('dt-single-select.create-group__input[name="parent_group"]').should('not.be.visible');

      }
    );
  })

  // Ensure Allow Parent group selection admin option is enabled.
  it('Ensure Allow Parent Group Selection admin option is enabled.', () => {
    cy.session(
      'ensure_allow_parent_group_selection_admin_option_is_enabled',
      () => {

        // Login and navigate to option settings admin view.
        cy.adminOptionsSettingsInit()

        // Force option to a checked state and save.
        cy.get('input[name="allow_parent_group_selection"]').check()
        cy.get('#post-body-content').find('form').submit()

      }
    );
  })

  // Confirm svg genmap is shown.
  it('Confirm Parent Group Selection is shown.', () => {
    cy.session(
      'confirm_parent_group_selection_is_shown.',
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

        // Confirm required svg genmap element exists and is visible.
        cy.get( 'dt-button[context="inactive"]' ).contains( 'My Groups' ).click();

        cy.get( '.churches__add' ).click();

        // Confirm parent group input is visible.
        cy.get('dt-single-select.create-group__input[name="parent_group"]').should('be.visible');

      }
    );
  })

})
