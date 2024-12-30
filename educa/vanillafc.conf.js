const path = require('path');
const Uglify = require("uglifyjs-webpack-plugin");

module.exports = {
    entry: './resources/js/fc_vanilla_helper.js',
    output: {
        path: path.resolve(__dirname, 'public/js/'),
        filename: 'fullcalendar_vanilla.js',
        library: 'FullCalendar',
        libraryTarget: 'window'
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [
                    "style-loader",
                    "css-loader",
                    "sass-loader"
                ]
            },
            {
                test: /\.css$/,
                use: [
                    "css-loader"
                ]
            },
            {
                test: /\.svg$/,
                loader: 'svg-inline-loader'
            }
        ]
    },
    plugins: [
        new Uglify()
    ]
}
