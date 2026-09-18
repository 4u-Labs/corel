#!/usr/bin/env python3
"""
CorelClone Pro 2026 — High-Fidelity Vector Conversion Bridge & Local Server
Converts .CDR and .PDF files to native, crystal-clear SVG using:
- pdftocairo (for .PDF, instant poppler vector conversion)
- LibreOffice/libcdr (for .CDR, with automatic pasteboard debris cleaning)
Also serves CorelClone Pro directly on http://127.0.0.1:54321 for 100% offline usage.
"""

from http.server import HTTPServer, BaseHTTPRequestHandler
import tempfile, subprocess, os, sys, shutil, re
import xml.etree.ElementTree as ET

PORT = 54321
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

MIME_TYPES = {
    '.html': 'text/html; charset=utf-8',
    '.htm': 'text/html; charset=utf-8',
    '.php': 'text/html; charset=utf-8',
    '.js': 'application/javascript; charset=utf-8',
    '.css': 'text/css; charset=utf-8',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.jpeg': 'image/jpeg',
    '.webp': 'image/webp',
    '.svg': 'image/svg+xml; charset=utf-8',
    '.json': 'application/json; charset=utf-8',
    '.ico': 'image/x-icon',
    '.woff': 'font/woff',
    '.woff2': 'font/woff2',
    '.ttf': 'font/ttf',
    '.pdf': 'application/pdf'
}

def clean_libreoffice_svg(svg_bytes):
    """
    Cleans up LibreOffice SVG export:
    - Removes dummy invisible BoundingBox rects
    - Removes empty Master Slide / presentation background groups
    - Prunes discarded objects placed outside the page margins (pasteboard / mesa de trabalho)
    - Prunes empty <g> containers recursively
    - Unwraps single-child transparent wrappers (SlideGroup -> g -> container-id1 -> id1 -> Page)
      so the visual shapes (images, paths, curves) are direct top-level elements.
    """
    try:
        ET.register_namespace('', 'http://www.w3.org/2000/svg')
        ET.register_namespace('xlink', 'http://www.w3.org/1999/xlink')
        root = ET.fromstring(svg_bytes)

        # Read viewBox
        vb_attr = root.attrib.get('viewBox', '')
        vb_parts = [float(p) for p in re.split(r'[\s,]+', vb_attr.strip()) if p]
        if len(vb_parts) == 4:
            vb_min_x, vb_min_y, vb_w, vb_h = vb_parts
        else:
            vb_min_x, vb_min_y, vb_w, vb_h = 0, 0, 8000, 16000

        # 1. Prune unwanted elements (BoundingBox rects, Master Slide, pasteboard items)
        for parent in list(root.iter()):
            for child in list(parent):
                tag = child.tag.split('}')[-1]
                cls = child.attrib.get('class', '')
                cid = child.attrib.get('id', '')

                # Remove dummy BoundingBox rects
                if tag == 'rect' and ('BoundingBox' in cls or (child.attrib.get('stroke') == 'none' and child.attrib.get('fill') == 'none')):
                    parent.remove(child)
                    continue

                # Remove Master Slide & presentation background
                if cls == 'Master_Slide' or cid in ('id2', 'bg-id2', 'bo-id2'):
                    parent.remove(child)
                    continue

                # Check coordinates of elements far outside viewBox
                x_str = child.attrib.get('x')
                y_str = child.attrib.get('y')
                if x_str is not None and y_str is not None:
                    try:
                        x = float(x_str)
                        y = float(y_str)
                        w = float(child.attrib.get('width', 0))
                        h = float(child.attrib.get('height', 0))
                        if (x >= vb_w * 1.02) or (x + w <= vb_min_x - vb_w * 0.02) or (y >= vb_h * 1.02) or (y + h <= vb_min_y - vb_h * 0.02):
                            parent.remove(child)
                            continue
                    except:
                        pass

                # Check path coordinates
                if tag == 'path':
                    d = child.attrib.get('d', '')
                    if d:
                        nums = [float(n) for n in re.findall(r'[-+]?\d*\.?\d+(?:[eE][-+]?\d+)?', d)]
                        if nums and len(nums) >= 2:
                            xs = nums[0::2]
                            ys = nums[1::2]
                            if xs and min(xs) >= vb_w * 1.02:
                                parent.remove(child)
                                continue

        # 2. Recursively prune empty groups
        changed = True
        while changed:
            changed = False
            for parent in list(root.iter()):
                for child in list(parent):
                    if child.tag.split('}')[-1] == 'g' and len(child) == 0:
                        parent.remove(child)
                        changed = True

        # 3. Unwrap transparent LibreOffice group hierarchies
        defs_and_styles = [c for c in list(root) if c.tag.split('}')[-1] in ('defs', 'style', 'metadata')]
        
        def find_visual_elements(node):
            res = []
            for c in list(node):
                tag = c.tag.split('}')[-1]
                if tag in ('defs', 'style', 'metadata'):
                    continue
                # If group with only 1 child group and no attributes/transforms, unwrap
                curr = c
                while curr.tag.split('}')[-1] == 'g' and len(curr) == 1 and list(curr)[0].tag.split('}')[-1] == 'g' and not curr.attrib.get('transform'):
                    curr = list(curr)[0]
                if curr.attrib.get('class') == 'Page':
                    for pch in list(curr):
                        # also unwrap single-element dummy wrappers like <g class='com.sun.star...'><path ...></g>
                        if pch.tag.split('}')[-1] == 'g' and len(pch) == 1 and not pch.attrib.get('transform'):
                            inner = list(pch)[0]
                            if inner.tag.split('}')[-1] in ('path', 'image', 'text', 'rect', 'ellipse', 'polygon'):
                                res.append(inner)
                                continue
                        res.append(pch)
                else:
                    if curr.tag.split('}')[-1] == 'g' and len(curr) == 1 and not curr.attrib.get('transform'):
                        inner = list(curr)[0]
                        if inner.tag.split('}')[-1] in ('path', 'image', 'text', 'rect', 'ellipse', 'polygon'):
                            res.append(inner)
                            continue
                    res.append(curr)
            return res

        visual_nodes = find_visual_elements(root)
        if visual_nodes:
            for c in list(root):
                if c not in defs_and_styles:
                    root.remove(c)
            for vn in visual_nodes:
                root.append(vn)

        return ET.tostring(root, encoding='utf-8')
    except Exception as e:
        sys.stderr.write(f"[CorelBridge] Error cleaning SVG: {e}\n")
        return svg_bytes

