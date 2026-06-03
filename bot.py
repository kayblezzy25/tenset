import os
import asyncio
import logging
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import (
    ApplicationBuilder,
    CommandHandler,
    ContextTypes,
    CallbackQueryHandler,
    ChatMemberHandler,
)

# Enable structured logging
logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s", level=logging.INFO
)
logger = logging.getLogger(__name__)

# --- CONFIGURATION / FAQ DATA ---
FAQ_DATA = {
    "faq_1": {
        "button": "🚀 Getting Started",
        "text": "Welcome to the group! To get started, make sure to read our pinned rules and introduce yourself in the main chat channel."
    },
    "faq_2": {
        "button": "📚 Rules & Guidelines",
        "text": "1. Be respectful to all members.\n2. No spamming or unsolicited self-promotion.\n3. Keep topics relevant to the channel theme."
    },
    "faq_3": {
        "button": "🛠️ Support & Contact",
        "text": "Need help? You can reach out to our official support team or open a ticket by messaging @YourSupportHandleBot."
    }
}

def get_faq_keyboard():
    keyboard = []
    for key, data in FAQ_DATA.items():
        keyboard.append([InlineKeyboardButton(data["button"], callback_data=key)])
    return InlineKeyboardMarkup(keyboard)

# --- GLOBAL BOT HANDLERS ---

async def start_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    user = update.effective_user
    welcome_text = (
        f"Hello {user.first_name}! 👋\n\n"
        "I am your automated helper assistant. Click any button below to read our frequently asked questions."
    )
    await update.message.reply_text(text=welcome_text, reply_markup=get_faq_keyboard())

async def on_new_chat_member(update: Update, context: ContextTypes.DEFAULT_TYPE):
    result = update.chat_member.new_chat_member
    if result.status == "member":
        user = result.user
        chat_title = update.effective_chat.title
        welcome_text = (
            f"Welcome to **{chat_title}**, {user.mention_markdown_v2()}\! 🎉\n\n"
            "We are thrilled to have you here\. Please use the menu below to review our common questions\:"
        )
        await context.bot.send_message(
            chat_id=update.effective_chat.id,
            text=welcome_text,
            parse_mode="MarkdownV2",
            reply_markup=get_faq_keyboard()
        )

async def handle_faq_click(update: Update, context: ContextTypes.DEFAULT_TYPE):
    query = update.callback_query
    await query.answer()

    faq_key = query.data
    if faq_key in FAQ_DATA:
        faq_answer = FAQ_DATA[faq_key]["text"]
        button_title = FAQ_DATA[faq_key]["button"]
        response_text = f"**{button_title}**\n\n{faq_answer}"
        
        await query.edit_message_text(
            text=response_text,
            parse_mode="Markdown",
            reply_markup=get_faq_keyboard()
        )

# --- ASYNC MULTI-BOT RUNNER ---

async def main():
    # Dynamic Discovery: Loops through environment variables to find tokens dynamically
    tokens = []
    for key, value in os.environ.items():
        if key.startswith("TELEGRAM_BOT_TOKEN_") and value.strip():
            tokens.append((key, value.strip()))

    if not tokens:
        logger.error("CRITICAL: No environment variables found matching 'TELEGRAM_BOT_TOKEN_X'!")
        return

    logger.info(f"Found {len(tokens)} bot tokens in environment configuration. Initializing...")

    apps = []

    # Initialize and attach handlers to every single bot instance
    for env_name, token in tokens:
        logger.info(f"Setting up bot instance linked to environment key: {env_name}")
        
        app = ApplicationBuilder().token(token).build()
        
        # Attach the FAQ logic components to this specific bot configuration
        app.add_handler(CommandHandler("start", start_command))
        app.add_handler(ChatMemberHandler(on_new_chat_member, ChatMemberHandler.CHAT_MEMBER))
        app.add_handler(CallbackQueryHandler(handle_faq_click))
        
        # Initialize and start polling connection manually inside the async loop
        await app.initialize()
        await app.start()
        await app.updater.start_polling(allowed_updates=Update.ALL_TYPES)
        
        apps.append(app)

    logger.info("All 10+ bots are actively long-polling Telegram server queues simultaneously!")

    # Keep the main asyncio execution frame alive indefinitely so the background worker doesn't exit
    try:
        while True:
            await asyncio.sleep(3600)
    except (KeyboardInterrupt, SystemExit):
        logger.info("Termination signal caught. Executing graceful bot cluster shutdown...")
        for app in apps:
            await app.updater.stop()
            await app.stop()
            await app.shutdown()

if __name__ == "__main__":
    # Drive the application via python-telegram-bot's loop configuration rules
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        pass
