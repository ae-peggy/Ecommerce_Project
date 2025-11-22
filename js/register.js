// Main initialization function
function initializeForm() {
    console.log('Initializing form...');
    
    const form = document.getElementById('registrationForm');
    const submitBtn = document.querySelector('.submit-btn');
    const artisanCheckbox = document.getElementById('register_as_artisan');
    const tierSelection = document.getElementById('tierSelection');
    const tierSelect = document.getElementById('artisan_tier');
    
    if (!form || !artisanCheckbox || !tierSelection) {
        console.error('Required elements not found!');
        return;
    }
    
    console.log('All elements found, setting up listeners...');
    
    // Show/hide tier selection based on checkbox
    function toggleTierSelection() {
        const isChecked = artisanCheckbox.checked;
        console.log('Toggling tier selection, checkbox checked:', isChecked);
        
        if (isChecked) {
            // Use cssText with !important to override inline styles
            tierSelection.style.cssText = 'display: block !important; opacity: 1 !important; margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(220, 38, 38, 0.1); visibility: visible !important;';
            
            if (tierSelect) {
                tierSelect.setAttribute('required', 'required');
                tierSelect.disabled = false;
                console.log('Tier selection shown and required');
            }
        } else {
            tierSelection.style.cssText = 'display: none !important; opacity: 0 !important;';
            
            if (tierSelect) {
                tierSelect.removeAttribute('required');
                tierSelect.value = '';
                tierSelect.disabled = true;
            }
        }
    }
    
    // Set up event listener
    console.log('Attaching change listener to checkbox...');
    artisanCheckbox.addEventListener('change', function(e) {
        console.log('Change event fired, checked:', this.checked);
        toggleTierSelection();
    });
    
    // Initialize on page load
    toggleTierSelection();
    console.log('Initialization complete!');
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
    
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
        
        // Ensure artisan_tier is included if checkbox is checked
        const isArtisan = document.getElementById('register_as_artisan').checked;
        if (isArtisan) {
            const tierValue = document.getElementById('artisan_tier').value;
            if (tierValue) {
                formData.set('artisan_tier', tierValue);
            }
        }
        
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
        
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const country = document.getElementById('country').value;
        const city = document.getElementById('city').value.trim();
        const phone = document.getElementById('phone_number').value.trim();
        
         // Validate name (at least 2 characters, only letters, spaces, hyphens, and apostrophes)
        if (name.length < 2) {
            showFieldError('name', 'Name must be at least 2 characters long');
            isValid = false;
        } else if (!/^[a-zA-Z\s'\-]+$/.test(name)) {
            showFieldError('name', 'Name can only contain letters, spaces, hyphens, and apostrophes');
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
        
        // Validate artisan tier if registering as artisan
        const isArtisan = document.getElementById('register_as_artisan').checked;
        if (isArtisan) {
            const tier = document.getElementById('artisan_tier').value;
            if (!tier || (tier !== '1' && tier !== '2')) {
                showFieldError('artisan_tier', 'Please select a tier');
                isValid = false;
            }
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
}

// Run initialization when DOM is ready OR immediately if already ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeForm);
} else {
    // DOM already loaded, run immediately
    initializeForm();
}