# Parent-Toddler Milestone Tracker

A web application for tracking child development milestones with comprehensive data storage.

## Features

- **Multi-step Child Registration**: Personal details, Language, Motor, Social Skills, and Cognitive development tracking
- **Age-appropriate Questions**: Dynamic questions based on child's age group (0-7 years)
- **Complete Data Storage**: All form data is stored in MySQL database
- **Image Upload**: Child photo storage with secure file handling
- **Data Verification**: View and verify all stored child data

## Database Structure

### Tables

1. **children**
   - `child_id` (Primary Key)
   - `child_name`
   - `dob` (Date of Birth)
   - `age_group` (Auto-calculated: 0-1, 1-2, 2-3, 3-4, 4-5, 5-6, 6-7)
   - `gender`
   - `center`
   - `child_image` (File path)
   - `created_at` (Timestamp)

2. **child_milestones**
   - `id` (Primary Key)
   - `child_id` (Foreign Key → children.child_id)
   - `domain` (Language, Motor, Social, Cognitive)
   - `question` (Milestone question text)
   - `answer` (yes/no)

## Setup Instructions

1. **Database Setup**:
   - Start XAMPP/MySQL server
   - Run `setup_database.php` in your browser to create tables automatically
   - OR manually execute `add_child/add_child.sql` in phpMyAdmin

2. **File Permissions**:
   - Ensure `add_child/uploads/img/` directory is writable
   - The application will create it automatically if needed

3. **Access the Application**:
   - Main form: `add_child/add_child.php`
   - Verify data: `verify_data.php`
   - Database setup: `setup_database.php`

## Data Storage Process

When a user completes all 5 form steps and clicks "Submit":

1. **Personal Details** → Stored in `children` table
2. **Language Development** (3 questions) → Stored in `child_milestones`
3. **Motor Development** (3 questions) → Stored in `child_milestones`
4. **Social Skills** (3 questions) → Stored in `child_milestones`
5. **Cognitive** (3 questions) → Stored in `child_milestones`

**Total Records Created**: 1 child record + 12 milestone records (4 domains × 3 questions each)

## Files

- `add_child/add_child.php` - Main registration form
- `add_child/save_child.php` - Data processing and storage
- `add_child/add_child.js` - Form validation and step management
- `add_child/config.php` - Database configuration
- `setup_database.php` - Database initialization
- `verify_data.php` - Data verification interface

## Error Handling

- Transaction-based data integrity
- Comprehensive input validation
- Automatic rollback on errors
- User-friendly error messages
- File cleanup on failed uploads

## Security Features

- Prepared statements for SQL injection prevention
- Input sanitization and validation
- Secure file upload handling
- Error logging for debugging
