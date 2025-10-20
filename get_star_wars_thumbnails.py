import requests
import os
import zipfile
from PIL import Image
from io import BytesIO
import time

characters = [
    "Luke Skywalker",
    "Han Solo",
    "Leia Organa",
    "Chewbacca",
    "R2-D2",
    "C-3PO",
    "Darth Vader",
    "Yoda",
    "Obi-Wan Kenobi",
    "Rey (Star Wars)",
    "Lando Calrissian",
    "Padmé Amidala",
    "Mace Windu",
    "Qui-Gon Jinn",
    "Anakin Skywalker",
    "Palpatine",
    "Boba Fett",
    "Jabba the Hutt",
    "Kylo Ren",
    "Finn (Star Wars)"
]

output_dir = "thumbnails"
os.makedirs(output_dir, exist_ok=True)

def fetch_thumbnail(title, width=256):
    """Return a thumbnail URL for a Wikipedia page title (with fallback search)."""
    api = "https://en.wikipedia.org/w/api.php"
    headers = {
        "User-Agent": "StarWarsThumbFetcher/2.0 (https://example.com; contact: you@example.com)"
    }

    params = {
        "action": "query",
        "titles": title,
        "redirects": 1,
        "prop": "pageimages",
        "format": "json",
        "pithumbsize": width
    }

    r = requests.get(api, params=params, headers=headers, timeout=10)
    data = r.json()

    pages = data.get("query", {}).get("pages", {})
    for _, page in pages.items():
        if "thumbnail" in page:
            return page["thumbnail"]["source"]

    # fallback: search for the most likely page
    search_params = {
        "action": "query",
        "generator": "search",
        "gsrsearch": title,
        "gsrlimit": 1,
        "prop": "pageimages",
        "format": "json",
        "pithumbsize": width
    }
    r = requests.get(api, params=search_params, headers=headers, timeout=10)
    data = r.json()

    pages = data.get("query", {}).get("pages", {})
    for _, page in pages.items():
        if "thumbnail" in page:
            return page["thumbnail"]["source"]

    return None


for name in characters:
    slug = name.lower().replace(" ", "-").replace("(", "").replace(")", "").replace(".", "")
    print(f"Fetching: {name}")

    url = fetch_thumbnail(name)
    if not url:
        print(f"❌ No thumbnail found for {name}")
        continue

    try:
        img_data = requests.get(url, timeout=10).content
        img = Image.open(BytesIO(img_data)).convert("RGBA")
        out_path = os.path.join(output_dir, f"{slug}.png")
        img.save(out_path, "PNG")
        print(f"✅ Saved {slug}.png")
    except Exception as e:
        print(f"❌ Failed to save {slug}: {e}")

    time.sleep(0.5)

# Zip them up
zip_path = "starwars_thumbnails.zip"
with zipfile.ZipFile(zip_path, "w", zipfile.ZIP_DEFLATED) as z:
    for f in os.listdir(output_dir):
        z.write(os.path.join(output_dir, f), arcname=f)

print(f"\n📦 Done. Created {zip_path}")

