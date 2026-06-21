<?php

namespace App\Http\Controllers;

use App\Models\User;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $kpis = [
            ['label' => 'Total Redemptions', 'value' => '128,420', 'delta' => '+12.4%', 'trend' => 'up',   'icon' => 'confirmation_number', 'spark' => [4,8,6,12,10,16,14,22,20,28,26,34]],
            ['label' => 'Active Users',      'value' => number_format(User::count()), 'delta' => 'Live', 'trend' => 'up', 'icon' => 'group', 'spark' => [0,0,0,0,0,0,0,0,0,0,0,User::count()]],
            ['label' => 'Revenue (MTD)',     'value' => '$284,920','delta' => '+8.7%',  'trend' => 'up',   'icon' => 'payments',            'spark' => [5,7,6,9,11,10,14,13,17,16,21,25]],
            ['label' => 'Failed Tokens',     'value' => '142',     'delta' => '-3.2%',  'trend' => 'down', 'icon' => 'report',              'spark' => [22,18,20,15,17,12,14,10,11,8,9,6]],
        ];
        $revenueSeries = ['labels' => ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'], 'current' => [12,18,14,24,22,30,36], 'previous' => [10,12,11,18,16,22,24]];
        $sourceBreakdown = [
            ['label' => 'Vinyl insert', 'value' => 48, 'color' => '#53e076'],
            ['label' => 'Digital pass', 'value' => 26, 'color' => '#72fe8f'],
            ['label' => 'NFC tag',      'value' => 14, 'color' => '#cfc4c4'],
            ['label' => 'Merch QR',     'value' => 12, 'color' => '#ffb3b3'],
        ];
        $topArtists = [
            ['name' => 'Cyber Echoes',  'plays' => '482,901', 'change' => '+18%', 'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuChqVpeUCEFs5U7WuEaY64Tg7gQTzHxHYGBIR_YhsRc_ZhKVx5O5_yip_OweNC0jPzTpJ6X9fKbk0bf4srrI6ij6PcWFdkZy4zHo_SlNLZMXLkLBsD6RRe9eqQUDdIFl1PSbssKB0lq5ndbplCdRmGLruSZgczH6dhDdDdcdxqGLqW7XnsPzcSLbodcWzjANZyDAx1u5rYqkaplsLAPwNmxyrI5Z_tbCq4klIwJ5taoKzbT09KaWge-Mbybr6yt-0S3J9pe1VlxwKs'],
            ['name' => 'Digital Drift', 'plays' => '341,012', 'change' => '+9%',  'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCal__rmJC6rH62tAtPXBhHWj0BqHK5N77copHmQO2GPo3Z9ZsucqeKO3t6QjkMzeRA2lm6x9wrdHXN643adSUSRiHA22mxrlAqQ3IOJn-gDzQGzXbQWQ0QssD255XkJ7M6ImwW-1dnZdBHatsHn5n5No4O6f2IvJULdBp8uS6x3sDESSGijebKVKueF100dM5i09YxyNOAjFWmyXYGe4XGaJ7UdvNY1E0A_h0UPKHnOeNLWjP_lcvU9TsrAFyQ8rIT4SJfKyjFnac'],
            ['name' => 'Flow State',    'plays' => '298,556', 'change' => '+6%',  'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCt_-sTtFeR6r6xH5xRemZpReuuMF39oyTtWil36RNL_NZRQqUvglDVqGuapXyb-DY5aUMcrjlC4ex3jEeQ0kxmkzaWUBpZ7ZByDODh9-4WEgKd29po8VJeQPciQZ8xMyCHrvoUz1_3wq4ozBvxSo_Hr9UhS5KG7_T8PCRIPLtpXDU05kHRYeYuP2tD3xYzLD8trQz2obauulg1zELcYeTHvqxCWTkToOHX7pexR5I8DiPLzB3jNQ_UPHCyZ0UlLyhHBvBfsQY0oIQ'],
            ['name' => 'Bass Theory',   'plays' => '212,488', 'change' => '+4%',  'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCAPhAU3ufy4jmWhXubAFevI9zzzJNKCd7vfd337ycgUkpbfBRQPeIEHsM2v_bJmXJ9xKPRTOt3q3BP7DWlZpgUV4K-hcm0cGW8Nf7iL7QEdGp1nnbda3pmKtseCvJ2553DZpWkTvFFbacAsu1QrJNjv4mLDlgZcuR1ygt54OQ6tAd_nvssG7JqUAGHSh15UWclCvsvi5fQV2Vn2G8oWtb1WRPFzLuSITC7K3DCZpk9nkezWvQXzDnkpB4SZlgJIgtXNmrJ9Yd-qFU'],
        ];
        $recentRedemptions = [];
        $users = $this->userRows();
        $systemHealth = [
            ['service' => 'API Gateway',     'status' => 'Operational', 'latency' => '42 ms',  'uptime' => '99.99%'],
            ['service' => 'Auth Service',    'status' => 'Operational', 'latency' => '38 ms',  'uptime' => '99.97%'],
            ['service' => 'CDN (Audio)',     'status' => 'Degraded',    'latency' => '128 ms', 'uptime' => '99.42%'],
            ['service' => 'Payment Gateway', 'status' => 'Operational', 'latency' => '210 ms', 'uptime' => '99.91%'],
            ['service' => 'Background Jobs', 'status' => 'Operational', 'latency' => '—',      'uptime' => '99.99%'],
        ];
        return view('super-admin.dashboard', compact('kpis','revenueSeries','sourceBreakdown','topArtists','recentRedemptions','users','systemHealth'));
    }

    public function redemptions()
    {
        $stats = [
            ['label' => 'Today',        'value' => '1,284',  'delta' => '+8.2%',  'icon' => 'today'],
            ['label' => 'This Week',    'value' => '9,612',  'delta' => '+12.4%', 'icon' => 'date_range'],
            ['label' => 'This Month',   'value' => '38,420', 'delta' => '+5.1%',  'icon' => 'calendar_month'],
            ['label' => 'Failure Rate', 'value' => '0.42%',  'delta' => '-0.1%',  'icon' => 'percent'],
        ];
        $rows = [];
        return view('super-admin.redemptions', compact('stats','rows'));
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
        return view('super-admin.users', compact('stats','rows'));
    }

    public function revenue()
    {
        $kpis = [
            ['label' => 'Revenue (MTD)', 'value' => '$284,920', 'delta' => '+8.7%',  'trend' => 'up',   'icon' => 'payments'],
            ['label' => 'ARR Forecast',  'value' => '$3.42M',   'delta' => '+11.4%', 'trend' => 'up',   'icon' => 'trending_up'],
            ['label' => 'Avg. Basket',   'value' => '$8.42',    'delta' => '+0.4%',  'trend' => 'up',   'icon' => 'shopping_bag'],
            ['label' => 'Refund Rate',   'value' => '0.62%',    'delta' => '-0.1%',  'trend' => 'down', 'icon' => 'autorenew'],
        ];
        $bars = ['labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'], 'values' => [120,138,142,168,182,210,232,248,264,278,290,284]];
        $byRegion = [
            ['region' => 'North America', 'value' => '$112,402', 'pct' => 42],
            ['region' => 'Europe',        'value' => '$84,901',  'pct' => 30],
            ['region' => 'Asia Pacific',  'value' => '$58,402',  'pct' => 20],
            ['region' => 'Latin America', 'value' => '$22,400',  'pct' => 8],
        ];
        $topEarners = [
            ['title' => 'Synthetic Horizons', 'artist' => 'Cyber Echoes',  'revenue' => '$124,012', 'pct' => 92, 'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuChqVpeUCEFs5U7WuEaY64Tg7gQTzHxHYGBIR_YhsRc_ZhKVx5O5_yip_OweNC0jPzTpJ6X9fKbk0bf4srrI6ij6PcWFdkZy4zHo_SlNLZMXLkLBsD6RRe9eqQUDdIFl1PSbssKB0lq5ndbplCdRmGLruSZgczH6dhDdDdcdxqGLqW7XnsPzcSLbodcWzjANZyDAx1u5rYqkaplsLAPwNmxyrI5Z_tbCq4klIwJ5taoKzbT09KaWge-Mbybr6yt-0S3J9pe1VlxwKs'],
            ['title' => 'Cyber Synth',        'artist' => 'Digital Drift', 'revenue' => '$96,802',  'pct' => 78, 'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCal__rmJC6rH62tAtPXBhHWj0BqHK5N77copHmQO2GPo3Z9ZsucqeKO3t6QjkMzeRA2lm6x9wrdHXN643adSUSRiHA22mxrlAqQ3IOJn-gDzQGzXbQWQ0QssD255XkJ7M6ImwW-1dnZdBHatsHn5n5No4O6f2IvJULdBp8uS6x3sDESSGijebKVKueF100dM5i09YxyNOAjFWmyXYGe4XGaJ7UdvNY1E0A_h0UPKHnOeNLWjP_lcvU9TsrAFyQ8rIT4SJfKyjFnac'],
            ['title' => 'Liquid Rhythms',     'artist' => 'Flow State',    'revenue' => '$78,401',  'pct' => 64, 'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCt_-sTtFeR6r6xH5xRemZpReuuMF39oyTtWil36RNL_NZRQqUvglDVqGuapXyb-DY5aUMcrjlC4ex3jEeQ0kxmkzaWUBpZ7ZByDODh9-4WEgKd29po8VJeQPciQZ8xMyCHrvoUz1_3wq4ozBvxSo_Hr9UhS5KG7_T8PCRIPLtpXDU05kHRYeYuP2tD3xYzLD8trQz2obauulg1zELcYeTHvqxCWTkToOHX7pexR5I8DiPLzB3jNQ_UPHCyZ0UlLyhHBvBfsQY0oIQ'],
            ['title' => 'Subterranean',       'artist' => 'Bass Theory',   'revenue' => '$48,901',  'pct' => 42, 'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCAPhAU3ufy4jmWhXubAFevI9zzzJNKCd7vfd337ycgUkpbfBRQPeIEHsM2v_bJmXJ9xKPRTOt3q3BP7DWlZpgUV4K-hcm0cGW8Nf7iL7QEdGp1nnbda3pmKtseCvJ2553DZpWkTvFFbacAsu1QrJNjv4mLDlgZcuR1ygt54OQ6tAd_nvssG7JqUAGHSh15UWclCvsvi5fQV2Vn2G8oWtb1WRPFzLuSITC7K3DCZpk9nkezWvQXzDnkpB4SZlgJIgtXNmrJ9Yd-qFU'],
        ];
        $payouts = [
            ['date' => 'Jul 14, 2026', 'method' => 'Stripe', 'amount' => '$84,201', 'status' => 'Processed'],
            ['date' => 'Jul 07, 2026', 'method' => 'Stripe', 'amount' => '$72,910', 'status' => 'Processed'],
            ['date' => 'Jun 30, 2026', 'method' => 'Stripe', 'amount' => '$68,402', 'status' => 'Processed'],
            ['date' => 'Jun 23, 2026', 'method' => 'Stripe', 'amount' => '$59,801', 'status' => 'Processed'],
        ];
        return view('super-admin.revenue', compact('kpis','bars','byRegion','topEarners','payouts'));
    }

    public function system()
    {
        $services = [
            ['service' => 'API Gateway',     'status' => 'Operational', 'latency' => '42 ms',  'uptime' => '99.99%', 'region' => 'us-east-1'],
            ['service' => 'Auth Service',    'status' => 'Operational', 'latency' => '38 ms',  'uptime' => '99.97%', 'region' => 'global'],
            ['service' => 'CDN (Audio)',     'status' => 'Degraded',    'latency' => '128 ms', 'uptime' => '99.42%', 'region' => 'eu-west-1'],
            ['service' => 'Payment Gateway', 'status' => 'Operational', 'latency' => '210 ms', 'uptime' => '99.91%', 'region' => 'us-east-1'],
            ['service' => 'Background Jobs', 'status' => 'Operational', 'latency' => '—',      'uptime' => '99.99%', 'region' => 'us-east-1'],
            ['service' => 'Database (RDS)',  'status' => 'Operational', 'latency' => '8 ms',   'uptime' => '99.99%', 'region' => 'us-east-1'],
            ['service' => 'Cache (Redis)',   'status' => 'Operational', 'latency' => '2 ms',   'uptime' => '99.99%', 'region' => 'us-east-1'],
            ['service' => 'Search (Meili)',  'status' => 'Operational', 'latency' => '14 ms',  'uptime' => '99.94%', 'region' => 'us-east-1'],
        ];
        $incidents = [
            ['title' => 'Elevated CDN latency in eu-west-1', 'status' => 'Investigating', 'severity' => 'Minor',    'time' => '14 min ago'],
            ['title' => 'Stripe webhook retry storm',        'status' => 'Resolved',      'severity' => 'Major',    'time' => '3 days ago'],
            ['title' => 'Auth Service intermittent 401s',    'status' => 'Resolved',      'severity' => 'Critical', 'time' => '12 days ago'],
        ];
        $metrics = [
            ['label' => 'API requests (24h)', 'value' => '12.4M', 'icon' => 'public'],
            ['label' => 'P95 latency',        'value' => '92 ms', 'icon' => 'speed'],
            ['label' => 'Error rate',         'value' => '0.04%', 'icon' => 'error'],
            ['label' => 'Active workers',     'value' => '48',    'icon' => 'memory'],
        ];
        $loadSeries = [22,24,21,28,33,30,36,41,38,44,40,48,52,50,56,60,58,65,70,68];
        return view('super-admin.system', compact('services','incidents','metrics','loadSeries'));
    }

    public function audit()
    {
        $events = [];
        return view('super-admin.audit', compact('events'));
    }

    private function userRows(): array
    {
        return User::query()
            ->latest()
            ->get()
            ->map(fn (User $user) => [
                'name' => $user->name,
                'email' => $user->email,
                'tier' => $user->is_super_admin ? 'Super Admin' : 'User',
                'redemptions' => 0,
                'status' => 'Active',
                'joined' => $user->created_at?->format('M Y') ?? '—',
                'country' => '—',
            ])
            ->all();
    }

    public function settings()
    {
        $apiKeys = [
            ['label' => 'Production · pk_live', 'masked' => 'live_pk_*****************42', 'created' => 'Jan 12, 2024', 'scopes' => 'read · write'],
            ['label' => 'Production · sk_live', 'masked' => 'live_sk_*****************a9', 'created' => 'Jan 12, 2024', 'scopes' => 'admin'],
            ['label' => 'Sandbox · pk_test',    'masked' => 'test_pk_*****************11', 'created' => 'Jun 02, 2024', 'scopes' => 'read · write'],
        ];
        $integrations = [
            ['name' => 'Stripe',     'desc' => 'Payments & payouts',        'status' => true,  'icon' => 'payments'],
            ['name' => 'Resend',     'desc' => 'Transactional email',       'status' => true,  'icon' => 'mail'],
            ['name' => 'Cloudflare', 'desc' => 'CDN + WAF',                 'status' => true,  'icon' => 'cloud'],
            ['name' => 'Slack',      'desc' => 'Ops alerts → #soundredeem', 'status' => true,  'icon' => 'chat'],
            ['name' => 'Datadog',    'desc' => 'Metrics + APM',             'status' => false, 'icon' => 'monitoring'],
            ['name' => 'PagerDuty',  'desc' => 'On-call escalation',        'status' => false, 'icon' => 'notifications_active'],
        ];
        return view('super-admin.settings', compact('apiKeys','integrations'));
    }
}
