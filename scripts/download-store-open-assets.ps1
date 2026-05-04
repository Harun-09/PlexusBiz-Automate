$ErrorActionPreference = 'Stop'

Add-Type -AssemblyName System.Drawing

$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$productDir = Join-Path $root 'public\images\store\products'
$bannerDir = Join-Path $root 'public\images\store\banners'
$sourceLog = Join-Path $root 'public\images\store\SOURCES.txt'
$tempDir = Join-Path ([System.IO.Path]::GetTempPath()) 'plexusbiz-store-assets'

New-Item -ItemType Directory -Force -Path $productDir, $bannerDir, $tempDir | Out-Null

$commonsBase = 'https://commons.wikimedia.org/wiki'

$sources = @{
    adapterCable = @{ File = 'ATX_power_supply_adapter_cable_R7309257_wp.jpg'; License = 'Free Art License/GFDL/CC'; Credit = 'Rainer Knaepper / Wikimedia Commons' }
    camera = @{ File = 'Surveillance_camera.jpg'; License = 'CC0 1.0'; Credit = 'Neslihan Turan / Wikimedia Commons' }
    charger = @{ File = 'USB_Type-C_Cable_-_iPad_USB-C_Charger_(45640822114).jpg'; License = 'CC BY 2.0'; Credit = 'Tony Webster / Wikimedia Commons' }
    coolingFan = @{ File = 'Computer_Cooler.jpg'; License = 'CC BY-SA 3.0/GFDL'; Credit = 'Vlad2000Plus / Wikimedia Commons' }
    cooler = @{ File = 'Computer_Cooler.jpg'; License = 'CC BY-SA 3.0/GFDL'; Credit = 'Vlad2000Plus / Wikimedia Commons' }
    cpu = @{ File = 'Am486dx2-66_(11).jpg'; License = 'CC0 1.0'; Credit = 'Oligopolism / Wikimedia Commons' }
    desktopBundle = @{ File = '32-bit-desktop.jpg'; License = 'CC0 1.0'; Credit = 'Batocera Team / Wikimedia Commons' }
    deskSetup = @{ File = 'Desk_Setup_(Unsplash).jpg'; License = 'CC0 1.0'; Credit = 'Unsplash via Wikimedia Commons' }
    dualMonitor = @{ File = "Designer's_two-screen_setup_(Unsplash).jpg"; License = 'CC0 1.0'; Credit = 'Unsplash via Wikimedia Commons' }
    filament = @{ File = '3D_Printing_Filament.jpg'; License = 'CC BY 2.0'; Credit = 'Creative Tools / Wikimedia Commons' }
    gpu = @{ File = 'Sapphire_ATI_Radeon_HD_2400_XT.jpg'; License = 'Public domain'; Credit = 'Tors / Wikimedia Commons' }
    gpuAlt = @{ File = 'Sapphire_ATI_Radeon_HD_2400_XT.jpg'; License = 'Public domain'; Credit = 'Tors / Wikimedia Commons' }
    gpuClassic = @{ File = 'Geforce8800gts.jpg'; License = 'Public domain'; Credit = 'Tyro / Wikimedia Commons' }
    hdd = @{ File = 'IBM_DPRA-21215.png'; License = 'CC0 1.0'; Credit = 'NickW1129 / Wikimedia Commons' }
    hddAlt = @{ File = 'IBM_DPRA-21215.png'; License = 'CC0 1.0'; Credit = 'NickW1129 / Wikimedia Commons' }
    headphones = @{ File = 'Headphones_(56330).jpg'; License = 'CC0 1.0'; Credit = 'Wikimedia Commons contributor' }
    kitchen = @{ File = 'Oster_2-Slice_Toaster.jpg'; License = 'CC0 1.0'; Credit = 'Wikimedia Commons contributor' }
    laptop = @{ File = 'AVANT-P870DMG-GAMING-LAPTOP.png'; License = 'CC BY-SA 4.0'; Credit = 'Nikitarama / Wikimedia Commons' }
    laptopAlt = @{ File = 'HP_Victus_15_gaming_laptop_side_view.jpg'; License = 'CC0 1.0'; Credit = 'Laziz940 / Wikimedia Commons' }
    laptopLiving = @{ File = 'Laptop_in_the_living_room_(Unsplash).jpg'; License = 'CC0 1.0'; Credit = 'Oliur Rahman / Unsplash via Wikimedia Commons' }
    laser = @{ File = '3D_printer.png'; License = 'CC BY-SA 4.0'; Credit = 'Nodin Cutfeet / Wikimedia Commons' }
    livingRoom = @{ File = 'Laptop_in_the_living_room_(Unsplash).jpg'; License = 'CC0 1.0'; Credit = 'Oliur Rahman / Unsplash via Wikimedia Commons' }
    microSd = @{ File = 'Intel_512G_M2_Solid_State_Drive.png'; License = 'CC BY-SA 4.0'; Credit = 'David290 / Wikimedia Commons' }
    monitor = @{ File = 'MonitorLCD_17in.jpg'; License = 'Public domain'; Credit = 'Julo / Wikimedia Commons' }
    motherboard = @{ File = 'Atx_computer_motherboard_with_cpu_and_fan.jpg'; License = 'Public domain'; Credit = 'Leon Brooks / Wikimedia Commons' }
    motherboardAlt = @{ File = 'Atx_computer_motherboard_with_cpu_and_fan.jpg'; License = 'Public domain'; Credit = 'Leon Brooks / Wikimedia Commons' }
    nas = @{ File = 'Network_Attached_Storage.jpg'; License = 'CC BY-SA 2.5'; Credit = 'Simon Haemmerle / Wikimedia Commons' }
    pc = @{ File = 'Miordenata02-nobackground.png'; License = 'Public domain'; Credit = 'Chaluco / Wikimedia Commons' }
    pcAlt = @{ File = '32-bit-desktop.jpg'; License = 'CC0 1.0'; Credit = 'Batocera Team / Wikimedia Commons' }
    pcCase = @{ File = 'Miordenata02-nobackground.png'; License = 'Public domain'; Credit = 'Chaluco / Wikimedia Commons' }
    printer3d = @{ File = '3D_printer.png'; License = 'CC BY-SA 4.0'; Credit = 'Nodin Cutfeet / Wikimedia Commons' }
    psu = @{ File = 'SS-400ES_ATX_power_supply.jpg'; License = 'Public domain'; Credit = 'Hitachi-Train / Wikimedia Commons' }
    qwerty = @{ File = 'Qwerty_Keyboard.JPG'; License = 'CC0 1.0'; Credit = 'MarkBuckawicki / Wikimedia Commons' }
    ram = @{ File = 'DDR_RAM-3.jpg'; License = 'Public domain'; Credit = 'Laszlo Szalai / Wikimedia Commons' }
    ramAlt = @{ File = 'Sodimm-ram-ddr.jpg'; License = 'Public domain'; Credit = 'M/ / Wikimedia Commons' }
    rgbSetup = @{ File = 'RGB_desktop_computer_setup_with_keyboard_and_monitor.jpg'; License = 'CC0 1.0'; Credit = 'SankalpSasnur / Wikimedia Commons' }
    router = @{ File = 'Wireless_Router_(50841204223).jpg'; License = 'CC BY 2.0'; Credit = 'ajay_suresh / Wikimedia Commons' }
    ssd = @{ File = 'Intel_512G_M2_Solid_State_Drive.png'; License = 'CC BY-SA 4.0'; Credit = 'David290 / Wikimedia Commons' }
    workspace = @{ File = 'Desk_Setup_(Unsplash).jpg'; License = 'CC0 1.0'; Credit = 'Unsplash via Wikimedia Commons' }
}

