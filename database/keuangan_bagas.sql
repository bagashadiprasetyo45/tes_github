CREATE DATABASE IF NOT EXISTS keuangan_bagas
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE keuangan_bagas;

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transactions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(15,2) UNSIGNED NOT NULL,
    category VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_transactions_user_date (user_id, transaction_date),
    KEY idx_transactions_user_type (user_id, type),
    KEY idx_transactions_user_category (user_id, category),
    CONSTRAINT fk_transactions_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS income_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_income_categories_user_name (user_id, name),
    CONSTRAINT fk_income_categories_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS expense_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_expense_categories_user_name (user_id, name),
    CONSTRAINT fk_expense_categories_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS recurring_transactions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(15,2) UNSIGNED NOT NULL,
    category VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    frequency ENUM('Harian', 'Mingguan', 'Bulanan', 'Tahunan') NOT NULL,
    next_date DATE NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_recurring_user_active_date (user_id, active, next_date),
    CONSTRAINT fk_recurring_transactions_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS budgets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(15,2) UNSIGNED NOT NULL,
    month TINYINT UNSIGNED NOT NULL,
    year SMALLINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_budgets_user_category_period (user_id, category, month, year),
    KEY idx_budgets_user_period (user_id, year, month),
    CONSTRAINT fk_budgets_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS savings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    target_amount DECIMAL(15,2) UNSIGNED NOT NULL,
    current_amount DECIMAL(15,2) UNSIGNED NOT NULL DEFAULT 0.00,
    deadline DATE NOT NULL,
    description VARCHAR(500) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_savings_user_deadline (user_id, deadline),
    CONSTRAINT fk_savings_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS savings_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    saving_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) UNSIGNED NOT NULL,
    type ENUM('tambah', 'kurang') NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    `date` DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_savings_history_saving_date (saving_id, `date`),
    CONSTRAINT fk_savings_history_saving
        FOREIGN KEY (saving_id) REFERENCES savings (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS financial_goals (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    target_amount DECIMAL(15,2) UNSIGNED NOT NULL,
    current_amount DECIMAL(15,2) UNSIGNED NOT NULL DEFAULT 0.00,
    deadline DATE NOT NULL,
    category VARCHAR(100) NOT NULL,
    status ENUM('Aktif', 'Selesai', 'Ditunda') NOT NULL DEFAULT 'Aktif',
    description VARCHAR(500) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_financial_goals_user_status_deadline (user_id, status, deadline),
    CONSTRAINT fk_financial_goals_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS debts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    total_amount DECIMAL(15,2) UNSIGNED NOT NULL,
    paid_amount DECIMAL(15,2) UNSIGNED NOT NULL DEFAULT 0.00,
    due_date DATE NOT NULL,
    description VARCHAR(500) NOT NULL DEFAULT '',
    status ENUM('Belum Lunas', 'Sebagian', 'Lunas') NOT NULL DEFAULT 'Belum Lunas',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_debts_user_status_due_date (user_id, status, due_date),
    CONSTRAINT fk_debts_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS debt_payments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    debt_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) UNSIGNED NOT NULL,
    payment_date DATE NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_debt_payments_debt_date (debt_id, payment_date),
    CONSTRAINT fk_debt_payments_debt
        FOREIGN KEY (debt_id) REFERENCES debts (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS receivables (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    total_amount DECIMAL(15,2) UNSIGNED NOT NULL,
    paid_amount DECIMAL(15,2) UNSIGNED NOT NULL DEFAULT 0.00,
    due_date DATE NOT NULL,
    description VARCHAR(500) NOT NULL DEFAULT '',
    status ENUM('Belum Lunas', 'Sebagian', 'Lunas') NOT NULL DEFAULT 'Belum Lunas',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_receivables_user_status_due_date (user_id, status, due_date),
    CONSTRAINT fk_receivables_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS receivable_payments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    receivable_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) UNSIGNED NOT NULL,
    payment_date DATE NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_receivable_payments_receivable_date (receivable_id, payment_date),
    CONSTRAINT fk_receivable_payments_receivable
        FOREIGN KEY (receivable_id) REFERENCES receivables (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bills (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    amount DECIMAL(15,2) UNSIGNED NOT NULL,
    due_date DATE NOT NULL,
    category VARCHAR(100) NOT NULL,
    recurring TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('Belum Dibayar', 'Sudah Dibayar', 'Terlambat') NOT NULL DEFAULT 'Belum Dibayar',
    description VARCHAR(500) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_bills_user_status_due_date (user_id, status, due_date),
    CONSTRAINT fk_bills_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reminders (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    description VARCHAR(500) NOT NULL DEFAULT '',
    reminder_date DATE NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'Umum',
    status VARCHAR(30) NOT NULL DEFAULT 'Aktif',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reminders_user_date_status (user_id, reminder_date, status),
    CONSTRAINT fk_reminders_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (username, password, name)
VALUES (
    'bagas',
    '$2y$10$ioE6dfJ63HnEA02vdxd6/OJgjTXgB2R79VmSZKl9uVMbN/iayksVu',
    'Bagas'
)
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT IGNORE INTO income_categories (user_id, name)
SELECT id, category_name
FROM users
JOIN (
    SELECT 'Gaji' AS category_name
    UNION ALL SELECT 'Freelance'
    UNION ALL SELECT 'Bisnis'
    UNION ALL SELECT 'Bonus'
    UNION ALL SELECT 'Komisi'
    UNION ALL SELECT 'Penjualan'
    UNION ALL SELECT 'Investasi'
    UNION ALL SELECT 'Hadiah'
    UNION ALL SELECT 'Pemasukan Lainnya'
) AS defaults ON users.username = 'bagas';

INSERT IGNORE INTO expense_categories (user_id, name)
SELECT id, category_name
FROM users
JOIN (
    SELECT 'Makanan' AS category_name
    UNION ALL SELECT 'Minuman'
    UNION ALL SELECT 'Transportasi'
    UNION ALL SELECT 'Bensin'
    UNION ALL SELECT 'Parkir'
    UNION ALL SELECT 'Tagihan'
    UNION ALL SELECT 'Belanja'
    UNION ALL SELECT 'Hiburan'
    UNION ALL SELECT 'Gaming'
    UNION ALL SELECT 'Kesehatan'
    UNION ALL SELECT 'Pendidikan'
    UNION ALL SELECT 'Rumah Tangga'
    UNION ALL SELECT 'Pulsa/Internet'
    UNION ALL SELECT 'Fashion'
    UNION ALL SELECT 'Nongkrong'
    UNION ALL SELECT 'Travel'
    UNION ALL SELECT 'Pengeluaran Lainnya'
) AS defaults ON users.username = 'bagas';

-- DATA DUMMY OPSIONAL:
-- Tambahkan INSERT transaksi, budget, tabungan, atau target di bawah ini
-- bila ingin mengisi contoh data setelah proses import selesai.
