<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['admin/top-sale-products/(:any)'] = 'admin/pages/top_sale_products';
$route['admin/top-sale-products'] = 'admin/pages/top_sale_products';

$route['admin/payment-faq/edit/(.+)']="admin/pages/payment_faq_form";
$route['admin/payment-faq/add'] = 'admin/pages/payment_faq_form';

$route['admin/faq/edit/(.+)']="admin/pages/faq_form";
$route['admin/faq/add'] = 'admin/pages/faq_form';

// download invoice

$route['product-invoice/(.+)'] = 'admin/order/download_invoice';

$route['admin/refunds/export']="admin/export/refund";

$route['admin/transactions/export']="admin/export/transaction";

$route['admin/notification']="admin/pages/notification";

$route['admin/refunds']="admin/pages/refunds";

$route['admin/transactions']="admin/pages/transaction";

$route['admin/contacts/edit/(.+)']="admin/contacts/contact_form";
$route['admin/contacts/add'] = 'admin/contacts/contact_form';

$route['admin/deal-of-day/add'] = 'admin/deal/deal_form';
$route['admin/deal-of-day'] = 'admin/deal';

$route['admin/generatepdf'] = "admin/order/generatePDFFile";

$route['admin/orders/print/(.+)'] = 'admin/order/print_order';
$route['admin/orders/(.+)'] = 'admin/order/order_summary';
$route['admin/orders'] = 'admin/order';

$route['admin/offers/edit/(:num)']="admin/offers/offer_form";
$route['admin/offers/add'] = 'admin/offers/offer_form';
$route['admin/offers/(:num)'] = 'admin/offers';
$route['admin/offers'] = 'admin/offers';

$route['admin/users/profile/(.+)'] = 'admin/users/user_profile';
$route['admin/users/edit/(.+)']="admin/users/user_form";
$route['admin/users/add'] = 'admin/users/user_form';
$route['admin/users/(:num)'] = 'admin/users';
$route['admin/users'] = 'admin/users';

$route['admin/coupon/edit/(:num)']="admin/coupon/coupon_form";
$route['admin/coupon/add'] = 'admin/coupon/coupon_form';
$route['admin/coupon/(:num)'] = 'admin/coupon';
$route['admin/coupon'] = 'admin/coupon';

$route['admin/banner/edit/(.+)']="admin/banner/banner_form";
$route['admin/banner/add'] = 'admin/banner/banner_form';
$route['admin/banner/products/(:num)'] = 'admin/banner/banner_products';
$route['admin/banner/(:num)'] = 'admin/banner';
$route['admin/banner'] = 'admin/banner';

$route['admin/products/duplicate-product/(.+)']="admin/product/clone_product";
$route['admin/products/edit/(:num)']="admin/product/product_form";
$route['admin/products/add'] = 'admin/product/product_form';
$route['admin/products/(:any)'] = 'admin/product';

$route['admin/products'] = 'admin/product';

$route['admin/brand/edit/(:num)']="admin/brand/brand_form";
$route['admin/brand/add'] = 'admin/brand/brand_form';
$route['admin/brand/(:num)'] = 'admin/brand';
$route['admin/brand'] = 'admin/brand';

$route['admin/sub-category/edit/(:num)'] = 'admin/SubCategory/sub_category_form';
$route['admin/sub-category/add'] = 'admin/SubCategory/sub_category_form';
$route['admin/sub-category/(:num)'] = 'admin/SubCategory';
$route['admin/sub-category'] = 'admin/SubCategory';

$route["admin/category/edit/(:num)"]="admin/category/category_form";
$route['admin/category/add'] = 'admin/category/category_form';
$route['admin/category/(:num)'] = 'admin/category';
$route['admin/category'] = 'admin/category';

$route['admin/profile'] = 'admin/pages/profile';

$route['admin/backup'] = 'admin/pages/backup';

$route['admin/page_settings'] = 'admin/pages/page_settings';
$route['admin/web-settings'] = 'admin/pages/web_settings';
$route['admin/android-settings'] = 'admin/pages/android_settings';
$route['admin/settings'] = 'admin/pages/settings';
$route['admin/verify-purchase'] = 'admin/pages/verify_purchase_page';
$route['admin/smtp-settings'] = 'admin/pages/smtp_settings';
$route['admin/api-urls'] = 'admin/pages/api_urls';

