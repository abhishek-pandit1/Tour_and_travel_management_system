# Tourism Management System - Data Dictionary

## Table: tblusers

| Field Name | Data Type | Description | Constraints | Example |
|------------|-----------|-------------|-------------|---------|
| UserId | INT | Unique identifier for each user | Primary Key, Auto Increment | 1 |
| FirstName | VARCHAR(50) | User's first name | NOT NULL | John |
| LastName | VARCHAR(50) | User's last name | NOT NULL | Doe |
| Email | VARCHAR(100) | User's email address | NOT NULL, UNIQUE | john.doe@example.com |
| Password | VARCHAR(255) | User's encrypted password | NOT NULL | hashed_password |
| MobileNumber | VARCHAR(15) | User's contact number | NOT NULL | +1234567890 |
| Address | TEXT | User's complete address | NULL | 123 Main St, City, Country |
| RegDate | DATETIME | Date and time of user registration | NOT NULL, DEFAULT CURRENT_TIMESTAMP | 2023-01-15 14:30:00 |
| UpdationDate | DATETIME | Date and time of last profile update | NULL | 2023-02-20 09:15:00 |
| Status | TINYINT | User account status | NOT NULL, DEFAULT 1 (1=Active, 0=Inactive) | 1 |

## Table: tbltourpackages

| Field Name | Data Type | Description | Constraints | Example |
|------------|-----------|-------------|-------------|---------|
| PackageId | INT | Unique identifier for each tour package | Primary Key, Auto Increment | 1 |
| PackageName | VARCHAR(200) | Name of the tour package | NOT NULL | Bali Paradise Tour |
| PackageType | VARCHAR(50) | Type of tour package | NOT NULL | Beach, Adventure, Cultural |
| PackageLocation | VARCHAR(200) | Location of the tour | NOT NULL | Bali, Indonesia |
| PackagePrice | DECIMAL(10,2) | Price of the tour package | NOT NULL | 1299.99 |
| PackageFetures | TEXT | Features included in the package | NOT NULL | 5-star hotel, airport transfer, guided tours |
| PackageDetails | TEXT | Detailed description of the package | NOT NULL | 7 days in Bali with daily activities... |
| PackageImage | VARCHAR(255) | Path to package image | NULL | images/packages/bali.jpg |
| CreationDate | DATETIME | Date and time of package creation | NOT NULL, DEFAULT CURRENT_TIMESTAMP | 2023-01-10 10:00:00 |
| UpdationDate | DATETIME | Date and time of last package update | NULL | 2023-03-15 16:45:00 |
| Status | TINYINT | Package status | NOT NULL, DEFAULT 1 (1=Active, 0=Inactive) | 1 |
| Meal | VARCHAR(50) | Meal plan options | NULL | Classic, Premium, Deluxe |

## Table: tblbooking

| Field Name | Data Type | Description | Constraints | Example |
|------------|-----------|-------------|-------------|---------|
| BookingId | INT | Unique identifier for each booking | Primary Key, Auto Increment | 1 |
| PackageId | INT | Reference to the booked tour package | Foreign Key (tbltourpackages) | 1 |
| UserId | INT | Reference to the user who made the booking | Foreign Key (tblusers) | 1 |
| TravelDate | DATE | Date of travel | NOT NULL | 2023-06-15 |
| NumberOfPeople | INT | Number of people in the booking | NOT NULL | 2 |
| MealPlan | VARCHAR(50) | Selected meal plan | NOT NULL | Premium |
| Comment | TEXT | Additional comments or special requests | NULL | Need vegetarian meals |
| BookingDate | DATETIME | Date and time of booking | NOT NULL, DEFAULT CURRENT_TIMESTAMP | 2023-04-20 11:30:00 |
| Status | TINYINT | Booking status | NOT NULL, DEFAULT 0 (0=Pending, 1=Confirmed, 2=Cancelled) | 1 |
| CancellationDate | DATETIME | Date and time of cancellation | NULL | 2023-05-10 09:15:00 |
| CancellationReason | TEXT | Reason for cancellation | NULL | Changed travel plans |

## Table: tblreviews

