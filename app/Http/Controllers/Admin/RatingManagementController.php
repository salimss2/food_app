<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderRating;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RatingManagementController extends Controller
{
    /**
     * GET /api/v1/admin/ratings/analytics
     */
    public function analytics()
    {
        $allRatings = OrderRating::all();
        $totalReviews = $allRatings->count();

        if ($totalReviews === 0) {
            return response()->json([
                'status' => true,
                'data' => [
                    'global_satisfaction' => 5.0,
                    'total_reviews' => 0,
                    'distributions' => [
                        5 => ['count' => 0, 'percentage' => 0],
                        4 => ['count' => 0, 'percentage' => 0],
                        3 => ['count' => 0, 'percentage' => 0],
                        2 => ['count' => 0, 'percentage' => 0],
                        1 => ['count' => 0, 'percentage' => 0],
                    ]
                ]
            ]);
        }

        $scores = [];
        $starCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        foreach ($allRatings as $r) {
            if (!is_null($r->driver_rating)) {
                $score = ($r->restaurant_rating + $r->meals_rating + $r->driver_rating) / 3.0;
            } else {
                $score = ($r->restaurant_rating + $r->meals_rating) / 2.0;
            }

            $scores[] = $score;

            $starLevel = (int) round($score);
            $starLevel = max(1, min(5, $starLevel));
            $starCounts[$starLevel]++;
        }

        $globalSatisfaction = round(array_sum($scores) / count($scores), 1);

        $distributions = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $starCounts[$i];
            $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            $distributions[$i] = [
                'count' => $count,
                'percentage' => $pct
            ];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'global_satisfaction' => $globalSatisfaction,
                'total_reviews' => $totalReviews,
                'distributions' => $distributions
            ]
        ]);
    }

    /**
     * GET /api/v1/admin/ratings
     */
    public function index(Request $request)
    {
        $entityType = strtoupper($request->get('entity_type', 'ALL'));
        $ratingScore = $request->get('rating_score');
        $search = strtolower($request->get('search', ''));
        $dateRange = $request->get('date_range');
        $perPage = (int) $request->get('per_page', 15);

        $query = OrderRating::with(['user', 'restaurant', 'driver']);

        if (!empty($dateRange)) {
            $dates = explode(' to ', $dateRange);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        $ratings = $query->orderBy('created_at', 'desc')->get();

        $rows = [];

        foreach ($ratings as $r) {
            $user = $r->user;
            $userName = $user ? $user->name : 'Unknown Customer';
            $nameParts = explode(' ', trim($userName));
            $initials = count($nameParts) >= 2
                ? mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1)
                : mb_substr($userName, 0, 2);
            $initials = strtoupper($initials);

            $avatar = $user && $user->profile_picture_full_url
                ? $user->profile_picture_full_url
                : "https://ui-avatars.com/api/?name=" . urlencode($userName) . "&color=fff&background=4f46e5";

            $customerUser = [
                'name' => $userName,
                'initials' => $initials,
                'avatar' => $avatar
            ];

            $createdAt = $r->created_at;
            if ($createdAt->isToday()) {
                $dateStr = 'Today, ' . $createdAt->format('h:i A');
            } elseif ($createdAt->isYesterday()) {
                $dateStr = 'Yesterday, ' . $createdAt->format('h:i A');
            } else {
                $dateStr = $createdAt->format('M d, Y');
            }

            $restBaseName = $r->restaurant ? $r->restaurant->name : ('Restaurant #' . $r->restaurant_id);

            // 1. Restaurant Entry
            if ($entityType === 'ALL' || $entityType === 'RESTAURANT') {
                $restScore = (int) $r->restaurant_rating;
                if (empty($ratingScore) || (int)$ratingScore === $restScore) {
                    $matchesSearch = empty($search)
                        || str_contains(strtolower($userName), $search)
                        || str_contains(strtolower($restBaseName), $search)
                        || str_contains(strtolower($r->comment ?? ''), $search);

                    if ($matchesSearch) {
                        $rows[] = [
                            'id' => 'RST-' . $r->id,
                            'rating_id' => $r->id,
                            'customer_user' => $customerUser,
                            'entity_type' => 'RESTAURANT',
                            'entity_name' => $restBaseName,
                            'rating' => $restScore,
                            'comment_preview' => $r->comment ? $r->comment : 'No comment provided.',
                            'created_at' => $dateStr,
                            'created_timestamp' => $r->created_at->timestamp,
                        ];
                    }
                }
            }

            // 2. Meals / Food Entry
            if ($entityType === 'ALL' || $entityType === 'RESTAURANT' || $entityType === 'MEAL') {
                $mealScore = (int) $r->meals_rating;
                $mealEntityName = $restBaseName . ' - جودة الوجبات';
                if (empty($ratingScore) || (int)$ratingScore === $mealScore) {
                    $matchesSearch = empty($search)
                        || str_contains(strtolower($userName), $search)
                        || str_contains(strtolower($mealEntityName), $search)
                        || str_contains(strtolower($r->comment ?? ''), $search);

                    if ($matchesSearch) {
                        $rows[] = [
                            'id' => 'MEL-' . $r->id,
                            'rating_id' => $r->id,
                            'customer_user' => $customerUser,
                            'entity_type' => 'MEAL',
                            'entity_name' => $mealEntityName,
                            'rating' => $mealScore,
                            'comment_preview' => $r->comment ? $r->comment : 'No comment provided.',
                            'created_at' => $dateStr,
                            'created_timestamp' => $r->created_at->timestamp,
                        ];
                    }
                }
            }

            // 3. Driver Entry (Only if $r->driver_rating is not null)
            if (($entityType === 'ALL' || $entityType === 'DRIVER') && !is_null($r->driver_rating) && !is_null($r->driver_id)) {
                $drvScore = (int) $r->driver_rating;
                $drvName = $r->driver ? $r->driver->name : 'مندوب التوصيل';

                if (empty($ratingScore) || (int)$ratingScore === $drvScore) {
                    $matchesSearch = empty($search)
                        || str_contains(strtolower($userName), $search)
                        || str_contains(strtolower($drvName), $search)
                        || str_contains(strtolower($r->comment ?? ''), $search);

                    if ($matchesSearch) {
                        $rows[] = [
                            'id' => 'DRV-' . $r->id,
                            'rating_id' => $r->id,
                            'customer_user' => $customerUser,
                            'entity_type' => 'DRIVER',
                            'entity_name' => $drvName,
                            'rating' => $drvScore,
                            'comment_preview' => $r->comment ? $r->comment : 'No comment provided.',
                            'created_at' => $dateStr,
                            'created_timestamp' => $r->created_at->timestamp,
                        ];
                    }
                }
            }
        }

        // Sort rows by created_timestamp desc
        usort($rows, function ($a, $b) {
            return $b['created_timestamp'] <=> $a['created_timestamp'];
        });

        // Pagination
        $totalItems = count($rows);
        $currentPage = (int) $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $pagedData = array_slice($rows, $offset, $perPage);
        $lastPage = (int) ceil($totalItems / $perPage);

        return response()->json([
            'status' => true,
            'data' => $pagedData,
            'pagination' => [
                'total' => $totalItems,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'last_page' => max(1, $lastPage),
            ]
        ]);
    }
}
