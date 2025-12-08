import { MainSettings } from './settings-pages/main-settings';
import { createRoot } from '@wordpress/element';
import { TrvlrProvider } from './context/TrvlrContext';
// import apiFetch from '@wordpress/api-fetch';
// import { useEffect, useState } from '@wordpress/element';

// const Testing = () => {
//    const [settings, setSettings] = useState(null);

//    useEffect(() => {
//       let cancelled = false;

//       (async () => {
//          console.log('TRVLR: Testing fetching settings');
//          try {
//             const result = await apiFetch({ path: '/wp/v2/settings' });
//             if (!cancelled) {
//                setSettings(result);
//             }
//          } catch (error) {
//             console.error(error);
//          }
//       })();

//       return () => {
//          cancelled = true;
//       };
//    }, []);

//    return (
//       <div style={{ color: 'white' }}>
//          Settings: {settings ? JSON.stringify(settings) : 'Loading…'}
//       </div>
//    );
// };

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