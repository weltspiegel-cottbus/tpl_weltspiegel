import { defineConfig } from 'vite';
import { resolve } from 'path';
import fs from 'fs';

export default defineConfig({
  build: {
    lib: {
      entry: resolve(__dirname, 'media/js/template.js'),
      formats: ['es'],
      fileName: () => 'template.min.js'
    },
    outDir: 'media/js',
    emptyOutDir: false,
    minify: true,
    rollupOptions: {
      output: {
        assetFileNames: 'template.min.css'
      }
    }
  },
  plugins: [
    {
      name: 'move-css',
      writeBundle() {
        const cssSource = resolve(__dirname, 'media/js/template.min.css');
        const cssDest = resolve(__dirname, 'media/css/template.min.css');

        if (fs.existsSync(cssSource)) {
          fs.mkdirSync(resolve(__dirname, 'media/css'), { recursive: true });
          fs.renameSync(cssSource, cssDest);
          console.log('✓ Moved CSS to media/css/template.min.css');
        }
      }
    }
  ]
});
