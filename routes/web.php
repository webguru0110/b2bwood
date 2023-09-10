<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
// use App\Mail\SupportMailManager;
//demo
//Route::get('/demo/cron_1', 'DemoController@cron_1');
//Route::get('/demo/cron_2', 'DemoController@cron_2');
//Route::get('/convert_assets', 'DemoController@convert_assets');
//Route::get('/convert_category', 'DemoController@convert_category');
//Route::get('/convert_tax', 'DemoController@convertTaxes');


Route::post('/credit-report/request/', 'CreditReportController@store')->name('credit-report.request');

Route::get('/refresh-csrf', function() {
    return csrf_token();
});
Route::post('/aiz-uploader', 'AizUploadController@show_uploader');
Route::post('/aiz-uploader/upload', 'AizUploadController@upload');
Route::get('/aiz-uploader/get_uploaded_files', 'AizUploadController@get_uploaded_files');
Route::post('/aiz-uploader/get_file_by_ids', 'AizUploadController@get_preview_files');
Route::get('/aiz-uploader/download/{id}', 'AizUploadController@attachment_download')->name('download_attachment');
// Tracking
Route::get('/aff{id}','AffiliateBannerController@track')->name('affiliate_banner.track');
Route::get('/link{id}','CompanyController@track_website_clicks')->name('website_clicks.track');
// Tracking - END

Auth::routes(['verify' => true]);
Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::get('/email/resend', 'Auth\VerificationController@resend')->name('email.verification.resend');
Route::get('/verification-confirmation/{code}', 'Auth\VerificationController@verification_confirmation')->name('email.verification.confirmation');
Route::get('/email_change/callback', 'HomeController@email_change_callback')->name('email_change.callback');
Route::post('/password/reset/email/submit', 'HomeController@reset_password_with_code')->name('user.password.update');


Route::post('/language', 'LanguageController@changeLanguage')->name('language.change');
Route::post('/currency', 'CurrencyController@changeCurrency')->name('currency.change');

Route::get('/social-login/redirect/{provider}', 'Auth\LoginController@redirectToProvider')->name('social.login');
Route::get('/social-login/{provider}/callback', 'Auth\LoginController@handleProviderCallback')->name('social.callback');
Route::get('/users/login', 'HomeController@login')->name('user.login');
Route::get('/users/registration', 'HomeController@registration')->name('user.registration');
Route::post('/users/login', 'HomeController@user_login')->name('user.login.submit');
Route::post('/users/login/cart', 'HomeController@cart_login')->name('cart.login.submit');

//Home Page
Route::get('/', 'HomeController@index')->name('home');
Route::post('/home/section/featured', 'HomeController@load_featured_section')->name('home.section.featured');
Route::post('/home/section/best_selling', 'HomeController@load_best_selling_section')->name('home.section.best_selling');
Route::post('/home/section/home_categories', 'HomeController@load_home_categories_section')->name('home.section.home_categories');
Route::post('/home/section/best_sellers', 'HomeController@load_best_sellers_section')->name('home.section.best_sellers');
//category dropdown menu ajax call
Route::post('/category/nav-element-list', 'HomeController@get_category_items')->name('category.elements');

//Flash Deal Details Page
Route::get('/flash-deals', 'HomeController@all_flash_deals')->name('flash-deals');
Route::get('/flash-deal/{slug}', 'HomeController@flash_deal_details')->name('flash-deal-details');


Route::get('/sitemap.xml', function() {
    return base_path('sitemap.xml');
});


Route::get('/customer-products', 'CustomerProductController@customer_products_listing')->name('customer.products');
Route::get('/customer-products?category={category_slug}', 'CustomerProductController@search')->name('customer_products.category');
Route::get('/customer-products?city={city_id}', 'CustomerProductController@search')->name('customer_products.city');
Route::get('/customer-products?q={search}', 'CustomerProductController@search')->name('customer_products.search');
Route::get('/customer-products/admin', 'HomeController@profile_edit')->name('customer.profile.edit');
Route::get('/customer-product/{slug}', 'CustomerProductController@customer_product')->name('customer.product');
Route::get('/customer-packages', 'HomeController@premium_package_index')->name('customer_packages_list_show');

Route::get('/search', 'HomeController@search')->name('search');
Route::get('/search?q={search}', 'HomeController@search')->name('suggestion.search');
Route::post('/ajax-search', 'HomeController@ajax_search')->name('search.ajax');

