const path = require('path');

let baseUrl = process.env.NODE_ENV === 'production' ? '/bundles/forcilexiktranslationui/dist/' : '/';

module.exports = {
    baseUrl: baseUrl,
    outputDir: path.resolve(__dirname, '../public/dist'),
    devServer: {
        proxy: {
            '/api': {
                target: 'https://your.host/app_dev.php/your/routing/to/bundle/index',
                changeOrigin: true,
                secure: false
            },
        }
    },
    chainWebpack: config => {
        config.optimization.delete('splitChunks')
    }
};