# Tourism Management System - Entity Relationship Model

## Entities

### 1. User
- **Primary Key**: UserId
- **Attributes**:
  - FirstName
  - LastName
  - Email
  - Password
  - MobileNumber
  - Address
  - RegDate
  - UpdationDate
  - Status (0/1)

### 2. TourPackage
- **Primary Key**: PackageId
- **Attributes**:
  - PackageName
  - PackageType
  - PackageLocation
  - PackagePrice
  - PackageFetures
  - PackageDetails
  - PackageImage
  - CreationDate
  - UpdationDate
  - Status (0/1)
  - Meal

### 3. Booking
- **Primary Key**: BookingId
- **Attributes**:
  - PackageId (Foreign Key)
  - UserId (Foreign Key)
  - TravelDate
  - NumberOfPeople
  - MealPlan
  - Comment
  - BookingDate
  - Status (0=Pending, 1=Confirmed, 2=Cancelled)
  - CancellationDate
  - CancellationReason

### 4. Review
- **Primary Key**: ReviewId
- **Attributes**:
  - PackageId (Foreign Key)
  - UserId (Foreign Key)
  - Rating
  - ReviewText
  - ReviewDate
  - Status (0/1)

### 5. Enquiry
- **Primary Key**: id
- **Attributes**:
  - UserId (Foreign Key)
  - Subject
  - Description
  - EnquiryDate
  - Status (0/1)

### 6. Issue
- **Primary Key**: id
- **Attributes**:
  - UserId (Foreign Key)
  - Subject
  - Description
  - IssueDate
  - Status (0/1)

## Relationships

1. **User to Booking**: One-to-Many
   - One user can make multiple bookings
   - Each booking belongs to one user

2. **TourPackage to Booking**: One-to-Many
   - One tour package can have multiple bookings
   - Each booking is for one tour package

3. **User to Review**: One-to-Many
   - One user can write multiple reviews
   - Each review is written by one user

4. **TourPackage to Review**: One-to-Many
   - One tour package can have multiple reviews
   - Each review is for one tour package

5. **User to Enquiry**: One-to-Many
   - One user can submit multiple enquiries
   - Each enquiry is submitted by one user

6. **User to Issue**: One-to-Many
   - One user can report multiple issues
   - Each issue is reported by one user

## ER Diagram

```
+-------------+       +-------------+       +-------------+
|    User     |       | TourPackage |       |   Booking   |
+-------------+       +-------------+       +-------------+
| UserId (PK) |       | PackageId   |       | BookingId   |
| FirstName   |       | PackageName |       | PackageId   |
| LastName    |       | PackageType |       | UserId      |
| Email       |       | Location    |       | TravelDate  |
| Password    |       | Price       |       | People      |
| Mobile      |       | Features    |       | MealPlan    |
| Address     |       | Details     |       | Comment     |
| RegDate     |       | Image       |       | BookingDate |
| Status      |       | Meal        |       | Status      |
+-------------+       +-------------+       +-------------+
       |                     |                     |
       |                     |                     |
       |                     |                     |
       v                     v                     v
+-------------+       +-------------+       +-------------+
|   Review    |       |  Enquiry    |       |   Issue     |
+-------------+       +-------------+       +-------------+
| ReviewId    |       | id          |       | id          |
| PackageId   |       | UserId      |       | UserId      |
| UserId      |       | Subject     |       | Subject     |
| Rating      |       | Description |       | Description |
| ReviewText  |       | EnquiryDate |       | IssueDate   |
| ReviewDate  |       | Status      |       | Status      |
| Status      |       +-------------+       +-------------+
+-------------+
```

## Notes

1. **Status Fields**: Most entities have a status field (0/1) to indicate active/inactive records
2. **Booking Status**: Uses specific values (0=Pending, 1=Confirmed, 2=Cancelled)
3. **Date Fields**: All entities include creation/booking dates for tracking
4. **Foreign Keys**: Maintain referential integrity between related tables
5. **Meal Plans**: Tour packages have meal options that affect booking prices 