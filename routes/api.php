<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BadgeController;
use App\Http\Controllers\Api\V1\CertificateController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\GamificationController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\QuizController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
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
        Route::get('/lessons/{lesson}/quiz', [QuizController::class, 'getQuizByLesson']);
        Route::get('/quiz-attempts', [QuizController::class, 'getAttemptsByLesson']);
        Route::get('/quizzes/{quiz}/attempts/{attempt}/results', [QuizController::class, 'results']);
        
        // Certificate routes
        Route::get('/certificates', [CertificateController::class, 'index']);
        Route::post('/certificates/generate', [CertificateController::class, 'generate']);
        Route::get('/certificates/{certificate}', [CertificateController::class, 'show']);
        Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download']);
        Route::get('/certificates/{certificate}/share', [CertificateController::class, 'share']);
        
        // Badge routes
        Route::get('/badges', [BadgeController::class, 'index']);
        Route::get('/badges/mine', [BadgeController::class, 'userBadges']);
        Route::get('/badges/recent', [BadgeController::class, 'recent']);
        Route::get('/badges/progress', [BadgeController::class, 'progress']);
        Route::get('/badges/{badge}', [BadgeController::class, 'show']);
        Route::post('/badges/{badge}/award', [BadgeController::class, 'award']);
        
        // Gamification routes
        Route::get('/gamification/stats', [GamificationController::class, 'getStats']);
        Route::get('/gamification/achievements', [GamificationController::class, 'getAchievements']);
        Route::get('/gamification/leaderboard', [GamificationController::class, 'getLeaderboard']);
        Route::get('/gamification/streak-calendar', [GamificationController::class, 'getStreakCalendar']);
        
        // Notification routes
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);
        Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{notificationId}', [NotificationController::class, 'destroy']);
        
        // Admin routes (protected)
        Route::prefix('admin')->group(function () {
            // Course management
            Route::apiResource('courses', AdminCourseController::class);
            Route::patch('courses/{course}/toggle-status', [AdminCourseController::class, 'toggleStatus']);
            Route::patch('courses/{course}/toggle-publish', [AdminCourseController::class, 'togglePublish']);
            Route::get('courses/{course}/statistics', [AdminCourseController::class, 'statistics']);
            
            // Module management
            Route::get('courses/{course}/modules', [App\Http\Controllers\Admin\ModuleController::class, 'index']);
            
            // Certificate Template management
            Route::apiResource('certificate-templates', App\Http\Controllers\Admin\CertificateTemplateController::class);
            Route::get('certificate-templates/{certificateTemplate}/preview', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'preview']);
            Route::patch('certificate-templates/{certificateTemplate}/set-default', [App\Http\Controllers\Admin\CertificateTemplateController::class, 'setDefault']);
            Route::post('courses/{course}/modules', [App\Http\Controllers\Admin\ModuleController::class, 'store']);
            Route::apiResource('modules', App\Http\Controllers\Admin\ModuleController::class)->except(['index', 'store']);
            Route::post('modules/reorder', [App\Http\Controllers\Admin\ModuleController::class, 'reorder']);
            
            // Lesson management
            Route::get('modules/{module}/lessons', [App\Http\Controllers\Admin\LessonController::class, 'index']);
            Route::apiResource('lessons', App\Http\Controllers\Admin\LessonController::class);
            Route::post('lessons/reorder', [App\Http\Controllers\Admin\LessonController::class, 'reorder']);
            
            // User management
            Route::get('users/statistics', [App\Http\Controllers\Admin\UserController::class, 'statistics']);
            Route::post('users/import-csv', [App\Http\Controllers\Admin\UserController::class, 'importCsv']);
            Route::get('users/download-template', [App\Http\Controllers\Admin\UserController::class, 'downloadTemplate']);
            Route::apiResource('users', App\Http\Controllers\Admin\UserController::class);
            Route::patch('users/{user}/toggle-admin', [App\Http\Controllers\Admin\UserController::class, 'toggleAdmin']);
            
                // Analytics
                Route::get('analytics', [AnalyticsController::class, 'index']);
                Route::get('analytics/export', [AnalyticsController::class, 'export']);
                Route::get('analytics/engagement', [AnalyticsController::class, 'engagement']);

                // Admin Enrollments
                Route::post('enrollments', [AdminEnrollmentController::class, 'store']);
                Route::post('enrollments/bulk', [AdminEnrollmentController::class, 'bulkEnroll']);
                Route::get('users/{user}/enrollments', [AdminEnrollmentController::class, 'userEnrollments']);
                Route::patch('enrollments/{enrollment}/status', [AdminEnrollmentController::class, 'updateStatus']);
                Route::delete('enrollments/{enrollment}', [AdminEnrollmentController::class, 'cancel']);
                Route::get('enrollments/statistics', [AdminEnrollmentController::class, 'statistics']);
        });
    });
});

