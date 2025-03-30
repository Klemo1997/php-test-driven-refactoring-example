DROP TABLE IF EXISTS invoices;
CREATE TABLE invoices (
    id INTEGER PRIMARY KEY,
    amount REAL NOT NULL,
    currency VARCHAR NOT NULL,
    exchange_rate REAL NOT NULL,
    issued_on INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);