describe('RT004_admin_disables_showing_nested_vertical_genmap_settings_option', () => {

    let shared_data = {};

    before(() => {
        cy.npmAutoLinkInit();
    })

    // Ensure show nested genmap admin option is disabled.
    it('Ensure show nested genmap admin option is disabled.', () => {
        cy.session(
            'ensure_show_nested_genmap_admin_option_is_disabled',
            () => {

                // Login and navigate to option settings admin view.
                cy.adminOptionsSettingsInit()

                // Force option to an unchecked state and save.
                cy.get('input[name="show_nested_genmap"]').uncheck()
                cy.get('#post-body-content').find('form').submit()

            }
        );
    })

    // Confirm svg genmap is shown.
    it('Confirm svg genmap is shown.', () => {
        cy.session(
            'confirm_svg_genmap_is_shown.',
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
                cy.get('#genmapper-graph-svg').should('exist')
                cy.get('#genmapper-graph-svg').should('be.visible')

            }
        );
    })

})
