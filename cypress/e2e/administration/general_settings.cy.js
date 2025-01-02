describe('Admin Home Screen General Settings Test Cases', () => {
    // Successfully login and access home screen general tab.
    it('Successfully login and access home screen general tab.', () => {
        cy.session('general_settings', () => {
            cy.adminGeneralSettingsInit()
        })
    })
})
