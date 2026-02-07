# 🔌 API Documentation - Kyle-HMS

RESTful API reference for Kyle Hospital Management System.

## Table of Contents

- [Overview](#overview)
- [Authentication](#authentication)
- [Error Handling](#error-handling)
- [Rate Limiting](#rate-limiting)
- [API Endpoints](#api-endpoints)
- [Response Format](#response-format)
- [Code Examples](#code-examples)

---

## Overview

Kyle-HMS provides a RESTful API for integration with mobile applications or third-party services.

### Base URL

```
Production: https://yourdomain.com/api
Development: http://localhost:8000/api
```

### API Version

Current version: **v1**

### Content Type

All requests and responses use JSON:

```http
Content-Type: application/json
Accept: application/json
```

---

## Authentication

Kyle-HMS uses Laravel Sanctum for API authentication with token-based authentication.

### Register User

Create a new user account.

**Endpoint:** `POST /api/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "usertype": "patient"
}
```

**Response:** `201 Created`
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "usertype": "patient",
        "created_at": "2026-02-07T10:00:00.000000Z"
    },
    "token": "1|abcdef1234567890..."
}
```

---

### Login

Authenticate and receive access token.

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:** `200 OK`
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "usertype": "patient"
    },
    "token": "1|abcdef1234567890...",
    "token_type": "Bearer"
}
```

**Error Response:** `401 Unauthorized`
```json
{
    "message": "Invalid credentials"
}
```

---

### Logout

Revoke current access token.

**Endpoint:** `POST /api/logout`

**Headers:**
```http
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
    "message": "Logged out successfully"
}
```

---

### Using Authentication Token

Include the token in all authenticated requests:

```http
GET /api/appointments HTTP/1.1
Host: yourdomain.com
Authorization: Bearer 1|abcdef1234567890...
Accept: application/json
```

---

## Error Handling

### Error Response Format

All errors follow this structure:

```json
{
    "message": "Error description",
    "errors": {
        "field_name": [
            "Validation error message"
        ]
    }
}
```

### HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created |
| 204 | No Content | Resource deleted |
| 400 | Bad Request | Invalid request data |
| 401 | Unauthorized | Missing/invalid token |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

### Example Error Responses

**Validation Error (422):**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email field is required."
        ],
        "password": [
            "The password must be at least 8 characters."
        ]
    }
}
```

**Unauthorized (401):**
```json
{
    "message": "Unauthenticated."
}
```

**Forbidden (403):**
```json
{
    "message": "This action is unauthorized."
}
```

**Not Found (404):**
```json
{
    "message": "Resource not found."
}
```

---

## Rate Limiting

### Default Limits

- **Authenticated requests:** 60 requests per minute
- **Guest requests:** 30 requests per minute

### Rate Limit Headers

Every response includes rate limit information:

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1644234000
```

### Rate Limit Exceeded Response

**Status:** `429 Too Many Requests`

```json
{
    "message": "Too many requests. Please try again later.",
    "retry_after": 60
}
```

---

## API Endpoints

### User Profile

#### Get Current User

Get authenticated user's profile.

**Endpoint:** `GET /api/user`

**Headers:**
```http
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "usertype": "patient",
    "created_at": "2026-02-07T10:00:00.000000Z",
    "patient": {
        "id": 1,
        "phone": "1234567890",
        "date_of_birth": "1990-01-01",
        "gender": "male",
        "blood_type": "A+",
        "allergies": "None"
    }
}
```

#### Update Profile

Update user information.

**Endpoint:** `PUT /api/user/profile`

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "email": "john.new@example.com",
    "phone": "0987654321",
    "address": "123 Main St, City, Country"
}
```

**Response:** `200 OK`
```json
{
    "message": "Profile updated successfully",
    "user": {
        // Updated user object
    }
}
```

---

### Doctors

#### List All Doctors

Get all available doctors.

**Endpoint:** `GET /api/doctors`

**Query Parameters:**
- `specialty` (optional): Filter by specialty ID
- `is_available` (optional): Filter by availability (true/false)
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)

**Example Request:**
```http
GET /api/doctors?specialty=1&is_available=true&page=1
```

**Response:** `200 OK`
```json
{
    "data": [
        {
            "id": 1,
            "user": {
                "id": 2,
                "name": "Dr. John Smith",
                "email": "dr.smith@kylehms.com"
            },
            "specialty": {
                "id": 1,
                "name": "Cardiology",
                "description": "Heart and cardiovascular system"
            },
            "license_number": "MD123456",
            "qualifications": "MD, MBBS",
            "years_of_experience": 10,
            "bio": "Experienced cardiologist...",
            "is_available": true,
            "profile_image_url": "https://..."
        }
    ],
    "links": {
        "first": "https://api.example.com/doctors?page=1",
        "last": "https://api.example.com/doctors?page=3",
        "prev": null,
        "next": "https://api.example.com/doctors?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "per_page": 15,
        "to": 15,
        "total": 45
    }
}
```

#### Get Doctor Details

Get specific doctor information.

**Endpoint:** `GET /api/doctors/{id}`

**Response:** `200 OK`
```json
{
    "id": 1,
    "user": {
        "id": 2,
        "name": "Dr. John Smith",
        "email": "dr.smith@kylehms.com"
    },
    "specialty": {
        "id": 1,
        "name": "Cardiology"
    },
    "license_number": "MD123456",
    "qualifications": "MD, MBBS, Cardiology Specialist",
    "years_of_experience": 10,
    "bio": "Experienced cardiologist with focus on...",
    "is_available": true,
    "profile_image_url": "https://...",
    "upcoming_schedules": [
        {
            "id": 5,
            "schedule_date": "2026-02-10",
            "start_time": "09:00:00",
            "end_time": "17:00:00",
            "available_slots": 12
        }
    ]
}
```

---

### Specialties

#### List All Specialties

Get all medical specialties.

**Endpoint:** `GET /api/specialties`

**Response:** `200 OK`
```json
{
    "data": [
        {
            "id": 1,
            "name": "Cardiology",
            "description": "Treatment of heart and cardiovascular system",
            "doctor_count": 5
        },
        {
            "id": 2,
            "name": "Neurology",
            "description": "Treatment of nervous system disorders",
            "doctor_count": 3
        }
    ]
}
```

---

### Schedules

#### Get Doctor Schedules

Get available schedules for a specific doctor.

**Endpoint:** `GET /api/doctors/{doctorId}/schedules`

**Query Parameters:**
- `from_date` (optional): Start date (YYYY-MM-DD)
- `to_date` (optional): End date (YYYY-MM-DD)
- `status` (optional): active, cancelled, completed

**Example Request:**
```http
GET /api/doctors/1/schedules?from_date=2026-02-10&to_date=2026-02-17&status=active
```

**Response:** `200 OK`
```json
{
    "data": [
        {
            "id": 1,
            "doctor_id": 1,
            "schedule_date": "2026-02-10",
            "start_time": "09:00:00",
            "end_time": "17:00:00",
            "duration_per_appointment": 30,
            "max_appointments": 16,
            "booked_appointments": 4,
            "available_slots": 12,
            "status": "active",
            "time_slots": [
                "09:00", "09:30", "10:00", "10:30", "11:00",
                "11:30", "13:00", "13:30", "14:00", "14:30",
                "15:00", "15:30", "16:00", "16:30"
            ]
        }
    ]
}
```

---

### Appointments

#### List Appointments

Get appointments for authenticated user.

**Endpoint:** `GET /api/appointments`

**Headers:**
```http
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (optional): pending, confirmed, completed, cancelled, no_show, expired
- `page` (optional): Page number

**Response:** `200 OK`
```json
{
    "data": [
        {
            "id": 1,
            "appointment_number": "APT20260207ABCD",
            "patient": {
                "id": 1,
                "name": "John Doe"
            },
            "doctor": {
                "id": 1,
                "name": "Dr. John Smith",
                "specialty": "Cardiology"
            },
            "schedule": {
                "schedule_date": "2026-02-10",
                "start_time": "09:00:00"
            },
            "appointment_time": "09:00:00",
            "status": "pending",
            "reason": "Regular checkup",
            "notes": null,
            "created_at": "2026-02-07T10:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 10
    }
}
```

#### Get Appointment Details

Get specific appointment information.

**Endpoint:** `GET /api/appointments/{id}`

**Response:** `200 OK`
```json
{
    "id": 1,
    "appointment_number": "APT20260207ABCD",
    "patient": {
        "id": 1,
        "name": "John Doe",
        "phone": "1234567890",
        "email": "john@example.com"
    },
    "doctor": {
        "id": 1,
        "name": "Dr. John Smith",
        "specialty": "Cardiology",
        "qualifications": "MD, MBBS"
    },
    "schedule": {
        "id": 1,
        "schedule_date": "2026-02-10",
        "start_time": "09:00:00",
        "end_time": "17:00:00"
    },
    "appointment_time": "09:00:00",
    "full_date_time": "February 10, 2026 at 9:00 AM",
    "status": "pending",
    "status_badge_class": "bg-yellow-100 text-yellow-800",
    "reason": "Regular checkup",
    "notes": null,
    "can_cancel": true,
    "created_at": "2026-02-07T10:00:00.000000Z",
    "updated_at": "2026-02-07T10:00:00.000000Z"
}
```

#### Create Appointment

Book a new appointment.

**Endpoint:** `POST /api/appointments`

**Headers:**
```http
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "schedule_id": 1,
    "appointment_time": "09:00",
    "reason": "Regular checkup",
    "notes": "First visit"
}
```

**Response:** `201 Created`
```json
{
    "message": "Appointment booked successfully",
    "data": {
        "id": 1,
        "appointment_number": "APT20260207ABCD",
        "patient_id": 1,
        "schedule_id": 1,
        "appointment_time": "09:00:00",
        "status": "pending",
        "reason": "Regular checkup",
        "notes": "First visit",
        "created_at": "2026-02-07T10:00:00.000000Z"
    }
}
```

**Error Response:** `422 Unprocessable Entity`
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "appointment_time": [
            "The selected time slot is not available."
        ]
    }
}
```

#### Update Appointment Status

Update appointment status (Doctor/Admin only).

**Endpoint:** `PATCH /api/appointments/{id}/status`

**Request Body:**
```json
{
    "status": "confirmed"
}
```

**Valid status transitions:**
- pending → confirmed
- pending/confirmed → completed
- pending/confirmed → cancelled
- confirmed → no_show

**Response:** `200 OK`
```json
{
    "message": "Appointment status updated successfully",
    "data": {
        "id": 1,
        "status": "confirmed",
        "updated_at": "2026-02-07T11:00:00.000000Z"
    }
}
```

#### Cancel Appointment

Cancel an appointment.

**Endpoint:** `DELETE /api/appointments/{id}`

**Response:** `200 OK`
```json
{
    "message": "Appointment cancelled successfully"
}
```

**Error Response:** `403 Forbidden`
```json
{
    "message": "Cannot cancel this appointment"
}
```

---

### Medical Records

#### List Medical Records

Get medical records for authenticated patient.

**Endpoint:** `GET /api/medical-records`

**Headers:**
```http
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number
- `per_page` (optional): Items per page

