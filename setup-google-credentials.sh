#!/bin/bash
# Setup Google Credentials for Dashboard Bulog
# This script helps you set up Google API credentials

echo "=== Setup Google Credentials for Dashboard Bulog ==="
echo ""
echo "This script will help you configure Google Sheets API access."
echo ""
echo "Prerequisites:"
echo "1. A Google Cloud Project with Sheets API enabled"
echo "2. OAuth 2.0 credentials (client ID and secret)"
echo "3. A refresh token for the account that has access to the spreadsheet"
echo ""
echo "Spreadsheet ID: 16G1AOk9NPkr8qvOmz22bW00V9_WsKWPE66izsoz038E"
echo "Spreadsheet URL: https://docs.google.com/spreadsheets/d/16G1AOk9NPkr8qvOmz22bW00V9_WsKWPE66izsoz038E/edit"
echo ""
echo "Sheet names to access:"
echo "  - data dashboard GKP"
echo "  - data dashboard Jagung"
echo "  - data dashboard beras PSO"
echo "  - dashboard pengolahan"
echo ""

read -p "Enter GOOGLE_CLIENT_ID: " CLIENT_ID
read -p "Enter GOOGLE_CLIENT_SECRET: " CLIENT_SECRET
read -p "Enter GOOGLE_REFRESH_TOKEN: " REFRESH_TOKEN

# Update .env file
ENV_FILE=".env"
if [ ! -f "$ENV_FILE" ]; then
    echo "Error: .env file not found"
    exit 1
fi

# Remove existing Google credentials if any
sed -i.bak '/^GOOGLE_CLIENT_ID=/d' "$ENV_FILE"
sed -i.bak '/^GOOGLE_CLIENT_SECRET=/d' "$ENV_FILE"
sed -i.bak '/^GOOGLE_REFRESH_TOKEN=/d' "$ENV_FILE"
rm -f "${ENV_FILE}.bak"

# Add new credentials
echo "" >> "$ENV_FILE"
echo "# Google Sheets API Credentials" >> "$ENV_FILE"
echo "GOOGLE_CLIENT_ID=$CLIENT_ID" >> "$ENV_FILE"
echo "GOOGLE_CLIENT_SECRET=$CLIENT_SECRET" >> "$ENV_FILE"
echo "GOOGLE_REFRESH_TOKEN=$REFRESH_TOKEN" >> "$ENV_FILE"

echo ""
echo "Credentials saved to .env file"
echo ""
echo "Testing connection..."
php artisan sheets:fetch

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Setup complete! Data fetched successfully."
else
    echo ""
    echo "❌ Setup failed. Please check your credentials and try again."
    echo "Make sure the Google account has access to the spreadsheet."
fi
