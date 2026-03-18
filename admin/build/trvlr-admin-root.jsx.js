/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./admin/src/components/page-heading.tsx":
/*!***********************************************!*\
  !*** ./admin/src/components/page-heading.tsx ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PageHeading: () => (/* binding */ PageHeading)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__);



const PageHeading = ({
  text,
  level = 2
}) => {
  const headingTags = [1, 2, 3, 4, 5, 6];
  if (!headingTags.includes(level)) {
    level = 2;
  }
  const HeadingTag = `h${level}`;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(HeadingTag, {
    className: `${level === 2 ? 'trvlr-settings-page-heading' : 'trvlr-settings-heading'}`,
    children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)(text, 'trvlr')
  });
};

/***/ }),

/***/ "./admin/src/components/plugin-instructions.jsx":
/*!******************************************************!*\
  !*** ./admin/src/components/plugin-instructions.jsx ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   PluginInstructions: () => (/* binding */ PluginInstructions)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__);




const getInstructionSteps = () => [{
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('1. Connect to TRVLR', 'trvlr'),
  content: () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Navigate to the Connection tab and enter your Organization ID.', 'trvlr')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: "info",
      isDismissible: false,
      style: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'flex-start',
        gap: '12px',
        marginTop: '12px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
        style: {
          margin: 0,
          marginBottom: '4px'
        },
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Your organization ID is your websites domain without the any prefixes ( subdomain. / www.) or suffixes ( .com / .org ).', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
        style: {
          margin: 0
        },
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('For example if your website is https://www.example.com, your organization ID is "example"', 'trvlr')
      })]
    })]
  }),
  dropdowns: []
}, {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('2. Sync Your Attractions', 'trvlr'),
  content: () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Navigate to the Sync tab to import your attractions from TRVLR.', 'trvlr')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      style: {
        marginTop: '12px',
        marginBottom: '12px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Manual Sync:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
        children: [" ", (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Click "Sync Now" to import attractions on demand. Progress is shown in real-time.', 'trvlr')]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      style: {
        marginBottom: '12px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Automatic Sync:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
        children: [" ", (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Enable scheduled syncing to keep attractions updated automatically.', 'trvlr')]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: "warning",
      isDismissible: false,
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Manual edits made in WordPress are preserved during sync. Review them in the Custom Edits section or use the "Sync from TRVLR" button on individual attraction pages to override.', 'trvlr')
    })]
  }),
  dropdowns: []
}, {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('3. Add Booking Buttons', 'trvlr'),
  content: () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Add booking functionality to any button or link by adding these attributes:', 'trvlr')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("ul", {
      style: {
        marginTop: '12px',
        marginBottom: '12px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("li", {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
          children: "class=\"trvlr-book-now\""
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("li", {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
          children: "attraction-id=\"YOUR_TRVLR_ID\""
        })
      })]
    })]
  }),
  dropdowns: [{
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Code Example', 'trvlr'),
    content: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
      style: {
        background: '#f6f7f7',
        padding: '12px',
        borderRadius: '4px',
        overflow: 'auto',
        fontSize: '13px'
      },
      children: `<button 
   class="trvlr-book-now" 
   attraction-id="123">
   Book Now
</button>`
    })
  }]
}, {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('4. Display Attractions', 'trvlr'),
  content: () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Use shortcodes to display attractions on any page:', 'trvlr')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      style: {
        marginTop: '16px',
        marginBottom: '16px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('All Attractions:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        style: {
          marginTop: '8px'
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
          children: "[trvlr_attraction_cards]"
        })
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      style: {
        marginTop: '16px',
        marginBottom: '16px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Single Attraction:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        style: {
          marginTop: '8px'
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
          children: "[trvlr_attraction_card id=\"123\"]"
        })
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      style: {
        marginTop: '16px',
        marginBottom: '16px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Booking Calendar:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        style: {
          marginTop: '8px'
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
          children: "[trvlr_booking_calendar]"
        })
      })]
    })]
  }),
  dropdowns: [{
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Shortcode Parameters', 'trvlr'),
    content: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        style: {
          display: 'block',
          marginTop: '12px'
        },
        children: "[trvlr_attraction_cards]"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("ul", {
        style: {
          marginTop: '8px',
          marginBottom: '16px'
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "posts_per_page"
          }), " - Number of attractions (-1 for all, default: -1)"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "orderby"
          }), " - Sort by: date, title, meta_value, etc. (default: date)"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "order"
          }), " - ASC or DESC (default: DESC)"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "ids"
          }), " - Comma-separated list of post IDs to include"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "exclude"
          }), " - Comma-separated list of post IDs to exclude"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "tag"
          }), " - Filter by tag name (comma-separated)"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "tag_id"
          }), " - Filter by tag ID (comma-separated)"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "tag_slug"
          }), " - Filter by tag slug (comma-separated)"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "tag_relation"
          }), " - AND or OR for multiple tags (default: AND)"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "meta_key"
          }), " - Meta key to filter/sort by"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "meta_value"
          }), " - Meta value to match"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "meta_compare"
          }), " - Comparison operator (=, !=, >, <, etc.)"]
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
        style: {
          background: '#f6f7f7',
          padding: '12px',
          borderRadius: '4px',
          fontSize: '13px',
          marginBottom: '16px'
        },
        children: `// Basic usage
[trvlr_attraction_cards posts_per_page="6" orderby="title" order="ASC"]

// Filter by tags
[trvlr_attraction_cards tag="popular,featured" tag_relation="OR"]

// Filter by meta field
[trvlr_attraction_cards meta_key="trvlr_is_on_sale" meta_value="1"]

// Exclude specific attractions
[trvlr_attraction_cards exclude="10,20,30"]`
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        style: {
          display: 'block',
          marginTop: '12px'
        },
        children: "[trvlr_attraction_card]"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("ul", {
        style: {
          marginTop: '8px',
          marginBottom: '16px'
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "id"
          }), " - Attraction post ID (defaults to current post)"]
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
        style: {
          background: '#f6f7f7',
          padding: '12px',
          borderRadius: '4px',
          fontSize: '13px',
          marginBottom: '16px'
        },
        children: `[trvlr_attraction_card id="123"]`
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        style: {
          display: 'block',
          marginTop: '12px'
        },
        children: "[trvlr_booking_calendar]"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("ul", {
        style: {
          marginTop: '8px',
          marginBottom: '16px'
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "id"
          }), " - Attraction post ID (auto-detected on single pages)"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "width"
          }), " - Calendar width (default: 450px)"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "height"
          }), " - Calendar height (default: 600px)"]
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
        style: {
          background: '#f6f7f7',
          padding: '12px',
          borderRadius: '4px',
          fontSize: '13px'
        },
        children: `[trvlr_booking_calendar id="123" width="500px" height="700px"]`
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        style: {
          display: 'block',
          marginTop: '16px'
        },
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Other Shortcodes:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("ul", {
        style: {
          marginTop: '8px'
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "[trvlr_attraction_gallery]"
          }), " - Display image gallery with thumbnails"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "[trvlr_description]"
          }), " - Full description"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "[trvlr_short_description]"
          }), " - Short description"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "[trvlr_accordion]"
          }), " - Inclusions, locations, and additional info"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "[trvlr_duration]"
          }), " - Duration with icon"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "[trvlr_advertised_price]"
          }), " - Price display"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "[trvlr_sale_badge]"
          }), " - Sale indicator"]
        })]
      })]
    })
  }]
}, {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('5. Single Attraction Pages', 'trvlr'),
  content: () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('The plugin automatically creates detailed pages for each attraction with gallery, description, booking calendar, and more.', 'trvlr')
    })
  }),
  dropdowns: [{
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Customize Templates', 'trvlr'),
    content: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Create a custom template in your theme:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
        style: {
          background: '#f6f7f7',
          padding: '12px',
          borderRadius: '4px',
          marginTop: '12px',
          fontSize: '13px'
        },
        children: `/wp-content/themes/your-theme/single-trvlr_attraction.php`
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
        style: {
          marginTop: '12px',
          display: 'block'
        },
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Use these template functions:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("ul", {
        style: {
          marginTop: '8px'
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "trvlr_gallery($post_id)"
          }), " - Image gallery"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "trvlr_accordion($post_id)"
          }), " - Collapsible content sections"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "trvlr_booking_calendar($post_id)"
          }), " - Booking calendar"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "trvlr_description($post_id)"
          }), " - Full description"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "trvlr_short_description($post_id)"
          }), " - Short description"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "trvlr_advertised_price($post_id)"
          }), " - Price display"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "trvlr_duration($post_id)"
          }), " - Duration"]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("code", {
            children: "trvlr_locations($post_id)"
          }), " - Start/End locations"]
        })]
      })]
    })
  }]
}, {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('6. Customize Appearance', 'trvlr'),
  content: () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Navigate to the Theme tab to customize colors, typography, and card styles.', 'trvlr')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("ul", {
      style: {
        marginTop: '12px',
        marginBottom: '12px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("li", {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Colors: Primary, secondary, accent, and sale badge', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("li", {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Typography: Font sizes for headings and text', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("li", {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Cards: Border radius, spacing, shadows, and hover effects', 'trvlr')
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('All styling uses CSS variables for easy customization.', 'trvlr')
    })]
  }),
  dropdowns: []
}, {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Advanced: PHP Development', 'trvlr'),
  content: () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('For developers building custom templates and features.', 'trvlr')
    })
  }),
  dropdowns: [{
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Display Functions', 'trvlr'),
    content: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
      style: {
        background: '#f6f7f7',
        padding: '12px',
        borderRadius: '4px',
        fontSize: '13px',
        whiteSpace: 'pre-wrap'
      },
      children: `// Display single card
echo trvlr_card($post_id);

// Display multiple cards (with optional args)
echo trvlr_cards($args);

// Display gallery
echo trvlr_gallery($post_id);

// Display booking calendar
echo trvlr_booking_calendar($post_id);

// Display accordion (inclusions/locations/info)
echo trvlr_accordion($post_id);

// Display description content
echo trvlr_description($post_id);
echo trvlr_short_description($post_id);

// Display price
echo trvlr_advertised_price($post_id);

// Display duration with icon
echo trvlr_duration($post_id);

// Display locations
echo trvlr_locations($post_id);`
    })
  }, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('trvlr_cards() Arguments', 'trvlr'),
    content: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
        style: {
          marginBottom: '12px'
        },
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('The trvlr_cards() function accepts extensive query arguments:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
        style: {
          background: '#f6f7f7',
          padding: '12px',
          borderRadius: '4px',
          fontSize: '13px',
          whiteSpace: 'pre-wrap'
        },
        children: `// Basic arguments
$args = array(
    'posts_per_page' => 12,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
);

// Filter by specific IDs
$args = array(
    'ids' => '123,456,789', // or array(123, 456, 789)
);

// Exclude specific IDs
$args = array(
    'exclude' => '10,20,30',
);

// Filter by attraction tags
$args = array(
    'tag' => 'popular,featured',
    'tag_relation' => 'OR', // AND or OR
);

$args = array(
    'tag_id' => '5,10',
    'tag_slug' => 'water-activities',
);

// Filter by meta fields
$args = array(
    'meta_key' => 'trvlr_duration',
    'meta_value' => '2 hours',
    'meta_compare' => '=', // =, !=, >, <, >=, <=, LIKE, etc.
);

// Advanced meta query
$args = array(
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => 'trvlr_is_on_sale',
            'value' => '1',
            'compare' => '='
        ),
        array(
            'key' => 'trvlr_duration',
            'value' => '3 hours',
            'compare' => 'LIKE'
        )
    )
);

// Advanced taxonomy query
$args = array(
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'trvlr_attraction_tag',
            'field' => 'slug',
            'terms' => array('popular', 'featured')
        )
    )
);

// Full custom query override
$args = array(
    'query_args' => array(
        'post_type' => 'trvlr_attraction',
        'posts_per_page' => 6,
        'orderby' => 'meta_value_num',
        'meta_key' => 'trvlr_advertised_price_value',
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'trvlr_attraction_tag',
                'field' => 'slug',
                'terms' => 'popular'
            )
        )
    )
);

echo trvlr_cards($args);`
      })]
    })
  }, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Data Getter Functions', 'trvlr'),
    content: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
      style: {
        background: '#f6f7f7',
        padding: '12px',
        borderRadius: '4px',
        fontSize: '13px',
        whiteSpace: 'pre-wrap'
      },
      children: `// Get TRVLR ID (for booking)
get_trvlr_id($post_id);
get_trvlr_attraction_id($post_id);

// Get text content
get_trvlr_title($post_id);
get_trvlr_description($post_id);
get_trvlr_short_description($post_id);
get_trvlr_inclusions($post_id);
get_trvlr_additional_info($post_id);

// Get timing
get_trvlr_duration($post_id);
get_trvlr_start_time($post_id);
get_trvlr_end_time($post_id);

// Get pricing data
get_trvlr_pricing($post_id); // Returns array
get_trvlr_advertised_price_value($post_id);
get_trvlr_advertised_price_type($post_id);

// Get sale info
get_trvlr_is_on_sale($post_id); // Returns boolean
get_trvlr_sale_description($post_id);

// Get locations
get_trvlr_locations($post_id); // Returns array

// Get media
get_trvlr_media($post_id); // Returns array of IDs
get_post_thumbnail_id($post_id); // Featured image

// Get tags/categories
get_trvlr_attraction_tags($post_id); // Returns WP_Term array

// Get all data
get_trvlr_attraction_all_data($post_id); // Returns associative array`
    })
  }, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Helper Functions', 'trvlr'),
    content: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
      style: {
        background: '#f6f7f7',
        padding: '12px',
        borderRadius: '4px',
        fontSize: '13px',
        whiteSpace: 'pre-wrap'
      },
      children: `// Check if post is an attraction
is_trvlr_attraction($post_id);

// Get organization settings
get_trvlr_organisation_id();
get_trvlr_base_domain($org_id);

// Get primary location
get_trvlr_attraction_primary_location($post_id);

// Get lowest price
get_trvlr_attraction_lowest_price($post_id);

// Get formatted price
get_trvlr_attraction_formatted_price($post_id);`
    })
  }, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Custom Loop Examples', 'trvlr'),
    content: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        style: {
          display: 'block',
          marginBottom: '8px'
        },
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Basic Loop:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
        style: {
          background: '#f6f7f7',
          padding: '12px',
          borderRadius: '4px',
          fontSize: '13px',
          whiteSpace: 'pre-wrap',
          marginBottom: '16px'
        },
        children: `<?php
$args = array(
    'post_type' => 'trvlr_attraction',
    'posts_per_page' => 6,
    'orderby' => 'date',
    'order' => 'DESC'
);
$query = new WP_Query($args);

if ($query->have_posts()) {
    echo '<div class="trvlr-cards">';
    while ($query->have_posts()) {
        $query->the_post();
        echo trvlr_card(get_the_ID());
    }
    echo '</div>';
    wp_reset_postdata();
}
?>`
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
        style: {
          display: 'block',
          marginBottom: '8px'
        },
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Using trvlr_cards() Function:', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
        style: {
          background: '#f6f7f7',
          padding: '12px',
          borderRadius: '4px',
          fontSize: '13px',
          whiteSpace: 'pre-wrap',
          marginBottom: '16px'
        },
        children: `<?php
// Show popular attractions on sale
echo trvlr_cards(array(
    'posts_per_page' => 6,
    'tag' => 'popular',
    'meta_key' => 'trvlr_is_on_sale',
    'meta_value' => '1'
));

// Show attractions by price (lowest first)
echo trvlr_cards(array(
    'posts_per_page' => 8,
    'orderby' => 'meta_value_num',
    'meta_key' => 'trvlr_advertised_price_value',
    'order' => 'ASC'
));

// Advanced: Multiple filters
echo trvlr_cards(array(
    'query_args' => array(
        'post_type' => 'trvlr_attraction',
        'posts_per_page' => 10,
        'tax_query' => array(
            array(
                'taxonomy' => 'trvlr_attraction_tag',
                'field' => 'slug',
                'terms' => array('popular', 'featured'),
                'operator' => 'IN'
            )
        ),
        'meta_query' => array(
            array(
                'key' => 'trvlr_duration',
                'value' => '3 hours',
                'compare' => 'LIKE'
            )
        )
    )
));
?>`
      })]
    })
  }, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Custom Template Example', 'trvlr'),
    content: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("pre", {
      style: {
        background: '#f6f7f7',
        padding: '12px',
        borderRadius: '4px',
        fontSize: '13px',
        whiteSpace: 'pre-wrap'
      },
      children: `<?php
// single-trvlr_attraction.php
get_header();

if (have_posts()) {
    while (have_posts()) {
        the_post();
        $post_id = get_the_ID();
        ?>
        <article class="attraction-single">
            <h1><?php echo get_trvlr_title($post_id); ?></h1>
            
            <?php echo trvlr_gallery($post_id); ?>
            
            <div class="attraction-meta">
                <?php echo trvlr_duration($post_id); ?>
                <?php echo trvlr_advertised_price($post_id); ?>
            </div>
            
            <?php echo trvlr_description($post_id); ?>
            <?php echo trvlr_accordion($post_id); ?>
            <?php echo trvlr_booking_calendar($post_id); ?>
        </article>
        <?php
    }
}

get_footer();
?>`
    })
  }]
}];
const PluginInstructions = () => {
  const steps = getInstructionSteps();
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Panel, {
    className: "trvlr-plugin-instructions",
    children: steps.map((step, index) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
      title: step.title,
      initialOpen: false,
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          style: {
            width: '100%',
            display: 'flex',
            flexDirection: 'column',
            gap: '12px'
          },
          children: [step.content(), step.dropdowns && step.dropdowns.length > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Panel, {
            children: step.dropdowns.map((dropdown, dropdownIndex) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
              title: dropdown.title,
              initialOpen: false,
              children: dropdown.content
            }, dropdownIndex))
          })]
        })
      })
    }, index))
  });
};

