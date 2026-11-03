import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// No Tailwind: the V7 prototype's CSS values are ported verbatim as plain modern CSS
// (see BUILD.md §2 and docs/inventory.md §9), so a utility framework would only get in the way.
// No font plugin either: the four V7 families are self-hosted from public/fonts/.

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: [
                '**/storage/framework/views/**',
                '**/design/**',
                '**/docs/**',
                '**/vendor/**',
            ],
        },
    },
});
