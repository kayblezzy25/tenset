"""Sends you a notification the instant a payment is confirmed."""

import os
import logging
import smtplib
from email.mime.text import MIMEText

import requests

logger = logging.getLogger(__name__)

TELEGRAM_NOTIFY_BOT_TOKEN = os.environ.get("TELEGRAM_NOTIFY_BOT_TOKEN")
TELEGRAM_NOTIFY_CHAT_ID = os.environ.get("TELEGRAM_NOTIFY_CHAT_ID")

SMTP_HOST = os.environ.get("SMTP_HOST")
SMTP_PORT = int(os.environ.get("SMTP_PORT", "587"))
SMTP_USER = os.environ.get("SMTP_USER")
SMTP_PASS = os.environ.get("SMTP_PASS")
NOTIFY_EMAIL_TO = os.environ.get("NOTIFY_EMAIL_TO")


def _send_telegram(text):
    if not TELEGRAM_NOTIFY_BOT_TOKEN or not TELEGRAM_NOTIFY_CHAT_ID:
        return
    url = f"https://api.telegram.org/bot{TELEGRAM_NOTIFY_BOT_TOKEN}/sendMessage"
    try:
        resp = requests.post(
            url,
            json={
                "chat_id": TELEGRAM_NOTIFY_CHAT_ID,
                "text": text,
                "parse_mode": "Markdown",
            },
            timeout=10,
        )
        if not resp.ok:
            logger.error("Telegram notify failed: %s %s", resp.status_code, resp.text)
    except requests.RequestException:
        logger.exception("Telegram notify request failed")


def _send_email(subject, body):
    if not (SMTP_HOST and SMTP_USER and SMTP_PASS and NOTIFY_EMAIL_TO):
        return
    msg = MIMEText(body)
    msg["Subject"] = subject
    msg["From"] = SMTP_USER
    msg["To"] = NOTIFY_EMAIL_TO
    try:
        with smtplib.SMTP(SMTP_HOST, SMTP_PORT) as server:
            server.starttls()
            server.login(SMTP_USER, SMTP_PASS)
            server.sendmail(SMTP_USER, [NOTIFY_EMAIL_TO], msg.as_string())
    except Exception:
        logger.exception("Email notify failed")


def notify_payment_received(order):
    text = (
        f"💰 *New payment received!*\n\n"
        f"Product: {order['product_name']}\n"
        f"Amount: {order['amount']} {order['currency']}\n"
        f"Buyer: {order.get('customer_name') or 'N/A'} "
        f"({order.get('customer_email') or 'no email'})\n"
        f"Provider: {order['provider']} (ref: {order.get('provider_ref') or 'N/A'})\n"
        f"Order ID: {order['id']}"
    )
    _send_telegram(text)
    _send_email(
        subject=f"New payment: {order['product_name']} ({order['amount']} {order['currency']})",
        body=text,
    )
