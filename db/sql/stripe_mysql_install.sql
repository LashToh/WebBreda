-- Stripe Plugin for WebEngine CMS 1.2.7
-- Run this on your Me_MuOnline (WebEngine) database.
-- Replace {TABLE_PREFIX} with your WE_PREFIX (usually empty).

CREATE TABLE {TABLE_PREFIX}WEBENGINE_STRIPE_TRANSACTIONS (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	ip_payed VARCHAR(50) NULL,
	userID VARCHAR(25) NULL,
	buy_id VARCHAR(120) NULL,
	username VARCHAR(50) NULL,
	credits VARCHAR(50) NULL,
	description VARCHAR(50) NULL,
	method VARCHAR(50) NULL,
	method_payed VARCHAR(50) NULL,
	date_create VARCHAR(50) NULL,
	amount VARCHAR(20) NULL,
	type_money VARCHAR(10) NULL,
	buy_status VARCHAR(50) NULL,
	buy_detail VARCHAR(120) NULL,
	approved_date VARCHAR(50) NULL
);
