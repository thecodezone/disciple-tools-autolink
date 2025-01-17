describe('RT003_admin_enables_showing_nested_vertical_genmap_settings_option', () => {

    let shared_data = {};

    before(() => {
        cy.npmAutoLinkInit();
    })

    // Ensure show nested genmap admin option is enabled.
    it('Ensure show nested genmap admin option is enabled.', () => {
        cy.session(
            'ensure_show_nested_genmap_admin_option_is_enabled',
            () => {

                // Login and navigate to option settings admin view.
                cy.adminOptionsSettingsInit()

                // Force option to a checked state and save.
                cy.get('input[name="show_nested_genmap"]').check()
                cy.get('#post-body-content').find('form').submit()

            }
        );
    })

    // Confirm vertical genmap is shown.
    it('Confirm vertical genmap is shown.', () => {
        cy.session(
            'confirm_vertical_genmap_is_shown.',
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

                // Confirm required vertical genmap element exists.
                cy.get('#genmap-v2').should('exist')
                cy.get('#genmap-v2').should('be.visible')

            }
        );
    })

})
