#!/usr/bin/env python3
"""
Script untuk mendapatkan Google Refresh Token
Mendengarkan callback otomatis dari browser
"""
import os
import json
import sys
import threading
import time
from http.server import HTTPServer, BaseHTTPRequestHandler
from urllib.parse import urlparse, parse_qs
from google_auth_oauthlib.flow import Flow

CLIENT_SECRET_FILE = os.path.expanduser('~/Downloads/client_secret_1013516093907-qpfgpde9houprbuc1pp9n929bcvvsire.apps.googleusercontent.com.json')

if not os.path.exists(CLIENT_SECRET_FILE):
    print(f"Error: Client secret file not found at {CLIENT_SECRET_FILE}")
    sys.exit(1)

with open(CLIENT_SECRET_FILE) as f:
    creds_data = json.load(f)

scopes = ['https://www.googleapis.com/auth/spreadsheets']

# Global variable to store the auth code
auth_code = None

class AuthHandler(BaseHTTPRequestHandler):
    def do_GET(self):
        global auth_code
        parsed = urlparse(self.path)
        params = parse_qs(parsed.query)
        
        if 'code' in params:
            auth_code = params['code'][0]
            self.send_response(200)
            self.send_header('Content-type', 'text/html')
            self.end_headers()
            self.wfile.write('<html><body><h1>Otorisasi berhasil!</h1><p>Anda bisa menutup tab ini.</p></body></html>'.encode('utf-8'))
        elif 'error' in params:
            self.send_response(400)
            self.send_header('Content-type', 'text/html')
            self.end_headers()
            self.wfile.write(f'<html><body><h1>❌ Error: {params["error"][0]}</h1></body></html>'.encode())
        else:
            self.send_response(404)
            self.end_headers()
    
    def log_message(self, format, *args):
        pass  # Suppress log messages

# Start server in background
server = HTTPServer(('localhost', 8080), AuthHandler)
server_thread = threading.Thread(target=server.serve_forever)
server_thread.daemon = True
server_thread.start()

# Create flow
flow = Flow.from_client_config(
    creds_data,
    scopes=scopes,
    redirect_uri='http://localhost:8080'
)

auth_url, _ = flow.authorization_url(prompt='consent', access_type='offline')

print("=" * 60)
print("Google OAuth - Mendapatkan Refresh Token")
print("=" * 60)
print()
print("Buka URL berikut di browser:")
print(auth_url)
print()
print("Menunggu otorisasi...")
print("Server mendengarkan di http://localhost:8080")

# Wait for auth code
timeout = 120  # 2 minutes
start_time = time.time()

while auth_code is None and (time.time() - start_time) < timeout:
    time.sleep(1)

server.shutdown()

if auth_code is None:
    print()
    print("❌ Timeout: Tidak ada otorisasi yang diterima dalam 2 menit")
    sys.exit(1)

print()
print("✅ Kode otorisasi diterima!")

try:
    flow.fetch_token(code=auth_code)
    creds = flow.credentials
    
    refresh_token = creds.refresh_token
    
    print()
    print("=" * 60)
    print("✅ Refresh token berhasil didapatkan!")
    print("=" * 60)
    print()
    print("Refresh Token:", refresh_token)
    print()
    
    # Update .env file
    env_path = '.env'
    if os.path.exists(env_path):
        with open(env_path, 'r') as f:
            env_content = f.read()
        
        import re
        env_content = re.sub(
            r'GOOGLE_REFRESH_TOKEN=',
            f'GOOGLE_REFRESH_TOKEN={refresh_token}',
            env_content
        )
        
        with open(env_path, 'w') as f:
            f.write(env_content)
        
        print("✅ Refresh token telah disimpan ke file .env")
        print()
        print("Sekarang jalankan: php artisan sheets:fetch")
    else:
        print(f"⚠️  File .env tidak ditemukan di {os.getcwd()}")
        print(f"   Tambahkan baris berikut ke .env Anda:")
        print(f"   GOOGLE_REFRESH_TOKEN={refresh_token}")
    
except Exception as e:
    print()
    print("❌ Error:", str(e))
    sys.exit(1)
