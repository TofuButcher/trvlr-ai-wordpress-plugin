class TrvlrBookingSystem {
   constructor(config) {
      this.config = config;
      this.state = {
         defaultFormCheckAvailability: false,
         dateSelectedInfo: {},
         currentAttractionId: null
      };
      this.elements = {};
   }

   init() {
      if (!this.validateConfig()) return;

      this.setupDOM();
      this.cacheElements();
      this.loadInitialView();
      this.bindEvents();
      this.updateCart();
      this.updateBookingCalendarSize();
      this.handlePageRefresh();
   }

   validateConfig() {
      return this.config.baseIframeUrl && this.config.baseIframeUrl.length > 0;
   }

   setupDOM() {
      if (!document.getElementById('trvlr-booking-modal')) {
         document.body.insertAdjacentHTML('beforeend', `
            <dialog id="trvlr-booking-modal" class="modal-dialog">
               <div id="trvlr-booking-modal-content" class="iframe-cont"></div>
            </dialog>
         `);
      }

      if (!document.getElementById('checkout-modal-iframe')) {
         document.body.insertAdjacentHTML('beforeend', `
            <div id="checkout-modal-iframe" style="display: none">
               <iframe src="${this.config.baseIframeUrl}/checkout-modal/index.html" 
                  frameborder="0" 
                  title="Checkout Modal"
                  class="iframe-cont" 
                  id="checkout-modal-btn-iframe">
               </iframe>
            </div>
         `);
      }
   }

   cacheElements() {
      this.elements = {
         modal: document.getElementById('trvlr-booking-modal'),
         modalContent: document.getElementById('trvlr-booking-modal-content'),
         checkoutContainer: document.getElementById('checkout-modal-iframe'),
         checkoutButton: document.getElementById('checkout-modal-btn-iframe')
      };
   }

   loadInitialView() {
      if (document.querySelector('[attraction-id].trvlr-check-availability')) {
         const attractionId = document.querySelector('[attraction-id].trvlr-check-availability').getAttribute('attraction-id');
         if (attractionId) {
            this.state.currentAttractionId = attractionId;
            this.loadCheckAvailabilityForm(attractionId);
            this.state.defaultFormCheckAvailability = true;
         }
      }
   }

   bindEvents() {
      if (!this.elements.modal) return;
      this.elements.modal.addEventListener('click', (e) => {
         if (e.target === this.elements.modal) {
            this.closeModalWithAnimation();
         }
      });

      this.elements.modal.addEventListener('cancel', (e) => {
         e.preventDefault();
         this.closeModalWithAnimation();
      });

      this.elements.modal.addEventListener('close', () => {
         if (this.elements.modal.classList.contains('closing')) {
            this.elements.modal.classList.remove('closing');
            this.elements.modalContent.innerHTML = '';
         }
      });

      document.body.addEventListener('click', (e) => {
         const bookNowBtn = e.target.closest('[attraction-id].trvlr-book-now');
         if (bookNowBtn) {
            this.handleBookNowClick(bookNowBtn);
         }
         const checkAvailabilityBtn = e.target.closest('[attraction-id].trvlr-check-availability');
         if (checkAvailabilityBtn) {
            this.handleCheckAvailabilityClick(checkAvailabilityBtn);
         }
      });

      window.addEventListener('message', (e) => this.handleMessage(e));
   }

   handleBookNowClick(button) {
      const attractionId = button.getAttribute('attraction-id');
      if (attractionId === '' || attractionId === null || attractionId === undefined) return;
      this.state.currentAttractionId = attractionId;
      this.showBookNowForm();
      this.elements.modal.showModal();
   }

   handleCheckAvailabilityClick(button) {
      const attractionId = button.getAttribute('attraction-id');
      if (attractionId === '' || attractionId === null || attractionId === undefined) return;
      this.state.currentAttractionId = attractionId;
      if (!this.elements.modalContent.querySelector(`iframe[src="${this.config.baseIframeUrl}/date-picker2/index.html?attr_id=${attractionId}"]`)) {
         this.loadCheckAvailabilityForm(attractionId);
      }
      this.elements.modal.showModal();
   }

   closeModalWithAnimation() {
      if (!this.elements.modal?.hasAttribute('open')) return;

      this.elements.modal.classList.add('closing');
      setTimeout(() => this.elements.modal.close(), 200);
      if (this.state.defaultFormCheckAvailability) {
         this.loadCheckAvailabilityForm();
      }
   }

   loadCheckAvailabilityForm(attractionId = this.state.currentAttractionId) {
      this.elements.modalContent.innerHTML = `
         <iframe
            style="width: 100%; height: 100%;"
            frameborder="0"
            src="${this.config.baseIframeUrl}/date-picker2/index.html?attr_id=${attractionId}"
            title="Check Availability"
            class="iframe-cont"
            id="check-availability-modal-iframe"
         ></iframe>
      `;
   }

   showBookNowForm() {
      this.elements.modalContent.innerHTML = `
         <iframe
            style="width: 100%; height: 100%;"
            frameborder="0"
            src="${this.config.baseIframeUrl}/book-now/index.html?attr_id=${this.state.currentAttractionId}"
            title="Book Now"
            class="iframe-cont"
            id="book-now-modal-iframe"
         ></iframe>
      `;
   }

   showCheckoutForm() {
      this.elements.modalContent.innerHTML = `
         <iframe
            class="iframe-cont"
            src="${this.config.baseIframeUrl}/payment/index.html?attr_id=${this.state.currentAttractionId}"
            style="width: 100%; height: 100%;"
            frameborder="0"
            id="cart-iframe"
         ></iframe>
      `;
   }

   showPassengerDetailsForm(data) {
      this.state.dateSelectedInfo.selectedTime = data;
      this.showCheckoutForm();

      const cartIframe = document.getElementById('cart-iframe');
      cartIframe.addEventListener('load', () => {
         cartIframe.contentWindow.postMessage({
            type: 'POST_FLOW_INITIATE',
            data: this.state.dateSelectedInfo
         }, '*');
      });
   }

   openCheckout() {
      this.showCheckoutForm();

      const cartIframe = document.getElementById('cart-iframe');
      cartIframe.addEventListener('load', () => {
         cartIframe.contentWindow.postMessage({ type: 'OPEN_CHECKOUT' }, '*');
      });
   }

   updateCart(cartData) {
      if (cartData) {
         localStorage.setItem('trvlr-cart', JSON.stringify(cartData));
      }

      let cart = null;
      const cartString = localStorage.getItem('trvlr-cart');

      if (cartString) {
         try {
            cart = JSON.parse(cartString);
         } catch (error) {
            localStorage.removeItem('trvlr-cart');
         }
      }

      if (!cart || !this.elements.checkoutContainer) return;

      const count = cart?.count ?? 0;

      if (this.elements.checkoutButton) {
         this.elements.checkoutButton.src = this.elements.checkoutButton.src;
      }

      this.elements.checkoutContainer.style.display = count > 0 ? 'block' : 'none';
   }

   updateBookingCalendarSize() {
      const calendar = document.getElementById('trvlr-booking-calendar-iframe');
      if (calendar) {
         calendar.addEventListener('load', () => {
            const height = calendar.contentWindow.document.body.scrollHeight;
            calendar.style.height = `${height}px`;
         });
      }
   }

   handlePageRefresh() {
      if (localStorage.getItem('isRefreshPage')) {
         localStorage.removeItem('isRefreshPage');
      }
   }

   handleMessage(event) {
      const { type, data } = event.data;

      console.log(event.data);

      const handlers = {
         'CART_UPDATED': () => {
            localStorage.setItem('trvlr-cart', JSON.stringify(data));
            this.updateCart(data);
         },
         'OPEN_MODAL': () => {
            this.elements.modal.showModal();
            this.showPassengerDetailsForm(data ?? '');
         },
         'flatpickr:dateSelected': () => {
            this.state.dateSelectedInfo = event.data.detail;
         },
         'GO_TO_CART': () => {
            this.state.dateSelectedInfo.isBookNow = true;
            this.showPassengerDetailsForm(data ?? '');
            this.updateCart();
         },
         'OPEN_CHECKOUT_MODAL': () => {
            this.elements.modal.showModal();
            this.openCheckout();
         },
         'UPDATE_CART': () => {
            this.updateCart();
         },
         'CLOSE_MODAL': () => {
            if (data) {
               this.showBookNowForm();
            } else {
               this.closeModalWithAnimation();
            }
         },
         'CLOSE_BOOK_NOW_MODAL': () => {
            this.elements.modal.close();
            if (this.state.defaultFormCheckAvailability) {
               this.loadCheckAvailabilityForm();
            }
         },
         'REDIRECT': () => {
            window.location.href = event.data.url;
         },
         'REFRESH_PAGE': () => {
            localStorage.setItem('isRefreshPage', 'true');
            setTimeout(() => {
               window.location.href = this.config.homeUrl;
            }, 2000);
         }
      };

      if (handlers[type]) {
         handlers[type]();
      }
   }
}

document.addEventListener('DOMContentLoaded', () => {
   if (typeof trvlrConfig !== 'undefined') {
      if (!trvlrConfig.baseIframeUrl || trvlrConfig.baseIframeUrl.length === 0) {
         console.warn('Trvlr AI base domain is not defined');
         return;
      }
      const bookingSystem = new TrvlrBookingSystem(trvlrConfig);
      bookingSystem.init();
      window.trvlrBookingSystem = bookingSystem;
   } else {
      console.warn('Trvlr AI configuration is not defined');
   }
});