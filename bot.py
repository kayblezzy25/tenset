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
# Replace this URL with your preferred high-end welcome graphic or logo
WELCOME_IMAGE_URL = "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1000&auto=format&fit=crop"

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
    """Handles the /start command by sending a welcome image with a caption and buttons."""
    user = update.effective_user
    welcome_text = (
        f"Hello {user.first_name}! 👋\n\n"
        "I am your automated helper assistant. Click any button below to read our frequently asked questions."
    )
    
    # Send photo with text as the caption
    await update.message.reply_photo(
        photo=WELCOME_IMAGE_URL,
        caption=welcome_text,
        reply_markup=get_faq_keyboard()
    )

async def on_new_chat_member(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Detects when a new user joins a group and welcomes them with the image and FAQ menu."""
    result = update.chat_member.new_chat_member
    if result.status == "member":
        user = result.user
        chat_title = update.effective_chat.title
        
        # Escaping special characters for MarkdownV2 text
