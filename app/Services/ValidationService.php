<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Services;

/**
 * Validation Service
 * Complex validation rules and business logic validation
 */
class ValidationService {
    
    /**
     * Validate patient registration data
     * 
     * @param array $data Registration data
     * @return array Validation errors (empty if valid)
     */
    public function validatePatientRegistration(array $data): array {
        $errors = [];
        
        // Name validation
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'Name must be at least 2 characters';
        } elseif (strlen($data['name']) > 255) {
            $errors['name'] = 'Name must not exceed 255 characters';
        }
        
        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        // Password validation
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        } elseif (!preg_match('/[A-Za-z]/', $data['password']) || !preg_match('/[0-9]/', $data['password'])) {
            $errors['password'] = 'Password must contain both letters and numbers';
        }
        
        // Confirm password validation
        if (empty($data['confirm_password'])) {
            $errors['confirm_password'] = 'Please confirm your password';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        // Date of birth validation
        if (empty($data['dob'])) {
            $errors['dob'] = 'Date of birth is required';
        } else {
            $age = calculateAge($data['dob']);
            if ($age < 0) {
                $errors['dob'] = 'Date of birth cannot be in the future';
            } elseif ($age > 150) {
                $errors['dob'] = 'Invalid date of birth';
            }
        }
        
        // Gender validation
        if (empty($data['gender'])) {
            $errors['gender'] = 'Gender is required';
        } elseif (!in_array($data['gender'], ['male', 'female', 'other'])) {
            $errors['gender'] = 'Invalid gender selection';
        }
        
        // Phone validation
        if (empty($data['tel'])) {
            $errors['tel'] = 'Phone number is required';
        } elseif (!$this->isValidPhone($data['tel'])) {
            $errors['tel'] = 'Invalid phone number format';
        }
        
        // Address validation
        if (empty($data['address'])) {
            $errors['address'] = 'Address is required';
        } elseif (strlen($data['address']) < 10) {
            $errors['address'] = 'Address must be at least 10 characters';
        }
        
        return $errors;
    }
    
    /**
     * Validate login credentials
     * 
     * @param array $data Login data
     * @return array Validation errors
     */
    public function validateLogin(array $data): array {
        $errors = [];
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        }
        
        return $errors;
    }
    
    /**
     * Validate appointment booking data
     * 
     * @param array $data Appointment data
     * @return array Validation errors
     */
    public function validateAppointmentBooking(array $data): array {
        $errors = [];
        
        if (empty($data['schedule_id'])) {
            $errors['schedule_id'] = 'Please select a schedule';
        }
        
        if (empty($data['patient_id'])) {
            $errors['patient_id'] = 'Patient information is missing';
        }
        
        if (!empty($data['symptoms']) && strlen($data['symptoms']) > 500) {
            $errors['symptoms'] = 'Symptoms description must not exceed 500 characters';
        }
        
        return $errors;
    }
    
    /**
     * Validate schedule creation data
     * 
     * @param array $data Schedule data
     * @return array Validation errors
     */
    public function validateScheduleCreation(array $data): array {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors['title'] = 'Schedule title is required';
        }
        
        if (empty($data['scheduledate'])) {
            $errors['scheduledate'] = 'Schedule date is required';
        } elseif (strtotime($data['scheduledate']) < strtotime('today')) {
            $errors['scheduledate'] = 'Schedule date cannot be in the past';
        }
        
        if (empty($data['scheduletime'])) {
            $errors['scheduletime'] = 'Schedule time is required';
        } elseif (!$this->isValidTime($data['scheduletime'])) {
            $errors['scheduletime'] = 'Invalid time format';
        }
        
        if (empty($data['nop'])) {
            $errors['nop'] = 'Number of patients is required';
        } elseif (!is_numeric($data['nop']) || $data['nop'] < 1) {
            $errors['nop'] = 'Number of patients must be at least 1';
        } elseif ($data['nop'] > 100) {
            $errors['nop'] = 'Number of patients cannot exceed 100';
        }
        
        if (!empty($data['duration'])) {
            if (!is_numeric($data['duration']) || $data['duration'] < 10) {
                $errors['duration'] = 'Duration must be at least 10 minutes';
            } elseif ($data['duration'] > 180) {
                $errors['duration'] = 'Duration cannot exceed 180 minutes';
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate doctor creation data
     * 
     * @param array $data Doctor data
     * @return array Validation errors
     */
    public function validateDoctorCreation(array $data): array {
        $errors = [];
        
        if (empty($data['docname'])) {
            $errors['docname'] = 'Doctor name is required';
        }
        
        if (empty($data['docemail'])) {
            $errors['docemail'] = 'Email is required';
        } elseif (!filter_var($data['docemail'], FILTER_VALIDATE_EMAIL)) {
            $errors['docemail'] = 'Invalid email format';
        }
        
        if (empty($data['doctel'])) {
            $errors['doctel'] = 'Phone number is required';
        } elseif (!$this->isValidPhone($data['doctel'])) {
            $errors['doctel'] = 'Invalid phone number format';
        }
        
        if (empty($data['specialties'])) {
            $errors['specialties'] = 'Specialty is required';
        }
        
        if (empty($data['docdegree'])) {
            $errors['docdegree'] = 'Degree/qualification is required';
        }
        
        if (!empty($data['docexperience'])) {
            if (!is_numeric($data['docexperience']) || $data['docexperience'] < 0) {
                $errors['docexperience'] = 'Experience must be a positive number';
            } elseif ($data['docexperience'] > 70) {
                $errors['docexperience'] = 'Experience years seems invalid';
            }
        }
        
        if (!empty($data['docconsultation_fee'])) {
            if (!is_numeric($data['docconsultation_fee']) || $data['docconsultation_fee'] < 0) {
                $errors['docconsultation_fee'] = 'Consultation fee must be a positive number';
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate password change
     * 
     * @param array $data Password data
     * @return array Validation errors
     */
    public function validatePasswordChange(array $data): array {
        $errors = [];
        
        if (empty($data['current_password'])) {
            $errors['current_password'] = 'Current password is required';
        }
        
        if (empty($data['new_password'])) {
            $errors['new_password'] = 'New password is required';
        } elseif (strlen($data['new_password']) < 8) {
            $errors['new_password'] = 'Password must be at least 8 characters';
        } elseif (!preg_match('/[A-Za-z]/', $data['new_password']) || !preg_match('/[0-9]/', $data['new_password'])) {
            $errors['new_password'] = 'Password must contain both letters and numbers';
        }
        
        if (empty($data['confirm_password'])) {
            $errors['confirm_password'] = 'Please confirm your new password';
        } elseif ($data['new_password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        if (!empty($data['current_password']) && !empty($data['new_password'])) {
            if ($data['current_password'] === $data['new_password']) {
                $errors['new_password'] = 'New password must be different from current password';
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate email format
     * 
     * @param string $email Email address
     * @return bool Valid status
     */
    public function isValidEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number (Cambodia format)
     * 
     * @param string $phone Phone number
     * @return bool Valid status
     */
    public function isValidPhone(string $phone): bool {
        // Remove spaces and dashes
        $phone = str_replace([' ', '-'], '', $phone);
        
        // Check if it's a valid Cambodia phone number
        // Format: 012-345-6789 or 096-999-0399 or +855123456789
        if (preg_match('/^(\+855|0)[1-9]\d{7,9}$/', $phone)) {
            return true;
        }
        
        // Also accept general format (9-15 digits)
        return preg_match('/^\d{9,15}$/', $phone);
    }
    
    /**
     * Validate time format (HH:MM)
     * 
     * @param string $time Time string
     * @return bool Valid status
     */
    public function isValidTime(string $time): bool {
        return preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time);
    }
    
    /**
     * Validate date format (YYYY-MM-DD)
     * 
     * @param string $date Date string
     * @return bool Valid status
     */
    public function isValidDate(string $date): bool {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Sanitize HTML content
     * 
     * @param string $content HTML content
     * @return string Sanitized content
     */
    public function sanitizeHtml(string $content): string {
        // Strip all HTML tags except basic formatting
        $allowedTags = '<p><br><b><i><u><strong><em>';
        return strip_tags($content, $allowedTags);
    }
    
    /**
     * Validate file upload
     * 
     * @param array $file File array from $_FILES
     * @param string $type File type (image, document)
     * @return array Validation result
     */
    public function validateFileUpload(array $file, string $type = 'image'): array {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['error' => 'No file uploaded'];
        }
        
        // Check file size (5MB max)
        if ($file['size'] > 5242880) {
            $errors['size'] = 'File size must not exceed 5MB';
        }
        
        // Check file type
        $allowedTypes = $type === 'image' 
            ? ['image/jpeg', 'image/png', 'image/gif']
            : ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $errors['type'] = 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes);
        }
        
        return $errors;
    }
}