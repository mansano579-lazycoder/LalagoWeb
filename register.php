<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - LalaGO</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* Reset and Base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

/* Container */
.form-container {
    background: white;
    border-radius: 24px;
    box-shadow: 0 25px 50px rgba(255, 87, 34, 0.15);
    width: 100%;
    max-width: 520px;
    padding: 50px;
    animation: fadeIn 0.6s ease-out;
    border: 1px solid rgba(255, 152, 0, 0.1);
    position: relative;
    overflow: hidden;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(25px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Logo/Header */
.logo {
    text-align: center;
    margin-bottom: 25px;
}

.logo h1 {
    font-family: 'Poppins', sans-serif;
    font-size: 38px;
    font-weight: 800;
    background: linear-gradient(90deg, #FF5722, #FF9800);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    letter-spacing: -0.5px;
    margin-bottom: 5px;
}

.logo p {
    color: #FF9800;
    font-size: 15px;
    font-weight: 500;
    letter-spacing: 1px;
}

/* Form Title */
.form-title {
    text-align: center;
    margin-bottom: 10px;
}

.form-title h2 {
    font-family: 'Poppins', sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
}

.form-title > p {
    color: #666;
    font-size: 15px;
    margin-bottom: 30px;
    font-weight: 400;
}

/* Form */
#registerForm {
    display: flex;
    flex-direction: column;
    gap: 22px;
}

.input-group {
    position: relative;
}

.input-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #FF5722;
    margin-bottom: 10px;
    transition: color 0.3s;
}

.input-group label i {
    margin-right: 8px;
    font-size: 13px;
}

.input-group input {
    width: 100%;
    padding: 16px 18px;
    padding-left: 52px;
    border: 2px solid #FFE0B2;
    border-radius: 14px;
    font-size: 15px;
    font-family: 'Inter', sans-serif;
    transition: all 0.3s;
    background-color: #FFF8E1;
    color: #333;
}

.input-group input::placeholder {
    color: #FFB74D;
    opacity: 0.7;
}

.input-group input:focus {
    outline: none;
    border-color: #FF9800;
    background-color: white;
    box-shadow: 0 0 0 4px rgba(255, 152, 0, 0.15);
}

.input-group i.fa-input-icon {
    position: absolute;
    left: 18px;
    bottom: 18px;
    color: #FF9800;
    font-size: 18px;
    transition: color 0.3s;
}

.input-group input:focus + i.fa-input-icon {
    color: #FF5722;
}

/* Password Strength */
.password-strength {
    margin-top: 8px;
    height: 6px;
    border-radius: 3px;
    background: #FFE0B2;
    overflow: hidden;
    display: none;
}

.password-strength-bar {
    height: 100%;
    width: 0%;
    border-radius: 3px;
    transition: all 0.3s;
}

/* Terms and Conditions */
.terms {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin: 10px 0 5px;
    padding: 12px;
    background: #FFF3E0;
    border-radius: 10px;
    border-left: 4px solid #FF9800;
}

.terms input[type="checkbox"] {
    accent-color: #FF9800;
    margin-top: 3px;
    flex-shrink: 0;
}

.terms label {
    font-size: 13px;
    color: #666;
    line-height: 1.5;
}

.terms label a {
    color: #FF5722;
    font-weight: 600;
    text-decoration: none;
}

.terms label a:hover {
    text-decoration: underline;
}

/* Submit Button */
button[type="submit"] {
    background: linear-gradient(90deg, #FF5722, #FF9800);
    color: white;
    border: none;
    padding: 18px;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    font-family: 'Poppins', sans-serif;
    margin-top: 15px;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(255, 87, 34, 0.25);
}

button[type="submit"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(255, 87, 34, 0.35);
    background: linear-gradient(90deg, #FF7043, #FFB74D);
}

button[type="submit"]:active {
    transform: translateY(0);
}

button[type="submit"]:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: 0 10px 20px rgba(255, 87, 34, 0.25) !important;
}

/* Login Link */
.login-section {
    text-align: center;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px dashed #FFE0B2;
    color: #FF9800;
    font-size: 15px;
    font-weight: 500;
}

.login-section a {
    color: #FF5722;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
    padding: 5px 15px;
    border-radius: 10px;
    display: inline-block;
    margin-left: 8px;
    border: 2px solid transparent;
}

.login-section a:hover {
    color: white;
    background-color: #FF9800;
    border-color: #FF9800;
    text-decoration: none;
    transform: translateY(-2px);
}

/* Loading Animation */
.spinner {
    display: none;
    width: 24px;
    height: 24px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
    margin: 0 auto;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Alert Styles */
.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-size: 14px;
    font-weight: 500;
    display: none;
    animation: slideIn 0.3s ease-out;
    border-left: 5px solid;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(-15px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert.error {
    background: linear-gradient(to right, #FFEBEE, #FFF);
    color: #D32F2F;
    border-left-color: #F44336;
}

.alert.success {
    background: linear-gradient(to right, #E8F5E9, #FFF);
    color: #388E3C;
    border-left-color: #4CAF50;
}

/* Responsive */
@media (max-width: 580px) {
    .form-container {
        padding: 35px 30px;
        margin: 15px;
        max-width: 100%;
    }
    
    .logo h1 {
        font-size: 32px;
    }
    
    .form-title h2 {
        font-size: 24px;
    }
    
    .input-group input {
        padding: 15px 16px;
        padding-left: 48px;
    }
    
    .name-group {
        flex-direction: column;
        gap: 22px;
    }
}

@media (max-width: 380px) {
    .form-container {
        padding: 25px 20px;
    }
    
    .logo h1 {
        font-size: 28px;
    }
    
    .form-title h2 {
        font-size: 22px;
    }
}

/* Name Group (First & Last Name side by side) */
.name-group {
    display: flex;
    gap: 20px;
}

.name-group .input-group {
    flex: 1;
}

/* Decorative Elements */
.orange-dots {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    gap: 6px;
}

.orange-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #FF9800;
    opacity: 0.6;
}

/* Footer Note */
.footer-note {
    text-align: center;
    margin-top: 20px;
    font-size: 12px;
    color: #FFB74D;
    font-style: italic;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: #FFF3E0;
}

::-webkit-scrollbar-thumb {
    background: #FFB74D;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: #FF9800;
}

/* Tooltip for Referral */
.referral-tooltip {
    position: relative;
    display: inline-block;
    margin-left: 8px;
    cursor: help;
}

.referral-tooltip i {
    color: #FF9800;
    font-size: 14px;
}

.referral-tooltip-text {
    visibility: hidden;
    width: 200px;
    background-color: #333;
    color: white;
    text-align: center;
    border-radius: 6px;
    padding: 10px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 12px;
    font-weight: normal;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.referral-tooltip-text::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #333 transparent transparent transparent;
}

.referral-tooltip:hover .referral-tooltip-text {
    visibility: visible;
    opacity: 1;
}
</style>
</head>

<body>

<div class="form-container">
    <!-- Decorative Dots -->
    <div class="orange-dots">
        <div class="orange-dot"></div>
        <div class="orange-dot"></div>
        <div class="orange-dot"></div>
    </div>
    
    <!-- Logo/Header -->
    <div class="logo">
        <h1>LalaGO</h1>
        <p>JOIN OUR COMMUNITY</p>
    </div>
    
    <!-- Form Title -->
    <div class="form-title">
        <h2>Create Your Account</h2>
        <p>Join LalaGO today and unlock exclusive benefits</p>
    </div>
    
    <!-- Alert Box -->
    <div id="alertBox" class="alert" role="alert"></div>
    
    <!-- Register Form -->
    <form id="registerForm">
        <!-- Name Fields (Side by Side) -->
        <div class="name-group">
            <div class="input-group">
                <label for="firstName"><i class="fas fa-user"></i>First Name</label>
                <i class="fas fa-user fa-input-icon"></i>
                <input type="text" id="firstName" placeholder="" required>
            </div>
            
            <div class="input-group">
                <label for="lastName"><i class="fas fa-user"></i>Last Name</label>
                <i class="fas fa-user fa-input-icon"></i>
                <input type="text" id="lastName" placeholder="" required>
            </div>
        </div>
        
        <!-- Email -->
        <div class="input-group">
            <label for="email"><i class="fas fa-envelope"></i>Email Address</label>
            <i class="fas fa-envelope fa-input-icon"></i>
            <input type="email" id="email" placeholder="you@example.com" required>
        </div>
        
        <!-- Phone -->
        <div class="input-group">
            <label for="phone"><i class="fas fa-phone"></i>Phone Number</label>
            <i class="fas fa-phone fa-input-icon"></i>
            <input type="text" id="phone" placeholder="+1 (555) 123-4567" required>
        </div>
        
        <!-- Password -->
        <div class="input-group">
            <label for="password"><i class="fas fa-lock"></i>Password</label>
            <i class="fas fa-lock fa-input-icon"></i>
            <input type="password" id="password" placeholder="Create a strong password" required>
            <div class="password-strength" id="passwordStrength">
                <div class="password-strength-bar" id="passwordStrengthBar"></div>
            </div>
        </div>
        
        <!-- Referral Code -->
        <div class="input-group">
            <label for="referralCode">
                <i class="fas fa-gift"></i>Referral Code
                <span class="referral-tooltip">
                    <i class="fas fa-question-circle"></i>
                    <span class="referral-tooltip-text">Enter a friend's referral code to get bonus points on signup!</span>
                </span>
            </label>
            <i class="fas fa-gift fa-input-icon"></i>
            <input type="text" id="referralCode" placeholder="Optional - Enter if you have one">
        </div>
        
        <!-- Terms and Conditions -->
        <div class="terms">
            <input type="checkbox" id="terms" required>
            <label for="terms">
                I agree to the <a href="#" onclick="return false;">Terms of Service</a> and <a href="#" onclick="return false;">Privacy Policy</a>. 
                I understand that my data will be processed in accordance with LalaGO's policies.
            </label>
        </div>
        
        <!-- Submit Button -->
        <button type="submit" id="registerBtn">
            <span id="btnText">
                <i class="fas fa-user-plus" style="margin-right: 10px;"></i>Create Account
            </span>
            <div class="spinner" id="spinner"></div>
        </button>
    </form>
    
    <!-- Login Link -->
    <div class="login-section">
        <p>Already have an account?
            <a href="login.php">Sign In Here</a>
        </p>
    </div>
    
    <!-- Footer Note -->
    <div class="footer-note">
        <i class="fas fa-shield-alt"></i> Your information is secured with 256-bit encryption
    </div>
</div>

<!-- Firebase SDKs -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>

<script>
const firebaseConfig = {
  apiKey: "AIzaSyAeIUnO8hDJ19YnruXWNZSW7iCsO9XggPg",
  authDomain: "lalago-1d721.firebaseapp.com",
  projectId: "lalago-1d721",
  storageBucket: "lalago-1d721.appspot.com",
  messagingSenderId: "687925021779",
  appId: "1:687925021779:web:3ab7482380d692f0790aa6",
  measurementId: "G-BGJEW8T98H"
};

firebase.initializeApp(firebaseConfig);
const auth = firebase.auth();
const db = firebase.firestore();

// DOM Elements
const registerForm = document.getElementById('registerForm');
const registerBtn = document.getElementById('registerBtn');
const btnText = document.getElementById('btnText');
const spinner = document.getElementById('spinner');
const alertBox = document.getElementById('alertBox');
const passwordInput = document.getElementById('password');
const passwordStrength = document.getElementById('passwordStrength');
const passwordStrengthBar = document.getElementById('passwordStrengthBar');

// Orange theme colors
const orangeTheme = {
    primary: '#FF5722',
    secondary: '#FF9800',
    light: '#FFE0B2',
    dark: '#E64A19'
};

// Password strength indicator
passwordInput.addEventListener('input', function() {
    const password = this.value;
    let strength = 0;
    
    if (password.length === 0) {
        passwordStrength.style.display = 'none';
        return;
    }
    
    // Length check
    if (password.length >= 8) strength += 25;
    
    // Lowercase check
    if (/[a-z]/.test(password)) strength += 25;
    
    // Uppercase check
    if (/[A-Z]/.test(password)) strength += 25;
    
    // Number/Special char check
    if (/[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength += 25;
    
    passwordStrength.style.display = 'block';
    passwordStrengthBar.style.width = strength + '%';
    
    // Color based on strength
    if (strength < 50) {
        passwordStrengthBar.style.background = '#FF5722'; // Weak - Red orange
    } else if (strength < 75) {
        passwordStrengthBar.style.background = '#FF9800'; // Medium - Orange
    } else {
        passwordStrengthBar.style.background = '#4CAF50'; // Strong - Green
    }
});

// Show alert message
function showAlert(message, type = 'error') {
    alertBox.textContent = message;
    alertBox.className = `alert ${type}`;
    alertBox.style.display = 'block';
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        alertBox.style.display = 'none';
    }, 5000);
}

// Set loading state
function setLoading(isLoading) {
    if (isLoading) {
        registerBtn.disabled = true;
        btnText.style.opacity = '0';
        spinner.style.display = 'block';
        registerBtn.style.background = `linear-gradient(90deg, ${orangeTheme.dark}, ${orangeTheme.primary})`;
    } else {
        registerBtn.disabled = false;
        btnText.style.opacity = '1';
        spinner.style.display = 'none';
        registerBtn.style.background = `linear-gradient(90deg, ${orangeTheme.primary}, ${orangeTheme.secondary})`;
    }
}

// Form submission - UPDATED to match your exact database structure
registerForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;
    const referralCode = document.getElementById('referralCode').value.trim();
    const termsAccepted = document.getElementById('terms').checked;

    // Validation
    if (!firstName || !lastName || !email || !phone || !password) {
        showAlert('Please fill in all required fields.');
        return;
    }

    if (!termsAccepted) {
        showAlert('You must accept the Terms of Service and Privacy Policy to continue.');
        return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showAlert('Please enter a valid email address (e.g., user@example.com).');
        return;
    }

    // Phone validation (basic)
    if (phone.length < 6) {
        showAlert('Please enter a valid phone number.');
        return;
    }

    // Password validation
    if (password.length < 6) {
        showAlert('Password must be at least 6 characters long.');
        return;
    }

    setLoading(true);

    // Create user in Firebase Auth
    auth.createUserWithEmailAndPassword(email, password)
        .then((userCredential) => {
            const uid = userCredential.user.uid;
            const timestamp = firebase.firestore.FieldValue.serverTimestamp();
            
            // Create user data EXACTLY matching your database structure
            const userData = {
                active: true,
                appIdentifier: "Web Registration",
                createdAt: timestamp,
                email: email,
                fcmToken: "",
                firstName: firstName,
                id: uid,
                lastName: lastName,
                lastOnlineTimestamp: timestamp,
                location: {
                    latitude: 0.01,
                    longitude: 0.01
                },
                phoneNumber: phone,
                profilePictureURL: "",
                role: "customer",
                settings: {
                    newArrivals: true,
                    orderUpdates: true,
                    promotions: true,
                    pushNewMessages: true
                },
                shippingAddress: [],
                wallet_amount: 0,
                referralCode: referralCode || ""
            };

            // Save to Firestore with the exact structure
            return db.collection('users').doc(uid).set(userData);
        })
        .then(() => {
            showAlert('🎉 Registration successful! Redirecting to login...', 'success');
            
            // Celebration effect
            registerBtn.innerHTML = '<i class="fas fa-check"></i> Success!';
            registerBtn.style.background = `linear-gradient(90deg, #4CAF50, #8BC34A)`;
            
            // Send email verification
            return auth.currentUser.sendEmailVerification();
        })
        .then(() => {
            // Sign out and redirect to login
            return auth.signOut();
        })
        .then(() => {
            // Redirect after delay
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        })
        .catch(err => {
            console.error("Registration error:", err);
            
            // User-friendly error messages
            let errorMessage = '';
            switch (err.code) {
                case 'auth/email-already-in-use':
                    errorMessage = 'This email is already registered. Please login or use a different email.';
                    break;
                case 'auth/invalid-email':
                    errorMessage = 'Invalid email address format.';
                    break;
                case 'auth/weak-password':
                    errorMessage = 'Password is too weak. Please use at least 6 characters.';
                    break;
                case 'auth/operation-not-allowed':
                    errorMessage = 'Email/password accounts are not enabled. Please contact support.';
                    break;
                case 'auth/network-request-failed':
                    errorMessage = 'Network error. Please check your internet connection.';
                    break;
                default:
                    errorMessage = 'Registration failed. Please try again.';
            }
            
            showAlert(`❌ ${errorMessage}`);
            setLoading(false);
            
            // Add shake animation to form on error
            registerForm.style.animation = 'none';
            setTimeout(() => {
                registerForm.style.animation = 'shake 0.5s';
            }, 10);
        });
});

// Auto-focus first name input on load
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('firstName').focus();
    
    // Add shake animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(style);
    
    // Add input validation styling
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '' && this.hasAttribute('required')) {
                this.style.borderColor = '#F44336';
            } else {
                this.style.borderColor = '#FFE0B2';
            }
        });
        
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.style.borderColor = '#FFE0B2';
            }
        });
    });
});

// Add input focus effects
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.querySelector('label').style.color = orangeTheme.primary;
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.querySelector('label').style.color = orangeTheme.secondary;
    });
});

// Terms checkbox styling
const termsCheckbox = document.getElementById('terms');
termsCheckbox.addEventListener('change', function() {
    if (this.checked) {
        this.parentElement.style.borderLeftColor = '#4CAF50';
    } else {
        this.parentElement.style.borderLeftColor = '#FF9800';
    }
});
</script>

</body>
</html>