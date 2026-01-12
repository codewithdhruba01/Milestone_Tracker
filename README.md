# Parent Toddler Dashboard - Child Development Tracker

A comprehensive web application for parents to track their child's growth, development milestones, and overall progress. Monitor physical growth (height & weight), development milestones, and get personalized insights for your child's development journey.


## Features

### Child Profile Management
- **Multi-Child Support**: Add and manage multiple children in one dashboard
- **Personal Details**: Name, date of birth, gender, center information
- **Photo Upload**: Secure child photo storage with validation
- **Dynamic Age Calculation**: Real-time age display in years and months

### Growth Tracking System
- **Monthly Height & Weight Recording**: Track physical growth over time
- **Real-time Updates**: Instant display updates when data is submitted
- **Automatic Date Tracking**: Last check date and next check reminders
- **Monthly Reminders**: Smart notifications for regular check-ups

### Development Milestones
- **Age-Appropriate Questions**: Dynamic questions based on child's age (0-7 years)
- **Four Development Domains**:
  - **Language**: Communication and linguistic skills
  - **Motor**: Physical abilities and coordination
  - **Social**: Interpersonal and emotional skills
  - **Cognitive**: Thinking and learning abilities
- **Progress Tracking**: Visual progress indicators for each domain

### Dashboard Analytics
- **Development Progress Cards**: Visual progress indicators for each skill area
- **Growth Charts**: Height and weight tracking over time
- **Observer Notes**: Space for teacher/parent observations
- **Interactive Charts**: Hover-able data points with detailed information

### Administrative Features
- **Child Editing**: Update child information and photos
- **Child Deletion**: Safe removal with confirmation dialogs
- **Data Verification**: Comprehensive data viewing and validation
- **Export Ready**: Structured data for reports and analysis

## Database Structure

### Tables Overview

| Table | Description | Key Fields |
|-------|-------------|------------|
| `children` | Child personal information | child_id, name, dob, gender, center, image |
| `child_milestones` | Development milestone answers | child_id, domain, question, answer |
| `child_growth_records` | Height and weight tracking | child_id, height, weight, check_date |

### Detailed Schema

#### 1. **children** - Child Information
```sql
CREATE TABLE children (
    child_id INT AUTO_INCREMENT PRIMARY KEY,
    child_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    age_group VARCHAR(10),
    gender ENUM('Male','Female') NOT NULL,
    center VARCHAR(50) NOT NULL,
    child_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 2. **child_milestones** - Development Tracking
```sql
CREATE TABLE child_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT,
    domain VARCHAR(20), -- Language, Motor, Social, Cognitive
    question TEXT,
    answer ENUM('yes','no'),
    FOREIGN KEY (child_id) REFERENCES children(child_id)
);
```

#### 3. **child_growth_records** - Physical Growth
```sql
CREATE TABLE child_growth_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT,
    height DECIMAL(5,2), -- Height in cm
    weight DECIMAL(5,2), -- Weight in kg
    check_date DATE, -- Date of measurement
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(child_id) ON DELETE CASCADE
);
```

## Quick Start

### Prerequisites
- **XAMPP/WAMP/LAMP Stack** (Apache, MySQL, PHP)
- **PHP 7.4+** with MySQLi extension
- **Web Browser** (Chrome, Firefox, Safari, Edge)

### Installation Steps

1. **Clone or Download**
   ```bash
   cd /opt/lampp/htdocs/
   # Place the parent_toddler1 folder here
   ```

2. **Start XAMPP Services**
   ```bash
   sudo /opt/lampp/lampp start
   # OR use XAMPP Control Panel
   ```

3. **Database Setup**
   - Open browser: `http://localhost/phpmyadmin`
   - Create database: `child_management`
   - Import: `parent_toddler1/add_child/add_child.sql`
   - OR run: `http://localhost/parent_toddler1/setup_database.php`

