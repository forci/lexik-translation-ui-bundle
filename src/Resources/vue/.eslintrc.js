module.exports = {
    root: true,
    env: {
        node: true
    },
    'extends': [
        'plugin:vue/essential',
        'eslint:recommended'
        // '@vue/standard' // This is way too strict
    ],
    rules: {
        'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
        'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
        // "indent": ["error", 4], // use with @vue/standard
    },
    parserOptions: {
        parser: 'babel-eslint'
    }
}
