"""Tiny SQLite order store. No ORM needed at this scale."""

import sqlite3
import time
import uuid
import os

DB_PATH = os.environ.get("ORDERS_DB_PATH", "orders.db")


def _connect():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db():
    conn = _connect()
    conn.execute(
        """
        CREATE TABLE IF NOT EXISTS orders (
            id TEXT PRIMARY KEY,
            product_id TEXT NOT NULL,
            product_name TEXT NOT NULL,
            currency TEXT NOT NULL,
            amount REAL NOT NULL,
            customer_name TEXT,
            customer_email TEXT,
            provider TEXT NOT NULL,
            provider_ref TEXT,
            status TEXT NOT NULL DEFAULT 'pending',
            created_at REAL NOT NULL,
            paid_at REAL
        )
        """
    )
    conn.commit()
    conn.close()


def create_order(product, currency, amount, provider, customer_name, customer_email):
    order_id = uuid.uuid4().hex
    conn = _connect()
    conn.execute(
        """
        INSERT INTO orders
            (id, product_id, product_name, currency, amount, customer_name,
             customer_email, provider, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)
        """,
        (
            order_id,
            product["id"],
            product["name"],
            currency,
            amount,
            customer_name,
            customer_email,
            provider,
            time.time(),
        ),
    )
    conn.commit()
    conn.close()
    return order_id


def get_order(order_id):
    conn = _connect()
    row = conn.execute("SELECT * FROM orders WHERE id = ?", (order_id,)).fetchone()
    conn.close()
    return dict(row) if row else None


def mark_paid(order_id, provider_ref=None):
    """Marks an order paid. Returns True the first time it transitions to
    paid (so callers can notify exactly once), False if it was already paid
    or the order doesn't exist."""
    conn = _connect()
    row = conn.execute("SELECT status FROM orders WHERE id = ?", (order_id,)).fetchone()
    if row is None or row["status"] == "paid":
        conn.close()
        return False
    conn.execute(
        "UPDATE orders SET status = 'paid', paid_at = ?, provider_ref = ? WHERE id = ?",
        (time.time(), provider_ref, order_id),
    )
    conn.commit()
    conn.close()
    return True


def mark_failed(order_id):
    conn = _connect()
    conn.execute(
        "UPDATE orders SET status = 'failed' WHERE id = ? AND status != 'paid'",
        (order_id,),
    )
    conn.commit()
    conn.close()


def list_orders(limit=200):
    conn = _connect()
    rows = conn.execute(
        "SELECT * FROM orders ORDER BY created_at DESC LIMIT ?", (limit,)
    ).fetchall()
    conn.close()
    return [dict(row) for row in rows]
