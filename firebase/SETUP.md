# Firebase Messaging Setup

## 1) Create Firebase Project
- Open Firebase Console.
- Create project (or use existing).
- Add a Web App and copy config values.

## 2) Enable Firebase Auth
- Open `Authentication` in Firebase Console.
- Enable Firebase Authentication for your project.
- No social provider is required for this module because Laravel issues custom tokens.

## 3) Enable Firestore
- Open `Firestore Database`.
- Create database in production mode.
- Deploy the rules from `firebase/firestore.rules`.
- Important: this messaging module uses **Cloud Firestore** (not Realtime Database rules).

## 4) Create Service Account Key
- Firebase Console -> Project Settings -> Service Accounts.
- Click `Generate new private key`.
- Keep the JSON secure. Do not commit it.

## 5) Fill Laravel `.env`
- Add Firebase client values and server credentials:

```
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_API_KEY=your-web-api-key
FIREBASE_AUTH_DOMAIN=your-project-id.firebaseapp.com
FIREBASE_STORAGE_BUCKET=your-project-id.appspot.com
FIREBASE_MESSAGING_SENDER_ID=...
FIREBASE_APP_ID=...
FIREBASE_MEASUREMENT_ID=
FIREBASE_DATABASE_URL=
FIREBASE_CLIENT_EMAIL=firebase-adminsdk-xxxx@your-project-id.iam.gserviceaccount.com
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n"
```

Important: keep `\n` escaped in `FIREBASE_PRIVATE_KEY`.

## 6) Apply Laravel Config
- Run:

```
php artisan config:clear
php artisan route:clear
```

## 7) Firestore Rules Deploy (optional CLI)
If you use Firebase CLI:

```
firebase deploy --only firestore:rules
```
