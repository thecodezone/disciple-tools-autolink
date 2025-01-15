import { defineConfig } from 'cypress'
import 'dotenv/config'

export default defineConfig({
    e2e: {
        setupNodeEvents() {
            // implement node event listeners here
        },
        baseUrl: 'https://wp.ddev.site:8443'
    },
    dt: {
        credentials: {
            admin: {
                username: 'admin',
                password: 'admin',
            },
        },
    },
})
