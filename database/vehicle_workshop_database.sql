-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: vehicle_workshop_db
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `company_id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'Ashok Leyland','Ramesh Kumar','9876500001','ramesh@ashokleyland.com','Chennai'),(2,'Greenfield School','Priya Sharma','9876500002','priya@greenfieldschool.com','Poonamallee'),(3,'RMD School','Suresh Kumar','9876500003','suresh@rmdschool.com','Chennai'),(4,'City Hospital','Anitha Raj','9876500004','anitha@cityhospital.com','Avadi'),(5,'XYZ Travels','Mohan Raj','9876500005','mohan@xyztravels.com','Chennai');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `employee_id` int NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `job_role` varchar(50) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'Karthik Raj','9000000001','Mechanic','2023-06-10','Active'),(2,'Vijay Kumar','9000000002','Senior Mechanic','2022-04-15','Active'),(3,'Sanjay Ravi','9000000003','Supervisor','2021-01-20','Active'),(4,'Praveen Kumar','9000000004','Electrician','2024-02-12','Active'),(5,'Arun Raj','9000000005','Mechanic','2024-07-05','Active');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotations`
--

DROP TABLE IF EXISTS `quotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotations` (
  `quotation_id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `vehicle_id` int NOT NULL,
  `quotation_date` date DEFAULT NULL,
  `estimated_amount` decimal(10,2) DEFAULT NULL,
  `quotation_status` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`quotation_id`),
  KEY `company_id` (`company_id`),
  KEY `vehicle_id` (`vehicle_id`),
  CONSTRAINT `quotations_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
  CONSTRAINT `quotations_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotations`
--

LOCK TABLES `quotations` WRITE;
/*!40000 ALTER TABLE `quotations` DISABLE KEYS */;
INSERT INTO `quotations` VALUES (1,1,1,'2026-09-01',45000.00,'Approved'),(2,2,2,'2026-09-02',38000.00,'Pending'),(3,3,3,'2026-09-03',25000.00,'Approved'),(4,4,4,'2026-09-04',55000.00,'Approved'),(5,5,5,'2026-09-05',42000.00,'Pending');
/*!40000 ALTER TABLE `quotations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salaries`
--

DROP TABLE IF EXISTS `salaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salaries` (
  `salary_id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `salary_month` date DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `bonus` decimal(10,2) DEFAULT NULL,
  `deduction` decimal(10,2) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`salary_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `salaries_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salaries`
--

LOCK TABLES `salaries` WRITE;
/*!40000 ALTER TABLE `salaries` DISABLE KEYS */;
INSERT INTO `salaries` VALUES (1,1,'2026-09-01',25000.00,2000.00,500.00,26500.00,'Paid'),(2,2,'2026-09-01',30000.00,2500.00,500.00,32000.00,'Paid'),(3,3,'2026-09-01',35000.00,3000.00,1000.00,37000.00,'Paid'),(4,4,'2026-09-01',28000.00,1500.00,500.00,29000.00,'Pending'),(5,5,'2026-09-01',25000.00,2000.00,500.00,26500.00,'Pending');
/*!40000 ALTER TABLE `salaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `service_id` int NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Engine Service','Complete engine inspection and servicing'),(2,'Brake Repair','Brake inspection, repair and replacement'),(3,'Oil Change','Engine oil and oil filter replacement'),(4,'Electrical Repair','Inspection and repair of electrical systems'),(5,'AC Service','Air conditioning inspection and servicing');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `vehicle_id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `vehicle_number` varchar(20) NOT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `chassis_number` varchar(50) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`vehicle_id`),
  UNIQUE KEY `vehicle_number` (`vehicle_number`),
  UNIQUE KEY `chassis_number` (`chassis_number`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (1,1,'TN01AB1234','Truck','CHASSIS1001','2026-09-01','2026-09-05','Completed'),(2,2,'TN02CD2345','School Bus','CHASSIS1002','2026-09-02','2026-09-07','In Progress'),(3,3,'TN03EF3456','School Bus','CHASSIS1003','2026-09-03','2026-09-08','Completed'),(4,4,'TN04GH4567','Ambulance','CHASSIS1004','2026-09-04','2026-09-10','In Progress'),(5,5,'TN05IJ5678','Travel Bus','CHASSIS1005','2026-09-05','2026-09-12','Pending');
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_assignments`
--

DROP TABLE IF EXISTS `work_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_assignments` (
  `assignment_id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `service_id` int NOT NULL,
  `start_date` date DEFAULT NULL,
  `expected_end_date` date DEFAULT NULL,
  `actual_end_date` date DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`assignment_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `employee_id` (`employee_id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `work_assignments_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`),
  CONSTRAINT `work_assignments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  CONSTRAINT `work_assignments_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_assignments`
--

LOCK TABLES `work_assignments` WRITE;
/*!40000 ALTER TABLE `work_assignments` DISABLE KEYS */;
INSERT INTO `work_assignments` VALUES (1,1,1,1,'2026-09-01','2026-09-03','2026-09-03','Completed'),(2,2,2,2,'2026-09-02','2026-09-06',NULL,'In Progress'),(3,3,3,3,'2026-09-03','2026-09-05','2026-09-05','Completed'),(4,4,4,4,'2026-09-04','2026-09-08',NULL,'In Progress'),(5,5,5,5,'2026-09-05','2026-09-10',NULL,'Pending');
/*!40000 ALTER TABLE `work_assignments` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-13 12:14:40