Route::get('/product/{slug}', 'HomeController@product')->name('product');
Route::get('/category/{category_slug}', 'HomeController@listingByCategory')->name('products.category');
Route::get('/brand/{brand_slug}', 'HomeController@listingByBrand')->name('products.brand');
Route::post('/product/variant_price', 'HomeController@variant_price')->name('products.variant_price');
Route::get('/shop/{slug}', 'HomeController@shop')->name('shop.visit');
Route::get('/shop/{slug}/info/{sub_page}', 'CompanyController@show')->name('shop.sub-page');
Route::get('/shop/{slug}/{type}', 'HomeController@filter_shop')->name('shop.visit.type');
Route::get('/event/{slug}', 'EventController@show')->name('event.visit');

Route::get('/cart', 'CartController@index')->name('cart');
Route::post('/cart/nav-cart-items', 'CartController@updateNavCart')->name('cart.nav_cart');
Route::post('/cart/show-cart-modal', 'CartController@showCartModal')->name('cart.showCartModal');
Route::post('/cart/addtocart', 'CartController@addToCart')->name('cart.addToCart');
Route::post('/cart/removeFromCart', 'CartController@removeFromCart')->name('cart.removeFromCart');
Route::post('/cart/updateQuantity', 'CartController@updateQuantity')->name('cart.updateQuantity');

//Checkout Routes
Route::group(['middleware' => ['checkout']], function() {
    Route::get('/checkout', 'CheckoutController@get_shipping_info')->name('checkout.shipping_info');
    Route::any('/checkout/delivery_info', 'CheckoutController@store_shipping_info')->name('checkout.store_shipping_infostore');
    Route::post('/checkout/payment_select', 'CheckoutController@store_delivery_info')->name('checkout.store_delivery_info');
});

Route::get('/checkout/order-confirmed', 'CheckoutController@order_confirmed')->name('order_confirmed');
Route::post('/checkout/payment', 'CheckoutController@checkout')->name('payment.checkout');
Route::post('/get_pick_ip_points', 'HomeController@get_pick_ip_points')->name('shipping_info.get_pick_ip_points');
Route::get('/checkout/payment_select', 'CheckoutController@get_payment_info')->name('checkout.payment_info');
Route::post('/checkout/apply_coupon_code', 'CheckoutController@apply_coupon_code')->name('checkout.apply_coupon_code');
Route::post('/checkout/remove_coupon_code', 'CheckoutController@remove_coupon_code')->name('checkout.remove_coupon_code');
//Club point
Route::post('/checkout/apply-club-point', 'CheckoutController@apply_club_point')->name('checkout.apply_club_point');
Route::post('/checkout/remove-club-point', 'CheckoutController@remove_club_point')->name('checkout.remove_club_point');

//Paypal START
Route::get('/paypal/payment/done', 'PaypalController@getDone')->name('payment.done');
Route::get('/paypal/payment/cancel', 'PaypalController@getCancel')->name('payment.cancel');
//Paypal END
// SSLCOMMERZ Start
Route::get('/sslcommerz/pay', 'PublicSslCommerzPaymentController@index');
Route::POST('/sslcommerz/success', 'PublicSslCommerzPaymentController@success');
Route::POST('/sslcommerz/fail', 'PublicSslCommerzPaymentController@fail');
Route::POST('/sslcommerz/cancel', 'PublicSslCommerzPaymentController@cancel');
Route::POST('/sslcommerz/ipn', 'PublicSslCommerzPaymentController@ipn');
//SSLCOMMERZ END
//Stipe Start
Route::get('stripe', 'StripePaymentController@stripe');
Route::post('/stripe/create-checkout-session', 'StripePaymentController@create_checkout_session')->name('stripe.get_token');
Route::any('/stripe/payment/callback', 'StripePaymentController@callback')->name('stripe.callback');
Route::get('/stripe/success', 'StripePaymentController@success')->name('stripe.success');
Route::get('/stripe/cancel', 'StripePaymentController@cancel')->name('stripe.cancel');
//Stripe END

Route::get('/compare', 'CompareController@index')->name('compare');
Route::get('/compare/reset', 'CompareController@reset')->name('compare.reset');
Route::post('/compare/addToCompare', 'CompareController@addToCompare')->name('compare.addToCompare');

