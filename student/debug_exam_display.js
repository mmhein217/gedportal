/**
 * EMERGENCY FIX - Add this console check to see what's happening
 */

// Add to initializeExam function - check if elements are visible
function debugExamScreen() {
    console.log('=== DEBUGGING EXAM SCREEN ===');

    const examScreen = document.getElementById('examScreen');
    console.log('Exam screen element:', examScreen);
    console.log('Exam screen classes:', examScreen?.className);
    console.log('Exam screen display:', window.getComputedStyle(examScreen).display);
    console.log('Exam screen visibility:', window.getComputedStyle(examScreen).visibility);

    const questionText = document.getElementById('questionText');
    console.log('Question text element:', questionText);
    console.log('Question text content:', questionText?.textContent);
    console.log('Question text display:', window.getComputedStyle(questionText).display);

    const answerOptions = document.getElementById('answerOptions');
    console.log('Answer options element:', answerOptions);
    console.log('Answer options children:', answerOptions?.children.length);
    console.log('Answer options display:', window.getComputedStyle(answerOptions).display);

    console.log('=== END DEBUG ===');
}

// Call this after renderQuestion in initializeExam
