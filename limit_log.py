import sys

# File size limit (1 MB)
LIMIT = 1024 * 1024 

with open("wp-check.log", "wb") as f:
    while True:
        # Read data from the scanner in chunks
        data = sys.stdin.buffer.read(4096)
        if not data:
            break
        
        f.write(data)
        
        # If file size exceeds 1MB, go back to the beginning (overwrite)
        if f.tell() > LIMIT:
            f.seek(0)
            f.truncate() # Clear old data to start fresh from the top
