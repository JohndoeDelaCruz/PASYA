# PASYA Crop Production Management API Documentation

## Overview
This API provides RESTful endpoints for managing crop data in the PASYA (Precision Agriculture System for Yield Analysis) application. All API endpoints are prefixed with `/api/`.

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
Currently, API endpoints are accessible without authentication. In production, consider implementing API token authentication.

## Content Type
All requests should use `Content-Type: application/json` for POST/PUT requests.
All responses return JSON format.

---

## Crop Production Management Endpoints

### 1. List All Crops
**GET** `/api/crops`

Get a paginated list of all crops with optional filtering.

**Query Parameters:**
- `per_page` (optional): Number of items per page (10, 15, 25, 50, 100). Default: 15
- `search` (optional): Search term to filter by crop name, municipality, or year
- `municipality` (optional): Filter by specific municipality
- `crop` (optional): Filter by specific crop name
- `sort` (optional): Sort direction (`asc` or `desc`)

**Example Request:**
```
GET /api/crops?per_page=25&municipality=La Trinidad&crop=Cabbage
```

**Response:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 150,
    "last_page": 10,
    "from": 1,
    "to": 15
  },
  "reference_data": {
    "municipalities": [...],
    "highland_crops": [...]
  }
}
```

### 2. Create New Crop
**POST** `/api/crops`

Create a new crop entry.

**Request Body:**
```json
{
  "municipality": "La Trinidad",
  "farm_type": "upland",
  "year": 2025,
  "crop_name": "Cabbage",
  "area_planted": 10.5,
  "area_harvested": 10.0,
  "production_mt": 250.75,
  "productivity_mt_ha": 25.075,
  "cropID": "CBG-2025-001",
  "cropCategory": "Cruciferous Vegetables",
  "cropDaysToMaturity": 75,
  "productionMonth": "March",
  "productionFarmType": "Irrigated"
}
```

**Validation Rules:**
- `municipality`: Required, must be one of the 13 Benguet municipalities
- `farm_type`: Required, must be one of: irrigated, rainfed, upland, lowland
- `year`: Required, integer between 2000-2030
- `crop_name`: Required, must be one of the 10 highland crops
- `area_planted`: Required, numeric, minimum 0.01, maximum 99999.99
- `area_harvested`: Required, numeric, minimum 0.01, maximum 99999.99
- `production_mt`: Required, numeric, minimum 0.01, maximum 99999999.99  
- `productivity_mt_ha`: Optional, numeric, minimum 0, maximum 99999.99
- `cropID`: Optional, string, maximum 50 characters
- `cropCategory`: Optional, string, maximum 100 characters (auto-populated for highland crops)
- `cropDaysToMaturity`: Optional, integer between 1-365 (auto-populated for highland crops)
- `productionMonth`: Optional, string, must be one of: January, February, March, April, May, June, July, August, September, October, November, December
- `productionFarmType`: Optional, string, must be one of: Irrigated, Rainfed

**Response:**
```json
{
  "success": true,
  "message": "Crop created successfully!",
  "data": {
    "id": 123,
    "municipality": "La Trinidad",
    "farm_type": "upland",
    "year": 2025,
    "crop_name": "Cabbage",
    "area_planted": 10.5,
    "area_harvested": 10.0,
    "production_mt": 250.75,
    "productivity_mt_ha": 25.075,
    "cropID": "CBG-2025-001",
    "cropCategory": "Cruciferous Vegetables",
    "cropDaysToMaturity": 75,
    "productionMonth": "March",
    "productionFarmType": "Irrigated",
    "created_at": "2025-09-25T21:00:00.000000Z",
    "updated_at": "2025-09-25T21:00:00.000000Z"
  }
}
```

### 3. Get Specific Crop
**GET** `/api/crops/{id}`

Get details of a specific crop by ID.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "municipality": "La Trinidad",
    "farm_type": "upland",
    "year": 2025,
    "crop_name": "Cabbage",
    "area_planted": 10.5,
    "area_harvested": 10.0,
    "production_mt": 250.75,
    "productivity_mt_ha": 25.075,
    "cropID": "CBG-2025-001",
    "cropCategory": "Cruciferous Vegetables",
    "cropDaysToMaturity": 75,
    "productionMonth": "March",
    "productionFarmType": "Irrigated",
    "farmer": {...}
  }
}
```