/***/ }),

/***/ "./admin/src/components/system-status.tsx":
/*!************************************************!*\
  !*** ./admin/src/components/system-status.tsx ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SystemStatus: () => (/* binding */ SystemStatus)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../context/TrvlrContext */ "./admin/src/context/TrvlrContext.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);






const SystemStatus = () => {
  const {
    systemStatus,
    createPaymentPage
  } = (0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__.useTrvlr)();
  const [creating, setCreating] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const handleCreatePaymentPage = async () => {
    setCreating(true);
    const result = await createPaymentPage();
    setCreating(false);
    if (result.success) {
      alert((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Payment page created successfully!', 'trvlr'));
    } else {
      alert((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Error creating payment page. Please try again.', 'trvlr'));
    }
  };
  const statusItemStyle = {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    background: '#fff',
    padding: '10px 16px',
    border: '1px solid #ddd',
    borderRadius: '4px'
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
    className: "trvlr-system-status",
    style: {
      display: 'flex',
      flexDirection: 'column',
      gap: '10px'
    },
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
      style: statusItemStyle,
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
          className: "dashicons dashicons-admin-page",
          style: {
            marginRight: '8px'
          }
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Payment Confirmation Page', 'trvlr')
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
        children: systemStatus.payment_page?.exists ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
          style: {
            display: 'flex',
            alignItems: 'center',
            gap: '10px'
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
            variant: "primary",
            size: "small",
            onClick: () => window.open(systemStatus.payment_page.url, '_blank'),
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('View Page', 'trvlr')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("span", {
            className: "trvlr-status-badge trvlr-status-success",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
              className: "dashicons dashicons-yes-alt"
            }), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Active', 'trvlr')]
          })]
        }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
          style: {
            display: 'flex',
            alignItems: 'center',
            gap: '10px'
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
            variant: "primary",
            size: "small",
            onClick: handleCreatePaymentPage,
            isBusy: creating,
            disabled: creating,
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Create Page', 'trvlr')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("span", {
            className: "trvlr-status-badge trvlr-status-error",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
              className: "dashicons dashicons-warning"
            }), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Not Found', 'trvlr')]
          })]
        })
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
      style: statusItemStyle,
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
          className: "dashicons dashicons-cloud",
          style: {
            marginRight: '8px'
          }
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('API Connection', 'trvlr')
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("span", {
          className: "trvlr-status-badge trvlr-status-info",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
            className: "dashicons dashicons-info"
          }), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Not Tested', 'trvlr')]
        })
      })]
    })]
  });
};

