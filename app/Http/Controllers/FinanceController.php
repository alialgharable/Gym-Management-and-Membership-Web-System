<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MembershipPlan;
use App\Models\Subscription;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $monthlyPayroll = (float) Trainer::sum('salary');
        $activeSubscriptions = Subscription::with('plan')
            ->where('status', 'active')
            ->get();

        $activeSubscriptionsCount = $activeSubscriptions->count();

        $estimatedMrr = $activeSubscriptions->sum(function (Subscription $subscription) {
            $plan = $subscription->plan;

            if (!$plan) {
                return 0;
            }

            $durationInMonths = max(((float) $plan->duration) / 30, 1);

            return ((float) $plan->price) / $durationInMonths;
        });

        $subscriptionsWithPlan = Subscription::with('plan')->get();

        $totalSubscriptionSales = $subscriptionsWithPlan->sum(function (Subscription $subscription) {
            return (float) optional($subscription->plan)->price;
        });

        $salesThisMonth = Subscription::with('plan')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->get()
            ->sum(function (Subscription $subscription) {
                return (float) optional($subscription->plan)->price;
            });

        $subscriptionsThisMonth = Subscription::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $activeRevenue = $activeSubscriptions->sum(function (Subscription $subscription) {
            return (float) optional($subscription->plan)->price;
        });

        $confirmedBookingsTotal = Booking::where('status', 'confirmed')->count();
        $bookingsThisMonth = Booking::where('status', 'confirmed')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $estimatedNetMonthlyProfit = $estimatedMrr - $monthlyPayroll;
        $marginPercent = $estimatedMrr > 0
            ? ($estimatedNetMonthlyProfit / $estimatedMrr) * 100
            : 0;

        $avgMrrPerActiveSubscription = $activeSubscriptionsCount > 0
            ? $estimatedMrr / $activeSubscriptionsCount
            : 0;

        $breakEvenSubscriptions = $avgMrrPerActiveSubscription > 0
            ? (int) ceil($monthlyPayroll / $avgMrrPerActiveSubscription)
            : null;

        $uniqueSubscribedMembers = $subscriptionsWithPlan->pluck('member_id')->filter()->unique()->count();
        $avgRevenuePerMember = $uniqueSubscribedMembers > 0
            ? $totalSubscriptionSales / $uniqueSubscribedMembers
            : 0;

        $planPortfolio = MembershipPlan::withCount([
            'subscriptions as total_subscriptions_count',
            'subscriptions as active_subscriptions_count' => function ($query) {
                $query->where('status', 'active');
            },
        ])->get()->map(function (MembershipPlan $plan) use ($totalSubscriptionSales, $estimatedMrr) {
            $durationInMonths = max(((float) $plan->duration) / 30, 1);

            $totalRevenue = (float) $plan->price * (int) $plan->total_subscriptions_count;
            $activeRevenue = (float) $plan->price * (int) $plan->active_subscriptions_count;
            $estimatedPlanMrr = ((float) $plan->price / $durationInMonths) * (int) $plan->active_subscriptions_count;

            return [
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'duration' => $plan->durationLabel(),
                'total_subscriptions' => (int) $plan->total_subscriptions_count,
                'active_subscriptions' => (int) $plan->active_subscriptions_count,
                'total_revenue' => $totalRevenue,
                'active_revenue' => $activeRevenue,
                'estimated_mrr' => $estimatedPlanMrr,
                'revenue_share_percent' => $totalSubscriptionSales > 0 ? ($totalRevenue / $totalSubscriptionSales) * 100 : 0,
                'mrr_share_percent' => $estimatedMrr > 0 ? ($estimatedPlanMrr / $estimatedMrr) * 100 : 0,
            ];
        })->sortByDesc('total_revenue')->values();

        $bookingCountByTrainer = Booking::selectRaw('gym_classes.trainer_id, COUNT(*) as total_bookings')
            ->join('gym_classes', 'bookings.class_id', '=', 'gym_classes.id')
            ->groupBy('gym_classes.trainer_id')
            ->pluck('total_bookings', 'gym_classes.trainer_id');

        $confirmedBookingCountByTrainer = Booking::selectRaw('gym_classes.trainer_id, COUNT(*) as total_confirmed_bookings')
            ->join('gym_classes', 'bookings.class_id', '=', 'gym_classes.id')
            ->where('bookings.status', 'confirmed')
            ->groupBy('gym_classes.trainer_id')
            ->pluck('total_confirmed_bookings', 'gym_classes.trainer_id');

        $trainerPortfolio = Trainer::with('user')
            ->withCount('gymClasses')
            ->get()
            ->map(function (Trainer $trainer) use ($monthlyPayroll, $bookingCountByTrainer, $confirmedBookingCountByTrainer) {
                $salary = (float) ($trainer->salary ?? 0);
                $classesCount = (int) $trainer->gym_classes_count;
                $totalBookings = (int) ($bookingCountByTrainer[$trainer->id] ?? 0);
                $confirmedBookings = (int) ($confirmedBookingCountByTrainer[$trainer->id] ?? 0);

                return [
                    'name' => optional($trainer->user)->name ?? 'Trainer',
                    'specialty' => $trainer->specialtyLabel(),
                    'salary' => $salary,
                    'classes_count' => $classesCount,
                    'total_bookings' => $totalBookings,
                    'confirmed_bookings' => $confirmedBookings,
                    'payroll_share_percent' => $monthlyPayroll > 0 ? ($salary / $monthlyPayroll) * 100 : 0,
                    'bookings_per_class' => $classesCount > 0 ? ($totalBookings / $classesCount) : 0,
                ];
            })
            ->sortByDesc('salary')
            ->values();

        $financeTrend = $this->buildFinanceTrend($monthlyPayroll);

        $stats = [
            'total_sales' => $totalSubscriptionSales,
            'active_revenue' => $activeRevenue,
            'sales_this_month' => $salesThisMonth,
            'estimated_mrr' => $estimatedMrr,
            'monthly_payroll' => $monthlyPayroll,
            'annual_payroll' => $monthlyPayroll * 12,
            'estimated_net_monthly_profit' => $estimatedNetMonthlyProfit,
            'margin_percent' => $marginPercent,
            'subscriptions_this_month' => $subscriptionsThisMonth,
            'active_subscriptions_count' => $activeSubscriptionsCount,
            'confirmed_bookings_total' => $confirmedBookingsTotal,
            'bookings_this_month' => $bookingsThisMonth,
            'avg_revenue_per_member' => $avgRevenuePerMember,
            'break_even_subscriptions' => $breakEvenSubscriptions,
        ];

        return view('admin.finance.index', compact(
            'stats',
            'financeTrend',
            'planPortfolio',
            'trainerPortfolio'
        ));
    }

    private function buildFinanceTrend(float $monthlyPayroll): Collection
    {
        $months = collect();

        for ($offset = 5; $offset >= 0; $offset--) {
            $monthStart = Carbon::now()->startOfMonth()->subMonths($offset);
            $monthEnd = Carbon::now()->startOfMonth()->subMonths($offset)->endOfMonth();

            $sales = Subscription::with('plan')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->get()
                ->sum(function (Subscription $subscription) {
                    return (float) optional($subscription->plan)->price;
                });

            $newSubscriptions = Subscription::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $activeSubscriptionsSnapshot = Subscription::where('status', 'active')
                ->whereDate('start_date', '<=', $monthEnd->toDateString())
                ->whereDate('end_date', '>=', $monthStart->toDateString())
                ->count();

            $months->push([
                'label' => $monthStart->format('M Y'),
                'sales' => $sales,
                'payroll' => $monthlyPayroll,
                'profit' => $sales - $monthlyPayroll,
                'new_subscriptions' => $newSubscriptions,
                'active_subscriptions' => $activeSubscriptionsSnapshot,
            ]);
        }

        return $months;
    }
}
