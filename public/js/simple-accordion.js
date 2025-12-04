/**
 * Simple Accordion Controller
 * Minimal accordion functionality with accessibility features
 */
class SimpleAccordion {
   constructor(element, options = {}) {
      this.accordion = element;
      this.options = {
         expandFirst: true,
         independentToggle: true,
         animationDuration: 300,
         ...options
      };

      this.items = [];
      this.init();
   }

   init() {
      const items = this.accordion.querySelectorAll('.accordion__item');

      items.forEach((item, index) => {
         const trigger = item.querySelector('.accordion__trigger');
         const content = item.querySelector('.accordion__content');

         if (!trigger || !content) return;

         const accordionItem = {
            item,
            trigger,
            content,
            index,
            isOpen: false
         };

         this.setupItem(accordionItem);
         this.items.push(accordionItem);
      });

      // Expand first item if option is enabled
      if (this.options.expandFirst && this.items.length > 0) {
         this.open(this.items[0], false);
      }
   }

   setupItem(accordionItem) {
      const { trigger, content, index } = accordionItem;

      // Set up ARIA attributes
      const triggerId = trigger.id || `accordion__trigger-${index}`;
      const contentId = content.id || `accordion__content-${index}`;

      trigger.id = triggerId;
      content.id = contentId;

      trigger.setAttribute('aria-controls', contentId);
      trigger.setAttribute('aria-expanded', 'false');
      trigger.setAttribute('role', 'button');
      trigger.setAttribute('tabindex', '0');

      content.setAttribute('role', 'region');
      content.setAttribute('aria-labelledby', triggerId);
      content.style.height = '0';
      content.style.overflow = 'hidden';

      // Event listeners
      trigger.addEventListener('click', (e) => {
         e.preventDefault();
         this.toggle(accordionItem);
      });

      trigger.addEventListener('keydown', (e) => {
         if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.toggle(accordionItem);
         }
      });
   }

   toggle(accordionItem) {
      if (accordionItem.isOpen) {
         this.close(accordionItem);
      } else {
         // Close other items if not independent toggle
         if (!this.options.independentToggle) {
            this.items.forEach(item => {
               if (item !== accordionItem && item.isOpen) {
                  this.close(item);
               }
            });
         }
         this.open(accordionItem);
      }
   }

   open(accordionItem, animate = true) {
      const { item, trigger, content } = accordionItem;

      accordionItem.isOpen = true;
      item.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');

      if (animate) {
         // Get the natural height
         content.style.height = 'auto';
         const height = content.scrollHeight + 'px';
         content.style.height = '0';

         // Force reflow
         content.offsetHeight;

         // Animate to natural height
         content.style.transition = `height ${this.options.animationDuration}ms ease-out`;
         content.style.height = height;

         // Clean up after animation
         setTimeout(() => {
            content.style.height = 'auto';
            content.style.transition = '';
         }, this.options.animationDuration);
      } else {
         content.style.height = 'auto';
      }
   }

   close(accordionItem, animate = true) {
      const { item, trigger, content } = accordionItem;

      accordionItem.isOpen = false;
      item.classList.remove('is-open');
      trigger.setAttribute('aria-expanded', 'false');

      if (animate) {
         const height = content.scrollHeight + 'px';
         content.style.height = height;

         // Force reflow
         content.offsetHeight;

         content.style.transition = `height ${this.options.animationDuration}ms ease-out`;
         content.style.height = '0';

         // Clean up after animation
         setTimeout(() => {
            content.style.transition = '';
         }, this.options.animationDuration);
      } else {
         content.style.height = '0';
      }
   }

   // Public methods for external control
   openItem(index) {
      if (this.items[index]) {
         this.open(this.items[index]);
      }
   }

   closeItem(index) {
      if (this.items[index]) {
         this.close(this.items[index]);
      }
   }

   closeAll() {
      this.items.forEach(item => {
         if (item.isOpen) {
            this.close(item);
         }
      });
   }
}