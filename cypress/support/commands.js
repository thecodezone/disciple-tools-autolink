// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

// -- Initialization NPM Home Screen Plugin Environment -- //
Cypress.Commands.add('npmHomeScreenInit', () => {
    cy.exec('composer install')
    cy.exec('composer update')
    cy.exec('npm install')
    cy.exec('npm run build')
    cy.exec(
        'cp -R ./node_modules/@disciple.tools/web-components/dist/generated ./dist/assets/',
    )
    cy.exec(
        'cp -R ./node_modules/@disciple.tools/web-components/dist/lit-localize-*.js ./dist/assets/',
    )
})

// -- Administration Login -- //
Cypress.Commands.add('loginAdmin', (username, password) => {
    // Navigate to WP Admin login page.
    cy.visit('/wp-admin')

    // Specify credentials and submit.fa
    cy.get('#user_login').invoke('attr', 'value', username)
    cy.get('#user_pass').invoke('attr', 'value', password)
    cy.get('#wp-submit').click()
})

// -- Frontend D.T Login -- //
Cypress.Commands.add('loginDT', (username, password) => {
    // Navigate to DT frontend login page.
    cy.visit('/wp-login.php')

    // Specify credentials and submit.
    cy.get('#user_login').type(username)
    cy.get('#user_pass').type(password)
    cy.get('#wp-submit').click()
})

// -- Admin General Settings Initialization -- //
Cypress.Commands.add('adminGeneralSettingsInit', () => {
    const general_tab_url_path = '/wp-admin/admin.php?page=dt_home&tab=general'

    /**
     * Ensure uncaught exceptions do not fail test run; however, any thrown
     * exceptions must not be ignored and a ticket must be raised, in order
     * to resolve identified exception.
     *
     * TODO:
     *  - Resolve any identified exceptions.
     */

    cy.on('uncaught:exception', () => {
        // Returning false here prevents Cypress from failing the test
        return false
    })

    // Capture admin credentials.
    const dt_config = Object.assign({}, cy.config('dt'))
    const username = dt_config.credentials.admin.username
    const password = dt_config.credentials.admin.password

    // Login to WP Admin area.
    cy.loginAdmin(username, password)

    // Access Home Screen plugin area on the general tab.
    cy.visit(general_tab_url_path)
})
