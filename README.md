# Skill payments site

A small Flask site for selling your skill(s)/service(s). Buyers can pay in:

- **Dollar (USD)** — card, via Flutterwave
- **Naira (NGN)** — card, bank transfer, or **USSD**, via Flutterwave
- **Bitcoin (BTC)** — via NOWPayments

You get an instant **Telegram** (and optionally email) notification the
moment a payment is confirmed.

This repo also still contains the original Telegram quiz bot (`bot.py`,
`questions.py`) — unrelated, left as-is, deployed as its own `worker`
service in `render.yaml`.

## 1. Edit your catalog

Open `products.py` and replace the placeholder with your real skill(s),
descriptions, and prices (USD and NGN).

## 2. Get your payment provider accounts

You need real accounts — these can't be created for you:

**Flutterwave** (USD + NGN + USSD): sign up at flutterwave.com, verify your
business, then:
- Grab your **Secret Key** from Settings → API (dashboard.flutterwave.com/settings/apis)
- Under Settings → Webhooks, set the webhook URL to
  `https://<your-domain>/webhook/flutterwave` and set a **Secret Hash**
  (any random string) — put the same string in `FLUTTERWAVE_WEBHOOK_HASH`
- Add your bank account(s) under Settings → Bank Accounts so USD/NGN
  settle where you want

**NOWPayments** (BTC): sign up at nowpayments.io, then:
- Grab your **API key** and set an **IPN secret** under Settings
- Add your BTC payout wallet address so received BTC forwards to you

## 3. Set up payment notifications

Recommended: Telegram.
1. Message [@BotFather](https://t.me/BotFather) on Telegram, `/newbot`, get a token
2. Send your new bot any message, then visit
   `https://api.telegram.org/bot<token>/getUpdates` to find your numeric chat id
3. Set `TELEGRAM_NOTIFY_BOT_TOKEN` and `TELEGRAM_NOTIFY_CHAT_ID`

Optional: also fill in the `SMTP_*` and `NOTIFY_EMAIL_TO` variables to get
an email per payment too.

## 4. Configure environment variables

Copy `.env.example` to `.env` for local testing, or set the same variables
in your host's dashboard (e.g. Render) for production. See that file for
what each variable does.

## 5. Run locally

```bash
pip install -r requirements.txt
python app.py
```

Visit http://localhost:5000. Note: webhooks need a public URL, so for local
end-to-end testing of a real payment, use a tunnel (e.g. `ngrok http 5000`)
and point `SITE_BASE_URL` and the provider webhook URLs at the tunnel URL.

## 6. Deploy

`render.yaml` already defines a `web` service (`skill-payments-site`) for
this site, alongside the existing quiz-bot `worker`. Push this repo to
Render (or any host that runs `gunicorn app:app`), then set the environment
variables from step 4 in that service's dashboard, and set
`SITE_BASE_URL` to the site's real public URL.

## Viewing orders

Visit `/admin/orders?token=<ADMIN_TOKEN>` (the token you set in step 4) to
see a simple table of all orders and their status.

## How payment confirmation works

- **Flutterwave**: after the buyer pays, they're redirected back to
  `/payment/flutterwave/callback`, and Flutterwave also POSTs a webhook to
  `/webhook/flutterwave`. Either path independently re-verifies the
  transaction directly with Flutterwave's API before marking it paid, so a
  buyer can't fake a successful payment by tampering with the redirect.
- **NOWPayments**: confirmation only happens via the signed IPN webhook to
  `/webhook/nowpayments`, since blockchain confirmations take time — the
  success page just tells the buyer their payment is being confirmed.

Either way, notifications only fire once per order (`db.mark_paid` is
idempotent), so you won't get duplicate pings if both the callback and the
webhook fire for the same order.
