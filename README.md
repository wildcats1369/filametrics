# Filametrics

> **Access multiple Google Analytics accounts in one place — beautifully integrated with Laravel Filament.**

---

## Features 🚀

- ✅ Connect multiple Google Analytics accounts
- 📈 View unified dashboards with charts & infolists
- 📄 Export reports to PDF (Charts + Data)
- 🎯 Built as a Laravel Filament package (easy to extend)

---

## Requirements

- PHP 8.1+
- Laravel 10+
- Node.js + NPM
- Google Analytics Service Account (JSON)

---

## Installation

1. Install the package:

```bash
composer require wildcats1369/filametrics
```

2. Publish assets, configs, and migrations:

```bash
php artisan vendor:publish --provider="wildcats1369\\Filametrics\\Providers\\FilametricsServiceProvider"
```

3. Run migrations:

```bash
php artisan migrate
```

4. Install Puppeteer (for PDF export):

```bash
npm install puppeteer --save
```

---

## Setting up Google Analytics 🎛️

### 1. Generate Service Account & JSON

Follow [this quick guide](https://console.cloud.google.com/apis/credentials) to create a Service Account + JSON key.

> **Make sure to:**
> - Share GA property access with the Service Account email (Viewer role)
> - Save the JSON file, you need to upload it to the system later.

### 2. Get your GA4 Property ID

1. Go to [analytics.google.com](https://analytics.google.com/)
2. Admin → Property Settings → **Measurement ID** (ex: `G-XXXXXXXXXX`)

## How to Create a Site in Filametrics 🌐

After installing, you can create a "Site" to link a Google Analytics property.

### Step 1: Create a Site Record

Go to **Filametrics > Sites** in Filament admin panel.

Fill in:
- **Site Name** (whatever you want)
- **Property ID** (from GA)


### Step 2: Add GA Credentials

Upload the Service Account JSON to the **Site** form.

Hit **Save**.

---

## How to Export a PDF 📄

1. Visit the **public PDF page** for your Site:

```
http://your-app.test/filametrics/{site-id}/pdf
```

2. It renders a Filament Page with charts + infolists (publicly accessible).

3. Export by clicking "Download PDF."

> **Pro Tip:** You can call `Browsershot::url($url)->save('report.pdf');` in a command if you want to automate this.

---

## Development / Extend 💡

- Main namespace: `wildcats1369\Filametrics\`
- Entry Service Provider: `FilametricsServiceProvider`
- Filament Pages/Resources under `/src/Filament`

### Dependencies

- [bezhansalleh/filament-google-analytics](https://github.com/bezhansalleh/filament-google-analytics)
- [spatie/browsershot](https://github.com/spatie/browsershot)
- [mpdf/mpdf](https://github.com/mpdf/mpdf)

---

## Contributing ❤️

Feel free to PR or open issues! Improvements are welcome.

---

## License

MIT
