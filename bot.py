import os
import asyncio
import logging
from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
from telegram.ext import (
    ApplicationBuilder,
    CommandHandler,
    ContextTypes,
    CallbackQueryHandler,
)
# Load the 50 questions dataset seamlessly
from questions import QUIZ_QUESTIONS

# Enable structured logging
logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s", level=logging.INFO
)
logger = logging.getLogger(__name__)

# Core asset config (Autoplay Media Engine link)
WELCOME_VIDEO_URL = "https://www.w3schools.com/html/mov_bbb.mp4" 

# Dynamic in-memory database to keep track of user progression cleanly across 10 bots
USER_QUIZ_STATES = {}

def get_quiz_keyboard(question_data):
    """Generates 3 clean, stacked selection buttons for the given question."""
    keyboard = []
    for idx, option in enumerate(question_data["options"]):
        keyboard.append([InlineKeyboardButton(option, callback_data=f"quiz_{idx}")])
    return InlineKeyboardMarkup(keyboard)

# --- BOT HANDLERS ---

async def start_command(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Initializes or resets a quiz session with an opening autoplay video window."""
    user_id = update.effective_user.id
    
    # Initialize state loop tracking parameters for this user
    USER_QUIZ_STATES[user_id] = {
        "current_index": 0,
        "answers": []
    }
    
    first_question = QUIZ_QUESTIONS[0]
    welcome_caption = (
        f"Welcome to the Master Challenge! 🏆\n\n"
        f"You will face {len(QUIZ_QUESTIONS)} questions. Answer carefully.\n\n"
        f"**Question 1:** {first_question['question']}"
    )
    
    await update.message.reply_video(
        video=WELCOME_VIDEO_URL,
        caption=welcome_caption,
        parse_mode="Markdown",
        reply_markup=get_quiz_keyboard(first_question)
    )

async def handle_quiz_selection(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Processes user multiple-choice selections and pushes them forward."""
    query = update.callback_query
    user_id = update.effective_user.id
    await query.answer()

    if user_id not in USER_QUIZ_STATES:
        await query.edit_message_caption(
            caption="⚠️ Your session timed out or expired. Please type /start to begin again!"
        )
        return

    state = USER_QUIZ_STATES[user_id]
    current_idx = state["current_index"]
    
    # Parse chosen option index out of the callback string data
    chosen_option = int(query.data.split("_")[1])
    state["answers"].append(chosen_option)
    
    next_idx = current_idx + 1
    state["current_index"] = next_idx

    # Scenario A: More questions remain in the queue
    if next_idx < len(QUIZ_QUESTIONS):
        next_question = QUIZ_QUESTIONS[next_idx]
        next_caption = (
            f"**Question {next_idx + 1} of {len(QUIZ_QUESTIONS)}**\n\n"
            f"{next_question['question']}"
        )
        await query.edit_message_caption(
            caption=next_caption,
            parse_mode="Markdown",
            reply_markup=get_quiz_keyboard(next_question)
        )
        
    # Scenario B: Quiz Finished - Compile scorecard readout details
    else:
        correct_count = 0
        summary_report = "📊 **QUIZ COMPLETED! YOUR FINAL REPORT**\n\n"
        
        for idx, q in enumerate(QUIZ_QUESTIONS):
            user_ans_idx = state["answers"][idx]
            correct_ans_idx = q["correct"]
            
            user_choice_txt = q["options"][user_ans_idx]
            correct_choice_txt = q["options"][correct_ans_idx]
            
            summary_report += f"❓ *Q{idx+1}: {q['question']}*\n"
            
            if user_ans_idx == correct_ans_idx:
                correct_count += 1
                summary_report += f"✅ Your Answer: {user_choice_txt}\n\n"
            else:
                summary_report += f"❌ Your Answer: {user_choice_txt}\n"
                summary_report += f"✨ Correct Answer: {correct_choice_txt}\n\n"
        
        final_score_header = f"🏆 **Final Score: {correct_count}/{len(QUIZ_QUESTIONS)}**\n\n"
        full_payload = final_score_header + summary_report
        
        # Clean up database entry to preserve memory stability on Render worker
        del USER_QUIZ_STATES[user_id]
        
        # If text is too long for a single caption (max 1024 chars), chunk it out nicely
        if len(full_payload) > 1000:
            await query.edit_message_caption(caption=f"🏁 **Finished!** Final Score: {correct_count}/{len(QUIZ_QUESTIONS)}. See the complete answer breakdown sent right below 👇")
            for chunk in [full_payload[i:i+4000] for i in range(0, len(full_payload), 4000)]:
                await context.bot.send_message(chat_id=update.effective_chat.id, text=chunk, parse_mode="Markdown")
        else:
            await query.edit_message_caption(caption=full_payload, parse_mode="Markdown")

# --- ASYNC MULTI-BOT RUNNER ---

async def main():
    tokens = []
    for key, value in os.environ.items():
        if key.startswith("TELEGRAM_BOT_TOKEN_") and value.strip():
            tokens.append((key, value.strip()))

    if not tokens:
        logger.error("CRITICAL: No environment variables found matching 'TELEGRAM_BOT_TOKEN_X'!")
        return

    logger.info(f"Found {len(tokens)} tokens. Loading Quiz cluster engine...")
    apps = []

    for env_name, token in tokens:
        app = ApplicationBuilder().token(token).build()
        
        app.add_handler(CommandHandler("start", start_command))
        app.add_handler(CallbackQueryHandler(handle_quiz_selection, pattern="^quiz_"))
        
        await app.initialize()
        await app.start()
        await app.updater.start_polling(allowed_updates=Update.ALL_TYPES)
        apps.append(app)

    logger.info("All 10+ quiz tracking engines initialized securely.")

    try:
        while True:
            await asyncio.sleep(3600)
    except (KeyboardInterrupt, SystemExit):
        for app in apps:
            await app.updater.stop()
            await app.stop()
            await app.shutdown()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        pass
