USE vehicle_workshop_db;


-- =========================================
-- CREATE
-- =========================================

INSERT INTO vehicles
(company_id, vehicle_number, vehicle_type, chassis_number, date_received, deadline, status)
VALUES
(1, 'TN99BB7777', 'Car', 'CHASSIS7777', '2026-09-16', '2026-09-21', 'Pending');


-- =========================================
-- READ
-- =========================================

SELECT *
FROM vehicles
WHERE vehicle_number = 'TN99BB7777';


-- =========================================
-- UPDATE
-- =========================================

UPDATE vehicles
SET status = 'In Progress'
WHERE vehicle_number = 'TN99BB7777';


-- Check updated record

SELECT *
FROM vehicles
WHERE vehicle_number = 'TN99BB7777';


-- =========================================
-- DELETE
-- =========================================

DELETE FROM vehicles
WHERE vehicle_number = 'TN99BB7777';


-- Check after deletion

SELECT *
FROM vehicles
WHERE vehicle_number = 'TN99BB7777';