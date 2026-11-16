"""Convert Windows line endings (CRLF) to Unix line endings (LF) in the text files of a folder.

Usage: python3 scripts/win2unix.py [--dry-run] <folder>

Only text files of the types below are changed. The folders of git, of the dependencies and of the software factory are left out.
A file which is not valid UTF-8 (a binary file with a text extension) is left as it is.

Git can do this for the files it tracks (.gitattributes, git add --renormalize .); this script is for a folder outside git, for
example an unpacked package.
"""

import argparse
import os
import sys

EXTENSIONS = {'.php', '.css', '.scss', '.xml', '.ini', '.js', '.mjs', '.json', '.txt', '.md', '.sql', '.html', '.svg', '.sh', '.py'}
SKIPPED_FOLDERS = {'.git', 'node_modules', 'vendor', '.worktrees', '.idea'}


def convert(path, dry_run):
    """Return the number of line endings converted in the file (0 when it is not changed)."""
    with open(path, 'rb') as handle:
        data = handle.read()

    count = data.count(b'\r\n')

    if count == 0:
        return 0

    try:
        data.decode('utf-8')
    except UnicodeDecodeError:
        print(f'{path}: not UTF-8 text, left as it is')
        return 0

    if not dry_run:
        with open(path, 'wb') as handle:
            handle.write(data.replace(b'\r\n', b'\n'))

    print(f"{path}: {'would replace' if dry_run else 'replaced'} {count}x CRLF with LF")

    return count


def main():
    parser = argparse.ArgumentParser(description='Convert CRLF to LF in the text files of a folder.')
    parser.add_argument('folder')
    parser.add_argument('--dry-run', action='store_true', help='only show what would be changed')
    arguments = parser.parse_args()

    if not os.path.isdir(arguments.folder):
        parser.error(f'{arguments.folder} is not a folder')

    files = 0

    for root, folders, names in os.walk(arguments.folder):
        folders[:] = [folder for folder in folders if folder not in SKIPPED_FOLDERS]

        for name in names:
            if os.path.splitext(name)[1].lower() in EXTENSIONS:
                if convert(os.path.join(root, name), arguments.dry_run):
                    files += 1

    print(f"{files} file(s) {'would be' if arguments.dry_run else ''} converted".replace('  ', ' '))

    return 0


if __name__ == '__main__':
    sys.exit(main())
