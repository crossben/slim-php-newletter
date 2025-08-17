# Slim PHP Newsletter API
A simple and lightweight API for managing newsletter subscriptions, built with [Slim Framework](https://www.slimframework.com/) and PHP.

## Features

- Easy integration with any frontend
- RESTful endpoints for subscribing and unsubscribing
- Minimal dependencies and fast setup

## Getting Started

1. **Clone the repository:**
    ```bash
    git clone https://github.com/crossben/slim-php-newsletter.git
    cd slim-php-newsletter
    ```
2. **Install dependencies with Composer:**
    ```bash
    composer install
    ```
3. **Configure your environment:**  
    Copy `.env.example` to `.env` and update settings as needed.
4. **Start the API server:**
    ```bash
    php -S localhost:8080 -t public
    ```

## API Endpoints

### Subscribe

- **Endpoint:** `POST http://localhost:8080/subscribe/{email}`
- **Request Body:**
  ```json
  {
     "email": "example@example.com"
  }
  ```
- **Success Response:**
  ```json
  {
     "message": "Subscribed successfully!"
  }
  ```

### Unsubscribe

- **Endpoint:** `DELETE http://localhost:8080/unsubscribe/{email}`
- **Example:**  
  `http://localhost:8080/unsubscribe/example@example.com`
- **Success Response:**
  ```json
  {
     "message": "Unsubscribed successfully!"
  }
  ```
- **Error Responses:**
  ```json
  {
     "error": "Email not found"
  }
  ```
  ```json
  {
     "error": "Invalid email"
  }
  ```

## License

MIT License