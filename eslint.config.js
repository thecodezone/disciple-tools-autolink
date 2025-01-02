// eslint.config.js
import eslint from '@eslint/js'
import eslintConfigPrettier from 'eslint-config-prettier'
import eslintPluginCypress from 'eslint-plugin-cypress/flat'
import globals from 'globals'

export default [
    {
        ignores: [
            '**/node_modules/*',
            '**/vendor/*',
        ],
    },
    eslint.configs.recommended,
    eslintConfigPrettier,
    eslintPluginCypress.configs.recommended,
    {
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: 'module',
            globals: {
                ...trimKeys(globals.browser),
                ...trimKeys(globals.node),
            },
        },
    },
]

function trimKeys(source) {
    return Object.keys(source).reduce((acc, key) => {
        acc[key.trim()] = source[key]
        return acc
    }, {})
}