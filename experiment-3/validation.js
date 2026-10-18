/**
 * Experiment 3: User Registration Form Validation
 * JavaScript Multi-Step Guided Form Engine
 */

let currentStep = 1;

function goToStep(targetStep) {
  // If moving forward, validate the current step first
  if (targetStep > currentStep) {
    if (currentStep === 1 && !validateStep1()) {
      return;
    }
    if (currentStep === 2 && !validateStep2()) {
      return;
    }
  }

  // Update Sections
  document.querySelectorAll('.form-step-section').forEach(section => {
    section.classList.remove('active');
  });
  document.getElementById(`stepSection${targetStep}`).classList.add('active');

  // Update Progress Nodes
  updateStepIndicator(targetStep);
  currentStep = targetStep;

  // Scroll wizard into view smoothly
  document.querySelector('.wizard-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function updateStepIndicator(step) {
  for (let i = 1; i <= 3; i++) {
    const node = document.getElementById(`stepNode${i}`);
    node.classList.remove('active', 'completed');
    if (i < step) {
      node.classList.add('completed');
    } else if (i === step) {
      node.classList.add('active');
    }
  }

  // Connectors
  const conn1 = document.getElementById('connector1');
  const conn2 = document.getElementById('connector2');
  if (step > 1) conn1.classList.add('completed');
  else conn1.classList.remove('completed');

  if (step > 2) conn2.classList.add('completed');
  else conn2.classList.remove('completed');
}

/* --- Validation Functions --- */

function validateStep1() {
  let isValid = true;

  // 1. Full Name
  const fullName = document.getElementById('fullName').value.trim();
  const nameRegex = /^[A-Za-z\s\.]+$/;
  if (!fullName) {
    showError('grpFullName', 'errFullName', 'Full Name is required.');
    isValid = false;
  } else if (fullName.length < 2) {
    showError('grpFullName', 'errFullName', 'Name must be at least 2 characters.');
    isValid = false;
  } else if (!nameRegex.test(fullName)) {
    showError('grpFullName', 'errFullName', 'Name must contain only valid letters, dots, and spaces.');
    isValid = false;
  } else {
    clearError('grpFullName', 'errFullName');
  }

  // 2. Email Address
  const email = document.getElementById('email').value.trim();
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  if (!email) {
    showError('grpEmail', 'errEmail', 'Email address is required.');
    isValid = false;
  } else if (!emailRegex.test(email)) {
    showError('grpEmail', 'errEmail', 'Please enter a valid email format (e.g., name@domain.com).');
    isValid = false;
  } else {
    clearError('grpEmail', 'errEmail');
  }

  // 3. Mobile Number
  const mobile = document.getElementById('mobile').value.trim();
  const mobileRegex = /^[0-9]{10}$/;
  if (!mobile) {
    showError('grpMobile', 'errMobile', 'Mobile number is required.');
    isValid = false;
  } else if (!mobileRegex.test(mobile)) {
    showError('grpMobile', 'errMobile', 'Mobile number must contain exactly 10 digits.');
    isValid = false;
  } else {
    clearError('grpMobile', 'errMobile');
  }

  // 4. Gender Selection
  const genderRadios = document.getElementsByName('gender');
  let genderSelected = false;
  for (let r of genderRadios) {
    if (r.checked) {
      genderSelected = true;
      break;
    }
  }
  if (!genderSelected) {
    showError('grpGender', 'errGender', 'Gender must be selected.');
    isValid = false;
  } else {
    clearError('grpGender', 'errGender');
  }

  // 5. Date of Birth
  const dob = document.getElementById('dob').value;
  if (!dob) {
    showError('grpDob', 'errDob', 'Date of Birth is required.');
    isValid = false;
  } else {
    clearError('grpDob', 'errDob');
  }

  return isValid;
}

function validateStep2() {
  let isValid = true;

  // 6. Address
  const address = document.getElementById('address').value.trim();
  if (!address) {
    showError('grpAddress', 'errAddress', 'Residential address must not be empty.');
    isValid = false;
  } else if (address.length < 5) {
    showError('grpAddress', 'errAddress', 'Please provide a complete address (minimum 5 characters).');
    isValid = false;
  } else {
    clearError('grpAddress', 'errAddress');
  }

  return isValid;
}

function validateStep3() {
  let isValid = true;

  // 7. Password
  const password = document.getElementById('password').value;
  if (!password) {
    showError('grpPassword', 'errPassword', 'Password is required.');
    isValid = false;
  } else if (password.length < 6) {
    showError('grpPassword', 'errPassword', 'Password must satisfy the minimum length requirement (at least 6 characters).');
    isValid = false;
  } else {
    clearError('grpPassword', 'errPassword');
  }

  // 8. Confirm Password
  const confirmPassword = document.getElementById('confirmPassword').value;
  if (!confirmPassword) {
    showError('grpConfirmPassword', 'errConfirmPassword', 'Please confirm your password.');
    isValid = false;
  } else if (confirmPassword !== password) {
    showError('grpConfirmPassword', 'errConfirmPassword', 'Confirm Password must match Password.');
    isValid = false;
  } else {
    clearError('grpConfirmPassword', 'errConfirmPassword');
  }

  // 9. Terms and Conditions
  const termsCheck = document.getElementById('termsCheck');
  if (!termsCheck.checked) {
    showError('grpTerms', 'errTerms', 'Terms and Conditions must be accepted.');
    isValid = false;
  } else {
    clearError('grpTerms', 'errTerms');
  }

  return isValid;
}

/**
 * Master validation function called on submit
 */
function validateForm() {
  const step1Valid = validateStep1();
  const step2Valid = validateStep2();
  const step3Valid = validateStep3();

  if (!step1Valid) {
    goToStep(1);
    return false;
  }
  if (!step2Valid) {
    goToStep(2);
    return false;
  }
  if (!step3Valid) {
    goToStep(3);
    return false;
  }

  // If all validations pass
  displaySuccessOutput();
  return true;
}

function displaySuccessOutput() {
  const form = document.getElementById('userRegForm');
  const successBox = document.getElementById('globalSuccessBox');
  const receipt = document.getElementById('summaryReceipt');

  // Fill Receipt details
  document.getElementById('recName').textContent = document.getElementById('fullName').value.trim();
  document.getElementById('recEmail').textContent = document.getElementById('email').value.trim();
  document.getElementById('recMobile').textContent = document.getElementById('mobile').value.trim();

  const genderRadios = document.getElementsByName('gender');
  let selectedGender = 'Not Specified';
  for (let r of genderRadios) {
    if (r.checked) {
      selectedGender = r.value;
      break;
    }
  }
  document.getElementById('recGender').textContent = selectedGender;
  document.getElementById('recDob').textContent = document.getElementById('dob').value;
  document.getElementById('recAddress').textContent = document.getElementById('address').value.trim();

  // Hide form sections & show success message
  form.style.display = 'none';
  successBox.style.display = 'flex';
  receipt.style.display = 'block';

  // Mark all steps completed
  for (let i = 1; i <= 3; i++) {
    document.getElementById(`stepNode${i}`).classList.add('completed');
  }

  successBox.scrollIntoView({ behavior: 'smooth' });
}

function showError(groupId, errorId, message) {
  const grp = document.getElementById(groupId);
  const err = document.getElementById(errorId);
  if (grp) grp.classList.add('has-error');
  if (err) err.textContent = message;
}

function clearError(groupId, errorId) {
  const grp = document.getElementById(groupId);
  const err = document.getElementById(errorId);
  if (grp) grp.classList.remove('has-error');
  if (err) err.textContent = '';
}

function resetForm() {
  document.getElementById('userRegForm').reset();
  document.getElementById('userRegForm').style.display = 'block';
  document.getElementById('globalSuccessBox').style.display = 'none';
  document.getElementById('summaryReceipt').style.display = 'none';

  // Clear all errors
  document.querySelectorAll('.form-group').forEach(grp => grp.classList.remove('has-error'));
  document.querySelectorAll('.error-msg').forEach(msg => msg.textContent = '');

  // Reset to step 1
  goToStep(1);
}

// Live Error Clearing on User Input
document.addEventListener('DOMContentLoaded', () => {
  ['fullName', 'email', 'mobile', 'dob', 'address', 'password', 'confirmPassword'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', () => {
        const grp = el.closest('.form-group');
        if (grp) grp.classList.remove('has-error');
      });
    }
  });

  document.querySelectorAll('input[name="gender"]').forEach(radio => {
    radio.addEventListener('change', () => {
      document.getElementById('grpGender').classList.remove('has-error');
    });
  });

  const termsCheck = document.getElementById('termsCheck');
  if (termsCheck) {
    termsCheck.addEventListener('change', () => {
      document.getElementById('grpTerms').classList.remove('has-error');
    });
  }
});