**Response:** `200 OK`
```json
{
    "data": [
        {
            "id": 1,
            "patient_id": 1,
            "doctor": {
                "id": 1,
                "name": "Dr. John Smith",
                "specialty": "Cardiology"
            },
            "visit_date": "2026-02-05",
            "diagnosis": "Hypertension",
            "treatment": "Prescribed medication",
            "prescription": "Lisinopril 10mg, once daily",
            "notes": "Patient advised to monitor blood pressure",
            "has_file": true,
            "file_name": "lab_results.pdf",
            "created_at": "2026-02-05T15:30:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 5
    }
}
```

#### Get Medical Record Details

Get specific medical record.

**Endpoint:** `GET /api/medical-records/{id}`

**Response:** `200 OK`
```json
{
    "id": 1,
    "patient": {
        "id": 1,
        "name": "John Doe",
        "date_of_birth": "1990-01-01",
        "blood_type": "A+",
        "allergies": "None"
    },
    "doctor": {
        "id": 1,
        "name": "Dr. John Smith",
        "specialty": "Cardiology",
        "license_number": "MD123456"
    },
    "appointment_id": 1,
    "visit_date": "2026-02-05",
    "diagnosis": "Hypertension",
    "treatment": "Lifestyle modification and medication",
    "prescription": "Lisinopril 10mg, once daily\nFollow up in 2 weeks",
    "notes": "Patient advised to monitor blood pressure daily",
    "file_path": "medical_records/1234567890_lab_results.pdf",
    "file_name": "lab_results.pdf",
    "file_type": "application/pdf",
    "file_size": 245760,
    "file_url": "https://yourdomain.com/storage/medical_records/...",
    "created_at": "2026-02-05T15:30:00.000000Z"
}
```

