#!/bin/bash
# ==============================================================================
# KIRBY HYBRID INSTALLER — v2.1
# Kirby 5.3 + Tailwind 4 + Vite 7 + GSAP + ECharts + kirbyup (Panel Extensions)
# DevContainer: PHP 8.4 / Node 24 / Rancher Desktop (macOS)
# ==============================================================================
#
# USAGE:
#   chmod +x install.sh && ./install.sh
#
# CLEANUP (wipe a failed attempt before re-running):
#   rm -rf .devcontainer site assets vendor node_modules src content
#   rm -f vite.config.js composer.json composer.lock package.json package-lock.json index.php
#
# SEQUENCE:
#   Phase 1 — Run this script on your Mac BEFORE opening in DevContainer
#   Phase 2 — Reopen in Container (VS Code green icon → Reopen in Container)
#   Phase 3 — Run Phase 2 commands inside the container terminal
# ==============================================================================

set -e

echo ""
echo "======================================================"
echo "  KIRBY HYBRID INSTALLER v2.1"
echo "======================================================"
echo ""

# ==============================================================================
# PHASE 1 — FILES GENERATED ON HOST (before container opens)
# ==============================================================================

echo "[1/5] Creating DevContainer configuration..."

mkdir -p .devcontainer

cat << 'EOF' > .devcontainer/Dockerfile
FROM mcr.microsoft.com/devcontainers/php:1-8.4-bullseye

# Remove expired Yarn GPG key that causes apt errors in Bullseye images
RUN rm -f /etc/apt/sources.list.d/yarn.list

# Disable Xdebug connection noise in terminal output
RUN echo "xdebug.mode=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install GD (required by Kirby for image processing)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install Node.js 24 (Active LTS — EOL April 2028)
RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*
EOF

cat << 'EOF' > .devcontainer/devcontainer.json
{
  "name": "Kirby-Hybrid-Stack",
  "build": { "dockerfile": "Dockerfile" },
  "forwardPorts": [8000, 5173],
  "postCreateCommand": "composer install && npm install && chmod -R 775 content site/accounts site/sessions site/cache media 2>/dev/null || true",
  "customizations": {
    "vscode": {
      "extensions": [
        "bmewburn.vscode-intelephense-client",
        "bradlc.vscode-tailwindcss",
        "arnoson.kirby-vite"
      ]
    }
  }
}
EOF

echo "[1/5] Done."

# ==============================================================================

echo "[2/6] Creating package.json..."

cat << 'EOF' > package.json
{
  "name": "kirby-hybrid-stack",
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "clean": "rm -rf assets/*",
    "panel:dev": "kirbyup dev site/plugins/project-blocks/src/index.js --host 0.0.0.0",
    "panel:build": "kirbyup build site/plugins/project-blocks/src/index.js"
  }
}
EOF

echo "[2/6] Done."

# ==============================================================================

echo "[3/6] Creating vite.config.js..."

# NOTE: vite-plugin-kirby auto-generates site/config/vite.config.php on dev start.
# Do NOT create site/config/vite.config.php manually — it will be overwritten.

cat << 'EOF' > vite.config.js
import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import kirby from 'vite-plugin-kirby';

export default defineConfig(({ mode }) => ({
  base: mode === 'development' ? '/' : '/assets/',

  plugins: [
    tailwindcss(),
    kirby({
      watch: [
        '../site/(templates|snippets|controllers|models|layouts)/**/*.php',
        '../content/**/*',
      ],
      // Must match Kirby config folder — vite.config.php is auto-generated here
      kirbyConfigDir: 'site/config',
    }),
  ],

  server: {
    host: '0.0.0.0',       // Bind to all interfaces inside container
    port: 5173,
    strictPort: true,
    hmr: {
      protocol: 'ws',
      host: 'localhost',   // Browser connects via macOS localhost
    },
    watch: {
      usePolling: true,    // Required for VirtioFS file watching in DevContainer
    },
  },

  build: {
    emptyOutDir: true,
    outDir: 'assets',
    rollupOptions: {
      input: ['src/index.js'],
    },
  },
}));
EOF

echo "[3/6] Done."

# ==============================================================================

echo "[4/6] Creating Kirby config and src files..."

mkdir -p site/config src

cat << 'EOF' > site/config/config.php
<?php
return [
    'debug' => true,

    // arnoson/kirby-vite — devServer is also set via auto-generated vite.config.php
    // Keeping this here as a fallback for environments where vite-plugin-kirby
    // has not yet run and generated the config file.
    'arnoson.kirby-vite' => [
        'devServer' => 'http://localhost:5173',
    ],

    'panel' => [
        'install' => true,
    ],
];
EOF