4. **Access Application**
   - Main Dashboard: `http://localhost/parent_toddler1/`
   - Add Child: `http://localhost/parent_toddler1/add_child/add_child.php`
   - Verify Data: `http://localhost/parent_toddler1/verify_data.php`

## 📁 Project Structure

```
parent_toddler1/
├── index.php                 # Main dashboard with child profiles
├── add_child/                # Child registration system
│   ├── add_child.php        # Multi-step registration form
│   ├── add_child.js         # Form validation & step management
│   ├── save_child.php       # Data processing & storage
│   ├── update_child.php     # Child information updates
│   ├── delete_child.php     # Child deletion handler
│   ├── config.php           # Database configuration
│   ├── add_child.sql        # Database schema
│   └── uploads/img/         # Child photo storage
├── config/                   # Global configurations
│   └── db.php               # Main database connection
├── Css/                      # Stylesheets
│   └── style.css            # Main application styles
├── Assets/img/              # Static images
│   ├── hero_bg.png          # Hero section background
│   ├── logo.png             # Application logo
│   ├── child1.png           # Sample child image
│   └── child2.png           # Sample child image
├── get_growth_data.php      # API for growth data retrieval
├── save_growth.php          # API for saving growth data
├── setup_database.php       # Database initialization
├── test_db.php              # Database connection test
└── verify_data.php          # Data verification interface
```

## User Interface Features

### Dashboard Layout
- **Hero Section**: Welcome message and navigation
- **Child Profile Cards**: Visual child selection interface
- **Growth Tracking**: Height/weight input and display
- **Development Progress**: Four-domain progress cards
- **Growth Charts**: Interactive height and weight graphs

### Responsive Design
- **Mobile-First Approach**: Optimized for all screen sizes
- **Tablet Support**: Adaptive layouts for tablets
- **Desktop Enhancement**: Full feature set on larger screens

## API Endpoints

### Growth Data Management
- `GET /get_growth_data.php?child_id={id}` - Fetch child's growth records
- `POST /save_growth.php` - Save new height/weight data

### Child Management
- `POST /add_child/save_child.php` - Register new child
- `POST /add_child/update_child.php` - Update child information
- `POST /add_child/delete_child.php` - Remove child profile

## Data Flow

### Child Registration Process
1. **Step 1**: Personal details → `children` table
2. **Step 2-5**: Development domains → `child_milestones` table
3. **Image Upload**: Secure file storage in `uploads/img/`
4. **Validation**: Server-side data validation
5. **Confirmation**: Success message with child profile access

### Growth Tracking Process
1. **Select Child**: Click on child avatar in dashboard
2. **Enter Data**: Height (cm) and Weight (kg) input
3. **Submit**: AJAX POST to save data
4. **Update Display**: Real-time UI updates
5. **Date Tracking**: Automatic last/next check date calculation

## Security Features

- **SQL Injection Prevention**: Prepared statements throughout
- **Input Sanitization**: XSS protection on all inputs
- **File Upload Security**: Type, size, and content validation
- **Session Management**: Secure user session handling
- **Error Logging**: Comprehensive error tracking

## Development Milestones

### Age-Based Question Sets
- **0-1 Year**: Basic reflexes and sensory responses
- **1-2 Years**: First words and basic motor skills
- **2-3 Years**: Simple sentences and social interaction
- **3-4 Years**: Complex language and imaginative play
- **4-5 Years**: Reading readiness and peer relationships
- **5-6 Years**: Academic preparation and self-control
- **6-7 Years**: Advanced reasoning and social skills

### Progress Scoring
- **Excellent**: 85%+ milestone achievement
- **Good**: 70-84% achievement
- **Developing**: Below 70% achievement

## Deployment

### Local Development
```bash
# Start XAMPP
sudo /opt/lampp/lampp start

# Access application
# http://localhost/parent_toddler1/
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -m 'Add feature'`
4. Push to branch: `git push origin feature-name`
5. Submit Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:
- Check the [Issues](../../issues) section
- Review the documentation
- Contact the development team
