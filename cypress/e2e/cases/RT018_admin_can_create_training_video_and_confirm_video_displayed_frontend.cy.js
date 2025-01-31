describe('RT018_admin_can_create_training_video_and_confirm_video_displayed_frontend', () => {

  let shared_data = {
    title: 'Cypress Training Video',
    embed: '<iframe width="560" height="315" src="https://www.youtube.com/embed/LyTXREZOxwA?si=sJtlAGdfQl2cPhhI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
  };
  //
  before(() => {
    cy.npmAutoLinkInit();
  })

// Admin can create a training video.
  it('Admin can create a training video', () => {
    cy.session(
      'admin_create_training_video',
      () => {

        // Login and navigate to option settings admin view.
        cy.adminOptionsSettingsInit()

        // Force option to a checked state and save.
      cy.get('al-training-videos-field')
      .shadow()
      .find('.button--success')
      .click();

      cy.get('al-training-videos-field')
      .shadow()
      .find('input[type="text"][value=""]')
      .type(shared_data.title);

     cy.get('al-training-videos-field')
      .shadow()
      .find('textarea[placeholder="Embed"]:empty')
      .type(shared_data.embed);

        cy.get('#post-body-content').find('form').submit()
      }
    );
  })

  // Confirm training video is displayed on frontend.
  it('Confirm training video is displayed on frontend', () => {
    cy.session(
      'confirm_training_video_displayed_frontend',
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
        cy.get('al-menu').click();
        cy.get('al-menu').shadow().find('a[title="Training"]').click();

        cy.get('.app').find(`dt-tile[title="${shared_data.title}"]`).should('be.visible');

      }
    );
  })
 // Admin can delete a training video.
  it('Admin can delete a training video', () => {
    cy.session(
      'admin_delete_training_video',
      () => {

        // Login and navigate to option settings admin view.
        cy.adminOptionsSettingsInit()
        cy.get('al-training-videos-field') // Target the shadow DOM host
          .shadow() // Traverse into shadow root
          .find('button.button--danger[aria-label="Remove"]') // Find the 'Remove' button
          .last() // Get the last matching button
          .should('be.visible') // Ensure the button is visible
          .click(); // Click the button with force


        cy.get('#post-body-content').find('form').submit()
      }
    );
  })

})