---

## Response Format

### Success Response

```json
{
    "data": {
        // Resource data
    },
    "message": "Success message",
    "meta": {
        // Pagination or additional metadata
    }
}
```

### Collection Response (Paginated)

```json
{
    "data": [
        // Array of resources
    ],
    "links": {
        "first": "...",
        "last": "...",
        "prev": null,
        "next": "..."
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "per_page": 15,
        "to": 15,
        "total": 75
    }
}
```

---

## Code Examples

### JavaScript (Fetch API)

```javascript
// Login
async function login(email, password) {
    const response = await fetch('https://yourdomain.com/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    
    if (response.ok) {
        localStorage.setItem('token', data.token);
        return data.user;
    } else {
        throw new Error(data.message);
    }
}

// Get appointments
async function getAppointments() {
    const token = localStorage.getItem('token');
    
    const response = await fetch('https://yourdomain.com/api/appointments', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    return await response.json();
}

// Book appointment
async function bookAppointment(scheduleId, time, reason) {
    const token = localStorage.getItem('token');
    
    const response = await fetch('https://yourdomain.com/api/appointments', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            schedule_id: scheduleId,
            appointment_time: time,
            reason: reason
        })
    });
    
    return await response.json();
}
```

### PHP (cURL)

```php
<?php

function login($email, $password) {
    $ch = curl_init('https://yourdomain.com/api/login');
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'email' => $email,
        'password' => $password
    ]));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

function getAppointments($token) {
    $ch = curl_init('https://yourdomain.com/api/appointments');
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
```