cat << 'EOF' > src/index.css
/**
 * src/index.css — Vite CSS Entry Point
 * Tailwind 4 — CSS-first configuration
 *
 * @source paths must only point to Kirby PHP templates and content files.
 * Never add site/panel/ — Tailwind must not affect the Kirby Panel UI.
 */

@import "tailwindcss";

@source "../site/**/*.php";
@source "../content/**/*.txt";

@theme {
  --color-primary: #0a0a0a;
  --color-accent:  #013220;
  --color-text:    #ffffff;
}
EOF

cat << 'EOF' > src/index.js
/**
 * src/index.js — Vite JS Entry Point
 * GSAP registered with ScrollTrigger.
 * ECharts lazy-loaded only when [data-echart] elements are present.
 */

import './index.css';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

console.log('Kirby + Vite 7 + Tailwind 4 + GSAP loaded.');

// ECharts — lazy loaded, only if charts exist on the page
const initCharts = async () => {
    const targets = document.querySelectorAll('[data-echart]');
    if (targets.length > 0) {
        const echarts = await import('echarts');
        targets.forEach(el => {
            const chart = echarts.init(el);
            chart.setOption(JSON.parse(el.dataset.options));
        });
    }
};
initCharts();
EOF

echo "[4/6] Done."

# ==============================================================================

echo "[5/6] Creating site blueprint scaffolding..."

mkdir -p site/blueprints/pages

cat << 'EOF' > site/blueprints/site.yml
title: Site

sections:
  pages:
    type: pages
    label: Pages
    templates:
      - home
      - about
      - landing
    empty: No pages yet.
EOF

echo "[5/6] Done."

# ==============================================================================

echo "[6/6] Creating Panel plugin scaffold (kirbyup)..."

# kirbyup compiles Vue.js Panel extension code into a Kirby-loadable plugin.
# This scaffold creates the minimum structure needed to register custom blocks.
# The plugin is built separately from Vite — run: npm run panel:build

mkdir -p site/plugins/project-blocks/src

cat << 'EOF' > site/plugins/project-blocks/src/index.js
/**
 * site/plugins/project-blocks/src/index.js
 * Panel extension entry point — compiled by kirbyup, not Vite.
 *
 * This file registers custom block types and their Panel preview components.
 * Written using Vue Options API (mandatory for Kirby 6 forward-compatibility).
 *
 * Build:  npm run panel:build
 * Dev:    npm run panel:dev   (watch mode with hot reload)
 */

// Placeholder — add block registrations here as needed.
// Example:
//
// panel.plugin("project/blocks", {
//   blocks: {
//     'my-block': {
//       data() { return {} },
//       template: `<div>{{ content.title }}</div>`
//     }
//   }
// });

console.log('Panel plugin loaded.');
EOF

# Create the composer.json for the plugin so Kirby registers it
cat << 'EOF' > site/plugins/project-blocks/composer.json
{
  "name": "project/blocks",
  "type": "kirby-plugin",
  "description": "Custom Panel blocks for this project"
}
EOF

echo "[6/6] Done."

# ==============================================================================
echo ""
echo "======================================================"
echo "  Phase 1 complete. Files generated."
echo "======================================================"
echo ""
echo "  NEXT STEPS:"
echo ""
echo "  1. Open this folder in VS Code"
echo "  2. Click the green icon (bottom-left) →"
echo "     'Dev Containers: Reopen in Container'"
echo "  3. Wait for container build + postCreateCommand to finish"
echo "  4. Open a terminal inside VS Code and run:"
echo ""
echo "     composer create-project getkirby/starterkit temp_kirby"
echo "     cp -r temp_kirby/. . 2>/dev/null || true"
echo "     rm -rf temp_kirby"
echo "     composer require arnoson/kirby-vite"
echo "     npm install vite-plugin-kirby @tailwindcss/vite gsap echarts"
echo "     npm install -D kirbyup"
echo "     chmod -R 775 content site/accounts site/sessions site/cache media"
echo ""
echo "  5. Start the servers — open three separate terminals:"
echo ""
echo "     Terminal 1:  php -S 0.0.0.0:8000 kirby/router.php"
echo "     Terminal 2:  npm run dev          (Vite — frontend assets)"
echo "     Terminal 3:  npm run panel:dev    (kirbyup — Panel extensions)"
echo ""
echo "  NOTE: Terminal 3 is only needed when actively developing"
echo "        Panel block previews or custom Panel extensions."
echo "        Run npm run panel:build for a one-time production build."
echo ""
echo "  6. Open in browser:"
echo "     Site:  http://localhost:8000"
echo "     Panel: http://localhost:8000/panel"
echo ""
echo "======================================================"
