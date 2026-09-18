<?php

namespace App\Services;

use App\Models\NewsArticle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NewsService
{
    /**
     * Fetch news from FXStreet RSS, save/update in database, and return latest articles.
     */
    public function syncAndGetLatest(int $limit = 30)
    {
        // Sync news from FXStreet RSS into database
        $this->syncFromRss();

        // Retrieve saved articles from the database with real comments & count
        return NewsArticle::withCount('comments')
            ->with(['comments' => function ($query) {
                $query->with('user:id,name,is_online')->latest();
            }])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Sync articles from FXStreet RSS feed into database.
     */
    public function syncFromRss(): void
    {
        Cache::remember('fxstreet_db_sync_v1', 300, function () {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://www.fxstreet.com/rss/news');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
                curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                $xmlString = curl_exec($ch);
                curl_close($ch);

                if (!$xmlString) {
                    Log::warning("FXStreet RSS curl returned empty response.");
                    return true;
                }

                $rss = @simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
                if ($rss === false) {
                    Log::warning("Failed to parse FXStreet RSS XML.");
                    return true;
                }

                $items = $rss->channel->item ?? [];

                foreach ($items as $item) {
                    $title = (string)$item->title;
                    $link = (string)$item->link;
                    $pubDate = (string)$item->pubDate;
                    $rawDesc = (string)$item->description;

                    // Extract image from XML if available (enclosure, media:content, or <img> tag)
                    $imageUrl = null;
                    if (isset($item->enclosure['url'])) {
                        $imageUrl = (string)$item->enclosure['url'];
                    } elseif ($media = $item->children('media', true)) {
                        if (isset($media->content['url'])) {
                            $imageUrl = (string)$media->content['url'];
                        }
                    }

                    if (!$imageUrl && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $rawDesc, $matches)) {
                        $imageUrl = $matches[1];
                    }

                    $cleanDesc = strip_tags($rawDesc);
                    $cleanDesc = html_entity_decode($cleanDesc, ENT_QUOTES, 'UTF-8');
                    $cleanDesc = preg_replace('/\s+/', ' ', $cleanDesc);
                    $cleanDesc = trim($cleanDesc);

                    // Parse timestamp
                    $timestamp = strtotime($pubDate);
                    $publishedAt = $timestamp ? date('Y-m-d H:i:s', $timestamp) : now();

                    // Determine if analysis
                    $isAnalysis = false;
                    $analysisKeywords = ['technical', 'fundamental', 'forecast', 'outlook', 'analysis', 'target', 'support', 'resistance', 'range', 'chart', 'trend', 'retracement', 'breakout'];
                    foreach ($analysisKeywords as $keyword) {
                        if (stripos($title, $keyword) !== false || stripos($cleanDesc, $keyword) !== false) {
                            $isAnalysis = true;
                            break;
                        }
                    }

                    if (!empty($title) && !empty($link)) {
                        NewsArticle::updateOrCreate(
                            ['url' => $link],
                            [
                                'title' => $title,
                                'image' => $imageUrl,
                                'description' => $cleanDesc,
                                'source' => 'FXStreet',
                                'is_analysis' => $isAnalysis,
                                'published_at' => $publishedAt,
                            ]
                        );
                    }
                }
            } catch (\Exception $e) {
                Log::warning("FXStreet RSS fetch/save failed: " . $e->getMessage());
            }

            return true;
        });
    }
}
