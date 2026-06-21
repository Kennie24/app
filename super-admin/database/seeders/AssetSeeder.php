<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            ['title' => 'Synthetic Horizons', 'artist' => 'Cyber Echoes',   'price' => 9.99, 'redemption_limit' => 15000, 'redemptions' => 12402, 'status' => 'live',      'cover_path' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuChqVpeUCEFs5U7WuEaY64Tg7gQTzHxHYGBIR_YhsRc_ZhKVx5O5_yip_OweNC0jPzTpJ6X9fKbk0bf4srrI6ij6PcWFdkZy4zHo_SlNLZMXLkLBsD6RRe9eqQUDdIFl1PSbssKB0lq5ndbplCdRmGLruSZgczH6dhDdDdcdxqGLqW7XnsPzcSLbodcWzjANZyDAx1u5rYqkaplsLAPwNmxyrI5Z_tbCq4klIwJ5taoKzbT09KaWge-Mbybr6yt-0S3J9pe1VlxwKs', 'description' => 'Limited-edition vinyl insert with full FLAC archive.'],
            ['title' => 'Liquid Rhythms',     'artist' => 'Flow State',     'price' => 9.99, 'redemption_limit' => 10000, 'redemptions' => 8116,  'status' => 'live',      'cover_path' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCt_-sTtFeR6r6xH5xRemZpReuuMF39oyTtWil36RNL_NZRQqUvglDVqGuapXyb-DY5aUMcrjlC4ex3jEeQ0kxmkzaWUBpZ7ZByDODh9-4WEgKd29po8VJeQPciQZ8xMyCHrvoUz1_3wq4ozBvxSo_Hr9UhS5KG7_T8PCRIPLtpXDU05kHRYeYuP2tD3xYzLD8trQz2obauulg1zELcYeTHvqxCWTkToOHX7pexR5I8DiPLzB3jNQ_UPHCyZ0UlLyhHBvBfsQY0oIQ', 'description' => null],
            ['title' => 'Subterranean',       'artist' => 'Bass Theory',    'price' => 9.99, 'redemption_limit' => 6000,  'redemptions' => 4902,  'status' => 'live',      'cover_path' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCAPhAU3ufy4jmWhXubAFevI9zzzJNKCd7vfd337ycgUkpbfBRQPeIEHsM2v_bJmXJ9xKPRTOt3q3BP7DWlZpgUV4K-hcm0cGW8Nf7iL7QEdGp1nnbda3pmKtseCvJ2553DZpWkTvFFbacAsu1QrJNjv4mLDlgZcuR1ygt54OQ6tAd_nvssG7JqUAGHSh15UWclCvsvi5fQV2Vn2G8oWtb1WRPFzLuSITC7K3DCZpk9nkezWvQXzDnkpB4SZlgJIgtXNmrJ9Yd-qFU', 'description' => null],
            ['title' => 'After Hours',        'artist' => 'The Collective', 'price' => 4.99, 'redemption_limit' => 5000,  'redemptions' => 0,     'status' => 'scheduled', 'cover_path' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA73c2TfvADgzSKAWq1xfkw_EZMsD1o3TDwuTkCA2Pb3hE5OG4nt_w0bo-i6ExYBbl8eut7d5fqGv6hkn_Xl_pT2aiJSSM-LPH9BvWojFXUMPv7tyAGitvvVnmXNT72yVq5UjCyWCK_fkDlsX89Cc1Ds8K-p3FuGhPMEwq2-iNEhIUskUhqnbxwiBjjgHsb544Htkuk_uRs2319lXXwSRNFRowUCPnq7bo6rohbleGEDD1BlmxvbHjcxMzOtnXUWfPAGBvIjCSTpHc', 'description' => null],
            ['title' => 'Midnight Mix',       'artist' => 'Main Stage',     'price' => 4.99, 'redemption_limit' => 8000,  'redemptions' => 6401,  'status' => 'live',      'cover_path' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBg-8NCXLlzR4irK3de5--mQrP8oW5zY3cn3bYqGXm5sHHWH7BVhlfrILYaThB-Wt_ouP8pznfSK8qhWZFO_-1m4-9eCBuXcRMMZ1NYXs9du0SfE10Jvr48k9bilSeVcjiw7u4P5u_sq-mbHVuWT4EMgu2dzbupzYkf-7MUNfbU_ww8aR4cOUG01MXkZyR5l8EjvGp_mIiHxEwKWzyyRWNDO1L3zTzW5rH7m6gEjhySg2FKC822C8JfdHj2eAdeqxppCHU8PSAJ-q8', 'description' => null],
            ['title' => 'Cyber Synth',        'artist' => 'Digital Drift',  'price' => 9.99, 'redemption_limit' => 12000, 'redemptions' => 9801,  'status' => 'live',      'cover_path' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCal__rmJC6rH62tAtPXBhHWj0BqHK5N77copHmQO2GPo3Z9ZsucqeKO3t6QjkMzeRA2lm6x9wrdHXN643adSUSRiHA22mxrlAqQ3IOJn-gDzQGzXbQWQ0QssD255XkJ7M6ImwW-1dnZdBHatsHn5n5No4O6f2IvJULdBp8uS6x3sDESSGijebKVKueF100dM5i09YxyNOAjFWmyXYGe4XGaJ7UdvNY1E0A_h0UPKHnOeNLWjP_lcvU9TsrAFyQ8rIT4SJfKyjFnac', 'description' => null],
        ];

        foreach ($assets as $a) {
            Asset::updateOrCreate(
                ['title' => $a['title'], 'artist' => $a['artist']],
                $a,
            );
        }
    }
}
