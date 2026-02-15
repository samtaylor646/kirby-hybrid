import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import kirby from 'vite-plugin-kirby';

export default defineConfig(({ mode }) => ({
  base: mode === 'development' ? '/' : '/assets/',

  plugins: [
    tailwindcss(),
    kirby({
      // Watch Kirby templates, snippets, and content for full reload
      watch: [
        '../site/(templates|snippets|controllers|models|layouts)/**/*.php',
        '../content/**/*',
      ],
      // Must match Kirby's config folder — this is where vite.config.php
      // will be auto-generated when Vite starts
      kirbyConfigDir: 'site/config',
    }),
  ],

  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    hmr: {
      protocol: 'ws',
      host: 'localhost',
    },
    watch: {
      usePolling: true,
    },
  },

  build: {
    emptyOutDir: true,
    outDir: 'assets',
    rollupOptions: {
      input: [
        'src/index.js',
        'src/index.css',
      ],
    },
  },
}));