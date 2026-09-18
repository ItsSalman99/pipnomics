<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $regular = User::firstOrCreate(
            ['email' => 'regular@example.com'],
            [
                'name' => 'Regular Trader',
                'password' => bcrypt('password'),
                'is_premium' => false,
            ]
        );

        $premium = User::firstOrCreate(
            ['email' => 'premium@example.com'],
            [
                'name' => 'Premium Trader',
                'password' => bcrypt('password'),
                'is_premium' => true,
            ]
        );

        $elite = User::firstOrCreate(
            ['email' => 'alex.fx@example.com'],
            [
                'name' => 'Alex River',
                'password' => bcrypt('password'),
                'is_premium' => true,
            ]
        );

        // Seed top community forums if none exist
        if (Group::count() === 0) {
            $forumsData = [
                ['name' => 'Forex Majors & Scalping Hub', 'description' => 'Discussions on EUR/USD, GBP/USD, USD/JPY key order blocks, liquidity sweeps, and high frequency setups.', 'owner' => $premium],
                ['name' => 'Gold & Commodities Circle', 'description' => 'Macro trends, geopolitical impacts, and technical breakout zones for XAU/USD, Brent Oil, and Silver.', 'owner' => $elite],
                ['name' => 'Central Bank & Interest Rates Watch', 'description' => 'Tracking FOMC, ECB, BOE, and BOJ monetary policy decisions, dot plots, and inflation expectations.', 'owner' => $premium],
                ['name' => 'Price Action & SMC Specialists', 'description' => 'Smart Money Concepts, Fair Value Gaps, BOS, CHoCH, and institutional orderflow analysis.', 'owner' => $elite],
                ['name' => 'London & New York Breakout Masters', 'description' => 'Live session open strategies, Asian range breakouts, and killzone execution setups.', 'owner' => $premium],
                ['name' => 'Algorithmic & Quant Trading Lab', 'description' => 'Automated EA development, Pine Script indicators, backtesting stats, and risk modeling.', 'owner' => $elite],
                ['name' => 'Swing Traders Macro Collective', 'description' => 'Multi-week position trading strategies based on COT reports, yield curve differentials, and carry trade dynamics.', 'owner' => $premium],
                ['name' => 'Indices & Tech Momentum (NAS100 / US30)', 'description' => 'Daily setups for Nasdaq 100, S&P 500, and Dow Jones industrial average index futures.', 'owner' => $elite],
                ['name' => 'Risk Management & Trading Psychology', 'description' => 'Mastering position sizing, R:R asymmetry, trader mindset, discipline, and emotional balance.', 'owner' => $premium],
                ['name' => 'Crypto FX & Cross Assets Syndicate', 'description' => 'Correlations between BTC/ETH liquidity cycles, stablecoin peg mechanisms, and fiat currencies.', 'owner' => $elite],
            ];

            foreach ($forumsData as $data) {
                $group = Group::create([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'owner_id' => $data['owner']->id,
                ]);

                // Attach members
                $group->members()->attach([$data['owner']->id, $regular->id]);

                // Create initial post
                $group->posts()->create([
                    'user_id' => $data['owner']->id,
                    'title' => 'Welcome to ' . $data['name'],
                    'content' => 'Welcome traders! Share your setups, ask questions, and post chart analyses here.',
                ]);
            }
        }
    }
}
