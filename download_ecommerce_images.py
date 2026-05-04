#!/usr/bin/env python3
"""
Download professional ecommerce images from free sources
- 20 Banners (1920x600)
- 20 Cards (800x600)
- 200 Products (600x600)
"""

import requests
import os
import time
from pathlib import Path

# Professional ecommerce image URLs from Unsplash/Pexels (free to use)
BANNER_IMAGES = [
    # Tech/Gaming banners
    "https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1531297424006-56f9d7e64b3e?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1518770660439-4636190af475?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1504639725590-34d0984388bd?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1551434678-e076c223a692?w=1920&h=600&fit=crop",
    # Electronics banners
    "https://images.unsplash.com/photo-1468495244123-6c6c332eeece?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1523206486230-b4b5f1b0b758?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1550009158-9ebf690569ba?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1563203369-26f2e4a5ccf7?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1572569028738-411a197bb65d?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1607082349566-187342175e2c?w=1920&h=600&fit=crop",
    "https://images.unsplash.com/photo-1603569283847-aa295f0d016a?w=1920&h=600&fit=crop",
]

CARD_IMAGES = [
    # Category cards
    "https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1593642632823-8f78536788c6?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1517336714731-489689fd1ca4?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1593642632823-8f78536788c6?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1572569028738-411a197bb65d?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1550009158-9ebf690569ba?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1580910051074-3ebf9d8b2c1a?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1549298916-b41d501d3772?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=800&h=600&fit=crop",
    "https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&h=600&fit=crop",
]

# Product images - mix of electronics, gadgets, accessories
PRODUCT_BASE_URLS = [
    "https://images.unsplash.com/photo-1505740420928-5e560c06d30e",  # Headphones
    "https://images.unsplash.com/photo-1572569028738-411a197bb65d",  # Laptop
    "https://images.unsplash.com/photo-1523275335684-37898b6baf30",  # Watch
    "https://images.unsplash.com/photo-1542291026-7efd1f1555ed",  # Shoes
    "https://images.unsplash.com/photo-1585565804112-f201f68c48b4",  # Camera
    "https://images.unsplash.com/photo-1546435770-a3e426bf472b",  # Speakers
    "https://images.unsplash.com/photo-1527443224154-c4a3942d3acf",  # Keyboard
    "https://images.unsplash.com/photo-1618384887929-16ec33fab9ef",  # Mouse
    "https://images.unsplash.com/photo-1593642632823-8f78536788c6",  # PC Case
    "https://images.unsplash.com/photo-1587302912306-cf1ed9c33146",  # Monitor
]

def generate_product_urls(count=200):
    """Generate 200 product image URLs with variations"""
    urls = []
    for i in range(count):
        base = PRODUCT_BASE_URLS[i % len(PRODUCT_BASE_URLS)]
        # Add random seed for variety
        seed = (i // len(PRODUCT_BASE_URLS)) + 1
        urls.append(f"{base}?w=600&h=600&fit=crop&seed={seed}")
    return urls

def download_image(url, filepath):
    """Download image from URL and save to filepath"""
    try:
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        }
        response = requests.get(url, headers=headers, timeout=30)
        response.raise_for_status()
        
        with open(filepath, 'wb') as f:
            f.write(response.content)
        return True
    except Exception as e:
        print(f"  ✗ Error downloading {url}: {e}")
        return False

def create_placeholder_image(filepath, width, height, text):
    """Create a placeholder image if download fails"""
    try:
        from PIL import Image, ImageDraw, ImageFont
        
        # Create gradient background
        img = Image.new('RGB', (width, height), color=(11, 46, 113))
        draw = ImageDraw.Draw(img)
        
        # Add text
        try:
            font = ImageFont.truetype("arial.ttf", 40)
        except:
            font = ImageFont.load_default()
        
        bbox = draw.textbbox((0, 0), text, font=font)
        text_width = bbox[2] - bbox[0]
        text_height = bbox[3] - bbox[1]
        
        x = (width - text_width) // 2
        y = (height - text_height) // 2
        
        draw.text((x, y), text, fill=(255, 255, 255), font=font)
        img.save(filepath)
        return True
    except Exception as e:
        print(f"  ✗ Error creating placeholder: {e}")
        return False

def main():
    """Main function to download all images"""
    base_path = Path("public/images/ecommerce")
    
    # Create directories
    folders = {
        'banners': base_path / 'banners',
        'cards': base_path / 'cards',
        'products': base_path / 'products'
    }
    
    for folder in folders.values():
        folder.mkdir(parents=True, exist_ok=True)
        print(f"✓ Created: {folder}")
    
    # Download banners (20)
    print("\n📥 Downloading 20 Banner images...")
    for i, url in enumerate(BANNER_IMAGES, 1):
        filepath = folders['banners'] / f"banner_{i:02d}.jpg"
        print(f"  [{i}/20] Downloading...", end=' ')
        
        if download_image(url, filepath):
            print(f"✓ Saved: {filepath.name}")
        else:
            # Create placeholder
            if create_placeholder_image(filepath, 1920, 600, f"Banner {i}"):
                print(f"✓ Created placeholder: {filepath.name}")
        
        time.sleep(0.5)  # Rate limiting
    
    # Download cards (20)
    print("\n📥 Downloading 20 Card images...")
    for i, url in enumerate(CARD_IMAGES, 1):
        filepath = folders['cards'] / f"card_{i:02d}.jpg"
        print(f"  [{i}/20] Downloading...", end=' ')
        
        if download_image(url, filepath):
            print(f"✓ Saved: {filepath.name}")
        else:
            if create_placeholder_image(filepath, 800, 600, f"Card {i}"):
                print(f"✓ Created placeholder: {filepath.name}")
        
        time.sleep(0.5)
    
    # Download products (200)
    print("\n📥 Downloading 200 Product images...")
    product_urls = generate_product_urls(200)
    
    for i, url in enumerate(product_urls, 1):
        filepath = folders['products'] / f"product_{i:03d}.jpg"
        print(f"  [{i}/200] Downloading...", end=' ')
        
        if download_image(url, filepath):
            print(f"✓ Saved: {filepath.name}")
        else:
            if create_placeholder_image(filepath, 600, 600, f"Product {i}"):
                print(f"✓ Created placeholder: {filepath.name}")
        
        if i % 20 == 0:
            print(f"\n  → {i}/200 completed...")
        time.sleep(0.3)
    
    print("\n" + "="*60)
    print("✅ Download Complete!")
    print(f"📁 Location: {base_path}")
    print(f"   - Banners:  20 images → {folders['banners']}")
    print(f"   - Cards:    20 images → {folders['cards']}")
    print(f"   - Products: 200 images → {folders['products']}")
    print("="*60)

if __name__ == "__main__":
    main()
