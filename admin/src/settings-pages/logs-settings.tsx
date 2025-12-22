import React from '@wordpress/element';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button, Card, CardBody, Notice, } from '@wordpress/components';
import { useTrvlr } from '../context/TrvlrContext';
import { PageHeading } from '../components/page-heading';
import apiFetch from '@wordpress/api-fetch';

declare global {
   interface Window {
      trvlrInitialData?: {
         restNonce?: string;
         [key: string]: any;
      };
      wp?: any;
   }
}

interface LogEntry {
   id: number;
   log_type: string;
   message: string;
   details: string;
   created_at: string;
}

interface SyncSession {
   session_id: string | null;
   started_at: string | null;
   completed_at: string | null;
   status: 'completed' | 'error' | 'standalone';
   summary: {
      created: number;
      updated: number;
      skipped: number;
      errors: number;
      total: number;
   };
   logs: LogEntry[];
}

export const LogsSettings = () => {
   const [sessions, setSessions] = useState<SyncSession[]>([]);
   const [expandedSessions, setExpandedSessions] = useState<Set<string>>(new Set());
   const [selectedLog, setSelectedLog] = useState<LogEntry | null>(null);
   const [loading, setLoading] = useState(true);
   const [saving, setSaving] = useState(false);
   const [message, setMessage] = useState<{ type: 'success' | 'error' | 'warning' | 'info'; text: string } | null>(null);

   const loadLogs = async () => {
      setLoading(true);
      try {
         console.log('TRVLR Logs: Fetching logs...');
         const response: SyncSession[] = await apiFetch({
            path: '/trvlr/v1/logs?grouped=true&limit=50',
         });
         console.log('TRVLR Logs: Loaded', response.length, 'sessions');
         setSessions(response);
      } catch (error: any) {
         console.error('TRVLR Logs: Error loading logs:', error);

         // Check if it's a 403 authentication error
         if (error?.data?.status === 403 || error?.code === 'rest_cookie_invalid_nonce') {
            setMessage({
               type: 'error',
               text: __('Authentication failed. Please refresh the page.', 'trvlr')
            });
         } else {
            setMessage({ type: 'error', text: __('Failed to load logs.', 'trvlr') });
         }
      } finally {
         setLoading(false);
      }
   };

   useEffect(() => {
      console.log('TRVLR Logs: Component mounted, checking auth...');
      console.log('TRVLR Initial Data:', window.trvlrInitialData);
      console.log('WP API Fetch available:', typeof window.wp?.apiFetch);

      // Only load if we have proper authentication
      if (window.trvlrInitialData?.restNonce) {
         loadLogs();
      } else {
         console.error('TRVLR Logs: No REST nonce available!');
         setMessage({
            type: 'error',
            text: __('Authentication not configured. Please refresh the page.', 'trvlr')
         });
         setLoading(false);
      }
   }, []);

   const toggleSession = (sessionId: string) => {
      const newExpanded = new Set(expandedSessions);
      if (newExpanded.has(sessionId)) {
         newExpanded.delete(sessionId);
      } else {
         newExpanded.add(sessionId);
      }
      setExpandedSessions(newExpanded);
   };

   const handleClearOldLogs = async () => {
      if (!window.confirm(__('Delete logs older than 30 days?', 'trvlr'))) {
         return;
      }
      setSaving(true);
      try {
         const result: any = await apiFetch({
            path: '/trvlr/v1/logs/clear-old',
            method: 'POST',
         });
         setMessage({ type: 'success', text: result.message });
         loadLogs();
      } catch (error) {
         setMessage({ type: 'error', text: __('Failed to clear old logs.', 'trvlr') });
      } finally {
         setSaving(false);
      }
   };

   const handleClearAllLogs = async () => {
      if (!window.confirm(__('Delete ALL logs? This cannot be undone.', 'trvlr'))) {
         return;
      }
      setSaving(true);
      try {
         const result: any = await apiFetch({
            path: '/trvlr/v1/logs/clear-all',
            method: 'POST',
         });
         setMessage({ type: 'success', text: result.message });
         loadLogs();
      } catch (error) {
         setMessage({ type: 'error', text: __('Failed to clear logs.', 'trvlr') });
      } finally {
         setSaving(false);
      }
   };

   const handleExportLogs = () => {
      window.open('/wp-json/trvlr/v1/logs/export', '_blank');
   };

   const getStatusBadge = (status: string) => {
      const badges: { [key: string]: { label: string; class: string } } = {
         completed: { label: __('Completed', 'trvlr'), class: 'trvlr-badge-success' },
         error: { label: __('Error', 'trvlr'), class: 'trvlr-badge-error' },
         standalone: { label: __('System', 'trvlr'), class: 'trvlr-badge-info' },
      };
      const badge = badges[status] || badges.completed;
      return <span className={`trvlr-badge ${badge.class}`}>{badge.label}</span>;
   };

   const getLogTypeBadge = (type: string) => {
      const typeClasses: { [key: string]: string } = {
         sync_start: 'trvlr-log-badge-info',
         sync_complete: 'trvlr-log-badge-success',
         attraction_created: 'trvlr-log-badge-created',
         attraction_updated: 'trvlr-log-badge-updated',
         attraction_skipped: 'trvlr-log-badge-skipped',
         no_updates: 'trvlr-log-badge-no-updates',
         error: 'trvlr-log-badge-error',
         system: 'trvlr-log-badge-system',
      };
      const className = typeClasses[type] || 'trvlr-log-badge-default';
      const label = type.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
      return <span className={`trvlr-log-badge ${className}`}>{label}</span>;
   };

   const formatDate = (dateString: string | null) => {
      if (!dateString) return '—';
      const date = new Date(dateString);
      return date.toLocaleString('en-US', {
         year: 'numeric',
         month: 'short',
         day: 'numeric',
         hour: '2-digit',
         minute: '2-digit',
      });
   };

   const getSummaryText = (summary: SyncSession['summary']) => {
      const parts: string[] = [];
      if (summary.created > 0) parts.push(`${summary.created} created`);
      if (summary.updated > 0) parts.push(`${summary.updated} updated`);
      if (summary.skipped > 0) parts.push(`${summary.skipped} skipped`);
      if (summary.errors > 0) parts.push(`${summary.errors} errors`);
      return parts.join(', ') || `${summary.total} events`;
   };

   if (loading) {
      return (
         <div className="trvlr-logs-settings">
            <PageHeading
               text={'TRVLR Wordpress Manager Logs'}
            />
            <p>{__('Loading logs...', 'trvlr')}</p>
         </div>
      );
   }

   return (
      <div className="trvlr-logs-settings">
         <PageHeading
            text={'TRVLR Wordpress Manager Logs'}
         />

         {message && (
            <Notice
               status={message.type}
               onRemove={() => setMessage(null)}
               isDismissible
            >
               {message.text}
            </Notice>
         )}

         {/* Actions */}
         <Card style={{ marginBottom: '15px' }}>
            <CardBody>
               <div style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
                  <Button
                     variant="secondary"
                     onClick={handleExportLogs}
                  >
                     <span className="dashicons dashicons-download" style={{ marginRight: '5px' }}></span>
                     {__('Export CSV', 'trvlr')}
                  </Button>
                  <Button
                     variant="secondary"
                     onClick={handleClearOldLogs}
                     isBusy={saving}
                     disabled={saving}
                  >
                     {__('Clear Old Logs (30+ days)', 'trvlr')}
                  </Button>
                  <Button
                     variant="secondary"
                     isDestructive
                     onClick={handleClearAllLogs}
                     isBusy={saving}
                     disabled={saving}
                  >
                     {__('Clear All Logs', 'trvlr')}
                  </Button>
               </div>
            </CardBody>
         </Card>

         {/* Sync Sessions */}
         {sessions.length === 0 ? (
            <Card className="trvlr-card">
               <CardBody>
                  <p className="description">{__('No logs found. Logs will appear here after syncing attractions.', 'trvlr')}</p>
               </CardBody>
            </Card>
         ) : (
            <div className="trvlr-sync-sessions">
               {sessions.map((session) => {
                  const sessionKey = session.session_id || 'standalone';
                  const isExpanded = expandedSessions.has(sessionKey);

                  return (
                     <Card key={sessionKey} className="trvlr-sync-session">
                        <CardBody style={{ padding: '8px 12px' }}>
                           <div
                              className="trvlr-session-header"
                              onClick={() => toggleSession(sessionKey)}
                              style={{ cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '15px' }}
                           >
                              <span className={`dashicons dashicons-arrow-${isExpanded ? 'down' : 'right'}-alt2`}></span>
                              <div style={{ flex: 1 }}>
                                 <div style={{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '5px' }}>
                                    <strong>
                                       {session.status === 'standalone'
                                          ? __('System Events', 'trvlr')
                                          : `${__('Sync', 'trvlr')} - ${formatDate(session.started_at)}`}
                                    </strong>
                                    {getStatusBadge(session.status)}
                                 </div>
                                 <div className="description">
                                    {getSummaryText(session.summary)}
                                    {session.completed_at && session.started_at && (
                                       <span> • {__('Duration:', 'trvlr')} {Math.round((new Date(session.completed_at).getTime() - new Date(session.started_at).getTime()) / 1000)}s</span>
                                    )}
                                 </div>
                              </div>
                           </div>

                           {isExpanded && (
                              <div className="trvlr-session-logs" style={{ marginTop: '15px', paddingTop: '15px', borderTop: '1px solid #ddd' }}>
                                 <table className="wp-list-table widefat fixed striped" style={{ width: '100%' }}>
                                    <thead>
                                       <tr>
                                          <th style={{ width: '150px' }}>{__('Time', 'trvlr')}</th>
                                          <th style={{ width: '140px' }}>{__('Type', 'trvlr')}</th>
                                          <th>{__('Message', 'trvlr')}</th>
                                          <th style={{ width: '80px' }}>{__('Details', 'trvlr')}</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       {session.logs.map((log) => (
                                          <tr key={log.id}>
                                             <td>{formatDate(log.created_at)}</td>
                                             <td>{getLogTypeBadge(log.log_type)}</td>
                                             <td>{log.message}</td>
                                             <td>
                                                {log.details && log.details !== '[]' && log.details !== 'null' ? (
                                                   <Button
                                                      variant="link"
                                                      size="small"
                                                      onClick={() => setSelectedLog(log)}
                                                   >
                                                      {__('View', 'trvlr')}
                                                   </Button>
                                                ) : (
                                                   '—'
                                                )}
                                             </td>
                                          </tr>
                                       ))}
                                    </tbody>
                                 </table>
                              </div>
                           )}
                        </CardBody>
                     </Card>
                  );
               })}
            </div>
         )}

         {/* Log Details Modal */}
         {selectedLog && (
            <div
               className="trvlr-modal"
               style={{
                  position: 'fixed',
                  top: 0,
                  left: 0,
                  right: 0,
                  bottom: 0,
                  background: 'rgba(0,0,0,0.7)',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  zIndex: 999999,
               }}
               onClick={() => setSelectedLog(null)}
            >
               <div
                  className="trvlr-modal-content"
                  style={{
                     background: '#fff',
                     padding: '20px',
                     borderRadius: '4px',
                     maxWidth: '600px',
                     width: '90%',
                     maxHeight: '80vh',
                     overflow: 'auto',
                  }}
                  onClick={(e) => e.stopPropagation()}
               >
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '15px' }}>
                     <h3 style={{ margin: 0 }}>{__('Log Details', 'trvlr')}</h3>
                     <Button
                        variant="tertiary"
                        onClick={() => setSelectedLog(null)}
                     >
                        <span className="dashicons dashicons-no-alt"></span>
                     </Button>
                  </div>
                  <pre style={{ background: '#f5f5f5', padding: '15px', borderRadius: '4px', overflow: 'auto', maxHeight: '50vh' }}>
                     {JSON.stringify(JSON.parse(selectedLog.details), null, 2)}
                  </pre>
               </div>
            </div>
         )}
      </div>
   );
};

