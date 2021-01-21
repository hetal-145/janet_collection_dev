define({ "api": [
  {
    "group": "Change_Password",
    "type": "post",
    "url": "change/password",
    "title": "User Change Password",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Change_Password",
    "name": "PostChangePassword"
  },
  {
    "group": "Forgot_Password",
    "type": "post",
    "url": "auth/forgot/password",
    "title": "Forgot Password",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_type",
            "description": "<p>customer</p>"
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Forgot_Password",
    "name": "PostAuthForgotPassword"
  },
  {
    "group": "General",
    "type": "post",
    "url": "http://13.126.61.97/janet_collection/ws/v1/",
    "title": "Prefix Url",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "General",
    "name": "PostHttpsGetnfixComApi"
  },
  {
    "group": "Notifications",
    "type": "get",
    "url": "notification_count",
    "title": "Notification Count",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Notifications",
    "name": "GetNotification_count"
  },
  {
    "group": "Notifications",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "messages_is_sms",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "messages_is_email",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "messages_is_push",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "request_updates_is_sms",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "request_updates_is_email",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "request_updates_is_push",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "transactions_is_sms",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "transactions_is_email",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "transactions_is_push",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "helpful_information_is_sms",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "helpful_information_is_email",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "helpful_information_is_push",
            "description": ""
          }
        ]
      }
    },
    "type": "post",
    "url": "notification_settings",
    "title": "Notification Setting",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Notifications",
    "name": "PostNotification_settings"
  },
  {
    "group": "Notify_Me",
    "type": "post",
    "url": "notify_me",
    "title": "Notify Me",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Double",
            "optional": true,
            "field": "max_price",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Double",
            "optional": true,
            "field": "min_price",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": true,
            "field": "min_distance",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": true,
            "field": "max_distance",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "search_keyword",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Notify_Me",
    "name": "PostNotify_me"
  },
  {
    "group": "Notify_Me",
    "type": "post",
    "url": "notify_me/delete",
    "title": "Delete Notify Me",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "id",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Notify_Me",
    "name": "PostNotify_meDelete"
  },

  {
    "group": "Shipping",
    "type": "post",
    "url": "shipping/charges",
    "title": "shipping charges",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "from_post_code",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "to_post_code",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "length_in_cm",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "width_in_cm",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "height_in_cm",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "weight_in_kg",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Shipping",
    "name": "PostShippingCharges"
  },
  // {
  //   "group": "Socket",
  //   "type": "TCP",
  //   "url": "https://www.getnfix.com:1333?user_id=1&user_type=customer",
  //   "title": "",
  //   "parameter": {
  //     "fields": {
  //       "Parameter": [
  //         {
  //           "group": "Parameter",
  //           "type": "Emit",
  //           "optional": false,
  //           "field": "message",
  //           "description": "<p><em>User App</em><br>Send message<br><code>{ &quot;message&quot;: &quot;hi&quot;, &quot;msg_type&quot; : &quot;text&quot;, &quot;chat_id&quot; : &quot;jasgdj&quot; }</code><br>==&gt; chat_id need to be get from part_request/fix_car_request/wreck_car_request<br>Use Ack for response</p>"
  //         },
  //         {
  //           "group": "Parameter",
  //           "type": "Listen",
  //           "optional": false,
  //           "field": "_message",
  //           "description": "<p><em>User App</em><br>Listen for new message<br><code>JSON Data</code></p>"
  //         },
  //         {
  //           "group": "Parameter",
  //           "type": "Emit",
  //           "optional": false,
  //           "field": "get_messages",
  //           "description": "<p><em>User App</em><br>Get all messages for particular request<br><code>{ &quot;chat_id&quot; : &quot;jasgdj&quot; }</code><br>==&gt; chat_id need to be get from part_request/fix_car_request/wreck_car_request<br>Use Ack for response</p>"
  //         },
  //         {
  //           "group": "Parameter",
  //           "type": "Emit",
  //           "optional": false,
  //           "field": "get_conversations",
  //           "description": "<p><em>User App</em><br>Get Conversations<br>Use Ack for response</p>"
  //         },
  //         {
  //           "group": "Parameter",
  //           "type": "Emit",
  //           "optional": false,
  //           "field": "get_fixcar_conversations",
  //           "description": "<p><em>User App</em><br>Get Conversations<br>Use Ack for response</p>"
  //         }
  //       ]
  //     }
  //   },
  //   "version": "1.0.0",
  //   "filename": "/var/www/html/janet_collection/routes/api.php",
  //   "groupTitle": "Socket",
  //   "name": "TcpHttpsWwwGetnfixCom1333User_id1User_typeCustomer"
  // },
  {
    "group": "Stripe",
    "type": "get",
    "url": "stripe/credentilas",
    "title": "get stripe credentials",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Stripe",
    "name": "GetStripeCredentilas"
  },
  {
    "group": "Stripe",
    "type": "get",
    "url": "stripe/get_cards",
    "title": "Get Stripe Cards",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Stripe",
    "name": "GetStripeGet_cards"
  },

  {
    "group": "Stripe",
    "type": "post",
    "url": "stripe/account/create",
    "title": "create stripe  account",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "account_holder_name",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "routing_number",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "account_number",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Stripe",
    "name": "PostStripeAccountCreate"
  },
  {
    "group": "Stripe",
    "type": "post",
    "url": "stripe/card/create",
    "title": "create stripe card",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "stripe_token",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Stripe",
    "name": "PostStripeCardCreate"
  },
  {
    "group": "Stripe",
    "type": "post",
    "url": "stripe/card/delete",
    "title": "Delete Stripe Cards",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "card_id",
            "description": ""
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Stripe",
    "name": "PostStripeCardDelete"
  },
  {
    "group": "Stripe",
    "type": "post",
    "url": "stripe/payment/withdraw",
    "title": "stripe payment withraw",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Double",
            "optional": false,
            "field": "amount",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Stripe",
    "name": "PostStripePaymentWithdraw"
  },
  {
    "group": "User",
    "type": "get",
    "url": "delete_account",
    "title": "Delete Account",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token</p>"
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "GetDelete_account"
  },
  {
    "group": "User",
    "type": "get",
    "url": "get_profile",
    "title": "Get Profile",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token</p>"
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "GetGet_profile"
  },
  {
    "group": "User",
    "type": "get",
    "url": "get_unread_notification_count",
    "title": "Get Unread Notification Count",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "offset",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "GetGet_unread_notification_count"
  },
  {
    "group": "user",
    "type": "get",
    "url": "logout",
    "title": "Logout",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token</p>"
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "user",
    "name": "GetLogout"
  },
  {
    "group": "User",
    "type": "get",
    "url": "notification_list",
    "title": "Get Notifications",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "offset",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "GetNotification_list"
  },
  {
    "group": "User",
    "type": "get",
    "url": "other_user_profile/{id}",
    "title": "Get Other User Profile",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token</p>"
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "GetOther_user_profileId"
  },
  {
    "group": "User",
    "type": "post",
    "url": "auth/send_otp",
    "title": "Send OTP",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "country_code",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_type",
            "description": "<p>customer</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_id",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "PostAuthSend_otp"
  },
  {
    "group": "user",
    "type": "post",
    "url": "register",
    "title": "Register",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "suburb",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "entity_name",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "latitude",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "longitude",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "user",
    "name": "PostRegister"
  },
  {
    "group": "User",
    "type": "post",
    "url": "resend/email/verification",
    "title": "Resend Email Verification",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token</p>"
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "PostResendEmailVerification"
  },
  {
    "group": "User",
    "type": "post",
    "url": "update/shipping_method",
    "title": "update shipping method",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "delivery_type",
            "description": "<p>self_delivery/pick_up/third-party</p>"
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "part_request_bid_id",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "PostUpdateShipping_method"
  },
  {
    "group": "User",
    "type": "post",
    "url": "update_location",
    "title": "Update Location",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "address",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "latitude",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "longitude",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "PostUpdate_location"
  },
  {
    "group": "User",
    "type": "post",
    "url": "update_profile",
    "title": "Update Profile",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "suburb",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "profile_address",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "profile_address_latitude",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "profile_address_longitude",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "profile_pic",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "about_me",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "Date",
            "optional": true,
            "field": "birth_date",
            "description": "<p>Birth date yyyy-mm-dd format</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": true,
            "field": "skills",
            "description": "<p>1,2,3</p>"
            
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "PostUpdate_profile"
  },
  {
    "group": "User",
    "type": "post",
    "url": "update_token",
    "title": "Update device token",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>bearer token</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "device_type",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_token",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "device_id",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "device_name",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "app_version",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "platform",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "PostUpdate_token"
  },
  {
    "group": "Web_View",
    "type": "get",
    "url": "http://3.104.18.33/account_faq",
    "title": "",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Web_View",
    "name": "GetHttp31041833Account_faq"
  },
  {
    "group": "Web_View",
    "type": "get",
    "url": "http://3.104.18.33/posting_and_tracking_faq",
    "title": "",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Web_View",
    "name": "GetHttp31041833Posting_and_tracking_faq"
  },
  {
    "group": "Web_View",
    "type": "get",
    "url": "http://3.104.18.33/privacy_policy",
    "title": "",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Web_View",
    "name": "GetHttp31041833Privacy_policy"
  },
  {
    "group": "Web_View",
    "type": "get",
    "url": "http://3.104.18.33/return_and_refund_faq",
    "title": "",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Web_View",
    "name": "GetHttp31041833Return_and_refund_faq"
  },
  {
    "group": "Web_View",
    "type": "get",
    "url": "http://3.104.18.33/selling_and_buying_faq",
    "title": "",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Web_View",
    "name": "GetHttp31041833Selling_and_buying_faq"
  },
  {
    "group": "Web_View",
    "type": "get",
    "url": "http://3.104.18.33/support_center",
    "title": "",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Web_View",
    "name": "GetHttp31041833Support_center"
  },
  {
    "group": "Web_View",
    "type": "get",
    "url": "http://3.104.18.33/terms_and_conditions",
    "title": "",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "Web_View",
    "name": "GetHttp31041833Terms_and_conditions"
  },
  
  {
    "group": "commission",
    "type": "get",
    "url": "commission",
    "title": "Commission",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "commission",
    "name": "GetCommission"
  },
  {
    "group": "gst",
    "type": "get",
    "url": "gst",
    "title": "GST",
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "gst",
    "name": "GetGst"
  },
  {
    "group": "User",
    "type": "post",
    "url": "auth/login",
    "title": "Login",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_type",
            "description": "<p>customer</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_token",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_id",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_name",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_type",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "User",
    "name": "PostAuthLogin"
  },
  {
    "group": "user",
    "type": "post",
    "url": "auth/verify",
    "title": "OTP verify",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "otp",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_type",
            "description": "customer"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_token",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_id",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_name",
            "description": ""
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_type",
            "description": ""
          }
        ]
      }
    },
    "version": "1.0.0",
    "filename": "/var/www/html/janet_collection/routes/api.php",
    "groupTitle": "user",
    "name": "PostAuthVerify"
  }
] });
