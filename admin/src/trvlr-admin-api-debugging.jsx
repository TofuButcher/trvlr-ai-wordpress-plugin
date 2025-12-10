import { useState } from '@wordpress/element';
import { Button, Card, CardBody } from '@wordpress/components';

export const DebuggingComponent = () => {
   console.log('TRVLR: DebuggingComponent rendered');

   const [results, setResults] = useState({});
   const [loading, setLoading] = useState(false);

   const testEndpoint = async (name, path) => {
      setLoading(true);
      setResults(prev => ({ ...prev, [name]: 'Loading...' }));

      try {
         // Use the global wp.apiFetch which WordPress configures automatically
         const response = await window.wp.apiFetch({ path });
         setResults(prev => ({
            ...prev,
            [name]: {
               success: true,
               data: response,
               timestamp: new Date().toLocaleTimeString()
            }
         }));
      } catch (error) {
         setResults(prev => ({
            ...prev,
            [name]: {
               success: false,
               error: error.message,
               code: error.code,
               data: error.data,
               timestamp: new Date().toLocaleTimeString()
            }
         }));
      } finally {
         setLoading(false);
      }
   };

   const testAllEndpoints = () => {
      testEndpoint('Theme Settings', '/trvlr/v1/settings/theme');
      setTimeout(() => testEndpoint('Sync Stats', '/trvlr/v1/sync/stats'), 500);
      setTimeout(() => testEndpoint('Custom Edits', '/trvlr/v1/sync/custom-edits'), 1000);
      setTimeout(() => testEndpoint('Logs', '/trvlr/v1/logs?grouped=true&limit=10'), 1500);
   };

   const testWordPressAPI = async () => {
      setLoading(true);
      setResults(prev => ({ ...prev, 'WP Users': 'Loading...' }));

      try {
         // Use the global wp.apiFetch which WordPress configures automatically
         const response = await window.wp.apiFetch({ path: '/wp/v2/users/me' });
         setResults(prev => ({
            ...prev,
            'WP Users': {
               success: true,
               data: response,
               timestamp: new Date().toLocaleTimeString()
            }
         }));
      } catch (error) {
         setResults(prev => ({
            ...prev,
            'WP Users': {
               success: false,
               error: error.message,
               code: error.code,
               data: error.data,
               timestamp: new Date().toLocaleTimeString()
            }
         }));
      } finally {
         setLoading(false);
      }
   };

   return (
      <div style={{ padding: '20px', maxWidth: '1200px' }}>
         <h1>TRVLR REST API Debugging</h1>

         <Card style={{ marginBottom: '20px' }}>
            <CardBody>
               <h2>Environment Info</h2>
               <pre style={{ background: '#f0f0f0', padding: '15px', overflow: 'auto', fontSize: '12px' }}>
                  {JSON.stringify({
                     wpApiSettings: window.wpApiSettings,
                     trvlrInitialData: window.trvlrInitialData ? {
                        hasRestNonce: !!window.trvlrInitialData.restNonce,
                        restNonce: window.trvlrInitialData.restNonce?.substring(0, 10) + '...',
                        restRoot: window.trvlrInitialData.restRoot,
                     } : 'Not found',
                     wpApiFetchAvailable: typeof window.wp?.apiFetch,
                  }, null, 2)}
               </pre>
            </CardBody>
         </Card>

         <Card style={{ marginBottom: '20px' }}>
            <CardBody>
               <h2>Test Endpoints</h2>
               <div style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
                  <Button
                     variant="primary"
                     onClick={testAllEndpoints}
                     disabled={loading}
                  >
                     Test All TRVLR Endpoints
                  </Button>
                  <Button
                     variant="secondary"
                     onClick={testWordPressAPI}
                     disabled={loading}
                  >
                     Test WordPress Core API
                  </Button>
                  <Button
                     variant="secondary"
                     onClick={() => testEndpoint('Theme Settings', '/trvlr/v1/settings/theme')}
                     disabled={loading}
                  >
                     Test Theme Settings
                  </Button>
                  <Button
                     variant="secondary"
                     onClick={() => testEndpoint('Logs', '/trvlr/v1/logs?grouped=true&limit=10')}
                     disabled={loading}
                  >
                     Test Logs
                  </Button>
                  <Button
                     variant="secondary"
                     onClick={() => setResults({})}
                  >
                     Clear Results
                  </Button>
               </div>
            </CardBody>
         </Card>

         <Card>
            <CardBody>
               <h2>Results</h2>
               {Object.keys(results).length === 0 ? (
                  <p>No tests run yet. Click a button above to test endpoints.</p>
               ) : (
                  Object.entries(results).map(([name, result]) => (
                     <div key={name} style={{
                        marginBottom: '20px',
                        padding: '15px',
                        background: result.success === true ? '#d4edda' : result.success === false ? '#f8d7da' : '#fff3cd',
                        border: '1px solid',
                        borderColor: result.success === true ? '#c3e6cb' : result.success === false ? '#f5c6cb' : '#ffeeba',
                        borderRadius: '4px'
                     }}>
                        <h3 style={{ margin: '0 0 10px 0' }}>
                           {name}
                           {result.timestamp && <small style={{ marginLeft: '10px', color: '#666' }}>({result.timestamp})</small>}
                        </h3>
                        {result === 'Loading...' ? (
                           <p>Loading...</p>
                        ) : (
                           <>
                              {result.success !== undefined && (
                                 <p style={{ margin: '5px 0', fontWeight: 'bold' }}>
                                    Status: {result.success ? '✅ Success' : '❌ Failed'}
                                 </p>
                              )}
                              {result.error && (
                                 <p style={{ margin: '5px 0', color: '#721c24' }}>
                                    <strong>Error:</strong> {result.error}
                                 </p>
                              )}
                              {result.code && (
                                 <p style={{ margin: '5px 0', color: '#856404' }}>
                                    <strong>Code:</strong> {result.code}
                                 </p>
                              )}
                              <pre style={{
                                 background: '#fff',
                                 padding: '10px',
                                 overflow: 'auto',
                                 fontSize: '11px',
                                 maxHeight: '300px'
                              }}>
                                 {JSON.stringify(result.data || result.error, null, 2)}
                              </pre>
                           </>
                        )}
                     </div>
                  ))
               )}
            </CardBody>
         </Card>
      </div>
   );
};