"""
Flutterwave integration -- handles USD and NGN card payments, and NGN
bank transfer / USSD (Flutterwave's hosted checkout offers USSD
automatically as a payment option for NGN charges, no extra code needed).

Docs: https://developer.flutterwave.com/docs/collecting-payments/standard
"""

import os
import logging

import requests

logger = logging.getLogger(__name__)

BASE_URL = "https://api.flutterwave.com/v3"
SECRET_KEY = os.environ.get("FLUTTERWAVE_SECRET_KEY")


def _headers():
    return {
        "Authorization": f"Bearer {SECRET_KEY}",
        "Content-Type": "application/json",
    }


def create_payment_link(order, redirect_url):
    """Creates a hosted Flutterwave checkout session and returns its URL."""
    payload = {
        "tx_ref": order["id"],
        "amount": order["amount"],
        "currency": order["currency"],
        "redirect_url": redirect_url,
        "customer": {
            "email": order.get("customer_email") or "buyer@example.com",
            "name": order.get("customer_name") or "Customer",
        },
        "customizations": {
            "title": order["product_name"],
        },
    }
    # USSD and bank transfer only make sense for NGN charges.
    if order["currency"] == "NGN":
        payload["payment_options"] = "card,banktransfer,ussd"

    resp = requests.post(f"{BASE_URL}/payments", json=payload, headers=_headers(), timeout=15)
    resp.raise_for_status()
    data = resp.json()
    return data["data"]["link"]


def verify_transaction(transaction_id):
    """Confirms a transaction's real status directly with Flutterwave
    (never trust the webhook/redirect payload alone)."""
    resp = requests.get(
        f"{BASE_URL}/transactions/{transaction_id}/verify",
        headers=_headers(),
        timeout=15,
    )
    resp.raise_for_status()
    return resp.json()["data"]