/***/ }),

/***/ "./admin/src/components/theme-field.tsx":
/*!**********************************************!*\
  !*** ./admin/src/components/theme-field.tsx ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ThemeField: () => (/* binding */ ThemeField)
/* harmony export */ });
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


const ThemeField = ({
  field,
  value,
  onChange
}) => {
  switch (field.type) {
    case 'color':
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
        style: {
          marginBottom: '20px'
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("strong", {
          children: field.label
        }), field.description && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("p", {
          style: {
            fontSize: '12px',
            color: '#666',
            margin: '4px 0 8px'
          },
          children: field.description
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.ColorPicker, {
          color: value,
          onChangeComplete: color => {
            // Handle different color picker outputs
            const colorValue = color.hex || `rgba(${color.rgb.r},${color.rgb.g},${color.rgb.b},${color.rgb.a})`;
            onChange(colorValue);
          }
        })]
      });
    case 'range':
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.RangeControl, {
        label: field.label,
        value: value,
        onChange: val => onChange(val !== null && val !== void 0 ? val : field.default),
        min: field.min,
        max: field.max,
        step: field.step,
        help: field.description
      });
    case 'text':
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.TextControl, {
        label: field.label,
        value: value,
        onChange: onChange,
        help: field.description
      });
    default:
      return null;
  }
};

/***/ }),

/***/ "./admin/src/components/theme-preview.tsx":
/*!************************************************!*\
  !*** ./admin/src/components/theme-preview.tsx ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AttractionCardPreview: () => (/* binding */ AttractionCardPreview)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


const AttractionCardPreview = () => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
    className: "trvlr-card trvlr-card--attraction",
    style: {
      width: '100%'
    },
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
      className: "trvlr-card__image-wrap",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("img", {
        width: "300",
        height: "225",
        src: "https://picsum.photos/id/866/300/225",
        className: "trvlr-card__image wp-post-image",
        alt: "Preview attraction"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
        className: "trvlr-popular-badge",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("svg", {
          className: "trvlr-icon trvlr-popular-badge__icon",
          viewBox: "0 0 18 18",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
            d: "M9.00002 0.5C9.38064 0.5 9.72803 0.716313 9.8965 1.05762L11.9805 5.28027L16.6446 5.96289C17.0211 6.01793 17.3338 6.28252 17.4512 6.64453C17.5684 7.00643 17.4698 7.40351 17.1973 7.66895L13.8242 10.9531L14.6211 15.5957C14.6855 15.9709 14.5307 16.3505 14.2227 16.5742C13.9148 16.7978 13.5067 16.8273 13.1699 16.6504L9.00002 14.457L4.8301 16.6504C4.49331 16.8273 4.0852 16.7978 3.77736 16.5742C3.46939 16.3505 3.31458 15.9709 3.37893 15.5957L4.17482 10.9531L0.802754 7.66895C0.530236 7.40351 0.431671 7.00643 0.548848 6.64453C0.666226 6.28252 0.978929 6.01793 1.35549 5.96289L6.01857 5.28027L8.10354 1.05762L8.17482 0.935547C8.35943 0.665559 8.66699 0.5 9.00002 0.5ZM7.57912 6.6377C7.43357 6.93249 7.15248 7.13702 6.82717 7.18457L3.64748 7.64844L5.94729 9.88867C6.18316 10.1184 6.29103 10.4499 6.23537 10.7744L5.6924 13.9365L8.5342 12.4424C8.82558 12.2891 9.17446 12.2891 9.46584 12.4424L12.3067 13.9365L11.7647 10.7744C11.709 10.4499 11.8169 10.1184 12.0528 9.88867L14.3516 7.64844L11.1729 7.18457C10.8476 7.13702 10.5665 6.93249 10.4209 6.6377L9.00002 3.75781L7.57912 6.6377Z"
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
          className: "trvlr-popular-badge__text",
          children: "Popular"
        })]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
      className: "trvlr-card__content",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("h3", {
        className: "trvlr-title trvlr-card__title",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("a", {
          href: "#",
          children: "Gordon River 3:15pm Afternoon Cruise \u2013 Upper Deck Window Seating    "
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
        className: "trvlr-card__meta",
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
          className: "trvlr-duration",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("svg", {
            className: "trvlr-duration__icon",
            viewBox: "0 0 18 18",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("g", {
              "clip-path": "url(#clip0_133_223)",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
                d: "M15.5 9C15.5 5.41015 12.5899 2.5 9 2.5C5.41015 2.5 2.5 5.41015 2.5 9C2.5 12.5899 5.41015 15.5 9 15.5C12.5899 15.5 15.5 12.5899 15.5 9ZM17.5 9C17.5 13.6944 13.6944 17.5 9 17.5C4.30558 17.5 0.5 13.6944 0.5 9C0.5 4.30558 4.30558 0.5 9 0.5C13.6944 0.5 17.5 4.30558 17.5 9Z"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
                d: "M8 4.5C8 3.94772 8.44772 3.5 9 3.5C9.55228 3.5 10 3.94772 10 4.5V8.38184L12.4473 9.60547C12.9412 9.85246 13.1415 10.4533 12.8945 10.9473C12.6475 11.4412 12.0467 11.6415 11.5527 11.3945L8.55273 9.89453C8.21395 9.72514 8 9.37877 8 9V4.5Z"
              })]
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("defs", {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("clipPath", {
                id: "clip0_133_223",
                children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("rect", {
                  width: "18",
                  height: "18"
                })
              })
            })]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
            className: "trvlr-duration__value",
            children: "5 hours 15 mins"
          })]
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
        className: "trvlr-card__footer",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
          className: "trvlr-card__price",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("div", {
            className: "trvlr-sale__badge",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
              children: "% Special Deal"
            })
          }), "           ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("div", {
            className: "trvlr-price",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
              className: "trvlr-price__value",
              children: "from $215"
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
              className: "trvlr-price__type",
              children: "per person"
            })]
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsxs)("button", {
          className: "trvlr-card__button trvlr-book-now",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("span", {
            children: "Book Now"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("svg", {
            viewBox: "0 0 21 21",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)("path", {
              d: "M9.83496 4.29285C10.2255 3.90241 10.8585 3.90236 11.249 4.29285L16.791 9.83484C16.7969 9.84072 16.8019 9.84741 16.8076 9.8534C16.8194 9.86578 16.8307 9.87851 16.8418 9.89148C16.8509 9.90206 16.8596 9.91284 16.8682 9.92371C16.879 9.93742 16.8893 9.95142 16.8994 9.9657C17.1465 10.3148 17.143 10.7848 16.8896 11.1307C16.8847 11.1375 16.8801 11.1446 16.875 11.1512C16.8612 11.1691 16.8462 11.1859 16.8311 11.203C16.8259 11.2089 16.8208 11.2148 16.8154 11.2206C16.807 11.2297 16.7999 11.2401 16.791 11.2489L11.249 16.7899C10.8585 17.1804 10.2255 17.1804 9.83496 16.7899C9.44461 16.3994 9.44449 15.7663 9.83496 15.3759L13.668 11.5419H5C4.4478 11.5419 4.00013 11.094 4 10.5419C4 9.98959 4.44772 9.54187 5 9.54187H13.6699L9.83496 5.70691C9.44444 5.31639 9.44444 4.68337 9.83496 4.29285Z"
            })
          })]
        })]
      })]
    })]
  });
};

/***/ }),

/***/ "./admin/src/context/TrvlrContext.jsx":
/*!********************************************!*\
  !*** ./admin/src/context/TrvlrContext.jsx ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TrvlrProvider: () => (/* binding */ TrvlrProvider),
/* harmony export */   generateCSSVariables: () => (/* binding */ generateCSSVariables),
/* harmony export */   getAllFieldsFromConfig: () => (/* binding */ getAllFieldsFromConfig),
/* harmony export */   getThemeDefaults: () => (/* binding */ getThemeDefaults),
/* harmony export */   mergeWithDefaults: () => (/* binding */ mergeWithDefaults),
/* harmony export */   processConfigForRendering: () => (/* binding */ processConfigForRendering),
/* harmony export */   useTrvlr: () => (/* binding */ useTrvlr)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__);



const TrvlrContext = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createContext)();

/**
 * Get all fields from theme config (flattened)
 */
