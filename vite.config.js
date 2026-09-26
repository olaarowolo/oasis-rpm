import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/landing.css',
        'resources/js/header.js',
        'resources/js/public-header.js',
        'resources/js/rich-text-editor.js',
        'resources/js/defense-readiness.js',
        'resources/js/error-page.js',
      ],
      refresh: [
        'app/**',
        'resources/views/**',
        'routes/**',
      ],
    }),
  ],
  css: {
    postcss: './postcss.config.js',
  },
});
