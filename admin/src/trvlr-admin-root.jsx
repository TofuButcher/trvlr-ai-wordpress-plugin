import { MainSettings } from './settings-pages/main-settings';
import { createRoot } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { TrvlrProvider } from './context/TrvlrContext';

// Vite bundles its own @wordpress/api-fetch; WP core already configures window.wp.apiFetch.
if (apiFetch !== window.wp?.apiFetch) {
   const restRoot =
      window.wpApiSettings?.root ||
      window.trvlrInitialData?.restRoot ||
      '/wp-json/';
   const restNonce =
      window.wpApiSettings?.nonce ||
      window.trvlrInitialData?.restNonce ||
      '';

   apiFetch.use(apiFetch.createRootURLMiddleware(restRoot));
   if (restNonce) {
      apiFetch.use(apiFetch.createNonceMiddleware(restNonce));
   }
}

document.addEventListener('DOMContentLoaded', () => {
   const rootElement = document.getElementById('trvlr-settings-root');
   if (rootElement) {
      console.log('TRVLR: Found root element, rendering...');
      const root = createRoot(rootElement);
      root.render(
         <TrvlrProvider>
            <MainSettings />
         </TrvlrProvider>
      );
   } else {
      console.error('TRVLR: Root element #trvlr-settings-root NOT FOUND!');
   }
});