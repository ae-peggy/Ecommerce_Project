// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const submitBtn = document.querySelector('.login-btn');
    
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
        submitBtn.textContent = 'Logging in...';
        
        // Collect form data
        const formData = new FormData(form);
        
        // Debug: Log what we're sending
        console.log('Sending login data:');
        for (let [key, value] of formData.entries()) {
            if (key === 'password') {
                console.log(key + ': [HIDDEN]');
            } else {
                console.log(key + ': ' + value);
            }
        }
        
        // Send data to server using fetch API
        fetch('../actions/login_customer_action.php', {
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
                    
                    // Redirect after 1.5 seconds
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
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
            submitBtn.textContent = 'Login';
        });
    });
    
    // Form validation function
    function validateForm() {
        let isValid = true;
        
        // Get form fields
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        
        // Validate email using regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showFieldError('email', 'Please enter a valid email address');
            isValid = false;
        }
        
        // Validate password (not empty)
        if (password.length === 0) {
            showFieldError('password', 'Password is required');
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
        const inputs = document.querySelectorAll('input');
        
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
                if (messageDiv && messageDiv.parentNode) {
                    messageDiv.remove();
                }
            }, 5000);
        }
    }
}); 