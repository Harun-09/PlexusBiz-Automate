-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: plexus_db
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `actor_id` bigint unsigned DEFAULT NULL,
  `module_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `subject_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `before_json` json DEFAULT NULL,
  `after_json` json DEFAULT NULL,
  `metadata_json` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_module_key_action_index` (`module_key`,`action`),
  KEY `audit_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `audit_logs_actor_id_created_at_index` (`actor_id`,`created_at`),
  KEY `audit_logs_module_key_index` (`module_key`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_subject_type_index` (`subject_type`),
  KEY `audit_logs_subject_id_index` (`subject_id`),
  KEY `audit_logs_ip_address_index` (`ip_address`),
  CONSTRAINT `audit_logs_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,NULL,'workflow','workflow.rules.created','App\\Domains\\Workflow\\Models\\AutomationRule',1,'Order confirmation automation','Automation rule created.',NULL,'{\"name\": \"Order confirmation automation\", \"status\": \"active\", \"priority\": 10, \"run_async\": false, \"actions_json\": [{\"type\": \"send_email\", \"config\": {\"body\": \"The order confirmation workflow completed.\", \"subject\": \"Your PlexusBiz order is confirmed\", \"to_path\": \"buyer.email\"}}, {\"type\": \"notify_supplier\", \"config\": {\"message\": \"Supplier notification prepared for order placement.\"}}], \"trigger_event\": \"order.placed\", \"conditions_json\": [{\"field\": \"order.grand_total\", \"value\": 0, \"operator\": \"greater_than_or_equal\"}]}','{\"trigger_event\": \"order.placed\"}',NULL,NULL,'2026-05-04 14:54:33','2026-05-04 14:54:33'),(2,NULL,'workflow','workflow.rules.created','App\\Domains\\Workflow\\Models\\AutomationRule',2,'Ticket supplier notification automation','Automation rule created.',NULL,'{\"name\": \"Ticket supplier notification automation\", \"status\": \"active\", \"priority\": 20, \"run_async\": false, \"actions_json\": [{\"type\": \"notify_supplier\", \"config\": {\"message\": \"Support ticket supplier notification was prepared.\"}}, {\"type\": \"create_ticket_auto_reply\", \"config\": {\"message\": \"Support auto response action recorded.\"}}], \"trigger_event\": \"ticket.created\", \"conditions_json\": [{\"field\": \"ticket.status\", \"value\": \"closed\", \"operator\": \"not_equals\"}]}','{\"trigger_event\": \"ticket.created\"}',NULL,NULL,'2026-05-04 14:54:33','2026-05-04 14:54:33'),(3,NULL,'workflow','workflow.rules.updated','App\\Domains\\Workflow\\Models\\AutomationRule',1,'Order confirmation automation','Automation rule updated.','{\"name\": \"Order confirmation automation\", \"status\": \"active\", \"priority\": 10, \"run_async\": false, \"actions_json\": [{\"type\": \"send_email\", \"config\": {\"body\": \"The order confirmation workflow completed.\", \"subject\": \"Your PlexusBiz order is confirmed\", \"to_path\": \"buyer.email\"}}, {\"type\": \"notify_supplier\", \"config\": {\"message\": \"Supplier notification prepared for order placement.\"}}], \"trigger_event\": \"order.placed\", \"conditions_json\": [{\"field\": \"order.grand_total\", \"value\": 0, \"operator\": \"greater_than_or_equal\"}]}','{\"name\": \"Order confirmation automation\", \"status\": \"active\", \"priority\": 10, \"run_async\": false, \"actions_json\": \"[{\\\"type\\\":\\\"send_email\\\",\\\"config\\\":{\\\"to_path\\\":\\\"buyer.email\\\",\\\"subject\\\":\\\"Your PlexusBiz order is confirmed\\\",\\\"body\\\":\\\"The order confirmation workflow completed.\\\"}},{\\\"type\\\":\\\"notify_supplier\\\",\\\"config\\\":{\\\"message\\\":\\\"Supplier notification prepared for order placement.\\\"}}]\", \"trigger_event\": \"order.placed\", \"conditions_json\": \"[{\\\"field\\\":\\\"order.grand_total\\\",\\\"operator\\\":\\\"greater_than_or_equal\\\",\\\"value\\\":0}]\"}','{\"trigger_event\": \"order.placed\", \"changed_fields\": [\"conditions_json\", \"actions_json\"]}',NULL,NULL,'2026-05-05 04:31:50','2026-05-05 04:31:50'),(4,NULL,'workflow','workflow.rules.updated','App\\Domains\\Workflow\\Models\\AutomationRule',2,'Ticket supplier notification automation','Automation rule updated.','{\"name\": \"Ticket supplier notification automation\", \"status\": \"active\", \"priority\": 20, \"run_async\": false, \"actions_json\": [{\"type\": \"notify_supplier\", \"config\": {\"message\": \"Support ticket supplier notification was prepared.\"}}, {\"type\": \"create_ticket_auto_reply\", \"config\": {\"message\": \"Support auto response action recorded.\"}}], \"trigger_event\": \"ticket.created\", \"conditions_json\": [{\"field\": \"ticket.status\", \"value\": \"closed\", \"operator\": \"not_equals\"}]}','{\"name\": \"Ticket supplier notification automation\", \"status\": \"active\", \"priority\": 20, \"run_async\": false, \"actions_json\": [{\"type\": \"notify_supplier\", \"config\": {\"message\": \"Support ticket supplier notification was prepared.\"}}, {\"type\": \"create_ticket_auto_reply\", \"config\": {\"message\": \"Support auto response action recorded.\"}}], \"trigger_event\": \"ticket.created\", \"conditions_json\": \"[{\\\"field\\\":\\\"ticket.status\\\",\\\"operator\\\":\\\"not_equals\\\",\\\"value\\\":\\\"closed\\\"}]\"}','{\"trigger_event\": \"ticket.created\", \"changed_fields\": [\"conditions_json\"]}',NULL,NULL,'2026-05-05 04:31:50','2026-05-05 04:31:50'),(5,NULL,'workflow','workflow.rules.created','App\\Domains\\Workflow\\Models\\AutomationRule',3,'RFQ supplier notification automation','Automation rule created.',NULL,'{\"name\": \"RFQ supplier notification automation\", \"status\": \"active\", \"priority\": 15, \"run_async\": false, \"actions_json\": [{\"type\": \"notify_supplier\", \"config\": {\"message\": \"New RFQ received for the requested product.\", \"subject\": \"New RFQ received\"}}], \"trigger_event\": \"rfq.created\", \"conditions_json\": [{\"field\": \"rfq.status\", \"value\": \"open\", \"operator\": \"equals\"}]}','{\"trigger_event\": \"rfq.created\"}',NULL,NULL,'2026-05-05 04:31:50','2026-05-05 04:31:50'),(6,NULL,'workflow','workflow.rules.created','App\\Domains\\Workflow\\Models\\AutomationRule',4,'Order status confirmed automation','Automation rule created.',NULL,'{\"name\": \"Order status confirmed automation\", \"status\": \"active\", \"priority\": 30, \"run_async\": false, \"actions_json\": [{\"type\": \"send_email\", \"config\": {\"body\": \"Your order has been confirmed. We are preparing it for the next fulfillment step.\", \"subject\": \"Your PlexusBiz order has been confirmed\", \"to_path\": \"buyer.email\"}}, {\"type\": \"create_notification\", \"config\": {\"message\": \"Your order has been confirmed. We are preparing it for the next fulfillment step.\", \"subject\": \"Your PlexusBiz order has been confirmed\"}}], \"trigger_event\": \"order.status_changed\", \"conditions_json\": [{\"field\": \"order.status\", \"value\": \"confirmed\", \"operator\": \"equals\"}]}','{\"trigger_event\": \"order.status_changed\"}',NULL,NULL,'2026-05-05 04:31:50','2026-05-05 04:31:50'),(7,NULL,'workflow','workflow.rules.created','App\\Domains\\Workflow\\Models\\AutomationRule',5,'Order status shipped automation','Automation rule created.',NULL,'{\"name\": \"Order status shipped automation\", \"status\": \"active\", \"priority\": 31, \"run_async\": false, \"actions_json\": [{\"type\": \"send_email\", \"config\": {\"body\": \"Your order has been shipped and is on the way.\", \"subject\": \"Your PlexusBiz order has shipped\", \"to_path\": \"buyer.email\"}}, {\"type\": \"create_notification\", \"config\": {\"message\": \"Your order has been shipped and is on the way.\", \"subject\": \"Your PlexusBiz order has shipped\"}}], \"trigger_event\": \"order.status_changed\", \"conditions_json\": [{\"field\": \"order.status\", \"value\": \"shipped\", \"operator\": \"equals\"}]}','{\"trigger_event\": \"order.status_changed\"}',NULL,NULL,'2026-05-05 04:31:50','2026-05-05 04:31:50'),(8,NULL,'workflow','workflow.rules.created','App\\Domains\\Workflow\\Models\\AutomationRule',6,'Order status completed automation','Automation rule created.',NULL,'{\"name\": \"Order status completed automation\", \"status\": \"active\", \"priority\": 32, \"run_async\": false, \"actions_json\": [{\"type\": \"send_email\", \"config\": {\"body\": \"Your order has been completed successfully.\", \"subject\": \"Your PlexusBiz order has been completed\", \"to_path\": \"buyer.email\"}}, {\"type\": \"create_notification\", \"config\": {\"message\": \"Your order has been completed successfully.\", \"subject\": \"Your PlexusBiz order has been completed\"}}], \"trigger_event\": \"order.status_changed\", \"conditions_json\": [{\"field\": \"order.status\", \"value\": \"completed\", \"operator\": \"equals\"}]}','{\"trigger_event\": \"order.status_changed\"}',NULL,NULL,'2026-05-05 04:31:50','2026-05-05 04:31:50'),(9,NULL,'workflow','workflow.rules.created','App\\Domains\\Workflow\\Models\\AutomationRule',7,'Order status cancelled automation','Automation rule created.',NULL,'{\"name\": \"Order status cancelled automation\", \"status\": \"active\", \"priority\": 33, \"run_async\": false, \"actions_json\": [{\"type\": \"send_email\", \"config\": {\"body\": \"Your order has been cancelled. Please contact support if you need help.\", \"subject\": \"Your PlexusBiz order has been cancelled\", \"to_path\": \"buyer.email\"}}, {\"type\": \"create_notification\", \"config\": {\"message\": \"Your order has been cancelled. Please contact support if you need help.\", \"subject\": \"Your PlexusBiz order has been cancelled\"}}, {\"type\": \"notify_supplier\", \"config\": {\"message\": \"The buyer cancelled the order.\", \"subject\": \"Supplier order cancellation notice\"}}], \"trigger_event\": \"order.status_changed\", \"conditions_json\": [{\"field\": \"order.status\", \"value\": \"cancelled\", \"operator\": \"equals\"}]}','{\"trigger_event\": \"order.status_changed\"}',NULL,NULL,'2026-05-05 04:31:51','2026-05-05 04:31:51');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `automation_rules`
--

