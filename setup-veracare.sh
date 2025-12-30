#!/bin/bash
#
# Script za kopiranje AI Chatbot i Content Generator u VeraCare folder za produkciju
# Originalni verabot folder ostaje u allrounder/ za sajt
#

echo "🚀 Setting up VeraCare production folder..."
echo ""

# Base directory
BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
VERACARE_DIR="$BASE_DIR/VeraCare"

# Create VeraCare directories
mkdir -p "$VERACARE_DIR/ai-chatbot"
mkdir -p "$VERACARE_DIR/content-generator"

echo "📁 Creating VeraCare structure..."

# Copy AI Chatbot (verabot folder excluding content-generator)
echo "📦 Copying AI Chatbot..."
if [ -d "$BASE_DIR/verabot" ]; then
    # Copy all files from verabot (excluding content-generator and test folders)
    cp -r "$BASE_DIR/verabot"/* "$VERACARE_DIR/ai-chatbot/" 2>/dev/null || true
    # Remove content-generator from ai-chatbot
    rm -rf "$VERACARE_DIR/ai-chatbot/content-generator" 2>/dev/null || true
    rm -rf "$VERACARE_DIR/ai-chatbot/allrounder" 2>/dev/null || true
    rm -rf "$VERACARE_DIR/ai-chatbot/allrounderhandwerker" 2>/dev/null || true
    echo "✅ AI Chatbot copied to VeraCare/ai-chatbot/"
else
    echo "❌ Error: verabot folder not found"
    exit 1
fi

# Note: Content Generator should already be in VeraCare/content-generator/
# (moved manually, not copied)
if [ -d "$VERACARE_DIR/content-generator" ]; then
    echo "✅ Content Generator found in VeraCare/content-generator/"
else
    echo "⚠️  Warning: Content Generator not found in VeraCare/content-generator/"
    echo "   It should have been moved from verabot/content-generator/"
fi

echo ""
echo "✅ Done! Production structure created:"
echo ""
echo "📁 VeraCare/"
echo "   ├── ai-chatbot/        (AI Chatbot za prodaju)"
echo "   └── content-generator/ (AI Content Generator za prodaju)"
echo ""
echo "📁 allrounder/verabot/ (ostaje za sajt - ne menjaj)"
echo ""
echo "🔐 Admin Panels:"
echo "   - AI Chatbot: VeraCare/ai-chatbot/chatbot-admin.php"
echo "   - Content Generator: VeraCare/content-generator/admin/license-admin.php"
echo ""