Route::resource('subscribers', 'SubscriberController');

Route::get('/brands', 'HomeController@all_brands')->name('brands.all');
Route::get('/categories', 'HomeController@all_categories')->name('categories.all');
Route::get('/sellers', 'CompanyController@index')->name('sellers');

Route::get('/all_jobs', 'JobController@all_jobs')->name('jobs.all');
Route::get('/shops/{shop_slug}/jobs/{job_slug}','JobController@job')->name('jobs.visit');

Route::get('/sellerpolicy', 'HomeController@sellerpolicy')->name('sellerpolicy');
Route::get('/returnpolicy', 'HomeController@returnpolicy')->name('returnpolicy');
Route::get('/supportpolicy', 'HomeController@supportpolicy')->name('supportpolicy');
Route::get('/terms', 'HomeController@terms')->name('terms');
Route::get('/privacypolicy', 'HomeController@privacypolicy')->name('privacypolicy');

Route::resource('companies', 'CompanyController');
Route::get('/companies/category/{category_slug}', 'CompanyController@listingByCategory')->name('companies.category');

Route::group(['middleware' => ['user', 'verified', 'unbanned']], function() {
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');
    Route::get('/dashboard/thank-you', 'CompanyController@thankYouPage')->name('company.thank-you');
    Route::get('/profile', 'HomeController@profile')->name('profile');
    Route::get('/attributes', 'HomeController@attributes')->name('attributes');
    Route::post('/new-user-verification', 'HomeController@new_verify')->name('user.new.verify');
    Route::post('/new-user-email', 'HomeController@update_email')->name('user.change.email');
    Route::post('/customer/update-profile', 'HomeController@customer_update_profile')->name('customer.profile.update');
    Route::post('/seller/update-profile', 'HomeController@seller_update_profile')->name('seller.profile.update');
    Route::post('/seller/update-category', 'HomeController@seller_update_category')->name('seller.category.update');
    Route::post('/update_attributes', 'HomeController@update_attributes')->name('frontend.attributes.update');

    Route::resource('purchase_history', 'PurchaseHistoryController')->parameters([
        'purchase_history' => 'id',
    ]);
    Route::post('/purchase_history/details', 'PurchaseHistoryController@purchase_history_details')->name('purchase_history.details');
//    Route::get('/purchase_history/destroy/{id}', 'PurchaseHistoryController@destroy')->name('purchase_history.destroy');

    Route::resource('wishlists', 'WishlistController');
    Route::post('/wishlists/remove', 'WishlistController@destroy')->name('wishlists.remove');

    Route::get('/wallet', 'WalletController@index')->name('wallet.index');
    Route::post('/recharge', 'WalletController@recharge')->name('wallet.recharge');

    Route::resource('support_ticket', 'SupportTicketController');
    Route::post('support_ticket/reply', 'SupportTicketController@seller_store')->name('support_ticket.seller_store');

    Route::resource('customer_products', 'CustomerProductController')->parameters([
        'customer_products' => 'id',
    ]);
    Route::post('/customer_packages/purchase', 'CustomerPackageController@purchase_package')->name('customer_packages.purchase');
    Route::post('/customer_products/published', 'CustomerProductController@updatePublished')->name('customer_products.published');
    Route::post('/customer_products/status', 'CustomerProductController@updateStatus')->name('customer_products.update.status');

    Route::get('digital_purchase_history', 'PurchaseHistoryController@digital_index')->name('digital_purchase_history.index');
});

//Route::get('/customer_products/destroy/{id}', 'CustomerProductController@destroy')->name('customer_products.destroy');

