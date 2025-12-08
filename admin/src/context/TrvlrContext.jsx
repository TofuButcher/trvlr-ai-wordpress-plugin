import { createContext, useContext, useState, useCallback } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { mergeWithDefaults } from '../config/themeConfig';

const TrvlrContext = createContext();

export const TrvlrProvider = ({ children }) => {
    // Load initial data from window object (localized by PHP)
    const initialData = window.trvlrInitialData || {
        settings: {},
        sync: {},
        system: {},
        nonce: '',
    };

    // State management (merge with defaults to ensure all fields exist)
    const [themeSettings, setThemeSettings] = useState(() => 
        mergeWithDefaults(initialData.settings?.theme || {})
    );
    const [connectionSettings, setConnectionSettings] = useState(initialData.settings?.connection || {});
    const [notificationSettings, setNotificationSettings] = useState(initialData.settings?.notifications || {});
    
    const [syncStats, setSyncStats] = useState(initialData.sync?.stats || {});
    const [scheduleSettings, setScheduleSettings] = useState(initialData.sync?.schedule || {});
    const [customEditsCount, setCustomEditsCount] = useState(initialData.sync?.custom_edits_count || 0);
    
    const [systemStatus, setSystemStatus] = useState(initialData.system || {});
    const [saving, setSaving] = useState(false);
    const [refreshing, setRefreshing] = useState(false);

    // Settings Methods
    const saveThemeSettings = useCallback(async (settings) => {
        setSaving(true);
        try {
            const response = await apiFetch({
                path: '/trvlr/v1/settings/theme',
                method: 'POST',
                data: settings,
            });
            setThemeSettings(settings);
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        } finally {
            setSaving(false);
        }
    }, []);

    const saveConnectionSettings = useCallback(async (settings) => {
        setSaving(true);
        try {
            const response = await apiFetch({
                path: '/trvlr/v1/settings/connection',
                method: 'POST',
                data: settings,
            });
            setConnectionSettings(settings);
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        } finally {
            setSaving(false);
        }
    }, []);

    const saveNotificationSettings = useCallback(async (settings) => {
        setSaving(true);
        try {
            const response = await apiFetch({
                path: '/trvlr/v1/settings/notifications',
                method: 'POST',
                data: settings,
            });
            setNotificationSettings(settings);
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        } finally {
            setSaving(false);
        }
    }, []);

    // Sync Methods
    const refreshSyncStats = useCallback(async () => {
        setRefreshing(true);
        try {
            const stats = await apiFetch({ path: '/trvlr/v1/sync/stats' });
            setSyncStats(stats);
            return { success: true, data: stats };
        } catch (error) {
            return { success: false, error };
        } finally {
            setRefreshing(false);
        }
    }, []);

    const triggerManualSync = useCallback(async () => {
        try {
            const response = await apiFetch({
                path: '/trvlr/v1/sync/manual',
                method: 'POST',
            });
            // Refresh stats after sync
            await refreshSyncStats();
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        }
    }, [refreshSyncStats]);

    const saveScheduleSettings = useCallback(async (settings) => {
        setSaving(true);
        try {
            const response = await apiFetch({
                path: '/trvlr/v1/sync/schedule',
                method: 'POST',
                data: settings,
            });
            setScheduleSettings(response);
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        } finally {
            setSaving(false);
        }
    }, []);

    const deleteData = useCallback(async (includeMedia = false) => {
        try {
            const response = await apiFetch({
                path: `/trvlr/v1/sync/delete?include_media=${includeMedia}`,
                method: 'POST',
            });
            // Refresh stats after deletion
            await refreshSyncStats();
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        }
    }, [refreshSyncStats]);

    // System Methods
    const refreshSystemStatus = useCallback(async () => {
        setRefreshing(true);
        try {
            const status = await apiFetch({ path: '/trvlr/v1/setup/status' });
            setSystemStatus(status);
            return { success: true, data: status };
        } catch (error) {
            return { success: false, error };
        } finally {
            setRefreshing(false);
        }
    }, []);

    const createPaymentPage = useCallback(async () => {
        try {
            const response = await apiFetch({
                path: '/trvlr/v1/setup/payment-page',
                method: 'POST',
            });
            // Refresh system status after creation
            await refreshSystemStatus();
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        }
    }, [refreshSystemStatus]);

    const testApiConnection = useCallback(async () => {
        try {
            const response = await apiFetch({
                path: '/trvlr/v1/setup/test-connection',
                method: 'POST',
            });
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        }
    }, []);

    const value = {
        // Settings
        themeSettings,
        connectionSettings,
        notificationSettings,
        saveThemeSettings,
        saveConnectionSettings,
        saveNotificationSettings,
        
        // Sync
        syncStats,
        scheduleSettings,
        customEditsCount,
        refreshSyncStats,
        triggerManualSync,
        saveScheduleSettings,
        deleteData,
        
        // System
        systemStatus,
        refreshSystemStatus,
        createPaymentPage,
        testApiConnection,
        
        // UI State
        saving,
        refreshing,
        nonce: initialData.nonce,
    };

    return (
        <TrvlrContext.Provider value={value}>
            {children}
        </TrvlrContext.Provider>
    );
};

export const useTrvlr = () => {
    const context = useContext(TrvlrContext);
    if (!context) {
        throw new Error('useTrvlr must be used within TrvlrProvider');
    }
    return context;
};

