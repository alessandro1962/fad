<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BadgeController;
use App\Http\Controllers\Api\V1\CertificateController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\QuizController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    
    // Course routes
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/featured', [CourseController::class, 'featured']);
    Route::get('/courses/level/{level}', [CourseController::class, 'byLevel']);
    Route::get('/courses/{course}', [CourseController::class, 'show']);
    
    // Public certificate routes
    Route::get('/certificates/public/{publicUid}', [CertificateController::class, 'public']);
    Route::get('/certificates/public/{publicUid}/download', [CertificateController::class, 'publicDownload']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        
        // Enrollment routes
        Route::get('/enrollments', [EnrollmentController::class, 'index']);
        Route::post('/enrollments', [EnrollmentController::class, 'store']);
        Route::get('/enrollments/active', [EnrollmentController::class, 'active']);
        Route::get('/enrollments/completed', [EnrollmentController::class, 'completed']);
        Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show']);
        Route::patch('/enrollments/{enrollment}/progress', [EnrollmentController::class, 'updateProgress']);
        
        // Progress routes
        Route::get('/progress/statistics', [ProgressController::class, 'statistics']);
        Route::get('/progress/course/{courseId}', [ProgressController::class, 'courseProgress']);
        Route::get('/progress/lesson/{lesson}', [ProgressController::class, 'show']);
        Route::patch('/progress/lesson/{lesson}', [ProgressController::class, 'update']);
        
        // Quiz routes
        Route::get('/quizzes/{quiz}', [QuizController::class, 'show']);
        Route::post('/quizzes/{quiz}/start', [QuizController::class, 'start']);
        Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit']);
        Route::get('/quizzes/{quiz}/history', [QuizController::class, 'history']);
        Route::get('/quizzes/{quiz}/attempts/{attempt}/results', [QuizController::class, 'results']);
        
        // Certificate routes
        Route::get('/certificates', [CertificateController::class, 'index']);
        Route::post('/certificates/generate', [CertificateController::class, 'generate']);
        Route::get('/certificates/{certificate}', [CertificateController::class, 'show']);
        Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download']);
        Route::get('/certificates/{certificate}/share', [CertificateController::class, 'share']);
        
        // Badge routes
        Route::get('/badges', [BadgeController::class, 'index']);
        Route::get('/badges/my-badges', [BadgeController::class, 'userBadges']);
        Route::get('/badges/progress', [BadgeController::class, 'progress']);
        Route::get('/badges/{badge}', [BadgeController::class, 'show']);
        Route::post('/badges/{badge}/award', [BadgeController::class, 'award']);
        
        // Notification routes
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);
        Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{notificationId}', [NotificationController::class, 'destroy']);
    });
});
