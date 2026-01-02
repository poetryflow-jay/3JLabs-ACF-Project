import os
import shutil
import zipfile

SOURCE_DIR = 'acf-css-neural-link'
OUTPUT_DIR = os.path.join(os.environ['USERPROFILE'], 'Desktop', 'JJ_Distributions')
ZIP_NAME = 'acf-css-neural-link-v3.1.0.zip'

if not os.path.exists(OUTPUT_DIR):
    os.makedirs(OUTPUT_DIR)

zip_path = os.path.join(OUTPUT_DIR, ZIP_NAME)

print(f"Packaging Neural Link...")

with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
    for root, dirs, files in os.walk(SOURCE_DIR):
        for file in files:
            file_path = os.path.join(root, file)
            arcname = os.path.relpath(file_path, os.path.dirname(SOURCE_DIR))
            zipf.write(file_path, arcname)

print(f"Created: {zip_path}")

