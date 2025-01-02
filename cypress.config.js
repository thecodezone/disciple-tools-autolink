import { defineConfig } from 'cypress'
import 'dotenv/config'

export default defineConfig({
    e2e: {
        setupNodeEvents() {
            // implement node event listeners here
        },
        baseUrl: process.env.S3_BUCKET,
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
