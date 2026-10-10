USE fibin_food;
CREATE TABLE IF NOT EXISTS ngos (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(180) NOT NULL, email VARCHAR(190) NULL, phone VARCHAR(30) NULL,
 city VARCHAR(120) NULL, address VARCHAR(255) NULL, description TEXT NULL, status ENUM('pending','verified','suspended') NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS volunteers (
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL UNIQUE, availability ENUM('available','busy','offline') NOT NULL DEFAULT 'available',
 service_area VARCHAR(180) NULL, skills VARCHAR(255) NULL, hours DECIMAL(10,1) NOT NULL DEFAULT 0, verified TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS campaigns (
 id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180) NOT NULL, description TEXT NULL, target_meals INT NOT NULL DEFAULT 0, raised_meals INT NOT NULL DEFAULT 0,
 location VARCHAR(180) NULL, start_date DATE NULL, end_date DATE NULL, status ENUM('draft','active','completed','paused') NOT NULL DEFAULT 'draft', created_by INT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS deliveries (
 id INT AUTO_INCREMENT PRIMARY KEY, food_id INT NOT NULL, volunteer_id INT NULL, ngo_id INT NULL, pickup_address VARCHAR(255) NULL, delivery_address VARCHAR(255) NULL,
 status ENUM('assigned','pickup','picked_up','on_the_way','delivered','cancelled') NOT NULL DEFAULT 'assigned', otp VARCHAR(8) NULL, pickup_at DATETIME NULL, delivered_at DATETIME NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(food_id) REFERENCES food_donations(id) ON DELETE CASCADE, FOREIGN KEY(volunteer_id) REFERENCES volunteers(id) ON DELETE SET NULL, FOREIGN KEY(ngo_id) REFERENCES ngos(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS reports (
 id INT AUTO_INCREMENT PRIMARY KEY, reporter_id INT NULL, target_type VARCHAR(40) NOT NULL, target_id INT NULL, reason VARCHAR(180) NOT NULL, details TEXT NULL,
 status ENUM('open','reviewing','resolved','dismissed') NOT NULL DEFAULT 'open', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(reporter_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS community_groups (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(160) NOT NULL, description TEXT NULL, created_by INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS community_events (
 id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180) NOT NULL, description TEXT NULL, location VARCHAR(180) NULL, starts_at DATETIME NULL, created_by INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
INSERT INTO ngos(name,email,city,description,status) SELECT 'Sevanya Foundation','contact@sevanyafoundation.site.je','India','Foundation network partner profile. Verify collaboration before using official partnership claims.','pending' WHERE NOT EXISTS(SELECT 1 FROM ngos WHERE name='Sevanya Foundation');
INSERT INTO campaigns(title,description,target_meals,raised_meals,location,start_date,end_date,status) SELECT 'FIBIN Community Food Rescue','A community campaign to rescue safe surplus meals and connect them with local organizations.',1000,0,'Local community',CURDATE(),DATE_ADD(CURDATE(),INTERVAL 30 DAY),'active' WHERE NOT EXISTS(SELECT 1 FROM campaigns WHERE title='FIBIN Community Food Rescue');
