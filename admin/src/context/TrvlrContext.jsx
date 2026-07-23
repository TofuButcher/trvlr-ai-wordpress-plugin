import { createContext, useContext, useState, useCallback, useMemo } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const TrvlrContext = createContext();

/**
 * Flatten theme config fields, including those nested under `cols-*` wrappers.
 *
 * @param {Record<string, any>} config
 * @returns {Array<{key: string} & Record<string, any>>}
 */
const getAllFieldsFromConfig = (config) => {
    const fields = [];

    Object.values(config || {}).forEach(group => {
        if (group.fields) {
            Object.entries(group.fields).forEach(([key, field]) => {
                fields.push({
                    key,
                    ...field
                });
            });
        }

        // Theme config nests multi-column layouts under keys like `cols-3`.
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
 * @param {Record<string, any>} config
 * @returns {Record<string, any>}
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
 * @param {Record<string, any>} userSettings
 * @param {Record<string, any>} config
 * @returns {Record<string, any>}
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
 * Normalize theme config into renderable field/group lists (handles `cols-*`).
 *
 * @param {Record<string, any>} config
 * @returns {Record<string, {label: string, description: string, fields: Array}>}
 */
const processConfigForRendering = (config) => {
    const processed = {};

    Object.entries(config || {}).forEach(([groupKey, group]) => {
        processed[groupKey] = {
            label: group.label,
            description: group.description,
            fields: []
        };

        if (group.fields) {
            Object.entries(group.fields).forEach(([key, field]) => {
                processed[groupKey].fields.push({
                    type: 'field',
                    key,
                    ...field
                });
            });
        }

        Object.entries(group).forEach(([key, value]) => {
            if (key.startsWith('cols-') && value.fields) {
                processed[groupKey].fields.push({
                    type: 'group',
                    colsClass: key,
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
    // Localized by PHP via wp_localize_script('trvlr-admin-root', 'trvlrInitialData', …)
    const initialData = window.trvlrInitialData || {
        settings: {},
        sync: {},
        system: {},
        themeConfig: {},
        templateChoices: { cards: [], singles: [], presentationThemes: [] },
        nonce: '',
    };

    const themeConfig = initialData.themeConfig || {};
    const processedThemeConfig = useMemo(() => processConfigForRendering(themeConfig), [themeConfig]);

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

    const saveThemeSettings = useCallback(async (settings) => {
        setSaving(true);
        try {
            const response = await apiFetch({
                path: '/trvlr/v1/settings/theme',
                method: 'POST',
                data: settings,
            });
            if (response?.settings) {
                setThemeSettings(mergeWithDefaults(response.settings, themeConfig));
            } else {
                setThemeSettings(mergeWithDefaults(settings, themeConfig));
            }
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        } finally {
            setSaving(false);
        }
    }, [themeConfig]);

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
            await refreshSyncStats();
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        }
    }, [refreshSyncStats]);

    const triggerManualSyncNoMedia = useCallback(async () => {
        try {
            const response = await apiFetch({
                path: '/trvlr/v1/sync/manual-no-media',
                method: 'POST',
            });
            await refreshSyncStats();
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        }
    }, [refreshSyncStats]);

    const cancelSync = useCallback(async () => {
        try {
            const response = await apiFetch({
                path: '/trvlr/v1/sync/cancel',
                method: 'POST',
            });
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        }
    }, []);

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
            await refreshSyncStats();
            return { success: true, data: response };
        } catch (error) {
            return { success: false, error };
        }
    }, [refreshSyncStats]);

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
        themeSettings,
        connectionSettings,
        notificationSettings,
        saveThemeSettings,
        saveConnectionSettings,
        saveNotificationSettings,
        themeConfig,
        processedThemeConfig,
        templateChoices: initialData.templateChoices || { cards: [], singles: [], presentationThemes: [] },
        syncStats,
        scheduleSettings,
        customEditsCount,
        refreshSyncStats,
        triggerManualSync,
        triggerManualSyncNoMedia,
        cancelSync,
        saveScheduleSettings,
        deleteData,
        systemStatus,
        refreshSystemStatus,
        createPaymentPage,
        testApiConnection,
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

/**
 * @returns {Record<string, any>}
 */
export const useTrvlr = () => {
    const context = useContext(TrvlrContext);
    if (!context) {
        throw new Error('useTrvlr must be used within TrvlrProvider');
    }
    return context;
};

/**
 * @param {Record<string, any>} settings
 * @param {Record<string, any>} config
 * @returns {string}
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

export { getAllFieldsFromConfig, getThemeDefaults, mergeWithDefaults, processConfigForRendering };