const getAllFieldsFromConfig = config => {
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
const getThemeDefaults = config => {
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
  const filtered = Object.fromEntries(Object.entries(userSettings || {}).filter(([_, value]) => value !== undefined));
  return {
    ...defaults,
    ...filtered
  };
};

/**
 * Process theme config fields for rendering
 * Handles cols-X groupings
 */
const processConfigForRendering = config => {
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
const TrvlrProvider = ({
  children
}) => {
  // Load initial data from window object (localized by PHP)
  const initialData = window.trvlrInitialData || {
    settings: {},
    sync: {},
    system: {},
    themeConfig: {},
    nonce: ''
  };

  // Get theme config from localized data
  const themeConfig = initialData.themeConfig || {};

  // Process config for rendering (memoized)
  const processedThemeConfig = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => processConfigForRendering(themeConfig), [themeConfig]);

  // State management (merge with defaults to ensure all fields exist)
  const [themeSettings, setThemeSettings] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(() => mergeWithDefaults(initialData.settings?.theme || {}, themeConfig));
  const [connectionSettings, setConnectionSettings] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialData.settings?.connection || {});
  const [notificationSettings, setNotificationSettings] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialData.settings?.notifications || {});
  const [syncStats, setSyncStats] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialData.sync?.stats || {});
  const [scheduleSettings, setScheduleSettings] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialData.sync?.schedule || {});
  const [customEditsCount, setCustomEditsCount] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialData.sync?.custom_edits_count || 0);
  const [systemStatus, setSystemStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialData.system || {});
  const [saving, setSaving] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [refreshing, setRefreshing] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);

  // Settings Methods
  const saveThemeSettings = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async settings => {
    setSaving(true);
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: '/trvlr/v1/settings/theme',
        method: 'POST',
        data: settings
      });
      setThemeSettings(settings);
      return {
        success: true,
        data: response
      };
    } catch (error) {
      return {
        success: false,
        error
      };
    } finally {
      setSaving(false);
    }
  }, []);
  const saveConnectionSettings = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async settings => {
    setSaving(true);
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: '/trvlr/v1/settings/connection',
        method: 'POST',
        data: settings
      });
      setConnectionSettings(settings);
      return {
        success: true,
        data: response
      };
    } catch (error) {
      return {
        success: false,
        error
      };
    } finally {
      setSaving(false);
    }
  }, []);
  const saveNotificationSettings = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async settings => {
    setSaving(true);
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: '/trvlr/v1/settings/notifications',
        method: 'POST',
        data: settings
      });
      setNotificationSettings(settings);
      return {
        success: true,
        data: response
      };
    } catch (error) {
      return {
        success: false,
        error
      };
    } finally {
      setSaving(false);
    }
  }, []);

  // Sync Methods
  const refreshSyncStats = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    setRefreshing(true);
    try {
      const stats = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: '/trvlr/v1/sync/stats'
      });
      setSyncStats(stats);
      return {
        success: true,
        data: stats
      };
    } catch (error) {
      return {
        success: false,
        error
      };
    } finally {
      setRefreshing(false);
    }
  }, []);
  const triggerManualSync = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: '/trvlr/v1/sync/manual',
        method: 'POST'
      });
      // Refresh stats after sync
      await refreshSyncStats();
      return {
        success: true,
        data: response
      };
    } catch (error) {
      return {
        success: false,
        error
      };
    }
  }, [refreshSyncStats]);
  const saveScheduleSettings = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async settings => {
    setSaving(true);
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: '/trvlr/v1/sync/schedule',
        method: 'POST',
        data: settings
      });
      setScheduleSettings(response);
      return {
        success: true,
        data: response
      };
    } catch (error) {
      return {
        success: false,
        error
      };
    } finally {
      setSaving(false);
    }
  }, []);
  const deleteData = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async (includeMedia = false) => {
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: `/trvlr/v1/sync/delete?include_media=${includeMedia}`,
        method: 'POST'
      });
      // Refresh stats after deletion
      await refreshSyncStats();
      return {
        success: true,
        data: response
      };
    } catch (error) {
      return {
        success: false,
        error
      };
    }
  }, [refreshSyncStats]);

  // System Methods
  const refreshSystemStatus = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    setRefreshing(true);
    try {
      const status = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: '/trvlr/v1/setup/status'
      });
      setSystemStatus(status);
      return {
        success: true,
        data: status
      };
    } catch (error) {
      return {
        success: false,
        error
      };
    } finally {
      setRefreshing(false);
    }
  }, []);
  const createPaymentPage = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: '/trvlr/v1/setup/payment-page',
        method: 'POST'
      });
      // Refresh system status after creation
      await refreshSystemStatus();
      return {
        success: true,
        data: response
      };
    } catch (error) {
      return {
        success: false,
        error
      };
    }
  }, [refreshSystemStatus]);
  const testApiConnection = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(async () => {
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: '/trvlr/v1/setup/test-connection',
        method: 'POST'
      });
      return {
        success: true,
        data: response
      };
    } catch (error) {
      return {
        success: false,
        error
      };
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
    nonce: initialData.nonce
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(TrvlrContext.Provider, {
    value: value,
    children: children
  });
};
const useTrvlr = () => {
  const context = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useContext)(TrvlrContext);
  if (!context) {
    throw new Error('useTrvlr must be used within TrvlrProvider');
  }
  return context;
};

/**
 * Generate CSS variables string from settings and config
 */
const generateCSSVariables = (settings, config) => {
  let css = ':root {\n';
  const allFields = getAllFieldsFromConfig(config);
  allFields.forEach(field => {
    if (field.cssVar) {
      var _settings$field$key;
      const value = (_settings$field$key = settings[field.key]) !== null && _settings$field$key !== void 0 ? _settings$field$key : field.default;
      const unit = field.unit || '';
      css += `  ${field.cssVar}: ${value}${unit};\n`;
    }
  });
  css += '}';
  return css;
};

// Export helper functions for use outside context


/***/ }),

/***/ "./admin/src/settings-forms/connection-settings-form.jsx":
/*!***************************************************************!*\
  !*** ./admin/src/settings-forms/connection-settings-form.jsx ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ConnectionSettingsForm: () => (/* binding */ ConnectionSettingsForm)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../context/TrvlrContext */ "./admin/src/context/TrvlrContext.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





const ConnectionSettingsForm = () => {
  const {
    connectionSettings,
    saveConnectionSettings,
    saving
  } = (0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__.useTrvlr)();
  const [saveStatus, setSaveStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [organisationId, setOrganisationId] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(connectionSettings.organisation_id || '');
  const handleSave = async () => {
    setSaveStatus(null);
    const result = await saveConnectionSettings({
      organisation_id: organisationId
    });
    if (result.success) {
      setSaveStatus('success');
      setTimeout(() => setSaveStatus(null), 3000);
    } else {
      console.error('Error saving connection settings:', result.error);
      setSaveStatus('error');
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
    id: "trvlr-connection-settings-form",
    className: "trvlr-settings-form",
    children: [saveStatus === 'success' && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: "success",
      isDismissible: false,
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Settings saved successfully!', 'trvlr')
    }), saveStatus === 'error' && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: "error",
      isDismissible: false,
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Error saving settings. Please try again.', 'trvlr')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Organisation ID', 'trvlr'),
      value: organisationId,
      onChange: setOrganisationId,
      help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Your Organisation ID from TRVLR AI.', 'trvlr')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
      variant: "primary",
      onClick: handleSave,
      isBusy: saving,
      disabled: saving,
      children: saving ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Saving...', 'trvlr') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Save Settings', 'trvlr')
    })]
  });
};

/***/ }),

/***/ "./admin/src/settings-forms/custom-edits-form.tsx":
/*!********************************************************!*\
  !*** ./admin/src/settings-forms/custom-edits-form.tsx ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CustomEditsForm: () => (/* binding */ CustomEditsForm)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);






const CustomEditsForm = () => {
  const [customEdits, setCustomEdits] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [loading, setLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  const [forceSyncSettings, setForceSyncSettings] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)({});
  const [saving, setSaving] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [message, setMessage] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    loadCustomEdits();
  }, []);
  const loadCustomEdits = async () => {
    try {
      const edits = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default()({
        path: '/trvlr/v1/sync/custom-edits'
      });
      setCustomEdits(edits);
      const initial = {};
      edits.forEach(edit => {
        initial[edit.id] = edit.force_sync_fields || [];
      });
      setForceSyncSettings(initial);
    } catch (error) {
      console.error('Error loading custom edits:', error);
      if (error?.data?.status === 403 || error?.code === 'rest_cookie_invalid_nonce') {
        setMessage({
          type: 'error',
          text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Authentication failed. Please refresh the page.', 'trvlr')
        });
      }
    } finally {
      setLoading(false);
    }
  };
  const toggleForceSync = (postId, fieldName) => {
    setForceSyncSettings(prev => {
      const current = prev[postId] || [];
      const updated = current.includes(fieldName) ? current.filter(f => f !== fieldName) : [...current, fieldName];
      return {
        ...prev,
        [postId]: updated
      };
    });
  };
  const toggleSelectAll = (postId, allFields) => {
    setForceSyncSettings(prev => {
      const current = prev[postId] || [];
      const allSelected = allFields.every(f => current.includes(f));
      return {
        ...prev,
        [postId]: allSelected ? [] : allFields
      };
    });
  };
  const handleSave = async () => {
    setSaving(true);
    setMessage(null);
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default()({
        path: '/trvlr/v1/sync/force-sync',
        method: 'POST',
        data: {
          force_sync_fields: forceSyncSettings
        }
      });
      setMessage({
        type: 'success',
        text: response.message
      });
    } catch (error) {
      setMessage({
        type: 'error',
        text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Failed to save force sync settings.', 'trvlr')
      });
    }
    setSaving(false);
  };
  const handleClear = async () => {
    if (!confirm((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Clear all force sync settings?', 'trvlr'))) return;
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default()({
        path: '/trvlr/v1/sync/clear-force-sync',
        method: 'POST'
      });
      const cleared = {};
      customEdits.forEach(edit => {
        cleared[edit.id] = [];
      });
      setForceSyncSettings(cleared);
      setMessage({
        type: 'success',
        text: response.message
      });
    } catch (error) {
      setMessage({
        type: 'error',
        text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Failed to clear force sync settings.', 'trvlr')
      });
    }
  };
  if (loading) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Spinner, {});
  }
  if (customEdits.length === 0) {
    return null;
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
    className: "trvlr-settings-form",
    children: [message && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: message.type,
      onRemove: () => setMessage(null),
      isDismissible: true,
      children: message.text
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
      style: {
        overflowX: 'auto'
      },
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("table", {
        className: "wp-list-table widefat fixed striped",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("thead", {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("tr", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("th", {
              style: {
                width: '35%'
              },
              children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Attraction', 'trvlr')
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("th", {
              style: {
                width: '25%'
              },
              children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Edited Fields', 'trvlr')
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("th", {
              style: {
                width: '15%'
              },
              children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Last Modified', 'trvlr')
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("th", {
              style: {
                width: '25%'
              },
              children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Force Sync Fields', 'trvlr')
            })]
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("tbody", {
          children: customEdits.map(edit => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("tr", {
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("td", {
                children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
                  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("a", {
                    href: edit.edit_url,
                    target: "_blank",
                    rel: "noopener noreferrer",
                    children: edit.title
                  })
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("td", {
                children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
                  style: {
                    background: '#f0f0f1',
                    padding: '4px 8px',
                    borderRadius: '3px',
                    fontSize: '12px'
                  },
                  children: edit.edited_fields_labels?.join(', ')
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("td", {
                children: edit.modified
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("td", {
                children: [forceSyncSettings[edit.id]?.length > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
                  style: {
                    marginBottom: '8px',
                    fontSize: '12px'
                  },
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
                    className: "dashicons dashicons-yes-alt",
                    style: {
                      color: '#00a32a'
                    }
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("strong", {
                    children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Will overwrite:', 'trvlr')
                  }), " ", forceSyncSettings[edit.id].map(f => edit.edited_fields_labels[edit.edited_fields.indexOf(f)]).join(', ')]
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
                  variant: "secondary",
                  size: "small",
                  onClick: () => {
                    const row = document.getElementById(`fields-${edit.id}`);
                    if (row) {
                      row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
                    }
                  },
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
                    className: "dashicons dashicons-arrow-down-alt2",
                    style: {
                      fontSize: '16px'
                    }
                  }), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Select Fields', 'trvlr')]
                })]
              })]
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("tr", {
              id: `fields-${edit.id}`,
              style: {
                display: 'none'
              },
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("td", {
                colSpan: 4,
                style: {
                  background: '#f9f9f9',
                  padding: '15px'
                },
                children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("label", {
                    style: {
                      fontWeight: 'bold',
                      marginRight: '20px',
                      display: 'block',
                      marginBottom: '10px'
                    },
                    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("input", {
                      type: "checkbox",
                      checked: edit.edited_fields.every(f => forceSyncSettings[edit.id]?.includes(f)),
                      onChange: () => toggleSelectAll(edit.id, edit.edited_fields),
                      style: {
                        marginRight: '5px'
                      }
                    }), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Select All Fields', 'trvlr')]
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
                    style: {
                      display: 'grid',
                      gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))',
                      gap: '10px'
                    },
                    children: edit.edited_fields.map((field, index) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("label", {
                      style: {
                        display: 'flex',
                        alignItems: 'center'
                      },
                      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("input", {
                        type: "checkbox",
                        checked: forceSyncSettings[edit.id]?.includes(field) || false,
                        onChange: () => toggleForceSync(edit.id, field),
                        style: {
                          marginRight: '5px'
                        }
                      }), edit.edited_fields_labels[index]]
                    }, field))
                  })]
                })
              })
            })]
          }, edit.id))
        })]
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
      style: {
        display: 'flex',
        gap: '10px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
        variant: "primary",
        onClick: handleSave,
        isBusy: saving,
        disabled: saving,
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Save Force Sync Settings', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
        variant: "secondary",
        onClick: handleClear,
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Clear All Force Sync Settings', 'trvlr')
      })]
    })]
  });
};