Route::group(['prefix' => 'seller', 'middleware' => ['seller', 'verified', 'user']], function() {
    Route::get('/products', 'HomeController@seller_product_list')->name('seller.products');
    Route::get('/product/upload', 'HomeController@show_product_upload_form')->name('seller.products.upload');
    Route::get('/product/{id}/edit', 'HomeController@show_product_edit_form')->name('seller.products.edit');
    Route::post('/products/featured', 'ProductController@updateFeatured')->name('products.featured');

    Route::resource('payments', 'PaymentController');

    Route::get('/shop/apply_for_verification', 'ShopController@verify_form')->name('shop.verify');
    Route::post('/shop/apply_for_verification', 'ShopController@verify_form_store')->name('shop.verify.store');

    Route::get('/reviews', 'ReviewController@seller_reviews')->name('reviews.seller');

    //digital Product
    Route::get('/digitalproducts', 'HomeController@seller_digital_product_list')->name('seller.digitalproducts');
    Route::get('/digitalproducts/upload', 'HomeController@show_digital_product_upload_form')->name('seller.digitalproducts.upload');
    Route::get('/digitalproducts/{id}/edit', 'HomeController@show_digital_product_edit_form')->name('seller.digitalproducts.edit');

    //Events
    Route::get('/events', 'EventController@seller_events')->name('seller.events');
    Route::get('/events/upload', 'EventController@seller_event_create')->name('seller.event.create');
    Route::get('/events/{id}/edit', 'EventController@seller_event_edit')->name('seller.event.edit');

    // jobs
    Route::get('/jobs', 'JobController@seller_jobs')->name('seller.jobs');
    Route::get('/jobs/upload', 'JobController@seller_jobs_create')->name('seller.jobs.upload');
    Route::get('/jobs/{id}/edit', 'JobController@seller_jobs_edit')->name('seller.jobs.edit');
});

Route::group(['middleware' => ['auth']], function() {
    Route::post('/products/store/', 'ProductController@store')->name('products.store');
    Route::post('/products/update/{id}', 'ProductController@update')->name('products.update');
    Route::get('/products/destroy/{id}', 'ProductController@destroy')->name('products.destroy');
    Route::get('/products/duplicate/{id}', 'ProductController@duplicate')->name('products.duplicate');
    Route::post('/products/sku_combination', 'ProductController@sku_combination')->name('products.sku_combination');
    Route::post('/products/sku_combination_edit', 'ProductController@sku_combination_edit')->name('products.sku_combination_edit');
    Route::post('/products/seller/featured', 'ProductController@updateSellerFeatured')->name('products.seller.featured');
    Route::post('/products/published', 'ProductController@updatePublished')->name('products.published');

    Route::get('invoice/{order_id}', 'InvoiceController@invoice_download')->name('invoice.download');

    Route::resource('orders', 'OrderController')->parameters([
        'orders' => 'id',
    ])->except(['destroy']);
    Route::get('/orders/destroy/{id}', 'OrderController@destroy')->name('orders.destroy');
    Route::post('/orders/details', 'OrderController@order_details')->name('orders.details');
    Route::post('/orders/update_delivery_status', 'OrderController@update_delivery_status')->name('orders.update_delivery_status');
    Route::post('/orders/update_payment_status', 'OrderController@update_payment_status')->name('orders.update_payment_status');

    Route::get('/reviews', 'ReviewController@index')->name('reviews.index');
    Route::get('/shop/{company_name}/review/create', 'ReviewController@create')->name('reviews.create');
    Route::post('/review/store', 'ReviewController@store')->name('reviews.store');
    Route::post('/review/published', 'ReviewController@updatePublished')->name('reviews.published');

    Route::resource('/withdraw_requests', 'SellerWithdrawRequestController');
    Route::get('/withdraw_requests_all', 'SellerWithdrawRequestController@request_index')->name('withdraw_requests_all');
    Route::post('/withdraw_request/payment_modal', 'SellerWithdrawRequestController@payment_modal')->name('withdraw_request.payment_modal');
    Route::post('/withdraw_request/message_modal', 'SellerWithdrawRequestController@message_modal')->name('withdraw_request.message_modal');

    Route::resource('conversations', 'ConversationController')->parameters([
        'conversations' => 'id',
    ]);
//    Route::get('/conversations/destroy/{id}', 'ConversationController@destroy')->name('conversations.destroy');
    Route::post('conversations/refresh', 'ConversationController@refresh')->name('conversations.refresh');
    Route::post('conversations/save', 'ConversationController@saveConversation')->name('conversations.save');

    Route::resource('messages', 'MessageController');

    //Product Bulk Upload
    Route::get('/product-bulk-upload/index', 'ProductBulkUploadController@index')->name('product_bulk_upload.index');
    Route::post('/bulk-product-upload', 'ProductBulkUploadController@bulk_upload')->name('bulk_product_upload');
    Route::get('/product-csv-download/{type}', 'ProductBulkUploadController@import_product')->name('product_csv.download');
    Route::get('/vendor-product-csv-download/{id}', 'ProductBulkUploadController@import_vendor_product')->name('import_vendor_product.download');
    Route::group(['prefix' => 'bulk-upload/download'], function() {
        Route::get('/category', 'ProductBulkUploadController@pdf_download_category')->name('pdf.download_category');
        Route::get('/brand', 'ProductBulkUploadController@pdf_download_brand')->name('pdf.download_brand');
        Route::get('/seller', 'ProductBulkUploadController@pdf_download_seller')->name('pdf.download_seller');
    });

    //Product Export
    Route::get('/product-bulk-export', 'ProductBulkUploadController@export')->name('product_bulk_export.index');

    Route::resource('digitalproducts', 'DigitalProductController')->parameters([
        'digitalproducts' => 'id',
    ])->except(['destroy']);
//    Route::get('/digitalproducts/edit/{id}', 'DigitalProductController@edit')->name('digitalproducts.edit');
    Route::get('/digitalproducts/destroy/{id}', 'DigitalProductController@destroy')->name('digitalproducts.destroy');
    Route::get('/digitalproducts/download/{id}', 'DigitalProductController@download')->name('digitalproducts.download');

    //Reports
    Route::get('/commission-log', 'ReportController@commission_history')->name('commission-log.index');

    //Document and Gallery
    Route::resource('documentgallery', 'DocumentGalleryController')->parameters([
        'documentgallery' => 'id',
    ])->except(['destroy']);
//    Route::get('/documentgallery/edit/{id}', 'DocumentGalleryController@edit')->name('documentgallery.edit');
//    Route::post('/documentgallery/update/{id}', 'DocumentGalleryController@update')->name('documentgallery.update');
    Route::get('/documentgallery/destroy/{id}', 'DocumentGalleryController@destroy')->name('documentgallery.destroy');

    //Notifications
    Route::resource('notifications', 'NotificationController');
    Route::post('/notifications/mark-all-as-read', 'NotificationController@markAllAsRead')->name('notifications.mark_all_as_read');


    //Events
    Route::resource('events', 'EventController')->parameters([
        'events' => 'id',
    ])->except(['show', 'index', 'destroy']);
    Route::get('/events', 'EventController@all_events')->name('events');
    Route::get('/events/{slug}', 'EventController@show')->name('event.show');
    Route::get('/events/category/{category_slug}', 'EventController@listingByCategory')->name('events.category');
//    Route::post('/events/update/{id}', 'EventController@update')->name('event.update');
    Route::get('/events/destroy/{id}', 'EventController@destroy')->name('event.destroy');


    // Jobs
    Route::resource('jobs', 'JobController')->parameters([
        'jobs' => 'id',
    ])->except(['destroy']);
//    Route::post('/jobs/store', 'JobController@store')->name('jobs.store');
//    Route::post('/jobs/update/{id}', 'JobController@update')->name('jobs.update');
    Route::get('/jobs/destroy/{id}', 'JobController@destroy')->name('jobs.destroy');

});

