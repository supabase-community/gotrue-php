# Supabase `gotrue-php` examples 

Examples of how to interact with the `gotrue-php` library.

```
.
├── auth-admin
│   ├── create-a-user
│   │               ├── auto-confirm-the.users-email.php
│   │               ├── auto-confirm-the.users-phone-number.php
│   │               └── with-custom-user-metadata.php
│   ├── delete-a-user
│   │               └── removes-a-users.php
│   ├── generate-an-email-link
│   │               ├── generate-a-magic-link.php
│   │               ├── generate-a-recovery-link.php
│   │               ├── generate-a-signup-link.php
│   │               ├── generate-an-invite-link.php
│   │               └── generate-links-to-change-current-email-address.php
│   ├── list-all-factors-for-a-user
│   │               └── list-all-factors-for-a-user.php
│   ├── list-all-users
│   │               ├── get-a-page-of-users.php
│   │               └── paginated-list-of-users.php
│   ├── send-a-password-reset-request
│   │               └── reset-password.php
│   ├── send-an-email-invite-link
│   │               └── invite-a-user.php
│   ├── update-a-user
│   │               ├── confirms-a-users-email-address.php
│   │               ├── confirms-a-users-phone-number.php
│   │               ├── updates-a-users-app-metadata.php
│   │               ├── updates-a-users-email.php
│   │               ├── updates-a-users-metadata.php
│   │               └── updates-a-users-password.php
│   └── retrive-a-user.php
└── authenticator-assurance-level
│   └── invite-a-user.php
├── create-a-user
│   ├── sign-up-with-a-redirect-URL.php
│   ├── sign-up-with-additional-user-metadata.php
│   └── sign-up.php
├── enroll-a-factor
│   └── enroll-a-factor.php
├── retrive-a-new-session
│   └── refresh-session-using-a-refresh-token.php
├── retrive-a-session
│   └── get-the-session-data.php
├── retrive-a-user
│   ├── get-the-logged-in-user-with-a-custom access-token-jwt.php
│   └── get-the-logged-in-user-with-the-current-existing-session.php
├── set-the-session-data
│   └── refresh-the-session.php
├── sign-in-a-user
│   └── sign-in-with-mail-and-password.php
├── sign-in-a-user-through-OTP
│   ├── sign-in-with-mail.php
│   ├── sign-in-with-SMS-OTP.php
│   └── sign-in-with-whatsApp-OTP.php
├── sign-out-a-user
│   └── sign-out.php
└── update-a-user
    ├── update-the-email-for-an-authenticated-user.php
    ├── update-the-password-for-an-authenticated-user.php
    └── update-the-users-metadata.php

```

## Setup
Clone the repository locally.

Install the dependencies `composer install` 

### Setup the Env
To obtain the API Access Details, please sign into your Supabase account. 

```
cp .env.example examples/.env
```

#### For the `REFERENCE_ID`
Once signed on to the dashboard, navigate to, Project >> Project Settings >> General settings. Copy the Reference ID for use in the `.env`.

#### For the `API_KEY`
Once signed on to the dashboard, navigate to, Project >> Project Settings >> API >> Project API keys. Choose either the `anon` `public` or the `service_role` key.

Populate the `examples/.env` to include `REFERENCE_ID` and `API_KEY`.

## Running Examples

```
cd examples
php auth-admin/retrive-a-user.php
```
