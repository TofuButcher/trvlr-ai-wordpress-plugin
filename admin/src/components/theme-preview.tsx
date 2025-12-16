import React from '@wordpress/element';

export const AttractionCardPreview = () => {
   return (
      <div className="trvlr-card trvlr-card--attraction" style={{ width: '100%' }}>
         <div className="trvlr-card__image-wrap">
            <img
               width="300"
               height="225"
               src="https://picsum.photos/id/866/300/225"
               className="trvlr-card__image wp-post-image"
               alt="Preview attraction"
            />
            <div className="trvlr-popular-badge">
               <svg className="trvlr-icon trvlr-popular-badge__icon" viewBox="0 0 18 18">
                  {/* <use href="#icon-star"></use> */}
                  <path d="M9.00002 0.5C9.38064 0.5 9.72803 0.716313 9.8965 1.05762L11.9805 5.28027L16.6446 5.96289C17.0211 6.01793 17.3338 6.28252 17.4512 6.64453C17.5684 7.00643 17.4698 7.40351 17.1973 7.66895L13.8242 10.9531L14.6211 15.5957C14.6855 15.9709 14.5307 16.3505 14.2227 16.5742C13.9148 16.7978 13.5067 16.8273 13.1699 16.6504L9.00002 14.457L4.8301 16.6504C4.49331 16.8273 4.0852 16.7978 3.77736 16.5742C3.46939 16.3505 3.31458 15.9709 3.37893 15.5957L4.17482 10.9531L0.802754 7.66895C0.530236 7.40351 0.431671 7.00643 0.548848 6.64453C0.666226 6.28252 0.978929 6.01793 1.35549 5.96289L6.01857 5.28027L8.10354 1.05762L8.17482 0.935547C8.35943 0.665559 8.66699 0.5 9.00002 0.5ZM7.57912 6.6377C7.43357 6.93249 7.15248 7.13702 6.82717 7.18457L3.64748 7.64844L5.94729 9.88867C6.18316 10.1184 6.29103 10.4499 6.23537 10.7744L5.6924 13.9365L8.5342 12.4424C8.82558 12.2891 9.17446 12.2891 9.46584 12.4424L12.3067 13.9365L11.7647 10.7744C11.709 10.4499 11.8169 10.1184 12.0528 9.88867L14.3516 7.64844L11.1729 7.18457C10.8476 7.13702 10.5665 6.93249 10.4209 6.6377L9.00002 3.75781L7.57912 6.6377Z"></path>
               </svg>
               <span className="trvlr-popular-badge__text">Popular</span>
            </div>
         </div>
         <div className="trvlr-card__content">
            <h3 className="trvlr-title trvlr-card__title">
               <a href="#">
                  Gordon River 3:15pm Afternoon Cruise – Upper Deck Window Seating				</a>
            </h3>
            <div className="trvlr-card__meta">
               <div className="trvlr-duration">
                  <svg className="trvlr-duration__icon" viewBox="0 0 18 18">
                     <g clip-path="url(#clip0_133_223)">
                        <path d="M15.5 9C15.5 5.41015 12.5899 2.5 9 2.5C5.41015 2.5 2.5 5.41015 2.5 9C2.5 12.5899 5.41015 15.5 9 15.5C12.5899 15.5 15.5 12.5899 15.5 9ZM17.5 9C17.5 13.6944 13.6944 17.5 9 17.5C4.30558 17.5 0.5 13.6944 0.5 9C0.5 4.30558 4.30558 0.5 9 0.5C13.6944 0.5 17.5 4.30558 17.5 9Z"></path>
                        <path d="M8 4.5C8 3.94772 8.44772 3.5 9 3.5C9.55228 3.5 10 3.94772 10 4.5V8.38184L12.4473 9.60547C12.9412 9.85246 13.1415 10.4533 12.8945 10.9473C12.6475 11.4412 12.0467 11.6415 11.5527 11.3945L8.55273 9.89453C8.21395 9.72514 8 9.37877 8 9V4.5Z"></path>
                     </g>
                     <defs>
                        <clipPath id="clip0_133_223">
                           <rect width="18" height="18"></rect>
                        </clipPath>
                     </defs>
                  </svg>
                  <span className="trvlr-duration__value">5 hours 15 mins</span>
               </div>
            </div>
            <div className="trvlr-card__footer">
               <div className="trvlr-card__price">
                  <div className="trvlr-sale__badge"><span>% Special Deal</span></div>											<div className="trvlr-price">
                     <span className="trvlr-price__value">from $215</span>
                     <span className="trvlr-price__type">per person</span>
                  </div>
               </div>
               <button className="trvlr-card__button trvlr-book-now">
                  <span>Book Now</span>
                  <svg viewBox="0 0 21 21">
                     {/* <symbol id="icon-arrow-right" viewBox="0 0 21 21"> */}
                     <path d="M9.83496 4.29285C10.2255 3.90241 10.8585 3.90236 11.249 4.29285L16.791 9.83484C16.7969 9.84072 16.8019 9.84741 16.8076 9.8534C16.8194 9.86578 16.8307 9.87851 16.8418 9.89148C16.8509 9.90206 16.8596 9.91284 16.8682 9.92371C16.879 9.93742 16.8893 9.95142 16.8994 9.9657C17.1465 10.3148 17.143 10.7848 16.8896 11.1307C16.8847 11.1375 16.8801 11.1446 16.875 11.1512C16.8612 11.1691 16.8462 11.1859 16.8311 11.203C16.8259 11.2089 16.8208 11.2148 16.8154 11.2206C16.807 11.2297 16.7999 11.2401 16.791 11.2489L11.249 16.7899C10.8585 17.1804 10.2255 17.1804 9.83496 16.7899C9.44461 16.3994 9.44449 15.7663 9.83496 15.3759L13.668 11.5419H5C4.4478 11.5419 4.00013 11.094 4 10.5419C4 9.98959 4.44772 9.54187 5 9.54187H13.6699L9.83496 5.70691C9.44444 5.31639 9.44444 4.68337 9.83496 4.29285Z"></path>
                     {/* </symbol> */}
                  </svg>
               </button>
            </div>
         </div>
      </div>
   )
}