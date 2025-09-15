// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
// });

import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/home.js",
                "resources/js/script.js",
                "resources/js/admin/admin.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
            "@components": path.resolve(__dirname, "resources/js/components"),
            "@helpers": path.resolve(
                __dirname,
                "resources/js/components/helpers"
            ),
            "@api": path.resolve(
                __dirname,
                "resources/js/components/apiService"
            ),
            "@forms": path.resolve(__dirname, "resources/js/components/form"),
            "@tables": path.resolve(
                __dirname,
                "resources/js/components/tables"
            ),
            "@handlers": path.resolve(
                __dirname,
                "resources/js/components/handleRequest"
            ),
            "@tomselect": path.resolve(
                __dirname,
                "resources/js/components/tomselect"
            ),
            "@sidebar": path.resolve(
                __dirname,
                "resources/js/components/sidebar"
            ),
            "@ui": path.resolve(__dirname, "resources/js/components/ui"),
            "@utils": path.resolve(__dirname, "resources/js/components/utils"),
            // Fallback untuk public/js yang belum dipindah
            "@public": path.resolve(__dirname, "public/js"),
        },
    },
});
