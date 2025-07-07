# ARMessenger Backend

**ARMessenger** is a real-time messaging platform built with Laravel 11. This backend serves both the web frontend and a mobile Flutter application. It includes encrypted chat, contact/invitation management, and real-time message delivery using Laravel Reverb.

## 🔐 Features

- User authentication with Sanctum (API Token-based)
- Realtime messaging with Laravel Reverb (WebSockets)
- Fully encrypted messages using per-user encryption keys
- Contact management (add/remove)
- Invitations system (send, accept, refuse)
- Message pagination & scroll optimization
- Event-driven architecture using Services and Repositories
- Notifications and real-time updates with broadcasting

## 📁 Tech Stack

- Laravel 11 / PHP 8.3
- Laravel Reverb (WebSockets)
- Sanctum (API Auth)
- MySQL
- Custom Laravel Package: `full-encryption`
- Clean code structure: Services, Repositories, Traits

