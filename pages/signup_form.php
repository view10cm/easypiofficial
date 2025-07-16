<?php
session_start();
if (!empty($_SESSION['account_id'])) {
    header('Location: dashboard.php'); // or wherever your homepage is
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EasyPi - Sign Up</title>
  <link rel="icon" type="image/png" href="../assets/titlelogo.png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="../css/sign_up.css">
  <style>
    /* Custom styles for responsive terms modal */
    .modal-dialog {
      max-width: 90vw;
      width: 90vw;
      margin: 1rem auto;
    }
    
    .modal-content {
      border-radius: 0.5rem;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
    }
    
    .modal-body {
      flex: 1;
      overflow-y: auto;
      padding: 1.5rem;
    }
    
    .modal-header {
      padding: 1.5rem 1.5rem 1rem 1.5rem;
      border-bottom: 1px solid #e9ecef;
    }
    
    .modal-footer {
      padding: 1rem 1.5rem 1.5rem 1.5rem;
      border-top: 1px solid #e9ecef;
      flex-direction: row;
      gap: 1rem;
    }
    
    @media (max-width: 992px) {
      .modal-dialog {
        max-width: 95vw;
        width: 95vw;
        margin: 0.5rem auto;
      }
      
      .modal-content {
        max-height: 95vh;
      }
    }
    
    @media (max-width: 768px) {
      .modal-dialog {
        max-width: 98vw;
        width: 98vw;
        margin: 0.25rem auto;
      }
      
      .modal-content {
        max-height: 98vh;
      }
      
      .modal-header {
        padding: 1rem;
      }
      
      .modal-body {
        padding: 1rem;
      }
      
      .modal-footer {
        padding: 1rem;
        flex-direction: column;
        gap: 0.5rem;
      }
      
      .modal-footer .btn {
        width: 100%;
      }
    }
    
    @media (max-width: 576px) {
      .modal-header {
        padding: 0.75rem;
      }
      
      .modal-body {
        padding: 0.75rem;
      }
      
      .modal-footer {
        padding: 0.75rem;
      }
      
      .modal-title {
        font-size: 1.1rem;
      }
    }
    
    /* Terms content styling */
    .terms-content h6 {
      color: #333;
      margin-top: 1.5rem;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }
    
    .terms-content h6:first-child {
      margin-top: 0;
    }
    
    .terms-content p {
      margin-bottom: 1rem;
      line-height: 1.6;
      color: #666;
      font-size: 0.95rem;
    }
    
    .terms-content ul {
      margin-bottom: 1rem;
      padding-left: 1.5rem;
    }
    
    .terms-content ul li {
      margin-bottom: 0.5rem;
      color: #666;
      line-height: 1.5;
      font-size: 0.95rem;
    }
    
    /* Scrollbar styling for webkit browsers */
    .modal-body::-webkit-scrollbar {
      width: 6px;
    }
    
    .modal-body::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }
    
    .modal-body::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }
    
    .modal-body::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }
  </style>
</head>
<body>
  <div class="background-pattern"></div>

  <div class="signup-container">
    <div class="row g-0">
      <div class="col-lg-5 left-side d-none d-lg-flex">
        <img src="../assets/register.png" alt="Illustration" class="left-side-img">
      </div>
      <div class="col-lg-7 right-side">
        <h2 class="form-title">Sign Up</h2>
        <form id="signupForm">
          <div class="form-group">
            <i class="fas fa-user-circle form-icon"></i>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required>
          </div>
          <div class="form-group">
            <i class="fas fa-envelope form-icon"></i>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required>
          </div>
          <div class="form-group">
            <i class="fas fa-lock form-icon"></i>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
          </div>
          <div class="form-group">
            <i class="fas fa-lock form-icon"></i>
            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="agreeTerms" required>
            <label class="form-check-label" for="agreeTerms">I agree to all <a href="#" id="termsLink" data-bs-toggle="modal" data-bs-target="#termsModal" style="text-decoration: none;">terms</a></label>
          </div>
          <button type="submit" class="btn btn-register" id="registerBtn">Register</button>
          <div class="signin-link">
            Already have an account? <a href="sign_in.php">Sign In</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Terms and Conditions Modal -->
  <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="terms-content">
            <h6>1. Acceptance of Terms</h6>
            <p>By using EasyPi, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our service.</p>
            
            <h6>2. Description of Service</h6>
            <p>EasyPi is a task management application that helps users organize and track their daily tasks and activities. Our service includes task creation, editing, prioritization, and progress tracking.</p>
            
            <h6>3. User Accounts</h6>
            <p>To use our service, you must create an account by providing accurate and complete information. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
            
            <h6>4. User Responsibilities</h6>
            <ul>
              <li>You must provide accurate and truthful information when creating your account</li>
              <li>You are responsible for maintaining the security of your account</li>
              <li>You must not use the service for any illegal or unauthorized purposes</li>
              <li>You must not interfere with or disrupt the service</li>
            </ul>
            
            <h6>5. Privacy Policy</h6>
            <p>Your privacy is important to us. We collect and use your personal information as described in our Privacy Policy, which is incorporated into these Terms by reference.</p>
            
            <h6>6. Data and Content</h6>
            <p>You retain ownership of the content you create using our service. However, you grant us a license to use, store, and process your content to provide the service to you.</p>
            
            <h6>7. Service Availability</h6>
            <p>We strive to provide continuous service availability, but we do not guarantee uninterrupted access. The service may be temporarily unavailable due to maintenance, updates, or technical issues.</p>
            
            <h6>8. Limitation of Liability</h6>
            <p>EasyPi is provided "as is" without warranties of any kind. We shall not be liable for any direct, indirect, incidental, or consequential damages arising from your use of the service.</p>
            
            <h6>9. Termination</h6>
            <p>We may terminate or suspend your account at any time for violation of these terms. You may also terminate your account at any time by contacting us.</p>
            
            <h6>10. Changes to Terms</h6>
            <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting. Your continued use of the service constitutes acceptance of any changes.</p>
            
            <h6>11. Contact Information</h6>
            <p>If you have any questions about these Terms and Conditions, please contact us through the application's support features.</p>
            
            <p class="mt-3"><strong>Last updated: July 16, 2025</strong></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="acceptTermsBtn" data-bs-dismiss="modal">Accept Terms</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script type="module" src="../scripts/sign_up.js"></script>
</body>
</html>