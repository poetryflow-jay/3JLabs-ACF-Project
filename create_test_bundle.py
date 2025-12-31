import os
import re
import json
import shutil
from pathlib import Path


BASE_VERSION = "8.0.0"
OUTPUT_ROOT = Path(os.environ["USERPROFILE"]) / "Desktop" / f"JJ_Distributions_v{BASE_VERSION}_Master_Control"
LOG_DIR = OUTPUT_ROOT / "deploy_logs"


def _parse_semver(v: str):
    parts = v.split(".")
    return tuple(int(p) for p in parts[:3])


def _pick_latest_semver_zip(files):
    files = list(files)
    if not files:
        return None

    best = None
    best_ver = None
    for f in files:
        m = re.search(r"v(\d+\.\d+\.\d+)\.zip$", f.name)
        if not m:
            continue
        ver = _parse_semver(m.group(1))
        if best_ver is None or ver > best_ver:
            best_ver = ver
            best = f

    if best is None:
        best = max(files, key=lambda p: p.stat().st_mtime)
    return best


def _latest_build_id():
    logs = sorted(LOG_DIR.glob("build_*.json"))
    if not logs:
        return None
    latest = logs[-1]
    m = re.match(r"build_(\d{8}-\d{6})\.json$", latest.name)
    if m:
        return m.group(1)
    try:
        data = json.loads(latest.read_text(encoding="utf-8"))
        return data.get("build_id")
    except Exception:
        return None


def _ensure_dir(p: Path):
    p.mkdir(parents=True, exist_ok=True)


def _copy_to_dir(src: Path, dst_dir: Path):
    _ensure_dir(dst_dir)
    shutil.copy2(src, dst_dir / src.name)


def main():
    if not OUTPUT_ROOT.exists():
        raise SystemExit(f"Output root not found: {OUTPUT_ROOT}")

    build_id = _latest_build_id()
    if not build_id:
        raise SystemExit(f"No build logs found in: {LOG_DIR}")

    dest_root = Path(os.environ["USERPROFILE"]) / "Desktop" / f"JJ_TestBundle_v{BASE_VERSION}_{build_id}"
    if dest_root.exists():
        shutil.rmtree(dest_root)
    _ensure_dir(dest_root)

    # Copy dashboard for convenience (optional)
    dashboard = OUTPUT_ROOT / "dashboard.html"
    if dashboard.exists():
        shutil.copy2(dashboard, dest_root / "dashboard.html")

    channels = {
        "Staging": ("Staging", f"{BASE_VERSION}-staging.{build_id}"),
        "Beta": ("Beta", f"{BASE_VERSION}-beta.1"),
        "Stable": ("Stable", f"{BASE_VERSION}"),
    }

    editions = ["Master", "Partner", "Pro", "Free"]
    core_suffixes = {
        "Master": ["-Master"],
        "Partner": ["-Partner"],
        "Pro": ["-Pro-Basic", "-Pro-Premium", "-Pro-Unlimited"],
        "Free": [""],
    }

    standard_addon_prefixes = {
        "ai": "ACF-CSS-AI-Extension-v",
        "neural": "ACF-CSS-Neural-Link-v",
        "woo": "ACF-CSS-Woo-License-v",
        "bulk": "WP-Bulk-Manager-v",
        "menu": "Admin-Menu-Editor-Lite-v",
    }
    master_addon_prefixes = {
        "ai": "ACF-CSS-AI-Extension-Master-v",
        "neural": "ACF-CSS-Neural-Link-Master-v",
        "woo": "ACF-CSS-Woo-License-Master-v",
        "bulk": "WP-Bulk-Manager-Master-v",
        "menu": "Admin-Menu-Editor-Lite-Master-v",
    }

    master_addon_src = OUTPUT_ROOT / "Master"
    master_addons = []
    for _, prefix in master_addon_prefixes.items():
        picked = _pick_latest_semver_zip(master_addon_src.glob(prefix + "*.zip"))
        if picked:
            master_addons.append(picked)

    for dest_channel, (src_folder, core_ver) in channels.items():
        src_dir = OUTPUT_ROOT / src_folder
        if not src_dir.exists():
            continue

        standard_addons = []
        for _, prefix in standard_addon_prefixes.items():
            picked = _pick_latest_semver_zip(src_dir.glob(prefix + "*.zip"))
            if picked:
                standard_addons.append(picked)

        for edition in editions:
            dst_dir = dest_root / dest_channel / edition
            _ensure_dir(dst_dir)

            # Core ZIPs
            for suff in core_suffixes[edition]:
                core_name = f"ACF-CSS{suff}-v{core_ver}.zip"
                core_src = src_dir / core_name
                if core_src.exists():
                    _copy_to_dir(core_src, dst_dir)
                else:
                    (dst_dir / f"__MISSING__{core_name}.txt").write_text(
                        f"Missing expected file:\n{core_src}\n",
                        encoding="utf-8",
                    )

            # Add-ons (optional convenience)
            addons_dir = dst_dir / "Add-ons"
            _ensure_dir(addons_dir)
            addons = master_addons if edition in ("Master", "Partner") else standard_addons
            for a in addons:
                _copy_to_dir(a, addons_dir)

    # Root README
    readme = dest_root / "README_INSTALL_ORDER.md"
    readme.write_text(
        "\n".join(
            [
                f"# JJ Test Bundle v{BASE_VERSION} ({build_id})",
                "",
                "## 폴더 구조",
                "- Staging / Beta / Stable",
                "  - Master / Partner / Pro / Free",
                "    - Core ZIP(들)",
                "    - Add-ons (참고용)",
                "",
                "## 권장 테스트 설치 순서(대표님용)",
                "1) Core ZIP 설치/활성화",
                "2) (필요 시) Add-ons 설치/활성화",
                "3) Admin Center → Updates 탭: 통합 테이블/토글/액션 확인",
                "4) Admin Center → System Status → 자가 진단 실행 (원인 파일 출력 확인)",
                "",
                "## 참고",
                "- Master/Partner 폴더에는 (업그레이드/제한 UI 제거용) Master Add-ons ZIP이 들어있습니다.",
                "- Pro/Free 폴더에는 각 채널의 Standard Add-ons ZIP이 들어있습니다.",
                "",
            ]
        )
        + "\n",
        encoding="utf-8",
    )

    print(f"✅ Test bundle created: {dest_root}")


if __name__ == "__main__":
    main()