/***/ }),

/***/ "./admin/src/settings-forms/danger-zone-form.tsx":
/*!*******************************************************!*\
  !*** ./admin/src/settings-forms/danger-zone-form.tsx ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DangerZoneForm: () => (/* binding */ DangerZoneForm)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../context/TrvlrContext */ "./admin/src/context/TrvlrContext.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);






const DangerZoneForm = () => {
  const {
    deleteData
  } = (0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__.useTrvlr)();
  const [message, setMessage] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const handleDelete = async includeMedia => {
    const confirmText = includeMedia ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Delete ALL data including images? This cannot be undone!', 'trvlr') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Delete all attraction posts? (Images will be kept)', 'trvlr');
    if (!confirm(confirmText)) return;
    setMessage(null);
    const result = await deleteData(includeMedia);
    if (result.success) {
      setMessage({
        type: 'success',
        text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Data deleted successfully.', 'trvlr')
      });
    } else {
      setMessage({
        type: 'error',
        text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Failed to delete data.', 'trvlr')
      });
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
    className: "trvlr-settings-form",
    children: [message && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: message.type,
      onRemove: () => setMessage(null),
      isDismissible: true,
      children: message.text
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
      style: {
        display: 'flex',
        gap: '10px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
        variant: "secondary",
        isDestructive: true,
        onClick: () => handleDelete(true),
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Delete EVERYTHING (Inc. Images)', 'trvlr')
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
        variant: "secondary",
        onClick: () => handleDelete(false),
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Delete Posts Only (Keep Images)', 'trvlr')
      })]
    })]
  });
};

/***/ }),

/***/ "./admin/src/settings-forms/email-settings-form.jsx":
/*!**********************************************************!*\
  !*** ./admin/src/settings-forms/email-settings-form.jsx ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EmailSettingsForm: () => (/* binding */ EmailSettingsForm)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../context/TrvlrContext */ "./admin/src/context/TrvlrContext.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





const EmailSettingsForm = () => {
  var _notificationSettings, _notificationSettings2, _notificationSettings3;
  const {
    notificationSettings,
    saveNotificationSettings,
    saving
  } = (0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__.useTrvlr)();
  const [saveStatus, setSaveStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [notificationEmail, setNotificationEmail] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(notificationSettings.email || '');
  const [notifyErrors, setNotifyErrors] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)((_notificationSettings = notificationSettings.notify_errors) !== null && _notificationSettings !== void 0 ? _notificationSettings : true);
  const [notifyComplete, setNotifyComplete] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)((_notificationSettings2 = notificationSettings.notify_complete) !== null && _notificationSettings2 !== void 0 ? _notificationSettings2 : false);
  const [notifyWeekly, setNotifyWeekly] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)((_notificationSettings3 = notificationSettings.notify_weekly) !== null && _notificationSettings3 !== void 0 ? _notificationSettings3 : false);
  const handleSave = async () => {
    setSaveStatus(null);
    const result = await saveNotificationSettings({
      email: notificationEmail,
      notify_errors: notifyErrors,
      notify_complete: notifyComplete,
      notify_weekly: notifyWeekly
    });
    if (result.success) {
      console.log('Save response:', result.data);
      setSaveStatus('success');
      setTimeout(() => setSaveStatus(null), 3000);
    } else {
      console.error('Error saving email settings:', result.error);
      setSaveStatus('error');
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
    id: "trvlr-email-settings-form",
    className: "trvlr-settings-form",
    children: [saveStatus === 'success' && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: "success",
      isDismissible: false,
      className: "w-full",
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Settings saved successfully!', 'trvlr')
    }), saveStatus === 'error' && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: "error",
      isDismissible: false,
      className: "w-full",
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Error saving email settings. Please try again.', 'trvlr')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Notification Email', 'trvlr'),
      value: notificationEmail,
      onChange: setNotificationEmail,
      help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Email address for sync notifications.', 'trvlr'),
      type: "email"
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
      className: "trvlr-settings-checkbox-group",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
        checked: notifyErrors,
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Notify on sync errors', 'trvlr'),
        onChange: value => setNotifyErrors(value)
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
        checked: notifyComplete,
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Notify on sync completion', 'trvlr'),
        onChange: value => setNotifyComplete(value)
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
        checked: notifyWeekly,
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Send weekly summary', 'trvlr'),
        onChange: value => setNotifyWeekly(value)
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
      variant: "primary",
      onClick: handleSave,
      isBusy: saving,
      disabled: saving,
      children: saving ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Saving...', 'trvlr') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Save Settings', 'trvlr')
    })]
  });
};

/***/ }),

/***/ "./admin/src/settings-forms/manual-sync-form.tsx":
/*!*******************************************************!*\
  !*** ./admin/src/settings-forms/manual-sync-form.tsx ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ManualSyncForm: () => (/* binding */ ManualSyncForm)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _context_TrvlrContext__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../context/TrvlrContext */ "./admin/src/context/TrvlrContext.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);






const ManualSyncForm = () => {
  const {
    refreshSyncStats
  } = (0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_4__.useTrvlr)();
  const [syncing, setSyncing] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [message, setMessage] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [progress, setProgress] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const pollingInterval = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const stopPolling = () => {
    if (pollingInterval.current) {
      clearInterval(pollingInterval.current);
      pollingInterval.current = null;
    }
  };
  const pollProgress = async () => {
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default()({
        path: '/trvlr/v1/sync/progress'
      });
      if (response.in_progress && response.progress) {
        setProgress(response.progress);
      } else if (response.status === 'stale') {
        stopPolling();
        setSyncing(false);
        setProgress(null);
        setMessage({
          type: 'error',
          text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Sync appears to have stalled. Please try again.', 'trvlr')
        });
      } else {
        stopPolling();
        setSyncing(false);
        setProgress(null);
        if (response.results) {
          const r = response.results;
          const parts = [];
          if (r.created > 0) parts.push(`${r.created} created`);
          if (r.updated > 0) parts.push(`${r.updated} updated`);
          if (r.skipped > 0) parts.push(`${r.skipped} skipped`);
          if (r.errors > 0) parts.push(`${r.errors} errors`);
          setMessage({
            type: r.errors > 0 ? 'error' : 'success',
            text: parts.length > 0 ? `Sync completed: ${parts.join(', ')}.` : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Sync completed successfully!', 'trvlr')
          });
        } else {
          setMessage({
            type: 'success',
            text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Sync completed successfully!', 'trvlr')
          });
        }
        await refreshSyncStats();
      }
    } catch (error) {
      console.error('Error polling sync progress:', error);
    }
  };
  const startPolling = () => {
    if (pollingInterval.current) return;
    pollingInterval.current = window.setInterval(pollProgress, 2000);
  };
  const handleManualSync = async () => {
    setSyncing(true);
    setMessage(null);
    setProgress(null);
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default()({
        path: '/trvlr/v1/sync/manual',
        method: 'POST'
      });
      if (response.total) {
        setProgress({
          processed: 0,
          total: response.total,
          percentage: 0,
          message: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Starting sync...', 'trvlr')
        });
      }
      startPolling();
    } catch (error) {
      setSyncing(false);
      const errorMessage = error?.message || error?.data?.message || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Sync failed. Please check logs.', 'trvlr');
      setMessage({
        type: 'error',
        text: errorMessage
      });
      console.error('Sync error:', error);
    }
  };
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const checkExistingSync = async () => {
      try {
        const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default()({
          path: '/trvlr/v1/sync/progress'
        });
        if (response.in_progress && response.progress) {
          setSyncing(true);
          setProgress(response.progress);
          startPolling();
        }
      } catch (e) {}
    };
    checkExistingSync();
    return () => stopPolling();
  }, []);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
    className: "trvlr-settings-form",
    children: [message && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: message.type,
      onRemove: () => setMessage(null),
      isDismissible: true,
      children: message.text
    }), syncing && progress && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
      style: {
        background: '#f0f0f1',
        border: '1px solid #c3c4c7',
        borderRadius: '4px',
        padding: '16px',
        marginBottom: '16px'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        style: {
          marginBottom: '12px',
          fontWeight: 600
        },
        children: [progress.percentage, "% Complete"]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("div", {
        style: {
          background: '#fff',
          height: '24px',
          borderRadius: '4px',
          overflow: 'hidden',
          position: 'relative',
          marginBottom: '8px'
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("div", {
          style: {
            background: '#2271b1',
            height: '100%',
            width: `${progress.percentage}%`,
            transition: 'width 0.3s ease'
          }
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        style: {
          fontSize: '13px',
          color: '#50575e'
        },
        children: [progress.processed, " of ", progress.total, " attractions synced"]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
      variant: "primary",
      onClick: handleManualSync,
      isBusy: syncing,
      disabled: syncing,
      children: syncing ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Syncing...', 'trvlr') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Sync Now', 'trvlr')
    })]
  });
};

