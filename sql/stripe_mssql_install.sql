-- Stripe Plugin for WebEngine CMS 1.2.7 (MSSQL)
-- Run this on your Me_MuOnline (WebEngine) database.
-- Replace {TABLE_PREFIX} with your WE_PREFIX (usually empty).

CREATE TABLE [dbo].[{TABLE_PREFIX}WEBENGINE_STRIPE_TRANSACTIONS] (
	[id] [int] IDENTITY(1,1) PRIMARY KEY,
	[ip_payed] [varchar](50) NULL,
	[userID] [varchar](25) NULL,
	[buy_id] [varchar](120) NULL,
	[username] [varchar](50) NULL,
	[credits] [varchar](50) NULL,
	[description] [varchar](50) NULL,
	[method] [varchar](50) NULL,
	[method_payed] [varchar](50) NULL,
	[date_create] [varchar](50) NULL,
	[amount] [varchar](20) NULL,
	[type_money] [varchar](10) NULL,
	[buy_status] [varchar](50) NULL,
	[buy_detail] [varchar](120) NULL,
	[approved_date] [varchar](50) NULL
);