### 4. Update Crop
**PUT** `/api/crops/{id}`

Update an existing crop entry.

**Request Body:** Same as Create Crop

**Response:**
```json
{
  "success": true,
  "message": "Crop updated successfully!",
  "data": {...}
}
```

### 5. Delete Crop
**DELETE** `/api/crops/{id}`

Delete (soft delete) a specific crop.

**Response:**
```json
{
  "success": true,
  "message": "Crop deleted successfully!"
}
```

### 6. Batch Delete Crops
**POST** `/api/crops/batch-delete`

Delete multiple crops at once.

**Request Body:**
```json
{
  "ids": [1, 2, 3, 4, 5]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Successfully deleted 5 crop(s)",
  "deleted_count": 5
}
```

### 7. Search Crops
**GET** `/api/crops/search/{term}`

Search crops by term across crop name, municipality, and year.

**Query Parameters:**
- `per_page` (optional): Number of items per page. Default: 15

**Example:**
```
GET /api/crops/search/cabbage?per_page=20
```

**Response:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {...},
  "search_term": "cabbage"
}
```

### 8. Filter Crops
**GET** `/api/crops/filter/{municipality}/{crop?}`

Filter crops by municipality and optionally by crop type.

**Parameters:**
- `municipality`: Required municipality name
- `crop`: Optional crop name

**Examples:**
```
GET /api/crops/filter/La Trinidad
GET /api/crops/filter/La Trinidad/Cabbage
```

---

## Reference Data Endpoints

### 1. Get Municipalities
**GET** `/api/reference/municipalities`

Get list of all 13 Benguet municipalities.

**Response:**
```json
{
  "success": true,
  "data": [
    "Atok", "Bakun", "Bokod", "Buguias", "Itogon", 
    "Kabayan", "Kapangan", "Kibungan", "La Trinidad", 
    "Mankayan", "Sablan", "Tuba", "Tublay"
  ]
}
```

### 2. Get Highland Crops
**GET** `/api/reference/highland-crops`

Get list of all 10 highland crops.

**Response:**
```json
{
  "success": true,
  "data": [
    "Cabbage", "Carrots", "Broccoli", "Potato", "Lettuce",
    "Cauliflower", "Bell Pepper", "Onion", "Tomato", "Chinese Cabbage (Pechay)"
  ]
}
```

### 3. Get Farm Types
**GET** `/api/reference/farm-types`

Get list of all farm types.

**Response:**
```json
{
  "success": true,
  "data": ["irrigated", "rainfed", "upland", "lowland"]
}
```

---

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "municipality": ["The municipality field is required."],
    "crop_name": ["The selected crop name is invalid."]
  }
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "An error occurred: Database connection failed"
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Crop not found"
}
```

---

## Example Usage with cURL

### Create a new crop:
```bash
curl -X POST http://127.0.0.1:8000/api/crops \
  -H "Content-Type: application/json" \
  -d '{
    "municipality": "La Trinidad",
    "farm_type": "upland", 
    "year": 2025,
    "crop_name": "Cabbage",
    "area_planted": 10.5,
    "area_harvested": 10.0,
    "production_mt": 250.75,
    "productivity_mt_ha": 25.075
  }'
```

### Get all crops with filters:
```bash
curl "http://127.0.0.1:8000/api/crops?municipality=La Trinidad&per_page=25"
```

### Update a crop:
```bash
curl -X PUT http://127.0.0.1:8000/api/crops/123 \
  -H "Content-Type: application/json" \
  -d '{
    "municipality": "Baguio",
    "farm_type": "irrigated",
    "year": 2025,
    "crop_name": "Broccoli",
    "area_planted": 15.0,
    "area_harvested": 14.5,
    "production_mt": 400.0,
    "productivity_mt_ha": 27.586
  }'
```

### Delete a crop:
```bash
curl -X DELETE http://127.0.0.1:8000/api/crops/123
```