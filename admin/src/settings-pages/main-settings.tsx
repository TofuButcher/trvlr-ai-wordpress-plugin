import React, { useState, useEffect } from '@wordpress/element';
import { SetupSettings } from './setup-settings';
import { ThemeSettings } from './theme-settings';
import { SyncSettings } from './sync-settings';
import { LogsSettings } from './logs-settings';

interface Tab {
   key: string;
   label: string;
   icon: string;
   component: React.ComponentType;
}

export const MainSettings = () => {
   const tabs: Tab[] = [
      {
         key: 'setup',
         label: 'Setup',
         icon: 'dashicons-admin-settings',
         component: SetupSettings,
      },
      {
         key: 'theme',
         label: 'Theme',
         icon: 'dashicons-admin-appearance',
         component: ThemeSettings,
      },
      {
         key: 'sync',
         label: 'Sync',
         icon: 'dashicons-update',
         component: SyncSettings,
      },
      {
         key: 'logs',
         label: 'Logs',
         icon: 'dashicons-list-view',
         component: LogsSettings,
      },
   ];

   const getInitialTab = () => {
      const urlParams = new URLSearchParams(window.location.search);
      const tabParam = urlParams.get('tab');
      return tabParam && tabs.find(t => t.key === tabParam) ? tabParam : tabs[0].key;
   };

   const [activeTab, setActiveTab] = useState<string>(getInitialTab());

   useEffect(() => {
      const urlParams = new URLSearchParams(window.location.search);
      urlParams.set('tab', activeTab);
      const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
      window.history.replaceState({}, '', newUrl);
   }, [activeTab]);

   const handleTabClick = (tabKey: string) => {
      setActiveTab(tabKey);
   };

   return (
      <div className="trvlr-settings-wrapper">
         <nav className="trvlr-tabs-nav">
            {tabs.map(tab => (
               <a
                  key={tab.key}
                  href="#"
                  className={`trvlr-tab-link ${activeTab === tab.key ? 'active' : ''}`}
                  onClick={(e) => {
                     e.preventDefault();
                     handleTabClick(tab.key);
                  }}
               >
                  <span className={`dashicons ${tab.icon}`}></span>
                  {tab.label}
               </a>
            ))}
         </nav>

         <div className="trvlr-tabs-content">
            {tabs.map(tab => (
               <div
                  key={tab.key}
                  className={`trvlr-tab-pane ${activeTab === tab.key ? 'active' : ''}`}
                  style={{ display: activeTab === tab.key ? 'block' : 'none' }}
               >
                  <tab.component />
               </div>
            ))}
         </div>
      </div>
   );
};

