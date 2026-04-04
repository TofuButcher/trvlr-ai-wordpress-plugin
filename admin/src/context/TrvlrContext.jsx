import { createContext, useContext, useState, useCallback, useMemo } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const TrvlrContext = createContext();

/**
 * Get all fields from theme config (flattened)
 */
const getAllFieldsFromConfig = (config) => {
    const fields = [];

    Object.values(config || {}).forEach(group => {
        // Direct fields
        if (group.fields) {
            Object.entries(group.fields).forEach(([key, field]) => {
                fields.push({
                    key,
                    ...field
                });
            });
        }

        // Fields inside cols-X wrappers (at group level)
        Object.entries(group).forEach(([key, value]) => {
            if (key.startsWith('cols-') && value.fields) {
                Object.entries(value.fields).forEach(([fieldKey, field]) => {
                    fields.push({
                        key: fieldKey,
                        ...field
                    });
                });
            }
        });
    });

    return fields;
};

/**
 * Get default values from theme config
 */
const getThemeDefaults = (config) => {
    const defaults = {};
    const allFields = getAllFieldsFromConfig(config);

    allFields.forEach(field => {
        if (field.default !== undefined) {
            defaults[field.key] = field.default;
        }
    });

    return defaults;
};

/**
 * Merge user settings with defaults from config
 */
const mergeWithDefaults = (userSettings, config) => {
    const defaults = getThemeDefaults(config);
    const filtered = Object.fromEntries(
        Object.entries(userSettings || {}).filter(([_, value]) => value !== undefined)
    );

    return {
        ...defaults,
        ...filtered,
    };
};

/**
 * Process theme config fields for rendering
 * Handles cols-X groupings
 */
const processConfigForRendering = (config) => {
    const processed = {};

    Object.entries(config || {}).forEach(([groupKey, group]) => {
        processed[groupKey] = {
            label: group.label,
            description: group.description,
            fields: []
        };

        // Add direct fields first
        if (group.fields) {
            Object.entries(group.fields).forEach(([key, field]) => {
                processed[groupKey].fields.push({
                    type: 'field',
                    key,
                    ...field
                });
            });
        }

        // Add cols-X groupings (at group level)
        Object.entries(group).forEach(([key, value]) => {
            if (key.startsWith('cols-') && value.fields) {
                const colsClass = key; // e.g., "cols-3"
                processed[groupKey].fields.push({
                    type: 'group',
                    colsClass,
                    label: value.label,
                    description: value.description,
                    fields: Object.entries(value.fields || {}).map(([fieldKey, field]) => ({
                        key: fieldKey,
                        ...field
                    }))
                });
            }
        });
    });

    return processed;
};

export const TrvlrProvider = ({ children }) => {
    // Load initial data from window object (localized by PHP)
    const initialData = window.trvlrInitialData || {
        settings: {},
        sync: {},
        system: {},
        themeConfig: {},
        nonce: '',
    };

    // Get theme config from localized data
    const themeConfig = initialData.themeConfig || {};

    // Process config for rendering (memoized)
    const processedThemeConfig = useMemo(() => processConfigForRendering(themeConfig), [themeConfig]);

    // State management (merge with defaults to ensure all fields exist)
    const [themeSettings, setThemeSettings] = useState(() =>
        mergeWithDefaults(initialData.settings?.theme || {}, themeConfig)
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
            if (response && response.settings) {
                setConnectionSettings(response.settings);
            } else {
                setConnectionSettings(settings);
            }
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

        // Theme Config
        themeConfig,
        processedThemeConfig,

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

/**
 * Generate CSS variables string from settings and config
 */
export const generateCSSVariables = (settings, config) => {
    let css = ':root {\n';
    const allFields = getAllFieldsFromConfig(config);

    allFields.forEach(field => {
        if (field.cssVar) {
            const value = settings[field.key] ?? field.default;
            const unit = field.unit || '';
            css += `  ${field.cssVar}: ${value}${unit};\n`;
        }
    });

    css += '}';
    return css;
};

// Export helper functions for use outside context
export { getAllFieldsFromConfig, getThemeDefaults, mergeWithDefaults, processConfigForRendering };

