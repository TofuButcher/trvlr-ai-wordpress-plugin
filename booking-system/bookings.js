document.addEventListener('DOMContentLoaded', function () {
   let dateSelectedInfo = {};
   let currentAttractionId = null;
   const baseIframeUrl = trvlrConfig.baseIframeUrl || '';
   const homeUrl = trvlrConfig.homeUrl || '';

   if (!baseIframeUrl) {
      console.warn('Trvlr base domain not configured');
      return;
   }

   if (!document.getElementById('trvlr-booking-modal')) {
      console.warn('Booking modal not found, creating fallback');
      const modalHtml = `
         <dialog id="trvlr-booking-modal" class="modal-dialog">
            <div id="trvlr-booking-modal-content" class="iframe-cont">
            </div>
         </dialog>
      `;
      document.body.insertAdjacentHTML('beforeend', modalHtml);
   }

   if (!document.getElementById('checkout-modal-iframe')) {
      console.warn('Checkout modal iframe not found, creating fallback');
      const checkoutHtml = `
         <div id="checkout-modal-iframe" style="display: none">
            <iframe src="${baseIframeUrl}/checkout-modal/index.html" frameborder="0" title="Checkout Modal"
               class="iframe-cont" id="checkout-modal-btn-iframe">
            </iframe>
         </div>
      `;
      document.body.insertAdjacentHTML('beforeend', checkoutHtml);
   }

   const bookNowModal = document.getElementById('trvlr-booking-modal');
   const bookNowModalContent = document.getElementById('trvlr-booking-modal-content');

   if (!bookNowModal || !bookNowModalContent) {
      console.error('Booking modal structure not found. Halting booking script.');
      return;
   }

   bookNowModal.addEventListener('click', function (event) {
      if (event.target === bookNowModal) {
         closeDialogWithAnimation(bookNowModal);
      }
   });
   bookNowModal.addEventListener('cancel', (event) => {
      event.preventDefault();
      closeDialogWithAnimation(bookNowModal);
   });
   bookNowModal.addEventListener('close', () => {
      if (bookNowModal.classList.contains('closing')) {
         bookNowModal.classList.remove('closing');
         bookNowModalContent.innerHTML = '';
      }
   });

   function closeDialogWithAnimation(dialog) {
      if (!dialog || typeof dialog.close !== 'function' || !dialog.hasAttribute('open')) return;
      console.log("Closing dialog with animation.");
      dialog.classList.add('closing');
      setTimeout(() => {
         dialog.close();
      }, 200);
   }

   document.querySelectorAll('[attraction-id].book-now').forEach((btn) => {
      btn.addEventListener("click", function () {
         currentAttractionId = btn.getAttribute("attraction-id");
         console.log(`Book Now button clicked. Attraction ID: ${currentAttractionId}`);
         bookNowForm();
         bookNowModal.showModal();
      });
   });

   function bookNowForm() {
      console.log('Setting modal to book now form');
      bookNowModalContent.innerHTML = `
     <iframe
       style="width: 100%; height: 100%;"
       frameborder="0"
       src="${baseIframeUrl}/book-now/index.html?attr_id=${currentAttractionId}"
       title="Book Now"
       class="iframe-cont"
       id="book-now-modal-iframe"
     ></iframe>`;
   }

   function passengerDetailsAndCheckoutForm() {
      console.log('Setting modal to passenger details and checkout form');
      bookNowModalContent.innerHTML = `
     <iframe
      class="iframe-cont"
      src="${baseIframeUrl}/payment/index.html?attr_id=${currentAttractionId}"
      style="width: 100%; height: 100%;"
      frameborder="0"
      id="cart-iframe"
    ></iframe>
   `;
   }

   function passengerDetailsForm(data) {
      console.log('Setting modal to passenger details form with data:', data);
      dateSelectedInfo.selectedTime = data;
      passengerDetailsAndCheckoutForm();
      let cartIframe = document.getElementById("cart-iframe");
      cartIframe.addEventListener("load", function () {
         console.log('Cart iframe loaded, sending POST_FLOW_INITIATE message');
         cartIframe.contentWindow.postMessage(
            {
               type: "POST_FLOW_INITIATE",
               data: dateSelectedInfo,
            },
            "*"
         );
      });
   }

   function openCheckoutForm() {
      console.log('Opening checkout form');
      passengerDetailsAndCheckoutForm();
      let cartIframe = document.getElementById("cart-iframe");
      cartIframe.addEventListener("load", function () {
         console.log('Cart iframe loaded, sending OPEN_CHECKOUT message');
         cartIframe.contentWindow.postMessage(
            {
               type: "OPEN_CHECKOUT",
            },
            "*"
         );
      });
   }

   function updateBookingCalendarSize() {
      const bookingCalendar = document.getElementById('trvlr-booking-calendar-iframe');
      if (bookingCalendar) {
         bookingCalendar.addEventListener('load', function () {
            let height = bookingCalendar.contentWindow.document.body.scrollHeight;
            bookingCalendar.style.height = `${height}px`;
         });
      }
   }
   updateBookingCalendarSize();

   function updateCart(cartData) {
      if (cartData) {
         localStorage.setItem('trvlr-cart', JSON.stringify(cartData));
      }
      const cartString = localStorage.getItem('trvlr-cart');
      let cart = null;
      if (cartString) {
         try {
            cart = JSON.parse(cartString);
         } catch (error) {
            localStorage.removeItem('trvlr-cart');
            cart = null;
         }
      }
      const count = cart?.count ?? 0;
      const cartElement = document.getElementById('checkout-modal-iframe');
      const cartButton = document.getElementById('checkout-modal-btn-iframe');

      if (cartButton) {
         cartButton.src = cartButton.src;
      }

      if (!cartElement) {
         console.log('Cart element not found');
         return;
      }

      if (!cart) {
         console.log('Cart data not found');
         return;
      }

      cartElement.style.display = count > 0 ? 'block' : 'none';
   }

   updateCart();

   window.addEventListener("message", function (event) {
      console.log('Received message event:', event.data);

      if (event.data.type === "CART_UPDATED") {
         localStorage.setItem('trvlr-cart', JSON.stringify(event.data.data));
         updateCart(event.data.data);
      }

      if (event.data.type === "OPEN_MODAL") {
         bookNowModal.showModal();
         passengerDetailsForm(event?.data?.data ?? "");
      }

      if (event?.data?.type === "flatpickr:dateSelected") {
         dateSelectedInfo = event?.data?.detail;
      }

      if (event.data.type === "GO_TO_CART") {
         dateSelectedInfo.isBookNow = true;
         passengerDetailsForm(event?.data?.data ?? "");
         updateCart();
      }

      if (event.data.type === "OPEN_CHECKOUT_MODAL") {
         bookNowModal.showModal();
         openCheckoutForm();
      }

      if (event.data.type === "UPDATE_CART") {
         updateCart();
      }

      if (event.data.type === "CLOSE_MODAL") {
         console.log('Closing modal');
         if (!event?.data?.data) {
            closeDialogWithAnimation(bookNowModal);
         } else {
            bookNowForm();
         }
      }

      if (event.data.type === "CLOSE_BOOK_NOW_MODAL") {
         console.log('Closing book now modal');
         bookNowModal.close();
      }

      if (event.data.type == "REDIRECT") {
         window.location.href = event?.data?.url;
      }
   });

   document.addEventListener('DOMContentLoaded', function () {
      const isRefreshPage = localStorage.getItem('isRefreshPage');
      if (isRefreshPage) {
         console.log('Refresh page flag found, clearing localStorage');
         localStorage.removeItem('isRefreshPage');
      }
   });

});

