describe('RT002_admin_adds_main_dt_menu_link_settings_option', () => {

  let shared_data = {};

  before(() => {
    cy.npmAutoLinkInit();
  })


  // Ensure Add main DT menu link admin option is enabled.
  it('Ensure Add main DT menu link admin option is not enabled.', () => {
    cy.session(
      'ensure_Add_main_DT_menu_link_admin_option_is_not_enabled',
      () => {

        // Login and navigate to option settings admin view.
        cy.adminOptionsSettingsInit()

        // Force option to a checked state and save.
        cy.get('input[name="show_in_menu"]').uncheck()
        cy.get('#post-body-content').find('form').submit()
      }
    );
  })

  // Confirm the DT menu is not shown.
  it('Confirm the DT menu is not shown.', () => {
    cy.session(
      'confirm_the_dt_menu_is_not_shown.',
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

        cy.visit( '/' )

      // Check that the Autolink menu item is not visible
        cy.get('ul[role="menubar"]').not('li').contains('Autolink').should('not.exist');
      }
    );
  })

  // Ensure Add main DT menu link admin option is enabled.
  it('Ensure Add main DT menu link admin option is enabled.', () => {
    cy.session(
      'ensure_Add_main_DT_menu_link_admin_option_is_enabled',
      () => {

        // Login and navigate to option settings admin view.
        cy.adminOptionsSettingsInit()

        // Force option to a checked state and save.
        cy.get('input[name="show_in_menu"]').check()
        cy.get('#post-body-content').find('form').submit()
      }
    );
  })

  // Confirm the DT menu is shown.
  it('Confirm the DT menu is shown.', () => {
    cy.session(
      'confirm_the_dt_menu_is_shown.',
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

        cy.visit('/');
        cy.reload(true);

        // Check that the Autolink menu item is not visible
        cy.get('ul[role="menubar"] li', { timeout: 10000 })
          .contains('Autolink')
          .should('exist');
      }
    );
  })

})
