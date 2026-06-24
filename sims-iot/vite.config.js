import { defineConfig } from 'vite';
import { resolve } from 'path';

/**
 * Vite config para CodeIgniter 4
 *
 * Compilación:
 *   npm run build
 *
 * Los archivos compilados se guardan en public/assets/
 * Las vistas hacen referencia a:
 *   base_url('assets/css/app.css')
 *   base_url('assets/js/app.js')
 *
 * Para desarrollo (con HMR):
 *   npm run dev
 */
export default defineConfig({
  root: '.',

  // Archivos de entrada
  build: {
    outDir: 'public/assets',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'resources/js/app.js'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          if (/\.css$/.test(assetInfo.name)) return 'css/[name][extname]';
          if (/\.(png|jpe?g|svg|gif|webp|ico)$/.test(assetInfo.name)) return 'img/[name][extname]';
          if (/\.(woff2?|eot|ttf|otf)$/.test(assetInfo.name)) return 'fonts/[name][extname]';
          return '[name][extname]';
        },
      },
    },
    minify: 'terser',
    sourcemap: false,
  },

  // Servidor de desarrollo
  server: {
    port: 5173,
    host: true,
    strictPort: true,
    // Proxy de API al servidor PHP local
    proxy: {
      '/api': {
        target: 'http://localhost:8080',
        changeOrigin: true,
      },
    },
  },

  // Alias de rutas
  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources'),
      '~bootstrap': resolve(__dirname, 'node_modules/bootstrap'),
    },
  },
});