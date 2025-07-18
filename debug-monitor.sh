#!/bin/bash

echo "Starting CodeDesign Plugin Debug Monitor..."
echo "=========================================="

# Set the WordPress debug log path
DEBUG_LOG="/Users/manjunathm/Local Sites/codedesign-test/app/public/wp-content/debug.log"

echo "WordPress Debug Log: $DEBUG_LOG"
echo ""

# Show recent logs first
echo "Recent logs (last 10 lines):"
echo "-----------------------------"
tail -10 "$DEBUG_LOG" | grep -v "Xdebug"
echo ""

echo "Now monitoring NEW logs in real-time..."
echo "Press Ctrl+C to stop"
echo "=========================================="

# Follow new logs and filter out Xdebug noise
tail -f "$DEBUG_LOG" | grep -v "Xdebug" 