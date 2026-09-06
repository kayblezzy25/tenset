import os
import hmac
import logging

from dotenv import load_dotenv

load_dotenv()

from flask import (
    Flask,
    render_template,
    request,
    redirect,
    url_for,
    flash,
    abort,
)

from products import PRODUCTS, get_product
import db
from payments import flutterwave, nowpayments
import notify

logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s", level=logging.INFO
)
logger = logging.getLogger(__name__)

app = Flask(__name__)
app.secret_key = os.environ.get("FLASK_SECRET_KEY", "dev-secret-change-me")

SITE_BASE_URL = os.environ.get("SITE_BASE_URL", "http://localhost:5000").rstrip("/")
FLUTTERWAVE_WEBHOOK_HASH = os.environ.get("FLUTTERWAVE_WEBHOOK_HASH")
ADMIN_TOKEN = os.environ.get("ADMIN_TOKEN")

db.init_db()


@app.route("/")
def index():
    return render_template("index.html", products=PRODUCTS)


@app.route("/checkout/<product_id>")
def checkout(product_id):
    product = get_product(product_id)
    if not product:
        abort(404)
    return render_template("checkout.html", product=product)


@app.route("/checkout/<product_id>", methods=["POST"])
def checkout_submit(product_id):
    product = get_product(product_id)
    if not product:
        abort(404)

    name = request.form.get("name", "").strip()
    email = request.form.get("email", "").strip()
    currency = request.form.get("currency")  # "USD", "NGN", or "BTC"

    if not email:
        flash("Please enter your email so we can reach you about your order.")
        return redirect(url_for("checkout", product_id=product_id))

    if currency == "USD":
        amount = product["price_usd"]
        order_id = db.create_order(product, "USD", amount, "flutterwave", name, email)
        order = db.get_order(order_id)
        try:
            link = flutterwave.create_payment_link(
                order, redirect_url=f"{SITE_BASE_URL}/payment/flutterwave/callback"
            )
        except Exception:
            logger.exception("Flutterwave USD payment link creation failed")
            flash("Sorry, we couldn't start the payment. Please try again shortly.")
            return redirect(url_for("checkout", product_id=product_id))
        return redirect(link)

    if currency == "NGN":
        amount = product["price_ngn"]
        order_id = db.create_order(product, "NGN", amount, "flutterwave", name, email)
        order = db.get_order(order_id)
        try:
            link = flutterwave.create_payment_link(
                order, redirect_url=f"{SITE_BASE_URL}/payment/flutterwave/callback"
            )
        except Exception:
            logger.exception("Flutterwave NGN payment link creation failed")
            flash("Sorry, we couldn't start the payment. Please try again shortly.")
            return redirect(url_for("checkout", product_id=product_id))
        return redirect(link)

    if currency == "BTC":
        amount = product["price_usd"]  # BTC invoice priced in USD, paid in BTC
        order_id = db.create_order(product, "BTC", amount, "nowpayments", name, email)
        order = db.get_order(order_id)
        try:
            invoice_url = nowpayments.create_invoice(
                order,
                ipn_callback_url=f"{SITE_BASE_URL}/webhook/nowpayments",
                success_url=f"{SITE_BASE_URL}/payment/nowpayments/success",
                cancel_url=f"{SITE_BASE_URL}/payment/nowpayments/cancel",
            )
        except Exception:
            logger.exception("NOWPayments invoice creation failed")
            flash("Sorry, we couldn't start the crypto payment. Please try again shortly.")
            return redirect(url_for("checkout", product_id=product_id))
        return redirect(invoice_url)

    flash("Please choose a payment method.")
    return redirect(url_for("checkout", product_id=product_id))


@app.route("/payment/flutterwave/callback")
def flutterwave_callback():
    tx_ref = request.args.get("tx_ref")
    transaction_id = request.args.get("transaction_id")
    status = request.args.get("status")

    order = db.get_order(tx_ref) if tx_ref else None
    if not order:
        return render_template("cancel.html")

    if status == "successful" and transaction_id:
        try:
            verified = flutterwave.verify_transaction(transaction_id)
            if (
                verified.get("status") == "successful"
                and float(verified.get("amount", 0)) >= float(order["amount"])
                and verified.get("currency") == order["currency"]
            ):
                if db.mark_paid(order["id"], provider_ref=str(transaction_id)):
                    notify.notify_payment_received(db.get_order(order["id"]))
                return render_template("success.html", order=db.get_order(order["id"]))
        except Exception:
            logger.exception("Flutterwave callback verification failed")

    return render_template("cancel.html")


@app.route("/webhook/flutterwave", methods=["POST"])
def flutterwave_webhook():
    signature = request.headers.get("verif-hash")
    if not FLUTTERWAVE_WEBHOOK_HASH or not signature or not hmac.compare_digest(
        signature, FLUTTERWAVE_WEBHOOK_HASH
    ):
        abort(401)

    payload = request.get_json(silent=True) or {}
    data = payload.get("data", {})
    tx_ref = data.get("tx_ref")
    transaction_id = data.get("id")

    order = db.get_order(tx_ref) if tx_ref else None
    if not order:
        return "", 200

    try:
        verified = flutterwave.verify_transaction(transaction_id)
        if (
            verified.get("status") == "successful"
            and float(verified.get("amount", 0)) >= float(order["amount"])
            and verified.get("currency") == order["currency"]
        ):
            if db.mark_paid(order["id"], provider_ref=str(transaction_id)):
                notify.notify_payment_received(db.get_order(order["id"]))
    except Exception:
        logger.exception("Flutterwave webhook verification failed")

    return "", 200


@app.route("/webhook/nowpayments", methods=["POST"])
def nowpayments_webhook():
    signature = request.headers.get("x-nowpayments-sig")
    payload = request.get_json(silent=True) or {}

    if not nowpayments.verify_ipn_signature(payload, signature):
        abort(401)

    order_id = payload.get("order_id")
    status = payload.get("payment_status")
    order = db.get_order(order_id) if order_id else None
    if not order:
        return "", 200

    if status in ("finished", "confirmed"):
        if db.mark_paid(order["id"], provider_ref=str(payload.get("payment_id"))):
            notify.notify_payment_received(db.get_order(order["id"]))
    elif status in ("failed", "expired", "refunded"):
        db.mark_failed(order["id"])

    return "", 200


@app.route("/payment/nowpayments/success")
def nowpayments_success():
    return render_template("success.html", order=None, pending_crypto=True)


@app.route("/payment/nowpayments/cancel")
def nowpayments_cancel():
    return render_template("cancel.html")


@app.route("/admin/orders")
def admin_orders():
    token = request.args.get("token", "")
    if not ADMIN_TOKEN or not hmac.compare_digest(token, ADMIN_TOKEN):
        abort(403)
    return render_template("admin_orders.html", orders=db.list_orders())


if __name__ == "__main__":
    app.run(debug=True)
