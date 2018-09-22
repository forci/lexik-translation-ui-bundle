const path = require('path');

let baseUrl = process.env.NODE_ENV === 'production' ? '/bundles/forcilexiktranslationui/dist/' : '/';

module.exports = {
    baseUrl: baseUrl,
    outputDir: path.resolve(__dirname, '../public/dist'),
    devServer: {
        proxy: {
            '/api': {
                // When running the sample bundle, if you would like to play with the User Interface
                // This will proxy API requests to the Symfony web server started with server:start
                target: 'http://127.0.0.1:8000/ui/forci',
                changeOrigin: true,
                secure: false
            },
        }
    },
    chainWebpack: config => {
        config.optimization.delete('splitChunks')
    }
};