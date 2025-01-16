describe('Admin AutoLink Options Settings Test Cases', () => {

  before(() => {
    cy.npmAutoLinkInit();
  })

  it('Successfully login and access admin options tab.', () => {
    cy.session('options_settings', () => {
      cy.adminOptionsSettingsInit()
    })
  })
})
