import { MainSettings } from './settings-pages/main-settings';
import { createRoot } from '@wordpress/element';
import { TrvlrProvider } from './context/TrvlrContext';


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