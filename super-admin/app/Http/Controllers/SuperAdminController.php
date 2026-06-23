<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $assets = Asset::with(['tracks', 'user'])->latest()->get();
        $totalRevenue = (float) Purchase::where('status', Purchase::STATUS_SUCCEEDED)->sum('amount');
        $totalRedemptions = (int) Asset::sum('redemptions');
        $failedPurchases = (int) Purchase::whereIn('status', [Purchase::STATUS_FAILED, Purchase::STATUS_CANCELLED])->count();

        $kpis = [
            ['label' => 'Total Redemptions', 'value' => number_format($totalRedemptions), 'delta' => 'Live', 'trend' => 'up', 'icon' => 'confirmation_number', 'spark' => $this->flatSpark($totalRedemptions)],
            ['label' => 'Active Users', 'value' => number_format(User::count()), 'delta' => 'Live', 'trend' => 'up', 'icon' => 'group', 'spark' => $this->flatSpark(User::count())],
            ['label' => 'Revenue', 'value' => '$'.number_format($totalRevenue, 2), 'delta' => 'Live', 'trend' => 'up', 'icon' => 'payments', 'spark' => $this->flatSpark((int) $totalRevenue)],
            ['label' => 'Failed Payments', 'value' => number_format($failedPurchases), 'delta' => 'Live', 'trend' => 'down', 'icon' => 'report', 'spark' => $this->flatSpark($failedPurchases)],
        ];

        $revenueSeries = ['labels' => ['Revenue'], 'current' => [$totalRevenue], 'previous' => [0]];
        $sourceBreakdown = $this->methodBreakdown();
        $topArtists = $this->topArtists($assets);
        $recentRedemptions = $this->purchaseRows(Purchase::with(['user', 'asset'])->latest()->limit(8)->get());
        $users = $this->userRows();
        $systemHealth = [];

        return view('super-admin.dashboard', compact('kpis', 'revenueSeries', 'sourceBreakdown', 'topArtists', 'recentRedemptions', 'users', 'systemHealth'));
    }

    public function redemptions()
    {
        $rows = $this->purchaseRows(Purchase::with(['user', 'asset'])->latest()->limit(50)->get());
        $total = max(1, Purchase::count());
        $failed = Purchase::whereIn('status', [Purchase::STATUS_FAILED, Purchase::STATUS_CANCELLED])->count();

        $stats = [
            ['label' => 'Today', 'value' => number_format(Purchase::whereDate('created_at', today())->count()), 'delta' => 'Live', 'icon' => 'today'],
            ['label' => 'This Week', 'value' => number_format(Purchase::where('created_at', '>=', now()->subDays(7))->count()), 'delta' => 'Live', 'icon' => 'date_range'],
            ['label' => 'This Month', 'value' => number_format(Purchase::where('created_at', '>=', now()->startOfMonth())->count()), 'delta' => 'Live', 'icon' => 'calendar_month'],
            ['label' => 'Failure Rate', 'value' => number_format(($failed / $total) * 100, 2).'%', 'delta' => 'Live', 'icon' => 'percent'],
        ];

        return view('super-admin.redemptions', compact('stats', 'rows'));
    }

    public function users()
    {
        $totalUsers = User::count();
        $newUsers = User::where('created_at', '>=', now()->subDays(7))->count();
        $stats = [
            ['label' => 'Total Users', 'value' => number_format($totalUsers), 'delta' => 'Live', 'icon' => 'group', 'trend' => 'up'],
            ['label' => 'Admins', 'value' => number_format(User::where('is_super_admin', true)->count()), 'delta' => 'Active', 'icon' => 'workspace_premium', 'trend' => 'up'],
            ['label' => 'Suspended', 'value' => '0', 'delta' => 'None', 'icon' => 'block', 'trend' => 'down'],
            ['label' => 'New (7d)', 'value' => number_format($newUsers), 'delta' => 'Live', 'icon' => 'person_add', 'trend' => 'up'],
        ];
        $rows = $this->userRows();

        return view('super-admin.users', compact('stats', 'rows'));
    }

    public function revenue()
    {
        $successful = Purchase::where('status', Purchase::STATUS_SUCCEEDED);
        $revenue = (float) (clone $successful)->sum('amount');
        $purchaseCount = (int) (clone $successful)->count();
        $avgBasket = $purchaseCount > 0 ? $revenue / $purchaseCount : 0;

        $kpis = [
            ['label' => 'Revenue', 'value' => '$'.number_format($revenue, 2), 'delta' => 'Live', 'trend' => 'up', 'icon' => 'payments'],
            ['label' => 'Purchases', 'value' => number_format($purchaseCount), 'delta' => 'Live', 'trend' => 'up', 'icon' => 'shopping_bag'],
            ['label' => 'Avg. Basket', 'value' => '$'.number_format($avgBasket, 2), 'delta' => 'Live', 'trend' => 'up', 'icon' => 'shopping_bag'],
            ['label' => 'Refund Rate', 'value' => '0.00%', 'delta' => 'Live', 'trend' => 'down', 'icon' => 'autorenew'],
        ];

        $bars = ['labels' => ['Revenue'], 'values' => [$revenue]];
        $byRegion = [];
        $topEarners = Asset::with('user')
            ->latest()
            ->get()
            ->map(fn (Asset $asset) => [
                'title' => $asset->title,
                'artist' => $asset->user?->artist_name ?: $asset->user?->name ?: $asset->artist,
                'revenue' => '$'.number_format(((float) $asset->price) * ((int) $asset->redemptions), 2),
                'pct' => min(100, $asset->redemption_limit > 0 ? (int) round(($asset->redemptions / $asset->redemption_limit) * 100) : 0),
                'img' => $asset->cover_url,
            ])
            ->filter(fn (array $asset) => $asset['revenue'] !== '$0.00')
            ->take(5)
            ->values()
            ->all();
        $payouts = [];

        return view('super-admin.revenue', compact('kpis', 'bars', 'byRegion', 'topEarners', 'payouts'));
    }

    public function system()
    {
        $services = [];
        $incidents = [];
        $metrics = [];
        $loadSeries = [0];

        return view('super-admin.system', compact('services', 'incidents', 'metrics', 'loadSeries'));
    }

    public function audit()
    {
        $events = [];

        return view('super-admin.audit', compact('events'));
    }

    public function settings()
    {
        $apiKeys = [];
        $integrations = [];

        return view('super-admin.settings', compact('apiKeys', 'integrations'));
    }

    private function userRows(): array
    {
        return User::query()
            ->latest()
            ->get()
            ->map(fn (User $user) => [
                'name' => $user->name,
                'email' => $user->email,
                'tier' => $user->is_super_admin ? 'Super Admin' : ($user->is_artist ? 'Artist' : 'User'),
                'redemptions' => Purchase::where('user_id', $user->id)->where('status', Purchase::STATUS_SUCCEEDED)->count(),
                'status' => 'Active',
                'joined' => $user->created_at?->format('M Y') ?? '-',
                'country' => '-',
            ])
            ->all();
    }

    private function methodBreakdown(): array
    {
        $total = max(1, Purchase::count());
        $colors = ['#53e076', '#72fe8f', '#cfc4c4', '#ffb3b3'];

        return Purchase::query()
            ->selectRaw('method, COUNT(*) as count')
            ->groupBy('method')
            ->get()
            ->values()
            ->map(fn ($row, int $index) => [
                'label' => str($row->method ?: 'unknown')->replace('_', ' ')->title()->toString(),
                'value' => (int) round(($row->count / $total) * 100),
                'color' => $colors[$index % count($colors)],
            ])
            ->all();
    }

    private function topArtists(Collection $assets): array
    {
        return $assets
            ->groupBy(fn (Asset $asset) => $asset->user?->artist_name ?: $asset->user?->name ?: $asset->artist)
            ->map(fn ($artistAssets, string $name) => [
                'name' => $name ?: 'Unknown Artist',
                'plays' => number_format($artistAssets->sum(fn (Asset $asset) => $asset->tracks->sum('sample_plays'))),
                'change' => 'Live',
                'img' => $artistAssets->first()?->cover_url,
            ])
            ->values()
            ->take(5)
            ->all();
    }

    private function purchaseRows(Collection $purchases): array
    {
        return $purchases
            ->map(fn (Purchase $purchase) => [
                'id' => $purchase->reference,
                'user' => $purchase->user?->email ?: $purchase->email ?: 'Guest',
                'asset' => $purchase->asset?->title ?: 'Unknown release',
                'source' => str($purchase->method ?: 'unknown')->replace('_', ' ')->title()->toString(),
                'status' => str($purchase->status)->replace('_', ' ')->title()->toString(),
                'value' => '$'.number_format((float) $purchase->amount, 2),
                'ip' => '-',
                'time' => $purchase->created_at?->diffForHumans() ?? '-',
            ])
            ->all();
    }

    private function flatSpark(int $value): array
    {
        return array_fill(0, 12, max(0, $value));
    }
}

