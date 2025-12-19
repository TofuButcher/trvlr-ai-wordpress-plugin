import React from '@wordpress/element';
import { __ } from '@wordpress/i18n';


export const PageHeader = ({ title, description }: { title: string, description?: string }) => {
   return (
      <div className="trvlr-settings-page-header">
         <h2>{__(title, 'trvlr')}</h2>
         {description && <p>{__(description, 'trvlr')}</p>}
      </div>
   );
}