import React from '@wordpress/element';
import { __ } from '@wordpress/i18n';


export const PageHeading = ({ text, level = 2 }: { text: string, level?: number }) => {
   const headingTags = [1, 2, 3, 4, 5, 6];
   if (!headingTags.includes(level)) {
      level = 2;
   }

   const HeadingTag = `h${level}` as keyof JSX.IntrinsicElements;

   return (
      <HeadingTag className={`${level === 2 ? 'trvlr-settings-page-heading' : ''}`}>{__(text, 'trvlr')}</HeadingTag>
   );
}