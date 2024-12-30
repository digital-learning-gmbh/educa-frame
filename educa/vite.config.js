import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import fs from 'fs/promises';
import { viteCommonjs, esbuildCommonjs } from '@originjs/vite-plugin-commonjs';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app-react.js',
                'resources/sass/app.scss'
            ],
            refresh: true,
        }),
        react(),
        viteCommonjs({skipPreBuild: true }),
    ],
    esbuild: {
        loader: "jsx",
        include: /resources\/.*\.jsx?$/,
        // loader: "tsx",
        // include: /src\/.*\.[tj]sx?$/,
        exclude: [],
    },
    optimizeDeps: {
        esbuildOptions: {
            loader: {
                ".js": "jsx",
            },
            plugins: [
                esbuildCommonjs(['react-editor-js', '@react-editor-js/client', '@react-editor-js/server','@digitallearning/educa-h5p-webcomponents']),
                {
                    name: "load-js-files-as-jsx",
                    setup(build) {
                        build.onLoad({ filter: /resources\/.*\.js$/ }, async (args) => ({
                            loader: "jsx",
                            contents: await fs.readFile(args.path, "utf8"),
                        }));
                    },
                },
            ],
        },
    },
});
