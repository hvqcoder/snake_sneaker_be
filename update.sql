ALTER TABLE `tbl_settings` 
	ADD `braintree_status` VARCHAR(30) NOT NULL DEFAULT 'false' AFTER `razorpay_theme_color`, 
	ADD `braintree_mode` VARCHAR(10) NOT NULL AFTER `braintree_status`, 
	ADD `braintree_pubic_key` VARCHAR(150) NOT NULL AFTER `braintree_mode`, 
	ADD `braintree_private_key` TEXT NOT NULL AFTER `braintree_pubic_key`, 
	ADD `braintree_merchant_id` VARCHAR(150) NOT NULL AFTER `braintree_private_key`,
	ADD `braintree_merchant_account_id` VARCHAR(150) NOT NULL AFTER `braintree_merchant_id`;

ALTER TABLE `tbl_settings` 
	ADD `paystack_status` VARCHAR(30) NOT NULL DEFAULT 'false' AFTER `braintree_merchant_account_id`, 
	ADD `paystack_pubic_key` TEXT NOT NULL AFTER `paystack_status`,
	ADD `paystack_secret_key` TEXT NOT NULL AFTER `paystack_pubic_key`;

ALTER TABLE `tbl_applied_coupon` CHANGE `cart_id` `cart_id` VARCHAR(60) NOT NULL;

ALTER TABLE `tbl_bank_details` ADD `is_deleted` VARCHAR(1) NOT NULL DEFAULT 'N' AFTER `is_default`;

ALTER TABLE `tbl_settings` ADD `delete_instruction` LONGTEXT NOT NULL AFTER `app_home_recent_opt`; 