$productAssignments = @{
    'board-asus-b760' = 'motherboardAlt'
    'board-rog-b550' = 'motherboard'
    'case-lancool' = 'pcCase'
    'charger-usbc' = 'charger'
    'cpu-category' = 'cpu'
    'cpu-intel-i5' = 'cpu'
    'cpu-ryzen-5500' = 'cpu'
    'desktop-acer' = 'pcCase'
    'desktop-bundle-monitor' = 'desktopBundle'
    'desktop-dell-slim' = 'pc'
    'desktop-hp-omni' = 'pcAlt'
    'email-deals-card' = 'laptopLiving'
    'filament-spool' = 'filament'
    'gpu-category' = 'gpuAlt'
    'gpu-sapphire-nitro' = 'gpu'
    'hdd-category' = 'hddAlt'
    'hdd-red-plus' = 'hdd'
    'hdd-red-plus-duo' = 'hdd'
    'hdd-red-plus-single' = 'hddAlt'
    'headset-scape' = 'headphones'
    'home-audio' = 'headphones'
    'keyboard-g915' = 'qwerty'
    'kitchen-appliance' = 'kitchen'
    'laptop-acer-aspire' = 'laptopAlt'
    'laptop-lenovo-v15' = 'laptopLiving'
    'laptop-msi-vector' = 'laptop'
    'laptop-msi-venture' = 'laptopAlt'
    'mesh-router' = 'router'
    'mini-pc-stick' = 'pcCase'
    'monitor-s3-120hz' = 'monitor'
    'nas-dxp-storage' = 'nas'
    'nas-dxp2800' = 'nas'
    'nas-dxp4800' = 'nas'
    'nas-synology-ds225' = 'nas'
    'pc-arc-a580' = 'pc'
    'pc-cyclone-aqua' = 'pcAlt'
    'pc-cyclone-bundle' = 'desktopBundle'
    'pc-shell-shocker' = 'pc'
    'printer-3d-corexy' = 'printer3d'
    'printer-engraver' = 'laser'
    'printer-resin' = 'printer3d'
    'psu-850w' = 'psu'
    'ram-ddr4-blue' = 'ramAlt'
    'ram-rgb-pro' = 'ram'
    'ram-vengeance-lpx' = 'ram'
    'sas-adapter' = 'adapterCable'
    'sd-card' = 'microSd'
    'security-camera' = 'camera'
    'shield-tv' = 'monitor'
    'ssd-category' = 'ssd'
    'tv-video' = 'monitor'
}