DROP TABLE IF EXISTS `automation_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `automation_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trigger_event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conditions_json` json DEFAULT NULL,
  `actions_json` json NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `priority` int unsigned NOT NULL DEFAULT '100',
  `run_async` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `automation_rules_trigger_event_index` (`trigger_event`),
  KEY `automation_rules_status_index` (`status`),
  KEY `automation_rules_priority_index` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `automation_rules`
--

LOCK TABLES `automation_rules` WRITE;
/*!40000 ALTER TABLE `automation_rules` DISABLE KEYS */;
INSERT INTO `automation_rules` VALUES (1,'Order confirmation automation','order.placed','[{\"field\": \"order.grand_total\", \"value\": 0, \"operator\": \"greater_than_or_equal\"}]','[{\"type\": \"send_email\", \"config\": {\"body\": \"The order confirmation workflow completed.\", \"subject\": \"Your PlexusBiz order is confirmed\", \"to_path\": \"buyer.email\"}}, {\"type\": \"notify_supplier\", \"config\": {\"message\": \"Supplier notification prepared for order placement.\"}}]','active',10,0,'2026-05-04 14:54:33','2026-05-05 04:31:50',NULL),(2,'Ticket supplier notification automation','ticket.created','[{\"field\": \"ticket.status\", \"value\": \"closed\", \"operator\": \"not_equals\"}]','[{\"type\": \"notify_supplier\", \"config\": {\"message\": \"Support ticket supplier notification was prepared.\"}}, {\"type\": \"create_ticket_auto_reply\", \"config\": {\"message\": \"Support auto response action recorded.\"}}]','active',20,0,'2026-05-04 14:54:33','2026-05-05 04:31:50',NULL),(3,'RFQ supplier notification automation','rfq.created','[{\"field\": \"rfq.status\", \"value\": \"open\", \"operator\": \"equals\"}]','[{\"type\": \"notify_supplier\", \"config\": {\"message\": \"New RFQ received for the requested product.\", \"subject\": \"New RFQ received\"}}]','active',15,0,'2026-05-05 04:31:50','2026-05-05 04:31:50',NULL),(4,'Order status confirmed automation','order.status_changed','[{\"field\": \"order.status\", \"value\": \"confirmed\", \"operator\": \"equals\"}]','[{\"type\": \"send_email\", \"config\": {\"body\": \"Your order has been confirmed. We are preparing it for the next fulfillment step.\", \"subject\": \"Your PlexusBiz order has been confirmed\", \"to_path\": \"buyer.email\"}}, {\"type\": \"create_notification\", \"config\": {\"message\": \"Your order has been confirmed. We are preparing it for the next fulfillment step.\", \"subject\": \"Your PlexusBiz order has been confirmed\"}}]','active',30,0,'2026-05-05 04:31:50','2026-05-05 04:31:50',NULL),(5,'Order status shipped automation','order.status_changed','[{\"field\": \"order.status\", \"value\": \"shipped\", \"operator\": \"equals\"}]','[{\"type\": \"send_email\", \"config\": {\"body\": \"Your order has been shipped and is on the way.\", \"subject\": \"Your PlexusBiz order has shipped\", \"to_path\": \"buyer.email\"}}, {\"type\": \"create_notification\", \"config\": {\"message\": \"Your order has been shipped and is on the way.\", \"subject\": \"Your PlexusBiz order has shipped\"}}]','active',31,0,'2026-05-05 04:31:50','2026-05-05 04:31:50',NULL),(6,'Order status completed automation','order.status_changed','[{\"field\": \"order.status\", \"value\": \"completed\", \"operator\": \"equals\"}]','[{\"type\": \"send_email\", \"config\": {\"body\": \"Your order has been completed successfully.\", \"subject\": \"Your PlexusBiz order has been completed\", \"to_path\": \"buyer.email\"}}, {\"type\": \"create_notification\", \"config\": {\"message\": \"Your order has been completed successfully.\", \"subject\": \"Your PlexusBiz order has been completed\"}}]','active',32,0,'2026-05-05 04:31:50','2026-05-05 04:31:50',NULL),(7,'Order status cancelled automation','order.status_changed','[{\"field\": \"order.status\", \"value\": \"cancelled\", \"operator\": \"equals\"}]','[{\"type\": \"send_email\", \"config\": {\"body\": \"Your order has been cancelled. Please contact support if you need help.\", \"subject\": \"Your PlexusBiz order has been cancelled\", \"to_path\": \"buyer.email\"}}, {\"type\": \"create_notification\", \"config\": {\"message\": \"Your order has been cancelled. Please contact support if you need help.\", \"subject\": \"Your PlexusBiz order has been cancelled\"}}, {\"type\": \"notify_supplier\", \"config\": {\"message\": \"The buyer cancelled the order.\", \"subject\": \"Supplier order cancellation notice\"}}]','active',33,0,'2026-05-05 04:31:51','2026-05-05 04:31:51',NULL);
/*!40000 ALTER TABLE `automation_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_logs`
--

DROP TABLE IF EXISTS `campaign_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned DEFAULT NULL,
  `campaign_recipient_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `channel` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `response` json DEFAULT NULL,
  `error` text COLLATE utf8mb4_unicode_ci,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_logs_campaign_id_foreign` (`campaign_id`),
  KEY `campaign_logs_campaign_recipient_id_foreign` (`campaign_recipient_id`),
  KEY `campaign_logs_customer_id_foreign` (`customer_id`),
  KEY `campaign_logs_channel_index` (`channel`),
  KEY `campaign_logs_status_index` (`status`),
  KEY `campaign_logs_sent_at_index` (`sent_at`),
  CONSTRAINT `campaign_logs_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `campaign_logs_campaign_recipient_id_foreign` FOREIGN KEY (`campaign_recipient_id`) REFERENCES `campaign_recipients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `campaign_logs_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_logs`
--

