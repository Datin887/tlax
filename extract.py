#!/usr/bin/env python3
"""
Извлекает код из markdown-файлов проекта tlax.
Для каждого файла берёт САМЫЙ ДЛИННЫЙ блок кода.
"""
import re
import os
import sys
from collections import defaultdict

def extract_all_blocks(content):
    """Извлекает все блоки кода с их заголовками"""
    blocks = []
    
    # Найти все заголовки с путём к файлу в бэктиках
    header_re = re.compile(r'^##\s+(?:ЭТАП\s+\d+\s*[—–-]\s*)?(?:Файл\s+\d+\s*:\s*)?(?:Дополнени\w*\s+к\s+)?`([^`]+)`', re.MULTILINE)
    
    # Найти все кодовые блоки
    code_re = re.compile(r'^```(\w+)\n(.*?)^```', re.MULTILINE | re.DOTALL)
    
    headers = list(header_re.finditer(content))
    
    for idx, match in enumerate(headers):
        file_path = match.group(1).strip()
        start = match.end()
        end = headers[idx + 1].start() if idx + 1 < len(headers) else len(content)
        
        section = content[start:end]
        
        code_match = code_re.search(section)
        if code_match:
            lang = code_match.group(1)
            code = code_match.group(2).strip()
            
            # Пропускаем robots.txt (plain text, не код)
            if file_path.endswith('robots.txt'):
                continue
            # Пропускаемตาราง_text
            if file_path.endswith('.txt') or file_path.endswith('.md'):
                continue
                
            blocks.append((file_path, lang, code))
    
    return blocks

def main():
    base_dir = '/home/aiuser/.openclaw/workspace/tlax'
    build_dir = '/home/aiuser/.openclaw/workspace/projects/tlax/build'
    
    md_files = [
        '1_foundation',
        '2_main_page',
        '3_portfolio',
        '4_pricing 5_order_form',
        '6_contacts 7_admin',
        '8_final'
    ]
    
    # Собираем ВСЕ блоки (могут дублироваться)
    all_blocks = []
    
    for md_file in md_files:
        md_path = os.path.join(base_dir, md_file)
        if not os.path.exists(md_path):
            print(f"SKIP: {md_path}", file=sys.stderr)
            continue
        
        print(f"\n{'='*60}")
        print(f"ПАРСИМ: {md_file}")
        print(f"{'='*60}")
        
        with open(md_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        blocks = extract_all_blocks(content)
        print(f"  Найдено блоков: {len(blocks)}")
        
        for file_path, lang, code in blocks:
            lines = count = code.count('\n') + 1
            print(f"    [{lang:12s}] {file_path} ({count} строк)")
        
        all_blocks.extend(blocks)
    
    # Для каждого файла берём самый длинный блок
    best = {} # file_path -> (lang, code, lines_count)
    
    for file_path, lang, code in all_blocks:
        lc = code.count('\n') + 1
        if file_path not in best or lc > best[file_path][2]:
            best[file_path] = (lang, code, lc)
    
    # Записываем файлы
    print(f"\n{'='*60}")
    print(f"ЗАПИСЬ ФАЙЛОВ (берём最长 версию)")
    print(f"{'='*60}")
    
    total_lines = 0
    for file_path in sorted(best.keys()):
        lang, code, lines = best[file_path]
        print(f"  [{lang:12s}] {file_path:53s} {lines:5d} строк")
        
        target = os.path.join(build_dir, file_path.lstrip('/'))
        os.makedirs(os.path.dirname(target), exist_ok=True)
        
        with open(target, 'w', encoding='utf-8') as f:
            f.write(code)
        
        total_lines += lines
    
    print(f"\n{'='*60}")
    print(f"ИТОГО: {len(best)} файлов, {total_lines} строк")
    print(f"{'='*60}")
    
    return best

if __name__ == '__main__':
    main()