Route::resource('shops', 'ShopController');

Route::get('/track_your_order', 'HomeController@trackOrder')->name('orders.track');

Route::get('/instamojo/payment/pay-success', 'InstamojoController@success')->name('instamojo.success');

Route::post('rozer/payment/pay-success', 'RazorpayController@payment')->name('payment.rozer');

Route::get('/paystack/payment/callback', 'PaystackController@handleGatewayCallback');

Route::get('/vogue-pay', 'VoguePayController@showForm');
Route::get('/vogue-pay/success/{id}', 'VoguePayController@paymentSuccess');
Route::get('/vogue-pay/failure/{id}', 'VoguePayController@paymentFailure');

//Iyzico
Route::any('/iyzico/payment/callback/{payment_type}/{amount?}/{payment_method?}/{order_id?}/{customer_package_id?}/{seller_package_id?}', 'IyzicoController@callback')->name('iyzico.callback');

Route::post('/get-city', 'CityController@get_city')->name('get-city');

Route::resource('addresses', 'AddressController')->parameters([
    'addresses' => 'id',
]);
//Route::post('/addresses/update/{id}', 'AddressController@update')->name('addresses.update');
//Route::get('/addresses/destroy/{id}', 'AddressController@destroy')->name('addresses.destroy');
Route::get('/addresses/set_default/{id}', 'AddressController@set_default')->name('addresses.set_default');