$shelfAssignments = @{
    'shelf-cooling-01' = 'cooler'
    'shelf-cooling-02' = 'coolingFan'
    'shelf-cooling-03' = 'cooler'
    'shelf-cooling-04' = 'coolingFan'
    'shelf-cooling-05' = 'cooler'
    'shelf-cooling-06' = 'adapterCable'
    'shelf-gaming-laptop-01' = 'pc'
    'shelf-gaming-laptop-02' = 'ram'
    'shelf-gaming-laptop-03' = 'gpu'
    'shelf-gaming-laptop-04' = 'laptop'
    'shelf-gaming-laptop-05' = 'pcCase'
    'shelf-gaming-laptop-06' = 'laptopAlt'
    'shelf-printing-01' = 'printer3d'
    'shelf-printing-02' = 'laser'
    'shelf-printing-03' = 'printer3d'
    'shelf-printing-04' = 'filament'
    'shelf-printing-05' = 'adapterCable'
    'shelf-printing-06' = 'psu'
    'shelf-consider-01' = 'printer3d'
    'shelf-consider-02' = 'laser'
    'shelf-consider-03' = 'printer3d'
    'shelf-consider-04' = 'filament'
    'shelf-consider-05' = 'adapterCable'
    'shelf-consider-06' = 'psu'
    'shelf-consider-07' = 'pcCase'
    'shelf-consider-08' = 'pc'
    'shelf-consider-09' = 'desktopBundle'
    'shelf-consider-10' = 'pcAlt'
    'shelf-consider-11' = 'desktopBundle'
    'shelf-consider-12' = 'gpu'
    'shelf-consider-13' = 'ram'
    'shelf-consider-14' = 'laptop'
    'shelf-consider-15' = 'pcCase'
    'shelf-consider-16' = 'nas'
}

$bannerAssignments = @{
    'brand-strip' = 'motherboard'
    'gaming-setup' = 'deskSetup'
    'hero-main' = 'rgbSetup'
    'laptop-sale' = 'laptopLiving'
    'memory-finder' = 'ram'
    'smart-home' = 'livingRoom'
    'tool-gaming-finder' = 'pcAlt'
    'tool-laptop-finder' = 'laptop'
    'tool-memory-finder' = 'ram'
    'tool-nas-builder' = 'nas'
    'tool-network-builder' = 'router'
    'tool-nuc-config' = 'pcCase'
    'tool-pc-builder' = 'desktopBundle'
    'tool-pc-upgrader' = 'motherboardAlt'
    'tool-psu-calculator' = 'psu'
    'tool-server-config' = 'nas'
    'tools-feature-01' = 'deskSetup'
    'tools-feature-02' = 'nas'
    'tools-feature-03' = 'desktopBundle'
    'tools-feature-04' = 'router'
    'tools-feature-05' = 'laptopLiving'
    'top-products' = 'dualMonitor'
    'workspace-gift' = 'workspace'
}

function Get-CommonsPageUrl([string] $fileName) {
    return "$commonsBase/File:$($fileName -replace ' ', '_')"
}

function Get-CommonsDownload([string] $sourceKey, [int] $width) {
    $source = $sources[$sourceKey]
    if (-not $source) {
        throw "Unknown source key: $sourceKey"
    }

    $encodedFile = [Uri]::EscapeDataString($source.File)
    $downloadUrl = "https://commons.wikimedia.org/wiki/Special:Redirect/file/$encodedFile" + "?width=$width"
    $extension = [System.IO.Path]::GetExtension($source.File)
    if ([string]::IsNullOrWhiteSpace($extension)) {
        $extension = '.img'
    }

    $target = Join-Path $tempDir ("$sourceKey$extension")
    if ((Test-Path $target) -and ((Get-Item $target).Length -gt 0)) {
        return $target
    }

    $client = New-Object System.Net.WebClient
    $client.Headers.Add('User-Agent', 'PlexusBizAutomateStoreAssets/1.0 local-development')

    for ($attempt = 1; $attempt -le 5; $attempt += 1) {
        try {
            Start-Sleep -Milliseconds (1300 * $attempt)
            $client.DownloadFile($downloadUrl, $target)
            break
        }
        catch {
            if ($attempt -eq 5) {
                throw
            }

            $delaySeconds = 5 * $attempt
            Write-Host "download retry $attempt for $($source.File); waiting $delaySeconds seconds"
            Start-Sleep -Seconds $delaySeconds
        }
    }

    return $target
}