class CDRBridgeHandler(BaseHTTPRequestHandler):
    def log_message(self, format, *args):
        sys.stderr.write(f"[CorelBridge] {format % args}\n")

    def send_cors_headers(self):
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, HEAD')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type, Content-Length, X-Requested-With, Range, Access-Control-Request-Private-Network')
        self.send_header('Access-Control-Allow-Private-Network', 'true')
        self.send_header('Cache-Control', 'no-cache, no-store, must-revalidate')

    def do_OPTIONS(self):
        self.send_response(204)
        self.send_cors_headers()
        self.end_headers()

    def do_HEAD(self):
        self.do_GET()

    def do_GET(self):
        clean_path = self.path.split('?')[0].strip()

        # Status check
        if clean_path == '/status':
            self.send_response(200)
            self.send_cors_headers()
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(b'{"status":"ready","service":"CorelClone High-Fidelity Vector Bridge","converters":["pdftocairo","libreoffice/libcdr"],"version":"2026"}')
            return

        # CorelClone Web App
        if clean_path in ('', '/', '/index.php', '/index.html'):
            php_file = os.path.join(BASE_DIR, 'index.php')
            if os.path.isfile(php_file):
                env = os.environ.copy()
                env['REQUEST_URI'] = '/'
                env['SCRIPT_NAME'] = '/index.php'
                proc = subprocess.run(
                    ['php', php_file],
                    cwd=BASE_DIR,
                    env=env,
                    stdout=subprocess.PIPE,
                    stderr=subprocess.PIPE
                )
                if proc.returncode == 0 and proc.stdout:
                    self.send_response(200)
                    self.send_cors_headers()
                    self.send_header('Content-Type', 'text/html; charset=utf-8')
                    self.send_header('Content-Length', str(len(proc.stdout)))
                    self.end_headers()
                    self.wfile.write(proc.stdout)
                    return

        # Static assets
        rel_path = clean_path.lstrip('/')
        target_file = os.path.abspath(os.path.join(BASE_DIR, rel_path))

        # Prevent directory traversal
        if target_file.startswith(BASE_DIR) and os.path.isfile(target_file):
            ext = os.path.splitext(target_file)[1].lower()
            mime = MIME_TYPES.get(ext, 'application/octet-stream')

            try:
                with open(target_file, 'rb') as f:
                    content = f.read()

                self.send_response(200)
                self.send_cors_headers()
                self.send_header('Content-Type', mime)
                self.send_header('Content-Length', str(len(content)))
                self.end_headers()
                self.wfile.write(content)
                return
            except Exception as e:
                self.send_error(500, f"Erro ao ler arquivo: {e}")
                return

        self.send_error(404, "Arquivo nao encontrado")

    def do_POST(self):
        clean_path = self.path.split('?')[0].strip()
        if clean_path != '/convert':
            self.send_error(404, "Rota desconhecida. Use /convert")
            return

        try:
            content_length = int(self.headers.get('Content-Length', 0))
            if content_length <= 0:
                self.send_error(400, "Arquivo vazio")
                return

            body = self.rfile.read(content_length)

            # Check multipart form-data or raw bytes
            content_type = self.headers.get('Content-Type', '')
            file_bytes = b''
            orig_filename = 'input.cdr'

            if 'multipart/form-data' in content_type and 'boundary=' in content_type:
                boundary = content_type.split('boundary=')[1].strip().encode()
                parts = body.split(b'--' + boundary)
                for part in parts:
                    if b'filename=' in part:
                        # Extract filename
                        fn_match = re.search(rb'filename="([^"]+)"', part)
                        if fn_match:
                            orig_filename = fn_match.group(1).decode(errors='ignore')

                        header_end = part.find(b'\r\n\r\n')
                        if header_end != -1:
                            file_bytes = part[header_end + 4:].rstrip(b'\r\n-')
                            break
            
            if not file_bytes:
                file_bytes = body

            is_pdf = file_bytes.startswith(b'%PDF') or orig_filename.lower().endswith('.pdf')

            with tempfile.TemporaryDirectory() as tmpdir:
                if is_pdf:
                    # Conversion for PDF using pdftocairo (or libreoffice fallback)
                    input_file = os.path.join(tmpdir, "input.pdf")
                    output_svg = os.path.join(tmpdir, "output.svg")
                    with open(input_file, 'wb') as f:
                        f.write(file_bytes)

                    # Try pdftocairo first (fastest and cleanest vector SVG)
                    proc = subprocess.run(['pdftocairo', '-svg', input_file, output_svg], stdout=subprocess.PIPE, stderr=subprocess.PIPE, timeout=20)
                    
                    if not os.path.isfile(output_svg) or os.path.getsize(output_svg) == 0:
                        # Fallback to libreoffice
                        cmd = ['libreoffice', '--headless', '--convert-to', 'svg', input_file, '--outdir', tmpdir]
                        subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE, timeout=30)
                        svg_candidates = [f for f in os.listdir(tmpdir) if f.endswith('.svg')]
                        if svg_candidates:
                            output_svg = os.path.join(tmpdir, svg_candidates[0])

                    if not os.path.isfile(output_svg) or os.path.getsize(output_svg) == 0:
                        self.send_response(500)
                        self.send_cors_headers()
                        self.send_header('Content-Type', 'application/json')
                        self.end_headers()
                        self.wfile.write(b'{"error": "Falha na conversao do PDF para SVG"}')
                        return

                    with open(output_svg, 'rb') as f:
                        svg_content = f.read()

                else:
                    # Conversion for CDR using LibreOffice (libcdr backend)
                    input_cdr = os.path.join(tmpdir, "input.cdr")
                    with open(input_cdr, 'wb') as f:
                        f.write(file_bytes)

                    cmd = ['libreoffice', '--headless', '--convert-to', 'svg', input_cdr, '--outdir', tmpdir]
                    proc = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE, timeout=40)

                    svg_files = [f for f in os.listdir(tmpdir) if f.endswith('.svg')]
                    if not svg_files:
                        err_msg = proc.stderr.decode(errors='ignore') or proc.stdout.decode(errors='ignore') or "Falha na conversao vetorial"
                        self.send_response(500)
                        self.send_cors_headers()
                        self.send_header('Content-Type', 'application/json')
                        self.end_headers()
                        self.wfile.write(f'{{"error": "{err_msg}"}}'.encode())
                        return

                    svg_path = os.path.join(tmpdir, svg_files[0])
                    with open(svg_path, 'rb') as f:
                        raw_svg = f.read()

                    # Clean LibreOffice SVG: prune rogue pasteboard objects and dummy rects
                    svg_content = clean_libreoffice_svg(raw_svg)

                self.send_response(200)
                self.send_cors_headers()
                self.send_header('Content-Type', 'image/svg+xml; charset=utf-8')
                self.send_header('Content-Length', str(len(svg_content)))
                self.end_headers()
                self.wfile.write(svg_content)

        except Exception as e:
            self.send_response(500)
            self.send_cors_headers()
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(f'{{"error": "{str(e)}"}}'.encode())

def run_server():
    server = HTTPServer(('127.0.0.1', PORT), CDRBridgeHandler)
    print(f"CorelClone Vector Bridge & Web App running on http://127.0.0.1:{PORT}")
    server.serve_forever()

if __name__ == '__main__':
    run_server()