$route['admin/dashboard'] = 'admin/pages/dashboard';
$route['admin/logout'] = 'auth/logout';
$route['admin/login'] = 'auth/login';
$route['admin/forgot_passoword_form'] = 'auth/forgot_password_form';
$route['admin/forgot-password'] = 'auth/forgot_password';

$route['admin'] = 'auth';

$route['download-invoice/(.+)'] = 'order/download_invoice';

$route['saved-bank-accounts'] = 'bank/saved_bank_accounts';

$route['delete-instruction'] = 'pages/delete_instruction';

$route['cancellation'] = 'pages/cancellation';
$route['refund-return-policy'] = 'pages/refund_return_policy';
$route['privacy'] = 'pages/privacy';
$route['terms-of-use'] = 'pages/terms_of_use';
$route['payments'] = 'pages/payments';
$route['faq'] = 'pages/faq';
$route['about-us'] = 'pages/about_us';
$route['contact-us'] = 'pages/contact_us';

$route['my-reviews'] = 'user/my_reviews';
$route['product-reviews/(:any)'] = 'user/product_reviews';
$route['product-reviews/(:any)/(:any)'] = 'user/product_reviews';
$route['my-addresses'] = 'user/my_addresses';
$route['change-password'] = 'user/change_password_page';
$route['my-account'] = 'user/my_account';

$route['my-orders/(:any)'] = 'order/my_order/$1';
$route['my-orders'] = 'order/my_order';

$route['buy-now'] = 'checkout/buy_now';
$route['checkout'] = 'checkout/checkout';
$route['order-confirm'] = 'order/order_confirm';

$route['remove-to-cart/(:num)'] = 'user/remove_cart/$1';
$route['my-cart'] = 'user/my_cart';
$route['wishlist'] = 'user/wishlist';

$route['redirectGoogle'] = 'google/redirect_url';
$route['googleCallback'] = 'google/callback';
$route['redirectFacebook'] = 'facebook/redirect_url';
$route['facebookCallback'] = 'facebook/callback';

$route['reset-password'] = 'pages/reset_password_page';

$route['search-result/(:any)'] = 'product/search';
$route['search-result'] = 'product/search';

$route['product/(:any)'] = 'product/single_product/$1';

$route['recently-viewed-products/(:any)'] = 'product/recently_viewed_products';
$route['recently-viewed-products'] = 'product/recently_viewed_products';

$route['top-rated-products/(:any)'] = 'product/top_rated_products';
$route['top-rated-products'] = 'product/top_rated_products';

$route['latest-products/(:any)'] = 'product/latest_products';
$route['latest-products'] = 'product/latest_products';

$route['offers/(:any)/(:any)'] = 'product/offer_products';
$route['offers/(:any)'] = 'product/offer_products';
$route['offers'] = 'pages/offers';

$route['brand/(:any)/(:num)'] = 'product/brand_product/$1/$2';
$route['brand/(:any)'] = 'product/brand_product/$1/$2';
$route['brand'] = 'pages/brand';

$route['category/(:any)'] = 'pages/sub_category';
$route['category/(:any)/(:any)/(:num)'] = 'product/cat_sub_product/$1/$2';
$route['category/(:any)/(:any)'] = 'product/cat_sub_product/$1/$2';
$route['category'] = 'pages/category';

$route['todays-deals/(:any)'] = 'product/todays_deals';
$route['todays-deals'] = 'product/todays_deals';

$route['banners/(:any)/(:any)'] = 'product/banner_products';
$route['banners/(:any)'] = 'product/banner_products';
$route['banners'] = 'pages/banners';

$route['install'] = 'pages/page_not_found';

$route['forgot-password'] = 'pages/forgot_password';
$route['register'] = 'pages/register';
$route['login-register'] = 'pages/login';

$route['default_controller'] = 'pages/home';
$route['404_override'] = 'pages/page_404';
$route['translate_uri_dashes'] = FALSE;


/* End of file routes.php */
/* Location: ./application/config/routes.php */