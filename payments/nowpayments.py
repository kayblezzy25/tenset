"""
NOWPayments integration -- handles Bitcoin (and other crypto) payments via
a hosted invoice page.

Docs: https://documenter.getpostman.com/view/7907941/S1a32n38
"""

import os
import hmac
import hashlib
import json
import logging

import requests

logger = logging.getLogger(__name__)

BASE_URL = "https://api.nowpayments.io/v1"
API_KEY = os.environ.get("NOWPAYMENTS_API_KEY")
IPN_SECRET = os.environ.get("NOWPAYMENTS_IPN_SECRET")


def _headers():
    return {"x-api-key": API_KEY, "Content-Type": "application/json"}


def create_invoice(order, ipn_callback_url, success_url, cancel_url):
    """Creates a hosted invoice and returns its URL."""
    payload = {
        "price_amount": order["amount"],
        "price_currency": "usd",
        "pay_currency": "btc",
        "order_id": order["id"],
        "order_description": order["product_name"],
        "ipn_callback_url": ipn_callback_url,
        "success_url": success_url,
        "cancel_url": cancel_url,
    }
    resp = requests.post(f"{BASE_URL}/invoice", json=payload, headers=_headers(), timeout=15)
    resp.raise_for_status()
    return resp.json()["invoice_url"]


def verify_ipn_signature(raw_body_dict, signature_header):
    """NOWPayments signs the IPN body with HMAC-SHA512 over the JSON of the
    payload with keys sorted alphabetically."""
    if not IPN_SECRET or not signature_header:
        return False
    sorted_payload = json.dumps(raw_body_dict, sort_keys=True, separators=(",", ":"))
    computed = hmac.new(
        IPN_SECRET.encode("utf-8"), sorted_payload.encode("utf-8"), hashlib.sha512
    ).hexdigest()
    return hmac.compare_digest(computed, signature_header)