function New-CanvasImage([string] $sourcePath, [string] $targetPath, [int] $width, [int] $height, [bool] $cover, [string] $format) {
    $stream = [System.IO.File]::OpenRead($sourcePath)
    try {
        $source = [System.Drawing.Image]::FromStream($stream)
        try {
            $canvas = New-Object System.Drawing.Bitmap $width, $height
            try {
                $graphics = [System.Drawing.Graphics]::FromImage($canvas)
                try {
                    $graphics.Clear([System.Drawing.Color]::White)
                    $graphics.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
                    $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
                    $graphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality

                    $scaleX = $width / $source.Width
                    $scaleY = $height / $source.Height
                    $scale = if ($cover) { [Math]::Max($scaleX, $scaleY) } else { [Math]::Min($scaleX, $scaleY) * 0.88 }
                    $drawWidth = [int] [Math]::Round($source.Width * $scale)
                    $drawHeight = [int] [Math]::Round($source.Height * $scale)
                    $x = [int] [Math]::Round(($width - $drawWidth) / 2)
                    $y = [int] [Math]::Round(($height - $drawHeight) / 2)

                    $graphics.DrawImage($source, $x, $y, $drawWidth, $drawHeight)
                }
                finally {
                    $graphics.Dispose()
                }

                if ($format -eq 'jpeg') {
                    $codec = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object { $_.MimeType -eq 'image/jpeg' } | Select-Object -First 1
                    $encoder = [System.Drawing.Imaging.Encoder]::Quality
                    $params = New-Object System.Drawing.Imaging.EncoderParameters 1
                    $params.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter $encoder, 88L
                    $canvas.Save($targetPath, $codec, $params)
                    $params.Dispose()
                }
                else {
                    $canvas.Save($targetPath, [System.Drawing.Imaging.ImageFormat]::Png)
                }
            }
            finally {
                $canvas.Dispose()
            }
        }
        finally {
            $source.Dispose()
        }
    }
    finally {
        $stream.Dispose()
    }
}

$allProducts = @{}
$productAssignments.GetEnumerator() | ForEach-Object { $allProducts[$_.Key] = $_.Value }
$shelfAssignments.GetEnumerator() | ForEach-Object { $allProducts[$_.Key] = $_.Value }

foreach ($entry in $allProducts.GetEnumerator()) {
    $download = Get-CommonsDownload $entry.Value 1200
    $target = Join-Path $productDir "$($entry.Key).png"
    New-CanvasImage $download $target 900 675 $false 'png'
    Write-Host "product $($entry.Key).png <- $($sources[$entry.Value].File)"
}

foreach ($entry in $bannerAssignments.GetEnumerator()) {
    $download = Get-CommonsDownload $entry.Value 1800
    $target = Join-Path $bannerDir "$($entry.Key).jpg"
    $bannerHeight = if ($entry.Key -eq 'brand-strip') { 360 } else { 620 }
    New-CanvasImage $download $target 1600 $bannerHeight $true 'jpeg'
    Write-Host "banner $($entry.Key).jpg <- $($sources[$entry.Value].File)"
}

$usedSourceKeys = [System.Collections.Generic.HashSet[string]]::new()
$allProducts.Values | ForEach-Object { [void] $usedSourceKeys.Add($_) }
$bannerAssignments.Values | ForEach-Object { [void] $usedSourceKeys.Add($_) }

$sourceLines = New-Object System.Collections.Generic.List[string]
$sourceLines.Add('PlexusBiz store imagery')
$sourceLines.Add('')
$sourceLines.Add('The files in public/images/store/products and public/images/store/banners were rendered from real open-license Wikimedia Commons images. Product card assets are white-canvas PNGs; banner assets are cropped JPGs with no embedded marketing text.')
$sourceLines.Add('')
$sourceLines.Add('Sources:')

foreach ($sourceKey in ($usedSourceKeys | Sort-Object)) {
    $source = $sources[$sourceKey]
    $sourceLines.Add("- ${sourceKey}: $($source.File)")
    $sourceLines.Add("  Page: $(Get-CommonsPageUrl $source.File)")
    $sourceLines.Add("  License: $($source.License)")
    $sourceLines.Add("  Credit: $($source.Credit)")
}

$sourceLines | Set-Content -Path $sourceLog -Encoding UTF8
Write-Host "sources written to $sourceLog"