/***/ }),

/***/ "./admin/src/settings-forms/schedule-settings-form.tsx":
/*!*************************************************************!*\
  !*** ./admin/src/settings-forms/schedule-settings-form.tsx ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ScheduleSettingsForm: () => (/* binding */ ScheduleSettingsForm)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../context/TrvlrContext */ "./admin/src/context/TrvlrContext.jsx");
/* harmony import */ var _components_page_heading__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../components/page-heading */ "./admin/src/components/page-heading.tsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);







const ScheduleSettingsForm = () => {
  const {
    scheduleSettings,
    saveScheduleSettings
  } = (0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__.useTrvlr)();
  const [scheduleEnabled, setScheduleEnabled] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(scheduleSettings?.enabled || false);
  const [scheduleFrequency, setScheduleFrequency] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(scheduleSettings?.frequency || 'daily');
  const [nextSync, setNextSync] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(scheduleSettings?.next_sync || null);
  const [saving, setSaving] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [message, setMessage] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    setScheduleEnabled(scheduleSettings?.enabled || false);
    setScheduleFrequency(scheduleSettings?.frequency || 'daily');
    setNextSync(scheduleSettings?.next_sync || null);
  }, [scheduleSettings]);
  const handleSave = async () => {
    setSaving(true);
    setMessage(null);
    const result = await saveScheduleSettings({
      enabled: scheduleEnabled,
      frequency: scheduleFrequency
    });
    if (result.success) {
      setNextSync(result.data?.next_sync || null);
      setMessage({
        type: 'success',
        text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Schedule settings saved!', 'trvlr')
      });
    } else {
      setMessage({
        type: 'error',
        text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Failed to save schedule settings.', 'trvlr')
      });
    }
    setSaving(false);
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
    className: "trvlr-settings-form",
    children: [message && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: message.type,
      onRemove: () => setMessage(null),
      isDismissible: true,
      children: message.text
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
      style: {
        display: 'grid',
        gridTemplateColumns: '1fr 1fr',
        gap: '40px',
        justifyItems: 'start',
        width: '100%'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        style: {
          display: 'grid',
          gap: '10px',
          justifyItems: 'start'
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_4__.PageHeading, {
          level: 3,
          text: 'Auto-Sync: Schedule syncs to happen automatically'
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          style: {
            marginBottom: '0px'
          },
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Enable automatic synchronization', 'trvlr'),
          checked: scheduleEnabled,
          onChange: setScheduleEnabled
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
          variant: "primary",
          onClick: handleSave,
          isBusy: saving,
          disabled: saving,
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Save Schedule Settings', 'trvlr')
        })]
      }), scheduleEnabled && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        style: {
          display: 'grid',
          gap: '10px'
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_4__.PageHeading, {
          level: 3,
          text: 'Select auto-sync frequency'
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Sync Frequency', 'trvlr'),
          value: scheduleFrequency,
          onChange: setScheduleFrequency,
          disabled: !scheduleEnabled,
          options: [{
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Hourly', 'trvlr'),
            value: 'hourly'
          }, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Twice Daily', 'trvlr'),
            value: 'twicedaily'
          }, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Daily', 'trvlr'),
            value: 'daily'
          }, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Weekly', 'trvlr'),
            value: 'weekly'
          }]
        }), nextSync && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
          style: {
            padding: '10px',
            background: '#f0f0f1',
            borderRadius: '4px'
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("strong", {
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Next sync scheduled for:', 'trvlr')
          }), " ", nextSync]
        })]
      })]
    })]
  });
};

/***/ }),

/***/ "./admin/src/settings-pages/connection-settings.jsx":
/*!**********************************************************!*\
  !*** ./admin/src/settings-pages/connection-settings.jsx ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ConnectionSettings: () => (/* binding */ ConnectionSettings)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_page_heading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../components/page-heading */ "./admin/src/components/page-heading.tsx");
/* harmony import */ var _components_system_status__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/system-status */ "./admin/src/components/system-status.tsx");
/* harmony import */ var _settings_forms_connection_settings_form__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../settings-forms/connection-settings-form */ "./admin/src/settings-forms/connection-settings-form.jsx");
/* harmony import */ var _settings_forms_email_settings_form__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../settings-forms/email-settings-form */ "./admin/src/settings-forms/email-settings-form.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);






const ConnectionSettings = () => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.Fragment, {
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
      className: "trvlr-settings-sidebar-wrap",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        className: "trvlr-settings-section-spacer",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_1__.PageHeading, {
            text: "Connect with TRVLR"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_settings_forms_connection_settings_form__WEBPACK_IMPORTED_MODULE_3__.ConnectionSettingsForm, {})]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_1__.PageHeading, {
            text: "Set up notification preference"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_settings_forms_email_settings_form__WEBPACK_IMPORTED_MODULE_4__.EmailSettingsForm, {})]
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_1__.PageHeading, {
          text: "System Status"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_system_status__WEBPACK_IMPORTED_MODULE_2__.SystemStatus, {})]
      })]
    })
  });
};

/***/ }),

/***/ "./admin/src/settings-pages/getting-started-settings.tsx":
/*!***************************************************************!*\
  !*** ./admin/src/settings-pages/getting-started-settings.tsx ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GettingsStartedSettings: () => (/* binding */ GettingsStartedSettings)
/* harmony export */ });
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_page_heading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../components/page-heading */ "./admin/src/components/page-heading.tsx");
/* harmony import */ var _components_plugin_instructions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/plugin-instructions */ "./admin/src/components/plugin-instructions.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__);




const GettingsStartedSettings = () => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_1__.PageHeading, {
      text: "Getting Started with TRVLR Wordpress Manager"
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      className: "trvlr-settings-section-spacer",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_components_plugin_instructions__WEBPACK_IMPORTED_MODULE_2__.PluginInstructions, {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h2", {
          style: {
            marginBottom: '5px'
          },
          children: "Got Questions?"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("p", {
          style: {
            fontSize: '18px',
            margin: '0'
          },
          children: ["Write to ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("a", {
            href: "mailto:team@trvlr.ai",
            target: "_blank",
            children: "team@trvlr.ai"
          }), " for support."]
        })]
      })]
    })]
  });
};

/***/ }),

/***/ "./admin/src/settings-pages/logs-settings.tsx":
/*!****************************************************!*\
  !*** ./admin/src/settings-pages/logs-settings.tsx ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   LogsSettings: () => (/* binding */ LogsSettings)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_page_heading__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../components/page-heading */ "./admin/src/components/page-heading.tsx");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);