LOCK TABLES `campaign_logs` WRITE;
/*!40000 ALTER TABLE `campaign_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_recipients`
--

DROP TABLE IF EXISTS `campaign_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign_recipients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_recipients_campaign_id_customer_id_unique` (`campaign_id`,`customer_id`),
  KEY `campaign_recipients_customer_id_foreign` (`customer_id`),
  KEY `campaign_recipients_status_index` (`status`),
  CONSTRAINT `campaign_recipients_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campaign_recipients_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_recipients`
--

LOCK TABLES `campaign_recipients` WRITE;
/*!40000 ALTER TABLE `campaign_recipients` DISABLE KEYS */;
/*!40000 ALTER TABLE `campaign_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign_templates`
--

DROP TABLE IF EXISTS `campaign_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned DEFAULT NULL,
  `template_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `channel` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `variables` json DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_templates_template_key_unique` (`template_key`),
  KEY `campaign_templates_campaign_id_foreign` (`campaign_id`),
  KEY `campaign_templates_channel_index` (`channel`),
  KEY `campaign_templates_status_index` (`status`),
  CONSTRAINT `campaign_templates_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign_templates`
--

LOCK TABLES `campaign_templates` WRITE;
/*!40000 ALTER TABLE `campaign_templates` DISABLE KEYS */;
INSERT INTO `campaign_templates` VALUES (1,NULL,'new_customer_welcome','email','New Customer Welcome','Welcome to PlexusBiz, {{ customer_name }}','Hello {{ customer_name }}, your B2B buyer workspace is ready.','[\"customer_name\", \"company_name\"]','active','2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(2,NULL,'order_confirmation','email','Order Confirmation','Order {{ order_number }} confirmed','Hello {{ customer_name }}, order {{ order_number }} has been confirmed. Invoice: {{ invoice_url }}','[\"customer_name\", \"order_number\", \"invoice_url\"]','active','2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(3,NULL,'abandoned_cart_reminder','email','Abandoned Cart Reminder','Complete your PlexusBiz order','Hello {{ customer_name }}, your cart is waiting: {{ abandoned_cart_url }}','[\"customer_name\", \"abandoned_cart_url\"]','active','2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(4,1,NULL,'email','Priority Wholesale Campaign Email','Priority supply options for {{ company_name }}','Hello {{ customer_name }}, your priority wholesale pricing is ready for review.','[\"customer_name\", \"company_name\"]','active','2026-05-04 14:54:33','2026-05-04 14:54:33',NULL);
/*!40000 ALTER TABLE `campaign_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaigns`
--

DROP TABLE IF EXISTS `campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_by` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `segment_filters_json` json DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaigns_slug_unique` (`slug`),
  KEY `campaigns_created_by_foreign` (`created_by`),
  KEY `campaigns_type_index` (`type`),
  KEY `campaigns_status_index` (`status`),
  KEY `campaigns_scheduled_at_index` (`scheduled_at`),
  CONSTRAINT `campaigns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaigns`
--

LOCK TABLES `campaigns` WRITE;
/*!40000 ALTER TABLE `campaigns` DISABLE KEYS */;
INSERT INTO `campaigns` VALUES (1,4,'Priority Wholesale Welcome','priority-wholesale-welcome','email','draft','{\"tags\": [\"priority\", \"wholesale\"]}',NULL,NULL,NULL,'2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(2,4,'Summer Sale 2024','summer-sale-2024','email','scheduled','{\"tags\": [\"wholesale\"]}','2026-05-09 14:54:34',NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(3,4,'New Product Launch','new-product-launch','email','running','{\"tags\": [\"wholesale\"]}','2026-05-16 14:54:34',NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(4,4,'Loyalty Reward Program','loyalty-reward','sms','completed','{\"tags\": [\"wholesale\"]}','2026-05-28 14:54:34',NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL);
/*!40000 ALTER TABLE `campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cart_items_cart_id_product_id_unique` (`cart_id`,`product_id`),
  KEY `cart_items_product_id_foreign` (`product_id`),
  KEY `cart_items_supplier_id_index` (`supplier_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `converted_order_id` bigint unsigned DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carts_converted_order_id_foreign` (`converted_order_id`),
  KEY `carts_user_id_status_index` (`user_id`,`status`),
  KEY `carts_status_index` (`status`),
  KEY `carts_expires_at_index` (`expires_at`),
  CONSTRAINT `carts_converted_order_id_foreign` FOREIGN KEY (`converted_order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  KEY `categories_status_index` (`status`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,NULL,'Industrial Equipment','industrial-equipment','active','B2B wholesale Industrial Equipment','2026-05-04 14:54:33','2026-05-05 01:17:17',NULL),(2,NULL,'Office Supplies','office-supplies','active','B2B wholesale office supplies','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(3,NULL,'Safety Equipment','safety-equipment','active','B2B wholesale safety equipment','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(4,NULL,'Packaging Materials','packaging-materials','active','B2B wholesale packaging materials','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(5,NULL,'Electronics & IT','electronics-it','active','B2B wholesale electronics & it','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(6,NULL,'Cleaning Supplies','cleaning-supplies','active','B2B wholesale cleaning supplies','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(7,NULL,'Industrial Safety & PPE','industrial-safety-ppe','active','Professional B2B wholesale Industrial Safety & PPE','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(8,NULL,'Office & Stationery Supplies','office-stationery','active','Professional B2B wholesale Office & Stationery Supplies','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(9,NULL,'Packaging & Shipping Materials','packaging-shipping','active','Professional B2B wholesale Packaging & Shipping Materials','2026-05-04 14:54:35','2026-05-04 14:54:35',NULL),(10,NULL,'IT & Computer Accessories','it-computer-accessories','active','Professional B2B wholesale IT & Computer Accessories','2026-05-04 14:54:35','2026-05-04 14:54:35',NULL),(11,NULL,'Cleaning & Maintenance','cleaning-maintenance','active','Professional B2B wholesale Cleaning & Maintenance','2026-05-04 14:54:35','2026-05-04 14:54:35',NULL),(12,NULL,'Industrial Tools & Equipment','industrial-tools','active','Professional B2B wholesale Industrial Tools & Equipment','2026-05-04 14:54:35','2026-05-04 14:54:35',NULL),(13,NULL,'Furniture & Fixtures','furniture-fixtures','active','Professional B2B wholesale Furniture & Fixtures','2026-05-04 14:54:35','2026-05-04 14:54:35',NULL),(14,NULL,'Electrical & Lighting','electrical-lighting','active','Professional B2B wholesale Electrical & Lighting','2026-05-04 14:54:35','2026-05-04 14:54:35',NULL),(15,NULL,'Warehouse & Storage','warehouse-storage','active','Professional B2B wholesale Warehouse & Storage','2026-05-04 14:54:35','2026-05-04 14:54:35',NULL),(16,NULL,'Warehouse Logistics','warehouse-logistics','active','B2B wholesale Warehouse Logistics','2026-05-05 01:17:17','2026-05-05 01:17:17',NULL),(17,NULL,'Office Furniture','office-furniture','active','B2B wholesale Office Furniture','2026-05-05 01:17:17','2026-05-05 01:17:17',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_segments`
--

DROP TABLE IF EXISTS `customer_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_segments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `description` text COLLATE utf8mb4_unicode_ci,
  `filters_json` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_segments_slug_unique` (`slug`),
  KEY `customer_segments_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_segments`
--

LOCK TABLES `customer_segments` WRITE;
/*!40000 ALTER TABLE `customer_segments` DISABLE KEYS */;
INSERT INTO `customer_segments` VALUES (1,'Priority Wholesale','priority-wholesale','active','Wholesale buyers tagged for priority account management.','{\"tags\": [\"wholesale\", \"priority\"], \"status\": \"active\"}','2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(2,'VIP Enterprise','vip-enterprise','active','Segment for vip enterprise','{\"tags\": [\"enterprise\", \"vip\"]}','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(3,'Regular Buyers','regular-buyers','active','Segment for regular buyers','{\"tags\": [\"regular\", \"loyal\"]}','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(4,'New Prospects','new-prospects','active','Segment for new prospects','{\"tags\": [\"new\", \"prospect\"]}','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL);
/*!40000 ALTER TABLE `customer_segments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` json DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `lifecycle_stage` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `tags` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_user_id_unique` (`user_id`),
  KEY `customers_email_index` (`email`),
  KEY `customers_status_index` (`status`),
  KEY `customers_lifecycle_stage_index` (`lifecycle_stage`),
  KEY `customers_last_activity_at_index` (`last_activity_at`),
  CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,3,'Plexus Buyer Company','Buyer User','buyer@plexus.test',NULL,NULL,NULL,'active','customer','[\"wholesale\", \"priority\"]',NULL,'2026-05-04 14:54:34','2026-05-04 14:54:33','2026-05-04 14:54:34',NULL);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interactions`
--

DROP TABLE IF EXISTS `interactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `interactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint unsigned DEFAULT NULL,
  `summary` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json DEFAULT NULL,
  `occurred_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interactions_customer_id_foreign` (`customer_id`),
  KEY `interactions_user_id_foreign` (`user_id`),
  KEY `interactions_related_type_related_id_index` (`related_type`,`related_id`),
  KEY `interactions_type_index` (`type`),
  KEY `interactions_occurred_at_index` (`occurred_at`),
  CONSTRAINT `interactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `interactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interactions`
--

LOCK TABLES `interactions` WRITE;
/*!40000 ALTER TABLE `interactions` DISABLE KEYS */;
INSERT INTO `interactions` VALUES (1,1,4,'note','internal',NULL,NULL,'Seeded CRM profile for buyer account.','[]','2026-05-04 14:54:33','2026-05-04 14:54:33','2026-05-04 14:54:33'),(2,1,3,'order',NULL,'App\\Domains\\ECommerce\\Models\\Order',1,'Sample order ORD-2024-0001 placed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34');
/*!40000 ALTER TABLE `interactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_movements`
--

DROP TABLE IF EXISTS `inventory_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `quantity_before` int NOT NULL,
  `quantity_after` int NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_movements_product_id_foreign` (`product_id`),
  KEY `inventory_movements_supplier_id_foreign` (`supplier_id`),
  KEY `inventory_movements_created_by_foreign` (`created_by`),
  KEY `inventory_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `inventory_movements_type_index` (`type`),
  CONSTRAINT `inventory_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_movements_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_movements`
--

LOCK TABLES `inventory_movements` WRITE;
/*!40000 ALTER TABLE `inventory_movements` DISABLE KEYS */;
INSERT INTO `inventory_movements` VALUES (1,1,1,2,'stock_in',100,0,100,NULL,NULL,'Initial supplier inventory','[]','2026-05-04 14:54:33','2026-05-04 14:54:33'),(2,2,1,2,'stock_in',500,0,500,NULL,NULL,'Initial stock','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(3,3,1,2,'stock_in',200,0,200,NULL,NULL,'Initial stock','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(4,4,1,2,'stock_in',1000,0,1000,NULL,NULL,'Initial stock','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(5,5,1,2,'stock_in',50,0,50,NULL,NULL,'Initial stock','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(6,6,1,2,'stock_in',300,0,300,NULL,NULL,'Initial stock','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(7,7,1,2,'stock_in',150,0,150,NULL,NULL,'Initial stock','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(8,8,1,2,'stock_in',80,0,80,NULL,NULL,'Initial stock','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(9,9,1,2,'stock_in',400,0,400,NULL,NULL,'Initial stock','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(10,10,1,2,'stock_in',850,0,850,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(11,11,1,2,'stock_in',640,0,640,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(12,12,1,2,'stock_in',4000,0,4000,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(13,13,1,2,'stock_in',3000,0,3000,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(14,14,1,2,'stock_in',600,0,600,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(15,15,1,2,'stock_in',20500,0,20500,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(16,16,1,2,'stock_in',1000,0,1000,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(17,17,1,2,'stock_in',465,0,465,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(18,18,1,2,'stock_in',8400,0,8400,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(19,19,1,2,'stock_in',990,0,990,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(20,20,1,2,'stock_in',230,0,230,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(21,21,1,2,'stock_in',300,0,300,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(22,22,1,2,'stock_in',2400,0,2400,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(23,23,1,2,'stock_in',2150,0,2150,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(24,24,1,2,'stock_in',3600,0,3600,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(25,25,1,2,'stock_in',3600,0,3600,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(26,26,1,2,'stock_in',2250,0,2250,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(27,27,1,2,'stock_in',480,0,480,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(28,28,1,2,'stock_in',250,0,250,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(29,29,1,2,'stock_in',2220,0,2220,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(30,30,1,2,'stock_in',6750,0,6750,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(31,31,1,2,'stock_in',1120,0,1120,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(32,32,1,2,'stock_in',455,0,455,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(33,33,1,2,'stock_in',800,0,800,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:34','2026-05-04 14:54:34'),(34,34,1,2,'stock_in',1000,0,1000,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(35,35,1,2,'stock_in',960,0,960,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(36,36,1,2,'stock_in',1200,0,1200,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(37,37,1,2,'stock_in',660,0,660,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(38,38,1,2,'stock_in',1350,0,1350,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(39,39,1,2,'stock_in',800,0,800,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(40,40,1,2,'stock_in',1700,0,1700,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(41,41,1,2,'stock_in',900,0,900,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(42,42,1,2,'stock_in',3100,0,3100,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(43,43,1,2,'stock_in',252,0,252,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(44,44,1,2,'stock_in',200,0,200,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(45,45,1,2,'stock_in',430,0,430,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(46,46,1,2,'stock_in',1850,0,1850,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(47,47,1,2,'stock_in',940,0,940,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(48,48,1,2,'stock_in',1520,0,1520,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(49,49,1,2,'stock_in',1200,0,1200,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(50,50,1,2,'stock_in',1365,0,1365,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(51,51,1,2,'stock_in',1600,0,1600,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(52,52,1,2,'stock_in',1380,0,1380,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(53,53,1,2,'stock_in',1250,0,1250,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(54,54,1,2,'stock_in',390,0,390,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(55,55,1,2,'stock_in',280,0,280,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(56,56,1,2,'stock_in',585,0,585,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(57,57,1,2,'stock_in',4000,0,4000,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(58,58,1,2,'stock_in',1280,0,1280,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(59,59,1,2,'stock_in',750,0,750,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(60,60,1,2,'stock_in',4400,0,4400,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(61,61,1,2,'stock_in',3600,0,3600,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(62,62,1,2,'stock_in',2640,0,2640,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(63,63,1,2,'stock_in',2150,0,2150,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(64,64,1,2,'stock_in',1720,0,1720,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(65,65,1,2,'stock_in',950,0,950,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(66,66,1,2,'stock_in',55,0,55,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(67,67,1,2,'stock_in',7800,0,7800,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(68,68,1,2,'stock_in',720,0,720,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(69,69,1,2,'stock_in',2460,0,2460,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(70,70,1,2,'stock_in',540,0,540,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(71,71,1,2,'stock_in',255,0,255,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(72,72,1,2,'stock_in',580,0,580,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(73,73,1,2,'stock_in',2000,0,2000,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(74,74,1,2,'stock_in',720,0,720,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(75,75,1,2,'stock_in',1230,0,1230,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(76,76,1,2,'stock_in',1680,0,1680,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(77,77,1,2,'stock_in',1575,0,1575,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(78,78,1,2,'stock_in',1440,0,1440,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(79,79,1,2,'stock_in',510,0,510,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(80,80,1,2,'stock_in',288,0,288,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(81,81,1,2,'stock_in',750,0,750,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(82,82,1,2,'stock_in',370,0,370,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(83,83,1,2,'stock_in',95,0,95,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(84,84,1,2,'stock_in',280,0,280,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(85,85,1,2,'stock_in',552,0,552,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(86,86,1,2,'stock_in',81,0,81,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(87,87,1,2,'stock_in',880,0,880,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(88,88,1,2,'stock_in',230,0,230,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(89,89,1,2,'stock_in',525,0,525,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(90,90,1,2,'stock_in',575,0,575,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(91,91,1,2,'stock_in',1800,0,1800,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(92,92,1,2,'stock_in',1450,0,1450,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(93,93,1,2,'stock_in',690,0,690,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(94,94,1,2,'stock_in',1550,0,1550,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(95,95,1,2,'stock_in',960,0,960,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(96,96,1,2,'stock_in',1715,0,1715,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(97,97,1,2,'stock_in',720,0,720,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(98,98,1,2,'stock_in',1160,0,1160,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(99,99,1,2,'stock_in',725,0,725,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(100,100,1,2,'stock_in',1520,0,1520,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(101,101,1,2,'stock_in',4800,0,4800,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(102,102,1,2,'stock_in',2350,0,2350,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(103,103,1,2,'stock_in',1500,0,1500,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(104,104,1,2,'stock_in',360,0,360,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(105,105,1,2,'stock_in',430,0,430,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(106,106,1,2,'stock_in',132,0,132,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(107,107,1,2,'stock_in',860,0,860,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:35','2026-05-04 14:54:35'),(108,108,1,2,'stock_in',1950,0,1950,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:36','2026-05-04 14:54:36'),(109,109,1,2,'stock_in',390,0,390,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:36','2026-05-04 14:54:36'),(110,110,1,2,'stock_in',516,0,516,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:36','2026-05-04 14:54:36'),(111,111,1,2,'stock_in',920,0,920,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:36','2026-05-04 14:54:36'),(112,112,1,2,'stock_in',150,0,150,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:36','2026-05-04 14:54:36'),(113,113,1,2,'stock_in',980,0,980,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:36','2026-05-04 14:54:36'),(114,114,1,2,'stock_in',165,0,165,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:36','2026-05-04 14:54:36'),(115,115,1,2,'stock_in',117,0,117,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:36','2026-05-04 14:54:36'),(116,116,1,2,'stock_in',600,0,600,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:36','2026-05-04 14:54:36'),(117,117,1,2,'stock_in',1100,0,1100,NULL,NULL,'Initial bulk inventory seed','[]','2026-05-04 14:54:36','2026-05-04 14:54:36'),(118,118,2,9,'stock_in',60,0,60,NULL,NULL,'Initial supplier inventory','[]','2026-05-05 01:17:17','2026-05-05 01:17:17'),(119,119,3,10,'stock_in',40,0,40,NULL,NULL,'Initial supplier inventory','[]','2026-05-05 01:17:17','2026-05-05 01:17:17');
/*!40000 ALTER TABLE `inventory_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'issued',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `issued_at` timestamp NULL DEFAULT NULL,
  `due_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_order_id_unique` (`order_id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_status_index` (`status`),
  KEY `invoices_issued_at_index` (`issued_at`),
  CONSTRAINT `invoices_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,1,'INV-2024-000001','paid',25000.00,2500.00,28000.00,'2026-04-27 14:54:34','2026-05-27 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(2,2,'INV-2024-000002','paid',50000.00,5000.00,56000.00,'2026-04-20 14:54:34','2026-05-20 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(3,3,'INV-2024-000003','paid',75000.00,7500.00,84000.00,'2026-04-13 14:54:34','2026-05-13 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL);
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned DEFAULT NULL,
  `assigned_user_id` bigint unsigned DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `next_follow_up_at` timestamp NULL DEFAULT NULL,
  `converted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leads_customer_id_foreign` (`customer_id`),
  KEY `leads_assigned_user_id_foreign` (`assigned_user_id`),
  KEY `leads_source_index` (`source`),
  KEY `leads_status_index` (`status`),
  KEY `leads_email_index` (`email`),
  KEY `leads_next_follow_up_at_index` (`next_follow_up_at`),
  CONSTRAINT `leads_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES (1,NULL,4,'website','qualified','Procurement Group','Procurement Lead','procurement@example.com','+8801711111111',750000.00,'Interested in recurring industrial equipment supply.','2026-05-07 14:54:33',NULL,'2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(2,NULL,4,'trade_show','new','Global Sourcing Co','Ahmed Khan','procurement@globalsourcing.test','+8801714788390',500000.00,'Interested in bulk supply agreement','2026-05-10 14:54:34',NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(3,NULL,4,'website','contacted','MegaCorp Industries','Sarah Johnson','buyer@megacorp.test','+8801770679952',1250000.00,'Interested in bulk supply agreement','2026-05-08 14:54:34',NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(4,NULL,4,'website','qualified','Tech Distributors Ltd','Raj Patel','orders@techdistributors.test','+8801747143202',750000.00,'Interested in bulk supply agreement','2026-05-09 14:54:34',NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL);
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint unsigned DEFAULT NULL,
  `receiver_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `channel` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload_json` json DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_receiver_id_index` (`sender_id`,`receiver_id`),
  KEY `messages_receiver_id_status_index` (`receiver_id`,`status`),
  KEY `messages_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `messages_customer_id_created_at_index` (`customer_id`,`created_at`),
  KEY `messages_channel_index` (`channel`),
  KEY `messages_status_index` (`status`),
  KEY `messages_sent_at_index` (`sent_at`),
  KEY `messages_read_at_index` (`read_at`),
  CONSTRAINT `messages_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,1,3,NULL,'system','Welcome to PlexusBiz!','Thank you for joining PlexusBiz. Your B2B buyer account is now active.',NULL,'sent','2026-04-28 14:54:34',NULL,NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(2,2,3,NULL,'system','New products available','We have added new safety equipment to our catalog. Check them out!',NULL,'sent','2026-04-30 14:54:34',NULL,NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(3,1,2,NULL,'system','Supplier verification complete','Your supplier account has been verified and approved.',NULL,'sent','2026-05-02 14:54:34',NULL,NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_05_03_123425_create_permission_tables',1),(6,'2026_05_03_124000_add_status_to_users_table',1),(7,'2026_05_03_130000_create_ecommerce_tables',1),(8,'2026_05_03_140000_create_crm_tables',1),(9,'2026_05_03_150000_create_marketing_tables',1),(10,'2026_05_03_160000_create_social_tables',1),(11,'2026_05_03_170000_create_workflow_tables',1),(12,'2026_05_03_180000_create_support_tables',1),(13,'2026_05_04_000001_add_payment_fields_to_orders_table',1),(14,'2026_05_04_000002_create_payments_table',1),(15,'2026_05_04_000003_add_media_storage_meta_to_product_images_and_social_posts',1),(16,'2026_05_04_000004_create_module_settings_table',1),(17,'2026_05_04_053802_create_pages_table',1),(18,'2026_05_04_100000_create_messages_table',1),(19,'2026_05_05_000001_create_audit_logs_table',1),(20,'2026_05_05_000001_create_supplier_orders_table',2),(21,'2026_05_05_000002_add_business_profile_fields_to_customers_table',2),(22,'2026_05_05_000003_create_notifications_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(3,'App\\Models\\User',3),(4,'App\\Models\\User',4),(5,'App\\Models\\User',5),(3,'App\\Models\\User',6),(3,'App\\Models\\User',7),(3,'App\\Models\\User',8),(2,'App\\Models\\User',9),(2,'App\\Models\\User',10),(2,'App\\Models\\User',11),(2,'App\\Models\\User',12),(1,'App\\Models\\User',13),(3,'App\\Models\\User',14),(4,'App\\Models\\User',15);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_settings`
--

DROP TABLE IF EXISTS `module_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_settings_module_key_unique` (`module_key`),
  KEY `module_settings_enabled_index` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_settings`
--

LOCK TABLES `module_settings` WRITE;
/*!40000 ALTER TABLE `module_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `module_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_supplier_id_status_index` (`supplier_id`,`status`),
  KEY `order_items_status_index` (`status`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `order_items_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,1,'Commercial Water Pump 100L','PX-PUMP-100',2,12500.00,25000.00,'completed','2026-05-04 14:54:34','2026-05-04 14:54:34'),(2,1,2,1,'Industrial Safety Helmet','PX-SAFETY-001',10,450.00,4500.00,'completed','2026-05-04 14:54:34','2026-05-04 14:54:34'),(3,1,3,1,'Safety Gloves (Pack of 12)','PX-SAFETY-002',5,1200.00,6000.00,'completed','2026-05-04 14:54:34','2026-05-04 14:54:34'),(4,2,1,1,'Commercial Water Pump 100L','PX-PUMP-100',2,12500.00,25000.00,'completed','2026-05-04 14:54:34','2026-05-04 14:54:34'),(5,2,2,1,'Industrial Safety Helmet','PX-SAFETY-001',10,450.00,4500.00,'completed','2026-05-04 14:54:34','2026-05-04 14:54:34'),(6,2,3,1,'Safety Gloves (Pack of 12)','PX-SAFETY-002',5,1200.00,6000.00,'completed','2026-05-04 14:54:34','2026-05-04 14:54:34'),(7,3,1,1,'Commercial Water Pump 100L','PX-PUMP-100',2,12500.00,25000.00,'completed','2026-05-04 14:54:34','2026-05-04 14:54:34'),(8,3,2,1,'Industrial Safety Helmet','PX-SAFETY-001',10,450.00,4500.00,'completed','2026-05-04 14:54:34','2026-05-04 14:54:34'),(9,3,3,1,'Safety Gloves (Pack of 12)','PX-SAFETY-002',5,1200.00,6000.00,'completed','2026-05-04 14:54:34','2026-05-04 14:54:34');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `buyer_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `shipping_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BDT',
  `checkout_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_buyer_id_status_index` (`buyer_id`,`status`),
  KEY `orders_customer_id_index` (`customer_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_placed_at_index` (`placed_at`),
  KEY `orders_checkout_token_index` (`checkout_token`),
  KEY `orders_payment_status_index` (`payment_status`),
  CONSTRAINT `orders_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,1,'ORD-2024-0001','completed',25000.00,2500.00,500.00,0.00,28000.00,'BDT','27963716-c82b-4201-8de9-5866eb108adf','stripe','processing',NULL,'2026-04-27 14:54:34','2026-05-04 14:54:34','2026-05-05 01:12:47',NULL),(2,3,1,'ORD-2024-0002','completed',50000.00,5000.00,500.00,0.00,56000.00,'BDT',NULL,NULL,'pending',NULL,'2026-04-20 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(3,3,1,'ORD-2024-0003','completed',75000.00,7500.00,500.00,0.00,84000.00,'BDT',NULL,NULL,'pending',NULL,'2026-04-13 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `payment_method` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_transaction_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BDT',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `gateway_response` json DEFAULT NULL,
  `payer_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_order_id_status_index` (`order_id`,`status`),
  KEY `payments_status_index` (`status`),
  KEY `payments_paid_at_index` (`paid_at`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,3,'stripe','TXN-ZSPTGLAWZXJ2','cs_test_a1gPa6gTO6gdEkfuQXPgfxcLAQ8e1w9q1HUgRZOqLLLripbyaBvFP7exRR',28000.00,'BDT','processing','{\"stripe_session_id\": \"cs_test_a1gPa6gTO6gdEkfuQXPgfxcLAQ8e1w9q1HUgRZOqLLLripbyaBvFP7exRR\", \"stripe_initiated_at\": \"2026-05-05T07:13:04+00:00\", \"stripe_redirect_url\": \"https://checkout.stripe.com/c/pay/cs_test_a1gPa6gTO6gdEkfuQXPgfxcLAQ8e1w9q1HUgRZOqLLLripbyaBvFP7exRR#fid1d2BpamRhQ2prcSc%2FJ0xrcWB3JyknZ2p3YWB3VnF8aWAnPydhYGNkcGlxJyknaWpmZGlgJz8nZHBxaicpJ2JwZGZkaGppYFNkd2xka3EnPydmamtxd2ppJyknZHVsTmB8Jz8ndW5acWB2cVowNFZ8Smh0NzQwcDVdZH1yMVVCPHBfS1dWNTJTT3UwQ2tIYGB8NXJda01AQUB0bXEwPHFqaXNoUVFRf3FvcWNnfFNvVGc1SWNMUG5pX1NBbFZjMHRkfGhONDU1RGByamEwXzcnKSdjd2poVmB3c2B3Jz9xd3BgKSdnZGZuYndqcGthRmppancnPycmY2NjY2NjJyknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl\"}',NULL,NULL,NULL,'2026-05-05 01:12:46','2026-05-05 01:13:04');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'view_dashboard','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(2,'manage_users','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(3,'manage_suppliers','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(4,'manage_products','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(5,'manage_orders','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(6,'manage_customers','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(7,'manage_campaigns','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(8,'manage_social_posts','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(9,'manage_automation_rules','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(10,'manage_workflow_logs','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(11,'manage_tickets','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(12,'manage_settings','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(13,'manage_own_products','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(14,'manage_own_orders','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(15,'manage_cart','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(16,'manage_own_tickets','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(17,'manage_marketing','web','2026-05-04 14:54:33','2026-05-04 14:54:33');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pricing_tiers`
--

DROP TABLE IF EXISTS `pricing_tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pricing_tiers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `min_quantity` int unsigned NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pricing_tiers_product_id_min_quantity_unique` (`product_id`,`min_quantity`),
  CONSTRAINT `pricing_tiers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=348 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pricing_tiers`
--

LOCK TABLES `pricing_tiers` WRITE;
/*!40000 ALTER TABLE `pricing_tiers` DISABLE KEYS */;
INSERT INTO `pricing_tiers` VALUES (1,1,5,11800.00,'2026-05-04 14:54:33','2026-05-04 14:54:33'),(2,1,10,11000.00,'2026-05-04 14:54:33','2026-05-04 14:54:33'),(3,2,20,405.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(4,2,50,360.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(5,3,10,1080.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(6,3,25,960.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(7,4,20,3150.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(8,4,50,2800.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(9,5,4,7650.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(10,5,10,6800.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(11,6,10,2520.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(12,6,25,2240.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(13,7,20,765.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(14,7,50,680.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(15,8,4,10800.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(16,8,10,9600.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(17,9,40,585.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(18,9,100,520.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(19,10,100,405.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(20,10,250,382.50,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(21,10,500,360.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(22,11,40,1080.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(23,11,100,1020.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(24,11,200,960.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(25,12,200,315.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(26,12,500,297.50,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(27,12,1000,280.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(28,13,200,252.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(29,13,500,238.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(30,13,1000,224.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(31,14,80,585.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(32,14,200,552.50,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(33,14,400,520.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(34,15,1000,40.50,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(35,15,2500,38.25,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(36,15,5000,36.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(37,16,50,1620.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(38,16,125,1530.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(39,16,250,1440.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(40,17,30,2880.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(41,17,75,2720.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(42,17,150,2560.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(43,18,400,162.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(44,18,1000,153.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(45,18,2000,144.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(46,19,60,2520.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(47,19,150,2380.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(48,19,300,2240.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(49,20,20,3780.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(50,20,50,3570.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(51,20,100,3360.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(52,21,40,3150.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(53,21,100,2975.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(54,21,200,2800.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(55,22,200,342.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(56,22,500,323.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(57,22,1000,304.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(58,23,100,585.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(59,23,250,552.50,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(60,23,500,520.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(61,24,400,108.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(62,24,1000,102.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(63,24,2000,96.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(64,25,200,252.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(65,25,500,238.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(66,25,1000,224.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(67,26,100,405.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(68,26,250,382.50,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(69,26,500,360.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(70,27,60,342.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(71,27,150,323.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(72,27,300,304.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(73,28,50,765.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(74,28,125,722.50,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(75,28,250,680.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(76,29,120,288.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(77,29,300,272.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(78,29,600,256.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(79,30,300,162.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(80,30,750,153.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(81,30,1500,144.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(82,31,80,252.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(83,31,200,238.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(84,31,400,224.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(85,32,70,468.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(86,32,175,442.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(87,32,350,416.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(88,33,100,432.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(89,33,250,408.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(90,33,500,384.00,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(91,34,100,765.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(92,34,250,722.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(93,34,500,680.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(94,35,40,1080.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(95,35,100,1020.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(96,35,200,960.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(97,36,80,612.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(98,36,200,578.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(99,36,400,544.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(100,37,30,1305.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(101,37,75,1232.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(102,37,150,1160.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(103,38,60,1080.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(104,38,150,1020.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(105,38,300,960.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(106,39,50,855.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(107,39,125,807.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(108,39,250,760.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(109,40,100,405.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(110,40,250,382.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(111,40,500,360.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(112,41,60,702.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(113,41,150,663.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(114,41,300,624.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(115,42,200,315.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(116,42,500,297.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(117,42,1000,280.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(118,43,24,1512.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(119,43,60,1428.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(120,43,120,1344.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(121,44,16,4050.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(122,44,40,3825.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(123,44,80,3600.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(124,45,20,1980.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(125,45,50,1870.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(126,45,100,1760.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(127,46,100,585.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(128,46,250,552.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(129,46,500,520.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(130,47,40,2520.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(131,47,100,2380.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(132,47,200,2240.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(133,48,80,1080.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(134,48,200,1020.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(135,48,400,960.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(136,49,50,1620.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(137,49,125,1530.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(138,49,250,1440.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(139,50,70,855.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(140,50,175,807.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(141,50,350,760.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(142,51,80,675.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(143,51,200,637.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(144,51,400,600.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(145,52,120,405.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(146,52,300,382.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(147,52,600,360.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(148,53,50,1080.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(149,53,125,1020.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(150,53,250,960.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(151,54,60,765.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(152,54,150,722.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(153,54,300,680.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(154,55,40,1980.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(155,55,100,1870.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(156,55,200,1760.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(157,56,90,612.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(158,56,225,578.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(159,56,450,544.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(160,57,160,342.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(161,57,400,323.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(162,57,800,304.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(163,58,80,405.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(164,58,200,382.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(165,58,400,360.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(166,59,60,585.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(167,59,150,552.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(168,59,300,520.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(169,60,200,162.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(170,60,500,153.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(171,60,1000,144.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(172,61,300,252.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(173,61,750,238.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(174,61,1500,224.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(175,62,160,288.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(176,62,400,272.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(177,62,800,256.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(178,63,100,522.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(179,63,250,493.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(180,63,500,464.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(181,64,80,405.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(182,64,200,382.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(183,64,400,360.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(184,65,50,765.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(185,65,125,722.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(186,65,250,680.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(187,66,10,10800.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(188,66,25,10200.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(189,66,50,9600.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(190,67,400,108.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(191,67,1000,102.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(192,67,2000,96.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(193,68,40,1080.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(194,68,100,1020.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(195,68,200,960.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(196,69,120,342.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(197,69,300,323.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(198,69,600,304.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(199,70,24,5850.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(200,70,60,5525.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(201,70,120,5200.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(202,71,30,2880.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(203,71,75,2720.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(204,71,150,2560.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(205,72,40,2520.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(206,72,100,2380.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(207,72,200,2240.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(208,73,100,405.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(209,73,250,382.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(210,73,500,360.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(211,74,30,1980.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(212,74,75,1870.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(213,74,150,1760.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(214,75,60,1080.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(215,75,150,1020.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(216,75,300,960.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(217,76,80,585.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(218,76,200,552.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(219,76,400,520.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(220,77,70,855.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(221,77,175,807.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(222,77,350,760.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(223,78,120,342.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(224,78,300,323.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(225,78,600,304.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(226,79,60,765.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(227,79,150,722.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(228,79,300,680.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(229,80,24,3420.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(230,80,60,3230.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(231,80,120,3040.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(232,81,50,1620.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(233,81,125,1530.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(234,81,250,1440.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(235,82,20,7650.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(236,82,50,7225.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(237,82,100,6800.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(238,83,10,25200.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(239,83,25,23800.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(240,83,50,22400.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(241,84,16,10800.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(242,84,40,10200.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(243,84,80,9600.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(244,85,24,4050.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(245,85,60,3825.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(246,85,120,3600.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(247,86,6,22500.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(248,86,15,21250.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(249,86,30,20000.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(250,87,40,1620.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(251,87,100,1530.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(252,87,200,1440.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(253,88,20,5850.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(254,88,50,5525.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(255,88,100,5200.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(256,89,30,3780.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(257,89,75,3570.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(258,89,150,3360.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(259,90,50,765.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(260,90,125,722.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(261,90,250,680.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(262,91,80,585.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(263,91,200,552.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(264,91,400,520.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(265,92,100,405.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(266,92,250,382.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(267,92,500,360.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(268,93,60,1080.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(269,93,150,1020.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(270,93,300,960.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(271,94,100,612.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(272,94,250,578.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(273,94,500,544.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(274,95,40,2520.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(275,95,100,2380.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(276,95,200,2240.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(277,96,70,765.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(278,96,175,722.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(279,96,350,680.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(280,97,40,1620.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(281,97,100,1530.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(282,97,200,1440.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(283,98,80,585.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(284,98,200,552.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(285,98,400,520.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(286,99,50,1080.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(287,99,125,1020.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(288,99,250,960.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(289,100,160,342.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(290,100,400,323.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(291,100,800,304.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(292,101,400,108.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(293,101,1000,102.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(294,101,2000,96.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(295,102,100,405.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(296,102,250,382.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(297,102,500,360.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(298,103,60,855.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(299,103,150,807.50,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(300,103,300,760.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(301,104,30,4050.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(302,104,75,3825.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(303,104,150,3600.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(304,105,20,5850.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(305,105,50,5525.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(306,105,100,5200.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(307,106,24,7650.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(308,106,60,7225.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(309,106,120,6800.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(310,107,40,2520.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(311,107,100,2380.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(312,107,200,2240.00,'2026-05-04 14:54:35','2026-05-04 14:54:35'),(313,108,100,585.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(314,108,250,552.50,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(315,108,500,520.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(316,109,30,2880.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(317,109,75,2720.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(318,109,150,2560.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(319,110,24,4050.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(320,110,60,3825.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(321,110,120,3600.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(322,111,40,2520.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(323,111,100,2380.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(324,111,200,2240.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(325,112,20,7650.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(326,112,50,7225.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(327,112,100,6800.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(328,113,40,1620.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(329,113,100,1530.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(330,113,200,1440.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(331,114,10,10800.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(332,114,25,10200.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(333,114,50,9600.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(334,115,6,16200.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(335,115,15,15300.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(336,115,30,14400.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(337,116,60,765.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(338,116,150,722.50,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(339,116,300,680.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(340,117,100,405.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(341,117,250,382.50,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(342,117,500,360.00,'2026-05-04 14:54:36','2026-05-04 14:54:36'),(343,1,4,11750.00,'2026-05-05 01:17:17','2026-05-05 01:17:17'),(344,118,10,11280.00,'2026-05-05 01:17:17','2026-05-05 01:17:17'),(345,118,25,10560.00,'2026-05-05 01:17:17','2026-05-05 01:17:17'),(346,119,6,7990.00,'2026-05-05 01:17:17','2026-05-05 01:17:17'),(347,119,15,7480.00,'2026-05-05 01:17:17','2026-05-05 01:17:17');
/*!40000 ALTER TABLE `pricing_tiers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_meta` json DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_is_primary_index` (`product_id`,`is_primary`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `base_price` decimal(12,2) NOT NULL,
  `moq` int unsigned NOT NULL DEFAULT '1',
  `stock_quantity` int unsigned NOT NULL DEFAULT '0',
  `reserved_quantity` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_supplier_id_status_index` (`supplier_id`,`status`),
  KEY `products_category_id_status_index` (`category_id`,`status`),
  KEY `products_stock_quantity_reserved_quantity_index` (`stock_quantity`,`reserved_quantity`),
  KEY `products_status_index` (`status`),
  KEY `products_published_at_index` (`published_at`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,1,'PX-PUMP-100','Commercial Water Pump 100L','commercial-water-pump-100l','High-volume commercial water pump for supplier-managed B2B orders.',12500.00,2,100,0,'active','2026-05-05 04:34:36','2026-05-04 14:54:33','2026-05-05 04:34:36',NULL),(2,1,3,'PX-SAFETY-001','Industrial Safety Helmet','industrial-safety-helmet','High-quality B2B wholesale Industrial Safety Helmet',450.00,10,500,0,'active','2026-05-04 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(3,1,3,'PX-SAFETY-002','Safety Gloves (Pack of 12)','safety-gloves-pack-of-12','High-quality B2B wholesale Safety Gloves (Pack of 12)',1200.00,5,200,0,'active','2026-05-04 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(4,1,2,'PX-OFFICE-001','A4 Copy Paper (Box of 10 reams)','a4-copy-paper-box-of-10-reams','High-quality B2B wholesale A4 Copy Paper (Box of 10 reams)',3500.00,10,1000,0,'active','2026-05-04 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(5,1,2,'PX-OFFICE-002','Ergonomic Office Chair','ergonomic-office-chair','High-quality B2B wholesale Ergonomic Office Chair',8500.00,2,50,0,'active','2026-05-04 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(6,1,4,'PX-PACK-001','Cardboard Box 18x18x18 (Bundle of 50)','cardboard-box-18x18x18-bundle-of-50','High-quality B2B wholesale Cardboard Box 18x18x18 (Bundle of 50)',2800.00,5,300,0,'active','2026-05-04 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(7,1,4,'PX-PACK-002','Bubble Wrap Roll 50m','bubble-wrap-roll-50m','High-quality B2B wholesale Bubble Wrap Roll 50m',850.00,10,150,0,'active','2026-05-04 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(8,1,5,'PX-IT-001','Wireless Mouse (Bulk 20 units)','wireless-mouse-bulk-20-units','High-quality B2B wholesale Wireless Mouse (Bulk 20 units)',12000.00,2,80,0,'active','2026-05-04 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(9,1,6,'PX-CLEAN-001','Industrial Floor Cleaner 5L','industrial-floor-cleaner-5l','High-quality B2B wholesale Industrial Floor Cleaner 5L',650.00,20,400,0,'active','2026-05-04 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(10,1,7,'PX-IND-0001','Heavy-Duty Safety Helmet ABS','heavy-duty-safety-helmet-abs-0','Professional-grade Heavy-Duty Safety Helmet ABS for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',450.00,50,850,0,'active','2026-04-06 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(11,2,7,'PX-IND-0002','Welding Mask Auto-Darkening','welding-mask-auto-darkening-1','Professional-grade Welding Mask Auto-Darkening for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',1200.00,20,640,0,'active','2026-04-03 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(12,3,7,'PX-IND-0003','Cut-Resistant Gloves Level 5','cut-resistant-gloves-level-5-2','Professional-grade Cut-Resistant Gloves Level 5 for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',350.00,100,4000,0,'active','2026-04-17 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(13,1,7,'PX-IND-0004','Safety Goggles Anti-Fog','safety-goggles-anti-fog-3','Professional-grade Safety Goggles Anti-Fog for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',280.00,100,3000,0,'active','2026-04-26 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(14,2,7,'PX-IND-0005','Ear Protection Earmuffs 30dB','ear-protection-earmuffs-30db-4','Professional-grade Ear Protection Earmuffs 30dB for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',650.00,40,600,0,'active','2026-04-26 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(15,3,7,'PX-IND-0006','Dust Mask N95 Certified','dust-mask-n95-certified-5','Professional-grade Dust Mask N95 Certified for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',45.00,500,20500,0,'active','2026-04-07 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(16,1,7,'PX-IND-0007','Chemical Resistant Suit','chemical-resistant-suit-6','Professional-grade Chemical Resistant Suit for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',1800.00,25,1000,0,'active','2026-03-13 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(17,2,7,'PX-IND-0008','Safety Harness Full Body','safety-harness-full-body-7','Professional-grade Safety Harness Full Body for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',3200.00,15,465,0,'active','2026-04-10 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(18,3,7,'PX-IND-0009','Reflective Safety Vest High-Viz','reflective-safety-vest-high-viz-8','Professional-grade Reflective Safety Vest High-Viz for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',180.00,200,8400,0,'active','2026-03-24 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(19,1,7,'PX-IND-0010','Steel Toe Safety Boots','steel-toe-safety-boots-9','Professional-grade Steel Toe Safety Boots for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',2800.00,30,990,0,'active','2026-03-17 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(20,2,7,'PX-IND-0011','Fire Extinguisher 6KG CO2','fire-extinguisher-6kg-co2-10','Professional-grade Fire Extinguisher 6KG CO2 for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',4200.00,10,230,0,'active','2026-04-17 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(21,3,7,'PX-IND-0012','First Aid Kit Industrial 50-Person','first-aid-kit-industrial-50-person-11','Professional-grade First Aid Kit Industrial 50-Person for B2B wholesale. Ideal for Industrial Safety & PPE applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',3500.00,20,300,0,'active','2026-03-25 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(22,1,8,'PX-OFF-0013','Premium A4 Copy Paper 80gsm (Ream)','premium-a4-copy-paper-80gsm-ream-12','Professional-grade Premium A4 Copy Paper 80gsm (Ream) for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',380.00,100,2400,0,'active','2026-04-20 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(23,2,8,'PX-OFF-0014','Gel Pen Blue 0.5mm (Box 50)','gel-pen-blue-05mm-box-50-13','Professional-grade Gel Pen Blue 0.5mm (Box 50) for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',650.00,50,2150,0,'active','2026-03-11 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(24,3,8,'PX-OFF-0015','Sticky Notes 3x3 Neon Colors','sticky-notes-3x3-neon-colors-14','Professional-grade Sticky Notes 3x3 Neon Colors for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',120.00,200,3600,0,'active','2026-04-21 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(25,1,8,'PX-OFF-0016','Document Envelope A4 (Pack 100)','document-envelope-a4-pack-100-15','Professional-grade Document Envelope A4 (Pack 100) for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',280.00,100,3600,0,'active','2026-03-22 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(26,2,8,'PX-OFF-0017','File Folder Cardboard (Box 50)','file-folder-cardboard-box-50-16','Professional-grade File Folder Cardboard (Box 50) for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',450.00,50,2250,0,'active','2026-04-16 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(27,3,8,'PX-OFF-0018','Stapler Heavy Duty 40 Sheets','stapler-heavy-duty-40-sheets-17','Professional-grade Stapler Heavy Duty 40 Sheets for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',380.00,30,480,0,'active','2026-04-22 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(28,1,8,'PX-OFF-0019','Printer Ink Cartridge HP Compatible','printer-ink-cartridge-hp-compatible-18','Professional-grade Printer Ink Cartridge HP Compatible for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',850.00,25,250,0,'active','2026-04-08 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(29,2,8,'PX-OFF-0020','Whiteboard Marker Set 8 Colors','whiteboard-marker-set-8-colors-19','Professional-grade Whiteboard Marker Set 8 Colors for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',320.00,60,2220,0,'active','2026-04-07 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(30,3,8,'PX-OFF-0021','Document Binder Clip Assorted','document-binder-clip-assorted-20','Professional-grade Document Binder Clip Assorted for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',180.00,150,6750,0,'active','2026-04-18 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(31,1,8,'PX-OFF-0022','Letter Tray Stackable Plastic','letter-tray-stackable-plastic-21','Professional-grade Letter Tray Stackable Plastic for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',280.00,40,1120,0,'active','2026-03-23 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(32,2,8,'PX-OFF-0023','Desk Organizer 5-Compartment','desk-organizer-5-compartment-22','Professional-grade Desk Organizer 5-Compartment for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',520.00,35,455,0,'active','2026-03-21 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(33,3,8,'PX-OFF-0024','Ballpoint Pen Black (Box 100)','ballpoint-pen-black-box-100-23','Professional-grade Ballpoint Pen Black (Box 100) for B2B wholesale. Ideal for Office & Stationery Supplies applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',480.00,50,800,0,'active','2026-03-13 01:05:27','2026-05-04 14:54:34','2026-05-05 01:05:27',NULL),(34,1,9,'PX-PAC-0025','Cardboard Box 12x12x12 (Bundle 25)','cardboard-box-12x12x12-bundle-25-24','Professional-grade Cardboard Box 12x12x12 (Bundle 25) for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',850.00,50,1000,0,'active','2026-03-28 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(35,2,9,'PX-PAC-0026','Bubble Wrap Roll 100m x 500mm','bubble-wrap-roll-100m-x-500mm-25','Professional-grade Bubble Wrap Roll 100m x 500mm for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',1200.00,20,960,0,'active','2026-04-13 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(36,3,9,'PX-PAC-0027','Packing Tape Clear 2-inch (Pack 36)','packing-tape-clear-2-inch-pack-36-26','Professional-grade Packing Tape Clear 2-inch (Pack 36) for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',680.00,40,1200,0,'active','2026-04-20 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(37,1,9,'PX-PAC-0028','Stretch Wrap Film 18-inch (Roll)','stretch-wrap-film-18-inch-roll-27','Professional-grade Stretch Wrap Film 18-inch (Roll) for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',1450.00,15,660,0,'active','2026-03-23 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(38,2,9,'PX-PAC-0029','Corrugated Mailer Box Small (Pack 50)','corrugated-mailer-box-small-pack-50-28','Professional-grade Corrugated Mailer Box Small (Pack 50) for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',1200.00,30,1350,0,'active','2026-04-28 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(39,3,9,'PX-PAC-0030','Packing Peanuts Biodegradable (Bag)','packing-peanuts-biodegradable-bag-29','Professional-grade Packing Peanuts Biodegradable (Bag) for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',950.00,25,800,0,'active','2026-04-24 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(40,1,9,'PX-PAC-0031','Shipping Label A4 Sticker (Pack 100)','shipping-label-a4-sticker-pack-100-30','Professional-grade Shipping Label A4 Sticker (Pack 100) for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',450.00,50,1700,0,'active','2026-04-03 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(41,2,9,'PX-PAC-0032','Edge Protector Cardboard (Pack 100)','edge-protector-cardboard-pack-100-31','Professional-grade Edge Protector Cardboard (Pack 100) for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',780.00,30,900,0,'active','2026-03-07 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(42,3,9,'PX-PAC-0033','Poly Mailer Bag 10x13 (Pack 100)','poly-mailer-bag-10x13-pack-100-32','Professional-grade Poly Mailer Bag 10x13 (Pack 100) for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',350.00,100,3100,0,'active','2026-04-14 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(43,1,9,'PX-PAC-0034','Strapping Band PP 12mm (Roll)','strapping-band-pp-12mm-roll-33','Professional-grade Strapping Band PP 12mm (Roll) for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',1680.00,12,252,0,'active','2026-04-08 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(44,2,9,'PX-PAC-0035','Carton Sealer Machine Manual','carton-sealer-machine-manual-34','Professional-grade Carton Sealer Machine Manual for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',4500.00,8,200,0,'active','2026-04-30 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(45,3,9,'PX-PAC-0036','Void Fill Paper Roll 500m','void-fill-paper-roll-500m-35','Professional-grade Void Fill Paper Roll 500m for B2B wholesale. Ideal for Packaging & Shipping Materials applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',2200.00,10,430,0,'active','2026-05-02 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(46,1,10,'PX-ITC-0037','Wireless Mouse Ergonomic','wireless-mouse-ergonomic-36','Professional-grade Wireless Mouse Ergonomic for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',650.00,50,1850,0,'active','2026-04-25 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(47,2,10,'PX-ITC-0038','Mechanical Keyboard RGB','mechanical-keyboard-rgb-37','Professional-grade Mechanical Keyboard RGB for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',2800.00,20,940,0,'active','2026-03-28 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(48,3,10,'PX-ITC-0039','USB-C Hub 7-in-1 Adapter','usb-c-hub-7-in-1-adapter-38','Professional-grade USB-C Hub 7-in-1 Adapter for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',1200.00,40,1520,0,'active','2026-04-02 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(49,1,10,'PX-ITC-0040','Webcam HD 1080p AutoFocus','webcam-hd-1080p-autofocus-39','Professional-grade Webcam HD 1080p AutoFocus for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',1800.00,25,1200,0,'active','2026-03-20 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(50,2,10,'PX-ITC-0041','Laptop Stand Adjustable Aluminum','laptop-stand-adjustable-aluminum-40','Professional-grade Laptop Stand Adjustable Aluminum for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',950.00,35,1365,0,'active','2026-04-13 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(51,3,10,'PX-ITC-0042','Monitor Stand Riser Mesh Metal','monitor-stand-riser-mesh-metal-41','Professional-grade Monitor Stand Riser Mesh Metal for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',750.00,40,1600,0,'active','2026-04-16 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(52,1,10,'PX-ITC-0043','Cable Management Box Set','cable-management-box-set-42','Professional-grade Cable Management Box Set for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',450.00,60,1380,0,'active','2026-03-30 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(53,2,10,'PX-ITC-0044','HDMI Cable 4K 3ft (Pack 10)','hdmi-cable-4k-3ft-pack-10-43','Professional-grade HDMI Cable 4K 3ft (Pack 10) for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',1200.00,25,1250,0,'active','2026-03-07 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(54,3,10,'PX-ITC-0045','Desk Pad Large Leather','desk-pad-large-leather-44','Professional-grade Desk Pad Large Leather for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',850.00,30,390,0,'active','2026-03-10 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(55,1,10,'PX-ITC-0046','Headset Noise Cancelling USB','headset-noise-cancelling-usb-45','Professional-grade Headset Noise Cancelling USB for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',2200.00,20,280,0,'active','2026-04-19 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(56,2,10,'PX-ITC-0047','Power Strip Surge Protector 8-Outlet','power-strip-surge-protector-8-outlet-46','Professional-grade Power Strip Surge Protector 8-Outlet for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',680.00,45,585,0,'active','2026-04-01 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(57,3,10,'PX-ITC-0048','Laptop Sleeve 15.6-inch Felt','laptop-sleeve-156-inch-felt-47','Professional-grade Laptop Sleeve 15.6-inch Felt for B2B wholesale. Ideal for IT & Computer Accessories applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',380.00,80,4000,0,'active','2026-03-07 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(58,1,11,'PX-CLE-0049','Floor Mop Industrial 24-inch','floor-mop-industrial-24-inch-48','Professional-grade Floor Mop Industrial 24-inch for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',450.00,40,1280,0,'active','2026-04-16 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(59,2,11,'PX-CLE-0050','Disinfectant Liquid 5L Concentrate','disinfectant-liquid-5l-concentrate-49','Professional-grade Disinfectant Liquid 5L Concentrate for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',650.00,30,750,0,'active','2026-05-01 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(60,3,11,'PX-CLE-0051','Glass Cleaner Spray 500ml','glass-cleaner-spray-500ml-50','Professional-grade Glass Cleaner Spray 500ml for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',180.00,100,4400,0,'active','2026-04-30 01:05:27','2026-05-04 14:54:35','2026-05-05 01:05:27',NULL),(61,1,11,'PX-CLE-0052','Trash Bag Heavy Duty 33gal (Roll)','trash-bag-heavy-duty-33gal-roll-51','Professional-grade Trash Bag Heavy Duty 33gal (Roll) for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',280.00,150,3600,0,'active','2026-04-26 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(62,2,11,'PX-CLE-0053','Microfiber Cleaning Cloth (Pack 50)','microfiber-cleaning-cloth-pack-50-52','Professional-grade Microfiber Cleaning Cloth (Pack 50) for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',320.00,80,2640,0,'active','2026-03-26 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(63,3,11,'PX-CLE-0054','Toilet Paper Roll 2-Ply (Pack 48)','toilet-paper-roll-2-ply-pack-48-53','Professional-grade Toilet Paper Roll 2-Ply (Pack 48) for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',580.00,50,2150,0,'active','2026-03-14 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(64,1,11,'PX-CLE-0055','Hand Soap Dispenser Refill 5L','hand-soap-dispenser-refill-5l-54','Professional-grade Hand Soap Dispenser Refill 5L for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',450.00,40,1720,0,'active','2026-03-16 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(65,2,11,'PX-CLE-0056','Air Freshener Automatic Dispenser','air-freshener-automatic-dispenser-55','Professional-grade Air Freshener Automatic Dispenser for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',850.00,25,950,0,'active','2026-03-30 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(66,3,11,'PX-CLE-0057','Vacuum Cleaner Industrial Wet/Dry','vacuum-cleaner-industrial-wetdry-56','Professional-grade Vacuum Cleaner Industrial Wet/Dry for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',12000.00,5,55,0,'active','2026-04-02 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(67,1,11,'PX-CLE-0058','Scrub Brush Heavy Duty Plastic','scrub-brush-heavy-duty-plastic-57','Professional-grade Scrub Brush Heavy Duty Plastic for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',120.00,200,7800,0,'active','2026-03-24 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(68,2,11,'PX-CLE-0059','Paper Towel Roll Center-Pull (Case)','paper-towel-roll-center-pull-case-58','Professional-grade Paper Towel Roll Center-Pull (Case) for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',1200.00,20,720,0,'active','2026-04-03 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(69,3,11,'PX-CLE-0060','Bleach Chlorine 5L Commercial','bleach-chlorine-5l-commercial-59','Professional-grade Bleach Chlorine 5L Commercial for B2B wholesale. Ideal for Cleaning & Maintenance applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',380.00,60,2460,0,'active','2026-04-07 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(70,1,12,'PX-IND-0061','Cordless Drill Driver 20V','cordless-drill-driver-20v-60','Professional-grade Cordless Drill Driver 20V for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',6500.00,12,540,0,'active','2026-04-09 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(71,2,12,'PX-IND-0062','Angle Grinder 4-1/2 inch 800W','angle-grinder-4-12-inch-800w-61','Professional-grade Angle Grinder 4-1/2 inch 800W for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',3200.00,15,255,0,'active','2026-04-16 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(72,3,12,'PX-IND-0063','Wrench Set Chrome 24-Piece','wrench-set-chrome-24-piece-62','Professional-grade Wrench Set Chrome 24-Piece for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',2800.00,20,580,0,'active','2026-03-30 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(73,1,12,'PX-IND-0064','Screwdriver Set Precision 6-Piece','screwdriver-set-precision-6-piece-63','Professional-grade Screwdriver Set Precision 6-Piece for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',450.00,50,2000,0,'active','2026-03-12 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(74,2,12,'PX-IND-0065','Tool Box Metal 24-inch with Tray','tool-box-metal-24-inch-with-tray-64','Professional-grade Tool Box Metal 24-inch with Tray for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',2200.00,15,720,0,'active','2026-04-01 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(75,3,12,'PX-IND-0066','Measuring Tape Steel 25ft (Pack 10)','measuring-tape-steel-25ft-pack-10-65','Professional-grade Measuring Tape Steel 25ft (Pack 10) for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',1200.00,30,1230,0,'active','2026-03-30 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(76,1,12,'PX-IND-0067','Level Spirit Magnetic 12-inch','level-spirit-magnetic-12-inch-66','Professional-grade Level Spirit Magnetic 12-inch for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',650.00,40,1680,0,'active','2026-04-03 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(77,2,12,'PX-IND-0068','Pliers Set 3-Piece Heavy Duty','pliers-set-3-piece-heavy-duty-67','Professional-grade Pliers Set 3-Piece Heavy Duty for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',950.00,35,1575,0,'active','2026-03-16 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(78,3,12,'PX-IND-0069','Hammer Claw Fiberglass 16oz','hammer-claw-fiberglass-16oz-68','Professional-grade Hammer Claw Fiberglass 16oz for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',380.00,60,1440,0,'active','2026-04-13 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(79,1,12,'PX-IND-0070','Cable Cutter Heavy Duty 10-inch','cable-cutter-heavy-duty-10-inch-69','Professional-grade Cable Cutter Heavy Duty 10-inch for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',850.00,30,510,0,'active','2026-05-01 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(80,2,12,'PX-IND-0071','Torque Wrench 1/2-inch Drive','torque-wrench-12-inch-drive-70','Professional-grade Torque Wrench 1/2-inch Drive for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',3800.00,12,288,0,'active','2026-04-27 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(81,3,12,'PX-IND-0072','Multimeter Digital Auto-Ranging','multimeter-digital-auto-ranging-71','Professional-grade Multimeter Digital Auto-Ranging for B2B wholesale. Ideal for Industrial Tools & Equipment applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',1800.00,25,750,0,'active','2026-03-14 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(82,1,13,'PX-FUR-0073','Ergonomic Office Chair Mesh Back','ergonomic-office-chair-mesh-back-72','Professional-grade Ergonomic Office Chair Mesh Back for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',8500.00,10,370,0,'active','2026-04-07 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(83,2,13,'PX-FUR-0074','Standing Desk Electric Adjustable','standing-desk-electric-adjustable-73','Professional-grade Standing Desk Electric Adjustable for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',28000.00,5,95,0,'active','2026-03-19 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(84,3,13,'PX-FUR-0075','Filing Cabinet 4-Drawer Steel','filing-cabinet-4-drawer-steel-74','Professional-grade Filing Cabinet 4-Drawer Steel for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',12000.00,8,280,0,'active','2026-03-28 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(85,1,13,'PX-FUR-0076','Bookshelf 5-Tier Wood 72-inch','bookshelf-5-tier-wood-72-inch-75','Professional-grade Bookshelf 5-Tier Wood 72-inch for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',4500.00,12,552,0,'active','2026-04-15 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(86,2,13,'PX-FUR-0077','Conference Table 8ft Modern','conference-table-8ft-modern-76','Professional-grade Conference Table 8ft Modern for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',25000.00,3,81,0,'active','2026-05-02 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(87,3,13,'PX-FUR-0078','Visitor Chair Stackable Plastic','visitor-chair-stackable-plastic-77','Professional-grade Visitor Chair Stackable Plastic for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',1800.00,20,880,0,'active','2026-04-22 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(88,1,13,'PX-FUR-0079','Whiteboard Magnetic 48x36 Mobile','whiteboard-magnetic-48x36-mobile-78','Professional-grade Whiteboard Magnetic 48x36 Mobile for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',6500.00,10,230,0,'active','2026-04-20 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(89,2,13,'PX-FUR-0080','Partition Divider 72-inch Fabric','partition-divider-72-inch-fabric-79','Professional-grade Partition Divider 72-inch Fabric for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',4200.00,15,525,0,'active','2026-04-27 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(90,3,13,'PX-FUR-0081','Coat Rack Metal Freestanding','coat-rack-metal-freestanding-80','Professional-grade Coat Rack Metal Freestanding for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',850.00,25,575,0,'active','2026-04-08 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(91,1,13,'PX-FUR-0082','Shoe Rack Metal 4-Tier','shoe-rack-metal-4-tier-81','Professional-grade Shoe Rack Metal 4-Tier for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',650.00,40,1800,0,'active','2026-03-06 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(92,2,13,'PX-FUR-0083','Cabinet Lock Keyed Alike (Pack 10)','cabinet-lock-keyed-alike-pack-10-82','Professional-grade Cabinet Lock Keyed Alike (Pack 10) for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',450.00,50,1450,0,'active','2026-03-17 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(93,3,13,'PX-FUR-0084','Chair Mat Hard Floor 48x36','chair-mat-hard-floor-48x36-83','Professional-grade Chair Mat Hard Floor 48x36 for B2B wholesale. Ideal for Furniture & Fixtures applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',1200.00,30,690,0,'active','2026-04-08 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(94,1,14,'PX-ELE-0085','LED Bulb 9W Warm White (Pack 10)','led-bulb-9w-warm-white-pack-10-84','Professional-grade LED Bulb 9W Warm White (Pack 10) for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',680.00,50,1550,0,'active','2026-04-26 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(95,2,14,'PX-ELE-0086','LED Panel Light 2x2 40W','led-panel-light-2x2-40w-85','Professional-grade LED Panel Light 2x2 40W for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',2800.00,20,960,0,'active','2026-04-13 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(96,3,14,'PX-ELE-0087','Extension Cord 15m Heavy Duty','extension-cord-15m-heavy-duty-86','Professional-grade Extension Cord 15m Heavy Duty for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',850.00,35,1715,0,'active','2026-04-15 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(97,1,14,'PX-ELE-0088','Emergency Light LED Exit Sign','emergency-light-led-exit-sign-87','Professional-grade Emergency Light LED Exit Sign for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',1800.00,20,720,0,'active','2026-03-13 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(98,2,14,'PX-ELE-0089','Motion Sensor Light Indoor','motion-sensor-light-indoor-88','Professional-grade Motion Sensor Light Indoor for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',650.00,40,1160,0,'active','2026-03-18 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(99,3,14,'PX-ELE-0090','Work Light LED 50W Portable','work-light-led-50w-portable-89','Professional-grade Work Light LED 50W Portable for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',1200.00,25,725,0,'active','2026-03-28 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(100,1,14,'PX-ELE-0091','Cable Tie Nylon 8-inch (Pack 1000)','cable-tie-nylon-8-inch-pack-1000-90','Professional-grade Cable Tie Nylon 8-inch (Pack 1000) for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',380.00,80,1520,0,'active','2026-04-28 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(101,2,14,'PX-ELE-0092','Wall Socket 3-Pin Universal','wall-socket-3-pin-universal-91','Professional-grade Wall Socket 3-Pin Universal for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',120.00,200,4800,0,'active','2026-04-19 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(102,3,14,'PX-ELE-0093','MCB Circuit Breaker 32A','mcb-circuit-breaker-32a-92','Professional-grade MCB Circuit Breaker 32A for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',450.00,50,2350,0,'active','2026-03-20 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(103,1,14,'PX-ELE-0094','LED Strip Light 5m RGB','led-strip-light-5m-rgb-93','Professional-grade LED Strip Light 5m RGB for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',950.00,30,1500,0,'active','2026-03-17 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(104,2,14,'PX-ELE-0095','Voltage Stabilizer 1000VA','voltage-stabilizer-1000va-94','Professional-grade Voltage Stabilizer 1000VA for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',4500.00,15,360,0,'active','2026-03-15 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(105,3,14,'PX-ELE-0096','Solar Flood Light 100W','solar-flood-light-100w-95','Professional-grade Solar Flood Light 100W for B2B wholesale. Ideal for Electrical & Lighting applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',6500.00,10,430,0,'active','2026-04-20 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(106,1,15,'PX-WAR-0097','Pallet Rack Upright Frame 16ft','pallet-rack-upright-frame-16ft-96','Professional-grade Pallet Rack Upright Frame 16ft for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',8500.00,12,132,0,'active','2026-04-15 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(107,2,15,'PX-WAR-0098','Wire Shelf 48x18 Chrome','wire-shelf-48x18-chrome-97','Professional-grade Wire Shelf 48x18 Chrome for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',2800.00,20,860,0,'active','2026-03-17 01:05:28','2026-05-04 14:54:35','2026-05-05 01:05:28',NULL),(108,3,15,'PX-WAR-0099','Storage Bin Stackable 50L','storage-bin-stackable-50l-98','Professional-grade Storage Bin Stackable 50L for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',650.00,50,1950,0,'active','2026-04-01 01:05:28','2026-05-04 14:54:36','2026-05-05 01:05:28',NULL),(109,1,15,'PX-WAR-0100','Hand Truck Dolly 300kg Capacity','hand-truck-dolly-300kg-capacity-99','Professional-grade Hand Truck Dolly 300kg Capacity for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',3200.00,15,390,0,'active','2026-05-04 01:05:28','2026-05-04 14:54:36','2026-05-05 01:05:28',NULL),(110,2,15,'PX-WAR-0101','Platform Trolley Foldable 150kg','platform-trolley-foldable-150kg-100','Professional-grade Platform Trolley Foldable 150kg for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',4500.00,12,516,0,'active','2026-04-23 01:05:28','2026-05-04 14:54:36','2026-05-05 01:05:28',NULL),(111,3,15,'PX-WAR-0102','Barcode Scanner USB Handheld','barcode-scanner-usb-handheld-101','Professional-grade Barcode Scanner USB Handheld for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',2800.00,20,920,0,'active','2026-05-01 01:05:28','2026-05-04 14:54:36','2026-05-05 01:05:28',NULL),(112,1,15,'PX-WAR-0103','Label Printer Thermal 4-inch','label-printer-thermal-4-inch-102','Professional-grade Label Printer Thermal 4-inch for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses 24-month warranty included with all purchases Contact us for volume pricing and customization options.',8500.00,10,150,0,'active','2026-04-01 01:05:28','2026-05-04 14:54:36','2026-05-05 01:05:28',NULL),(113,2,15,'PX-WAR-0104','Safety Mirror Convex 18-inch','safety-mirror-convex-18-inch-103','Professional-grade Safety Mirror Convex 18-inch for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',1800.00,20,980,0,'active','2026-03-22 01:05:28','2026-05-04 14:54:36','2026-05-05 01:05:28',NULL),(114,3,15,'PX-WAR-0105','Dock Plate Aluminum 1500lb','dock-plate-aluminum-1500lb-104','Professional-grade Dock Plate Aluminum 1500lb for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',12000.00,5,165,0,'active','2026-03-12 01:05:28','2026-05-04 14:54:36','2026-05-05 01:05:28',NULL),(115,1,15,'PX-WAR-0106','Pallet Jack Manual 5500lb','pallet-jack-manual-5500lb-105','Professional-grade Pallet Jack Manual 5500lb for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',18000.00,3,117,0,'active','2026-04-04 01:05:28','2026-05-04 14:54:36','2026-05-05 01:05:28',NULL),(116,2,15,'PX-WAR-0107','Stretch Wrap Dispenser','stretch-wrap-dispenser-106','Professional-grade Stretch Wrap Dispenser for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Contact us for volume pricing and customization options.',850.00,30,600,0,'active','2026-04-08 01:05:28','2026-05-04 14:54:36','2026-05-05 01:05:28',NULL),(117,3,15,'PX-WAR-0108','Inventory Tag Numbered (Pack 500)','inventory-tag-numbered-pack-500-107','Professional-grade Inventory Tag Numbered (Pack 500) for B2B wholesale. Ideal for Warehouse & Storage applications. Premium quality materials ensure long-lasting durability Manufactured to meet international safety standards Bulk pricing available for enterprise orders Fast shipping from local warehouses Contact us for volume pricing and customization options.',450.00,50,1100,0,'active','2026-05-04 01:05:28','2026-05-04 14:54:36','2026-05-05 01:05:28',NULL),(118,5,16,'PX-LOG-101','Industrial Dock Plate 1500lb','industrial-dock-plate-1500lb','Heavy-duty dock plate for warehouse loading and supplier fulfillment.',12000.00,5,60,0,'active','2026-05-05 04:34:36','2026-05-05 01:17:17','2026-05-05 04:34:36',NULL),(119,2,17,'PX-OFF-102','Wholesale Filing Cabinet Pro','wholesale-filing-cabinet-pro','Bulk-ready office storage furniture for corporate procurement.',8500.00,3,40,0,'active','2026-05-05 04:34:36','2026-05-05 01:17:17','2026-05-05 04:34:36',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rfq_items`
--

DROP TABLE IF EXISTS `rfq_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rfq_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rfq_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL,
  `target_price` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rfq_items_rfq_id_foreign` (`rfq_id`),
  KEY `rfq_items_product_id_foreign` (`product_id`),
  CONSTRAINT `rfq_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rfq_items_rfq_id_foreign` FOREIGN KEY (`rfq_id`) REFERENCES `rfqs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rfq_items`
--

LOCK TABLES `rfq_items` WRITE;
/*!40000 ALTER TABLE `rfq_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `rfq_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rfqs`
--

DROP TABLE IF EXISTS `rfqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rfqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `buyer_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `rfq_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `message` text COLLATE utf8mb4_unicode_ci,
  `needed_by` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rfqs_rfq_number_unique` (`rfq_number`),
  KEY `rfqs_buyer_id_foreign` (`buyer_id`),
  KEY `rfqs_supplier_id_foreign` (`supplier_id`),
  KEY `rfqs_status_index` (`status`),
  KEY `rfqs_needed_by_index` (`needed_by`),
  CONSTRAINT `rfqs_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `rfqs_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rfqs`
--

LOCK TABLES `rfqs` WRITE;
/*!40000 ALTER TABLE `rfqs` DISABLE KEYS */;
/*!40000 ALTER TABLE `rfqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(1,2),(13,2),(14,2),(16,2),(1,3),(15,3),(16,3),(1,4),(7,4),(8,4),(9,4),(10,4),(17,4),(1,5),(9,5),(10,5);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(2,'supplier','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(3,'buyer','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(4,'marketing_manager','web','2026-05-04 14:54:33','2026-05-04 14:54:33'),(5,'workflow_manager','web','2026-05-04 14:54:33','2026-05-04 14:54:33');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_accounts`
--

DROP TABLE IF EXISTS `social_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `handle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `credentials_json` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_accounts_platform_index` (`platform`),
  KEY `social_accounts_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_accounts`
--

LOCK TABLES `social_accounts` WRITE;
/*!40000 ALTER TABLE `social_accounts` DISABLE KEYS */;
INSERT INTO `social_accounts` VALUES (1,'facebook','PlexusBiz Facebook','@plexusbiz','active','eyJpdiI6ImJONzd3a0x1R3hJaGg3UmRaRmErRFE9PSIsInZhbHVlIjoiOURlc3FxUTUzYW5hcXhOUC9VS0NVQT09IiwibWFjIjoiNmYzMjVkNjdlMzE2OTE1ZTRmMThlYmFlMzE0YTg1YmQ2YjE0ZGI3YWFiODFmMjQ3NjFjZmMwMDRkMWFlOGZmNyIsInRhZyI6IiJ9','2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(2,'instagram','PlexusBiz Instagram','@plexusbiz','active','eyJpdiI6InZxTVgyK1lRelorZW9ra0psNzFVRlE9PSIsInZhbHVlIjoielVIN3lzV0g1M2hIUk9xRUFoTW1hdz09IiwibWFjIjoiNWI1Y2FjMTlkY2E3MTFhNzI5Nzc3OTJiNTYxZjkxNmEzMjUxOGMzNGU2NzU5YzEzYTM4MGI2ZWUxYzZlZGI5MSIsInRhZyI6IiJ9','2026-05-04 14:54:33','2026-05-04 14:54:33',NULL);
/*!40000 ALTER TABLE `social_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_posts`
--

DROP TABLE IF EXISTS `social_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned DEFAULT NULL,
  `social_account_id` bigint unsigned DEFAULT NULL,
  `platform` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_meta` json DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `external_post_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `likes_count` int unsigned NOT NULL DEFAULT '0',
  `comments_count` int unsigned NOT NULL DEFAULT '0',
  `shares_count` int unsigned NOT NULL DEFAULT '0',
  `reach_count` int unsigned NOT NULL DEFAULT '0',
  `clicks_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_posts_campaign_id_foreign` (`campaign_id`),
  KEY `social_posts_social_account_id_foreign` (`social_account_id`),
  KEY `social_posts_platform_status_scheduled_at_index` (`platform`,`status`,`scheduled_at`),
  KEY `social_posts_platform_index` (`platform`),
  KEY `social_posts_scheduled_at_index` (`scheduled_at`),
  KEY `social_posts_status_index` (`status`),
  KEY `social_posts_published_at_index` (`published_at`),
  CONSTRAINT `social_posts_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL,
  CONSTRAINT `social_posts_social_account_id_foreign` FOREIGN KEY (`social_account_id`) REFERENCES `social_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_posts`
--

LOCK TABLES `social_posts` WRITE;
/*!40000 ALTER TABLE `social_posts` DISABLE KEYS */;
INSERT INTO `social_posts` VALUES (1,NULL,1,'facebook','Priority B2B supply workflows are live.',NULL,NULL,'2026-05-05 14:54:33','scheduled',NULL,NULL,NULL,0,0,0,0,0,'2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(2,NULL,2,'instagram','Automated supplier operations for modern teams.',NULL,NULL,'2026-05-06 14:54:33','scheduled',NULL,NULL,NULL,0,0,0,0,0,'2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(3,NULL,NULL,'facebook','🚀 Exciting news! We have expanded our B2B catalog with 50+ new industrial products. Check out our latest offerings! #B2B #Wholesale',NULL,NULL,'2026-05-02 14:54:34','published',NULL,'2026-05-02 14:54:34',NULL,58,7,0,0,0,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(4,NULL,NULL,'instagram','Behind the scenes at our partner facilities. Quality control is our priority. #QualityFirst #B2B',NULL,NULL,'2026-05-05 14:54:34','scheduled',NULL,NULL,NULL,0,0,0,0,0,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(5,NULL,NULL,'facebook','Summer bulk pricing now available! Save up to 20% on orders over 100 units. Contact us today! 💼',NULL,NULL,'2026-05-07 14:54:34','draft',NULL,NULL,NULL,0,0,0,0,0,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL);
/*!40000 ALTER TABLE `social_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_notifications`
--

DROP TABLE IF EXISTS `supplier_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `support_ticket_id` bigint unsigned DEFAULT NULL,
  `type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload_json` json DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_notifications_support_ticket_id_foreign` (`support_ticket_id`),
  KEY `supplier_notifications_supplier_id_read_at_index` (`supplier_id`,`read_at`),
  KEY `supplier_notifications_type_index` (`type`),
  KEY `supplier_notifications_read_at_index` (`read_at`),
  CONSTRAINT `supplier_notifications_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_notifications_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_notifications`
--

LOCK TABLES `supplier_notifications` WRITE;
/*!40000 ALTER TABLE `supplier_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_orders`
--

DROP TABLE IF EXISTS `supplier_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `supplier_order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BDT',
  `placed_at` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_orders_supplier_order_number_unique` (`supplier_order_number`),
  KEY `supplier_orders_order_id_supplier_id_index` (`order_id`,`supplier_id`),
  KEY `supplier_orders_supplier_id_status_index` (`supplier_id`,`status`),
  KEY `supplier_orders_status_index` (`status`),
  KEY `supplier_orders_placed_at_index` (`placed_at`),
  CONSTRAINT `supplier_orders_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_orders`
--

LOCK TABLES `supplier_orders` WRITE;
/*!40000 ALTER TABLE `supplier_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` json DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_user_id_unique` (`user_id`),
  UNIQUE KEY `suppliers_slug_unique` (`slug`),
  KEY `suppliers_approved_by_foreign` (`approved_by`),
  KEY `suppliers_status_index` (`status`),
  CONSTRAINT `suppliers_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `suppliers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,2,NULL,'Plexus Industrial Supply','plexus-industrial-supply','approved','supplier@plexus.test','+8801700000000',NULL,'{\"city\": \"Dhaka\", \"line_1\": \"House 12, Road 8\", \"country\": \"Bangladesh\"}','2026-05-05 04:34:36','2026-05-04 14:54:33','2026-05-05 04:34:36',NULL),(2,9,NULL,'Dhaka Tools & Equipment','dhaka-tools-equipment','approved','supplier2@dhakatools.test','+8801712345601',NULL,'{\"city\": \"Dhaka\", \"line_1\": \"Sector 3, Road 14\", \"country\": \"Bangladesh\"}','2026-05-05 04:34:36','2026-05-04 14:54:34','2026-05-05 04:34:36',NULL),(3,10,NULL,'BD Textile Mills','bd-textile-mills','approved','supplier3@bdtex.test','+8801712345602',NULL,'{\"city\": \"Gazipur\", \"line_1\": \"Plot 18, Industrial Area\", \"country\": \"Bangladesh\"}','2026-05-05 04:34:36','2026-05-04 14:54:34','2026-05-05 04:34:36',NULL),(4,11,NULL,'New Supplier Inc','new-supplier-inc','pending','pending@supplier.test','+8801712345678',NULL,NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(5,12,NULL,'Assignment Demo Supplier','assignment-demo-supplier','approved','supplier@example.com','+8801700000099',NULL,'{\"city\": \"Dhaka\", \"line_1\": \"Demo Supplier Office\", \"country\": \"Bangladesh\"}','2026-05-05 04:34:36','2026-05-05 04:34:36','2026-05-05 04:34:36',NULL);
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_faqs`
--

DROP TABLE IF EXISTS `support_faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_faqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords_json` json DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `priority` smallint unsigned NOT NULL DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_faqs_status_index` (`status`),
  KEY `support_faqs_priority_index` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_faqs`
--

LOCK TABLES `support_faqs` WRITE;
/*!40000 ALTER TABLE `support_faqs` DISABLE KEYS */;
INSERT INTO `support_faqs` VALUES (1,'How can I check order shipping status?','Your order shipping status is available from the Orders workspace. If the supplier has not updated tracking yet, a support ticket will notify the supplier.','[\"shipping\", \"shipment\", \"tracking\", \"delivery\", \"eta\", \"order status\"]','active',10,'2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(2,'How do I request a supplier quote?','Open the marketplace product and submit an RFQ with quantity, target price, and delivery notes. The supplier will respond from their order workspace.','[\"rfq\", \"quote\", \"bulk price\", \"supplier quote\", \"request quote\"]','active',20,'2026-05-04 14:54:33','2026-05-04 14:54:33',NULL),(3,'What is the minimum order quantity (MOQ)?','MOQ varies by product. Each product page displays the specific minimum order quantity. Bulk pricing tiers are available for larger quantities.','[\"moq\", \"minimum order\", \"bulk\", \"quantity\"]','active',39,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(4,'How do I become a verified supplier?','Register as a supplier, complete your company profile, and submit for admin approval. Our team will review and approve within 2-3 business days.','[\"supplier\", \"verified\", \"approval\", \"become supplier\"]','active',42,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(5,'What payment methods are accepted?','We accept bank transfers, credit cards, and mobile banking (bKash, Nagad). Net-30 terms available for approved enterprise customers.','[\"payment\", \"pay\", \"bkash\", \"bank transfer\", \"credit card\"]','active',14,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(6,'How can I track my order?','Once your order ships, you will receive a tracking number via email. You can also view order status in your buyer dashboard.','[\"track\", \"tracking\", \"order status\", \"where is my order\"]','active',21,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL);
/*!40000 ALTER TABLE `support_faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_messages`
--

DROP TABLE IF EXISTS `support_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint unsigned NOT NULL,
  `sender_id` bigint unsigned DEFAULT NULL,
  `sender_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibility` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_messages_sender_id_foreign` (`sender_id`),
  KEY `support_messages_support_ticket_id_created_at_index` (`support_ticket_id`,`created_at`),
  KEY `support_messages_sender_type_index` (`sender_type`),
  KEY `support_messages_visibility_index` (`visibility`),
  CONSTRAINT `support_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_messages_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_messages`
--

LOCK TABLES `support_messages` WRITE;
/*!40000 ALTER TABLE `support_messages` DISABLE KEYS */;
INSERT INTO `support_messages` VALUES (1,1,3,'customer','public','I need help with this issue. Please assist.',NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(2,2,3,'customer','public','I need help with this issue. Please assist.',NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34');
/*!40000 ALTER TABLE `support_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requester_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `channel` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `tags_json` json DEFAULT NULL,
  `metadata_json` json DEFAULT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  KEY `support_tickets_order_id_foreign` (`order_id`),
  KEY `support_tickets_customer_id_foreign` (`customer_id`),
  KEY `support_tickets_assigned_to_foreign` (`assigned_to`),
  KEY `support_tickets_supplier_id_status_index` (`supplier_id`,`status`),
  KEY `support_tickets_requester_id_status_index` (`requester_id`,`status`),
  KEY `support_tickets_channel_index` (`channel`),
  KEY `support_tickets_priority_index` (`priority`),
  KEY `support_tickets_status_index` (`status`),
  KEY `support_tickets_last_message_at_index` (`last_message_at`),
  CONSTRAINT `support_tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
INSERT INTO `support_tickets` VALUES (1,'TKT-2026-001',3,NULL,NULL,NULL,1,'web','Order delivery delay inquiry','Sample ticket for demo purposes','normal','resolved',NULL,NULL,NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL),(2,'TKT-2026-002',3,NULL,NULL,NULL,1,'web','Product quality question','Sample ticket for demo purposes','high','open',NULL,NULL,NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34',NULL);
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin User','admin@plexus.test','active','2026-05-04 14:54:33','$2y$10$OY2Ci1APfjh.vgxlrvQALu9NUUnp877WzogKUzXagsGLnqLWFVXmm','sT5cRUNZTIvknqrtSbtLo38GfL3Q0nvsOeRrFy8vXDuq0DuhhH2KKLF8xHj2','2026-05-05 00:57:40','2026-05-04 14:54:33','2026-05-05 00:57:40'),(2,'Supplier User','supplier@plexus.test','active','2026-05-04 14:54:33','$2y$10$yOS72BNhDY0oh4/rg63OBewFwtWSJ.HiMB8WLXvjEtv7oukIFYC4y',NULL,NULL,'2026-05-04 14:54:33','2026-05-04 14:54:33'),(3,'Buyer User','buyer@plexus.test','active','2026-05-04 14:54:33','$2y$10$me1HQXOi8QmcPppVN6bwJ.VBWQjd2GBskAaia/68hqkmZRMadF8eK',NULL,NULL,'2026-05-04 14:54:33','2026-05-04 14:54:33'),(4,'Marketing Manager','marketing@plexus.test','active','2026-05-04 14:54:33','$2y$10$F8X9B2tfMlyxPui409r1V.e7f.QSKGMKdteLMbD2qVLr1YPzfRcqu',NULL,NULL,'2026-05-04 14:54:33','2026-05-04 14:54:33'),(5,'Workflow Manager','workflow@plexus.test','active','2026-05-04 14:54:33','$2y$10$q7nkw5XjuoD9YAG2KXC.SuNyVZjineBibtnn3ra21pTFShmnxJ6fe',NULL,NULL,'2026-05-04 14:54:33','2026-05-04 14:54:33'),(6,'Acme Corp Buyer','buyer2@acme.test','active','2026-05-04 14:54:34','$2y$10$dU7lmuTc4ZC9cuBQqjMvZezdwvKwqTq2BXFyPp7S6rIllTd4YuVPm',NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(7,'Global Trade Ltd','buyer3@global.test','active','2026-05-04 14:54:34','$2y$10$pgDefCkdgfZpMm.20M..TOgaSV/N4JLr6ta5FHkIz/OIrqyYaCTj2',NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(8,'Metro Industries','buyer4@metro.test','active','2026-05-04 14:54:34','$2y$10$pAatY4jrSOJ02tHOsly2xOHYb0ZSW8P.Q4iiHhePcX.rxwW6RYAca',NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(9,'Dhaka Tools Ltd','supplier2@dhakatools.test','active','2026-05-04 14:54:34','$2y$10$7snLuX6p4iJ.2AzDRPs8Su65pIkqNWE2CR2miAcNku731CsYoKGd.',NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(10,'Bangladesh Textiles','supplier3@bdtex.test','active','2026-05-04 14:54:34','$2y$10$D.wfJTgHCGYIfpu8i4awOemCi/pLLWwZaVAKgoXbRf1lXKw9v3cP.',NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(11,'New Supplier Inc','pending@supplier.test','active','2026-05-04 14:54:34','$2y$10$dtClpI7l8WixnEJorMHjruniUR2jNvGuRh7XaZExTyXXfQZzkptja',NULL,NULL,'2026-05-04 14:54:34','2026-05-04 14:54:34'),(12,'Demo Supplier','supplier@example.com','active','2026-05-05 04:34:36','$2y$10$Vd4tMcA95jD0wzUbJvkP2.hGCtjYUIBQq7oJIdfWA7jNGq/mP3gzG',NULL,NULL,'2026-05-05 04:34:36','2026-05-05 04:34:36'),(13,'Demo Admin','admin@example.com','active','2026-05-05 04:34:36','$2y$10$J6LjObj8hI0mlCgl4YN3HuEqFNlO4XQu0.9nqjyCOqC/rI/sqQoky',NULL,NULL,'2026-05-05 04:34:36','2026-05-05 04:34:36'),(14,'Demo Buyer','buyer@example.com','active','2026-05-05 04:34:36','$2y$10$BDARyWvCGw.QhkVobdJnpOBxHOiqIHM6HGXvwiB7Eptlk6Kl1k6iS',NULL,NULL,'2026-05-05 04:34:36','2026-05-05 04:34:36'),(15,'Demo Marketing Manager','marketing@example.com','active','2026-05-05 04:34:36','$2y$10$sksx6WNKcsfYxpyjJ4h73el6eZLnWouy9tGCaMMhXbOO5CLcXVRH.',NULL,NULL,'2026-05-05 04:34:36','2026-05-05 04:34:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflow_logs`
--

DROP TABLE IF EXISTS `workflow_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workflow_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` bigint unsigned DEFAULT NULL,
  `trigger_event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `result` json DEFAULT NULL,
  `error` text COLLATE utf8mb4_unicode_ci,
  `executed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_logs_rule_id_status_index` (`rule_id`,`status`),
  KEY `workflow_logs_trigger_event_index` (`trigger_event`),
  KEY `workflow_logs_status_index` (`status`),
  KEY `workflow_logs_executed_at_index` (`executed_at`),
  CONSTRAINT `workflow_logs_rule_id_foreign` FOREIGN KEY (`rule_id`) REFERENCES `automation_rules` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflow_logs`
--

LOCK TABLES `workflow_logs` WRITE;
/*!40000 ALTER TABLE `workflow_logs` DISABLE KEYS */;
INSERT INTO `workflow_logs` VALUES (1,1,'order.placed','{\"demo\": true, \"order_id\": 10}','success','{\"actions\": 2, \"executed\": true}',NULL,'2026-05-03 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34'),(2,2,'ticket.created','{\"demo\": true, \"order_id\": 4}','success','{\"actions\": 2, \"executed\": true}',NULL,'2026-04-30 14:54:34','2026-05-04 14:54:34','2026-05-04 14:54:34');
/*!40000 ALTER TABLE `workflow_logs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-05 16:34:44
