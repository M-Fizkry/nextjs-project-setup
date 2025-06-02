-- Create database
CREATE DATABASE IF NOT EXISTS inventory_control;
USE inventory_control;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(36) PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'USER') NOT NULL DEFAULT 'USER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Materials table
CREATE TABLE IF NOT EXISTS materials (
    id VARCHAR(36) PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('RAW_MATERIAL', 'WORK_IN_PROGRESS', 'FINISHED_GOOD') NOT NULL,
    current_stock DECIMAL(10,2) DEFAULT 0,
    min_stock DECIMAL(10,2) DEFAULT 0,
    max_stock DECIMAL(10,2) DEFAULT 0,
    unit VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- BOM table
CREATE TABLE IF NOT EXISTS bom (
    id VARCHAR(36) PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- BOM Items table
CREATE TABLE IF NOT EXISTS bom_items (
    id VARCHAR(36) PRIMARY KEY,
    bom_id VARCHAR(36) NOT NULL,
    material_id VARCHAR(36) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bom_id) REFERENCES bom(id),
    FOREIGN KEY (material_id) REFERENCES materials(id)
);

-- Production Plans table
CREATE TABLE IF NOT EXISTS production_plans (
    id VARCHAR(36) PRIMARY KEY,
    plan_date DATE NOT NULL,
    number_mo VARCHAR(50) UNIQUE NOT NULL,
    item_number VARCHAR(50) NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    planning_qty DECIMAL(10,2) NOT NULL,
    actual_prod DECIMAL(10,2) DEFAULT 0,
    achievement DECIMAL(5,2) DEFAULT 0,
    plan_type ENUM('PLAN_1', 'PLAN_2', 'PLAN_3') NOT NULL,
    plan_code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Stock Movements table
CREATE TABLE IF NOT EXISTS stock_movements (
    id VARCHAR(36) PRIMARY KEY,
    material_id VARCHAR(36) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    type ENUM('IN', 'OUT') NOT NULL,
    date TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (material_id) REFERENCES materials(id)
);

-- System Settings table
CREATE TABLE IF NOT EXISTS system_settings (
    id VARCHAR(36) PRIMARY KEY,
    key_name VARCHAR(50) UNIQUE NOT NULL,
    value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (id, username, password, role) VALUES 
(UUID(), 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN');

-- Insert sample materials
INSERT INTO materials (id, code, name, type, current_stock, min_stock, max_stock, unit) VALUES
(UUID(), 'RM001', 'Raw Material 1', 'RAW_MATERIAL', 100, 50, 200, 'KG'),
(UUID(), 'RM002', 'Raw Material 2', 'RAW_MATERIAL', 150, 75, 300, 'KG'),
(UUID(), 'WIP001', 'Work in Progress 1', 'WORK_IN_PROGRESS', 50, 25, 100, 'PCS'),
(UUID(), 'FG001', 'Finished Good 1', 'FINISHED_GOOD', 75, 30, 150, 'PCS');

-- Insert default system settings
INSERT INTO system_settings (id, key_name, value) VALUES
(UUID(), 'system_title', 'Inventory Control System'),
(UUID(), 'system_language', 'id');
