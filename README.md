# naqlthaqel# Dynamic Service Plugin API Documentation

## Introduction

The **Dynamic Service Plugin** provides REST API endpoints that allow external applications to interact with your custom services and integrate with WooCommerce. This API enables you to:

- Retrieve a list of available services with their custom data.
- Add a service to the WooCommerce cart programmatically.

---

## Base URL

All API endpoints are relative to the following base URL:


Replace `https://yourdomain.com` with your actual website domain.

---

## Table of Contents

- [Authentication](#authentication)
  - [Authentication Methods](#authentication-methods)
- [Endpoints](#endpoints)
  - [1. Get Services with Custom Data](#1-get-services-with-custom-data)
  - [2. Add Service to Cart](#2-add-service-to-cart)
- [Authentication Details](#authentication-details)
  - [Using Nonce Authentication](#using-nonce-authentication)
  - [Cookie Authentication](#cookie-authentication)
  - [Using JWT Authentication](#using-jwt-authentication)
- [Handling File Uploads](#handling-file-uploads)
- [Example Workflow](#example-workflow)
- [Error Handling](#error-handling)
- [Notes and Best Practices](#notes-and-best-practices)
- [Extending the API](#extending-the-api)
- [Frequently Asked Questions](#frequently-asked-questions)
- [Conclusion](#conclusion)
- [Additional Resources](#additional-resources)

---

## Authentication

- **Public Endpoints**: Some endpoints are publicly accessible and do not require authentication.
- **Protected Endpoints**: Endpoints that modify data (e.g., adding to the cart) require authentication.

### Authentication Methods

- **Cookie Authentication**: For requests made from within the site (e.g., AJAX requests), WordPress cookies handle authentication.
- **Nonce Authentication**: Use WordPress nonces for secure requests.
- **JWT Authentication**: For external applications, consider implementing JWT (JSON Web Tokens) or OAuth for authentication.

---

## Endpoints

### 1. Get Services with Custom Data

- **Endpoint**: `/services`
- **Method**: `GET`
- **Authentication**: Not required (public access)

#### Description

Retrieves a list of all services, including their custom fields and associated data.

#### Request

**URL**


**Full URL Example**


**Parameters**

- None

#### Response

- **Status Code**: `200 OK`
- **Content Type**: `application/json`
- **Body**: An array of service objects, each containing:
  - `id`: The service ID.
  - `title`: The service title.
  - `content`: The service content.
  - `base_price`: The base price of the service.
  - `wc_product_id`: The associated WooCommerce product ID.
  - `form_fields`: An array of form fields and their options.

**Example Response**

```json
[
  {
    "id": 123,
    "title": "Service One",
    "content": "Description of Service One.",
    "base_price": 100,
    "wc_product_id": 456,
    "form_fields": [
      {
        "label": "Option 1",
        "type": "dropdown",
        "options": [
          {
            "option_label": "Choice A",
            "option_price": 10
          },
          {
            "option_label": "Choice B",
            "option_price": 20
          }
        ]
      },
      {
        "label": "Notes",
        "type": "text"
      }
    ]
  },
  {
    "id": 124,
    "title": "Service Two",
    "content": "Description of Service Two.",
    "base_price": 150,
    "wc_product_id": 457,
    "form_fields": [
      {
        "label": "Size",
        "type": "dropdown",
        "options": [
          {
            "option_label": "Small",
            "option_price": 0
          },
          {
            "option_label": "Large",
            "option_price": 30
          }
        ]
      }
    ]
  }
]
