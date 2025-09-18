// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const submitBtn = document.querySelector('.submit-btn');
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        // Clear any existing error messages
        clearErrorMessages();
        
        // Validate form
        if (!validateForm()) {
            return; // Stop if validation fails
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Registering...';
        
        // Collect form data
        const formData = new FormData(form);
        
        // Debug: Log what we're sending
        console.log('Sending form data:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Send data to server using fetch API
        fetch('../actions/register_customer_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text(); // Get as text first to see raw response
        })
        .then(text => {
            console.log('Raw response:', text);
            try {
                const data = JSON.parse(text);
                if (data.status === 'success') {
                    // Show success message
                    showMessage(data.message, 'success');
                    
                    // Redirect after 2 seconds
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    // Show error message
                    showMessage(data.message, 'error');
                }
            } catch (e) {
                console.error('JSON Parse error:', e);
                showMessage('Server response error: ' + text, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred. Please try again.', 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.textContent = 'Register';
        });
    });
    
    // Form validation function
    function validateForm() {
        let isValid = true;
        
        // Get form fields
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const country = document.getElementById('country').value;
        const city = document.getElementById('city').value.trim();
        const phone = document.getElementById('phone_number').value.trim();
        
        // Validate name (at least 2 characters, only letters and spaces)
        if (name.length < 2) {
            showFieldError('name', 'Name must be at least 2 characters long');
            isValid = false;
        } else if (!/^[a-zA-Z\s]+$/.test(name)) {
            showFieldError('name', 'Name can only contain letters and spaces');
            isValid = false;
        }
        
        // Validate email using regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showFieldError('email', 'Please enter a valid email address');
            isValid = false;
        }
        
        // Validate password (at least 6 characters, contain letter and number)
        if (password.length < 6) {
            showFieldError('password', 'Password must be at least 6 characters long');
            isValid = false;
        } else if (!/(?=.*[a-zA-Z])(?=.*\d)/.test(password)) {
            showFieldError('password', 'Password must contain at least one letter and one number');
            isValid = false;
        }
        
        // Validate country selection
        if (!country) {
            showFieldError('country', 'Please select a country');
            isValid = false;
        }
        
        // Validate city (at least 2 characters)
        if (city.length < 2) {
            showFieldError('city', 'City must be at least 2 characters long');
            isValid = false;
        }
        
        // Validate phone number (at least 10 digits)
        const phoneRegex = /^\+?[\d\s\-\(\)]{10,}$/;
        if (!phoneRegex.test(phone)) {
            showFieldError('phone_number', 'Please enter a valid phone number (at least 10 digits)');
            isValid = false;
        }
        
        return isValid;
    }
    
    // Show field-specific error message
    function showFieldError(fieldName, message) {
        const field = document.getElementById(fieldName);
        const errorDiv = document.getElementById(fieldName + '-error') || createErrorDiv(fieldName);
        
        field.style.borderColor = '#e74c3c';
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
    
    // Create error div if it doesn't exist
    function createErrorDiv(fieldName) {
        const field = document.getElementById(fieldName);
        const errorDiv = document.createElement('div');
        errorDiv.id = fieldName + '-error';
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '14px';
        errorDiv.style.marginTop = '5px';
        field.parentNode.appendChild(errorDiv);
        return errorDiv;
    }
    
    // Clear all error messages
    function clearErrorMessages() {
        const errorMessages = document.querySelectorAll('.error-message');
        const inputs = document.querySelectorAll('input, select');
        
        errorMessages.forEach(error => {
            error.style.display = 'none';
            error.textContent = '';
        });
        
        inputs.forEach(input => {
            input.style.borderColor = '#ddd';
        });
        
        // Hide any existing message
        const existingMessage = document.querySelector('.message');
        if (existingMessage) {
            existingMessage.remove();
        }
    }
    
    // Show general message (success or error)
    function showMessage(message, type) {
        // Remove any existing message
        const existingMessage = document.querySelector('.message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = message;
        
        // Style the message
        messageDiv.style.padding = '15px';
        messageDiv.style.borderRadius = '4px';
        messageDiv.style.marginBottom = '20px';
        messageDiv.style.textAlign = 'center';
        messageDiv.style.fontWeight = 'bold';
        
        if (type === 'success') {
            messageDiv.style.backgroundColor = '#d4edda';
            messageDiv.style.color = '#155724';
            messageDiv.style.border = '1px solid #c3e6cb';
        } else {
            messageDiv.style.backgroundColor = '#f8d7da';
            messageDiv.style.color = '#721c24';
            messageDiv.style.border = '1px solid #f5c6cb';
        }
        
        // Insert message at the top of the form
        const formContainer = document.querySelector('.form-container');
        formContainer.insertBefore(messageDiv, formContainer.firstChild);
        
        // Auto-remove error messages after 5 seconds
        if (type === 'error') {
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
    }
});