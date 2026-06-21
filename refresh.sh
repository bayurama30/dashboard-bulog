#!/bin/bash
# Refresh data dari Google Sheets
cd "$(dirname "$0")"
echo "🔄 Fetching data dari Google Sheets..."
python3 fetch-sheets-data.py
echo "✅ Done! Dashboard siap diakses."
