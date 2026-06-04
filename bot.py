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

# --- CONFIGURATION / GLOBAL ASSETS ---
# Replace this direct link with your hosted MP4 file
WELCOME_VIDEO_URL = "https://www.w3schools.com/html/mov_bbb.mp4" 

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
    """Handles the /start command by sending an autoplaying video with an FAQ caption."""
    user = update.effective_user
    welcome_text = (
        f"Hello {user.first_name}! 👋\n\n"
        "I am your automated helper assistant. Click any button below to read our frequently asked questions."
    )
    
    # Send the video stream directly. Captions sit seamlessly beneath the player.
    await update.message.reply_video(
        video=WELCOME_VIDEO_URL,
        caption=welcome_text,
        reply_markup=get_faq_keyboard()
    )

async def on_new_chat_member(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Detects new chat joins and triggers the automated video presentation frame."""
    result = update.chat_member.new_chat_member
    if result.status == "member":
        user = result.user
        chat_title = update.effective_chat.title
        
        # Escape markdown special tokens accurately 
        welcome_text = (
            f"Welcome to **{chat_title}**, {user.mention_markdown_v2()}\! 🎉\n\n"
            "We are thrilled to have you here\. Please use the menu below to review our common questions\:"
        )
        
        await context.bot.send_video(
            chat_id=update.effective_chat.id,
            video=WELCOME_VIDEO_URL,
            caption=welcome_text,
            parse_mode="MarkdownV2",
            reply_markup=get_faq_keyboard()
        )

async def handle_faq_click(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Intercepts inline callback clicks and replaces the caption area cleanly."""
    query = update.callback_query
    await query.answer()

    faq_key = query.data
    if faq_key in FAQ_DATA:
        faq_answer = FAQ_DATA[faq_key]["text"]
        button_title = FAQ_DATA[faq_key]["button"]
        response_text = f"**{button_title}**\n\n{faq_answer}"
        
        # We target edit_message_caption to keep the active video window playing at the top
        await query.edit_message_caption(
            caption=response_text,
            parse_mode="Markdown",
            reply_markup=get_faq_keyboard()
        )

# --- ASYNC MULTI-BOT RUNNER ---

async def main():
    tokens = []
    for key, value in os.environ.items():
        if key.startswith("TELEGRAM_BOT_TOKEN_") and value.strip():
            tokens.append((key, value.strip()))

    if not tokens:
        logger.error("CRITICAL: No environment variables found matching 'TELEGRAM_BOT_TOKEN_X'!")
        return

    logger.info(f"Found {len(tokens)} bot tokens in environment configuration. Initializing...")

    apps = []

    for env_name, token in tokens:
        logger.info(f"Setting up bot instance linked to environment key: {env_name}")
        
        app = ApplicationBuilder().token(token).build()
        
        app.add_handler(CommandHandler("start", start_command))
        app.add_handler(ChatMemberHandler(on_new_chat_member, ChatMemberHandler.CHAT_MEMBER))
        app.add_handler(CallbackQueryHandler(handle_faq_click))
        
        await app.initialize()
        await app.start()
        await app.updater.start_polling(allowed_updates=Update.ALL_TYPES)
        
        apps.append(app)

    logger.info("All 10+ bots are actively long-polling Telegram server queues with Video Support!")

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
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        pass
