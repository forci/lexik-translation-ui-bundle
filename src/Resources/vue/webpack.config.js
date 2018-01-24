let webpack = require('webpack');
let path = require('path');
const ManifestPlugin = require('webpack-manifest-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');

module.exports = {
    entry: {
        app: './app/main.js',
        vendor: ['vue', 'axios']
    },

    output: {
        path: path.resolve(__dirname, '../public/build'),
        filename: '[name].[chunkhash].js',
        publicPath: '/'
    },

    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader'
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.css$/,
                loader: 'style-loader!css-loader'
            }
        ]
    },

    resolve: {
        extensions: ['*', '.js', 'vue', '.json'],
        alias: {
            'vue$': 'vue/dist/vue.common.js'
        }
    },

    plugins: [
        new CleanWebpackPlugin([
            'build'
        ], {
            root:     path.resolve(__dirname, '../public'),
            exclude:  ['shared.js'],
            verbose:  true,
            dry:      false
        }),
        new webpack.optimize.CommonsChunkPlugin({
            names: ['vendor']
        }),
        new ManifestPlugin({
            publicPath: 'bundles/forcilexiktranslationui/build/'
        }),
    ]
};

if (process.env.NODE_ENV === 'production') {
    module.exports.plugins.push(
        new webpack.optimize.UglifyJsPlugin({
            sourcemap: true,
            compress: {
                warnings: false
            }
        })
    );

    module.exports.plugins.push(
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        })
    );
}