const LogsSettings = () => {
  const [sessions, setSessions] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [expandedSessions, setExpandedSessions] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(new Set());
  const [selectedLog, setSelectedLog] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [loading, setLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  const [saving, setSaving] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [message, setMessage] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const loadLogs = async () => {
    setLoading(true);
    try {
      console.log('TRVLR Logs: Fetching logs...');
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_4___default()({
        path: '/trvlr/v1/logs?grouped=true&limit=50'
      });
      console.log('TRVLR Logs: Loaded', response.length, 'sessions');
      setSessions(response);
    } catch (error) {
      console.error('TRVLR Logs: Error loading logs:', error);

      // Check if it's a 403 authentication error
      if (error?.data?.status === 403 || error?.code === 'rest_cookie_invalid_nonce') {
        setMessage({
          type: 'error',
          text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Authentication failed. Please refresh the page.', 'trvlr')
        });
      } else {
        setMessage({
          type: 'error',
          text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Failed to load logs.', 'trvlr')
        });
      }
    } finally {
      setLoading(false);
    }
  };
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    console.log('TRVLR Logs: Component mounted, checking auth...');
    console.log('TRVLR Initial Data:', window.trvlrInitialData);
    console.log('WP API Fetch available:', typeof window.wp?.apiFetch);

    // Only load if we have proper authentication
    if (window.trvlrInitialData?.restNonce) {
      loadLogs();
    } else {
      console.error('TRVLR Logs: No REST nonce available!');
      setMessage({
        type: 'error',
        text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Authentication not configured. Please refresh the page.', 'trvlr')
      });
      setLoading(false);
    }
  }, []);
  const toggleSession = sessionId => {
    const newExpanded = new Set(expandedSessions);
    if (newExpanded.has(sessionId)) {
      newExpanded.delete(sessionId);
    } else {
      newExpanded.add(sessionId);
    }
    setExpandedSessions(newExpanded);
  };
  const handleClearOldLogs = async () => {
    if (!window.confirm((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Delete logs older than 30 days?', 'trvlr'))) {
      return;
    }
    setSaving(true);
    try {
      const result = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_4___default()({
        path: '/trvlr/v1/logs/clear-old',
        method: 'POST'
      });
      setMessage({
        type: 'success',
        text: result.message
      });
      loadLogs();
    } catch (error) {
      setMessage({
        type: 'error',
        text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Failed to clear old logs.', 'trvlr')
      });
    } finally {
      setSaving(false);
    }
  };
  const handleClearAllLogs = async () => {
    if (!window.confirm((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Delete ALL logs? This cannot be undone.', 'trvlr'))) {
      return;
    }
    setSaving(true);
    try {
      const result = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_4___default()({
        path: '/trvlr/v1/logs/clear-all',
        method: 'POST'
      });
      setMessage({
        type: 'success',
        text: result.message
      });
      loadLogs();
    } catch (error) {
      setMessage({
        type: 'error',
        text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Failed to clear logs.', 'trvlr')
      });
    } finally {
      setSaving(false);
    }
  };
  const handleExportLogs = () => {
    window.open('/wp-json/trvlr/v1/logs/export', '_blank');
  };
  const getStatusBadge = status => {
    const badges = {
      completed: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Completed', 'trvlr'),
        class: 'trvlr-badge-success'
      },
      error: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Error', 'trvlr'),
        class: 'trvlr-badge-error'
      },
      standalone: {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('System', 'trvlr'),
        class: 'trvlr-badge-info'
      }
    };
    const badge = badges[status] || badges.completed;
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("span", {
      className: `trvlr-badge ${badge.class}`,
      children: badge.label
    });
  };
  const getLogTypeBadge = type => {
    const typeClasses = {
      sync_start: 'trvlr-log-badge-info',
      sync_complete: 'trvlr-log-badge-success',
      attraction_created: 'trvlr-log-badge-created',
      attraction_updated: 'trvlr-log-badge-updated',
      attraction_skipped: 'trvlr-log-badge-skipped',
      no_updates: 'trvlr-log-badge-no-updates',
      error: 'trvlr-log-badge-error',
      system: 'trvlr-log-badge-system'
    };
    const className = typeClasses[type] || 'trvlr-log-badge-default';
    const label = type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("span", {
      className: `trvlr-log-badge ${className}`,
      children: label
    });
  };
  const formatDate = dateString => {
    if (!dateString) return '—';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };
  const getSummaryText = summary => {
    const parts = [];
    if (summary.created > 0) parts.push(`${summary.created} created`);
    if (summary.updated > 0) parts.push(`${summary.updated} updated`);
    if (summary.skipped > 0) parts.push(`${summary.skipped} skipped`);
    if (summary.errors > 0) parts.push(`${summary.errors} errors`);
    return parts.join(', ') || `${summary.total} events`;
  };
  if (loading) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
      className: "trvlr-logs-settings",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_3__.PageHeading, {
        text: 'TRVLR Wordpress Manager Logs'
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("p", {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Loading logs...', 'trvlr')
      })]
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
    className: "trvlr-logs-settings",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_3__.PageHeading, {
      text: 'TRVLR Wordpress Manager Logs'
    }), message && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
      status: message.type,
      onRemove: () => setMessage(null),
      isDismissible: true,
      children: message.text
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Card, {
      style: {
        marginBottom: '15px'
      },
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CardBody, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
          style: {
            display: 'flex',
            gap: '10px',
            flexWrap: 'wrap'
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
            variant: "secondary",
            onClick: handleExportLogs,
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("span", {
              className: "dashicons dashicons-download",
              style: {
                marginRight: '5px'
              }
            }), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Export CSV', 'trvlr')]
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
            variant: "secondary",
            onClick: handleClearOldLogs,
            isBusy: saving,
            disabled: saving,
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Clear Old Logs (30+ days)', 'trvlr')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
            variant: "secondary",
            isDestructive: true,
            onClick: handleClearAllLogs,
            isBusy: saving,
            disabled: saving,
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Clear All Logs', 'trvlr')
          })]
        })
      })
    }), sessions.length === 0 ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Card, {
      className: "trvlr-card",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CardBody, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("p", {
          className: "description",
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('No logs found. Logs will appear here after syncing attractions.', 'trvlr')
        })
      })
    }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("div", {
      className: "trvlr-sync-sessions",
      children: sessions.map(session => {
        const sessionKey = session.session_id || 'standalone';
        const isExpanded = expandedSessions.has(sessionKey);
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Card, {
          className: "trvlr-sync-session",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CardBody, {
            style: {
              padding: '8px 12px'
            },
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
              className: "trvlr-session-header",
              onClick: () => toggleSession(sessionKey),
              style: {
                cursor: 'pointer',
                display: 'flex',
                alignItems: 'center',
                gap: '15px'
              },
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("span", {
                className: `dashicons dashicons-arrow-${isExpanded ? 'down' : 'right'}-alt2`
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
                style: {
                  flex: 1
                },
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
                  style: {
                    display: 'flex',
                    alignItems: 'center',
                    gap: '10px',
                    marginBottom: '5px'
                  },
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("strong", {
                    children: session.status === 'standalone' ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('System Events', 'trvlr') : `${(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Sync', 'trvlr')} - ${formatDate(session.started_at)}`
                  }), getStatusBadge(session.status)]
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
                  className: "description",
                  children: [getSummaryText(session.summary), session.completed_at && session.started_at && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("span", {
                    children: [" \u2022 ", (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Duration:', 'trvlr'), " ", Math.round((new Date(session.completed_at).getTime() - new Date(session.started_at).getTime()) / 1000), "s"]
                  })]
                })]
              })]
            }), isExpanded && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("div", {
              className: "trvlr-session-logs",
              style: {
                marginTop: '15px',
                paddingTop: '15px',
                borderTop: '1px solid #ddd'
              },
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("table", {
                className: "wp-list-table widefat fixed striped",
                style: {
                  width: '100%'
                },
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("thead", {
                  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("tr", {
                    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
                      style: {
                        width: '150px'
                      },
                      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Time', 'trvlr')
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
                      style: {
                        width: '140px'
                      },
                      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Type', 'trvlr')
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
                      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Message', 'trvlr')
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("th", {
                      style: {
                        width: '80px'
                      },
                      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Details', 'trvlr')
                    })]
                  })
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("tbody", {
                  children: session.logs.map(log => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("tr", {
                    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("td", {
                      children: formatDate(log.created_at)
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("td", {
                      children: getLogTypeBadge(log.log_type)
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("td", {
                      children: log.message
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("td", {
                      children: log.details && log.details !== '[]' && log.details !== 'null' ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
                        variant: "link",
                        size: "small",
                        onClick: () => setSelectedLog(log),
                        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('View', 'trvlr')
                      }) : '—'
                    })]
                  }, log.id))
                })]
              })
            })]
          })
        }, sessionKey);
      })
    }), selectedLog && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("div", {
      className: "trvlr-modal",
      style: {
        position: 'fixed',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        background: 'rgba(0,0,0,0.7)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        zIndex: 999999
      },
      onClick: () => setSelectedLog(null),
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        className: "trvlr-modal-content",
        style: {
          background: '#fff',
          padding: '20px',
          borderRadius: '4px',
          maxWidth: '600px',
          width: '90%',
          maxHeight: '80vh',
          overflow: 'auto'
        },
        onClick: e => e.stopPropagation(),
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
          style: {
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            marginBottom: '15px'
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("h3", {
            style: {
              margin: 0
            },
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Log Details', 'trvlr')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
            variant: "tertiary",
            onClick: () => setSelectedLog(null),
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("span", {
              className: "dashicons dashicons-no-alt"
            })
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("pre", {
          style: {
            background: '#f5f5f5',
            padding: '15px',
            borderRadius: '4px',
            overflow: 'auto',
            maxHeight: '50vh'
          },
          children: JSON.stringify(JSON.parse(selectedLog.details), null, 2)
        })]
      })
    })]
  });
};

/***/ }),

/***/ "./admin/src/settings-pages/main-settings.tsx":
/*!****************************************************!*\
  !*** ./admin/src/settings-pages/main-settings.tsx ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MainSettings: () => (/* binding */ MainSettings)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _getting_started_settings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./getting-started-settings */ "./admin/src/settings-pages/getting-started-settings.tsx");
/* harmony import */ var _connection_settings__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./connection-settings */ "./admin/src/settings-pages/connection-settings.jsx");
/* harmony import */ var _theme_settings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./theme-settings */ "./admin/src/settings-pages/theme-settings.tsx");
/* harmony import */ var _sync_settings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./sync-settings */ "./admin/src/settings-pages/sync-settings.tsx");
/* harmony import */ var _logs_settings__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./logs-settings */ "./admin/src/settings-pages/logs-settings.tsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__);







const MainSettings = () => {
  const tabs = [{
    key: 'getting-started',
    label: 'Getting Started',
    icon: 'dashicons-welcome-learn-more',
    component: _getting_started_settings__WEBPACK_IMPORTED_MODULE_1__.GettingsStartedSettings
  }, {
    key: 'connection',
    label: 'Connection',
    icon: 'dashicons-admin-settings',
    component: _connection_settings__WEBPACK_IMPORTED_MODULE_2__.ConnectionSettings
  }, {
    key: 'theme',
    label: 'Theme',
    icon: 'dashicons-admin-appearance',
    component: _theme_settings__WEBPACK_IMPORTED_MODULE_3__.ThemeSettings
  }, {
    key: 'sync',
    label: 'Sync',
    icon: 'dashicons-update',
    component: _sync_settings__WEBPACK_IMPORTED_MODULE_4__.SyncSettings
  }, {
    key: 'logs',
    label: 'Logs',
    icon: 'dashicons-list-view',
    component: _logs_settings__WEBPACK_IMPORTED_MODULE_5__.LogsSettings
  }];
  const getInitialTab = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    return tabParam && tabs.find(t => t.key === tabParam) ? tabParam : tabs[0].key;
  };
  const [activeTab, setActiveTab] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(getInitialTab());
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('tab', activeTab);
    const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
    window.history.replaceState({}, '', newUrl);
  }, [activeTab]);
  const handleTabClick = tabKey => {
    setActiveTab(tabKey);
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
    className: "trvlr-settings-wrapper",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("nav", {
      className: "trvlr-tabs-nav",
      children: tabs.map(tab => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("a", {
        href: "#",
        className: `trvlr-tab-link ${activeTab === tab.key ? 'active' : ''}`,
        onClick: e => {
          e.preventDefault();
          handleTabClick(tab.key);
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("span", {
          className: `dashicons ${tab.icon}`
        }), tab.label]
      }, tab.key))
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
      className: "trvlr-tabs-content",
      children: tabs.map((tab, index) => {
        const activeIndex = tabs.findIndex(t => t.key === activeTab);
        const positionClass = index < activeIndex ? 'before' : index > activeIndex ? 'after' : '';
        return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
          className: `trvlr-tab-pane ${activeTab === tab.key ? 'active' : ''} ${positionClass}`
          // style={{ display: activeTab === tab.key ? 'block' : 'none' }}
          ,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(tab.component, {})
        }, tab.key);
      })
    })]
  });
};

