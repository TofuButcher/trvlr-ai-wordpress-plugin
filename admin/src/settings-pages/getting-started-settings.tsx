import { __ } from '@wordpress/i18n';
import React from '@wordpress/components';
import { PageHeading } from '../components/page-heading';
import { PluginInstructions } from '../components/plugin-instructions';

export const GettingsStartedSettings = () => {
   return (
      <>
         <PageHeading
            text="Getting Started with TRVLR Wordpress Manager"
         />
         <div className="trvlr-settings-section-spacer">
            <PluginInstructions />
            <div>
               <h2 style={{ marginBottom: '5px' }}>Got Questions?</h2>
               <p style={{ fontSize: '18px', margin: '0' }}>Write to <a href="mailto:team@trvlr.ai" target="_blank">team@trvlr.ai</a> for support.</p>
            </div>
         </div >
      </>
   );
}
