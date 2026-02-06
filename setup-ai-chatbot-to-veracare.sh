#!/bin/bash
#
# Kopiranje AI Chatbot iz verabot/ u VeraCare/ai-chatbot/ za produkciju
# Originalni verabot/ folder ostaje za sajt
#

echo "🚀 Setting up AI Chatbot in VeraCare for production..."
echo ""

# Base directory
BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
VERACARE_DIR="$BASE_DIR/VeraCare"

# Create VeraCare/ai-chatbot directory
mkdir -p "$VERACARE_DIR/ai-chatbot"

echo "📁 Creating VeraCare/ai-chatbot structure..."

# Copy AI Chatbot (verabot folder excluding content-generator and test folders)
echo "📦 Copying AI Chatbot from verabot/ to VeraCare/ai-chatbot/..."
if [ -d "$BASE_DIR/verabot" ]; then
    # Copy all files from verabot
    cp -r "$BASE_DIR/verabot"/* "$VERACARE_DIR/ai-chatbot/" 2>/dev/null || true
    
    # Remove content-generator from ai-chatbot (it's already moved to VeraCare/content-generator/)
    rm -rf "$VERACARE_DIR/ai-chatbot/content-generator" 2>/dev/null || true
    
    # Remove test folders
    rm -rf "$VERACARE_DIR/ai-chatbot/allrounder" 2>/dev/null || true
    rm -rf "$VERACARE_DIR/ai-chatbot/allrounderhandwerker" 2>/dev/null || true
    
    echo "✅ AI Chatbot copied to VeraCare/ai-chatbot/"
else
    echo "❌ Error: verabot folder not found"
    exit 1
fi

echo ""
echo "✅ Done! AI Chatbot setup for production:"
echo ""
echo "📁 Structure:"
echo "   allrounder/verabot/              (✅ OSTAJE - koristi se na sajtu)"
echo "   allrounder/VeraCare/ai-chatbot/  (✅ KOPIJA - za produkciju/prodaju)"
echo ""
echo "🔐 Admin Panel:"
echo "   - Production: VeraCare/ai-chatbot/chatbot-admin.php"
echo "   - Website: allrounder/verabot/chatbot-admin.php"
echo ""