/***/ }),

/***/ "./admin/src/settings-pages/sync-settings.tsx":
/*!****************************************************!*\
  !*** ./admin/src/settings-pages/sync-settings.tsx ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SyncSettings: () => (/* binding */ SyncSettings)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../context/TrvlrContext */ "./admin/src/context/TrvlrContext.jsx");
/* harmony import */ var _components_page_heading__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../components/page-heading */ "./admin/src/components/page-heading.tsx");
/* harmony import */ var _settings_forms_manual_sync_form__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../settings-forms/manual-sync-form */ "./admin/src/settings-forms/manual-sync-form.tsx");
/* harmony import */ var _settings_forms_schedule_settings_form_tsx__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../settings-forms/schedule-settings-form.tsx */ "./admin/src/settings-forms/schedule-settings-form.tsx");
/* harmony import */ var _settings_forms_custom_edits_form__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../settings-forms/custom-edits-form */ "./admin/src/settings-forms/custom-edits-form.tsx");
/* harmony import */ var _settings_forms_danger_zone_form__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../settings-forms/danger-zone-form */ "./admin/src/settings-forms/danger-zone-form.tsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__);










const SyncSettings = () => {
  const {
    syncStats
  } = (0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__.useTrvlr)();
  const syncStatsElements = [{
    key: 'total_attractions',
    label: 'Total Attractions',
    color: '#1d2327'
  }, {
    key: 'synced_count',
    label: 'Synced (No Edits)',
    color: '#00a32a'
  }, {
    key: 'custom_edit_count',
    label: 'With Custom Edits',
    color: '#dba617'
  }];
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)("div", {
    className: "trvlr-sync-settings",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_4__.PageHeading, {
      text: 'Your TRVLR Products'
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)("div", {
      className: "trvlr-settings-section-spacer",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)("div", {
        style: {
          display: 'grid',
          gap: '20px'
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)("div", {
          style: {
            display: 'grid',
            gridTemplateColumns: 'repeat(3, 1fr)',
            gap: '20px'
          },
          children: syncStatsElements.map(element => {
            return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Card, {
              style: {
                borderLeft: `5px solid ${element.color}`
              },
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)("div", {
                style: {
                  display: 'grid',
                  gap: '10px',
                  padding: '20px',
                  textAlign: 'center'
                },
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)("div", {
                  style: {
                    fontSize: '36px',
                    fontWeight: 'bold',
                    color: element.color
                  },
                  children: syncStats?.[element.key] || 0
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)("div", {
                  style: {
                    color: '#666',
                    fontSize: '14px'
                  },
                  children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)(`${element.label}`, 'trvlr')
                })]
              })
            });
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_settings_forms_manual_sync_form__WEBPACK_IMPORTED_MODULE_5__.ManualSyncForm, {})]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)("div", {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_4__.PageHeading, {
          text: 'Sync Settings'
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_settings_forms_schedule_settings_form_tsx__WEBPACK_IMPORTED_MODULE_6__.ScheduleSettingsForm, {})]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)("div", {
        style: {
          display: 'grid',
          gap: '10px',
          justifyItems: 'start',
          width: '100%'
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_4__.PageHeading, {
          text: 'Attractions with Custom Edits'
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_settings_forms_custom_edits_form__WEBPACK_IMPORTED_MODULE_7__.CustomEditsForm, {})]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Card, {
        style: {
          borderColor: '#d63638'
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)("div", {
          style: {
            padding: '20px'
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)("h3", {
            className: "trvlr-settings-form-heading",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Danger Zone', 'trvlr')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)("p", {
            className: "trvlr-settings-form-description",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Delete data imported by this plugin.', 'trvlr')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_settings_forms_danger_zone_form__WEBPACK_IMPORTED_MODULE_8__.DangerZoneForm, {})]
        })
      })]
    })]
  });
};

/***/ }),

/***/ "./admin/src/settings-pages/theme-settings.tsx":
/*!*****************************************************!*\
  !*** ./admin/src/settings-pages/theme-settings.tsx ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ThemeSettings: () => (/* binding */ ThemeSettings)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../context/TrvlrContext */ "./admin/src/context/TrvlrContext.jsx");
/* harmony import */ var _components_page_heading__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../components/page-heading */ "./admin/src/components/page-heading.tsx");
/* harmony import */ var _components_theme_field__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../components/theme-field */ "./admin/src/components/theme-field.tsx");
/* harmony import */ var _components_theme_preview__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../components/theme-preview */ "./admin/src/components/theme-preview.tsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__);









const ThemeSettings = () => {
  const {
    themeSettings,
    saveThemeSettings,
    saving,
    themeConfig,
    processedThemeConfig
  } = (0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__.useTrvlr)();

  // Initialize state with merged defaults + saved settings
  const [settings, setSettings] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(() => (0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__.mergeWithDefaults)(themeSettings, themeConfig));

  // Update a single field
  const updateField = (key, value) => {
    setSettings(prev => ({
      ...prev,
      [key]: value
    }));
  };

  // Reset to defaults
  const resetToDefaults = () => {
    if (confirm((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Reset all theme settings to defaults?', 'trvlr'))) {
      setSettings((0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__.getThemeDefaults)(themeConfig));
    }
  };

  // Save settings
  const handleSave = async () => {
    const result = await saveThemeSettings(settings);
    if (result.success) {
      alert((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Theme settings saved successfully!', 'trvlr'));
    } else {
      console.error('Error saving settings:', result.error);
      alert((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Error saving settings. Please try again.', 'trvlr'));
    }
  };

  // Apply CSS variables to preview in real-time
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    applyCSSVariables();
  }, [settings]);
  const applyCSSVariables = () => {
    const preview = document.getElementById('trvlr-preview-card');
    if (!preview) return;
    const allFields = (0,_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_3__.getAllFieldsFromConfig)(themeConfig);
    allFields.forEach(field => {
      if (field.cssVar) {
        const value = settings[field.key];
        const unit = field.unit || '';
        preview.style.setProperty(field.cssVar, `${value}${unit}`);
      }
    });
  };

  // Render a field or group
  const renderFieldOrGroup = item => {
    if (item.type === 'group') {
      // Render a cols-X wrapper
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("div", {
        className: `trvlr-${item.colsClass}`,
        children: [item.label && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("div", {
          style: {
            gridColumn: '1 / -1',
            marginBottom: '8px'
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("strong", {
            children: item.label
          }), item.description && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("p", {
            style: {
              margin: '4px 0 0',
              color: '#666',
              fontSize: '13px'
            },
            children: item.description
          })]
        }), item.fields.map(field => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_components_theme_field__WEBPACK_IMPORTED_MODULE_5__.ThemeField, {
          field: field,
          value: settings[field.key],
          onChange: value => updateField(field.key, value)
        }, field.key))]
      }, item.colsClass);
    } else {
      // Regular field
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_components_theme_field__WEBPACK_IMPORTED_MODULE_5__.ThemeField, {
        field: item,
        value: settings[item.key],
        onChange: value => updateField(item.key, value)
      }, item.key);
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("div", {
    className: "trvlr-theme-settings trvlr-settings-sidebar-wrap",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("div", {
      style: {
        display: 'flex',
        flexDirection: 'column'
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_4__.PageHeading, {
        text: "Customise appearance of components"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("div", {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Panel, {
          children: Object.entries(processedThemeConfig).map(([groupKey, group]) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.PanelBody, {
            title: group.label,
            initialOpen: false,
            children: [group.description && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("p", {
              style: {
                marginTop: 0,
                color: '#666',
                fontSize: '13px'
              },
              children: group.description
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("div", {
              children: group.fields.map(item => renderFieldOrGroup(item))
            })]
          }, groupKey))
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("div", {
        style: {
          display: 'flex',
          gap: '10px',
          marginTop: '20px',
          flexGrow: 1,
          alignItems: 'flex-end'
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          variant: "primary",
          onClick: handleSave,
          isBusy: saving,
          disabled: saving,
          children: saving ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Saving...', 'trvlr') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Save Settings', 'trvlr')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          variant: "secondary",
          onClick: resetToDefaults,
          disabled: saving,
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Reset to Defaults', 'trvlr')
        })]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("div", {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_components_page_heading__WEBPACK_IMPORTED_MODULE_4__.PageHeading, {
        text: "Preview"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("div", {
        id: "trvlr-preview-card",
        style: {
          display: 'flex',
          position: 'sticky',
          top: '50px'
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_components_theme_preview__WEBPACK_IMPORTED_MODULE_6__.AttractionCardPreview, {})
      })]
    })]
  });
};

/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["apiFetch"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["ReactJSXRuntime"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!****************************************!*\
  !*** ./admin/src/trvlr-admin-root.jsx ***!
  \****************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _settings_pages_main_settings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./settings-pages/main-settings */ "./admin/src/settings-pages/main-settings.tsx");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _context_TrvlrContext__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./context/TrvlrContext */ "./admin/src/context/TrvlrContext.jsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__);




document.addEventListener('DOMContentLoaded', () => {
  const rootElement = document.getElementById('trvlr-settings-root');
  if (rootElement) {
    console.log('TRVLR: Found root element, rendering...');
    const root = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createRoot)(rootElement);
    root.render(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_context_TrvlrContext__WEBPACK_IMPORTED_MODULE_2__.TrvlrProvider, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_settings_pages_main_settings__WEBPACK_IMPORTED_MODULE_0__.MainSettings, {})
    }));
  } else {
    console.error('TRVLR: Root element #trvlr-settings-root NOT FOUND!');
  }
});
})();

/******/ })()
;
//# sourceMappingURL=trvlr-admin-root.jsx.js.map