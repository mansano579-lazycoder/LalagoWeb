<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - LalaGO</title>
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
    max-width: 460px;
    padding: 50px;
    animation: fadeIn 0.6s ease-out;
    border: 1px solid rgba(255, 152, 0, 0.1);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(25px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Logo/Header */
.logo {
    text-align: center;
    margin-bottom: 30px;
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

/* Form */
#loginForm {
    display: flex;
    flex-direction: column;
    gap: 28px;
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

.input-group input {
    width: 100%;
    padding: 18px 20px;
    padding-left: 56px;
    border: 2px solid #FFE0B2;
    border-radius: 16px;
    font-size: 16px;
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

.input-group i {
    position: absolute;
    left: 20px;
    bottom: 20px;
    color: #FF9800;
    font-size: 20px;
    transition: color 0.3s;
}

.input-group input:focus + i {
    color: #FF5722;
}

/* Forgot Password */
.forgot-link {
    display: inline-block;
    color: #FF9800;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 15px;
    transition: all 0.3s;
    padding: 5px 10px;
    border-radius: 8px;
    align-self: flex-start;
}

.forgot-link:hover {
    color: #FF5722;
    text-decoration: none;
    background-color: #FFF3E0;
    transform: translateX(5px);
}

.forgot-link i {
    margin-right: 8px;
    font-size: 12px;
}

/* Submit Button */
button[type="submit"] {
    background: linear-gradient(90deg, #FF5722, #FF9800);
    color: white;
    border: none;
    padding: 20px;
    border-radius: 16px;
    font-size: 17px;
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

/* Sign Up Link */
.signup-section {
    text-align: center;
    margin-top: 35px;
    padding-top: 30px;
    border-top: 2px dashed #FFE0B2;
    color: #FF9800;
    font-size: 15px;
    font-weight: 500;
}

.signup-section a {
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

.signup-section a:hover {
    color: white;
    background-color: #FF9800;
    border-color: #FF9800;
    text-decoration: none;
    transform: translateY(-2px);
}

/* Loading Animation */
.spinner {
    display: none;
    width: 26px;
    height: 26px;
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
    padding: 18px 22px;
    border-radius: 14px;
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
@media (max-width: 520px) {
    .form-container {
        padding: 35px 30px;
        margin: 15px;
        max-width: 100%;
    }
    
    .logo h1 {
        font-size: 32px;
    }
    
    .input-group input {
        padding: 16px 18px;
        padding-left: 52px;
    }
}

@media (max-width: 380px) {
    .form-container {
        padding: 25px 20px;
    }
    
    .logo h1 {
        font-size: 28px;
    }
}

/* Password Toggle */
.password-toggle {
    position: absolute;
    right: 20px;
    bottom: 20px;
    background: none;
    border: none;
    color: #FF9800;
    cursor: pointer;
    font-size: 20px;
    transition: all 0.3s;
    background: rgba(255, 152, 0, 0.1);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle:hover {
    color: #FF5722;
    background: rgba(255, 87, 34, 0.15);
    transform: scale(1.1);
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
    margin-top: 25px;
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
        <p>Tausog Food Delivery Service</p>
    </div>
    
    <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 16px;">
        Welcome back! Please sign in to your account.
    </p>
    
    <!-- Alert Box -->
    <div id="alertBox" class="alert" role="alert"></div>
    
    <!-- Login Form -->
    <form id="loginForm">
        <div class="input-group">
            <label for="email">
                <i class="fas fa-user-circle" style="margin-right: 8px;"></i>Email Address
            </label>
            <i class="fas fa-envelope"></i>
            <input type="email" id="email" placeholder="you@example.com" required>
        </div>
        
        <div class="input-group">
            <label for="password">
                <i class="fas fa-lock" style="margin-right: 8px;"></i>Password
            </label>
            <i class="fas fa-key"></i>
            <input type="password" id="password" placeholder="Enter your password" required>
            <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <a href="forgot.php" class="forgot-link">
                <i class="fas fa-question-circle"></i> Forgot Password?
            </a>
            
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="remember" style="accent-color: #FF9800;">
                <label for="remember" style="color: #FF9800; font-size: 14px; cursor: pointer;">Remember me</label>
            </div>
        </div>
        
        <button type="submit" id="loginBtn">
            <span id="btnText">
                <i class="fas fa-sign-in-alt" style="margin-right: 10px;"></i>Sign In
            </span>
            <div class="spinner" id="spinner"></div>
        </button>
    </form>
    
    <!-- Sign Up Section -->
    <div class="signup-section">
        <p>New to LalaGO?
            <a href="register.php">Create Account</a>
        </p>
    </div>
    
    <!-- Footer Note -->
    <div class="footer-note">
        <i class="fas fa-shield-alt"></i> Your security is our priority
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
const db   = firebase.firestore();

// DOM Elements
const loginForm = document.getElementById('loginForm');
const loginBtn = document.getElementById('loginBtn');
const btnText = document.getElementById('btnText');
const spinner = document.getElementById('spinner');
const alertBox = document.getElementById('alertBox');
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');

// Orange theme colors
const orangeTheme = {
    primary: '#FF5722',
    secondary: '#FF9800',
    light: '#FFE0B2',
    dark: '#E64A19'
};

// Password visibility toggle
togglePassword.addEventListener('click', function() {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.innerHTML = type === 'password' ? 
        '<i class="fas fa-eye"></i>' : 
        '<i class="fas fa-eye-slash"></i>';
    
    this.style.color = type === 'password' ? orangeTheme.secondary : orangeTheme.primary;
});

// Show alert message
function showAlert(message, type = 'error') {
    alertBox.textContent = message;
    alertBox.className = `alert ${type}`;
    alertBox.style.display = 'block';
    
    setTimeout(() => {
        alertBox.style.display = 'none';
    }, 5000);
}

// Set loading state
function setLoading(isLoading) {
    if (isLoading) {
        loginBtn.disabled = true;
        btnText.style.opacity = '0';
        spinner.style.display = 'block';
        loginBtn.style.background = `linear-gradient(90deg, ${orangeTheme.dark}, ${orangeTheme.primary})`;
    } else {
        loginBtn.disabled = false;
        btnText.style.opacity = '1';
        spinner.style.display = 'none';
        loginBtn.style.background = `linear-gradient(90deg, ${orangeTheme.primary}, ${orangeTheme.secondary})`;
    }
}

// Create PHP session for the logged-in user
async function createPHPSession(user, rememberMe) {
    try {
        // Try to get user's full details from Firestore
        let displayName = '';
        let firstName = '';
        let lastName = '';
        
        try {
            const userDoc = await db.collection('users').doc(user.uid).get();
            if (userDoc.exists) {
                const userData = userDoc.data();
                displayName = userData.displayName || userData.name || '';
                firstName = userData.firstName || userData.first_name || '';
                lastName = userData.lastName || userData.last_name || '';
            }
        } catch (firestoreError) {
            console.log('Could not fetch user details:', firestoreError);
        }
        
        // Prepare session data with ALL needed fields
        const sessionData = {
            uid: user.uid,
            email: user.email,
            displayName: displayName,
            firstName: firstName,
            lastName: lastName
        };
        
        console.log('Creating PHP session with data:', sessionData);
        
        // Send as JSON
        const response = await fetch('inc/set_session.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(sessionData)
        });
        
        console.log('Session response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Session HTTP error:', errorText);
            throw new Error(`Session failed: HTTP ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Session response data:', data);
        
        if (data.status === 'success') {
            console.log('✅ PHP session created successfully');
            return true;
        } else {
            console.error('Session creation failed:', data.message);
            return false;
        }
        
    } catch (error) {
        console.error('PHP session creation error:', error);
        return false;
    }
}

// Get user's display name from Firestore
async function getUserDisplayName(uid) {
    try {
        const doc = await db.collection('users').doc(uid).get();
        if (doc.exists) {
            const data = doc.data();
            return data.firstName || data.name || data.displayName || '';
        }
        return '';
    } catch (error) {
        console.error('Error fetching user data:', error);
        return '';
    }
}

// Check PHP session status
async function checkPHPSession() {
    try {
        const response = await fetch('inc/check_session.php');
        const data = await response.json();
        console.log('Current PHP session:', data);
        return data;
    } catch (error) {
        console.error('Failed to check PHP session:', error);
        return { isLoggedIn: false };
    }
}

// Form submission
loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const rememberMe = document.getElementById('remember').checked;
    
    if (!email || !password) {
        showAlert('Please enter both email and password.');
        document.getElementById('email').focus();
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showAlert('Please enter a valid email address (e.g., user@example.com).');
        document.getElementById('email').focus();
        return;
    }
    
    setLoading(true);
    
    try {
        // Set persistence
        const persistence = rememberMe ? 
            firebase.auth.Auth.Persistence.LOCAL : 
            firebase.auth.Auth.Persistence.SESSION;
        
        await auth.setPersistence(persistence);
        
        // Sign in with Firebase
        const userCredential = await auth.signInWithEmailAndPassword(email, password);
        const user = userCredential.user;
        const uid = user.uid;
        
        console.log('✅ Firebase login successful, UID:', uid);
        
        // Check Firestore user data
        const userDoc = await db.collection('users').doc(uid).get();
        
        if (!userDoc.exists) {
            throw new Error('User record not found');
        }
        
        const userData = userDoc.data();
        
        // Check if account is active
        if (userData.active === false) {
            throw new Error('Account is deactivated. Please contact support.');
        }
        
        // Update last online timestamp
        await db.collection('users').doc(uid).update({
            lastOnlineTimestamp: firebase.firestore.FieldValue.serverTimestamp()
        });
        
        // Get user's display name
        const displayName = await getUserDisplayName(uid);
        
        // CREATE PHP SESSION - THIS IS CRITICAL
        console.log('Creating PHP session...');
        const sessionCreated = await createPHPSession(user, rememberMe);
        
        if (sessionCreated) {
            console.log('✅ PHP session created successfully');
            showAlert('✅ Login successful! Creating session...', 'success');
        } else {
            console.warn('⚠️ PHP session not created, some features might not work');
            showAlert('⚠️ Login successful but session setup incomplete', 'error');
        }
        
        // Store user info in localStorage for header.js
        localStorage.setItem('userInfo', JSON.stringify({
            uid: uid,
            email: user.email,
            name: displayName || user.email.split('@')[0],
            photoURL: user.photoURL || ''
        }));
        
        localStorage.setItem('isLoggedIn', 'true');
        
        // Show final success message
        const welcomeName = displayName || user.email.split('@')[0];
        showAlert(`🎉 Welcome back, ${welcomeName}! Redirecting...`, 'success');
        
        // Update button to show success
        loginBtn.innerHTML = '<i class="fas fa-check"></i> Success! Redirecting...';
        loginBtn.style.background = `linear-gradient(90deg, #4CAF50, #8BC34A)`;
        
        // Get redirect URL - use the one from URL or go to index.php by default
        const urlParams = new URLSearchParams(window.location.search);
        let redirectUrl = urlParams.get('redirect') || 'index.php'; // Changed to index.php
        
        // Go directly to index.php
        redirectUrl = 'index.php'; // Changed to index.php
        
        console.log('Redirecting to:', redirectUrl);
        
        // Wait a bit then redirect
        setTimeout(() => {
            window.location.href = redirectUrl;
        }, 2000);
        
    } catch (error) {
        console.error('Login error:', error);
        
        let errorMessage = '';
        switch (error.message) {
            case 'User record not found':
                errorMessage = 'Account not found. Please check your email or register.';
                break;
            case 'Account is deactivated. Please contact support.':
                errorMessage = 'Account is deactivated. Please contact support.';
                break;
            default:
                switch (error.code) {
                    case 'auth/user-not-found':
                        errorMessage = 'No account found with this email.';
                        break;
                    case 'auth/wrong-password':
                        errorMessage = 'Incorrect password. Please try again.';
                        break;
                    case 'auth/invalid-email':
                        errorMessage = 'Invalid email format.';
                        break;
                    case 'auth/user-disabled':
                        errorMessage = 'Account has been disabled.';
                        break;
                    case 'auth/too-many-requests':
                        errorMessage = 'Too many attempts. Try again later.';
                        break;
                    case 'auth/network-request-failed':
                        errorMessage = 'Network error. Check your connection.';
                        break;
                    default:
                        errorMessage = 'Login failed. Please try again.';
                }
        }
        
        showAlert(`⚠️ ${errorMessage}`);
        setLoading(false);
        
        loginForm.style.animation = 'none';
        setTimeout(() => {
            loginForm.style.animation = 'shake 0.5s';
        }, 10);
    }
});

// Auto-focus email input on load
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('email').focus();
    
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
    
    // Check if user is already logged in via Firebase
    auth.onAuthStateChanged(async (user) => {
        if (user) {
            console.log('User already logged in via Firebase');
            
            // Check PHP session
            const sessionCheck = await checkPHPSession();
            
            if (!sessionCheck.isLoggedIn) {
                console.log('PHP session missing, creating one...');
                await createPHPSession(user, false);
            }
            
            // Check for redirect parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('redirect')) {
                setTimeout(() => {
                    window.location.href = urlParams.get('redirect');
                }, 500);
            } else {
                // If no redirect parameter and already logged in, go to index.php
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 500);
            }
        }
    });
    
    // Check URL for redirect parameter
    const urlParams = new URLSearchParams(window.location.search);
    const redirect = urlParams.get('redirect');
    if (redirect) {
        console.log('Will redirect to after login:', redirect);
    }
});

// Enter key to submit
loginForm.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && !loginBtn.disabled) {
        loginBtn.click();
    }
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

// Test credentials for local development
window.addEventListener('load', () => {
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        // Optional: Add test credentials here
    }
});
</script>

</body>
</html>