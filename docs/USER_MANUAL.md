# 📖 User Manual - Kyle-HMS

Complete guide for using the Kyle Hospital Management System

## Table of Contents

- [Getting Started](#getting-started)
- [Patient Portal](#patient-portal)
- [Doctor Portal](#doctor-portal)
- [Admin Portal](#admin-portal)
- [Common Tasks](#common-tasks)
- [FAQs](#faqs)

---

## Getting Started

### Accessing Kyle-HMS

1. Open your web browser
2. Navigate to the application URL:
   - Local: `http://localhost:8000`
   - Virtual Host: `http://kyle-hms.local`
   - Production: `https://yourdomain.com`

### First Time Login

#### Default Credentials (After Installation)

**Administrator:**
- Email: `admin@kylehms.com`
- Password: `password`

**Doctor:**
- Email: `doctor1@kylehms.com`
- Password: `password`

**Patient:**
- Email: `patient1@kylehms.com`
- Password: `password`

⚠️ **Change default passwords immediately after first login!**

### Registering a New Account

1. Click **"Register"** on the login page
2. Fill in the registration form:
   - Full Name
   - Email Address
   - Password (minimum 8 characters)
   - Confirm Password
3. Select user type: **Patient** (default)
4. Click **"Register"**
5. Check your email for verification (if enabled)

### Password Reset

If you forget your password:

1. Click **"Forgot Password?"** on login page
2. Enter your email address
3. Click **"Email Password Reset Link"**
4. Check your email
5. Click the reset link
6. Enter new password
7. Click **"Reset Password"**

---

## Patient Portal

### Dashboard

After logging in as a patient, you'll see:

- **Welcome Message**: Personalized greeting
- **Quick Stats**: 
  - Upcoming appointments count
  - Total appointments
  - Medical records count
- **Recent Appointments**: List of upcoming appointments
- **Quick Actions**: Buttons to book appointments or view doctors

### Managing Your Profile

#### View Profile

1. Click your avatar/name in top-right corner
2. Select **"Profile"**
3. View your personal information

#### Edit Profile

1. Go to **Profile** page
2. Click **"Edit Profile Information"**
3. Update:
   - Name
   - Email
   - Phone number
   - Date of birth
   - Gender
   - Address
   - Emergency contact
4. Click **"Save"**

#### Upload Profile Picture

1. Go to **Profile** page
2. In the **Avatar Upload** section:
   - Click **"Choose File"**
   - Select an image (JPG, PNG, max 2MB)
   - Preview will appear
   - Click **"Upload Avatar"**

#### Update Patient Information

1. Go to **Profile** page
2. Scroll to **"Patient Information"**
3. Update:
   - Medical history
   - Blood type
   - Known allergies
4. Click **"Save Patient Information"**

#### Change Password

1. Go to **Profile** page
2. Scroll to **"Update Password"**
3. Enter:
   - Current password
   - New password
   - Confirm new password
4. Click **"Save"**

### Finding Doctors

#### Browse All Doctors

1. Click **"Doctors"** in navigation menu
2. View list of all available doctors
3. Each card shows:
   - Doctor name and photo
   - Specialty
   - Experience (years)
   - Qualifications
   - Availability status

#### Search Doctors

1. On the **Doctors** page:
   - Use search box to find by name
   - Filter by specialty (dropdown)
   - Sort by experience or name

#### View Doctor Profile

1. Click **"View Profile"** on any doctor card
2. View detailed information:
   - Full biography
   - Qualifications and credentials
   - Years of experience
   - Specialty
   - Available schedules
   - Recent appointments (count)

### Booking Appointments

#### Step 1: Select Doctor

1. Go to **"Doctors"** page
2. Find your preferred doctor
3. Click **"View Profile"**

#### Step 2: Choose Schedule

1. View available schedules
2. Check:
   - Date
   - Time slots
   - Available appointments
   - Duration per appointment
3. Click **"Book Appointment"** on desired schedule

#### Step 3: Select Time Slot

1. Choose from available time slots
2. Each slot shows:
   - Time (e.g., 9:00 AM)
   - Duration (e.g., 30 minutes)
   - Availability status

#### Step 4: Provide Details

1. Select appointment time
2. Enter **Reason for Visit** (required)
3. Add any additional notes (optional)
4. Review appointment details
5. Click **"Book Appointment"**

#### Step 5: Confirmation

- Appointment created with status: **Pending**
- Appointment number generated (e.g., APT20260207ABCD)
- Wait for doctor confirmation
- Check email for confirmation (if notifications enabled)

### Managing Appointments

#### View All Appointments

1. Click **"Appointments"** in navigation
2. See all your appointments:
   - Upcoming (pending, confirmed)
   - Past (completed, cancelled)

#### Appointment Statuses

- 🟡 **Pending**: Awaiting doctor confirmation
- 🔵 **Confirmed**: Doctor approved, appointment scheduled
- 🟢 **Completed**: Appointment finished
- 🔴 **Cancelled**: Appointment cancelled
- ⚫ **No Show**: Patient didn't attend
- 🟠 **Expired**: Past date without completion

#### View Appointment Details

1. Click **"View Details"** on any appointment
2. See complete information:
   - Appointment number
   - Doctor details
   - Date and time
   - Status
   - Reason for visit
   - Notes (if any)

#### Cancel Appointment

**Requirements:**
- Status must be Pending or Confirmed
- Date must be in the future

**Steps:**
1. Go to appointment details
2. Click **"Cancel Appointment"**
3. Confirm cancellation
4. Appointment status changes to Cancelled

⚠️ **You cannot cancel:**
- Completed appointments
- Past-date appointments
- Already cancelled appointments

### Viewing Medical Records

#### Access Medical Records

1. Click **"Medical Records"** in navigation
2. View all your medical history

#### Medical Record Information

Each record shows:
- Visit date
- Doctor name and specialty
- Diagnosis
- Treatment prescribed
- Prescription details
- Any attached files
- Doctor's notes

#### Download Medical Documents

1. Open medical record
2. Click **"Download"** button (if file attached)
3. Save document to your computer

#### Print Medical Record

1. View medical record details
2. Use browser print function (Ctrl+P / Cmd+P)
3. Print or save as PDF

---

## Doctor Portal

### Doctor Dashboard

After logging in as a doctor:

- **Statistics Overview**:
  - Today's appointments
  - This week's appointments
  - Total patients
  - Pending appointments
- **Upcoming Appointments**: Next 5 appointments
- **Recent Patients**: Last visited patients
- **Quick Actions**: Create schedule, view appointments

### Managing Schedule

#### Create New Schedule

1. Click **"Schedule"** in navigation
2. Click **"Create New Schedule"**
3. Fill in details:
   - **Date**: Select date (future only)
   - **Start Time**: e.g., 09:00
   - **End Time**: e.g., 17:00
   - **Duration per Appointment**: 15/30/45/60 minutes
   - **Maximum Appointments**: Auto-calculated based on time
   - **Status**: Active (default)
4. Review calculated time slots
5. Click **"Create Schedule"**

#### View Schedules

1. Go to **"Schedule"** page
2. View all your schedules:
   - Date
   - Time range
   - Available/booked slots
   - Status

#### Edit Schedule

**Can only edit future schedules with no bookings**

1. Find schedule in list
2. Click **"Edit"**
3. Modify details
4. Click **"Update Schedule"**

#### Cancel Schedule

1. View schedule details
2. Click **"Cancel Schedule"**
3. Confirm cancellation
4. Status changes to Cancelled
5. Patients with bookings are notified

⚠️ **Cancelling a schedule with appointments:**
- All booked appointments are cancelled
- Patients receive notifications

### Managing Appointments

#### View All Appointments

1. Click **"Appointments"** in navigation
2. Filter by status:
   - Pending: Awaiting your confirmation
   - Confirmed: Appointments you confirmed
   - Completed: Finished consultations
   - All: See everything

#### Appointment Actions

##### Confirm Appointment

1. View pending appointment
2. Review patient details and reason
3. Click **"Confirm Appointment"**
4. Status changes to Confirmed
5. Patient is notified

##### Complete Appointment

**After consultation is finished:**

1. View confirmed appointment
2. Click **"Complete Appointment"**
3. Choose:
   - Mark as Completed only, OR
   - Create Medical Record (recommended)

##### Mark as No-Show

If patient doesn't arrive:

1. View confirmed appointment (on scheduled date)
2. Click **"Mark as No Show"**
3. Confirm action
4. Status changes to No Show

##### Cancel Appointment

1. View appointment
2. Click **"Cancel"**
3. Provide reason (optional)
4. Confirm cancellation
5. Patient is notified

### Creating Medical Records

#### Option 1: From Completed Appointment

1. Complete the appointment
2. Select **"Create Medical Record"**
3. Form pre-filled with appointment data

#### Option 2: Manually Create

1. Go to **"Patients"** page
2. Find patient
3. Click **"View Profile"**
4. Click **"Create Medical Record"**

#### Medical Record Form

Fill in required information:

1. **Visit Date**: Date of consultation (auto-filled)
2. **Diagnosis**: Patient's condition/diagnosis
3. **Treatment**: Treatment provided or recommended
4. **Prescription**: Medications prescribed
   - Medication name
   - Dosage
   - Frequency
   - Duration
5. **Additional Notes**: Any other observations
6. **Attachments** (optional):
   - Lab results
   - X-rays
   - Other medical documents
   - Max file size: 5MB
   - Supported formats: PDF, JPG, PNG

#### Save Medical Record

1. Review all information
2. Click **"Save Medical Record"**
3. Record is saved and linked to patient
4. Patient can view it in their portal

#### Edit Medical Record

1. View patient profile
2. Find medical record in history
3. Click **"Edit"**
4. Update information
5. Click **"Update Medical Record"**

### Managing Patients

#### View Patient List

1. Click **"Patients"** in navigation
2. View all your patients
3. Search by name or email
4. Sort by recent visits

#### View Patient Profile

1. Click patient name
2. See patient information:
   - Personal details
   - Contact information
   - Medical history
   - Blood type and allergies
   - Appointment history
   - Medical records

#### Patient Medical History

1. Open patient profile
2. Scroll to **"Medical Records"**
3. View all past records chronologically
4. Click to view full details

---

## Admin Portal

### Admin Dashboard

Comprehensive overview of the entire system:

- **System Statistics**:
  - Total patients
  - Total doctors
  - Total appointments
  - Total specialties
- **Recent Activity**:
  - New registrations
  - Recent appointments
  - System alerts
- **Charts and Analytics**:
  - Appointments per month
  - Patient growth
  - Popular specialties
  - Doctor performance

### Managing Doctors

#### View All Doctors

1. Click **"Doctors"** in admin menu
2. View complete list of doctors
3. Search, filter, and sort

#### Add New Doctor

1. Click **"Add New Doctor"**
2. Fill in details:
   - **User Information**:
     - Full name
     - Email address
     - Password (auto-generate option)
   - **Doctor Information**:
     - Specialty (select from list)
     - Phone number
     - License number
     - Qualifications
     - Years of experience
     - Biography
     - Profile image (optional)
   - **Status**:
     - Available (Yes/No)
3. Click **"Create Doctor"**
4. Doctor account is created
5. Credentials sent via email

#### Edit Doctor

1. Find doctor in list
2. Click **"Edit"**
3. Modify any information
4. Click **"Update Doctor"**

#### Delete Doctor

⚠️ **Warning**: This action cannot be undone

1. View doctor profile
2. Click **"Delete Doctor"**
3. Confirm deletion
4. All associated data is removed

### Managing Patients

#### View All Patients

1. Click **"Patients"** in menu
2. View all registered patients
3. Search and filter capabilities

#### View Patient Details

1. Click patient name
2. View complete profile:
   - Personal information
   - Medical history
   - Appointments
   - Medical records

#### Edit Patient

1. View patient profile
2. Click **"Edit Profile"**
3. Update information
4. Click **"Update Patient"**

#### Delete Patient

⚠️ **Warning**: Deletes all patient data

1. View patient profile
2. Click **"Delete Patient"**
3. Confirm deletion

### Managing Specialties

#### View Specialties

1. Click **"Specialties"** in menu
2. View all medical specialties
3. See number of doctors per specialty

#### Add New Specialty

1. Click **"Add New Specialty"**
2. Enter:
   - Specialty name
   - Description
3. Click **"Create Specialty"**

#### Edit Specialty

1. Find specialty in list
2. Click **"Edit"**
3. Modify name or description
4. Click **"Update Specialty"**

#### Delete Specialty

**Requirements**: No doctors assigned

1. View specialty
2. Click **"Delete"**
3. Confirm deletion

### Monitoring Appointments

#### View All Appointments

1. Click **"Appointments"** in menu
2. View system-wide appointments
3. Filter by:
   - Status
   - Doctor
   - Patient
   - Date range

#### Appointment Details

1. Click on appointment
2. View complete information:
   - Patient details
   - Doctor details
   - Schedule information
   - Status history
   - Notes

#### Cancel Appointment (Admin)

**Use with caution**

1. View appointment
2. Click **"Cancel Appointment"**
3. Provide reason
4. Confirm cancellation
5. Both patient and doctor notified

### Reports and Analytics

#### Access Reports

1. Click **"Reports"** in menu
2. Choose report type:
   - Appointment Reports
   - Patient Reports
   - Doctor Performance
   - Financial Reports

#### Generate Custom Report

1. Select **"Custom Report"**
2. Choose parameters:
   - Date range
   - Report type
   - Data to include
3. Click **"Generate Report"**

#### Export Data

1. View any report
2. Click **"Export"** button
3. Choose format:
   - CSV (Excel)
   - PDF
   - Excel (.xlsx)
4. Download file

#### Report Examples

**Appointment Summary:**
- Total appointments
- By status breakdown
- By specialty
- Peak times/dates

**Patient Statistics:**
- New registrations
- Active patients
- Demographics
- Popular specialties

**Doctor Performance:**
- Appointments handled
- Completion rate
- Patient satisfaction
- Availability metrics

### User Management

#### View All Users

1. Click **"Users"** (admin section)
2. View all system users
3. Filter by role

#### Assign Roles

1. View user profile
2. Click **"Edit Roles"**
3. Select role(s):
   - Admin
   - Doctor
   - Patient
4. Click **"Update Roles"**

#### Deactivate User

1. View user profile
2. Click **"Deactivate Account"**
3. Confirm action
4. User cannot log in until reactivated

---

## Common Tasks

### Changing Your Password

**All User Types:**

1. Go to Profile page
2. Click **"Update Password"**
3. Enter current password
4. Enter new password (min 8 characters)
5. Confirm new password
6. Click **"Save"**

### Uploading Profile Picture

**All User Types:**

1. Go to Profile page
2. In Avatar section:
   - Current avatar displayed
   - Click **"Choose File"**
   - Select image (JPG, PNG, max 2MB)
   - Preview appears
   - Click **"Upload Avatar"**
3. Success message appears
4. New avatar displayed

### Deleting Profile Picture

1. Go to Profile page
2. Click **"Remove Avatar"**
3. Confirm deletion
4. Default avatar appears

### Searching and Filtering

**Search Box:**
- Type keywords
- Search works on names, emails, specialties
- Results update in real-time

**Filters:**
- Click filter dropdown
- Select criteria
- Click **"Apply Filter"**
- Clear filters: Click **"Clear All"**

**Sorting:**
- Click column headers to sort
- Click again to reverse order
- Default: Most recent first

### Notifications

**Email Notifications** (if configured):

- Appointment confirmations
- Appointment cancellations
- Schedule changes
- Password resets
- System announcements

**In-App Notifications:**

- Bell icon in top bar
- Red badge shows unread count
- Click to view all notifications
- Mark as read individually or all

---

## FAQs

### General Questions

**Q: How do I reset my password?**
A: Click "Forgot Password?" on login page, enter your email, and follow the link sent to you.

**Q: Can I have multiple roles?**
A: Technically yes, but typically users have one primary role (Patient, Doctor, or Admin).

**Q: Is my medical data secure?**
A: Yes, Kyle-HMS implements industry-standard security measures including encryption, CSRF protection, and secure authentication.

### Patient FAQs

**Q: How do I book an appointment?**
A: Browse doctors → Select doctor → View schedule → Choose time slot → Fill form → Submit.

**Q: Can I cancel my appointment?**
A: Yes, you can cancel pending or confirmed appointments for future dates from your appointments page.

**Q: How do I view my medical records?**
A: Click "Medical Records" in the navigation menu to see all your records.

**Q: Can I download my medical records?**
A: Yes, you can view and download any attached documents from your medical records.

**Q: What if the doctor I want isn't available?**
A: Check back later, or browse other doctors in the same specialty. Doctors update their schedules regularly.

### Doctor FAQs

**Q: How do I create my schedule?**
A: Go to Schedule → Create New Schedule → Fill in date, time, and duration → Save.

**Q: Can I edit a schedule with bookings?**
A: No, you can only edit schedules that have no booked appointments. You can cancel and create a new one.

**Q: How do I confirm appointments?**
A: Go to Appointments → View pending → Click "Confirm Appointment".

**Q: Can I create medical records for old appointments?**
A: Yes, view the patient's profile and create a medical record manually.

**Q: How do I upload medical documents?**
A: When creating/editing a medical record, use the file upload field to attach documents (max 5MB).

### Admin FAQs

**Q: How do I add a new doctor?**
A: Doctors → Add New Doctor → Fill in all required information → Create.

**Q: Can I delete appointments?**
A: You can cancel appointments, but deletion should be avoided as it removes historical data.

**Q: How do I generate reports?**
A: Reports → Select report type → Choose date range → Generate → Export if needed.

**Q: Can I modify patient medical records?**
A: Only doctors can create and edit medical records. Admins can view them.

**Q: How do I backup the database?**
A: Contact your system administrator or use phpMyAdmin/MySQL tools to export the database.

---

## Getting Help

### Support Channels

**Technical Issues:**
- Email: nounsunheng290503@gmail.com
- GitHub Issues: https://github.com/nounsunheng/Kyle-HMS/issues

**Documentation:**
- Installation Guide: See INSTALLATION.md
- README: See README.md
- Technical Docs: See TECHNICAL_DOCUMENTATION.md

**Common Solutions:**
- Check [Troubleshooting](#troubleshooting) section
- Review error messages carefully
- Check browser console for JavaScript errors
- Verify your internet connection

### Reporting Bugs

When reporting issues, include:

1. **Description**: What happened?
2. **Steps to Reproduce**: How can we recreate it?
3. **Expected Behavior**: What should happen?
4. **Screenshots**: Visual evidence helps
5. **Environment**: Browser, OS, PHP version
6. **Error Messages**: Copy full error text

### Feature Requests

We welcome suggestions!

1. Check existing issues first
2. Describe the feature clearly
3. Explain the use case
4. Submit to GitHub Issues

---

## Best Practices

### For Patients

- Keep profile information up to date
- Update medical history regularly
- Arrive on time for appointments
- Bring relevant documents to appointments
- Review medical records after visits

### For Doctors

- Update schedules at least one week in advance
- Confirm appointments promptly
- Create detailed medical records
- Upload supporting documents when needed
- Keep profile and qualifications current

### For Admins

- Regularly review system reports
- Monitor user activity
- Keep doctor and specialty information current
- Backup database regularly
- Review and update security settings

---

## Appendix

### System Requirements

**Minimum:**
- Modern web browser (Chrome, Firefox, Safari, Edge)
- Internet connection (2 Mbps)
- Screen resolution: 1366x768

**Recommended:**
- Latest browser version
- High-speed internet (5+ Mbps)
- Screen resolution: 1920x1080

### Keyboard Shortcuts

- `Ctrl + S` / `Cmd + S`: Save forms (where applicable)
- `Esc`: Close modals
- `Ctrl + P` / `Cmd + P`: Print page
- `Tab`: Navigate form fields
- `Enter`: Submit forms

### Browser Compatibility

✅ **Fully Supported:**
- Google Chrome 90+
- Mozilla Firefox 88+
- Safari 14+
- Microsoft Edge 90+

⚠️ **Partial Support:**
- Internet Explorer (not recommended)
- Older browser versions

---

**User Manual Version 1.0**  
Last Updated: February 2026  
For Kyle-HMS v1.0

Need more help? Contact: nounsunheng290503@gmail.com
