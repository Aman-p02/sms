<?php
// chatbot_api.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['message'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit();
}

$message = strtolower(trim($_POST['message']));

// Rule-Based Responses
$rules = [
    [
        'keywords' => ['apply', 'how to apply', 'application process', 'register'],
        'response' => "To apply for a scholarship, please follow these steps:<br>1. Register an account as a student.<br>2. Log in and go to the 'Available Scholarships' section.<br>3. Fill out the application form with your details.<br>4. Upload the required documents.<br>5. Submit the application."
    ],
    [
        'keywords' => ['eligibility', 'eligible', 'who can apply', 'criteria'],
        'response' => "Eligibility depends on the specific scholarship. Generally, it's based on your GPA (e.g., above 7.0), family income, and attendance. You can view the specific criteria by clicking 'View Details' on any scholarship in the list."
    ],
    [
        'keywords' => ['status', 'track', 'my application', 'approved', 'rejected'],
        'response' => "You can check your application status by logging into your student dashboard. The status will show as 'Pending', 'Approved', or 'Rejected'"
    ],
    [
        'keywords' => ['document', 'upload', 'pdf', 'certificate', 'mark sheet'],
        'response' => "During the application process, you will be asked to upload documents like your previous year's mark sheet, income certificate, and ID proof. Please ensure files are in PDF, JPG, or PNG format and under 5MB."
    ],
    [
        'keywords' => ['password', 'forgot password', 'reset'],
        'response' => "If you forgot your password, please click on the 'Forgot Password' link on the login page. You will receive instructions on your registered email to reset it."
    ],
    [
        'keywords' => ['contact', 'help', 'support', 'email', 'phone'],
        'response' => "If you need further assistance, you can contact the admin team at:<br>📧 support@scholarshipms.com<br>📞 +123 456 7890"
    ],
    [
        'keywords' => ['hi', 'hello', 'hey', 'greetings'],
        'response' => "Hello! 👋 Welcome to the Scholarship Management System. How can I assist you today? You can ask me about applying, eligibility, or tracking your status."
    ],
    [
        'keywords' => ['thank', 'thanks', 'thank you'],
        'response' => "You're very welcome! If you have any more questions, feel free to ask. Good luck with your scholarship application! 🎓"
    ],
    [
        'keywords' => ['bye', 'goodbye', 'see you'],
        'response' => "Goodbye! Have a great day! Don't hesitate to return if you need more help."
    ],
    [
        'keywords' => ['who are you', 'what are you', 'your name', 'bot'],
        'response' => "I am the Virtual Assistant, an AI chatbot built to help you navigate this scholarship portal smoothly!"
    ],
    [
        'keywords' => ['outlier', 'cluster', 'prediction', 'ml', 'machine learning'],
        'response' => "The portal uses Machine Learning to provide smart features like predicting your scholarship eligibility, detecting anomalies in data (outliers), and grouping feedback (clustering). These are mainly used by the admins for better decision making."
    ]
];

$reply = "";

// Simple Keyword Matching Engine
foreach ($rules as $rule) {
    foreach ($rule['keywords'] as $keyword) {
        // If the message contains the keyword
        if (strpos($message, $keyword) !== false) {
            $reply = $rule['response'];
            break 2; // Break out of both loops once a match is found
        }
    }
}

// Fallback response if no keywords match
if (empty($reply)) {
    $reply = "I'm not quite sure how to answer that. Could you please rephrase? You can ask me things like 'How to apply?', 'What is the eligibility?', or 'How to track my status?'.";
}

// Optional: Simulate a small typing delay to make it feel more "AI" like
usleep(800000); // 0.8 seconds

echo json_encode([
    'status' => 'success',
    'reply' => $reply
]);
