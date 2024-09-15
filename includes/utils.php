<?php
// includes/utils.php

function extractTextFromResume($filePath) {
    // Placeholder for text extraction logic
    // In a real-world scenario, you might use a library to extract text from PDFs, DOCX, etc.
    return file_get_contents($filePath);
}

function calculateResumeScore($text) {
    // Placeholder for scoring logic
    // Implement your own scoring algorithm based on the extracted text
    return strlen($text); // Example: score based on text length
}
?>
