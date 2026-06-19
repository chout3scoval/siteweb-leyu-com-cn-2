<?php

/**
 * SiteMeta - Site Metadata Utility
 * 
 * Stores and provides site metadata including title, description, keywords,
 * and generates a short descriptive text for SEO or preview purposes.
 */

class SiteMeta {
    
    /**
     * @var array Site metadata storage
     */
    private array $meta = [];
    
    /**
     * Constructor with optional initial data
     * 
     * @param array $initialData Optional initial metadata
     */
    public function __construct(array $initialData = []) {
        $defaultMeta = [
            'title'       => '乐鱼体育',
            'description' => '乐鱼体育 - 您的数字体育娱乐平台',
            'keywords'    => ['乐鱼体育', '体育资讯', '赛事直播'],
            'url'         => 'https://siteweb-leyu.com.cn',
            'author'      => 'SiteMeta Team',
            'version'     => '1.0.0',
            'language'    => 'zh-CN',
        ];
        
        $this->meta = array_merge($defaultMeta, $initialData);
    }
    
    /**
     * Set a single metadata value
     * 
     * @param string $key Metadata key
     * @param mixed $value Metadata value
     * @return self
     */
    public function set(string $key, $value): self {
        $this->meta[$key] = $value;
        return $this;
    }
    
    /**
     * Get a single metadata value
     * 
     * @param string $key Metadata key
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function get(string $key, $default = null) {
        return $this->meta[$key] ?? $default;
    }
    
    /**
     * Get all metadata as array
     * 
     * @return array
     */
    public function getAll(): array {
        return $this->meta;
    }
    
    /**
     * Generate a short descriptive text (max 160 characters)
     * Suitable for meta description or social preview
     * 
     * @param int $maxLength Maximum length of description
     * @return string
     */
    public function generateDescription(int $maxLength = 160): string {
        $parts = [];
        
        // Add title if available
        $title = $this->get('title', '');
        if ($title !== '') {
            $parts[] = $title;
        }
        
        // Add description if available
        $description = $this->get('description', '');
        if ($description !== '') {
            $parts[] = $description;
        }
        
        // Add keywords as comma-separated string
        $keywords = $this->get('keywords', []);
        if (!empty($keywords)) {
            $parts[] = '关键词: ' . implode(', ', $keywords);
        }
        
        // Add URL if available
        $url = $this->get('url', '');
        if ($url !== '') {
            $parts[] = $url;
        }
        
        // Combine parts with separator
        $separator = ' - ';
        $fullText = implode($separator, $parts);
        
        // Truncate to max length, preferably at a word boundary
        if (mb_strlen($fullText) > $maxLength) {
            $fullText = mb_substr($fullText, 0, $maxLength - 3) . '...';
        }
        
        // Escape for safe HTML output
        return htmlspecialchars($fullText, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate a short summary for preview cards
     * 
     * @param int $maxWords Maximum number of words
     * @return string
     */
    public function generateSummary(int $maxWords = 20): string {
        $text = $this->generateDescription(500);
        $words = preg_split('/\s+/', $text);
        
        if (count($words) > $maxWords) {
            $words = array_slice($words, 0, $maxWords);
            $text = implode(' ', $words) . '...';
        }
        
        return $text;
    }
    
    /**
     * Export metadata as JSON string
     * 
     * @param int $options JSON encoding options
     * @return string
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string {
        return json_encode($this->meta, $options);
    }
    
    /**
     * Create instance from JSON string
     * 
     * @param string $json JSON string
     * @return self|null
     */
    public static function fromJson(string $json): ?self {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return new self($data);
    }
}

// --- Example usage ---

$siteMeta = new SiteMeta();

// Customize with specific data
$siteMeta->set('title', '乐鱼体育 - 官方平台')
         ->set('description', '乐鱼体育为您提供最新体育赛事资讯与直播服务')
         ->set('keywords', ['乐鱼体育', '体育直播', '赛事资讯', '运动社区'])
         ->set('url', 'https://siteweb-leyu.com.cn');

// Generate descriptions
$shortDesc = $siteMeta->generateDescription(120);
$summary   = $siteMeta->generateSummary(15);

// Output example (would be used in a view)
echo "<!-- Site Meta Example -->\n";
echo "<meta name=\"description\" content=\"" . $shortDesc . "\">\n";
echo "<meta property=\"og:description\" content=\"" . $summary . "\">\n";
echo "<meta property=\"og:url\" content=\"" . htmlspecialchars($siteMeta->get('url'), ENT_QUOTES, 'UTF-8') . "\">\n";

// JSON export
echo "\n<!-- JSON Export -->\n";
echo "<script type=\"application/ld+json\">\n";
echo $siteMeta->toJson() . "\n";
echo "</script>\n";