# Crops CRUD System for Administrators

This documentation explains the newly added CRUD (Create, Read, Update, Delete) functionality for crops in the PASYA agricultural platform.

## Features Added

### 1. Crop Model (`app/Models/Crop.php`)
- Complete model with proper relationships to Farmer
- Fields include: name, variety, planting_date, expected_harvest_date, actual_harvest_date, area_hectares, description, status, expected_yield_kg, actual_yield_kg
- Status options: planted, growing, harvested, failed
- Proper date casting and decimal formatting

### 2. Database Migration
- Created `crops` table with all necessary fields
- Foreign key relationship to `tblFarmers` table
- Proper indexing and constraints

### 3. CropController (`app/Http/Controllers/CropController.php`)
- Full resource controller with all CRUD operations
- **Admin Access**: Administrators can manage crops for all farmers
- Proper validation for all form inputs
- Pagination support for crop listings
- Farmer selection dropdown for creating/editing crops

### 4. Routes (`routes/web.php`)
- Resource routes for crops under **admin middleware protection**
- All standard RESTful routes available:
  - `GET /admin/crops` - List all crops from all farmers
  - `GET /admin/crops/create` - Show create form with farmer selection
  - `POST /admin/crops` - Store new crop
  - `GET /admin/crops/{crop}` - Show crop details
  - `GET /admin/crops/{crop}/edit` - Show edit form
  - `PUT /admin/crops/{crop}` - Update crop
  - `DELETE /admin/crops/{crop}` - Delete crop

### 5. Views (`resources/views/admin/crops/`)
- **index.blade.php** - Table layout showing all crops from all farmers with farmer information
- **create.blade.php** - Form to add new crops with farmer selection dropdown
- **edit.blade.php** - Form to edit existing crops (includes farmer, status and yield updates)
- **show.blade.php** - Detailed view of a single crop with calculations and farmer info

### 6. Navigation
- Added "Crop Management" link to admin sidebar
- Consistent styling with existing admin panel design

## How to Use

### For Administrators:
1. **View All Crops**: Click "Crop Management" in the admin sidebar to see all crops from all farmers
2. **Add Crop**: Click "Add New Crop" button, select farmer and fill in crop details
3. **Edit Crop**: Click "Edit" on any crop row to update details, status, or yields
4. **View Details**: Click "View" to see comprehensive crop information with calculations
5. **Delete Crop**: Use the delete button (with confirmation) to remove crops
6. **Farmer Overview**: See which farmer owns each crop directly in the table

### Key Features:
- **Multi-Farmer Management**: Manage crops for all farmers from one interface
- **Status Tracking**: Track crops from planting to harvest
- **Yield Comparison**: Compare expected vs actual yields
- **Area Management**: Track hectares planted per crop
- **Timeline View**: See planting and harvest dates
- **Calculations**: Automatic yield per hectare calculations
- **Farmer Information**: View farmer details alongside crop data
- **Responsive Design**: Works on desktop and mobile devices

## Database Schema

```sql
CREATE TABLE crops (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    farmer_id BIGINT,
    name VARCHAR(255) NOT NULL,
    variety VARCHAR(255) NULLABLE,
    planting_date DATE NOT NULL,
    expected_harvest_date DATE NULLABLE,
    actual_harvest_date DATE NULLABLE,
    area_hectares DECIMAL(8,2) NOT NULL,
    description TEXT NULLABLE,
    status ENUM('planted', 'growing', 'harvested', 'failed') DEFAULT 'planted',
    expected_yield_kg DECIMAL(10,2) NULLABLE,
    actual_yield_kg DECIMAL(10,2) NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES tblFarmers(farmerID) ON DELETE CASCADE
);
```

## Security Features
- Farmers can only access their own crops
- All forms include CSRF protection
- Input validation on all fields
- Confirmation dialogs for destructive actions

## Installation
The crops CRUD system is now fully integrated into the PASYA platform. The database migration has been run and all files are in place.