### Python (Requests)

```python
import requests

BASE_URL = "https://yourdomain.com/api"

def login(email, password):
    response = requests.post(
        f"{BASE_URL}/login",
        json={"email": email, "password": password},
        headers={"Accept": "application/json"}
    )
    return response.json()

def get_appointments(token):
    response = requests.get(
        f"{BASE_URL}/appointments",
        headers={
            "Authorization": f"Bearer {token}",
            "Accept": "application/json"
        }
    )
    return response.json()

def book_appointment(token, schedule_id, time, reason):
    response = requests.post(
        f"{BASE_URL}/appointments",
        json={
            "schedule_id": schedule_id,
            "appointment_time": time,
            "reason": reason
        },
        headers={
            "Authorization": f"Bearer {token}",
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
    )
    return response.json()
```

---

## Testing the API

### Using Postman

1. Import the collection (if provided)
2. Set environment variables:
   - `base_url`: https://yourdomain.com/api
   - `token`: Your Bearer token
3. Test endpoints

### Using cURL

```bash
# Login
curl -X POST https://yourdomain.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Get appointments (authenticated)
curl -X GET https://yourdomain.com/api/appointments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"

# Book appointment
curl -X POST https://yourdomain.com/api/appointments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"schedule_id":1,"appointment_time":"09:00","reason":"Checkup"}'
```

---

## Changelog

### Version 1.0 (2026-02-07)
- Initial API release
- Authentication endpoints
- User profile management
- Doctor listing and details
- Specialty catalog
- Schedule viewing
- Appointment CRUD operations
- Medical records viewing

---

## Support

For API support:
- Email: nounsunheng290503@gmail.com
- GitHub Issues: https://github.com/nounsunheng/Kyle-HMS/issues
- Documentation: https://github.com/nounsunheng/Kyle-HMS

---

**API Documentation Version**: 1.0  
**Last Updated**: February 7, 2026  
**For**: Kyle-HMS v1.0