| Field Name | Data Type | Description | Constraints | Example |
|------------|-----------|-------------|-------------|---------|
| ReviewId | INT | Unique identifier for each review | Primary Key, Auto Increment | 1 |
| PackageId | INT | Reference to the reviewed tour package | Foreign Key (tbltourpackages) | 1 |
| UserId | INT | Reference to the user who wrote the review | Foreign Key (tblusers) | 1 |
| Rating | INT | Rating given to the package (1-5) | NOT NULL, CHECK (Rating BETWEEN 1 AND 5) | 5 |
| ReviewText | TEXT | Text of the review | NOT NULL | Amazing experience! The tour was well organized... |
| ReviewDate | DATETIME | Date and time of review submission | NOT NULL, DEFAULT CURRENT_TIMESTAMP | 2023-07-05 14:20:00 |
| Status | TINYINT | Review status | NOT NULL, DEFAULT 1 (1=Active, 0=Inactive) | 1 |

## Table: tblenquiry

| Field Name | Data Type | Description | Constraints | Example |
|------------|-----------|-------------|-------------|---------|
| id | INT | Unique identifier for each enquiry | Primary Key, Auto Increment | 1 |
| UserId | INT | Reference to the user who submitted the enquiry | Foreign Key (tblusers) | 1 |
| Subject | VARCHAR(200) | Subject of the enquiry | NOT NULL | Question about payment methods |
| Description | TEXT | Content of the enquiry | NOT NULL | What payment methods do you accept? |
| EnquiryDate | DATETIME | Date and time of enquiry submission | NOT NULL, DEFAULT CURRENT_TIMESTAMP | 2023-05-15 16:40:00 |
| Status | TINYINT | Enquiry status | NOT NULL, DEFAULT 0 (0=Unread, 1=Read) | 0 |

## Table: tblissues

| Field Name | Data Type | Description | Constraints | Example |
|------------|-----------|-------------|-------------|---------|
| id | INT | Unique identifier for each issue | Primary Key, Auto Increment | 1 |
| UserId | INT | Reference to the user who reported the issue | Foreign Key (tblusers) | 1 |
| Subject | VARCHAR(200) | Subject of the issue | NOT NULL | Problem with booking confirmation |
| Description | TEXT | Description of the issue | NOT NULL | I didn't receive my booking confirmation email |
| IssueDate | DATETIME | Date and time of issue submission | NOT NULL, DEFAULT CURRENT_TIMESTAMP | 2023-06-10 09:25:00 |
| Status | TINYINT | Issue status | NOT NULL, DEFAULT 0 (0=Open, 1=Resolved) | 0 |

## Relationships and Constraints

1. **Foreign Key Constraints**:
   - `tblbooking.PackageId` references `tbltourpackages.PackageId`
   - `tblbooking.UserId` references `tblusers.UserId`
   - `tblreviews.PackageId` references `tbltourpackages.PackageId`
   - `tblreviews.UserId` references `tblusers.UserId`
   - `tblenquiry.UserId` references `tblusers.UserId`
   - `tblissues.UserId` references `tblusers.UserId`

2. **Indexes**:
   - Primary keys are automatically indexed
   - Foreign key columns should be indexed for performance
   - `Email` in `tblusers` should be indexed for login queries
   - `Status` columns should be indexed for filtering

3. **Data Integrity Rules**:
   - Users cannot be deleted if they have associated bookings, reviews, enquiries, or issues
   - Tour packages cannot be deleted if they have associated bookings or reviews
   - Bookings cannot be created for inactive tour packages
   - Reviews cannot be submitted for inactive tour packages

## Notes

1. **Status Values**:
   - User Status: 1=Active, 0=Inactive
   - Package Status: 1=Active, 0=Inactive
   - Booking Status: 0=Pending, 1=Confirmed, 2=Cancelled
   - Review Status: 1=Active, 0=Inactive
   - Enquiry Status: 0=Unread, 1=Read
   - Issue Status: 0=Open, 1=Resolved

2. **Date Fields**:
   - All creation/registration dates use `DEFAULT CURRENT_TIMESTAMP`
   - Update dates are manually set when records are modified
   - Cancellation dates are set when bookings are cancelled

3. **Meal Plans**:
   - Available options: Classic, Premium, Deluxe
   - Each option may have different pricing and inclusions
   - Meal plan selection affects the total booking price 