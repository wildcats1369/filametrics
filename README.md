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

### 1. Make a Service Account & Download the Magic JSON Key 📄✨

This “JSON file” is like the *secret key* that lets our system talk to Google Analytics for you. Think of it like a house key — you don’t share it with strangers, but you need it to open the door.

#### Step-by-Step (no skipping!):

1. **Go to Google Cloud Console:**  
   👉 [https://console.cloud.google.com/iam-admin/serviceaccounts](https://console.cloud.google.com/iam-admin/serviceaccounts)  
   *(You may need to sign in with your Google account.)*
   ![alt text](./docs/Screenshot 2025-08-29 092911.png)

2. **Pick the right project:**  
   - On the top left, click the project drop-down.  
   - Select the project where your Analytics lives.

3. **Find your service account:**  
   - You should see the service account you already created.  
   - If not, click **Create Service Account** and give it a name like `service-account`.
   ![alt text](./docs/Screenshot 2025-08-29 093024.png)
   ![alt text](./docs/Screenshot 2025-08-29 093220.png)
   ![alt text](./docs/Screenshot 2025-08-29 093300.png)

4. **Add a key (the JSON):**  
   - On the service account row, click the three dots `⋮` → **Manage Keys**.  
   - Click **Add Key → Create new key**.  
   - Choose **JSON**.  
   - A `.json` file will download to your computer. 🎉  
   - **Save this file in a safe place** (you’ll upload it later to our system).
   ![alt text](./docs/Screenshot 2025-08-29 093356.png)
   ![alt text](./docs/Screenshot 2025-08-29 093406.png)
   ![alt text](./docs/Screenshot 2025-08-29 094014.png)

> ⚠️ Important: Don’t share this file with anyone, don’t put it on GitHub. Treat it like your password.

5. **Give the service account access to your GA property:**  
   - Go to [Google Analytics](https://analytics.google.com/).  
   - Click **Admin** (gear icon, bottom left).  
   - Under **Property**, click **Property Access Management**.  
   - Click **+ → Add users**.  
   - Paste your service account email (it looks like `name@project-id.iam.gserviceaccount.com`).  
   - Make sure you select the **Viewer** role.  
   - Click **Add**.  
   ![alt text](./docs/Screenshot 2025-08-29 093517.png)
   ![alt text](./docs/Screenshot 2025-08-29 093712.png)

---
### 2. Find Your GA4 Property ID 🔍

We also need your “GA property ID” — this tells us which Analytics property to read.

1. Go to [analytics.google.com](https://analytics.google.com/).  
2. Click **Admin** (gear icon, bottom left).  
3. Under **Property Settings**, look for PROPERTY ID: 46XXXXXXXXXXX.  
4. Copy that value, you’ll paste it into our system later.
![alt text](./docs/Screenshot 2025-08-29 094459.png)
![alt text](./docs/Screenshot 2025-08-29 094746.png)
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
