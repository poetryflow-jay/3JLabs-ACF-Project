import os
import re

EXCLUDE_PATTERNS = [
    r'\.git', r'\.vscode', r'\.idea', r'__pycache__', r'\.DS_Store',
    r'tests', r'phpunit\.xml', r'composer\.json', r'node_modules',
    r'package\.json', r'package-lock\.json', r'gulpfile\.js', 
    r'\.editorconfig', r'README\.md', r'\.bak', r'local-server\\venv',
    r'marketing',
]

def test_copy():
    src = 'acf-css-really-simple-style-management-center-master'
    for root, dirs, files in os.walk(src):
        print(f"Checking root: {root}")
        # Filter out excluded directories
        original_dirs = list(dirs)
        dirs[:] = [d for d in dirs if not any(re.search(p, os.path.join(root, d)) for p in EXCLUDE_PATTERNS)]
        for d in original_dirs:
            if d not in dirs:
                print(f"  EXCLUDED DIR: {d}")
        
        for file in files:
            path = os.path.join(root, file)
            is_excluded = any(re.search(p, path) for p in EXCLUDE_PATTERNS)
            if is_excluded:
                print(f"  EXCLUDED FILE: {file} (Path: {path})")
            else:
                # print(f"  KEEP: {file}")
                pass

if __name__ == "__main__":
    test_copy()