//payhere below
Route::get('/payhere/checkout/testing', 'PayhereController@checkout_testing')->name('payhere.checkout.testing');
Route::get('/payhere/wallet/testing', 'PayhereController@wallet_testing')->name('payhere.wallet.testing');
Route::get('/payhere/customer_package/testing', 'PayhereController@customer_package_testing')->name('payhere.customer_package.testing');

Route::any('/payhere/checkout/notify', 'PayhereController@checkout_notify')->name('payhere.checkout.notify');
Route::any('/payhere/checkout/return', 'PayhereController@checkout_return')->name('payhere.checkout.return');
Route::any('/payhere/checkout/cancel', 'PayhereController@chekout_cancel')->name('payhere.checkout.cancel');

Route::any('/payhere/wallet/notify', 'PayhereController@wallet_notify')->name('payhere.wallet.notify');
Route::any('/payhere/wallet/return', 'PayhereController@wallet_return')->name('payhere.wallet.return');
Route::any('/payhere/wallet/cancel', 'PayhereController@wallet_cancel')->name('payhere.wallet.cancel');

Route::any('/payhere/seller_package_payment/notify', 'PayhereController@seller_package_notify')->name('payhere.seller_package_payment.notify');
Route::any('/payhere/seller_package_payment/return', 'PayhereController@seller_package_payment_return')->name('payhere.seller_package_payment.return');
Route::any('/payhere/seller_package_payment/cancel', 'PayhereController@seller_package_payment_cancel')->name('payhere.seller_package_payment.cancel');
Route::get('/migrate/products/', 'PayhereController@migrate_seller_package_payment');

Route::any('/payhere/customer_package_payment/notify', 'PayhereController@customer_package_notify')->name('payhere.customer_package_payment.notify');
Route::any('/payhere/customer_package_payment/return', 'PayhereController@customer_package_return')->name('payhere.customer_package_payment.return');
Route::any('/payhere/customer_package_payment/cancel', 'PayhereController@customer_package_cancel')->name('payhere.customer_package_payment.cancel');

//N-genius
Route::any('ngenius/cart_payment_callback', 'NgeniusController@cart_payment_callback')->name('ngenius.cart_payment_callback');
Route::any('ngenius/wallet_payment_callback', 'NgeniusController@wallet_payment_callback')->name('ngenius.wallet_payment_callback');
Route::get('/migrate/database', 'NgeniusController@migrate_ngenius');
Route::any('ngenius/customer_package_payment_callback', 'NgeniusController@customer_package_payment_callback')->name('ngenius.customer_package_payment_callback');
Route::any('ngenius/seller_package_payment_callback', 'NgeniusController@seller_package_payment_callback')->name('ngenius.seller_package_payment_callback');

//bKash
Route::post('/bkash/createpayment', 'BkashController@checkout')->name('bkash.checkout');
Route::post('/bkash/executepayment', 'BkashController@excecute')->name('bkash.excecute');
Route::get('/bkash/success', 'BkashController@success')->name('bkash.success');

//Nagad
Route::get('/nagad/callback', 'NagadController@verify')->name('nagad.callback');


//Blog Section
Route::get('/news', 'BlogController@all_blog')->name('news');
Route::get('/news/{slug}', 'BlogController@blog_details')->name('news.details');
Route::get('/news/category/{slug}', 'BlogController@blog_category')->name('news.category');

// Chat
Route::get('/styleguide', 'PageController@styleguide')->name('styleguide.index');

Route::get('/chat', 'ChatController@index')->name('chat.index');
Route::get('/early-bird', 'PageController@early_bird')->name('landing.early-bird');
Route::get('/pricing', 'PageController@pricing')->name('landing.pricing');

//Custom page
Route::get('/{slug}', 'PageController@show_custom_page')->name('custom-pages.show_custom_page');


// Mailchimp subscriptions routes
Route::post('/subscribe/{type}', 'Integrations\MailchimpController@subscribe')
    ->name('mailchimp.subscribe');

Route::get('/page/{slug}', 'PageController@show_static_page')->name('page.static_page');

// Get Stream Integration routes
Route::get('/feed/all', 'Integrations\GetStreamControler@index');
